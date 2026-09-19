<?php
/**
 * Über uns: Vision, Geschichte, Julius.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();
$fwg_meeting = defined( 'FWG_MEETING_URL' ) && FWG_MEETING_URL;
?>

<section class="fwg-hero fwg-hero--sub" aria-labelledby="hero-title">
	<div class="fwg-hero__panel">
		<div class="fwg-hero__media">
			<?php echo fwg_img( 'hero-gruen', 'Golfer geht auf einem sonnigen Grün zur Fahne, im Hintergrund ein Cart und herbstliche Bäume', '(min-width: 1280px) 1280px, 100vw', array( 'priority' => true ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</div>
		<div class="fwg-hero__content">
			<div>
				<h1 id="hero-title" class="fwg-hero__title">Von Golfern für Golfer. Und noch einmal von vorn.</h1>
				<p class="fwg-hero__text">Golf ist mehr als ein Spiel: Bewegung, Natur, Gemeinschaft. Wir wollen den Sport für alle zugänglich und modern machen, mit einer Mitgliedschaft für alle Golfplätze.</p>
			</div>
		</div>
	</div>
</section>

<section class="fwg-section" aria-labelledby="vision-title">
	<div class="fwg-container fwg-intro">
		<div>
			<p class="fwg-intro__label">Unsere Vision</p>
			<h2 id="vision-title" class="fwg-intro__claim">Golf für die nächste Generation: eine Mitgliedschaft, alle Golfplätze, ganz Deutschland.</h2>
			<p class="fwg-intro__text">Golfanlagen tun sich schwer, jüngere Golfer zu erreichen. Gleichzeitig wollen viele Menschen spielen, ohne sich an einen einzigen Platz zu binden. Fair-Way-Golf bringt beide zusammen: Du bist Mitglied in einem echten Partnerclub und spielst trotzdem überall. Die Plätze gewinnen neue Mitglieder, ohne sich gegenseitig etwas wegzunehmen.</p>
		</div>
		<div class="fwg-intro__tiles">
			<div class="fwg-tile fwg-tile--photo fwg-tile--tall"><?php echo fwg_img( 'schwung', 'Golfer beim vollen Schwung auf der Range', '(min-width: 1024px) 34vw, 100vw' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><div class="fwg-tile__body"><h3>Verbinden, spielen, wachsen</h3><p>Eine Community aus Golfern, die ihre Liebe zum Spiel teilen.</p></div></div>
			<div class="fwg-tile fwg-tile--green"><div class="fwg-tile__body"><p class="fwg-tile__num">50+</p><h3>Anlagen im Austausch</h3><p>Mit ihnen haben wir das Konzept auf Herz und Nieren geprüft.</p></div></div>
			<div class="fwg-tile fwg-tile--lime"><div class="fwg-tile__body"><p class="fwg-tile__num">2.000+</p><h3>Voranmeldungen</h3><p>Golfer, die dabei sein wollten, bevor es losging.</p></div></div>
		</div>
	</div>
</section>

<section class="fwg-section fwg-section--flush" aria-labelledby="meilensteine-title">
	<div class="fwg-container">
		<div class="fwg-panel fwg-panel--alt">
			<div class="fwg-section__head">
				<h2 id="meilensteine-title" class="fwg-h2">Unsere Geschichte</h2>
				<p class="fwg-lead">Von der ersten Idee über 2.000 Voranmeldungen bis zum zweiten Anlauf.</p>
			</div>
			<ol class="fwg-timeline">
				<?php foreach ( fwg_meilensteine() as $m ) : ?>
					<li>
						<span class="fwg-timeline__wann"><?php echo esc_html( $m['wann'] ); ?></span>
						<h3><?php echo esc_html( $m['was'] ); ?></h3>
						<p><?php echo esc_html( $m['detail'] ); ?></p>
					</li>
				<?php endforeach; ?>
			</ol>
		</div>
	</div>
</section>

<section class="fwg-section" aria-labelledby="julius-title">
	<div class="fwg-container fwg-portrait-layout">
		<div class="fwg-portrait">
			<?php echo fwg_img( 'julius', 'Julius Klinzer auf dem Golfplatz, der Kopf hinter Fair-Way-Golf', '(min-width: 1024px) 30vw, 92vw', array( 'class' => 'fwg-portrait__img' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</div>
		<div>
			<h2 id="julius-title" class="fwg-h2">Wer dahintersteht</h2>
			<p class="fwg-lead">Ich bin Julius Klinzer, der Kopf hinter Fair-Way-Golf und Golfer aus München.</p>
			<p>Ende 2020 haben wir Fair-Way-Golf gestartet, nach Monaten auf Golfplätzen und in Gesprächen mit Clubmanagern und Spielern. Mit Startup Creator und PC Caddie haben wir die App gebaut, 20 Anlagen in Bayern waren dabei, über 2.000 Golfer haben sich vorangemeldet. Dann fehlte das Kapital für den Start, und die damalige Gesellschaft gibt es nicht mehr.</p>
			<p>Die Nachfrage hat nie aufgehört. Deshalb betreibe ich diese Seite privat weiter und sammle, was den zweiten Anlauf möglich macht: Golfer, Plätze und Unterstützer. Sobald genug zusammenkommt, gründen wir eine neue Gesellschaft und starten, diesmal deutschlandweit. Alles, was hier eingeht, landet bei mir, und ich antworte selbst.</p>
			<div class="fwg-actions">
				<a href="mailto:<?php echo esc_attr( FWG_CONTACT_EMAIL ); ?>" class="fwg-btn fwg-btn--primary"><span>E-Mail an Julius</span><?php echo fwg_icon( 'envelope-simple', 'fwg-btn__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
				<?php if ( $fwg_meeting ) : ?>
					<a href="<?php echo esc_url( FWG_MEETING_URL ); ?>" class="fwg-link" rel="noopener" target="_blank">Termin mit Julius buchen <?php echo fwg_icon( 'arrow-up-right' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>

<section class="fwg-section fwg-section--flush" aria-labelledby="cta-title">
	<div class="fwg-container">
		<div class="fwg-panel fwg-panel--photo fwg-panel--cta">
			<?php echo fwg_img( 'inselgruen', '', '(min-width: 1280px) 1280px, 100vw', array( 'class' => 'fwg-panel__bg' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<div class="fwg-panel__content">
				<h2 id="cta-title" class="fwg-h2">Du willst dabei sein?</h2>
				<p class="fwg-lead">Als Golfer, als Anlage oder als Unterstützer. Jeder Eintrag bringt den zweiten Anlauf näher.</p>
				<div class="fwg-actions">
					<?php echo fwg_button( 'Jetzt voranmelden', home_url( '/voranmeldung/#formular' ), 'lime' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<?php echo fwg_button( 'Unterstützen', home_url( '/unterstuetzen/' ), 'ghost', '' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</div>
			</div>
		</div>
	</div>
</section>

<?php
get_footer();
