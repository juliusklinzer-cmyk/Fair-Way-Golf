<?php
/**
 * 404 und 410 (alte Shop-Seiten).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();
$fwg_gone = (bool) get_query_var( 'fwg_gone' );
?>
<section class="fwg-section" aria-labelledby="fehler-title">
	<div class="fwg-container">
		<div class="fwg-panel fwg-panel--photo fwg-panel--cta">
			<?php echo fwg_img( 'aussicht', '', '(min-width: 1280px) 1280px, 100vw', array( 'class' => 'fwg-panel__bg' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<div class="fwg-panel__content">
				<h1 id="fehler-title" class="fwg-h2"><?php echo $fwg_gone ? 'Diese Seite gibt es nicht mehr.' : 'Hier ist kein Loch.'; ?></h1>
				<p class="fwg-lead"><?php echo $fwg_gone ? 'Der alte Shop und die Konto-Seiten sind mit der neuen Seite verschwunden. Was bleibt, ist die Voranmeldung.' : 'Die Adresse führt ins Rough. Zurück auf den Platz geht es hier:'; ?></p>
				<div class="fwg-actions">
					<?php echo fwg_button( 'Zur Startseite', home_url( '/' ), 'lime' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<?php echo fwg_button( 'Voranmelden', home_url( '/voranmeldung/' ), 'ghost', '' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</div>
			</div>
		</div>
	</div>
</section>
<?php
get_footer();
