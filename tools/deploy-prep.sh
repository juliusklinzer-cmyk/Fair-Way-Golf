#!/usr/bin/env bash
# Baut das Deploy-Paket für den Dateitausch auf Hetzner (ohne Duplicator):
#   wordpress/_build/deploy/fwg-site.zip      WordPress-Core + Theme + mu-plugins + .htaccess (ohne wp-config.php)
#   wordpress/_build/deploy/fwg-prod.sql      Datenbank mit Produktions-URL (https://www.fair-way-golf.com)
#   wordpress/_build/deploy/wp-config.php     Produktions-Konfiguration mit Platzhaltern <DB_*>, <GA_ID>, <SMTP_*>
#   wordpress/_build/deploy/_fwg_install.php  Einmal-Installer (entpackt, importiert, räumt auf), Token in install-token.txt
# Aufruf: bash tools/deploy-prep.sh      (lokaler Stack muss laufen; _build/ ist gitignored)
set -euo pipefail
cd "$(dirname "$0")/.."
OUT=wordpress/_build/deploy
PROD=https://www.fair-way-golf.com
mkdir -p "$OUT" && chmod 777 "$OUT" # WP-CLI im Container schreibt als www-data
rm -f "$OUT"/fwg-site.zip "$OUT"/fwg-prod.sql

echo "== 1. Lokale Instanz bereinigen"
ids=$(./wp.sh post list --post_type=fwg_anmeldung --field=ID </dev/null | tr '\n' ' ')
[ -n "$ids" ] && ./wp.sh post delete $ids --force </dev/null
./wp.sh transient delete --all </dev/null >/dev/null
./wp.sh option update admin_email hallo@fair-way-golf.com </dev/null >/dev/null
./wp.sh user update 1 --user_email=hallo@fair-way-golf.com </dev/null >/dev/null
./wp.sh option delete fwg_rundmail_jobs </dev/null >/dev/null 2>&1 || true
./wp.sh cache flush </dev/null >/dev/null
# Neues Admin-Passwort (das Dev-Passwort gilt als verbrannt); gilt ab jetzt auch lokal
newpass=$(python3 -c "import secrets,string; a=string.ascii_letters+string.digits; print(''.join(secrets.choice(a) for _ in range(24)))")
./wp.sh user update 1 --user_pass="$newpass" --skip-email </dev/null >/dev/null
printf 'Admin-Login fair-way-golf.com (lokal und Produktion, gesetzt %s)
User: julius
Pass: %s
' "$(date +%F)" "$newpass" > .wp-admin-pass.txt
chmod 600 .wp-admin-pass.txt
echo "   neues Admin-Passwort in .wp-admin-pass.txt"

echo "== 2. Datenbank mit Produktions-URL exportieren"
./wp.sh search-replace "http://localhost:8092" "$PROD" --all-tables-with-prefix --export=/var/www/html/_build/deploy/fwg-prod.sql --report-changed-only </dev/null
rest=$(grep -c "localhost:8092" "$OUT/fwg-prod.sql" || true)
echo "   Reste von localhost:8092 im Export: $rest"
ls -la "$OUT/fwg-prod.sql" | awk '{print "   " $5 " Bytes"}'

echo "== 3. .htaccess für den Webroot und für uploads/"
python3 - "$OUT" <<'PY'
import re, sys, pathlib
out = pathlib.Path(sys.argv[1])
doc = pathlib.Path('docs/server-htaccess.md').read_text(encoding='utf-8')
block = re.search(r"```apache\n(.*?)```", doc, re.S).group(1)
# auskommentierten Uploads-Teil entfernen (kommt als eigene Datei)
block = re.sub(r"# Keine PHP-Ausführung in Uploads.*", "", block, flags=re.S).rstrip() + "\n"
wp = """
# BEGIN WordPress
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteBase /
RewriteRule ^index\\.php$ - [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . /index.php [L]
</IfModule>
# END WordPress
"""
(out / 'htaccess-webroot.txt').write_text(block + wp, encoding='utf-8')
(out / 'htaccess-uploads.txt').write_text('<FilesMatch "\\.php$">\n  Require all denied\n</FilesMatch>\n', encoding='utf-8')
print("   htaccess-webroot.txt, htaccess-uploads.txt geschrieben")
PY

echo "== 4. Produktions-wp-config.php (Platzhalter)"
curl -sf https://api.wordpress.org/secret-key/1.1/salt/ > "$OUT/salts.tmp"
cat > "$OUT/wp-config.php" <<'PHP'
<?php
/**
 * fair-way-golf.com – Produktion (Hetzner Webhosting). Erzeugt von tools/deploy-prep.sh.
 * Platzhalter <...> vor dem Upload ersetzen. Datei liegt NICHT im Repo.
 */
define( 'DB_NAME',     '<DB_NAME>' );
define( 'DB_USER',     '<DB_USER>' );
define( 'DB_PASSWORD', '<DB_PASSWORD>' );
define( 'DB_HOST',     '<DB_HOST>' );
define( 'DB_CHARSET',  'utf8mb4' );
define( 'DB_COLLATE',  '' );

__SALTS__
$table_prefix = '__PREFIX__'; // eigene Tabellen neben denen der alten Seite in derselben Datenbank

define( 'WP_HOME',    '__PROD__' );
define( 'WP_SITEURL', '__PROD__' );

/* Härtung */
define( 'FS_METHOD', 'direct' );
define( 'WP_DEBUG', false );
define( 'WP_DEBUG_DISPLAY', false );
define( 'DISALLOW_FILE_EDIT', true );
define( 'DISABLE_WP_CRON', false ); // vorerst WordPress-eigener Cron; auf true stellen, sobald in konsoleH ein Cronjob auf /wp-cron.php?doing_wp_cron=1 (alle 15 Minuten) läuft
define( 'WP_AUTO_UPDATE_CORE', 'minor' );
define( 'WP_POST_REVISIONS', 5 );

/* Fair-Way-Golf */
define( 'FWG_CONTACT_EMAIL', 'hallo@fair-way-golf.com' );
define( 'FWG_MEETING_URL',   '' ); // HubSpot-Link von Julius; leer = Termin-Links ausgeblendet
define( 'FWG_GA_ID',         '' ); // GA4-Mess-ID; leer = kein Analytics, kein Consent-Banner

/* Brevo-SMTP (mu-plugins/fwg-smtp.php). Host leer = Versand über den Hetzner-Mailer (Fallback).
   Sobald der Brevo-Zugang da ist: Host smtp-relay.brevo.com, Port 587, Login und Key eintragen. */
define( 'FWG_SMTP_HOST',   '' );
define( 'FWG_SMTP_PORT',   587 );
define( 'FWG_SMTP_USER',   '' );
define( 'FWG_SMTP_PASS',   '' );
define( 'FWG_SMTP_SECURE', 'tls' );
define( 'FWG_MAIL_FROM',   'hallo@fair-way-golf.com' );

if ( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && 'https' === $_SERVER['HTTP_X_FORWARDED_PROTO'] ) {
	$_SERVER['HTTPS'] = 'on';
}

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}
require_once ABSPATH . 'wp-settings.php';
PHP
prefix=$(./wp.sh db prefix </dev/null)
python3 - "$OUT" "$PROD" "$prefix" <<'PY2'
import sys, pathlib
out = pathlib.Path(sys.argv[1]); cfg = out / 'wp-config.php'
salts = (out / 'salts.tmp').read_text(encoding='utf-8').strip()
assert salts.count("define(") == 8, "Salts nicht geladen"
assert sys.argv[3] == 'fwg_', f"Lokales Tabellen-Präfix ist {sys.argv[3]!r}, erwartet 'fwg_' (siehe tools/prefix-umstellen.sh)"
cfg.write_text(cfg.read_text(encoding='utf-8').replace('__SALTS__', salts).replace('__PROD__', sys.argv[2]).replace('__PREFIX__', sys.argv[3]), encoding='utf-8')
(out / 'salts.tmp').unlink()
PY2
echo "   wp-config.php geschrieben ($(grep -c "define(" "$OUT/wp-config.php") Konstanten)"

echo "== 5. Dateien packen (Core + Theme + mu-plugins, ohne wp-config, uploads, _build)"
python3 - "$OUT" <<'PY'
import os, sys, zipfile, pathlib
out = pathlib.Path(sys.argv[1]); root = pathlib.Path('wordpress')
skip_top = {'_build', 'wp-config.php', 'wp-config-docker.php', 'wp-config-sample.php', '.htaccess'}
skip_content = {'debug.log', 'upgrade'}
n = 0
with zipfile.ZipFile(out / 'fwg-site.zip', 'w', zipfile.ZIP_DEFLATED) as z:
    for dirpath, dirnames, filenames in os.walk(root):
        rel = pathlib.Path(dirpath).relative_to(root)
        parts = rel.parts
        if parts and parts[0] in skip_top: dirnames[:] = []; continue
        if len(parts) >= 2 and parts[0] == 'wp-content' and parts[1] in skip_content: dirnames[:] = []; continue
        if len(parts) >= 2 and parts[0] == 'wp-content' and parts[1] == 'uploads':
            dirnames[:] = []  # nur index.php aus uploads, keine lokalen Testdateien
            filenames = [f for f in filenames if f == 'index.php']
        if len(parts) >= 2 and parts[0] == 'wp-content' and parts[1] == 'themes':
            dirnames[:] = [d for d in dirnames if d == 'fairwaygolf' or len(parts) > 2]
        for f in filenames:
            if not parts and f in skip_top: continue
            if f.endswith(':Zone.Identifier'): continue
            p = pathlib.Path(dirpath) / f
            z.write(p, str(rel / f)); n += 1
    z.writestr('.htaccess', (out / 'htaccess-webroot.txt').read_text(encoding='utf-8'))
    z.writestr('wp-content/uploads/.htaccess', (out / 'htaccess-uploads.txt').read_text(encoding='utf-8'))
print(f"   {n} Dateien, {os.path.getsize(out / 'fwg-site.zip') // 1024 // 1024} MB")
PY

echo "== 6. Einmal-Installer"
token=$(python3 -c "import secrets; print(secrets.token_hex(16))")
echo "$token" > "$OUT/install-token.txt"
sed "s|__TOKEN__|$token|" tools/deploy-installer.php > "$OUT/_fwg_install.php"
echo "   _fwg_install.php mit Token (install-token.txt)"
echo "== fertig: $OUT"
ls -la "$OUT" | awk 'NR>1 {print "   " $5 "\t" $9}'
