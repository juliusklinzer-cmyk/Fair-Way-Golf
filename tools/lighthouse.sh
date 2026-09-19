#!/usr/bin/env bash
# Lighthouse (mobil, gedrosselt) im Playwright-Container gegen die lokale Seite.
# Aufruf: bash tools/lighthouse.sh [pfad ...]   (Standard: / /voranmeldung/ /golfplaetze/)
set -e
cd "$(dirname "$0")/.."
mkdir -p tools/reports
paths=("$@"); [ ${#paths[@]} -eq 0 ] && paths=(/ /voranmeldung/ /golfplaetze/)
docker run --rm --network host \
  -v "$PWD/tools:/w" -w /w -e HOME=/tmp \
  mcr.microsoft.com/playwright:v1.49.0-jammy \
  bash -lc '
    [ -d node_modules/lighthouse ] || npm i --no-save --silent lighthouse@12 >/dev/null 2>&1
    export CHROME_PATH=$(ls -d /ms-playwright/chromium-*/chrome-linux/chrome | head -1)
    for p in '"${paths[*]}"'; do
      name=$(echo "$p" | tr -d / ); name=${name:-start}
      npx lighthouse "http://localhost:8092$p" --quiet --chrome-flags="--headless=new --no-sandbox --disable-gpu" \
        --only-categories=performance,accessibility,best-practices,seo --output=json --output-path="/w/reports/lh-$name.json" >/dev/null 2>&1 || echo "lighthouse failed for $p"
      node -e "
        const r=require(\"/w/reports/lh-$name.json\"); const c=r.categories; const a=r.audits;
        const s=(k)=>Math.round((c[k]&&c[k].score||0)*100);
        console.log(\"$p\", \"perf\", s(\"performance\"), \"a11y\", s(\"accessibility\"), \"bp\", s(\"best-practices\"), \"seo\", s(\"seo\"),
          \"| LCP\", a[\"largest-contentful-paint\"].displayValue, \"CLS\", a[\"cumulative-layout-shift\"].displayValue, \"TBT\", a[\"total-blocking-time\"].displayValue, \"Gewicht\", a[\"total-byte-weight\"].displayValue);
        const fails=Object.values(a).filter(x=>x.score!==null&&x.score<0.9&&x.scoreDisplayMode!==\"informative\"&&x.scoreDisplayMode!==\"notApplicable\").map(x=>x.id+\"(\"+Math.round((x.score||0)*100)+\")\");
        console.log(\"   schwach:\", fails.join(\", \")||\"nichts\");
      "
    done
  '
