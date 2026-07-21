#!/usr/bin/env bash
# Shared helpers for VPS production-tree pruning (manifest-driven).
set -euo pipefail

prune_remove_path() {
  local path="$1"
  if [[ -e "${path}" ]]; then
    rm -rf "${path}"
    echo "    removed ${path}"
  fi
}

prune_paths_from_manifest() {
  local manifest="$1"
  if [[ ! -f "${manifest}" ]]; then
    echo "ERROR: prune manifest not found: ${manifest}" >&2
    exit 1
  fi

  local line trimmed
  while IFS= read -r line || [[ -n "${line}" ]]; do
    trimmed="${line%%#*}"
    trimmed="${trimmed#"${trimmed%%[![:space:]]*}"}"
    trimmed="${trimmed%"${trimmed##*[![:space:]]}"}"
    [[ -z "${trimmed}" ]] && continue
    prune_remove_path "${trimmed}"
  done < "${manifest}"
}

prune_verify_absent_from_manifest() {
  local manifest="$1"
  if [[ ! -f "${manifest}" ]]; then
    echo "ERROR: prune verify manifest not found: ${manifest}" >&2
    exit 1
  fi

  local line trimmed
  while IFS= read -r line || [[ -n "${line}" ]]; do
    trimmed="${line%%#*}"
    trimmed="${trimmed#"${trimmed%%[![:space:]]*}"}"
    trimmed="${trimmed%"${trimmed##*[![:space:]]}"}"
    [[ -z "${trimmed}" ]] && continue
    if [[ -e "${trimmed}" ]]; then
      echo "ERROR: prune incomplete — still present: ${trimmed}" >&2
      exit 1
    fi
  done < "${manifest}"
}
