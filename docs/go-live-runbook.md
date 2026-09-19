# Go-Live-Runbook: fair-way-golf.com (Dateitausch auf Hetzner)

Stand: 19.09.2026. Ablauf nach Vorbild von firmengolf.app. Die alte Seite bleibt
bis zum Tausch unangetastet. DNS, Let's-Encrypt-SSL und Microsoft-365-Mail (MX)
werden nicht verändert. Kanonische Variante: https://www.fair-way-golf.com/.

## Phase 0: Vorbereiten (vor dem Umzugstag)

- [x] **Impressum und Datenschutz**: Privatanschrift von Julius (Görresstraße 1, 80798 München) am
      19.09.2026 in `content/rechtliches/impressum.html` und `datenschutz.html` eingetragen und importiert.
- [ ] **AGB**: Zahlungsdienstleister ist Stripe (eingetragen 19.09.2026); nur die Kündigungsanschrift bleibt bis zum
      echten Start markiert (siehe `docs/offene-punkte.md`).
- [ ] **Brevo**: Domain fair-way-golf.com bei Brevo authentifizieren (DKIM-CNAMEs `brevo1._domainkey`,
      `brevo2._domainkey` in der Hetzner-DNS-Zone anlegen, SPF um `include:spf.brevo.com` ergänzen; der
      bestehende Eintrag `include:spf.protection.outlook.com` für Microsoft 365 bleibt). Eigenen SMTP-Key
      für dieses Projekt erzeugen, in `.deploy-creds.txt` ablegen. Absender `FWG_SMTP_FROM` muss eine
      Adresse der Domain sein.
- [ ] **Microsoft 365 DKIM**: Im Defender-Portal DKIM für fair-way-golf.com aktivieren (CNAMEs
      `selector1._domainkey`, `selector2._domainkey`), sonst scheitert DMARC für die normalen Mails.
- [ ] **DMARC** in Stufen: erst `v=DMARC1; p=none; rua=mailto:dmarc@fair-way-golf.com`, nach zwei
      Wochen ohne Fehlberichte `p=quarantine`, später `p=reject`.
- [ ] **GA4**: Property für www.fair-way-golf.com anlegen, Mess-ID notieren. In der Property:
      Datenaufbewahrung auf 14 Monate, Google-Signale aus, keine Werbefunktionen (so steht es in der
      Datenschutzerklärung).
- [ ] **HubSpot**: Meeting-Link von Julius prüfen (Konstante `FWG_MEETING_URL`).
- [ ] **Postfach**: `hallo@fair-way-golf.com` existiert in Microsoft 365 (Alias auf Julius)? Sonst
      `FWG_CONTACT_EMAIL` auf `info@fair-way-golf.com` setzen.
- [ ] **Prüf-Agents**: Alle Berichte grün oder Befunde in `docs/offene-punkte.md` begründet.
      `release-pruefer` ausführen.

## Phase A: Lokal paketieren

- [ ] Testdaten löschen: `./wp.sh post delete $(./wp.sh post list --post_type=fwg_anmeldung --field=ID) --force`
- [ ] Admin-Passwort auf ein neues, starkes setzen (das Dev-Passwort in `.wp-admin-pass.txt` gilt als verbrannt).
- [ ] Duplicator lokal installieren (`./wp.sh plugin install duplicator --activate`), Paket bauen
      (Archiv + `installer.php`) unter http://localhost:8092/wp-admin/, danach Duplicator lokal deinstallieren.
      Ausschließen: nichts Zusätzliches nötig (Uploads sind leer, `_build/` liegt außerhalb von wp-content
      und wird von Duplicator nur mitgenommen, wenn nicht ausgeschlossen: **`_build` ausschließen**).

## Phase B: Ziel vorbereiten (konsoleH)

- [ ] Neue MySQL-Datenbank anlegen (DB-Host ist NICHT localhost, sondern `lx…your-database.de`;
      Hauptlogin verwenden, der R/W-Login reicht für Core-Updates nicht). Zugang in `.deploy-creds.txt`.
- [ ] Per SFTP (www733.your-server.de, Port 22) den Ordner `fair-way-golf.com` in
      `fair-way-golf.com_alt_2026-09` umbenennen. Neuen leeren Ordner `fair-way-golf.com` anlegen.
      Falls konsoleH die Domain auf einen festen Pfad zeigt, bleibt der Pfad gleich.
- [ ] PHP-Version in konsoleH auf 8.3 (oder die lokal getestete Version) stellen.

## Phase C: Hochladen und installieren

- [ ] `installer.php` + Archiv in den neuen Ordner laden, `https://www.fair-way-golf.com/installer.php` öffnen.
- [ ] DB-Daten eintragen, URL-Tausch `http://localhost:8092` → `https://www.fair-way-golf.com`.
- [ ] Mit den lokalen Admin-Daten einloggen, Installer-Aufräumung bestätigen, Duplicator deinstallieren.

## Phase D: wp-config.php produktiv (manuell ergänzen)

```php
define( 'WP_HOME',    'https://www.fair-way-golf.com' );
define( 'WP_SITEURL', 'https://www.fair-way-golf.com' );

/* Härtung */
define( 'FS_METHOD', 'direct' );
define( 'WP_DEBUG', false );
define( 'WP_DEBUG_DISPLAY', false );
define( 'DISALLOW_FILE_EDIT', true );
define( 'DISABLE_WP_CRON', true ); // externer Cron, siehe Phase E

/* Fair-Way-Golf */
define( 'FWG_CONTACT_EMAIL', 'hallo@fair-way-golf.com' );
define( 'FWG_MEETING_URL',   'https://meetings.hubspot.com/…' );
define( 'FWG_GA_ID',         'G-XXXXXXXXXX' ); // leer lassen = kein Analytics, kein Banner

/* Brevo-SMTP (mu-plugins/fwg-smtp.php) */
define( 'FWG_SMTP_HOST',   'smtp-relay.brevo.com' );
define( 'FWG_SMTP_PORT',   587 );
define( 'FWG_SMTP_USER',   '<brevo-login>' );
define( 'FWG_SMTP_PASS',   '<brevo-smtp-key>' );
define( 'FWG_SMTP_SECURE', 'tls' );
define( 'FWG_MAIL_FROM',   'hallo@fair-way-golf.com' );
```

## Phase E: Server-Feinschliff

- [ ] `.htaccess` im Webroot um den Block aus `docs/server-htaccess.md` ergänzen (Kompression,
      Cache-Header, `Options -Indexes`, Sperre für `xmlrpc.php`, `readme.html`, `license.txt`,
      `wp-config.php`); die Uploads-`.htaccess` (kein PHP) separat nach `wp-content/uploads/` legen.
- [ ] Einstellungen → Permalinks → Speichern (Rewrites, `wp-sitemap.xml`).
- [ ] `blog_public = 1` prüfen (Einstellungen → Lesen, Suchmaschinen erlaubt).
- [ ] Externen Cron einrichten (cron-job.org): `https://www.fair-way-golf.com/wp-cron.php?doing_wp_cron=1` alle 15 Minuten.
- [ ] konsoleH: HTTPS-Weiterleitung, HSTS (mindestens `max-age=31536000`), TLS 1.2+ aktiv (war schon so).
- [ ] Rechtstexte final: `curl -s https://www.fair-way-golf.com/impressum/ | grep -c '<mark>'` muss 0 ergeben
      (ebenso für /datenschutz/).

## Phase F: Smoke-Test (extern)

- [ ] `curl -I https://fair-way-golf.com/` → 301 auf www; `https://www.fair-way-golf.com/` → 200.
- [ ] Jede alte URL aus `docs/alte-urls.md` per `curl -I` prüfen (301 bzw. 410).
- [ ] Alle vier Formulare einmal real absenden: Mail bei hallo@ (nicht Spam), Bestätigungsmail beim
      Absender, SPF/DKIM/DMARC PASS im Header (Gmail „Original anzeigen").
- [ ] Double-Opt-in-Link klicken → „bestätigt", Abmeldelink → „abgemeldet".
- [ ] Backend: Anmeldungen → Liste, Filter, CSV-Export, Rundmail-Testmail an dich.
- [ ] Consent-Banner erscheint (nur mit GA-ID), Ablehnen lädt kein Google-Skript, Annehmen schon.
- [ ] `/wp-sitemap.xml` → 200, `/robots.txt` → 200, `/gibt-es-nicht/` → 404, `/shop/` → 410.
- [ ] Search Console: Property prüfen, Sitemap einreichen. UptimeRobot-Monitor anlegen.
- [ ] Alte Datenbank und Ordner `_alt` 30 Tage aufbewahren, dann löschen.

## Rollback

Ordner `fair-way-golf.com` in `fair-way-golf.com_neu` umbenennen und `fair-way-golf.com_alt_2026-09`
zurück in `fair-way-golf.com`. Die alte Datenbank ist unverändert. Dauer: zwei Minuten.
