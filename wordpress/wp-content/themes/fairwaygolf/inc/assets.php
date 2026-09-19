<?php
/**
 * Styles und Scripts: Tokens, Main, JS (defer), Font-Preload, Ajax-Daten.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'wp_enqueue_scripts',
	function () {
		$dir  = get_template_directory();
		$base = get_template_directory_uri() . '/assets/';
		$ver  = function ( $rel ) use ( $dir ) {
			$f = $dir . '/assets/' . $rel;
			return file_exists( $f ) ? (string) filemtime( $f ) : FWG_THEME_VERSION;
		};
		wp_enqueue_style( 'fwg-tokens', $base . 'css/tokens.css', array(), $ver( 'css/tokens.css' ) );
		wp_enqueue_style( 'fwg-main', $base . 'css/main.css', array( 'fwg-tokens' ), $ver( 'css/main.css' ) );
		wp_enqueue_script( 'fwg-main', $base . 'js/main.js', array(), $ver( 'js/main.js' ), array( 'strategy' => 'defer', 'in_footer' => true ) );
		wp_localize_script(
			'fwg-main',
			'fwgAjax',
			array(
				'url'   => admin_url( 'admin-ajax.php' ),
				'nonce' => wp_create_nonce( 'fwg_form' ),
			)
		);
	}
);

/** Früh im Head: JS-Marker (für Reveal-Styles), Viewport, Theme-Color, Font-Preload, Icons. */
add_action(
	'wp_head',
	function () {
		$base = get_template_directory_uri() . '/assets/';
		echo '<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">' . "\n";
		echo '<meta name="theme-color" content="#005949">' . "\n";
		echo '<script>document.documentElement.classList.add("fwg-js");</script>' . "\n";
		echo '<link rel="preload" href="' . esc_url( $base . 'fonts/rajdhani-700.woff2' ) . '" as="font" type="font/woff2" crossorigin>' . "\n";
		echo '<link rel="preload" href="' . esc_url( $base . 'fonts/barlow-400.woff2' ) . '" as="font" type="font/woff2" crossorigin>' . "\n";
		echo '<link rel="icon" href="' . esc_url( $base . 'img/favicon.svg' ) . '" type="image/svg+xml">' . "\n";
		echo '<link rel="apple-touch-icon" href="' . esc_url( $base . 'img/apple-touch-icon.png' ) . '">' . "\n";
	},
	1
);

/** Keine jQuery im Frontend, wenn nicht eingeloggt. */
add_action(
	'wp_enqueue_scripts',
	function () {
		if ( ! is_admin() && ! is_user_logged_in() ) {
			wp_deregister_script( 'jquery' );
		}
	},
	100
);
