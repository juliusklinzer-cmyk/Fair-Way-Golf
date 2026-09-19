<?php
/**
 * Startseite: Eine Mitgliedschaft, alle Golfplätze.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();
?>

<section class="fwg-hero" aria-labelledby="hero-title">
	<div class="fwg-hero__panel">
		<div class="fwg-hero__media">
			<?php echo fwg_img( 'hero-platz', 'Golfplatz im Herbstlicht: Grün mit roter Fahne, Bunker, Steinbrücke über einen Bach und bunte Bäume', '(min-width: 1280px) 1280px, 100vw', array( 'priority' => true ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</div>
		<div class="fwg-hero__content">
			<div>
				<h1 id="hero-title" class="fwg-hero__title">Eine Mitgliedschaft. Alle Golfplätze.</h1>
				<p class="fwg-hero__text">Spiele, trainiere und vernetze dich auf allen Partneranlagen, ohne Greenfee und ohne Grenzen. Mit echtem Heimatclub, DGV-Ausweis und allem in einer App.</p>
				<div class="fwg-hero__actions">
					<?php echo fwg_button( 'Jetzt voranmelden', home_url( '/voranmeldung/#formular' ), 'lime' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<?php echo fwg_button( 'So funktioniert es', home_url( '/#so-gehts' ), 'ghost', '' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</div>
			</div>
			<div class="fwg-hero__card">
				<span class="fwg-hero__card-num">2.000+</span>
				<strong>Golfer haben sich schon vorangemeldet</strong>
				<span>20 Anlagen in Bayern waren beim ersten Anlauf dabei. Jetzt geht es deutschlandweit weiter.</span>
			</div>
		</div>
	</div>
</section>

<section class="fwg-section" aria-labelledby="intro-title">
	<div class="fwg-container fwg-intro">
		<div>
			<p class="fwg-intro__label">Die Idee seit</p>
			<p class="fwg-intro__number">2020</p>
			<h2 id="intro-title" class="fwg-intro__claim">Fair-Way-Golf ist eine Golf-Mitgliedschaft für viele Plätze statt für einen.</h2>
			<p class="fwg-intro__text">Du wirst Mitglied in einem Partnerclub in deiner Nähe und spielst zusätzlich alle anderen Partnerplätze deiner Kategorie. Wann du willst, wo du willst und so oft du willst. Ohne Greenfee, mit Startzeiten und Turnieren in einer App.</p>
			<div class="fwg-actions">
				<a href="<?php echo esc_url( home_url( '/#so-gehts' ) ); ?>" class="fwg-link">So funktioniert es <?php echo fwg_icon( 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
			</div>
		</div>
		<div class="fwg-intro__tiles">
			<a href="<?php echo esc_url( home_url( '/#modelle' ) ); ?>" class="fwg-tile fwg-tile--photo fwg-tile--tall">
				<?php echo fwg_img( 'fairway-hoch', 'Fairway zwischen Bäumen unter blauem Himmel', '(min-width: 1024px) 34vw, 100vw' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<div class="fwg-tile__body">
					<h3>Alle Plätze deiner Kategorie</h3>
					<p>Von Practice bis XL: Du wählst, wie viele Plätze du spielen willst.</p>
				</div>
			</a>
			<div class="fwg-tile fwg-tile--lime">
				<div class="fwg-tile__body">
					<p class="fwg-tile__num">0 €</p>
					<h3>Greenfee auf Partnerplätzen</h3>
					<p>Runden sind in deiner Mitgliedschaft drin. Bei Turnieren zahlst du nur die Startgebühr.</p>
				</div>
			</div>
			<a href="<?php echo esc_url( home_url( '/#so-gehts' ) ); ?>" class="fwg-tile fwg-tile--photo">
				<?php echo fwg_img( 'driver', 'Driver hinter einem aufgeteeten Ball, Nahaufnahme', '(min-width: 1024px) 24vw, 100vw' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<div class="fwg-tile__body">
					<h3>Startzeit in der App</h3>
					<p>Platz suchen, Zeit buchen, Freunde einladen, am Clubhaus einchecken.</p>
				</div>
			</a>
		</div>
	</div>
</section>

<section class="fwg-section fwg-section--flush" id="vorteile" aria-labelledby="vorteile-title">
	<div class="fwg-container">
		<div class="fwg-panel fwg-panel--alt">
			<div class="fwg-section__head">
				<h2 id="vorteile-title" class="fwg-h2">Was du mit Fair&#8209;Way&#8209;Golf bekommst</h2>
				<?php echo fwg_button( 'Modelle ansehen', home_url( '/#modelle' ), 'secondary' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</div>
			<div class="fwg-grid fwg-grid--4 fwg-vorteile">
				<a href="<?php echo esc_url( home_url( '/#modelle' ) ); ?>" class="fwg-tile fwg-tile--photo">
					<?php echo fwg_img( 'range-szene', 'Golfer schlägt auf der Driving Range ab, Bälle liegen im Gras', '(min-width: 1024px) 24vw, 100vw' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<div class="fwg-tile__body"><h3>Trainiere</h3><p>Auf allen Partneranlagen und Kurzplätzen, so oft du willst. Jeder Ball macht dich besser.</p></div>
				</a>
				<a href="<?php echo esc_url( home_url( '/#modelle' ) ); ?>" class="fwg-tile fwg-tile--photo">
					<?php echo fwg_img( 'inselgruen', 'Grün an einem See im Abendlicht, Fahne am Ufer', '(min-width: 1024px) 24vw, 100vw' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<div class="fwg-tile__body"><h3>Spiele</h3><p>Alle Partnerplätze deiner Kategorie, ohne Greenfee. Wann, wo und so oft du willst.</p></div>
				</a>
				<a href="<?php echo esc_url( home_url( '/#modelle' ) ); ?>" class="fwg-tile fwg-tile--photo">
					<?php echo fwg_img( 'putt', 'Golfer beim Putt auf dem Grün neben der Fahne', '(min-width: 1024px) 24vw, 100vw' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<div class="fwg-tile__body"><h3>Turniere</h3><p>Melde dich zu Turnieren auf allen Partnerplätzen an und zahle nur die Startgebühr.</p></div>
				</a>
				<a href="<?php echo esc_url( home_url( '/#so-gehts' ) ); ?>" class="fwg-tile fwg-tile--green">
					<div class="fwg-tile__body">
						<?php echo fwg_icon( 'lightning', 'fwg-tile__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<h3>Alles in einer App</h3>
						<p>Startzeiten, Turniere, Golfplatzsuche und dein Mobile-Bag-Tag. Kein Papier, kein Anruf im Sekretariat.</p>
					</div>
				</a>
			</div>
		</div>
	</div>
</section>

<section class="fwg-section" id="so-gehts" aria-labelledby="so-gehts-title">
	<div class="fwg-container">
		<div class="fwg-panel fwg-panel--green fwg-steps-panel">
			<div>
				<h2 id="so-gehts-title" class="fwg-h2">So funktioniert Fair&#8209;Way&#8209;Golf</h2>
				<p class="fwg-lead">Einfachheit und Flexibilität stehen im Vordergrund. In vier Schritten spielst du auf einer Vielzahl von Golfplätzen.</p>
				<ol class="fwg-steps">
					<li><h3>Modell wählen</h3><p>Practice, S, M, L oder XL. Die Kategorie legt fest, welche Partnerplätze du spielst.</p></li>
					<li><h3>Heimatclub bekommen</h3><p>Du bleibst in deinem Partnerclub oder wirst Mitglied im nächstgelegenen. Er stellt den DGV-Ausweis und führt dein Handicap.</p></li>
					<li><h3>Startzeit buchen</h3><p>In der App siehst du alle Partnerplätze, freie Startzeiten und Turniere. Freunde lädst du direkt mit ein.</p></li>
					<li><h3>Einchecken und abschlagen</h3><p>Am Clubhaus scannst du den QR-Code, dein Mobile-Bag-Tag erscheint im Handy. Kein Greenfee, kein Papier.</p></li>
				</ol>
				<div class="fwg-actions">
					<?php echo fwg_button( 'Jetzt voranmelden', home_url( '/voranmeldung/#formular' ), 'lime' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</div>
			</div>
			<div class="fwg-appshot">
				<?php echo fwg_img( 'oben-gruen', '', '(min-width: 1024px) 50vw, 100vw', array( 'class' => 'fwg-appshot__bg' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<span class="fwg-appshot__badge"><?php echo fwg_icon( 'check-circle' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>Die Fair-Way-Golf-App</span>
				<?php echo fwg_img( 'app-home', 'Startbildschirm der Fair-Way-Golf-App mit Heimatclub, Favoriten und Plätzen in der Nähe', '(min-width: 1024px) 22vw, 60vw', array( 'class' => 'fwg-appshot__phone' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</div>
		</div>
	</div>
</section>

<section class="fwg-section fwg-section--flush" id="modelle" aria-labelledby="modelle-title">
	<div class="fwg-container">
		<div class="fwg-section__head">
			<div>
				<h2 id="modelle-title" class="fwg-h2">Fünf Modelle, ein Prinzip</h2>
				<p class="fwg-lead">Du bestimmst, welches Paket zu deinem Spiel passt. Jedes Modell enthält die Mitgliedschaft im Heimatclub und den DGV-Ausweis. Alle Modelle sind Jahresmitgliedschaften, die Preise verstehen sich inklusive Umsatzsteuer und stammen aus der Planung.</p>
			</div>
			<div class="fwg-toggle" role="group" aria-label="Preise anzeigen">
				<button type="button" class="fwg-toggle__btn" data-fwg-preis="monat" aria-pressed="true">monatlich</button>
				<button type="button" class="fwg-toggle__btn" data-fwg-preis="jahr" aria-pressed="false">jährlich</button>
			</div>
		</div>
		<div class="fwg-modelle">
			<?php foreach ( fwg_modelle() as $mod ) : ?>
				<article class="fwg-modell">
					<h3 class="fwg-modell__name"><?php echo esc_html( $mod['name'] ); ?></h3>
					<p class="fwg-modell__kurz"><?php echo esc_html( $mod['kurz'] ); ?></p>
					<p class="fwg-modell__preis">
						<span data-fwg-preis-monat><?php echo esc_html( number_format_i18n( $mod['monat'] ) ); ?>&nbsp;€<small>im Monat</small></span>
						<span data-fwg-preis-jahr hidden><?php echo esc_html( number_format_i18n( $mod['jahr'] ) ); ?>&nbsp;€<small>im Jahr</small></span>
					</p>
					<ul class="fwg-modell__liste">
						<?php foreach ( $mod['punkte'] as $p ) : ?>
							<li><?php echo fwg_icon( 'check', 'fwg-icon--check' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( $p ); ?></span></li>
						<?php endforeach; ?>
					</ul>
					<a href="<?php echo esc_url( home_url( '/voranmeldung/?modell=' . $mod['key'] . '#formular' ) ); ?>" class="fwg-btn fwg-btn--secondary fwg-btn--sm fwg-btn--block"><span>Voranmelden</span></a>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="fwg-section" id="anlagen" aria-labelledby="anlagen-title">
	<div class="fwg-container">
		<div class="fwg-panel fwg-panel--photo">
			<?php echo fwg_img( 'wasser', '', '(min-width: 1280px) 1280px, 100vw', array( 'class' => 'fwg-panel__bg' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<div class="fwg-panel__content">
				<h2 id="anlagen-title" class="fwg-h2">Für Golfanlagen: neue Golfer auf deinem Platz</h2>
				<p class="fwg-lead">Fair-Way-Golf bringt dir Interessenten, die sonst nirgends Mitglied werden, und vermittelt sie direkt in deinen Club. Kostenfrei, ohne Gebietsschutz, mit voller Auszahlung an den Heimatclub.</p>
				<div class="fwg-actions">
					<?php echo fwg_button( 'Für Golfanlagen', home_url( '/golfplaetze/' ), 'white' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</div>
			</div>
		</div>
	</div>
</section>

<section class="fwg-section fwg-section--flush" id="projekt" aria-labelledby="projekt-title">
	<div class="fwg-container fwg-projekt">
		<div class="fwg-projekt__text">
			<h2 id="projekt-title" class="fwg-h2">Wo das Projekt steht</h2>
			<p class="fwg-lead">Fair-Way-Golf gab es schon einmal. Über 2.000 Golfer und 20 Anlagen in Bayern waren dabei, die App war gebaut, die Verträge unterschrieben. Dann fehlte das Kapital für den Start. Im Golf heißt der zweite Versuch Mulligan. Den nehmen wir jetzt, diesmal deutschlandweit. Je mehr Golfer sich eintragen, desto leichter überzeugen wir die Plätze.</p>
			<div class="fwg-actions">
				<?php echo fwg_button( 'Unterstützen', home_url( '/unterstuetzen/' ), 'primary' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<a href="<?php echo esc_url( home_url( '/ueber-uns/' ) ); ?>" class="fwg-link">Die ganze Geschichte <?php echo fwg_icon( 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
			</div>
		</div>
		<div class="fwg-grid fwg-grid--2 fwg-projekt__tiles">
			<div class="fwg-tile fwg-tile--green"><div class="fwg-tile__body"><p class="fwg-tile__num">2.000+</p><h3>Voranmeldungen</h3><p>Golfer, die beim ersten Anlauf dabei sein wollten.</p></div></div>
			<div class="fwg-tile fwg-tile--lime"><div class="fwg-tile__body"><p class="fwg-tile__num">20</p><h3>Golfanlagen</h3><p>Plätze in Bayern, die unterschrieben hatten.</p></div></div>
			<div class="fwg-tile fwg-tile--mint"><div class="fwg-tile__body"><p class="fwg-tile__num">50+</p><h3>Anlagen im Austausch</h3><p>Mit ihnen wurde das Konzept geprüft.</p></div></div>
			<a href="<?php echo esc_url( home_url( '/unterstuetzen/' ) ); ?>" class="fwg-tile fwg-tile--photo">
				<?php echo fwg_img( 'aussicht', 'Blick über Bunker und Grün auf eine weite Golflandschaft', '(min-width: 1024px) 24vw, 100vw' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<div class="fwg-tile__body"><h3>Gründungsmitglied werden</h3><p>Von Anfang an dabei sein und mitreden.</p></div>
			</a>
		</div>
	</div>
</section>

<section class="fwg-section" id="fragen" aria-labelledby="fragen-title">
	<div class="fwg-container fwg-faq-layout">
		<div>
			<h2 id="fragen-title" class="fwg-h2">Eure Fragen, unsere Antworten</h2>
			<p class="fwg-lead">Noch etwas unklar? Schreib uns an <a href="mailto:<?php echo esc_attr( FWG_CONTACT_EMAIL ); ?>"><?php echo esc_html( FWG_CONTACT_EMAIL ); ?></a>.</p>
		</div>
		<div class="fwg-faq">
			<?php foreach ( fwg_faq_golfer() as $i => $f ) : ?>
				<details class="fwg-faq__item" name="faq-golfer"<?php echo 0 === $i ? ' open' : ''; ?>>
					<summary><span><?php echo esc_html( $f['q'] ); ?></span><?php echo fwg_icon( 'plus', 'fwg-faq__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></summary>
					<div class="fwg-faq__body"><p><?php echo esc_html( $f['a'] ); ?></p></div>
				</details>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="fwg-section fwg-section--flush" aria-labelledby="cta-title">
	<div class="fwg-container">
		<div class="fwg-panel fwg-panel--photo fwg-panel--cta">
			<?php echo fwg_img( 'meer', '', '(min-width: 1280px) 1280px, 100vw', array( 'class' => 'fwg-panel__bg' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<div class="fwg-panel__content">
				<h2 id="cta-title" class="fwg-h2">Bereit für alle Plätze?</h2>
				<p class="fwg-lead">Voranmelden dauert keine fünf Minuten und verpflichtet dich zu nichts. Sag uns deine Wunschplätze, wir kümmern uns um den Rest.</p>
				<div class="fwg-actions">
					<?php echo fwg_button( 'Jetzt voranmelden', home_url( '/voranmeldung/#formular' ), 'lime' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<a href="<?php echo esc_url( home_url( '/golfplaetze/' ) ); ?>" class="fwg-link fwg-link--light">Du führst eine Golfanlage? <?php echo fwg_icon( 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
				</div>
			</div>
		</div>
	</div>
</section>

<?php
get_footer();
