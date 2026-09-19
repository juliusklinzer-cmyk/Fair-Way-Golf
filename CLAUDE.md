# CLAUDE.md — Fair-Way-Golf Website (Neubau)

## Projekt

Kompletter Neubau von **https://fair-way-golf.com** als eigenes WordPress-Theme,
lokal in Docker gebaut, am Ende werden die Dateien auf dem Hetzner-Webhosting
1:1 getauscht. Entscheidung Julius (19.09.2026): Die bestehende Live-Seite
(WordPress + Elementor, stürzt mit PHP-Speicherfehler ab) wird **nicht**
repariert, stabilisiert oder inhaltlich gerettet. Sie bleibt bis zum Tausch
unverändert. Nichts aus ihr wird übernommen.

Unverändert lassen (nicht anfassen, nur beim Go-Live verifizieren): Registrar,
DNS und Webhosting bei Hetzner (konsoleH-Account „VisionPunch", Server
www733.your-server.de, Ordner `fair-way-golf.com` im Webspace c5wkuy), Let's-
Encrypt-SSL, Mail über Microsoft 365 (MX bleibt). Zugangsdaten nie ins Repo,
sondern in `.deploy-creds.txt` (gitignored) wie in den Schwesterprojekten.

Schwesterprojekte als Vorlage, nicht vermischen: `~/projects/firmengolf-web`
(Theme-Aufbau, `docs/go-live-runbook.md`, `docs/server-htaccess.md`, `wp.sh`,
mu-plugin `fg-smtp.php`, Klaro-Banner), `~/projects/benko-web` (`DEPLOY.md`).

## Universum (zentrales Wissen)

Projektübergreifende Wahrheit (Infrastruktur, Mail, Marke, Entscheidungen)
liegt in `~/projects/firmengolf-universum` (eigenes Git-Repo). Bei Sessionstart:
`git -C ~/projects/firmengolf-universum log --oneline $(cat .universum-stand)..HEAD`
zeigt Neues seit dem letzten Sync → relevante Dateien lesen → `.universum-stand`
auf den neuen HEAD setzen (Datei fehlt noch → beim ersten Sync anlegen).
Entscheidungen hier mit Wirkung auf andere Projekte dort dokumentieren.

## Entscheidungen (Julius, 19.09.2026)

- **Zweck**: Relaunch-Seite für den zweiten Anlauf. Besucher sollen sofort sehen: Projekt sucht Unterstützer. Drei Wege: Golfer voranmelden (Wunschplätze, Updates), Golfanlagen anfragen, Investoren und Gründungsmitglieder Interesse zeigen (nur Interesse, keine Konditionen).
- **Betreiber**: Julius Klinzer privat im Impressum, mit Hinweis auf GmbH-Gründung bei Wiederaufnahme. Weder Fair-Golf UG noch VisionPunch UG.
- **Preise**: Modelle und Preise wie auf der alten Seite zeigen, Buttons immer „Voranmelden".
- **Anrede**: durchgehend du. **Team**: nur Julius.
- **Design**: Grün, Weiß, Schwarz der alten Seite; Rajdhani Bold für Überschriften, Barlow als Textschrift; Details in DESIGN.md.
- **Formulare**: Mail an Julius plus Speicherung in WordPress (Liste, Filter, CSV, Rundmail mit Double-Opt-in). Exit-Intent-Popup „Sag uns deinen Lieblingsplatz" bleibt.
- **Dienste**: Brevo-SMTP wie firmengolf, GA4 hinter Klaro (Banner im neuen Design), HubSpot-Kalender als Link. Bilder: vorhandenes Material; neue Bilder erzeugt Julius mit ChatGPT nach Prompts aus `docs/bild-prompts.md`.
- **Rechtstexte**: AGB aus der alten Seite übernehmen und auf Anpassungsbedarf prüfen, Datenschutz neu auf die echten Dienste zuschneiden, Widerruf entfällt.
- **www**: kanonisch mit www (wie bisher).

## Dev-Umgebung (seit 19.09.2026 eingerichtet)

Werkzeuge: `bash tools/screenshot.sh [slug]` (Playwright im Docker, Ganzseiten- und
Sektions-Screenshots nach tools/screenshots/), `bash tools/lighthouse.sh [pfad ...]`
(Lighthouse mobil, Reports nach tools/reports/), `bash tools/formtest.sh` (alle
Formularwege inkl. Double-Opt-in gegen MailHog), `bash tools/import-content.sh`
(Rechtstexte aus content/rechtliches/ in die Seiten), Bild-Pipeline
`docker exec fwg_wordpress php -d memory_limit=1024M /var/www/html/_build/images.php`
(Quellen in wordpress/_build/src/). Alle WSL-Skripte über
`wsl.exe -- bash -c 'cat > /tmp/x.sh && bash -l /tmp/x.sh' < datei` starten (Befehle
über die Bash-Brücke unter 8 KB halten, `./wp.sh … </dev/null`, weil der Container stdin liest).

```bash
docker compose up -d        # WordPress, MariaDB, phpMyAdmin, MailHog
./wp.sh <command>           # WP-CLI, z. B. ./wp.sh plugin list
```

- WordPress: http://localhost:8092 · phpMyAdmin: http://localhost:8093 ·
  MailHog: http://localhost:8027
- Ports 8080/8081/8025 (firmengolf-events), 8090/8091/8026 (firmengolf-web) und
  8088 (benko) gehören anderen Stacks. Nicht anfassen.
- Eigener Code lebt nur in `wordpress/wp-content/themes/fairwaygolf/` und
  `wordpress/wp-content/mu-plugins/`. Alles andere unter `wordpress/` und `db/`
  ist generiert und gitignored.
- Weitere Ordner: `docs/` (Runbooks, Specs, alte URLs, offene Punkte),
  `design/` (Design-Handoff, read-only), `content/` (Texte als Markdown),
  `tools/` (Test-Skripte, z. B. Playwright).

## Technische Leitplanken

- Kein Elementor, kein Page-Builder, keine Plugin-Sammlung. Inhalte in
  Templates (`front-page.php`, `page-*.php`, `template-parts/`) oder als
  Datenarrays. Erlaubte Plugins: Duplicator nur zum Deploy, sonst keines ohne
  Eintrag hier.
- Schriften selbst gehostet (WOFF2), keine Google-CDN. Consent über Klaro,
  Drittdienste (Analytics, Kalender, Karten, Video) erst nach Einwilligung als
  Zwei-Klick-Lösung. Mail-Versand über Brevo-SMTP per mu-plugin.
- Alle Farben, Abstände, Schriften, Radien und Easings als CSS-Variablen aus
  `DESIGN.md`; keine Hex-Farben außerhalb der Token-Datei.
- Formulare: eigener Handler mit Nonce, Honeypot, Rate-Limit, Server-Validierung,
  funktionieren ohne JavaScript. Jede Ausgabe escaped, jede Eingabe sanitized.
- PHP 8.x, WordPress-Coding-Standards, Textdomain `fairwaygolf`.
- Texte: kein Gendern, keine Gedankenstriche als Satzzeichen, deutsche
  Anführungszeichen, Golf immer positiv (keine Vorurteile wiederholen).
  Texte sind Entwürfe für Julius, nichts darf nach KI klingen.
- Barrierefreiheit nach WCAG 2.1 AA ist Pflicht (BFSG), inklusive Seite
  „Erklärung zur Barrierefreiheit".

## Skills (welche wann)

Liegen als Kopien in `.claude/skills/` (Quellen in `skills-lock.json`); die
Design-Skills sind zusätzlich global installiert.

- **Vor jeder UI-Arbeit**: `impeccable` (Vorgehen, Kritik, Polish),
  `emil-design-eng` (Interaktion, Motion-Entscheidungen, unsichtbare Details),
  `design-taste-frontend` = der taste-skill von Leonxlnx (Basis des Neubaus: Design-Read, Dials, Layout-Regeln, Anti-Slop-Checkliste),
  `frontend-design` ergänzend.
- **WordPress-Code**: `wordpress-router` zuerst, dann je nach Aufgabe
  `wp-plugin-development`, `wp-performance`, `wp-wpcli-and-ops`, `wp-phpstan`,
  `wp-rest-api`. `wp-block-development`, `wp-abilities-api`, `blueprint`,
  `wp-project-triage` nur bei Bedarf.
- **Planung**: `spec-driven-development` vor neuen Features,
  `planning-and-task-breakdown` zur Zerlegung.
- **Inhalte und SEO**: `content-strategy`, `seo-audit`, `ai-seo`;
  `programmatic-seo` nur, falls Platz-Seiten in Serie entstehen.
- **Vor jedem größeren Bauabschnitt**: `grill-me` (Julius zum Plan ausfragen, bis alle Verzweigungen geklärt sind).
- **Session-Ende**: `abschluss`. Fehlt etwas: `find-skills`.
- `building-native-ui` wird nur mitgeführt (App-Projekte), hier nicht nutzen.

## Agents (welche wann)

Alle Agents liegen in `.claude/agents/`, prüfen nur und ändern nie Code.
Regel: Wer etwas gebaut hat, nimmt es nicht selbst ab. Befunde werden
behoben oder begründet in `docs/offene-punkte.md` eingetragen.

| Agent | Einsatz |
|---|---|
| `design-ci-pruefer` | nach jeder neuen oder geänderten Seite bzw. Komponente; vor Release |
| `besucher-tester` | nach jeder Seite und jedem Prozess (Formular, Consent, Termin); vor Release |
| `barrierefreiheit-pruefer` | nach jeder Seite oder Komponente; vor Release |
| `datenschutz-sicherheit-pruefer` | bei neuem Formular oder Drittdienst, nach Änderungen an functions.php, mu-plugins, wp-config, .htaccess; vor Release |
| `text-lektor` | sobald Texte stehen, nach Textänderungen; vor Release |
| `seo-pruefer` | nach Festlegung der Seitenstruktur, nach neuen Seiten; vor Release |
| `performance-pruefer` | sobald die Startseite steht, nach Bild- oder Skriptänderungen; vor Release |
| `wp-code-pruefer` | vor jedem Commit, der PHP berührt; vor Release |
| `release-pruefer` | unmittelbar vor und nach dem Dateitausch auf Hetzner |

## Deploy (Kurzfassung)

Lokal Duplicator-Paket bauen → auf Hetzner alten Ordner umbenennen (nicht
löschen), neue Installation in den DocumentRoot, eigene neue Datenbank,
URL-Tausch, produktive `wp-config.php` härten, 301-Liste aus
`docs/alte-urls.md` aktivieren, Smoke-Test, Installer-Reste entfernen. DNS,
SSL und MX bleiben unangetastet. Details entstehen in `docs/go-live-runbook.md`
nach Vorbild von firmengolf-web.

## Alte Seite (nur zur Kenntnis)

Wird nicht übernommen. Falls je etwas gebraucht wird: one.com-Backup vom
02.07.2026 in `C:\Users\Julius\Downloads` (Datenbank, Website-Dateien,
Postfach hallo@). Alte URL-Liste für Weiterleitungen: `docs/alte-urls.md`.
