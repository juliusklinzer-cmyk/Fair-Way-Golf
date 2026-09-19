<?php
/**
 * Fair-Way-Golf Theme: lädt nur die Module aus inc/.
 * Konfiguration über Konstanten in wp-config.php (Fallbacks hier):
 *   FWG_CONTACT_EMAIL  Empfänger aller Anfragen
 *   FWG_MEETING_URL    HubSpot-Kalender von Julius (Link, kein Embed)
 *   FWG_GA_ID          GA4-Mess-ID; ohne Wert lädt weder Analytics noch Klaro
 *   FWG_SMTP_*         siehe mu-plugins/fwg-smtp.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'FWG_THEME_VERSION', '1.0.0' );

if ( ! defined( 'FWG_CONTACT_EMAIL' ) ) {
	define( 'FWG_CONTACT_EMAIL', 'hallo@fair-way-golf.com' );
}
if ( ! defined( 'FWG_MEETING_URL' ) ) {
	define( 'FWG_MEETING_URL', '' ); // leer = Termin-Links werden ausgeblendet; echten HubSpot-Link in wp-config.php setzen
}
if ( ! defined( 'FWG_GA_ID' ) ) {
	define( 'FWG_GA_ID', '' );
}

foreach ( array( 'helpers', 'setup', 'cleanup', 'assets', 'content', 'forms', 'anmeldungen', 'seo', 'consent', 'redirects' ) as $fwg_module ) {
	require_once get_template_directory() . '/inc/' . $fwg_module . '.php';
}
unset( $fwg_module );
