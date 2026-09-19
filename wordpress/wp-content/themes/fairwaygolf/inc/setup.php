<?php
/**
 * Theme-Setup: Supports, Menüs, Bildgrößen, Seiten-Anlage.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'after_setup_theme',
	function () {
		add_theme_support( 'title-tag' );
		add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'responsive-embeds' );
		load_theme_textdomain( 'fairwaygolf', get_template_directory() . '/languages' );
	}
);

/** Titel-Trenner. */
add_filter( 'document_title_separator', fn() => '·' );

/** Body-Klasse je Seite (für seitenspezifisches CSS ohne IDs). */
add_filter(
	'body_class',
	function ( $classes ) {
		if ( is_page() ) {
			$classes[] = 'fwg-page-' . get_post_field( 'post_name', get_queried_object_id() );
		}
		if ( is_front_page() ) {
			$classes[] = 'fwg-page-home';
		}
		return $classes;
	}
);

/**
 * Seiten anlegen, die das Theme braucht (idempotent). Läuft einmal nach der
 * Theme-Aktivierung und bei jedem Theme-Versionssprung.
 */
add_action(
	'after_switch_theme',
	function () {
		$pages = array(
			'voranmeldung'     => 'Voranmeldung',
			'golfplaetze'      => 'Für Golfanlagen',
			'unterstuetzen'    => 'Unterstützen',
			'ueber-uns'        => 'Über uns',
			'impressum'        => 'Impressum',
			'datenschutz'      => 'Datenschutzerklärung',
			'agb'              => 'AGB',
			'barrierefreiheit' => 'Erklärung zur Barrierefreiheit',
		);
		foreach ( $pages as $slug => $title ) {
			if ( ! get_page_by_path( $slug ) ) {
				wp_insert_post(
					array(
						'post_type'    => 'page',
						'post_status'  => 'publish',
						'post_name'    => $slug,
						'post_title'   => $title,
						'post_content' => '',
					)
				);
			}
		}
		$home = get_page_by_path( 'start' );
		if ( ! $home ) {
			$home_id = wp_insert_post(
				array(
					'post_type'   => 'page',
					'post_status' => 'publish',
					'post_name'   => 'start',
					'post_title'  => 'Start',
				)
			);
		} else {
			$home_id = $home->ID;
		}
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $home_id );
		update_option( 'page_for_posts', 0 );
		flush_rewrite_rules();
	}
);
