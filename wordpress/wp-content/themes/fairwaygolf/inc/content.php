<?php
/**
 * Inhaltsdaten für mehrere Seiten: Modelle, Meilensteine, FAQ, Zahlen.
 * Preise und Leistungen stammen von der alten Seite (Stand 2024) und gelten als
 * geplante Modelle; jede Aktion heißt „Voranmelden“.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Mitgliedschaftsmodelle. */
function fwg_modelle(): array {
	return array(
		array(
			'key'     => 'practice',
			'name'    => 'Practice',
			'monat'   => 35,
			'jahr'    => 395,
			'kurz'    => 'Üben ohne Ende',
			'punkte'  => array( 'Alle Kurzplätze spielen', 'Auf allen Partneranlagen trainieren', 'Greenfee-Angebote der Partner nutzen' ),
		),
		array(
			'key'     => 's',
			'name'    => 'S',
			'monat'   => 115,
			'jahr'    => 1295,
			'kurz'    => 'Der Einstieg',
			'punkte'  => array( 'Alle S-Plätze spielen (9-Loch und einige 18-Loch)', 'Auf allen Partneranlagen und Kurzplätzen trainieren', 'S-Turniere spielen, nur Startgebühr' ),
		),
		array(
			'key'     => 'm',
			'name'    => 'M',
			'monat'   => 165,
			'jahr'    => 1895,
			'kurz'    => 'Die Mitte',
			'punkte'  => array( 'Alle M-Plätze spielen (fast alle 18-Loch, hochwertige 9-Loch)', 'Alle S-Leistungen inklusive', 'M-Turniere spielen, nur Startgebühr' ),
		),
		array(
			'key'     => 'l',
			'name'    => 'L',
			'monat'   => 215,
			'jahr'    => 2495,
			'kurz'    => 'Fast alles',
			'punkte'  => array( 'Alle L-Plätze spielen (fast alle 18-Loch, auch gehobene Anlagen)', 'Alle M-Leistungen inklusive', 'L-Turniere spielen, nur Startgebühr' ),
		),
		array(
			'key'     => 'xl',
			'name'    => 'XL',
			'monat'   => 255,
			'jahr'    => 2995,
			'kurz'    => 'Alles, überall',
			'punkte'  => array( 'Alle Golfplätze spielen, auch hochpreisige Anlagen', 'Simulatoren nutzen', 'XL-Turniere spielen, nur Startgebühr' ),
		),
	);
}

/** Meilensteine. */
function fwg_meilensteine(): array {
	return array(
		array( 'wann' => 'Dez. 2020', 'was' => 'Erster Anlauf', 'detail' => 'Nach Marktanalyse und viel Zuspruch aus der Golfer-Community startet Fair-Way-Golf. Die damalige Gesellschaft besteht heute nicht mehr.' ),
		array( 'wann' => 'Aug. 2021', 'was' => 'Konzept geprüft', 'detail' => 'Austausch mit über 50 Golfanlagen und Branchenexperten. Das Modell hält.' ),
		array( 'wann' => 'Dez. 2022', 'was' => 'App gebaut', 'detail' => 'Mit Startup Creator und PC Caddie entsteht der erste lauffähige Stand: Startzeiten, Turniere, Bag-Tag.' ),
		array( 'wann' => 'Sept. 2023', 'was' => 'Erste Verträge', 'detail' => 'Erste Golfanlagen in Bayern unterschreiben, die Suche nach Seed-Investoren beginnt.' ),
		array( 'wann' => '2024', 'was' => 'Über 2.000 Voranmeldungen', 'detail' => '20 Golfanlagen in Bayern sind dabei. Dann fehlt das Kapital für den Start, das Projekt wird gestoppt.' ),
		array( 'wann' => 'Heute', 'was' => 'Zweiter Anlauf', 'detail' => 'Die Idee lebt. Mit genug Golfern, Plätzen und Unterstützern startet Fair-Way-Golf neu, diesmal deutschlandweit.' ),
	);
}

/** FAQ für Golfer (Startseite). */
function fwg_faq_golfer(): array {
	return array(
		array( 'q' => 'Ist Fair-Way-Golf schon wieder am Start?', 'a' => 'Noch nicht. Gerade sammeln wir Voranmeldungen, Partnerplätze und Unterstützer. Sobald genug zusammenkommt, starten wir den zweiten Anlauf und melden uns bei dir.' ),
		array( 'q' => 'Was bedeutet die Voranmeldung für mich?', 'a' => 'Nichts Verbindliches. Du sagst uns, welche Plätze du spielen willst und welches Modell dich interessiert. Wir informieren dich, wenn es losgeht, und du entscheidest dann.' ),
		array( 'q' => 'Wo werde ich Mitglied?', 'a' => 'In einem echten Partnerclub, nicht in einer Fernmitgliedschaft. Bist du schon in einem Partnerclub, bleibst du dort. Sonst wird der Partnerclub, der deinem Wohnort am nächsten liegt, dein Heimatclub. Er stellt den DGV-Ausweis und führt dein Handicap.' ),
		array( 'q' => 'Zahle ich Greenfee auf den Partnerplätzen?', 'a' => 'Nein. Innerhalb deiner Kategorie spielst du alle Partnerplätze ohne Greenfee. Bei Turnieren zahlst du nur die Startgebühr.' ),
		array( 'q' => 'Was kostet eine Mitgliedschaft?', 'a' => 'Die geplanten Modelle reichen von Practice für 35 € im Monat bis XL für 255 € im Monat. Alle Modelle sind Jahresmitgliedschaften, die Preise verstehen sich inklusive Umsatzsteuer.' ),
		array( 'q' => 'Wo kann ich spielen?', 'a' => 'Der erste Anlauf lief in Bayern mit 20 Anlagen. Der Neustart ist deutschlandweit geplant: Jede Region, in der sich genug Golfer und Plätze finden, kommt dazu. Deine Wunschplätze aus der Voranmeldung zeigen uns, wo wir zuerst anklopfen.' ),
	);
}

/** FAQ für Golfanlagen. */
function fwg_faq_anlagen(): array {
	return array(
		array( 'q' => 'Was unterscheidet Fair-Way-Golf von anderen Modellen?', 'a' => 'Kein Gebietsschutz, echte Clubmitgliedschaft statt Fernmitgliedschaft und ein Wirtschaftsmodell, das zu Ende gedacht ist. Golfer bleiben Mitglied in ihrem Partnerclub, und der Heimatclub bekommt seine Auszahlung auch dann, wenn ein Mitglied nicht spielt.' ),
		array( 'q' => 'Was kostet die Partnerschaft?', 'a' => 'Nichts. Keine Gebühr, keine versteckten Kosten. Fair-Way-Golf übernimmt das Marketing für neue Mitglieder und vermittelt sie direkt an deinen Club.' ),
		array( 'q' => 'Ist das eine Fernmitgliedschaft?', 'a' => 'Nein. Golfer werden tatsächliche Mitglieder im Partnerclub mit allen Rechten einer regulären Mitgliedschaft und spielen zusätzlich die anderen Partnerplätze.' ),
		array( 'q' => 'Wo werden Golfer Mitglied, die über Fair-Way-Golf kommen?', 'a' => 'Golfer, die schon in einem Partnerclub sind, bleiben dort. Golfer ohne Club werden Mitglied im Partnerclub, der ihrem Wohnort am nächsten liegt. So stärken wir die Plätze in der Region.' ),
		array( 'q' => 'Wie stellt ihr sicher, dass Gäste unsere Regeln respektieren?', 'a' => 'Jede Runde wird über die App gebucht und am Clubhaus per QR-Code bestätigt. Mitglieder bestätigen dabei die Nutzungsbedingungen deines Platzes. Regelverstöße können zur Beendigung der Mitgliedschaft führen.' ),
		array( 'q' => 'Was bedeutet Umsatzsteigerung von 5 bis 12 Prozent?', 'a' => 'Das war die Kalkulation aus dem ersten Anlauf, basierend auf Sichtbarkeit in der Region, neuen Mitgliedern und Gästen anderer Partnerplätze. Die genauen Zahlen für deinen Platz rechnen wir gemeinsam durch.' ),
	);
}
