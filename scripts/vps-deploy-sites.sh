#!/usr/bin/env bash
# Deploy one or more Laravel sites on the VPS (git pull + scripts/deploy.sh per site).
# Used by GitHub Actions after frontend build upload.
#
# Environment:
#   DEPLOY_SITES          all | aksharcoffee
#   DEPLOY_BRANCH         default main
#   FRONTEND_BUILD_ARCHIVE  shared CI tarball applied to each site
#   CHECKOUT_HELPER       default /var/tmp/vps-ensure-checkout.sh
#   DEPLOY_KEY            default /root/.ssh/aksharcoffee_deploy
#   SITES_LIST_FILE       override path to deploy-sites.list
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
# shellcheck source=deploy-site-lib.sh
source "${SCRIPT_DIR}/deploy-site-lib.sh"

SITES_LIST_FILE="${SITES_LIST_FILE:-$(deploy_sites_list_file "${SCRIPT_DIR}")}"
DEPLOY_SITES="${DEPLOY_SITES:-all}"
DEPLOY_BRANCH="${DEPLOY_BRANCH:-main}"
FRONTEND_BUILD_ARCHIVE="${FRONTEND_BUILD_ARCHIVE:-}"
CHECKOUT_HELPER="${CHECKOUT_HELPER:-/var/tmp/vps-ensure-checkout.sh}"
DEPLOY_KEY="${DEPLOY_KEY:-/root/.ssh/aksharcoffee_deploy}"
BUILD_FRONTEND="${BUILD_FRONTEND:-0}"
RUN_DB_SEED="${RUN_DB_SEED:-0}"
DB_SEED_CLASSES="${DB_SEED_CLASSES:-}"
DB_SEED_CLASS="${DB_SEED_CLASS:-}"

if [[ ! -f "${SITES_LIST_FILE}" ]]; then
  echo "ERROR: Missing sites list: ${SITES_LIST_FILE}" >&2
  exit 1
fi

if [[ ! -f "${CHECKOUT_HELPER}" ]]; then
  echo "ERROR: Missing checkout helper: ${CHECKOUT_HELPER}" >&2
  exit 1
fi

if [[ ! -f "${DEPLOY_KEY}" ]]; then
  echo "ERROR: Missing ${DEPLOY_KEY}. Add aksharcoffee_deploy key to GitHub Deploy keys." >&2
  exit 1
fi

deploy_sites_validate_selection "${SITES_LIST_FILE}" "${DEPLOY_SITES}"

export GIT_SSH_COMMAND="ssh -i ${DEPLOY_KEY} -o IdentitiesOnly=yes -o StrictHostKeyChecking=accept-new"
export DEPLOY_KEY
export DEPLOY_BRANCH
export BUILD_FRONTEND
export RUN_DB_SEED
export DB_SEED_CLASSES
export DB_SEED_CLASS

declare -a TARGET_IDS=()
while IFS= read -r id; do
  [[ -n "${id}" ]] && TARGET_IDS+=("${id}")
done < <(deploy_sites_requested_ids "${SITES_LIST_FILE}" "${DEPLOY_SITES}")

echo "==> Deploy: ${#TARGET_IDS[@]} site(s) on branch ${DEPLOY_BRANCH}"
deploy_sites_print_table "${SITES_LIST_FILE}"

SHARED_FRONTEND_ARCHIVE="${FRONTEND_BUILD_ARCHIVE:-}"
if [[ -n "${SHARED_FRONTEND_ARCHIVE}" ]]; then
  if [[ ! -f "${SHARED_FRONTEND_ARCHIVE}" ]]; then
    echo "ERROR: FRONTEND_BUILD_ARCHIVE not found: ${SHARED_FRONTEND_ARCHIVE}" >&2
    exit 1
  fi
  echo "==> Shared frontend archive: ${SHARED_FRONTEND_ARCHIVE}"
fi

index=0

for site_id in "${TARGET_IDS[@]}"; do
  row="$(deploy_sites_lookup "${SITES_LIST_FILE}" "${site_id}")"
  IFS='|' read -r _ label app_path web_user <<< "${row}"

  echo ""
  echo "================================================================"
  echo "==> Site $((index + 1))/${#TARGET_IDS[@]}: ${site_id} — ${label}"
  echo "    Path: ${app_path}"
  echo "    User: ${web_user}"
  echo "================================================================"

  export VPS_APP_PATH="${app_path}"
  export WEB_USER="${web_user}"

  bash "${CHECKOUT_HELPER}"
  cd "${app_path}"
  git reset --hard "origin/${DEPLOY_BRANCH}"

  if [[ -n "${SHARED_FRONTEND_ARCHIVE}" ]]; then
    SITE_ARCHIVE="/var/tmp/frontend-build-${site_id}-$$.tar.gz"
    cp -f "${SHARED_FRONTEND_ARCHIVE}" "${SITE_ARCHIVE}"
    export FRONTEND_BUILD_ARCHIVE="${SITE_ARCHIVE}"
    unset KEEP_FRONTEND_BUILD_ARCHIVE
  else
    unset FRONTEND_BUILD_ARCHIVE
    unset KEEP_FRONTEND_BUILD_ARCHIVE
  fi

  bash scripts/deploy.sh

  index=$((index + 1))
done

if [[ -n "${SHARED_FRONTEND_ARCHIVE}" ]]; then
  rm -f "${SHARED_FRONTEND_ARCHIVE}"
fi

echo ""
echo "==> Deploy finished OK (${#TARGET_IDS[@]} site(s))"
