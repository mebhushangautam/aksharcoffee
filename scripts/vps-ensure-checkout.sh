#!/usr/bin/env bash
# Ensure VPS app directory is a git checkout (recover after files removed). Preserves .env.
set -euo pipefail

APP="${VPS_APP_PATH:-/var/www/aksharcoffee}"
DEPLOY_BRANCH="${DEPLOY_BRANCH:-main}"
ENV_BACKUP="${ENV_BACKUP:-/var/tmp/aksharcoffee.env.backup}"
DEPLOY_KEY="${DEPLOY_KEY:-/root/.ssh/aksharcoffee_deploy}"
REPO="${REPO:-git@github.com:mebhushangautam/aksharcoffee.git}"

if [[ -f "${APP}/.env" ]]; then
  cp "${APP}/.env" "${ENV_BACKUP}"
  chmod 600 "${ENV_BACKUP}"
fi

if [[ ! -f "${DEPLOY_KEY}" ]]; then
  echo "ERROR: Missing ${DEPLOY_KEY}. Add aksharcoffee_deploy to GitHub Deploy keys." >&2
  exit 1
fi

export GIT_SSH_COMMAND="ssh -i ${DEPLOY_KEY} -o IdentitiesOnly=yes -o StrictHostKeyChecking=accept-new"

fresh_clone() {
  local parent name
  parent="$(dirname "${APP}")"
  name="$(basename "${APP}")"
  echo "==> Fresh git clone into ${APP}"
  mkdir -p "${parent}"
  cd "${parent}"
  rm -rf "${name}"
  git clone --branch "${DEPLOY_BRANCH}" "${REPO}" "${name}"
  if [[ -f "${ENV_BACKUP}" ]]; then
    cp "${ENV_BACKUP}" "${APP}/.env"
    chmod 600 "${APP}/.env"
  fi
}

if [[ ! -f "${APP}/artisan" ]]; then
  echo "==> Bootstrap: artisan missing"
  fresh_clone
fi

if ! git config --global --get-all safe.directory 2>/dev/null | grep -Fxq "${APP}"; then
  git config --global --add safe.directory "${APP}"
fi

cd "${APP}"
if ! git fetch origin "${DEPLOY_BRANCH}"; then
  echo "==> git fetch failed; re-cloning"
  fresh_clone
  cd "${APP}"
  git fetch origin "${DEPLOY_BRANCH}"
fi
