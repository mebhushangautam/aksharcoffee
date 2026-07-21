#!/usr/bin/env bash
# Production deploy entrypoint - run on VPS from app root (`bash scripts/deploy.sh`).
set -euo pipefail

APP_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$APP_DIR"

export COMPOSER_ALLOW_SUPERUSER="${COMPOSER_ALLOW_SUPERUSER:-1}"

DEPLOY_BRANCH="${DEPLOY_BRANCH:-main}"
SKIP_GIT_FETCH="${SKIP_GIT_FETCH:-0}"
BUILD_FRONTEND="${BUILD_FRONTEND:-1}"
RUN_DB_SEED="${RUN_DB_SEED:-0}"
DB_SEED_CLASS="${DB_SEED_CLASS:-}"
DB_SEED_CLASSES="${DB_SEED_CLASSES:-}"
FRONTEND_BUILD_ARCHIVE="${FRONTEND_BUILD_ARCHIVE:-}"
WEB_USER="${WEB_USER:-aksharcoffeeshop-system}"
DEPLOY_KEY="${DEPLOY_KEY:-/root/.ssh/aksharcoffee_deploy}"

if [[ -f "${DEPLOY_KEY}" ]]; then
  export GIT_SSH_COMMAND="ssh -i ${DEPLOY_KEY} -o IdentitiesOnly=yes -o StrictHostKeyChecking=accept-new"
fi

echo "==> Deploy start: ${APP_DIR} (branch: ${DEPLOY_BRANCH})"

if [[ ! -f artisan ]]; then
  echo "ERROR: artisan not found - run this script from Laravel project root." >&2
  exit 1
fi

if ! git config --global --get-all safe.directory 2>/dev/null | grep -Fxq "${APP_DIR}"; then
  git config --global --add safe.directory "${APP_DIR}"
fi

if [[ "${SKIP_GIT_FETCH}" == "1" ]]; then
  echo "==> SKIP_GIT_FETCH=1"
else
  git fetch origin "${DEPLOY_BRANCH}"
  git checkout "${DEPLOY_BRANCH}"
  git reset --hard "origin/${DEPLOY_BRANCH}"
fi

echo "==> Composer install"
composer install --no-dev --no-interaction --optimize-autoloader

echo "==> Migrations"
php artisan migrate --force --no-interaction

if [[ "${RUN_DB_SEED}" == "1" ]]; then
  echo "==> Database seeders"
  if [[ -n "${DB_SEED_CLASSES}" ]]; then
    IFS=',' read -r -a seeders <<< "${DB_SEED_CLASSES}"
    for seeder in "${seeders[@]}"; do
      seeder="${seeder#"${seeder%%[![:space:]]*}"}"
      seeder="${seeder%"${seeder##*[![:space:]]}"}"
      if [[ -n "${seeder}" ]]; then
        php artisan db:seed --class="${seeder}" --force --no-interaction
      fi
    done
  elif [[ -n "${DB_SEED_CLASS}" ]]; then
    php artisan db:seed --class="${DB_SEED_CLASS}" --force --no-interaction
  else
    php artisan db:seed --force --no-interaction
  fi
fi

if [[ -n "${FRONTEND_BUILD_ARCHIVE}" ]]; then
  if [[ ! -f "${FRONTEND_BUILD_ARCHIVE}" ]]; then
    echo "ERROR: FRONTEND_BUILD_ARCHIVE is set but file not found: ${FRONTEND_BUILD_ARCHIVE}" >&2
    exit 1
  fi
  echo "==> Frontend from CI archive (${FRONTEND_BUILD_ARCHIVE})"
  rm -rf public/build
  mkdir -p public
  tar xzf "${FRONTEND_BUILD_ARCHIVE}" -C public
  if [[ "${KEEP_FRONTEND_BUILD_ARCHIVE:-0}" != "1" ]]; then
    rm -f "${FRONTEND_BUILD_ARCHIVE}"
  fi
elif [[ "${BUILD_FRONTEND}" == "1" ]] && [[ -f package.json ]] && command -v npm >/dev/null 2>&1; then
  echo "==> Frontend (npm ci + build on server)"
  npm ci --no-audit --no-fund
  npm run build
elif [[ "${BUILD_FRONTEND}" == "1" ]] && [[ -f package.json ]]; then
  echo "ERROR: BUILD_FRONTEND=1 but npm is not available." >&2
  echo "Use GitHub Actions deploy (archive upload) or install Node on VPS." >&2
  exit 1
fi

echo "==> Ensure storage directories"
mkdir -p storage/framework/{sessions,views,cache/data} storage/logs bootstrap/cache
mkdir -p storage/app/public storage/app/livewire-tmp
mkdir -p public/user-uploads/{temp,logo,favicons/super-admin,meta-image}

if [[ -n "${WEB_USER}" ]]; then
  echo "==> Permissions before cache rebuild (${WEB_USER})"
  chown -R "${WEB_USER}:${WEB_USER}" storage bootstrap/cache public/user-uploads || true
  chmod -R ug+rwx storage bootstrap/cache public/user-uploads || true
fi

echo "==> Clear & rebuild caches"
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Queue workers restart signal"
php artisan queue:restart || true

if [[ -n "${WEB_USER}" ]]; then
  echo "==> Permissions after deploy (${WEB_USER})"
  chown -R "${WEB_USER}:${WEB_USER}" storage bootstrap/cache public/user-uploads || true
  chmod -R ug+rwx storage bootstrap/cache public/user-uploads || true
  if [[ -d public/build ]]; then
    chown -R "${WEB_USER}:${WEB_USER}" public/build || true
  fi
fi

echo "==> Public storage symlink"
if [[ -e public/storage ]] && [[ ! -L public/storage ]]; then
  rm -rf public/storage
fi
php artisan storage:link --no-interaction

if [[ -n "${WEB_USER}" ]]; then
  chown -h "${WEB_USER}:${WEB_USER}" public/storage 2>/dev/null || true
  chown -R "${WEB_USER}:${WEB_USER}" storage/app/public 2>/dev/null || true
  find storage/app/public -type d -exec chmod 775 {} + 2>/dev/null || true
  find storage/app/public -type f -exec chmod 664 {} + 2>/dev/null || true
fi

for svc in php8.3-fpm php8.2-fpm php-fpm8.3 php-fpm8.2; do
  if command -v systemctl >/dev/null 2>&1 && systemctl is-active --quiet "${svc}" 2>/dev/null; then
    systemctl reload "${svc}" || true
    break
  fi
done

echo "==> Prune dev-only files"
bash scripts/prune-production-tree.sh

echo "==> Deploy finished OK"
