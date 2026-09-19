#!/usr/bin/env bash
# Phase C des Go-Live-Runbooks: Tausch der Seite INNERHALB des Ordners fair-way-golf.com auf dem
# Hetzner-Webhosting (SFTP per curl/libssh2) plus Aufruf des Einmal-Installers. Auf dem Server liegen
# weitere Seiten; dieses Skript fasst nichts außerhalb von fair-way-golf.com an.
# Zugangsdaten aus .deploy-creds.txt (gitignored), Paket aus tools/deploy-prep.sh in wordpress/_build/deploy/.
#
# Aufruf: bash tools/deploy-push.sh <schritt>
#   status    Inhalt von fair-way-golf.com auflisten, Live-Domain prüfen
#   dbcreds   DB-Zugang und Präfix aus der wp-config.php der ALTEN Seite lesen, in .deploy-creds.txt ergänzen;
#             zeigt außerdem PHP-relevante Zeilen der alten .htaccess
#   config    DB-Zugang aus .deploy-creds.txt in die produktive wp-config.php eintragen
#   upload    Installer, Zip, SQL und wp-config.php (als wp-config-new.php) in den Ordner laden
#   install   Installer: check, clear (alte Seite löschen), unzip, db, activate; bricht bei Fehlern ab
#   cleanup   Installer: Zip, SQL und Installer löschen; danach prüfen
#
# .deploy-creds.txt erwartet die Zeilen (Reihenfolge egal):
#   FTP-User: c5wkuy        FTP-Pass: ...
#   DB-Host: ...            DB-Name: ...   DB-User: ...   DB-Pass: ...   (per dbcreds von der alten Seite)
# Die neue Seite nutzt dieselbe Datenbank wie die alte, aber das Tabellen-Präfix fwg_; die alten
# wp_-Tabellen bleiben stehen (darin die Voranmeldungen aus dem ersten Anlauf).
set -euo pipefail
cd "$(dirname "$0")/.."

HOST=www733.your-server.de
SITE=fair-way-golf.com
DEP=wordpress/_build/deploy
CREDS=.deploy-creds.txt
PROD=https://www.fair-way-golf.com

[ -f "$CREDS" ] || { echo "Fehlt: $CREDS (siehe Kopf dieses Skripts)"; exit 1; }
cred() { awk -v k="$1" -F': *' '$1==k {print $2; exit}' "$CREDS"; }

# curl-Konfiguration mit Zugangsdaten, nur für diesen Lauf, nicht in der Prozessliste sichtbar
CFG=$(mktemp)
chmod 600 "$CFG"
trap 'rm -f "$CFG"' EXIT
printf 'user = "%s:%s"\nsilent\nshow-error\n' "$(cred FTP-User)" "$(cred FTP-Pass)" > "$CFG"
sftp() { curl -K "$CFG" "$@"; }
listing() { sftp "sftp://$HOST/~/$SITE/$1" | awk '{print $NF}' | grep -v '^\.\{1,2\}$' || true; }
installer() { curl -s "$PROD/_fwg_install.php?t=$(cat "$DEP/install-token.txt")&step=$1"; }

case "${1:-}" in
	status)
		echo "== Startverzeichnis des FTP-Benutzers (nur lesen)"
		sftp "sftp://$HOST/~/" | awk '{print $NF}' | grep -v '^\.\{1,2\}$' | sed 's/^/   /'
		echo "== Inhalt von $SITE"
		listing "" | sed 's/^/   /'
		echo "== Domain"
		echo "   $PROD -> HTTP $(curl -s -o /dev/null -w '%{http_code}' "$PROD/")"
		;;
	dbcreds)
		tmp=$(mktemp); chmod 600 "$tmp"
		sftp -o "$tmp" "sftp://$HOST/~/$SITE/wp-config.php"
		python3 - "$tmp" "$CREDS" <<'PY'
import sys, re, pathlib
src = pathlib.Path(sys.argv[1]).read_text(encoding='utf-8', errors='replace')
creds = pathlib.Path(sys.argv[2])
vals = {}
for k in ('DB_NAME', 'DB_USER', 'DB_PASSWORD', 'DB_HOST'):
    m = re.search(r"define\(\s*['\"]" + k + r"['\"]\s*,\s*['\"]((?:[^'\"\\]|\\.)*)['\"]\s*\)", src)
    if not m: sys.exit(f"{k} in der alten wp-config.php nicht gefunden")
    vals[k] = m.group(1)
pref = re.search(r"\$table_prefix\s*=\s*['\"]([^'\"]+)['\"]", src)
text = creds.read_text(encoding='utf-8')
text = re.sub(r'^DB-(Host|Name|User|Pass):.*\n?', '', text, flags=re.M).rstrip('\n') + '\n'
text += f"DB-Host: {vals['DB_HOST']}\nDB-Name: {vals['DB_NAME']}\nDB-User: {vals['DB_USER']}\nDB-Pass: {vals['DB_PASSWORD']}\n"
creds.write_text(text, encoding='utf-8')
print(f"   DB-Zugang übernommen: Host {vals['DB_HOST']}, Datenbank {vals['DB_NAME']}, User {vals['DB_USER']}; Präfix der alten Seite: {pref.group(1) if pref else '?'}")
PY
		rm -f "$tmp"
		echo "== alte .htaccess (PHP-/Handler-Zeilen)"
		sftp "sftp://$HOST/~/$SITE/.htaccess" 2>/dev/null | grep -iE 'php|handler|rewrite|redirect' | head -12 | sed 's/^/   /' || echo "   keine oder nicht lesbar"
		;;
	config)
		python3 - "$DEP/wp-config.php" "$CREDS" <<'PY'
import sys, re, pathlib
cfg = pathlib.Path(sys.argv[1]); creds = pathlib.Path(sys.argv[2]).read_text(encoding='utf-8')
def c(k):
    m = re.search(r'^' + re.escape(k) + r':\s*(.+)$', creds, re.M)
    if not m: sys.exit(f"{k} fehlt in .deploy-creds.txt (erst: dbcreds)")
    return m.group(1).strip()
s = cfg.read_text(encoding='utf-8')
for ph, k in (('<DB_NAME>', 'DB-Name'), ('<DB_USER>', 'DB-User'), ('<DB_PASSWORD>', 'DB-Pass'), ('<DB_HOST>', 'DB-Host')):
    s = s.replace(ph, c(k).replace("'", "\\'"))
cfg.write_text(s, encoding='utf-8')
rest = re.findall(r'<DB_[A-Z]+>', s)
print("   wp-config.php ausgefüllt" if not rest else f"   Platzhalter übrig: {rest}")
PY
		;;
	upload)
		grep -q '<DB_' "$DEP/wp-config.php" && { echo "Abbruch: wp-config.php hat noch Platzhalter (erst: config)"; exit 1; }
		for f in _fwg_install.php fwg-site.zip fwg-prod.sql; do
			[ -f "$DEP/$f" ] || { echo "Fehlt: $DEP/$f (erst tools/deploy-prep.sh)"; exit 1; }
			printf '   %-18s' "$f"
			sftp -T "$DEP/$f" "sftp://$HOST/~/$SITE/" && echo "hochgeladen"
		done
		printf '   %-18s' "wp-config-new.php"
		sftp -T "$DEP/wp-config.php" "sftp://$HOST/~/$SITE/wp-config-new.php" && echo "hochgeladen"
		echo "== Inhalt von $SITE"
		listing "" | sed 's/^/   /'
		;;
	install)
		steps=${2:-"check clear unzip db activate"}
		for step in $steps; do
			echo "== $step"
			out=$(curl -s -w '\n__HTTP_%{http_code}__' "$PROD/_fwg_install.php?t=$(cat "$DEP/install-token.txt")&step=$step")
			code=$(echo "$out" | grep -oE '__HTTP_[0-9]+__' | grep -oE '[0-9]+')
			out=$(echo "$out" | grep -v '__HTTP_')
			echo "$out" | sed 's/^/   /'
			if [ "$code" != "200" ] || [ -z "$out" ] || echo "$out" | grep -qE 'FEHLT|[1-9][0-9]* Fehler|Fehler:|fehlgeschlagen|Abbruch|Forbidden|Platzhalter|NEIN|Fatal|Parse error|Warning'; then
				echo "Abbruch bei $step (HTTP $code)"; exit 1
			fi
		done
		echo "== Startseite"
		echo "   $PROD -> HTTP $(curl -s -o /dev/null -w '%{http_code}' "$PROD/")"
		;;
	cleanup)
		installer cleanup | sed 's/^/   /'
		echo "== Reste im Ordner (muss leer sein)"
		listing "" | grep -E '_fwg_install|fwg-site|fwg-prod|wp-config-new' | sed 's/^/   /' || echo "   keine"
		echo "   Installer per HTTP -> $(curl -s -o /dev/null -w '%{http_code}' "$PROD/_fwg_install.php") (404 erwartet)"
		;;
	*)
		sed -n 2,20p "$0"
		;;
esac
