<?php
/**
 * Voranmeldung für Golfer.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();
$fwg_state  = isset( $_GET['fwg'] ) ? sanitize_key( $_GET['fwg'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$fwg_modell = isset( $_GET['modell'] ) ? sanitize_key( $_GET['modell'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
?>

<section class="fwg-hero fwg-hero--sub" aria-labelledby="hero-title">
	<div class="fwg-hero__panel">
		<div class="fwg-hero__media">
			<?php echo fwg_img( 'inselgruen', 'Grün an einem See im Abendlicht, Fahne am Ufer', '(min-width: 1280px) 1280px, 100vw', array( 'priority' => true ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</div>
		<div class="fwg-hero__content">
			<div>
				<h1 id="hero-title" class="fwg-hero__title">Sag uns, wo du spielen willst.</h1>
				<p class="fwg-hero__text">Deine Voranmeldung ist unverbindlich. Sie zeigt uns, welche Plätze dir wichtig sind, und den Anlagen, dass echte Nachfrage da ist.</p>
				<div class="fwg-hero__actions">
					<?php echo fwg_button( 'Zum Formular', '#formular', 'lime', 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</div>
			</div>
		</div>
	</div>
</section>

<section class="fwg-section" id="formular">
	<div class="fwg-container fwg-formlayout">
		<aside class="fwg-formlayout__aside">
			<h2 class="fwg-h3">Was danach passiert</h2>
			<ol class="fwg-steps fwg-steps--compact">
				<li><h3>E-Mail bestätigen</h3><p>Wenn du Updates willst, bestätigst du deine Adresse mit einem Klick.</p></li>
				<li><h3>Wir sprechen mit deinen Plätzen</h3><p>Jede Voranmeldung ist ein Argument für die Anlagen.</p></li>
				<li><h3>Du bekommst ein Angebot</h3><p>Sobald es losgeht. Du entscheidest dann, nicht heute.</p></li>
			</ol>
			<div class="fwg-aside__note"><?php echo fwg_icon( 'seal-check' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><p>Deine Daten liegen in unserer eigenen Datenbank in Deutschland. Kein Verkauf, keine Werbung von Dritten.</p></div>
		</aside>

		<div class="fwg-formlayout__main">
			<?php
			$fwg_token_page = fwg_token_page();
			if ( $fwg_token_page && 'ungueltig' !== $fwg_token_page['action'] ) {
				$fwg_confirm = 'bestaetigen' === $fwg_token_page['action'];
				echo '<div class="fwg-panel fwg-panel--mint fwg-tokenbox">';
				echo '<h2 class="fwg-h3">' . ( $fwg_confirm ? 'Updates von Fair-Way-Golf bekommen?' : 'Keine Updates mehr bekommen?' ) . '</h2>';
				echo '<p>' . ( $fwg_confirm ? 'Mit einem Klick bestätigst du, dass wir dich per E-Mail über den Neustart, neue Partnerplätze und dein Angebot informieren dürfen. Du kannst das jederzeit beenden.' : 'Mit einem Klick beendest du die Updates. Deine Voranmeldung bleibt bestehen.' ) . '</p>';
				echo '<form method="post" action="' . esc_url( home_url( '/voranmeldung/' ) ) . '">';
				wp_nonce_field( 'fwg_token_' . $fwg_token_page['token'] );
				echo '<input type="hidden" name="fwg_token_action" value="' . esc_attr( $fwg_token_page['action'] ) . '">';
				echo '<input type="hidden" name="fwg_token" value="' . esc_attr( $fwg_token_page['token'] ) . '">';
				echo '<button type="submit" class="fwg-btn fwg-btn--primary"><span>' . ( $fwg_confirm ? 'Ja, haltet mich auf dem Laufenden' : 'Ja, keine Updates mehr' ) . '</span></button>';
				echo '</form></div>';
			} elseif ( $fwg_token_page ) {
				echo '<div class="fwg-notice fwg-notice--fehler" role="status">' . fwg_icon( 'warning-circle' ) . '<p>Dieser Link ist nicht mehr gültig. Melde dich einfach noch einmal an.</p></div>'; // phpcs:ignore WordPress.Security.EscapeOutput
			}
			if ( 'bestaetigt' === $fwg_state ) {
				echo '<div class="fwg-notice fwg-notice--ok" role="status">' . fwg_icon( 'check-circle' ) . '<p>Danke, deine E-Mail-Adresse ist bestätigt. Wir halten dich auf dem Laufenden.</p></div>'; // phpcs:ignore WordPress.Security.EscapeOutput
			} elseif ( 'abgemeldet' === $fwg_state ) {
				echo '<div class="fwg-notice fwg-notice--ok" role="status">' . fwg_icon( 'check-circle' ) . '<p>Du bekommst keine Updates mehr. Deine Voranmeldung bleibt bestehen, bis du uns etwas anderes sagst.</p></div>'; // phpcs:ignore WordPress.Security.EscapeOutput
			} elseif ( 'link-ungueltig' === $fwg_state ) {
				echo '<div class="fwg-notice fwg-notice--fehler" role="status">' . fwg_icon( 'warning-circle' ) . '<p>Dieser Link ist nicht mehr gültig. Melde dich einfach noch einmal an.</p></div>'; // phpcs:ignore WordPress.Security.EscapeOutput
			}
			echo fwg_form_notice( 'voranmeldung' ); // phpcs:ignore WordPress.Security.EscapeOutput
			?>
			<form class="fwg-form" data-fwg-form="voranmeldung" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" novalidate>
				<?php get_template_part( 'template-parts/form-hidden', null, array( 'type' => 'voranmeldung' ) ); ?>
				<div class="fwg-form__row">
					<div class="fwg-field">
						<label for="fwg-vorname">Vorname <span class="fwg-field__req">(Pflicht)</span></label>
						<input id="fwg-vorname" name="vorname" type="text" autocomplete="given-name" required maxlength="120">
						<p class="fwg-field__error" data-fwg-error="vorname" hidden></p>
					</div>
					<div class="fwg-field">
						<label for="fwg-nachname">Nachname <span class="fwg-field__opt">(freiwillig)</span></label>
						<input id="fwg-nachname" name="nachname" type="text" autocomplete="family-name" maxlength="120">
					</div>
				</div>
				<div class="fwg-form__row">
					<div class="fwg-field">
						<label for="fwg-email">E-Mail <span class="fwg-field__req">(Pflicht)</span></label>
						<input id="fwg-email" name="email" type="email" autocomplete="email" inputmode="email" required maxlength="120">
						<p class="fwg-field__error" data-fwg-error="email" hidden></p>
					</div>
					<div class="fwg-field">
						<label for="fwg-ort">Wohnort <span class="fwg-field__opt">(freiwillig)</span></label>
						<input id="fwg-ort" name="ort" type="text" autocomplete="address-level2" maxlength="120" placeholder="z. B. München">
					</div>
				</div>
				<div class="fwg-field">
					<label for="fwg-club">Wo bist du gerade Mitglied? <span class="fwg-field__opt">(freiwillig)</span></label>
					<input id="fwg-club" name="club" type="text" maxlength="120" placeholder="Club oder „nirgends“">
				</div>

				<div class="fwg-field fwg-field--chips" data-fwg-chips>
					<label for="fwg-wunsch">Welche Plätze willst du spielen? <span class="fwg-field__req">(Pflicht)</span></label>
					<p class="fwg-field__hint" id="fwg-wunsch-hint" data-fwg-chips-hint="Einen Platz eingeben und mit Enter oder Komma bestätigen. Bis zu zehn Plätze.">Mehrere Plätze durch Komma trennen. Bis zu zehn Plätze.</p>
					<ul class="fwg-chips__list" data-fwg-chips-list aria-live="polite" aria-label="Deine Wunschplätze"></ul>
					<input id="fwg-wunsch" name="wunschplaetze" type="text" autocomplete="off" aria-describedby="fwg-wunsch-hint" placeholder="z. B. GC München Eichenried, GC Valley">
					<p class="fwg-field__error" data-fwg-error="wunschplaetze" hidden></p>
				</div>

				<fieldset class="fwg-field fwg-field--radios">
					<legend>Welches Modell interessiert dich?</legend>
					<div class="fwg-radios">
						<?php foreach ( fwg_kategorien() as $key => $label ) : ?>
							<label class="fwg-radio">
								<input type="radio" name="kategorie" value="<?php echo esc_attr( $key ); ?>"<?php checked( $fwg_modell ? $fwg_modell === $key : 'unsicher' === $key ); ?>>
								<span><?php echo esc_html( $label ); ?></span>
							</label>
						<?php endforeach; ?>
					</div>
					<p class="fwg-field__hint">Die Modelle stehen auf der <a href="<?php echo esc_url( home_url( '/#modelle' ) ); ?>">Startseite</a>.</p>
				</fieldset>

				<div class="fwg-field">
					<label for="fwg-nachricht">Willst du uns noch etwas sagen? <span class="fwg-field__opt">(freiwillig)</span></label>
					<textarea id="fwg-nachricht" name="nachricht" rows="3" maxlength="2000"></textarea>
				</div>

				<div class="fwg-field fwg-field--check">
					<input id="fwg-updates" type="checkbox" name="updates" value="1">
					<label for="fwg-updates">Haltet mich per E-Mail auf dem Laufenden: Neustart, neue Partnerplätze, mein Angebot. Ich bestätige das per Klick in der E-Mail und kann es jederzeit beenden.</label>
				</div>
				<?php get_template_part( 'template-parts/form-consent', null, array( 'id' => 'fwg-datenschutz-vor' ) ); ?>

				<p class="fwg-form__msg" data-fwg-msg role="status" aria-live="polite" hidden></p>
				<button type="submit" class="fwg-btn fwg-btn--primary fwg-btn--lg" data-fwg-submit><span>Voranmelden</span><?php echo fwg_icon( 'arrow-right', 'fwg-btn__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
			</form>
			<div class="fwg-form__done fwg-panel fwg-panel--mint" data-fwg-done hidden tabindex="-1">
				<?php echo fwg_icon( 'check-circle', 'fwg-done__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<h2 class="fwg-h3">Deine Voranmeldung ist da.</h2>
				<p data-fwg-done-text></p>
				<p>Kennst du jemanden, der auch auf vielen Plätzen spielen will? Dann schick diese Seite weiter.</p>
			</div>
		</div>
	</div>
</section>

<?php
get_footer();
