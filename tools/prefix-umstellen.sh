#!/usr/bin/env bash
# Stellt die lokale Instanz einmalig vom Tabellen-Präfix wp_ auf fwg_ um. Grund: In der Produktion
# teilt sich die neue Seite die Datenbank mit der alten (deren wp_-Tabellen bleiben für den Rollback),
# und der Export aus tools/deploy-prep.sh soll 1:1 passen. Startet den WordPress-Container neu.
set -euo pipefail
cd "$(dirname "$0")/.."

cur=$(./wp.sh db prefix </dev/null)
if [ "$cur" = "fwg_" ]; then
	echo "Präfix ist schon fwg_"
	exit 0
fi
[ "$cur" = "wp_" ] || { echo "Unerwartetes Präfix: $cur"; exit 1; }

echo "== Tabellen umbenennen"
tables=$(./wp.sh db query "SHOW TABLES LIKE 'wp\\_%'" --skip-column-names </dev/null)
for t in $tables; do
	./wp.sh db query "RENAME TABLE \`$t\` TO \`fwg_${t#wp_}\`" </dev/null
	echo "   $t -> fwg_${t#wp_}"
done

echo "== präfixabhängige Schlüssel"
./wp.sh db query "UPDATE fwg_options SET option_name = 'fwg_user_roles' WHERE option_name = 'wp_user_roles'" </dev/null
./wp.sh db query "UPDATE fwg_usermeta SET meta_key = CONCAT('fwg_', SUBSTRING(meta_key, 4)) WHERE meta_key LIKE 'wp\\_%'" </dev/null
./wp.sh db query "SELECT meta_key FROM fwg_usermeta WHERE meta_key LIKE 'fwg\\_%'" --skip-column-names </dev/null | sed 's/^/   /'

echo "== Docker-Umgebung"
grep -q "WORDPRESS_TABLE_PREFIX" docker-compose.yml || sed -i 's|^      WORDPRESS_DB_NAME: fwg_wordpress$|      WORDPRESS_DB_NAME: fwg_wordpress\n      WORDPRESS_TABLE_PREFIX: fwg_|' docker-compose.yml
grep -q "WORDPRESS_TABLE_PREFIX" wp.sh || sed -i 's|^  -e WORDPRESS_DB_NAME=fwg_wordpress \\$|  -e WORDPRESS_DB_NAME=fwg_wordpress \\\n  -e WORDPRESS_TABLE_PREFIX=fwg_ \\|' wp.sh
grep -n "TABLE_PREFIX" docker-compose.yml wp.sh
docker compose up -d wordpress 2>&1 | tail -1
sleep 4

echo "== Prüfung"
echo "   Präfix: $(./wp.sh db prefix </dev/null)"
echo "   Startseite: HTTP $(curl -s -o /dev/null -w '%{http_code}' http://localhost:8092/)"
./wp.sh user list --fields=user_login,roles </dev/null | sed 's/^/   /'
./wp.sh option get siteurl </dev/null | sed 's/^/   siteurl: /'
