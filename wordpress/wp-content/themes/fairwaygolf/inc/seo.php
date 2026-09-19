<?php
/**
 * SEO: Meta-Beschreibungen, Open Graph, strukturierte Daten, noindex für Hilfsseiten.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function fwg_page_meta(): array {
	return array(
		'start'            => array(
			'title' => 'Fair-Way-Golf: Eine Mitgliedschaft, alle Golfplätze',
			'desc'  => 'Eine Mitgliedschaft, alle Golfplätze, kein Greenfee. Fair-Way-Golf startet neu: Melde dich unverbindlich vor, werde Partnerplatz oder unterstütze uns.',
		),
		'voranmeldung'     => array(
			'title' => 'Voranmeldung: Sag uns deine Wunschplätze',
			'desc'  => 'Unverbindlich vormerken: Welche Golfplätze willst du spielen, welches Modell passt zu dir? Wir melden uns, sobald Fair-Way-Golf startet.',
		),
		'golfplaetze'      => array(
			'title' => 'Golfanlagen: Neue Mitglieder, keine Kosten',
			'desc'  => 'Fair-Way-Golf bringt deiner Golfanlage neue Interessenten: kostenfrei, ohne Gebietsschutz, mit voller Auszahlung an den Heimatclub. Jetzt anfragen.',
		),
		'unterstuetzen'    => array(
			'title' => 'Unterstützen: Gründungsmitglied oder Investor',
			'desc'  => 'Über 2.000 Voranmeldungen und 20 Golfanlagen waren beim ersten Anlauf dabei. Hilf mit, Fair-Way-Golf wieder auf den Platz zu bringen.',
		),
		'ueber-uns'        => array(
			'title' => 'Über uns: Julius und die Geschichte des Projekts',
			'desc'  => 'Von der Idee 2020 bis zum Neustart: Wer hinter Fair-Way-Golf steht, was schon erreicht wurde und warum die Idee weiterlebt.',
		),
		'impressum'        => array( 'title' => 'Impressum', 'desc' => 'Anbieterkennzeichnung von fair-way-golf.com.', 'noindex' => true ),
		'datenschutz'      => array( 'title' => 'Datenschutzerklärung', 'desc' => 'Welche Daten fair-way-golf.com verarbeitet und warum.', 'noindex' => true ),
		'agb'              => array( 'title' => 'AGB', 'desc' => 'Allgemeine Geschäftsbedingungen von Fair-Way-Golf.', 'noindex' => true ),
		'barrierefreiheit' => array( 'title' => 'Erklärung zur Barrierefreiheit', 'desc' => 'Stand der Barrierefreiheit von fair-way-golf.com und Kontakt für Rückmeldungen.', 'noindex' => true ),
	);
}

function fwg_current_slug(): string {
	if ( is_front_page() ) {
		return 'start';
	}
	if ( is_page() ) {
		return (string) get_post_field( 'post_name', get_queried_object_id() );
	}
	return '';
}

add_filter(
	'document_title_parts',
	function ( $parts ) {
		$meta = fwg_page_meta();
		$slug = fwg_current_slug();
		if ( isset( $meta[ $slug ]['title'] ) ) {
			$parts['title'] = $meta[ $slug ]['title'];
			if ( 'start' === $slug ) {
				unset( $parts['tagline'], $parts['site'] );
			}
		}
		return $parts;
	},
	20
);

add_filter(
	'wp_robots',
	function ( $robots ) {
		$meta = fwg_page_meta();
		$slug = fwg_current_slug();
		if ( ! empty( $meta[ $slug ]['noindex'] ) || is_404() ) {
			$robots['noindex'] = true;
			$robots['follow']  = true;
		} else {
			$robots['max-image-preview'] = 'large';
		}
		return $robots;
	}
);

add_action(
	'wp_head',
	function () {
		$meta  = fwg_page_meta();
		$slug  = fwg_current_slug();
		$desc  = $meta[ $slug ]['desc'] ?? '';
		$title = wp_get_document_title();
		$url   = is_front_page() ? home_url( '/' ) : ( is_page() ? get_permalink() : home_url( '/' ) );
		$img   = get_template_directory_uri() . '/assets/img/og.jpg';
		if ( $desc ) {
			echo '<meta name="description" content="' . esc_attr( $desc ) . '">' . "\n";
		}
		echo '<meta property="og:type" content="website">' . "\n";
		echo '<meta property="og:site_name" content="Fair-Way-Golf">' . "\n";
		echo '<meta property="og:locale" content="de_DE">' . "\n";
		echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
		if ( $desc ) {
			echo '<meta property="og:description" content="' . esc_attr( $desc ) . '">' . "\n";
		}
		echo '<meta property="og:url" content="' . esc_url( $url ) . '">' . "\n";
		echo '<meta property="og:image" content="' . esc_url( $img ) . '">' . "\n";
		echo '<meta property="og:image:width" content="1200"><meta property="og:image:height" content="630">' . "\n";
		echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
		if ( is_front_page() ) {
			$ld = array(
				'@context' => 'https://schema.org',
				'@graph'   => array(
					array(
						'@type'       => 'Organization',
						'@id'         => home_url( '/#organization' ),
						'name'        => 'Fair-Way-Golf',
						'url'         => home_url( '/' ),
						'logo'        => get_template_directory_uri() . '/assets/img/apple-touch-icon.png',
						'email'       => FWG_CONTACT_EMAIL,
						'areaServed'  => 'Bayern, Deutschland',
						'description' => 'Golf-Mitgliedschaftsmodell: eine Mitgliedschaft, viele Partnerplätze, kein Greenfee. Aktuell in der Voranmeldung für den zweiten Anlauf.',
					),
					array(
						'@type'     => 'WebSite',
						'@id'       => home_url( '/#website' ),
						'url'       => home_url( '/' ),
						'name'      => 'Fair-Way-Golf',
						'inLanguage' => 'de-DE',
						'publisher' => array( '@id' => home_url( '/#organization' ) ),
					),
				),
			);
			echo '<script type="application/ld+json">' . wp_json_encode( $ld, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
		}
		if ( is_page( 'golfplaetze' ) || is_front_page() ) {
			$faq   = is_front_page() ? fwg_faq_golfer() : fwg_faq_anlagen();
			$items = array();
			foreach ( $faq as $f ) {
				$items[] = array( '@type' => 'Question', 'name' => $f['q'], 'acceptedAnswer' => array( '@type' => 'Answer', 'text' => $f['a'] ) );
			}
			echo '<script type="application/ld+json">' . wp_json_encode( array( '@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $items ), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
		}
	},
	5
);

/** Sitemap: nur echte Seiten, keine Rechtstexte, keine Anmeldungen. */
add_filter( 'wp_sitemaps_add_provider', fn( $provider, $name ) => 'users' === $name || 'taxonomies' === $name ? false : $provider, 10, 2 );
add_filter(
	'wp_sitemaps_posts_query_args',
	function ( $args, $type ) {
		if ( 'page' === $type ) {
			$exclude = array();
			foreach ( array( 'impressum', 'datenschutz', 'agb', 'barrierefreiheit' ) as $slug ) {
				$p = get_page_by_path( $slug );
				if ( $p ) {
					$exclude[] = $p->ID;
				}
			}
			$args['post__not_in'] = $exclude;
		}
		return $args;
	},
	10,
	2
);
add_filter( 'wp_sitemaps_post_types', fn( $types ) => array_intersect_key( $types, array( 'page' => 1 ) ) );
add_action(
	'template_redirect',
	function () {
		$sm = get_query_var( 'sitemap' );
		if ( '' !== $sm && ! in_array( $sm, array( 'index', 'posts' ), true ) ) {
			global $wp_query;
			$wp_query->set_404();
			status_header( 404 );
			nocache_headers();
		}
	},
	0
);
