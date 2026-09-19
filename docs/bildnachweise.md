# Bildnachweise und Provenienz der ausgelieferten Raster

Jedes Bild in `wordpress/wp-content/themes/fairwaygolf/assets/img/` stammt aus einer
Quelle in `wordpress/_build/src/` und wird von `wordpress/_build/images.php` als
WebP in mehreren Breiten erzeugt (Verkleinerung, bei `julius` ein Beschnitt, keine
Retusche). Stand: 19.09.2026, nach dem Neubau im Foto-Panel-Stil. Nur noch
Schlüssel, die eine Seite tatsächlich nutzt; alles andere wurde aus `images.php` und
`assets/img/` entfernt (die Quelldateien liegen weiter in `_build/src/`).

Die Bildserie aus „Bilder für Projekte/Firmengolf Bilder“ ist laut Dateinamen und
einheitlichem Format 1920×1080 KI-generiert; Julius bestätigt das vor dem Go-Live.
Zeilen mit „Einwilligung prüfen“ zeigen erkennbare Personen.

| Schlüssel | Quelle (Datei) | Herkunft | Rechte | Verwendung |
|---|---|---|---|---|
| hero-platz | Firmengolf Bilder `Golfloch mit Brücke über wasser.jpg` | KI-generiert | frei | Startseite Hero, Social-Bild `og.jpg` |
| hero-gruen | Firmengolf Bilder `Erfahrener Golfer läuft über grün.jpg` | KI-generiert | frei | Über uns Kopf |
| inselgruen | Firmengolf Bilder `Golfplatz mit Inselgrün.jpg` | KI-generiert | frei | Voranmeldung Kopf, Startseite, Über uns |
| oben-gruen | Firmengolf Bilder `Golfplatz von Oben Grün.jpg` | KI-generiert | frei | Startseite „So funktioniert es“ (Hintergrund App), Golfplätze |
| wasser | Firmengolf Bilder `Golfplatz mit Wasserhinderniss.jpg` | KI-generiert | frei | Golfplätze Kopf, Startseite Golfanlagen-Panel |
| meer | Firmengolf Bilder `Berühmter Golfplatz Grün am Meer.jpg` | KI-generiert | frei | Startseite CTA-Panel, Unterstützen |
| aussicht | Firmengolf Bilder `Golfplatz mit aussicht.jpg` | KI-generiert | frei | Unterstützen Kopf, Startseite Projekt-Tile, 404 |
| fairway-hoch | Firmengolf Bilder `Golfplatz Abschlag sttes Grün.JPEG` | KI-generiert | frei | Hochformat-Tiles (Startseite Vorteile, Unterstützen) |
| range-szene | Firmengolf Bilder `Aktive Szene Golf mit Rasenabschlägen und nicht perfekt echtes Golf.jpg` | KI-generiert | frei | Startseite Vorteile „Trainiere“, Golfplätze |
| driver | Firmengolf Bilder `Driver abschlag closeup.jpg` | KI-generiert | frei | Startseite Vorteile „Spiele“ |
| putt | Firmengolf Bilder `Erfahrener Golfer puttet.jpg` | KI-generiert | frei | Startseite Vorteile „Turniere“, Unterstützen |
| fahne | Firmengolf Bilder `Golffahne rechts im Bild.jpg` | KI-generiert | frei | Golfplätze Schritte-Panel (Hintergrund App) |
| schwung | Firmengolf Bilder `Golfer übt vollen Schwung.JPEG` | eigenes Foto | Einwilligung prüfen | Über uns |
| julius | Firmengolf Bilder `7C0A916C-EC3D-4EC6-96B4-4A939771F27F.JPG` | eigenes Foto von Julius auf dem Platz, aufrecht gedreht, Beschnitt 4:5 | eigen | Über uns |
| app-home, app-tee | alte Website `Mokup-*.svg` (eingebettetes Raster extrahiert) | eigene App-Mockups aus dem ersten Anlauf (Beispieldaten 2021) | eigen | Startseite und Golfplätze Schritte |
| og.jpg | aus `hero-platz` | Beschnitt 1200×630 | wie hero-platz | Social-Vorschau |
| apple-touch-icon.png, favicon.svg, logo-mark.svg | alte Website `cropped-2.png`, `Fwg-basis.svg` | eigenes Logo | eigen | Icons, Header |
| icons.svg | Phosphor Icons (MIT) | Sprite mit den genutzten Symbolen | MIT-Lizenz | alle Seiten |

Nicht mehr genutzt (Quellen bleiben in `_build/src/`): die Stockfotos der alten Seite
(`hero-see`, `hero-kueste`, `cart`), das Teamfoto, die MidJourney-Grafiken
(`community`, `generation`), `simulator`, `leaderboard`, `ubahn`, `range`, `gruen`,
`baelle`, `loch`, `clubhaus`, `greenkeeper`, `handshake`, `fairway`, `cart-happy`,
`abschlag`, `app-bagtag`, `app-platz`. Damit entfällt auch die offene Rechtefrage zu
den Unsplash-Bildern der alten Seite.
