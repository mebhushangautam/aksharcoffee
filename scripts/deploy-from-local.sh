#!/usr/bin/env bash
# Trigger VPS deploy via GitHub Actions — never rsync or SSH-deploy code directly.
#
# Usage:
#   bash scripts/deploy-from-local.sh
#   DEPLOY_SITES=aksharcoffee bash scripts/deploy-from-local.sh
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT"

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
# shellcheck source=deploy-site-lib.sh
source "${SCRIPT_DIR}/deploy-site-lib.sh"

SITES_LIST_FILE="$(deploy_sites_list_file "${SCRIPT_DIR}")"
DEPLOY_SITES="${DEPLOY_SITES:-all}"
DEPLOY_BRANCH="${DEPLOY_BRANCH:-main}"

[[ -f artisan ]] || { echo "ERROR: Run from Laravel project root." >&2; exit 1; }

if ! command -v gh >/dev/null 2>&1; then
  echo "ERROR: GitHub CLI (gh) is required. Install: https://cli.github.com/" >&2
  exit 1
fi

deploy_sites_validate_selection "${SITES_LIST_FILE}" "${DEPLOY_SITES}"

if [[ -n "$(git status --porcelain)" ]]; then
  echo "ERROR: Uncommitted changes. Commit and push to ${DEPLOY_BRANCH} first." >&2
  git status --short
  exit 1
fi

current_branch="$(git rev-parse --abbrev-ref HEAD)"
if [[ "${current_branch}" != "${DEPLOY_BRANCH}" ]]; then
  echo "ERROR: Switch to branch '${DEPLOY_BRANCH}' before deploying (current: ${current_branch})." >&2
  exit 1
fi

echo "==> Deploy via GitHub Actions only (no direct VPS upload)"
deploy_sites_print_table "${SITES_LIST_FILE}"
echo "    Sites input: ${DEPLOY_SITES}"

echo "==> Push ${DEPLOY_BRANCH} to origin"
git push origin "${DEPLOY_BRANCH}"

echo "==> Trigger workflow: Deploy to VPS (sites=${DEPLOY_SITES})"
gh workflow run "Deploy to VPS" --ref "${DEPLOY_BRANCH}" -f "sites=${DEPLOY_SITES}"

echo ""
echo "==> Deploy triggered. Watch progress:"
echo "    gh run list --workflow=deploy.yml --limit 3"
echo "    gh run watch"
