#!/usr/bin/env bash
# Shared helpers for multi-site VPS deploy (sourced by other scripts).
set -euo pipefail

deploy_sites_list_file() {
  local script_dir="${1:?}"
  echo "${script_dir}/deploy-sites.list"
}

deploy_sites_normalize_id() {
  echo "${1}" | tr '[:upper:]' '[:lower:]' | tr -d '[:space:]'
}

deploy_sites_all_ids() {
  local list_file="$1"
  local line id
  while IFS= read -r line || [[ -n "${line}" ]]; do
    [[ -z "${line}" ]] && continue
    [[ "${line}" =~ ^[[:space:]]*# ]] && continue
    IFS='|' read -r id _ _ _ <<< "${line}"
    id="$(deploy_sites_normalize_id "${id}")"
    [[ -n "${id}" ]] && echo "${id}"
  done < "${list_file}"
}

deploy_sites_requested_ids() {
  local list_file="$1"
  local selection="${2:-all}"
  selection="$(deploy_sites_normalize_id "${selection}")"

  if [[ -z "${selection}" || "${selection}" == "all" ]]; then
    deploy_sites_all_ids "${list_file}"
    return 0
  fi

  local part
  IFS=',' read -r -a parts <<< "${selection}"
  for part in "${parts[@]}"; do
    part="$(deploy_sites_normalize_id "${part}")"
    [[ -n "${part}" ]] && echo "${part}"
  done
}

deploy_sites_lookup() {
  local list_file="$1"
  local wanted_id="$2"
  local line id label path web_user

  wanted_id="$(deploy_sites_normalize_id "${wanted_id}")"

  while IFS= read -r line || [[ -n "${line}" ]]; do
    [[ -z "${line}" ]] && continue
    [[ "${line}" =~ ^[[:space:]]*# ]] && continue
    IFS='|' read -r id label path web_user <<< "${line}"
    id="$(deploy_sites_normalize_id "${id}")"
    if [[ "${id}" == "${wanted_id}" ]]; then
      printf '%s|%s|%s|%s\n' "${id}" "${label}" "${path}" "${web_user}"
      return 0
    fi
  done < "${list_file}"

  return 1
}

deploy_sites_validate_selection() {
  local list_file="$1"
  local selection="${2:-all}"
  local -a requested=()
  local id row

  while IFS= read -r id; do
    [[ -n "${id}" ]] && requested+=("${id}")
  done < <(deploy_sites_requested_ids "${list_file}" "${selection}")

  if [[ ${#requested[@]} -eq 0 ]]; then
    echo "ERROR: No deploy sites selected. Use DEPLOY_SITES=all or aksharcoffee." >&2
    echo "Available:" >&2
    deploy_sites_all_ids "${list_file}" | sed 's/^/  - /' >&2
    return 1
  fi

  for id in "${requested[@]}"; do
    if ! row="$(deploy_sites_lookup "${list_file}" "${id}")"; then
      echo "ERROR: Unknown deploy site id: ${id}" >&2
      echo "Available:" >&2
      deploy_sites_all_ids "${list_file}" | sed 's/^/  - /' >&2
      return 1
    fi
  done
}

deploy_sites_print_table() {
  local list_file="$1"
  local id row label path web_user

  printf '%-18s %-32s %s\n' "ID" "LABEL" "PATH"
  while IFS= read -r id; do
    row="$(deploy_sites_lookup "${list_file}" "${id}")"
    IFS='|' read -r id label path web_user <<< "${row}"
    printf '%-18s %-32s %s\n' "${id}" "${label}" "${path}"
  done < <(deploy_sites_all_ids "${list_file}")
}
