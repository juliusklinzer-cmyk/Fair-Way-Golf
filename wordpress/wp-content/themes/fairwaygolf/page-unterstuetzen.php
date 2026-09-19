<?php
/**
 * Unterstützen: Gründungsmitglieder, Investoren, Partner.
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
			<?php echo fwg_img( 'aussicht', 'Blick über Bunker und Grün auf eine weite Golflandschaft unter Wolken', '(min-width: 1280px) 1280px, 100vw', array( 'priority' => true ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</div>
		<div class="fwg-hero__content">
			<div>
				<h1 id="hero-title" class="fwg-hero__title">Hilf uns, den zweiten Abschlag zu machen.</h1>
				<p class="fwg-hero__text">Fair-Way-Golf hatte eine lauffähige App, unterschriebene Verträge und über 2.000 Voranmeldungen. Was fehlte, war das Kapital für den Start. Für den zweiten Anlauf suchen wir Menschen, die das Projekt mit uns tragen.</p>
				<div class="fwg-hero__actions">
					<?php echo fwg_button( 'Interesse zeigen', '#formular', 'lime' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</div>
			</div>
		</div>
	</div>
</section>

<section class="fwg-section" aria-labelledby="steht-title">
	<div class="fwg-container fwg-grid fwg-grid--2 fwg-grid--wide">
		<div class="fwg-panel fwg-panel--alt">
			<h2 id="steht-title" class="fwg-h3">Was schon steht</h2>
			<ul class="fwg-checklist">
				<li><strong>App und Backend</strong> Startzeiten, Turniere, Mobile-Bag-Tag, Anbindung an PC Caddie. Gebaut mit Startup Creator.</li>
				<li><strong>Konzept</strong> Mit über 50 Anlagen geprüft, Wirtschaftsmodell durchgerechnet, Verträge erarbeitet.</li>
				<li><strong>Nachfrage</strong> Über 2.000 Voranmeldungen und 20 Golfanlagen in Bayern beim ersten Anlauf.</li>
				<li><strong>Marke</strong> Name, Domain, Auftritt und eine Community, die bis heute nachfragt.</li>
			</ul>
		</div>
		<div class="fwg-panel fwg-panel--green">
			<h2 class="fwg-h3">Was noch fehlt</h2>
			<ul class="fwg-checklist fwg-checklist--light">
				<li><strong>Startkapital</strong> Für Betrieb, Marketing und die ersten Auszahlungen an Partnerclubs, bevor die Beiträge fließen.</li>
				<li><strong>Gründungsmitglieder</strong> Ein Kern von Golfern, der von Anfang an dabei ist und das Modell mitprägt.</li>
				<li><strong>Partner</strong> Technik, Marketing, Netzwerk in der Golfbranche. Menschen, die Türen öffnen.</li>
				<li><strong>Gesellschaft</strong> Bei Wiederaufnahme gründen wir eine GmbH als Träger. Bis dahin sammeln wir nur Interesse.</li>
			</ul>
		</div>
	</div>
</section>

<section class="fwg-section fwg-section--flush" aria-labelledby="rollen-title">
	<div class="fwg-container">
		<div class="fwg-section__head">
			<h2 id="rollen-title" class="fwg-h2">Drei Rollen, die jetzt zählen</h2>
			<p class="fwg-lead">Gerade sammeln wir Interesse, keine Zusagen. Was jede Rolle konkret bedeutet, legen wir mit den ersten Interessenten fest.</p>
		</div>
		<div class="fwg-grid fwg-grid--3">
			<div class="fwg-tile fwg-tile--photo"><?php echo fwg_img( 'putt', 'Golfer beim Putt neben der Fahne', '(min-width: 1024px) 32vw, 100vw' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><div class="fwg-tile__body"><h3>Gründungsmitglied</h3><p>Du willst von Anfang an dabei sein und mitreden, wie Fair-Way-Golf zurückkommt.</p></div></div>
			<div class="fwg-tile fwg-tile--photo"><?php echo fwg_img( 'meer', 'Golfplatz an der Steilküste von oben', '(min-width: 1024px) 32vw, 100vw' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><div class="fwg-tile__body"><h3>Investor</h3><p>Du siehst das Potenzial im Golfmarkt und willst den Start finanzieren. Wir zeigen dir Zahlen, Verträge und den Plan.</p></div></div>
			<div class="fwg-tile fwg-tile--photo"><?php echo fwg_img( 'fairway-hoch', 'Fairway zwischen Bäumen unter blauem Himmel', '(min-width: 1024px) 32vw, 100vw' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><div class="fwg-tile__body"><h3>Partner</h3><p>Du bringst Technik, Marketing oder Kontakte in die Golfbranche mit. Vielleicht führst du selbst eine Anlage.</p></div></div>
		</div>
	</div>
</section>

<section class="fwg-section" id="formular" aria-labelledby="formular-title">
	<div class="fwg-container">
		<div class="fwg-panel fwg-panel--alt fwg-formlayout">
			<aside class="fwg-formlayout__aside">
				<h2 id="formular-title" class="fwg-h3">Sag uns, wie du dabei sein willst</h2>
				<p class="fwg-muted">Das ist keine Zusage und kein Vertrag. Julius meldet sich persönlich, und ihr sprecht darüber, was passt.</p>
				<?php if ( $fwg_meeting ) : ?>
					<p><a href="<?php echo esc_url( FWG_MEETING_URL ); ?>" class="fwg-link" rel="noopener" target="_blank">Termin mit Julius buchen <?php echo fwg_icon( 'arrow-up-right' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a></p>
				<?php endif; ?>
			</aside>
			<div class="fwg-formlayout__main">
				<?php echo fwg_form_notice( 'unterstuetzen' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<form class="fwg-form" data-fwg-form="unterstuetzen" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" novalidate>
					<?php get_template_part( 'template-parts/form-hidden', null, array( 'type' => 'unterstuetzen' ) ); ?>
					<div class="fwg-form__row">
						<div class="fwg-field">
							<label for="fwg-name-u">Name <span class="fwg-field__req">(Pflicht)</span></label>
							<input id="fwg-name-u" name="name" type="text" autocomplete="name" required maxlength="120">
							<p class="fwg-field__error" data-fwg-error="name" hidden></p>
						</div>
						<div class="fwg-field">
							<label for="fwg-email-u">E-Mail <span class="fwg-field__req">(Pflicht)</span></label>
							<input id="fwg-email-u" name="email" type="email" autocomplete="email" inputmode="email" required maxlength="120">
							<p class="fwg-field__error" data-fwg-error="email" hidden></p>
						</div>
					</div>
					<fieldset class="fwg-field fwg-field--radios">
						<legend>Wie willst du unterstützen? <span class="fwg-field__req">(Pflicht)</span></legend>
						<div class="fwg-radios fwg-radios--stack">
							<?php foreach ( fwg_rollen() as $k => $label ) : ?>
								<label class="fwg-radio"><input type="radio" name="rolle" value="<?php echo esc_attr( $k ); ?>" required><span><?php echo esc_html( $label ); ?></span></label>
							<?php endforeach; ?>
						</div>
						<p class="fwg-field__error" data-fwg-error="rolle" hidden></p>
					</fieldset>
					<div class="fwg-field">
						<label for="fwg-nachricht-u">Willst du uns noch etwas sagen? <span class="fwg-field__opt">(freiwillig)</span></label>
						<textarea id="fwg-nachricht-u" name="nachricht" rows="4" maxlength="2000"></textarea>
					</div>
					<?php get_template_part( 'template-parts/form-consent', null, array( 'id' => 'fwg-datenschutz-u' ) ); ?>
					<p class="fwg-form__msg" data-fwg-msg role="status" aria-live="polite" hidden></p>
					<button type="submit" class="fwg-btn fwg-btn--primary fwg-btn--lg" data-fwg-submit><span>Interesse zeigen</span><?php echo fwg_icon( 'arrow-right', 'fwg-btn__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
				</form>
				<div class="fwg-form__done fwg-panel fwg-panel--mint" data-fwg-done hidden tabindex="-1">
					<?php echo fwg_icon( 'check-circle', 'fwg-done__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<h2 class="fwg-h3">Danke. Das bedeutet uns viel.</h2>
					<p data-fwg-done-text></p>
				</div>
			</div>
		</div>
	</div>
</section>

<?php
get_footer();
