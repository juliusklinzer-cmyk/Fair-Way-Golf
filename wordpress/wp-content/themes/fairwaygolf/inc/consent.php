<?php
/**
 * Consent (Klaro, selbst gehostet) und Google Analytics 4.
 * Banner, Texte und Design 1:1 aus firmengolf.app übernommen (zentriertes Modal beim ersten Besuch,
 * gleichwertiges „Ablehnen“, Einstellungen jederzeit über „Cookie-Einstellungen“ im Footer).
 * Ohne FWG_GA_ID gibt es keinen Drittdienst und deshalb auch keinen Banner.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function fwg_has_consent_banner(): bool {
	return '' !== FWG_GA_ID;
}

function fwg_klaro_config(): array {
	$icon   = '<svg viewBox="0 0 24 24" fill="none" stroke="#005949" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 2a10 10 0 1 0 9.8 12 3.4 3.4 0 0 1-4.3-4.3A3.4 3.4 0 0 1 12.3 5.4 2 2 0 0 1 12 2z"/><circle cx="9.5" cy="10" r="1" fill="#005949" stroke="none"/><circle cx="14.5" cy="14" r="1" fill="#005949" stroke="none"/><circle cx="9.5" cy="15" r="1" fill="#005949" stroke="none"/></svg>';
	$notice = '<span class="fwg-cc-head">' . $icon . 'Diese Webseite verwendet Cookies</span>'
		. '<span class="fwg-cc-body">Wir verwenden Cookies und ähnliche Technologien, um die Nutzung unserer Website zu analysieren. Dabei können Daten an Google übertragen werden. Du entscheidest selbst, was geladen wird, und kannst deine Wahl jederzeit über „Cookie-Einstellungen“ im Footer ändern. Mehr dazu in der <a href="' . esc_url( home_url( '/datenschutz/' ) ) . '">Datenschutzerklärung</a>.</span>';

	return array(
		'version'                => 2,
		'elementID'              => 'klaro',
		'styling'                => array( 'theme' => array( 'light', 'top', 'wide' ) ),
		'noAutoLoad'             => false,
		'htmlTexts'              => true,
		'embedded'               => false,
		'groupByPurpose'         => true,
		'storageMethod'          => 'cookie',
		'cookieName'             => 'fwg_consent',
		'cookieExpiresAfterDays' => 180,
		'default'                => false,
		'mustConsent'            => false,
		'acceptAll'              => true,
		'hideDeclineAll'         => false,
		'hideLearnMore'          => false,
		'noticeAsModal'          => true,
		'lang'                   => 'de',
		'translations'           => array(
			'de' => array(
				'privacyPolicyUrl' => home_url( '/datenschutz/' ),
				'consentModal'     => array(
					'title'       => 'Datenschutz-Einstellungen',
					'description' => 'Hier entscheidest du, welche Dienste wir einbinden dürfen. Technisch notwendige Funktionen laufen immer. Statistik laden wir nur mit deiner Einwilligung.',
				),
				'consentNotice'    => array(
					'description' => $notice,
					'learnMore'   => 'Einstellungen',
				),
				'acceptAll'        => 'Alle akzeptieren',
				'acceptSelected'   => 'Auswahl speichern',
				'decline'          => 'Ablehnen',
				'ok'               => 'Alle akzeptieren',
				'close'            => 'Schließen',
				'save'             => 'Auswahl speichern',
				'poweredBy'        => '',
				'purposes'         => array(
					'functional' => 'Notwendig',
					'statistics' => 'Statistik',
				),
				'service'          => array(
					'disableAll' => array( 'title' => 'Alle Dienste an/aus', 'description' => 'Aktiviert oder deaktiviert alle Dienste auf einmal.' ),
					'required'   => array( 'title' => '(immer aktiv)', 'description' => 'Dieser Dienst ist technisch notwendig und kann nicht deaktiviert werden.' ),
				),
				'wordpress'        => array( 'title' => 'WordPress (technisch notwendig)', 'description' => 'Sicherheits-Cookies beim Login sowie das Speichern deiner Cookie-Auswahl. Ohne diese funktioniert die Seite nicht.' ),
				'googleanalytics'  => array( 'title' => 'Google Analytics', 'description' => 'Statistik zur Nutzung der Seite mit gekürzter IP-Adresse. Setzt Cookies und überträgt Daten an Google.' ),
			),
		),
		'services'               => array(
			array( 'name' => 'wordpress',       'title' => 'WordPress (technisch notwendig)', 'purposes' => array( 'functional' ), 'required' => true, 'default' => true ),
			array( 'name' => 'googleanalytics', 'title' => 'Google Analytics',                 'purposes' => array( 'statistics' ), 'default' => false, 'cookies' => array( array( '/^_ga.*/', '/', '.fair-way-golf.com' ), array( '/^_ga.*/', '/', '' ), array( '/^_gid$/', '/', '' ) ) ),
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
		$cver = file_exists( $dir . 'klaro-custom.css' ) ? (string) filemtime( $dir . 'klaro-custom.css' ) : $ver;
		wp_enqueue_style( 'fwg-klaro', $base . 'klaro.css', array( 'fwg-main' ), $ver );
		wp_enqueue_style( 'fwg-klaro-custom', $base . 'klaro-custom.css', array( 'fwg-klaro' ), $cver );
		// Markenfarbe für Klaro-eigene Elemente (Schalter usw.).
		wp_add_inline_style( 'fwg-klaro-custom', '.klaro{--green1:#005949;--green2:#013f34;}' );
		wp_enqueue_script( 'fwg-klaro', $base . 'klaro.js', array(), $ver, true );
		wp_add_inline_script( 'fwg-klaro', 'window.klaroConfig = ' . wp_json_encode( fwg_klaro_config(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . ';', 'before' );
		// Barrierefreier Name für den Klaro-Dialog (role=dialog hat sonst keinen Namen).
		wp_add_inline_script( 'fwg-klaro', "(function(){function n(){var d=document.getElementById('klaro-cookie-notice')||document.querySelector('.cookie-modal-notice[role=dialog],.cookie-notice[role=dialog]');if(!d)return false;if(!d.getAttribute('aria-label')){d.removeAttribute('aria-labelledby');d.setAttribute('aria-label','Cookie-Hinweis');}return true;}if(n())return;var m=new MutationObserver(function(){if(n())m.disconnect();});var s=function(){m.observe(document.body,{childList:true,subtree:true});};if(document.body){s();}else{document.addEventListener('DOMContentLoaded',s);}})();", 'after' );
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
