#!/usr/bin/env bash
# Barrierefreiheits-Prüfung (axe-core + skriptgesteuerte Tastatur-/Fokus-/Reflow-Checks) im Playwright-Container.
# Aufruf: bash tools/axe.sh  (Ausgabe auf stdout)
# Hinweis: npm i --no-save ohne package.json entfernt andere Pakete in tools/node_modules; deshalb alle in einem Aufruf.
set -e
cd "$(dirname "$0")/.."
mkdir -p tools/screenshots
docker run --rm --network host \
  -v "$PWD/tools:/w" -w /w \
  -e HOME=/tmp \
  mcr.microsoft.com/playwright:v1.49.0-jammy \
  bash -lc "{ [ -d node_modules/playwright ] && [ -d node_modules/@axe-core/playwright ]; } || npm i --no-save --silent playwright@1.49.0 @axe-core/playwright axe-core >/dev/null 2>&1; node axe.mjs http://localhost:8092"
