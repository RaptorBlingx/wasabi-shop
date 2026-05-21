#!/usr/bin/env bash

set -euo pipefail

slug="${1:-all}"

WASABI_ROOT="${WASABI_ROOT:-/home/ubuntu/wasabi}"
UPLOAD_DIR="${UPLOAD_DIR:-${WASABI_ROOT}/upload}"
OVOS_RELEASE_DIR="${OVOS_RELEASE_DIR:-/home/ubuntu/ovos-llm/releases}"
FULL_STACK_RELEASE_DIR="${FULL_STACK_RELEASE_DIR:-/home/ubuntu/humanergy/releases}"

publish_one() {
  local product_slug="$1"
  local artifact=""
  local checksum=""
  local create_script=""
  local image_script=""
  local source_dir=""

  case "${product_slug}" in
    ovos-skill)
      artifact="HumanEnerDIA-OVOS-skill-v1.0.0.zip"
      checksum="${artifact}.sha256"
      create_script="create_humanerdia_product.php"
      image_script="add_humanerdia_product_image.php"
      source_dir="${OVOS_RELEASE_DIR}"
      ;;
    full-stack)
      artifact="HumanEnerDIA-full-stack-v1.0.0.tar.gz"
      checksum="${artifact}.sha256"
      create_script="create_humanerdia_full_stack_product.php"
      image_script="add_humanerdia_full_stack_product_image.php"
      source_dir="${FULL_STACK_RELEASE_DIR}"
      ;;
    *)
      echo "Unknown HumanEnerDIA product slug: ${product_slug}" >&2
      exit 1
      ;;
  esac

  if [[ ! -f "${source_dir}/${artifact}" ]]; then
    echo "Missing release artifact: ${source_dir}/${artifact}" >&2
    exit 1
  fi

  if [[ ! -f "${source_dir}/${checksum}" ]]; then
    echo "Missing checksum file: ${source_dir}/${checksum}" >&2
    exit 1
  fi

  sudo install -m 664 -o www-data -g www-data \
    "${source_dir}/${artifact}" "${UPLOAD_DIR}/${artifact}"
  sudo install -m 664 -o www-data -g www-data \
    "${source_dir}/${checksum}" "${UPLOAD_DIR}/${checksum}"

  docker exec wasabi-project php "/var/www/html/tools/${create_script}"
  docker exec wasabi-project php "/var/www/html/tools/${image_script}"
}

case "${slug}" in
  all)
    publish_one "ovos-skill"
    publish_one "full-stack"
    ;;
  ovos-skill|full-stack)
    publish_one "${slug}"
    ;;
  *)
    echo "Usage: $0 [all|ovos-skill|full-stack]" >&2
    exit 1
    ;;
esac
