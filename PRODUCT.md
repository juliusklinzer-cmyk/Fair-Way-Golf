# Product

<!-- impeccable:product-schema 1 -->

## Platform

web

## Stack

WordPress-Theme `fairwaygolf` mit nativem CSS und JavaScript, ohne Build-Schritt (Entscheidung Julius, 19.09.2026: gleiches Muster wie benko und firmengolf.app, Deploy per Duplicator auf Hetzner Webhosting).

## Users

- **Golfer im Raum München und Bayern**, die auf vielen Plätzen spielen wollen statt auf einem. Situation: Sie hören vom Projekt, wollen wissen, ob es weitergeht, und sich unverbindlich vormerken lassen, inklusive Wunschplätzen.
- **Golfanlagen** (Clubmanager, Präsidium), die neue Zielgruppen und Mitglieder gewinnen wollen, ohne Kosten und ohne Gebietsschutz.
- **Investoren und Gründungsmitglieder**, die den zweiten Anlauf finanzieren oder tragen wollen. Aktuell wird nur Interesse gesammelt, keine Konditionen.

Alle Zielgruppen werden mit „du" angesprochen (Entscheidung Julius: im Golf geht man persönlich und freundschaftlich miteinander um).

## Product Purpose

Fair-Way-Golf ist ein Mitgliedschaftsmodell: eine Mitgliedschaft, viele Partnerplätze, kein Greenfee. Der Heimatclub bleibt der eigene oder wird der nächste Partnerclub; er stellt DGV-Ausweis und Handicapführung. Kategorien Practice, S, M, L, XL. App mit Startzeitenbuchung, Turnieranmeldung (nur Startgebühr) und Mobile-Bag-Tag (QR-Check-in). Für Golfanlagen kostenfrei, ohne Gebietsschutz, mit voller Auszahlung an den Heimatclub, auch wenn ein Mitglied nicht spielt.

Die Website hat heute einen Zweck: Sie macht Besuchern klar, dass Fair-Way-Golf ein Projekt auf der Suche nach Unterstützern ist, und sammelt drei Arten von Interesse: Voranmeldungen von Golfern (mit Wunschplätzen und Update-Einwilligung), Anfragen von Golfanlagen und Interesse von Investoren oder Gründungsmitgliedern. Erfolg heißt: verwertbare Voranmeldungen und Anfragen, sauber gespeichert, erreichbar für Info-Mails.

## Positioning

Kein Gebietsschutz und echte Clubmitgliedschaft statt Fernmitgliedschaft: Golfer werden Mitglied im Partnerclub, Clubs werben sich nicht gegenseitig Bestandsmitglieder ab, und die Auszahlung an den Heimatclub ist garantiert. Dazu ein digitaler Ablauf (App, Bag-Tag, Startzeiten) ohne Mehraufwand für die Anlage.

## Operating Context

- Status: Die Fair-Golf UG (haftungsbeschränkt) hat das Projekt aus finanziellen Gründen gestoppt und existiert nicht mehr. Über 2.000 Voranmeldungen und 20 Golfanlagen in Bayern waren dabei. Die Seite ruft zum zweiten Anlauf auf.
- Betreiber der Website: Julius Klinzer privat (Impressum mit Privatname und Hinweis, dass bei Wiederaufnahme eine GmbH gegründet wird). VisionPunch UG ist NICHT Betreiber.
- Preise und Modelle werden wie auf der alten Seite gezeigt (monatlich Practice 35 €, S 115 €, M 165 €, L 215 €, XL 255 €; jährlich 395, 1.295, 1.895, 2.495, 2.995 €), aber jeder Button heißt „Voranmelden", nie „Abschließen"; damit ist klar, dass nichts live ist.
- Anfragen: Mail an Julius plus Speicherung in WordPress (eigene Liste mit Filtern, CSV-Export, Rundmail an Gruppen). Info-Mails nur mit Double-Opt-in.
- Terminbuchung: Julius' HubSpot-Kalender als Link (kein Embed).
- Mailversand: Brevo-SMTP über mu-plugin (wie firmengolf). Analytics: GA4 hinter Klaro-Consent. Consent-Banner im neuen Design.
- Exit-Intent-Popup „Sag uns deinen Lieblingsplatz" bleibt als leichter Sammelkanal.
- Team: nur Julius Klinzer.
- Hosting: Hetzner Webhosting (Ordner fair-way-golf.com), Mail-Postfächer bei Microsoft 365, Domain und DNS unverändert.

## Capabilities and Constraints

- Formulare: Voranmeldung (Golfer), Golfanlage, Unterstützen (Investor/Gründungsmitglied), Lieblingsplatz (Popup), Updates. Alle ohne Drittdienst, mit Nonce, Honeypot, Rate-Limit, ohne JavaScript nutzbar.
- Keine Verträge, keine Zahlungen auf der Seite. AGB werden aus der alten Seite übernommen und geprüft; Widerruf entfällt.
- Kein Page-Builder, keine Plugin-Sammlung. PHP 8.3, WordPress 7.
- Undecided: Konditionen für Gründungsmitglieder (nur Interesse), Zeitpunkt des Neustarts.

## Brand Commitments

- Name „Fair-Way-Golf" (mit Bindestrichen). Logo: schwarzer Pin mit Golfschläger (`design/alt-medien/cropped-2.png`, Fwg-basis.svg).
- Farben: Grün, Weiß, Schwarz wie auf der alten Seite (Marke `#005949`, Akzentgrün aus dem alten Set), Julius verbindlich.
- Überschriften: Rajdhani Bold (Google Font, selbst gehostet). Textschrift: passend dazu, Vorschlag Barlow.
- Tonalität: persönlich, freundschaftlich, du. Golf positiv, keine Vorurteile wiederholen. Keine Gedankenstriche.

## Evidence on Hand

- Alte Seitentexte: `content/alt/*.txt` (Home, Über uns, Partner-Port, Voranmeldung, Golf in München, Impressum, Datenschutz, AGB).
- Alte Medien: `design/alt-medien/` (Luftaufnahmen 2560 px, Teamfoto, KI-Bilder, App-Mockups als SVG, Logos, Partnerlogos GMVD Poolpartner, Golf Manager, Startup Creator).
- Fotos: `~/projects/Bilder für Projekte/Firmengolf Bilder/`.
- Zahlen: über 2.000 Voranmeldungen, 20 Golfanlagen in Bayern, über 50 Anlagen im Austausch (2021). Keine Testimonials mit echten Namen verwenden (alte waren Platzhalter).
- Fehlend: aktuelle Partnerplatz-Liste, Konditionen für Gründungsmitglieder, GA4-Mess-ID, Brevo-Zugang, HubSpot-Meeting-Link. Nichts davon erfinden; Platzhalter als Konstanten.

## Product Principles

1. Ehrlich über den Status: Projekt, nicht laufender Betrieb. Jede Handlung heißt Voranmelden oder Interesse zeigen.
2. Drei Zielgruppen, drei klare Wege, ein gemeinsamer Ton.
3. Jede Anfrage bleibt im eigenen System (WordPress) und ist per Mail erreichbar.
4. Nichts lädt von Dritten ohne Einwilligung.
5. Golf als Lebensgefühl zeigen, nicht als Status.
