<?php
/**
 * Versteckte Felder für alle Formulare: Typ, Nonce, Zeitstempel, Quelle, Honeypot, Action (Nicht-JS).
 *
 * @var array $args ['type' => string]
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$fwg_type = $args['type'] ?? '';
?>
<input type="hidden" name="action" value="fwg_form">
<input type="hidden" name="form" value="<?php echo esc_attr( $fwg_type ); ?>">
<input type="hidden" name="nonce" value="<?php echo esc_attr( wp_create_nonce( 'fwg_form' ) ); ?>">
<?php $fwg_ts = time(); ?>
<input type="hidden" name="ts" value="<?php echo esc_attr( (string) $fwg_ts ); ?>">
<input type="hidden" name="sig" value="<?php echo esc_attr( fwg_ts_signature( $fwg_ts ) ); ?>">
<input type="hidden" name="quelle" value="<?php echo esc_attr( fwg_current_slug() ); ?>">
<div class="fwg-hp" aria-hidden="true"><label for="fwg-hp-<?php echo esc_attr( $fwg_type ); ?>">Website</label><input id="fwg-hp-<?php echo esc_attr( $fwg_type ); ?>" type="text" name="website" tabindex="-1" autocomplete="off"></div>
