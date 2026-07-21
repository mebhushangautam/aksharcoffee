#!/usr/bin/env bash
# Remove dev-only files after a successful Laravel production deploy.
set -euo pipefail

APP_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$APP_DIR"

if [[ ! -f artisan ]]; then
  echo "ERROR: artisan not found — run from Laravel project root." >&2
  exit 1
fi

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
# shellcheck source=lib/production-prune-lib.sh
source "${SCRIPT_DIR}/lib/production-prune-lib.sh"

echo "==> Prune dev-only files from production tree"
prune_paths_from_manifest "${SCRIPT_DIR}/production-prune.paths"

echo "==> Verify production tree"
prune_verify_absent_from_manifest "${SCRIPT_DIR}/production-prune.verify"

echo "==> Remove prune tooling (not needed until next deploy)"
prune_paths_from_manifest "${SCRIPT_DIR}/production-prune.tooling"

echo "==> Production tree pruned"
