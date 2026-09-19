</main>

<footer class="fwg-footer">
	<div class="fwg-footer__panel">
		<div class="fwg-footer__grid">
			<div class="fwg-footer__brand">
				<?php echo fwg_logo( 'fwg-footer__logo' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<p>Eine Mitgliedschaft, alle Golfplätze. Ein Projekt aus München, das gerade seinen zweiten Anlauf vorbereitet.</p>
				<p><a href="mailto:<?php echo esc_attr( FWG_CONTACT_EMAIL ); ?>" class="fwg-footer__mail"><?php echo esc_html( FWG_CONTACT_EMAIL ); ?></a></p>
			</div>
			<nav class="fwg-footer__col" aria-label="Mitmachen">
				<h2>Mitmachen</h2>
				<a href="<?php echo esc_url( home_url( '/voranmeldung/' ) ); ?>">Voranmelden</a>
				<a href="<?php echo esc_url( home_url( '/golfplaetze/' ) ); ?>">Für Golfanlagen</a>
				<a href="<?php echo esc_url( home_url( '/unterstuetzen/' ) ); ?>">Unterstützen</a>
			</nav>
			<nav class="fwg-footer__col" aria-label="Fair-Way-Golf">
				<h2>Fair-Way-Golf</h2>
				<a href="<?php echo esc_url( home_url( '/#so-gehts' ) ); ?>">So funktioniert es</a>
				<a href="<?php echo esc_url( home_url( '/#modelle' ) ); ?>">Modelle und Preise</a>
				<a href="<?php echo esc_url( home_url( '/#fragen' ) ); ?>">Fragen und Antworten</a>
				<a href="<?php echo esc_url( home_url( '/ueber-uns/' ) ); ?>">Über uns</a>
			</nav>
			<nav class="fwg-footer__col" aria-label="Rechtliches">
				<h2>Rechtliches</h2>
				<a href="<?php echo esc_url( home_url( '/impressum/' ) ); ?>">Impressum</a>
				<a href="<?php echo esc_url( home_url( '/datenschutz/' ) ); ?>">Datenschutz</a>
				<a href="<?php echo esc_url( home_url( '/agb/' ) ); ?>">AGB</a>
				<a href="<?php echo esc_url( home_url( '/barrierefreiheit/' ) ); ?>">Barrierefreiheit</a>
				<?php if ( fwg_has_consent_banner() ) : ?>
					<a href="#" data-fwg-consent>Cookie-Einstellungen</a>
				<?php endif; ?>
			</nav>
		</div>
		<div class="fwg-footer__word" aria-hidden="true">Fair-Way-Golf</div>
		<div class="fwg-footer__bottom">
			<span>© <?php echo esc_html( gmdate( 'Y' ) ); ?> Fair-Way-Golf · Julius Klinzer, München</span>
			<span>Von Golfern für Golfer.</span>
		</div>
	</div>
</footer>

<?php if ( ! is_page( 'voranmeldung' ) && ! is_404() ) : ?>
<div class="fwg-popup" data-fwg-popup hidden role="dialog" aria-labelledby="fwg-popup-title" aria-describedby="fwg-popup-text">
	<button type="button" class="fwg-popup__close" data-fwg-popup-close aria-label="Schließen"><?php echo fwg_icon( 'x' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
	<form class="fwg-popup__form" data-fwg-form="lieblingsplatz" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" novalidate>
		<h2 id="fwg-popup-title">Sag uns deinen Lieblingsplatz</h2>
		<p id="fwg-popup-text">Ein Platz reicht. So wissen wir, wo wir zuerst anklopfen sollen.</p>
		<?php get_template_part( 'template-parts/form-hidden', null, array( 'type' => 'lieblingsplatz' ) ); ?>
		<div class="fwg-field">
			<label for="fwg-popup-platz">Dein Lieblingsplatz <span class="fwg-field__req">(Pflicht)</span></label>
			<input id="fwg-popup-platz" name="platz" type="text" autocomplete="off" required maxlength="80" placeholder="z. B. GC Eichenried">
			<p class="fwg-field__error" data-fwg-error="platz" hidden></p>
		</div>
		<div class="fwg-field">
			<label for="fwg-popup-email">E-Mail <span class="fwg-field__opt">(freiwillig, für Rückfragen)</span></label>
			<input id="fwg-popup-email" name="email" type="email" autocomplete="email" inputmode="email" maxlength="120">
			<p class="fwg-field__error" data-fwg-error="email" hidden></p>
		</div>
		<p class="fwg-form__msg" data-fwg-msg role="status" aria-live="polite" hidden></p>
		<button type="submit" class="fwg-btn fwg-btn--primary fwg-btn--sm fwg-btn--block" data-fwg-submit><span>Lieblingsplatz senden</span></button>
		<p class="fwg-popup__note">Hinweise zum Umgang mit deinen Daten stehen in der <a href="<?php echo esc_url( home_url( '/datenschutz/' ) ); ?>">Datenschutzerklärung</a>.</p>
	</form>
	<div class="fwg-popup__done" data-fwg-done hidden>
		<?php echo fwg_icon( 'check-circle', 'fwg-done__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		<h2>Notiert. Danke dir!</h2>
		<p>Wenn du mehr willst: <a href="<?php echo esc_url( home_url( '/voranmeldung/' ) ); ?>">Voranmeldung mit Wunschplätzen</a>.</p>
	</div>
</div>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
