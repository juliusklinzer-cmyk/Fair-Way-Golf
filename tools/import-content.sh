#!/usr/bin/env bash
# Spielt die Rechtstexte aus content/rechtliches/*.html in die WordPress-Seiten ein.
# Aufruf: bash tools/import-content.sh
set -e
cd "$(dirname "$0")/.."
mkdir -p wordpress/_build/content
cp content/rechtliches/*.html wordpress/_build/content/
for slug in impressum datenschutz agb barrierefreiheit; do
  id=$(./wp.sh post list --post_type=page --name="$slug" --field=ID </dev/null | tr -d '\r\n')
  if [ -z "$id" ]; then echo "Seite $slug fehlt"; continue; fi
  if [ ! -f "content/rechtliches/$slug.html" ]; then echo "content/rechtliches/$slug.html fehlt"; continue; fi
  ./wp.sh post update "$id" "/var/www/html/_build/content/$slug.html" </dev/null | tail -1
done
