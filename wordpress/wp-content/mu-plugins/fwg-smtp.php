<?php
/**
 * Plugin Name: Fair-Way-Golf SMTP
 * Description: Leitet wp_mail an den SMTP-Host aus den FWG_SMTP_*-Konstanten (wp-config.php).
 *              Lokal: MailHog ohne Auth (FWG_SMTP_HOST=mailhog, Port 1025).
 *              Produktion (Brevo-Relay mit Auth + TLS):
 *              define('FWG_SMTP_HOST', 'smtp-relay.brevo.com');
 *              define('FWG_SMTP_PORT', 587);
 *              define('FWG_SMTP_USER', '<brevo-login>');
 *              define('FWG_SMTP_PASS', '<brevo-smtp-key>');
 *              define('FWG_SMTP_SECURE', 'tls');
 *              define('FWG_MAIL_FROM', 'hallo@fair-way-golf.com');
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'FWG_MAIL_FROM' ) ) {
	define( 'FWG_MAIL_FROM', 'hallo@fair-way-golf.com' );
}

add_action(
	'wp_mail_failed',
	function ( $error ) {
		error_log( 'FWG wp_mail_failed: ' . $error->get_error_message() ); // phpcs:ignore
	}
);

add_filter( 'wp_mail_from', fn( $from ) => is_email( $from ) && strpos( $from, 'wordpress@' ) !== 0 ? $from : FWG_MAIL_FROM );
add_filter( 'wp_mail_from_name', fn( $name ) => 'WordPress' === $name ? 'Fair-Way-Golf' : $name );

add_action(
	'phpmailer_init',
	function ( $phpmailer ) {
		if ( ! defined( 'FWG_SMTP_HOST' ) || ! FWG_SMTP_HOST ) {
			return;
		}
		$phpmailer->isSMTP();
		$phpmailer->Host    = FWG_SMTP_HOST;
		$phpmailer->Port    = defined( 'FWG_SMTP_PORT' ) ? (int) FWG_SMTP_PORT : 587;
		$phpmailer->CharSet = 'UTF-8';
		if ( defined( 'FWG_SMTP_USER' ) && FWG_SMTP_USER ) {
			$phpmailer->SMTPAuth   = true;
			$phpmailer->Username   = FWG_SMTP_USER;
			$phpmailer->Password   = defined( 'FWG_SMTP_PASS' ) ? FWG_SMTP_PASS : '';
			$phpmailer->SMTPSecure = defined( 'FWG_SMTP_SECURE' ) ? FWG_SMTP_SECURE : 'tls';
		} else {
			$phpmailer->SMTPAuth   = false;
			$phpmailer->SMTPSecure = '';
			$phpmailer->SMTPAutoTLS = false;
		}
		if ( empty( $phpmailer->From ) || strpos( $phpmailer->From, 'wordpress@' ) === 0 ) {
			$phpmailer->setFrom( FWG_MAIL_FROM, 'Fair-Way-Golf' );
		}
	}
);
