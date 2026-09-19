# Go-Live-Runbook: fair-way-golf.com (Dateitausch auf Hetzner)

**Go-Live erfolgt am 19.09.2026, ca. 18:00 Uhr.** Ablauf nach Vorbild von firmengolf.app, aber ohne
Duplicator (eigener Einmal-Installer). DNS, Let's-Encrypt-SSL und Microsoft-365-Mail (MX) wurden nicht
verändert. Kanonische Variante: https://www.fair-way-golf.com/.

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

## Phase A: Lokal paketieren (ohne Duplicator)

`bash tools/deploy-prep.sh` erledigt alles auf einmal und legt das Paket in `wordpress/_build/deploy/`
(gitignored): Testanmeldungen und Transients löschen, Admin-E-Mail auf hallo@, neues Admin-Passwort
(landet in `.wp-admin-pass.txt`), Datenbank-Export mit Produktions-URL (`fwg-prod.sql`), Dateipaket
`fwg-site.zip` (Core, Theme, mu-plugins, beide `.htaccess`), produktive `wp-config.php` mit frischen
Salts und Platzhaltern sowie der Einmal-Installer `_fwg_install.php` (Token in `install-token.txt`).

- [x] `bash tools/deploy-prep.sh` gelaufen, Ausgabe ohne Fehler, „Reste von localhost:8092“ = 0.
- [x] In `wordpress/_build/deploy/wp-config.php` die Platzhalter `<DB_NAME>`, `<DB_USER>`, `<DB_PASSWORD>`,
      `<DB_HOST>` aus `.deploy-creds.txt` eintragen; GA-ID, HubSpot-Link und Brevo-Zugang eintragen,
      sobald vorhanden (können auch später direkt auf dem Server ergänzt werden).

## Phase B: Ziel vorbereiten

- [x] Datenbank: Entscheidung Julius (19.09.2026), die **bestehende** Datenbank der alten Seite wird
      weiterverwendet. Die neue Seite legt ihre Tabellen mit dem Präfix `fwg_` daneben, die alten
      `wp_`-Tabellen bleiben unangetastet (Rollback). Zugang holt `bash tools/deploy-push.sh dbcreds`
      aus der alten `wp-config.php` auf dem Server in `.deploy-creds.txt`. Lokal vorher einmal
      `bash tools/prefix-umstellen.sh` (stellt die lokale Instanz auf `fwg_` um).
- [ ] PHP-Version für fair-way-golf.com in konsoleH auf 8.3 stellen (lokal getestet: 8.3; live läuft 8.2, funktioniert), macht Julius.

## Phase C: Dateitausch und Installation (SFTP + Installer)

Alles über `bash tools/deploy-push.sh <schritt>` (curl per SFTP auf www733.your-server.de, Port 22,
Account `c5wkuy`; Zugang und DB-Daten in `.deploy-creds.txt`, Format im Skriptkopf). Jeder Schritt ist
einzeln und prüft seine Voraussetzungen.

Entscheidung Julius (19.09.2026): kein Rollback nötig, die alte Seite kann komplett weg, und es wird
**nur innerhalb von `fair-way-golf.com`** gearbeitet, weil auf dem Server weitere Seiten liegen. Kein
Umbenennen im Webspace-Root.

- [x] `status`: Inhalt von `fair-way-golf.com` (alte WordPress-Dateien), Domain antwortet.
- [x] `dbcreds`: DB-Zugang der alten Seite in `.deploy-creds.txt` übernehmen; alte `.htaccess` auf
      PHP-Handler-Zeilen prüfen (falls Hetzner die PHP-Version darüber setzt, Zeile in
      `docs/server-htaccess.md` übernehmen und Paket neu bauen).
- [x] `config`: DB-Zugang aus `.deploy-creds.txt` in `wordpress/_build/deploy/wp-config.php` eintragen.
- [x] `upload`: Installer, Zip, SQL und die neue Konfiguration als `wp-config-new.php` in den Ordner
      laden. Die alte Seite läuft dabei weiter.
- [x] `install`: Installer-Schritte `check` (fwg_-Tabellen = 0), `clear` (löscht die alte Seite im
      Ordner), `unzip`, `db` (Import, 0 Fehler, siteurl/home = https://www.fair-way-golf.com),
      `activate` (wp-config-new.php wird wp-config.php). Zwischen `clear` und `activate` liefert die
      Domain wenige Minuten lang eine leere Seite. Danach Startseite HTTP 200.
- [x] `cleanup`: löscht Zip, SQL und Installer auf dem Server; Installer-URL muss danach 404 liefern.
- [x] Die alten `www0_`-Tabellen bleiben in der Datenbank `c5wkuy_db0` (geteilt mit firmengolf.app) (Voranmeldungen aus dem ersten Anlauf);
      Löschen oder Export später entscheiden.

## Phase D: Nacharbeiten auf dem Server

- [ ] Login unter https://www.fair-way-golf.com/wp-login.php mit `julius` und dem neuen Passwort aus
      `.wp-admin-pass.txt`.
- [x] Einstellungen → Permalinks → Speichern (Rewrites, `wp-sitemap.xml`).
- [x] `blog_public = 1` prüfen (Einstellungen → Lesen, Suchmaschinen erlaubt).
- [ ] Externen Cron einrichten (konsoleH → Cronjobs oder cron-job.org): `https://www.fair-way-golf.com/wp-cron.php?doing_wp_cron=1`
      alle 15 Minuten; danach in der wp-config.php `DISABLE_WP_CRON` auf `true` stellen (steht seit dem
      Go-Live auf `false`, damit Rundmails auch ohne externen Cron laufen).
- [ ] konsoleH: HTTPS-Weiterleitung, HSTS (mindestens `max-age=31536000`), TLS 1.2+ aktiv (war schon so).
- [x] Rechtstexte final: `curl -s https://www.fair-way-golf.com/impressum/ | grep -c '<mark>'` muss 0 ergeben
      (ebenso für /datenschutz/).

## Phase E: Smoke-Test (extern)

- [x] `curl -I https://fair-way-golf.com/` → 301 auf www; `https://www.fair-way-golf.com/` → 200.
- [x] Jede alte URL aus `docs/alte-urls.md` per `curl -I` prüfen (301 bzw. 410): am 19.09.2026 alle korrekt.
- [ ] Alle vier Formulare einmal real absenden: Mail bei hallo@ (nicht Spam), Bestätigungsmail beim
      Absender, SPF/DKIM/DMARC PASS im Header (Gmail „Original anzeigen").
- [ ] Double-Opt-in-Link klicken → „bestätigt", Abmeldelink → „abgemeldet".
- [ ] Backend: Anmeldungen → Liste, Filter, CSV-Export, Rundmail-Testmail an dich.
- [ ] Consent-Banner erscheint (nur mit GA-ID), Ablehnen lädt kein Google-Skript, Annehmen schon.
- [x] `/wp-sitemap.xml` → 200, `/robots.txt` → 200, `/gibt-es-nicht/` → 404, `/shop/` → 410.
- [ ] Search Console: Property prüfen, Sitemap einreichen. UptimeRobot-Monitor anlegen.
- [ ] Alte `www0_`-Tabellen in `c5wkuy_db0`: Voranmeldungen aus dem ersten Anlauf exportieren, dann Tabellen löschen (Entscheidung Julius).

## Rollback

Entfällt (Entscheidung Julius, 19.09.2026): Die alte Seite wird beim Schritt `clear` gelöscht.
Falls doch einmal nötig: one.com-Backup vom 02.07.2026 in `C:\Users\Julius\Downloads` plus die
alten `www0_`-Tabellen in der Datenbank.

## Nach dem Go-Live (Stand 19.09.2026, 18:10 Uhr)

Erledigt: Tausch im Ordner, Smoke-Test von außen (alle Seiten 200, 404/410, Redirects www/https/feed/page,
Systemdateien 403, Security-Header, Asset-Cache, keine Platzhalter), ein Test-Formular auf der Live-Seite
(Eintrag „Test Go-Live“, Mails an hallo@). Offen für Julius: Login mit dem Passwort aus `.wp-admin-pass.txt`,
Testeintrag löschen, Mails bei hallo@ prüfen (Eingang und Bestätigungsmail, ggf. Spam), Cron in konsoleH,
HSTS in konsoleH, Search Console (Sitemap `https://www.fair-way-golf.com/wp-sitemap.xml`), UptimeRobot,
Brevo-Zugang, GA4-ID und HubSpot-Link in die `wp-config.php` auf dem Server (Ordner `fair-way-golf.com`).
