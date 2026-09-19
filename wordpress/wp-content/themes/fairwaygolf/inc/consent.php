<?php
/**
 * Consent (Klaro, selbst gehostet) und Google Analytics 4.
 * Ohne FWG_GA_ID gibt es keinen Drittdienst und deshalb auch keinen Banner.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function fwg_has_consent_banner(): bool {
	return '' !== FWG_GA_ID;
}

function fwg_klaro_config(): array {
	return array(
		'elementID'              => 'klaro',
		'storageMethod'          => 'cookie',
		'cookieName'             => 'fwg_consent',
		'cookieExpiresAfterDays' => 180,
		'privacyPolicy'          => home_url( '/datenschutz/' ),
		'default'                => false,
		'mustConsent'            => false,
		'acceptAll'              => true,
		'hideDeclineAll'         => false,
		'hideLearnMore'          => false,
		'noticeAsModal'          => false,
		'htmlTexts'              => true,
		'lang'                   => 'de',
		'translations'           => array(
			'de' => array(
				'consentNotice' => array(
					'title'       => 'Kurze Frage zu Cookies',
					'description' => 'Wir nutzen Google Analytics, um zu sehen, wie die Seite genutzt wird. Das passiert nur mit deiner Einwilligung. Details in der <a href="' . esc_url( home_url( '/datenschutz/' ) ) . '">Datenschutzerklärung</a>.',
					'learnMore'   => 'Auswählen',
				),
				'consentModal'  => array(
					'title'       => 'Deine Auswahl',
					'description' => 'Hier legst du fest, welche Dienste wir verwenden dürfen. Notwendige Funktionen der Seite laufen ohne Cookies.',
				),
				'ok'            => 'Einverstanden',
				'acceptAll'     => 'Erlauben',
				'acceptSelected'=> 'Auswahl speichern',
				'decline'       => 'Ablehnen',
				'close'         => 'Schließen',
				'save'          => 'Speichern',
				'privacyPolicy' => array( 'name' => 'Datenschutzerklärung', 'text' => 'Mehr dazu in unserer {privacyPolicy}.' ),
				'poweredBy'     => '',
				'purposes'      => array( 'statistics' => 'Statistik' ),
				'googleanalytics' => array( 'title' => 'Google Analytics', 'description' => 'Statistik zur Nutzung der Seite mit gekürzter IP-Adresse. Setzt Cookies und überträgt Daten an Google.' ),
			),
		),
		'services'               => array(
			array(
				'name'     => 'googleanalytics',
				'title'    => 'Google Analytics',
				'purposes' => array( 'statistics' ),
				'default'  => false,
				'cookies'  => array( array( '/^_ga.*/', '/', '.fair-way-golf.com' ), array( '/^_ga.*/', '/', '' ) ),
			),
		),
	);
}

add_action(
	'wp_enqueue_scripts',
	function () {
		if ( ! fwg_has_consent_banner() ) {
			return;
		}
		$dir  = get_template_directory() . '/assets/klaro/';
		$base = get_template_directory_uri() . '/assets/klaro/';
		$ver  = file_exists( $dir . 'klaro.js' ) ? (string) filemtime( $dir . 'klaro.js' ) : FWG_THEME_VERSION;
		wp_enqueue_style( 'fwg-klaro', $base . 'klaro.css', array( 'fwg-main' ), $ver );
		wp_enqueue_script( 'fwg-klaro', $base . 'klaro.js', array(), $ver, true );
		wp_add_inline_script( 'fwg-klaro', 'window.klaroConfig = ' . wp_json_encode( fwg_klaro_config(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . ';', 'before' );
		// Barrierefreier Name für den Klaro-Dialog.
		wp_add_inline_script( 'fwg-klaro', "(function(){function n(){var d=document.querySelector('.cookie-notice[role=dialog],.cookie-modal-notice[role=dialog],#klaro-cookie-notice');if(!d)return false;if(!d.getAttribute('aria-label')){d.setAttribute('aria-label','Cookie-Hinweis');}return true;}if(n())return;var m=new MutationObserver(function(){if(n())m.disconnect();});document.addEventListener('DOMContentLoaded',function(){m.observe(document.body,{childList:true,subtree:true});});})();", 'after' );
	},
	30
);

add_filter(
	'script_loader_tag',
	function ( $tag, $handle ) {
		if ( 'fwg-klaro' === $handle ) {
			$tag = str_replace( ' src=', ' defer data-config="klaroConfig" src=', $tag );
		}
		return $tag;
	},
	10,
	2
);

/** GA4 nur als text/plain; Klaro aktiviert das Skript nach Einwilligung. */
add_action(
	'wp_head',
	function () {
		if ( ! fwg_has_consent_banner() ) {
			return;
		}
		$ga = FWG_GA_ID;
		echo '<script type="text/plain" data-type="application/javascript" data-name="googleanalytics" data-src="https://www.googletagmanager.com/gtag/js?id=' . esc_attr( $ga ) . '"></script>' . "\n";
		echo '<script type="text/plain" data-type="application/javascript" data-name="googleanalytics">window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag("js",new Date());gtag("config","' . esc_js( $ga ) . '",{anonymize_ip:true});</script>' . "\n";
	},
	30
);
