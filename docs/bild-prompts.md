# Bild-Prompts für ChatGPT (Bilderzeugung)

Die Seite läuft mit dem vorhandenen Material. Diese Prompts sind für Motive,
die das vorhandene Material nicht hergibt oder die du ersetzen willst. Alle
Prompts sind auf Englisch, weil die Bildmodelle damit zuverlässiger arbeiten.

## Grundregeln, damit es nicht nach KI aussieht

1. **Immer den Stil-Block anhängen** (unten). Er verhindert Hochglanz, Plastikhaut und Werbe-Posen.
2. **Eine Szene, ein Motiv.** Keine Collagen, keine Texte im Bild, keine Logos.
3. **Bayerisches Licht**: Morgen oder später Nachmittag, leicht dunstig, Voralpen oder Münchner Umland als Hintergrund.
4. **Format mitgeben**: Hero 16:9 oder 4:5, Tags 4:3, Porträt 4:5. In ChatGPT bei „Bild erstellen" das Seitenverhältnis angeben.
5. **Nach der Erzeugung**: mindestens 1600 px breit exportieren, in `wordpress/_build/src/` ablegen (Dateiname ohne Umlaute), Eintrag in `wordpress/_build/images.php`, dann die Pipeline laufen lassen.
6. **Keine echten Gesichter von Bekannten nachbauen** und keine Markenlogos auf Kleidung.

## Stil-Block (an jeden Prompt anhängen)

```
Documentary photography style, shot on a 35mm full-frame camera, 50mm lens, natural light, soft morning haze, muted natural colours with deep greens, realistic skin and fabric, slight film grain, no HDR, no glossy retouching, no text, no logos, no watermark, candid and unposed.
```

## Prompts nach Einsatzort

### Startseite, Hero (Alternative zur Luftaufnahme), 16:9
```
Aerial drone photograph of a golf course in the Bavarian foothills at golden hour, long shadows across fairways, a small lake, forest edges, the Alps faintly visible on the horizon, no people, no buildings in the foreground.
```

### Tag „Du spielst Golf", 4:3
```
Two friends in their early thirties walking down a fairway with carry bags, laughing, seen from behind at a slight distance, Bavarian countryside, late afternoon light.
```

### Tag „Du führst eine Golfanlage", 4:3
```
A club manager in a quilted vest standing on the clubhouse terrace of a small Bavarian golf club in the morning, looking out over the first tee, coffee cup in hand, seen in profile, calm and thoughtful.
```

### Tag „Du willst das Projekt tragen", 4:3
```
Close-up of hands placing a golf ball on a tee at first light, dew on the grass, shallow depth of field, no faces.
```

### Golfanlagen-Seite, Kopf, 4:3
```
Greenkeeper on a ride-on mower cutting stripes into a fairway at sunrise, mist over the course, seen from a low angle.
```

### Unterstützen-Seite, Kopf, 4:5
```
A single golfer standing at the edge of a tee box at dawn, bag on the shoulder, looking down the empty fairway, back to the camera, quiet and determined.
```

### Über uns, Porträt (falls du ein neues willst), 4:5
Hier lieber ein echtes Foto: draußen, auf dem Platz oder der Clubterrasse,
Oberkörper, natürliches Licht, neutraler Hintergrund. Ein Freund mit
Handy und Porträtmodus reicht. KI-Porträts von dir selbst wirken schnell falsch.

### Social-Bild (Open Graph), 1200 x 630
```
Aerial photograph of a golf green with a white flag and two bunkers, seen straight from above, clean composition, lots of grass around the green for text overlay space.
```

## Was du nicht bestellen solltest

- Gruppen mit sechs lachenden Menschen in perfekter Ausrüstung.
- Nahaufnahmen von Gesichtern.
- Grafiken mit Text, Diagrammen oder App-Screens. Die App zeigen wir mit den echten Mockups.
- Platzpläne, Satellitenkarten, Loch-Schemata.
