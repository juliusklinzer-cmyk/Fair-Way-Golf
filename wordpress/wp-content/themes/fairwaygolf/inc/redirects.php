<?php
/**
 * Weiterleitungen der alten URLs (docs/alte-urls.md). 301 auf die neue Seite,
 * 410 für Shop-Reste, die es nicht mehr gibt.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function fwg_redirect_map(): array {
	return array(
		'/golf-in-muenchen'             => '/',
		'/lieblingsplaetze-voranmeldung' => '/voranmeldung/',
		'/onboarding-form'              => '/voranmeldung/',
		'/partner-port'                 => '/golfplaetze/',
		'/kalender-julius'              => '/golfplaetze/#termin',
		'/kalender-julius-call'         => '/golfplaetze/#termin',
		'/datenschutzerklaerung'        => '/datenschutz/',
		'/agbs'                         => '/agb/',
		'/gc-eixendorfer-see'           => '/ueber-uns/',
		'/widerrufsbelehrung'           => '/agb/',
	);
}

function fwg_gone_paths(): array {
	return array( '/shop', '/warenkorb', '/kasse', '/mein-konto', '/bezahlmoeglichkeiten', '/echtheit-von-bewertungen' );
}

add_action(
	'template_redirect',
	function () {
		if ( ! is_404() ) {
			return;
		}
		$uri  = isset( $_SERVER['REQUEST_URI'] ) ? (string) wp_unslash( $_SERVER['REQUEST_URI'] ) : ''; // phpcs:ignore
		$path = rtrim( (string) wp_parse_url( $uri, PHP_URL_PATH ), '/' );
		$path = strtolower( $path );
		if ( '' === $path ) {
			return;
		}
		$map = fwg_redirect_map();
		if ( isset( $map[ $path ] ) ) {
			wp_safe_redirect( home_url( $map[ $path ] ), 301 );
			exit;
		}
		if ( in_array( $path, fwg_gone_paths(), true ) ) {
			status_header( 410 );
			nocache_headers();
			set_query_var( 'fwg_gone', true );
		}
	},
	1
);
