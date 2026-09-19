<?php
/**
 * Hilfsfunktionen: Assets, Icons, Bilder, Navigation.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** URL eines Theme-Assets mit Dateizeit als Version. */
function fwg_asset( string $path ): string {
	$file = get_template_directory() . '/assets/' . ltrim( $path, '/' );
	$ver  = file_exists( $file ) ? (string) filemtime( $file ) : FWG_THEME_VERSION;
	return get_template_directory_uri() . '/assets/' . ltrim( $path, '/' ) . '?v=' . $ver;
}

/** Icon aus dem Phosphor-Sprite (assets/img/icons.svg). */
function fwg_icon( string $name, string $class = '' ): string {
	static $sprite = null;
	if ( null === $sprite ) {
		$sprite = fwg_asset( 'img/icons.svg' );
	}
	$cls = trim( 'fwg-icon ' . $class );
	return '<svg class="' . esc_attr( $cls ) . '" aria-hidden="true" focusable="false" width="24" height="24"><use href="' . esc_url( $sprite ) . '#i-' . esc_attr( $name ) . '"></use></svg>';
}

/**
 * Responsives Bild aus den WebP-Varianten in assets/img/<key>-<w>.webp.
 *
 * @param string $key      Bildschlüssel aus wordpress/_build/images.php.
 * @param string $alt      Alt-Text (leer = dekorativ).
 * @param string $sizes    sizes-Attribut.
 * @param array  $args     class, priority (LCP), width_hint.
 */
function fwg_img( string $key, string $alt, string $sizes = '100vw', array $args = array() ): string {
	static $cache = array();
	$dir = get_template_directory() . '/assets/img/';
	if ( ! isset( $cache[ $key ] ) ) {
		$files = glob( $dir . $key . '-*.webp' );
		$list  = array();
		foreach ( (array) $files as $f ) {
			if ( preg_match( '/-(\d+)\.webp$/', $f, $m ) ) {
				$list[ (int) $m[1] ] = basename( $f );
			}
		}
		ksort( $list );
		$dims = array( 0, 0 );
		if ( $list ) {
			$largest = $dir . end( $list );
			$size    = @getimagesize( $largest );
			if ( $size ) {
				$dims = array( $size[0], $size[1] );
			}
		}
		$cache[ $key ] = array( 'list' => $list, 'dims' => $dims );
	}
	$data = $cache[ $key ];
	if ( ! $data['list'] ) {
		return '<!-- Bild fehlt: ' . esc_html( $key ) . ' -->';
	}
	$base   = get_template_directory_uri() . '/assets/img/';
	$srcset = array();
	foreach ( $data['list'] as $w => $file ) {
		$srcset[] = $base . $file . ' ' . $w . 'w';
	}
	$largest_w = array_key_last( $data['list'] );
	$fallback  = $base . $data['list'][ $largest_w ];
	$priority  = ! empty( $args['priority'] );
	$attrs     = array(
		'src'      => $fallback,
		'srcset'   => implode( ', ', $srcset ),
		'sizes'    => $sizes,
		'alt'      => $alt,
		'width'    => $data['dims'][0],
		'height'   => $data['dims'][1],
		'loading'  => $priority ? 'eager' : 'lazy',
		'decoding' => $priority ? 'sync' : 'async',
	);
	if ( $priority ) {
		$attrs['fetchpriority'] = 'high';
	}
	if ( ! empty( $args['class'] ) ) {
		$attrs['class'] = $args['class'];
	}
	$html = '<img';
	foreach ( $attrs as $k => $v ) {
		$html .= ' ' . $k . '="' . esc_attr( (string) $v ) . '"';
	}
	return $html . '>';
}

/** Logo (Mark + Wortmarke) als Link zur Startseite. */
function fwg_logo( string $class = '' ): string {
	$mark = file_get_contents( get_template_directory() . '/assets/img/logo-mark.svg' );
	return '<a href="' . esc_url( home_url( '/' ) ) . '" class="fwg-logo ' . esc_attr( $class ) . '" aria-label="Fair-Way-Golf, zur Startseite">'
		. $mark
		. '<span class="fwg-logo__word">Fair-Way-Golf</span></a>';
}

/** Klasse für den aktiven Navigationslink. */
function fwg_nav_class( string $slug, string $base = 'fwg-nav__link' ): string {
	return is_page( $slug ) ? $base . ' ' . $base . '--active' : $base;
}

/** Ist die aktuelle Seite eine der Formularseiten? */
function fwg_is_form_page(): bool {
	return is_page( array( 'voranmeldung', 'golfplaetze', 'unterstuetzen' ) );
}

/** Einheitlicher Button. $variant: primary | secondary | ghost | inverse | inverse-secondary. */
function fwg_button( string $label, string $href, string $variant = 'primary', string $icon = 'arrow-right', array $attrs = array() ): string {
	$cls  = 'fwg-btn fwg-btn--' . $variant;
	$html = '<a href="' . esc_url( $href ) . '" class="' . esc_attr( $cls ) . '"';
	foreach ( $attrs as $k => $v ) {
		$html .= ' ' . esc_attr( $k ) . '="' . esc_attr( $v ) . '"';
	}
	$html .= '><span>' . esc_html( $label ) . '</span>';
	if ( $icon ) {
		$html .= fwg_icon( $icon, 'fwg-btn__icon' );
	}
	return $html . '</a>';
}

/** Sanfter Trenner in Grün-Sektionen. */
function fwg_hairline(): string {
	return '<hr class="fwg-hairline" aria-hidden="true">';
}
