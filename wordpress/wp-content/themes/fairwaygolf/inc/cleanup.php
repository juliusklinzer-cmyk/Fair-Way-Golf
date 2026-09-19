<?php
/**
 * Ballast raus, Härtung rein: keine Emojis, kein oEmbed, kein XML-RPC,
 * keine Nutzer-Enumeration, keine Kommentare, Sicherheits-Header.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'init',
	function () {
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'admin_print_styles', 'print_emoji_styles' );
		remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
		remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
		remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
		remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
		remove_action( 'wp_head', 'wp_oembed_add_host_js' );
		remove_action( 'wp_head', 'rsd_link' );
		remove_action( 'wp_head', 'wlwmanifest_link' );
		remove_action( 'wp_head', 'wp_generator' );
		remove_action( 'wp_head', 'wp_shortlink_wp_head' );
		remove_action( 'wp_head', 'feed_links_extra', 3 );
		remove_action( 'wp_head', 'rest_output_link_wp_head' );
		remove_action( 'template_redirect', 'rest_output_link_header', 11 );
	}
);

add_filter( 'emoji_svg_url', '__return_false' );
add_filter( 'xmlrpc_enabled', '__return_false' );
add_filter( 'xmlrpc_methods', fn( $m ) => array_diff_key( $m, array( 'pingback.ping' => 1, 'pingback.extensions.getPingbacks' => 1, 'system.multicall' => 1 ) ) );
add_filter( 'wp_is_application_passwords_available', '__return_false' );
add_filter( 'wp_headers', fn( $h ) => array_diff_key( $h, array( 'X-Pingback' => 1 ) ) );
add_filter( 'the_generator', '__return_empty_string' );
remove_action( 'template_redirect', 'wp_shortlink_header', 11 );
add_filter( 'feed_links_show_comments_feed', '__return_false' );

/** Feeds sind leer und verraten nur die Version: auf die Startseite umleiten. */
add_action(
	'do_feed_rss2',
	function () {
		wp_safe_redirect( home_url( '/' ), 301 );
		exit;
	},
	1
);

/** Paginierte Duplikate von Seiten (/page/2/) auf die Seite selbst umleiten. */
add_action(
	'template_redirect',
	function () {
		if ( ( is_page() || is_front_page() ) && ( is_paged() || (int) get_query_var( 'page' ) > 1 ) ) {
			wp_safe_redirect( is_front_page() ? home_url( '/' ) : get_permalink(), 301 );
			exit;
		}
	}
);

/** Block-CSS und globale Styles nur laden, wenn Inhalte sie brauchen (Rechtstexte nutzen einfaches HTML). */
add_action(
	'wp_enqueue_scripts',
	function () {
		wp_dequeue_style( 'wp-block-library' );
		wp_dequeue_style( 'wp-block-library-theme' );
		wp_dequeue_style( 'global-styles' );
		wp_dequeue_style( 'classic-theme-styles' );
		if ( ! is_user_logged_in() ) {
			wp_dequeue_style( 'dashicons' );
		}
	},
	20
);
add_filter( 'should_load_separate_core_block_assets', '__return_true' );

/** Kommentare komplett aus. */
add_action(
	'admin_init',
	function () {
		remove_menu_page( 'edit-comments.php' );
		foreach ( get_post_types() as $type ) {
			if ( post_type_supports( $type, 'comments' ) ) {
				remove_post_type_support( $type, 'comments' );
				remove_post_type_support( $type, 'trackbacks' );
			}
		}
	}
);
add_filter( 'comments_open', '__return_false', 20, 2 );
add_filter( 'pings_open', '__return_false', 20, 2 );
add_filter( 'comments_array', '__return_empty_array', 10, 2 );

/** Keine Nutzer-Enumeration über REST oder ?author=. */
add_filter(
	'rest_endpoints',
	function ( $endpoints ) {
		if ( ! is_user_logged_in() ) {
			unset( $endpoints['/wp/v2/users'], $endpoints['/wp/v2/users/(?P<id>[\d]+)'] );
		}
		return $endpoints;
	}
);
add_action(
	'template_redirect',
	function () {
		if ( is_author() || ( isset( $_GET['author'] ) && ! is_admin() ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			wp_safe_redirect( home_url( '/' ), 301 );
			exit;
		}
	}
);

/** Login-Fehler nicht verraten. */
add_filter( 'login_errors', fn() => 'Anmeldung fehlgeschlagen. Bitte prüfe deine Eingaben.' );

/** Sicherheits-Header. HSTS wird in konsoleH gesetzt. */
add_action(
	'send_headers',
	function () {
		if ( is_admin() ) {
			return;
		}
		header( 'X-Content-Type-Options: nosniff' );
		header( 'Referrer-Policy: strict-origin-when-cross-origin' );
		header( 'X-Frame-Options: SAMEORIGIN' );
		header( 'Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=(), usb=()' );
		header( "Content-Security-Policy: frame-ancestors 'self'; base-uri 'self'; form-action 'self'; object-src 'none'" );
	}
);

/** Uploads: keine SVG-Uploads ohne Bereinigung, keine PHP-Ausführung (Regel in .htaccess). */
add_filter(
	'upload_mimes',
	function ( $mimes ) {
		unset( $mimes['svg'], $mimes['svgz'] );
		return $mimes;
	}
);
