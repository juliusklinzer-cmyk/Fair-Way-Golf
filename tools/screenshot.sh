#!/usr/bin/env bash
# Screenshots per Playwright im Docker-Container (kein lokales Node nötig).
# Aufruf: bash tools/screenshot.sh [nur-slug]
set -e
cd "$(dirname "$0")/.."
mkdir -p tools/screenshots .impeccable/review
docker run --rm --network host \
  -v "$PWD/tools:/w" -v "$PWD/.impeccable:/w/../.impeccable" -w /w \
  -e HOME=/tmp \
  mcr.microsoft.com/playwright:v1.49.0-jammy \
  bash -lc "[ -d node_modules/playwright ] || npm i --no-save --silent playwright@1.49.0 >/dev/null 2>&1; node screenshot.mjs http://localhost:8092 ${1:-}"
