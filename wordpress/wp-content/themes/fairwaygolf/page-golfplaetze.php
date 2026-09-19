<?php
/**
 * Für Golfanlagen: Partner werden.
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
			<?php echo fwg_img( 'wasser', 'Grün mit Fahne hinter einer Holzwand am Wasser, Palmen im Hintergrund', '(min-width: 1280px) 1280px, 100vw', array( 'priority' => true ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</div>
		<div class="fwg-hero__content">
			<div>
				<h1 id="hero-title" class="fwg-hero__title">Die perfekte Erweiterung für dein Angebot.</h1>
				<p class="fwg-hero__text">Fair-Way-Golf bringt dir Golfer, die sonst nirgends Mitglied werden, und vermittelt sie direkt an deinen Club. Kostenfrei, ohne Gebietsschutz, mit voller Auszahlung an den Heimatclub.</p>
				<div class="fwg-hero__actions">
					<?php echo fwg_button( 'Anfragen', '#formular', 'lime' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<?php if ( $fwg_meeting ) : ?>
						<a href="<?php echo esc_url( FWG_MEETING_URL ); ?>" class="fwg-btn fwg-btn--ghost" rel="noopener" target="_blank"><span>Termin mit Julius buchen</span></a>
					<?php endif; ?>
				</div>
			</div>
			<div class="fwg-hero__card">
				<span class="fwg-hero__card-num">20</span>
				<strong>Golfanlagen in Bayern</strong>
				<span>waren beim ersten Anlauf dabei. Jetzt suchen wir Partner in ganz Deutschland.</span>
			</div>
		</div>
	</div>
</section>

<section class="fwg-section" aria-labelledby="vorteile-title">
	<div class="fwg-container">
		<div class="fwg-section__head">
			<h2 id="vorteile-title" class="fwg-h2">Was dein Platz davon hat</h2>
			<p class="fwg-lead">Das Managen einer Golfanlage wird anspruchsvoller. Fair-Way-Golf bringt neue Golfer, ohne dass du etwas dafür zahlst.</p>
		</div>
		<div class="fwg-grid fwg-grid--3">
			<div class="fwg-tile fwg-tile--green"><div class="fwg-tile__body"><?php echo fwg_icon( 'users-three', 'fwg-tile__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><h3>Mehr Mitglieder</h3><p>Golfer ohne Club werden Mitglied im nächstgelegenen Partnerclub. Das Marketing dafür übernehmen wir.</p></div></div>
			<div class="fwg-tile fwg-tile--mint"><div class="fwg-tile__body"><?php echo fwg_icon( 'seal-check', 'fwg-tile__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><h3>Sicherheit</h3><p>Der Heimatclub bekommt seine Auszahlung zu 100 Prozent, auch wenn ein Mitglied in einem Monat nicht spielt. Bestandsmitglieder bleiben bei dir.</p></div></div>
			<div class="fwg-tile fwg-tile--lime"><div class="fwg-tile__body"><?php echo fwg_icon( 'globe', 'fwg-tile__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><h3>Kein Gebietsschutz</h3><p>Golfer spielen frei, Clubs werben sich nicht gegenseitig die Mitglieder ab, und die ganze Region wird attraktiver.</p></div></div>
			<div class="fwg-tile fwg-tile--photo"><?php echo fwg_img( 'oben-gruen', 'Golfplatz von oben mit Grün, Weg und Teichen', '(min-width: 1024px) 32vw, 100vw' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><div class="fwg-tile__body"><h3>Cluster-Marketing</h3><p>Deine Golfregion wird gemeinsam vermarktet, ohne zusätzliche Kosten für dich.</p></div></div>
			<div class="fwg-tile fwg-tile--photo"><?php echo fwg_img( 'range-szene', 'Golfer schlägt auf der Driving Range ab', '(min-width: 1024px) 32vw, 100vw' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><div class="fwg-tile__body"><h3>Umsatz</h3><p>Im ersten Anlauf haben wir mit 5 bis 12 Prozent mehr Umsatz je Anlage kalkuliert. Für deinen Platz rechnen wir es gemeinsam durch.</p></div></div>
			<div class="fwg-tile fwg-tile--green"><div class="fwg-tile__body"><?php echo fwg_icon( 'lightning', 'fwg-tile__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><h3>Kein Mehraufwand</h3><p>Buchungen laufen über die App und dein Startzeitensystem. Der Check-in per QR-Code am Clubhaus ersetzt das Papier-Bag-Tag.</p></div></div>
		</div>
	</div>
</section>

<section class="fwg-section fwg-section--flush" aria-labelledby="ablauf-title">
	<div class="fwg-container">
		<div class="fwg-panel fwg-panel--green fwg-steps-panel">
			<div>
				<h2 id="ablauf-title" class="fwg-h2">Einfacher Einstieg, langfristiger Erfolg</h2>
				<p class="fwg-lead">So läuft die Partnerschaft, vom ersten Gespräch bis zur monatlichen Auszahlung.</p>
				<ol class="fwg-steps">
					<li><h3>Kennenlernen</h3><p>Wir legen gemeinsam fest, in welche Kategorie dein Platz gehört und welches Angebot passt.</p></li>
					<li><h3>Anbindung</h3><p>Dein Startzeitensystem wird mit der App verbunden. Du siehst alle Buchungen ohne zusätzliche Verwaltung.</p></li>
					<li><h3>QR-Code am Clubhaus</h3><p>Jede Runde wird beim Check-in bestätigt. Keine Fehlbuchungen, kein Papier.</p></li>
					<li><h3>Mitglieder und Auswertung</h3><p>Golfer ohne Club werden über uns Mitglied bei dir. Monatlich siehst du Spielverhalten und Gäste anderer Partnerplätze.</p></li>
					<li><h3>Auszahlung</h3><p>Monatliche Zahlung auf Basis der Auswertung. Volle Auszahlung an den Heimatclub, auch ohne gespielte Runde.</p></li>
				</ol>
			</div>
			<div class="fwg-appshot">
				<?php echo fwg_img( 'fahne', '', '(min-width: 1024px) 50vw, 100vw', array( 'class' => 'fwg-appshot__bg' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<span class="fwg-appshot__badge"><?php echo fwg_icon( 'check-circle' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>Startzeiten in der App</span>
				<?php echo fwg_img( 'app-tee', 'Startzeitenbuchung in der Fair-Way-Golf-App mit freien Zeiten und Mitspielern', '(min-width: 1024px) 22vw, 60vw', array( 'class' => 'fwg-appshot__phone' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</div>
		</div>
	</div>
</section>

<section class="fwg-section" id="anlagen-fragen" aria-labelledby="anlagen-fragen-title">
	<div class="fwg-container fwg-faq-layout">
		<div>
			<h2 id="anlagen-fragen-title" class="fwg-h2">Fragen von Clubmanagern</h2>
			<p class="fwg-lead">Alles, was du vor dem ersten Gespräch wissen willst.</p>
		</div>
		<div class="fwg-faq">
			<?php foreach ( fwg_faq_anlagen() as $i => $f ) : ?>
				<details class="fwg-faq__item" name="faq-anlagen"<?php echo 0 === $i ? ' open' : ''; ?>>
					<summary><span><?php echo esc_html( $f['q'] ); ?></span><?php echo fwg_icon( 'plus', 'fwg-faq__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></summary>
					<div class="fwg-faq__body"><p><?php echo esc_html( $f['a'] ); ?></p></div>
				</details>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="fwg-section fwg-section--flush" id="formular" aria-labelledby="formular-title">
	<div class="fwg-container">
		<div class="fwg-panel fwg-panel--alt fwg-formlayout">
			<aside class="fwg-formlayout__aside">
				<h2 id="formular-title" class="fwg-h3">Wir melden uns bei dir</h2>
				<p class="fwg-muted">Schreib uns, wer du bist und wie wir dich am besten erreichen. Julius meldet sich persönlich, in der Regel innerhalb weniger Tage.</p>
				<?php if ( $fwg_meeting ) : ?>
					<p><a href="<?php echo esc_url( FWG_MEETING_URL ); ?>" class="fwg-link" rel="noopener" target="_blank">Termin mit Julius buchen <?php echo fwg_icon( 'arrow-up-right' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a></p>
				<?php endif; ?>
			</aside>
			<div class="fwg-formlayout__main">
				<?php echo fwg_form_notice( 'golfanlage' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<form class="fwg-form" data-fwg-form="golfanlage" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" novalidate>
					<?php get_template_part( 'template-parts/form-hidden', null, array( 'type' => 'golfanlage' ) ); ?>
					<div class="fwg-field">
						<label for="fwg-anlage">Golfanlage <span class="fwg-field__req">(Pflicht)</span></label>
						<input id="fwg-anlage" name="anlage" type="text" autocomplete="organization" required maxlength="120">
						<p class="fwg-field__error" data-fwg-error="anlage" hidden></p>
					</div>
					<div class="fwg-form__row">
						<div class="fwg-field">
							<label for="fwg-name">Ansprechpartner <span class="fwg-field__req">(Pflicht)</span></label>
							<input id="fwg-name" name="name" type="text" autocomplete="name" required maxlength="120">
							<p class="fwg-field__error" data-fwg-error="name" hidden></p>
						</div>
						<div class="fwg-field">
							<label for="fwg-email-anlage">E-Mail <span class="fwg-field__req">(Pflicht)</span></label>
							<input id="fwg-email-anlage" name="email" type="email" autocomplete="email" inputmode="email" required maxlength="120">
							<p class="fwg-field__error" data-fwg-error="email" hidden></p>
						</div>
					</div>
					<div class="fwg-form__row">
						<div class="fwg-field">
							<label for="fwg-telefon">Telefon <span class="fwg-field__opt">(freiwillig)</span></label>
							<input id="fwg-telefon" name="telefon" type="tel" autocomplete="tel" inputmode="tel" maxlength="40">
						</div>
						<div class="fwg-field">
							<label for="fwg-kontaktweg">Wie erreichen wir dich am besten?</label>
							<select id="fwg-kontaktweg" name="kontaktweg">
								<?php foreach ( fwg_kontaktwege() as $k => $label ) : ?>
									<option value="<?php echo esc_attr( $k ); ?>"><?php echo esc_html( $label ); ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>
					<div class="fwg-field">
						<label for="fwg-nachricht-anlage">Deine Nachricht <span class="fwg-field__opt">(freiwillig)</span></label>
						<textarea id="fwg-nachricht-anlage" name="nachricht" rows="4" maxlength="2000" placeholder="Was interessiert dich am meisten: Kategorie, Zahlen, Anbindung?"></textarea>
					</div>
					<?php get_template_part( 'template-parts/form-consent', null, array( 'id' => 'fwg-datenschutz-anlage' ) ); ?>
					<p class="fwg-form__msg" data-fwg-msg role="status" aria-live="polite" hidden></p>
					<button type="submit" class="fwg-btn fwg-btn--primary fwg-btn--lg" data-fwg-submit><span>Anfragen</span><?php echo fwg_icon( 'arrow-right', 'fwg-btn__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
				</form>
				<div class="fwg-form__done fwg-panel fwg-panel--mint" data-fwg-done hidden tabindex="-1">
					<?php echo fwg_icon( 'check-circle', 'fwg-done__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<h2 class="fwg-h3">Deine Anfrage ist da.</h2>
					<p data-fwg-done-text></p>
				</div>
			</div>
		</div>
	</div>
</section>

<?php
get_footer();
