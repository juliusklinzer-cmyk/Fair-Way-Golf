# DESIGN.md — Design-Linie Fair-Way-Golf

Verbindliche Quelle für alles Visuelle. Der `design-ci-pruefer` prüft jede
Seite gegen dieses Dokument. Vor JEDER UI-Arbeit lesen. Stand: 19.09.2026,
zweiter Entwurf nach Julius' Referenzen (Tennis- und Padel-Landingpages:
Foto-Panel mit runden Ecken, weiße luftige Fläche, Bildkacheln, Lindgrün als
Akzent, grüner Footer mit großer Wortmarke). Der erste Entwurf („Mulligan“,
dunkle Zahlen- und Footer-Flächen) ist verworfen und kein Vorbild mehr.

## 1. Markenkern und Richtung

- Name: **Fair-Way-Golf** (immer mit Bindestrichen, in Überschriften mit
  geschützten Bindestrichen `&#8209;`). Logo: Pin mit Golfschläger
  (`assets/img/logo-mark.svg`, `currentColor`), Wortmarke in Rajdhani Bold.
- Versprechen und USP: **Eine Mitgliedschaft. Alle Golfplätze.** Kein Greenfee
  auf den Partnerplätzen, echte Clubmitgliedschaft, deutschlandweit.
- Situation: zweiter Anlauf. Die Seite sagt das offen und lädt drei Gruppen
  ein: Golfer (voranmelden), Golfanlagen (Partner werden), Unterstützer
  (Investor oder Gründungsmitglied). Jede Aktion heißt „Voranmelden“, nie
  „Kaufen“ oder „Abschließen“.
- Gestaltungsthese: **Golfplätze sind der Held.** Jede Seite beginnt mit einem
  großen Platzfoto im Panel, der Text liegt auf dem Foto. Das Produkt wird in
  einer Zeile erklärt, bevor irgendetwas anderes passiert.
- Haltung: persönlich, freundschaftlich, du. Hell, sportlich, ruhig. Keine
  dunklen Sektionen, kein Startup-Hero mit drei gleichen Karten.

## 2. Farben: helle Seite, Grün als Marke, Lindgrün als Akzent

Weiß ist die Grundfläche (rund 70 Prozent). Dunkelgrün trägt die Marke und
je Seite ein bis zwei Panels (Schritte, Footer). Lindgrün ist der einzige
Akzent und tritt nur auf Grün oder auf Fotos auf (Primär-Button, Marker),
nie als Fläche auf Weiß und nie als Fließtext. Mint ist die helle Tint-Fläche
für Chips, Kacheln und Formularköpfe.

| Token | Wert | Rolle |
|---|---|---|
| `--fwg-green` | `#005949` | Marke, grüne Panels, Footer, Links, Primär-Button auf Weiß |
| `--fwg-green-deep` | `#013f34` | Hover auf Grün, dunkle Panel-Ecke |
| `--fwg-green-soft` | `#0b6b58` | zweite Panelstufe (App-Kachel) |
| `--fwg-lime` | `#b9f06a` | Akzent auf Grün und Foto: Button, Marker, Zähler |
| `--fwg-lime-deep` | `#9fdb4a` | Hover auf Lime |
| `--fwg-mint` | `#dff3e8` | helle Tint-Kacheln, Chips, Formularkopf |
| `--fwg-ink` | `#101614` | Text auf Weiß und auf Lime |
| `--fwg-ink-muted` | `#5a6661` | Sekundärtext auf Weiß (5,1:1) |
| `--fwg-on-green-muted` | `#cfe6dc` | Sekundärtext auf Grün (9,5:1) |
| `--fwg-surface-alt` | `#f4f6f5` | graue Panels (Modelle, FAQ, Formulare) |
| `--fwg-line` | `#e3e9e6` | Linien, Feldrahmen |
| `--fwg-scrim` | Verlauf | Abdunklung auf Foto-Panels, nur unten kräftig |

Regeln: Text auf Fotos steht immer auf dem Scrim (unten) oder in einer weißen
Karte. Kontrast mindestens 4,5:1 für Fließtext, 3:1 für Display-Text; die
Footer-Wortmarke ist mit 50 Prozent Weiß auf Grün die einzige bewusst leise
Stelle (3,3:1, dekorativ). Keine Verläufe außer dem Scrim, kein Glas, keine
Schatten außer `--fwg-shadow` unter dem Handy-Mockup und der Hero-Karte.

## 3. Typografie

- Display: **Rajdhani** 700 (500/600 für Zahlen und Labels), selbst gehostet
  (`assets/fonts/`). Wortmarke, alle Überschriften, große Zahlen, Labels.
- Body: **Barlow** 400/500/600, selbst gehostet. Fließtext, Formulare, Chips.
- Größen sind Tokens, nichts Freies: `--fwg-text-display` (Hero, 40 bis 76 px),
  `--fwg-text-h2` (34 bis 60 px), `--fwg-text-h3`, `--fwg-text-h4` 22 px,
  `--fwg-text-number` (56 bis 112 px, Intro-Zahl), `--fwg-text-lead`,
  `--fwg-text-body` 17 px, `--fwg-text-small` 15 px, `--fwg-text-micro` 13 px.
- Zeilenhöhen: Display 1.0, Überschriften 1.12, Fließtext 1.6. Lesebreite
  `.fwg-prose` 66ch, Leads maximal 60ch.
- Hierarchie je Seite: genau ein H1 (im Hero), H2 je Sektion, H3 in Kacheln
  und Schritten. Eyebrow-Labels in Rajdhani 600, Versalien, Lindgrün auf Grün
  bzw. Grün auf Weiß.
- Deutsche Typografie: „…“ Anführungszeichen, Halbgeviertstrich mit
  Leerzeichen, geschützte Leerzeichen vor „€“ und in „z. B.“, Zahlen mit
  Tausenderpunkt (2.000).

## 4. Komponenten (je genau eine Ausprägung, `assets/css/main.css`)

- **Header**: weiß, sticky, Logo links, Pill-Navigation mittig (So
  funktioniert es, Modelle, Für Golfanlagen, Unterstützen, Über uns), rechts
  Pill-Button „Voranmelden“ in Grün. Mobil: Burger, Vollbildmenü auf Grün.
- **Hero-Panel** `.fwg-hero__panel`: Foto im Container mit Radius 28,
  Scrim, Text unten links auf Weiß, Lead maximal 60ch, Lime-Button plus
  Ghost-Button (weißer Rahmen). Rechts unten die weiße **Hero-Karte** mit
  Zahl und Satz (2.000+ Golfer, 20 Anlagen). Unterseiten: `.fwg-hero--sub`
  mit niedrigerem Panel und Karte optional.
- **Intro** `.fwg-intro`: große Zahl links (Rajdhani, `--fwg-text-number`),
  Definitionssatz rechts, darunter die Kachelreihe.
- **Kacheln** `.fwg-tile`: Radius 20, Varianten Foto (Titel auf Scrim),
  Grün, Lime, Mint. Immer 3 bis 4 in einer Reihe, Mobil gestapelt. Kein
  Hover-Lift, nur Bild-Zoom 1.03 bei Foto-Kacheln.
- **Panels** `.fwg-panel`: Radius 28, Varianten `--green` (Schritte, Kopf
  Golfanlagen), `--alt` (grau: Modelle, FAQ, Formulare), `--mint`,
  `--photo` (Golfanlagen-Sektion, CTA), `--cta`.
- **Schritte** `.fwg-steps`: Zähler in Lime (Rajdhani 600) links, Titel und
  Satz rechts; auf Grün mit `--fwg-on-green-muted`. Daneben `.fwg-appshot`:
  Handy-Mockup über abgedunkeltem Platzfoto.
- **Modelle** `.fwg-modelle`: fünf weiße Karten auf grauem Panel, Toggle
  Monat/Jahr als Pill, Preis in Rajdhani 700, drei Punkte, jeder Button
  „Voranmelden“. Kein „beliebt“-Badge, keine Hervorhebung einer Karte.
- **Buttons** `.fwg-btn`: Pill, Barlow 600, Höhe 52 px. Primär auf
  Weiß: Grün mit weißem Text. Primär auf Grün oder Foto: Lime mit Ink-Text.
  Ghost: 2 px Rahmen in aktueller Textfarbe. `:active` scale(0.97),
  Fokusring 3 px Lime auf Grün, Grün auf Weiß.
- **Formulare** `.fwg-form`: Felder Radius 14, Rahmen `--fwg-line`, Fokus
  Grün; Radios und Chips als Pills (Mint, gewählt Grün). Fehler inline unter
  dem Feld in `--fwg-error`, Erfolg als grüne Box. Honeypot unsichtbar.
- **FAQ** `.fwg-faq-layout`: Titel links, `<details>` rechts mit Plus-Icon
  aus dem Phosphor-Sprite, Trenner `--fwg-line`.
- **Timeline** `.fwg-timeline`: drei Spalten, Jahr in Rajdhani Grün,
  Linie oben.
- **Porträt** `.fwg-portrait`: Foto-Kachel 4:5, Radius 20, `object-fit:
  cover`, Text rechts.
- **Popup** `.fwg-popup`: weiße Karte, Radius 28, Exit-Intent nur Desktop
  nach 8 s, einmal je Sitzung, schließbar per Escape.
- **Consent** (Klaro): in Markenfarben, Buttons als Pills, nur mit GA-ID.
- **Footer** `.fwg-footer__panel`: grünes Panel mit Radius 28 im Container,
  drei Spalten (Kontakt, Seiten, Rechtliches), darunter die große Wortmarke
  `.fwg-footer__word` in 50 Prozent Weiß und die Micro-Zeile in 78 Prozent
  Weiß.

Icons: ausschließlich Phosphor (Sprite `assets/img/icons.svg`, 1,5 px
Strich), 18 bis 24 px, immer mit Text. Keine Emojis.

## 5. Layout und Raster

- Container 1280 px, seitlicher Rand `--fwg-page-x` (16 bis 48 px). Panels
  und Hero liegen im Container, nie randlos.
- Sektionsabstand `--fwg-section-y` (56 bis 112 px). Innerhalb von Panels
  `--fwg-space-7` bis `--fwg-space-8`.
- Raster: 12 Spalten gedacht, umgesetzt als Grid mit `minmax(0, Nfr)`:
  Intro 5/7, Porträt 4/7, FAQ 4/8, Formular 7/5. Mobil (< 900 px) alles
  einspaltig, Kacheln < 640 px gestapelt.
- Reihenfolge Startseite: Hero → Intro (Zahl + Definition) → Vorteile-Kacheln
  → Schritte (grün) → Modelle (grau) → Golfanlagen (Foto) → Projekt (Text +
  Zahlen-Kacheln) → FAQ → CTA (Foto) → Footer. Kein Abschnitt dunkel außer
  Schritte und Footer.
- Keine horizontale Scrollfläche, `min-height` des Hero über `100dvh`
  begrenzt (max 760 px).

## 6. Bildsprache

- Motive: Golfplätze. Inselgrün, Wasserhindernis, Brücke, Grün am Meer,
  Platz von oben. Menschen nur in Kacheln (Range, Putt, Driver) und im
  Porträt. Keine Business-Stockfotos, keine Sitzungsräume.
- Technik: `fwg_img()` liefert WebP-Srcsets aus `assets/img/<key>-<w>.webp`
  mit `sizes`. Hero 1920 px, Panels 1600 px, Kacheln 800 bis 1200 px.
  Herkunft je Schlüssel in `docs/bildnachweise.md`.
- Auf Fotos liegt immer der Scrim, Text nur im unteren Drittel oder in
  einer weißen Karte. Radius wie das umgebende Panel (28) oder die Kachel
  (20).
- App-Mockups (`app-home`, `app-tee`) nur in den Schritte-Panels, als Handy
  über abgedunkeltem Platzfoto, mit Schatten `--fwg-shadow`.
- Social-Bild `og.jpg` aus dem Hero-Motiv.

## 7. Motion (nach Emil Kowalski)

- Nur `transform` und `opacity`. Easing `--fwg-ease-out`
  (`cubic-bezier(0.23, 1, 0.32, 1)`) für Ein- und Ausblenden,
  `--fwg-ease-in-out` für Bewegung auf der Seite.
- Dauern: Buttons 160 ms, Menü und Popup 250 ms, Reveal beim Scrollen 500 ms
  (nur Opacity und kleines translateY, einmalig, `IntersectionObserver`).
- Kein Parallax, keine Endlosschleifen, kein Marquee. Bild-Zoom in
  Foto-Kacheln 1.03 bei Hover.
- `prefers-reduced-motion`: alle Transitions auf 1 ms, Reveal sofort.

## 8. Ton und Texte

- Du, direkt, kurz. Ein Gedanke pro Satz. Keine Superlative, keine Claims,
  die die Seite nicht belegen kann („Am häufigsten gewählt“ ist gestrichen).
- Preise wie auf der alten Seite (35 bis 255 € im Monat, 395 bis 2.995 € im
  Jahr), aber jeder Button „Voranmelden“, nie „Kaufen“.
- Das Projekt sagt offen: zweiter Anlauf, keine Gesellschaft, Julius privat.
  Zahlen aus dem ersten Anlauf werden als solche benannt (2.000+
  Voranmeldungen, 20 Anlagen in Bayern, über 50 Gespräche).
- Golfbegriffe ohne Erklärung: Greenfee, Handicap, Startzeit, DGV-Ausweis.
  „Mulligan“ nur mit Erklärung.
- Fehlermeldungen sagen, was zu tun ist („Bitte gib eine E-Mail-Adresse mit
  @ ein“), nie nur „Ungültig“.
