<?php
/**
 * Datenschutz-Checkbox (Pflicht) für die Formulare.
 *
 * @var array $args ['id' => string]
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$fwg_id = $args['id'] ?? 'fwg-datenschutz';
?>
<div class="fwg-field fwg-field--check">
	<input id="<?php echo esc_attr( $fwg_id ); ?>" type="checkbox" name="datenschutz" value="1" required>
	<label for="<?php echo esc_attr( $fwg_id ); ?>">Ich habe die <a href="<?php echo esc_url( home_url( '/datenschutz/' ) ); ?>" target="_blank" rel="noopener">Datenschutzerklärung</a> gelesen. Meine Angaben werden gespeichert, um meine Anfrage zu bearbeiten. <span class="fwg-field__req">(Pflicht)</span></label>
	<p class="fwg-field__error" data-fwg-error="datenschutz" hidden></p>
</div>
