<?php
/**
 * Formulare: Voranmeldung, Golfanlage, Unterstützen, Lieblingsplatz.
 * Ein Handler für Ajax (fetch) und für den Nicht-JS-Weg (admin-post.php).
 * Schutz: Nonce, Honeypot, Zeitfalle, Rate-Limit je Verbindung.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function fwg_form_types(): array {
	return array(
		'voranmeldung'   => 'Voranmeldung',
		'golfanlage'     => 'Golfanlage',
		'unterstuetzen'  => 'Unterstützen',
		'lieblingsplatz' => 'Lieblingsplatz',
	);
}

function fwg_kategorien(): array {
	return array( 'practice' => 'Practice', 's' => 'S', 'm' => 'M', 'l' => 'L', 'xl' => 'XL', 'unsicher' => 'Weiß ich noch nicht' );
}

function fwg_rollen(): array {
	return array(
		'gruendungsmitglied' => 'Gründungsmitglied',
		'investor'           => 'Investor',
		'partner'            => 'Partner (Technik, Marketing, Netzwerk)',
		'sonstiges'          => 'Etwas anderes',
	);
}

function fwg_kontaktwege(): array {
	return array( 'email' => 'E-Mail', 'telefon' => 'Telefon', 'video' => 'Videogespräch', 'besuch' => 'Besuch vor Ort' );
}

/** Anonymer Schlüssel je Verbindung (IP wird nur gehasht verwendet). */
function fwg_client_key(): string {
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? (string) $_SERVER['REMOTE_ADDR'] : 'unknown'; // phpcs:ignore
	return substr( hash( 'sha256', $ip . wp_salt( 'nonce' ) ), 0, 20 );
}

function fwg_rate_limited( int $max = 8, bool $count_now = true ): bool {
	$key    = 'fwg_rl_' . fwg_client_key();
	$count  = (int) get_transient( $key );
	$global = (int) get_transient( 'fwg_rl_global' );
	if ( $count >= $max || $global >= 60 ) {
		return true;
	}
	if ( $count_now ) {
		set_transient( $key, $count + 1, HOUR_IN_SECONDS );
		set_transient( 'fwg_rl_global', $global + 1, HOUR_IN_SECONDS );
	}
	return false;
}

/** Signatur für den Zeitstempel im Formular (verhindert gefälschte oder fehlende ts-Werte). */
function fwg_ts_signature( int $ts ): string {
	return substr( hash_hmac( 'sha256', (string) $ts, wp_salt( 'nonce' ) ), 0, 20 );
}

/** Komma- oder zeilengetrennte Liste in bereinigte Einträge (max 10 à 80 Zeichen). */
function fwg_split_list( $raw ): array {
	$raw   = sanitize_textarea_field( wp_unslash( (string) $raw ) );
	$parts = preg_split( '/[,\n;]+/', $raw );
	$out   = array();
	foreach ( (array) $parts as $p ) {
		$p = trim( $p );
		if ( '' !== $p ) {
			$out[] = mb_substr( $p, 0, 80 );
		}
		if ( count( $out ) >= 10 ) {
			break;
		}
	}
	return array_values( array_unique( $out ) );
}

/**
 * Verarbeitet ein abgeschicktes Formular.
 *
 * @return array{ok:bool,message:string,errors:array<string,string>,id?:int}
 */
function fwg_process_form( array $post ): array {
	$type = sanitize_key( $post['form'] ?? '' );
	if ( ! isset( fwg_form_types()[ $type ] ) ) {
		return array( 'ok' => false, 'message' => 'Unbekanntes Formular.', 'errors' => array() );
	}
	// Honeypot: Bots füllen das versteckte Feld. Still annehmen, nichts speichern.
	if ( ! empty( $post['website'] ) ) {
		return array( 'ok' => true, 'message' => 'Danke!', 'errors' => array() );
	}
	$ts  = (int) ( $post['ts'] ?? 0 );
	$sig = sanitize_text_field( wp_unslash( (string) ( $post['sig'] ?? '' ) ) );
	if ( ! $ts || ! hash_equals( fwg_ts_signature( $ts ), $sig ) || ( time() - $ts ) < 3 || ( time() - $ts ) > DAY_IN_SECONDS ) {
		return array( 'ok' => false, 'message' => 'Das Formular war zu lange offen oder wurde zu schnell abgeschickt. Bitte lade die Seite neu und versuch es noch einmal.', 'errors' => array() );
	}
	if ( fwg_rate_limited( 8, false ) ) {
		return array( 'ok' => false, 'message' => 'Von dieser Verbindung kamen gerade viele Anfragen. Bitte versuch es in einer Stunde noch einmal.', 'errors' => array() );
	}

	$t = static function ( string $k, int $max = 120 ) use ( $post ): string {
		return mb_substr( trim( sanitize_text_field( wp_unslash( (string) ( $post[ $k ] ?? '' ) ) ) ), 0, $max );
	};
	$email_raw = trim( wp_unslash( (string) ( $post['email'] ?? '' ) ) );
	$email     = mb_substr( sanitize_email( $email_raw ), 0, 120 );
	if ( '' !== $email_raw && strtolower( $email_raw ) !== strtolower( $email ) ) {
		$email = '';
	}
	$nachricht = mb_substr( sanitize_textarea_field( wp_unslash( (string) ( $post['nachricht'] ?? '' ) ) ), 0, 2000 );
	$errors    = array();
	$data      = array( 'typ' => $type, 'email' => $email, 'nachricht' => $nachricht );

	switch ( $type ) {
		case 'voranmeldung':
			$data['vorname']       = $t( 'vorname' );
			$data['nachname']      = $t( 'nachname' );
			$data['ort']           = $t( 'ort' );
			$data['club']          = $t( 'club' );
			$data['kategorie']     = isset( fwg_kategorien()[ $t( 'kategorie', 20 ) ] ) ? $t( 'kategorie', 20 ) : 'unsicher';
			$data['wunschplaetze'] = fwg_split_list( $post['wunschplaetze'] ?? '' );
			$data['updates']       = ! empty( $post['updates'] ) ? 1 : 0;
			if ( '' === $data['vorname'] ) {
				$errors['vorname'] = 'Sag uns bitte deinen Vornamen.';
			}
			if ( ! is_email( $email ) ) {
				$errors['email'] = 'Diese E-Mail-Adresse sieht nicht vollständig aus.';
			}
			if ( ! $data['wunschplaetze'] ) {
				$errors['wunschplaetze'] = 'Nenn uns mindestens einen Platz, den du spielen willst.';
			}
			if ( empty( $post['datenschutz'] ) ) {
				$errors['datenschutz'] = 'Bitte bestätige den Hinweis zum Datenschutz.';
			}
			break;

		case 'golfanlage':
			$data['anlage']     = $t( 'anlage' );
			$data['vorname']    = $t( 'name' );
			$data['telefon']    = $t( 'telefon', 40 );
			$data['kontaktweg'] = isset( fwg_kontaktwege()[ $t( 'kontaktweg', 20 ) ] ) ? $t( 'kontaktweg', 20 ) : 'email';
			if ( '' === $data['anlage'] ) {
				$errors['anlage'] = 'Wie heißt deine Golfanlage?';
			}
			if ( '' === $data['vorname'] ) {
				$errors['name'] = 'Sag uns bitte, wen wir ansprechen dürfen.';
			}
			if ( ! is_email( $email ) ) {
				$errors['email'] = 'Diese E-Mail-Adresse sieht nicht vollständig aus.';
			}
			if ( empty( $post['datenschutz'] ) ) {
				$errors['datenschutz'] = 'Bitte bestätige den Hinweis zum Datenschutz.';
			}
			break;

		case 'unterstuetzen':
			$data['vorname'] = $t( 'name' );
			$data['rolle']   = isset( fwg_rollen()[ $t( 'rolle', 30 ) ] ) ? $t( 'rolle', 30 ) : '';
			if ( '' === $data['vorname'] ) {
				$errors['name'] = 'Sag uns bitte deinen Namen.';
			}
			if ( ! is_email( $email ) ) {
				$errors['email'] = 'Diese E-Mail-Adresse sieht nicht vollständig aus.';
			}
			if ( '' === $data['rolle'] ) {
				$errors['rolle'] = 'Wähle aus, wie du unterstützen möchtest.';
			}
			if ( empty( $post['datenschutz'] ) ) {
				$errors['datenschutz'] = 'Bitte bestätige den Hinweis zum Datenschutz.';
			}
			break;

		case 'lieblingsplatz':
			$data['platz'] = $t( 'platz', 80 );
			if ( '' === $data['platz'] ) {
				$errors['platz'] = 'Welcher Platz ist es?';
			}
			if ( '' !== $email && ! is_email( $email ) ) {
				$errors['email'] = 'Diese E-Mail-Adresse sieht nicht vollständig aus.';
			}
			break;
	}

	if ( $errors ) {
		return array( 'ok' => false, 'message' => 'Bitte schau dir die markierten Felder noch einmal an.', 'errors' => $errors );
	}

	fwg_rate_limited( 8, true );
	$id = fwg_save_anmeldung( $data, sanitize_key( $post['quelle'] ?? '' ) );
	if ( ! $id ) {
		return array( 'ok' => false, 'message' => 'Speichern hat nicht geklappt. Schreib uns bitte direkt an ' . FWG_CONTACT_EMAIL . '.', 'errors' => array() );
	}
	fwg_mail_team( $id, $data );
	$doi = false;
	if ( 'voranmeldung' === $type && $data['updates'] ) {
		$doi = fwg_mail_doi( $id, $data );
	} elseif ( is_email( $email ) && 'lieblingsplatz' !== $type ) {
		fwg_mail_receipt( $id, $data );
	}

	$name = isset( $data['vorname'] ) && '' !== $data['vorname'] ? ', ' . $data['vorname'] : '';
	switch ( $type ) {
		case 'voranmeldung':
			$message = $doi
				? 'Danke' . $name . '. Wir haben dir eine E-Mail geschickt: Bestätige darin kurz, dass wir dich auf dem Laufenden halten dürfen.'
				: 'Danke' . $name . '. Wir melden uns, sobald es losgeht.';
			break;
		case 'golfanlage':
			$message = 'Danke' . $name . '. Julius meldet sich persönlich, in der Regel innerhalb weniger Tage.';
			break;
		case 'unterstuetzen':
			$message = 'Danke' . $name . '. Julius meldet sich persönlich, in der Regel innerhalb weniger Tage.';
			break;
		default:
			$message = 'Notiert. Danke dir!';
	}
	return array( 'ok' => true, 'message' => $message, 'errors' => array(), 'id' => $id );
}

/** Ajax-Weg. */
function fwg_ajax_form(): void {
	if ( ! check_ajax_referer( 'fwg_form', 'nonce', false ) ) {
		wp_send_json( array( 'ok' => false, 'message' => 'Die Seite war zu lange offen. Bitte lade sie neu und versuch es noch einmal.', 'errors' => array() ), 403 );
	}
	wp_send_json( fwg_process_form( $_POST ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
}
add_action( 'wp_ajax_fwg_form', 'fwg_ajax_form' );
add_action( 'wp_ajax_nopriv_fwg_form', 'fwg_ajax_form' );

/** Nicht-JS-Weg über admin-post.php: verarbeiten und zurück zur Seite. */
function fwg_post_form(): void {
	$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
	$ref   = wp_get_referer() ? wp_get_referer() : home_url( '/' );
	$ref   = remove_query_arg( array( 'fwg', 'fwg_form' ), $ref );
	$ref   = strtok( $ref, '#' );
	if ( ! wp_verify_nonce( $nonce, 'fwg_form' ) ) {
		$result = array( 'ok' => false, 'message' => 'Die Seite war zu lange offen. Bitte versuch es noch einmal.' );
	} else {
		$result = fwg_process_form( $_POST );
	}
	$code = $result['ok'] ? 'ok' : ( ! empty( $result['errors'] ) ? 'felder' : 'fehler' );
	$url  = add_query_arg(
		array(
			'fwg'      => $code,
			'fwg_form' => sanitize_key( $_POST['form'] ?? '' ),
		),
		$ref
	) . '#formular';
	wp_safe_redirect( $url );
	exit;
}
add_action( 'admin_post_fwg_form', 'fwg_post_form' );
add_action( 'admin_post_nopriv_fwg_form', 'fwg_post_form' );

/** Status-Hinweis nach dem Nicht-JS-Weg (Template ruft das im Formularbereich auf). */
function fwg_form_notice( string $type ): string {
	if ( ! isset( $_GET['fwg'], $_GET['fwg_form'] ) || sanitize_key( $_GET['fwg_form'] ) !== $type ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return '';
	}
	$code  = sanitize_key( $_GET['fwg'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$ok    = 'ok' === $code;
	$texts = array(
		'ok'     => 'Danke, deine Angaben sind angekommen. Julius meldet sich persönlich, in der Regel innerhalb weniger Tage.',
		'felder' => 'Bitte prüfe die Pflichtfelder und schick das Formular noch einmal ab.',
		'fehler' => 'Das hat nicht geklappt. Bitte versuch es noch einmal oder schreib uns an ' . FWG_CONTACT_EMAIL . '.',
	);
	$msg   = $texts[ $code ] ?? $texts['fehler'];
	return '<div class="fwg-notice fwg-notice--' . ( $ok ? 'ok' : 'fehler' ) . '" role="status">' . fwg_icon( $ok ? 'check-circle' : 'warning-circle' ) . '<p>' . esc_html( $msg ) . '</p></div>';
}

/* ---------- Mails ---------- */

function fwg_mail_wrap( string $title, string $body_html ): string {
	$logo = get_template_directory_uri() . '/assets/img/apple-touch-icon.png';
	return '<!doctype html><html lang="de"><body style="margin:0;background:#f3f6f4;font-family:Arial,Helvetica,sans-serif;color:#141917">'
		. '<div style="max-width:600px;margin:0 auto;padding:24px 16px">'
		. '<div style="background:#005949;color:#fff;border-radius:16px 16px 0 0;padding:22px 24px;font-size:20px;font-weight:700">'
		. '<img src="' . esc_url( $logo ) . '" alt="" width="28" height="28" style="vertical-align:middle;margin-right:10px;border-radius:6px"> ' . esc_html( $title ) . '</div>'
		. '<div style="background:#fff;border-radius:0 0 16px 16px;padding:24px;font-size:16px;line-height:1.6">' . $body_html . '</div>'
		. '<p style="color:#4a5651;font-size:13px;line-height:1.5;padding:16px 4px 0">Fair-Way-Golf · <a href="' . esc_url( home_url( '/' ) ) . '" style="color:#005949">fair-way-golf.com</a> · <a href="' . esc_url( home_url( '/datenschutz/' ) ) . '" style="color:#005949">Datenschutz</a></p>'
		. '</div></body></html>';
}

function fwg_send_mail( string $to, string $subject, string $body_html, array $extra_headers = array() ): bool {
	$headers = array_merge( array( 'Content-Type: text/html; charset=UTF-8' ), $extra_headers );
	return wp_mail( $to, $subject, $body_html, $headers );
}

/** Datenübersicht als Tabelle (für Team-Mail und Admin). */
function fwg_data_table( array $data ): string {
	$labels = array(
		'typ' => 'Formular', 'vorname' => 'Vorname', 'nachname' => 'Nachname', 'email' => 'E-Mail', 'ort' => 'Ort',
		'club' => 'Aktueller Club', 'kategorie' => 'Modell', 'wunschplaetze' => 'Wunschplätze', 'updates' => 'Updates gewünscht',
		'anlage' => 'Golfanlage', 'telefon' => 'Telefon', 'kontaktweg' => 'Kontaktweg', 'rolle' => 'Rolle', 'platz' => 'Lieblingsplatz', 'nachricht' => 'Nachricht',
	);
	$rows = '';
	foreach ( $labels as $k => $label ) {
		if ( ! isset( $data[ $k ] ) || '' === $data[ $k ] || array() === $data[ $k ] ) {
			continue;
		}
		$v = $data[ $k ];
		if ( 'typ' === $k ) {
			$v = fwg_form_types()[ $v ] ?? $v;
		} elseif ( 'kategorie' === $k ) {
			$v = fwg_kategorien()[ $v ] ?? $v;
		} elseif ( 'rolle' === $k ) {
			$v = fwg_rollen()[ $v ] ?? $v;
		} elseif ( 'kontaktweg' === $k ) {
			$v = fwg_kontaktwege()[ $v ] ?? $v;
		} elseif ( 'updates' === $k ) {
			$v = $v ? 'ja' : 'nein';
		} elseif ( is_array( $v ) ) {
			$v = implode( ', ', $v );
		}
		$rows .= '<tr><td style="padding:6px 10px 6px 0;color:#4a5651;vertical-align:top;white-space:nowrap">' . esc_html( $label ) . '</td><td style="padding:6px 0;vertical-align:top">' . nl2br( esc_html( (string) $v ) ) . '</td></tr>';
	}
	return '<table style="border-collapse:collapse;font-size:15px">' . $rows . '</table>';
}

function fwg_mail_team( int $id, array $data ): void {
	$type    = fwg_form_types()[ $data['typ'] ] ?? $data['typ'];
	$who     = trim( ( $data['vorname'] ?? '' ) . ' ' . ( $data['nachname'] ?? '' ) );
	$subject = '[Fair-Way-Golf] ' . $type . ( $who ? ': ' . $who : '' ) . ( ! empty( $data['anlage'] ) ? ' (' . $data['anlage'] . ')' : '' );
	$body    = fwg_data_table( $data )
		. '<p style="margin-top:20px"><a href="' . esc_url( admin_url( 'post.php?post=' . $id . '&action=edit' ) ) . '" style="color:#005949">Im Backend ansehen</a></p>';
	$headers = array();
	if ( ! empty( $data['email'] ) && is_email( $data['email'] ) ) {
		$headers[] = 'Reply-To: ' . $data['email'];
	}
	fwg_send_mail( FWG_CONTACT_EMAIL, $subject, fwg_mail_wrap( $type, $body ), $headers );
}

function fwg_mail_doi( int $id, array $data ): bool {
	$token = get_post_meta( $id, 'fwg_token', true );
	$link  = add_query_arg( 'fwg_bestaetigen', $token, home_url( '/voranmeldung/' ) );
	$body  = '<p>Hallo ' . esc_html( $data['vorname'] ) . ',</p>'
		. '<p>danke für deine Voranmeldung bei Fair-Way-Golf. Damit wir dich über den zweiten Anlauf und deine Wunschplätze informieren dürfen, bestätige bitte kurz deine E-Mail-Adresse:</p>'
		. '<p style="margin:24px 0"><a href="' . esc_url( $link ) . '" style="display:inline-block;background:#005949;color:#fff;text-decoration:none;font-weight:700;padding:14px 24px;border-radius:999px">Ja, haltet mich auf dem Laufenden</a></p>'
		. '<p>Falls der Button nicht funktioniert: <a href="' . esc_url( $link ) . '" style="color:#005949">' . esc_html( $link ) . '</a></p>'
		. '<p>Deine Angaben:</p>' . fwg_data_table( array_diff_key( $data, array( 'typ' => 1, 'updates' => 1, 'nachricht' => 1 ) ) )
		. '<p style="color:#4a5651;font-size:14px">Wenn du das nicht warst, ignoriere diese E-Mail einfach. Ohne Bestätigung schicken wir dir keine Updates.</p>'
		. '<p>Viele Grüße<br>Julius</p>';
	return fwg_send_mail( $data['email'], 'Bitte bestätige deine E-Mail-Adresse für Updates von Fair-Way-Golf', fwg_mail_wrap( 'Nur noch ein Klick für Updates', $body ) );
}

function fwg_mail_receipt( int $id, array $data ): void {
	$name = $data['vorname'] ?? '';
	if ( 'voranmeldung' === $data['typ'] ) {
		$subject = 'Deine Voranmeldung bei Fair-Way-Golf';
		$body    = '<p>Hallo ' . esc_html( $name ) . ',</p><p>danke für deine Voranmeldung. Wir melden uns, sobald es losgeht. Updates dazwischen bekommst du nur, wenn du sie ausdrücklich möchtest.</p>';
	} else {
		$subject = 'Danke für deine Anfrage bei Fair-Way-Golf';
		$body    = '<p>Hallo ' . esc_html( $name ) . ',</p><p>danke für deine Anfrage. Julius meldet sich persönlich, in der Regel innerhalb weniger Tage.</p>';
		if ( 'golfanlage' === $data['typ'] && defined( 'FWG_MEETING_URL' ) && FWG_MEETING_URL ) {
			$body .= '<p>Wenn es schneller gehen soll: <a href="' . esc_url( FWG_MEETING_URL ) . '" style="color:#005949">Termin mit Julius buchen</a>.</p>';
		}
	}
	$body .= '<p>Deine Angaben:</p>' . fwg_data_table( array_diff_key( $data, array( 'typ' => 1, 'nachricht' => 1 ) ) ) . '<p>Viele Grüße<br>Julius</p>';
	fwg_send_mail( $data['email'], $subject, fwg_mail_wrap( 'Danke', $body ) );
}

/* ---------- Bestätigen / Abmelden per Link (Zwischenseite mit Button, kein Auto-Klick) ---------- */

/** Findet die Anmeldung zu einem Token, oder 0. */
function fwg_find_by_token( string $token ): int {
	if ( ! preg_match( '/^[a-z0-9]{32}$/', $token ) ) {
		return 0;
	}
	$found = get_posts(
		array(
			'post_type'      => 'fwg_anmeldung',
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'meta_key'       => 'fwg_token', // phpcs:ignore WordPress.DB.SlowDBQuery
			'meta_value'     => $token, // phpcs:ignore WordPress.DB.SlowDBQuery
			'fields'         => 'ids',
		)
	);
	return $found ? (int) $found[0] : 0;
}

/** Liefert Aktion und Token aus der URL für die Zwischenseite, oder null. */
function fwg_token_page(): ?array {
	foreach ( array( 'fwg_bestaetigen' => 'bestaetigen', 'fwg_abmelden' => 'abmelden' ) as $param => $action ) {
		if ( isset( $_GET[ $param ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$token = sanitize_text_field( wp_unslash( $_GET[ $param ] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return array( 'action' => fwg_find_by_token( $token ) ? $action : 'ungueltig', 'token' => $token );
		}
	}
	return null;
}

/** POST vom Bestätigungs-Button: Einwilligung setzen bzw. entziehen. */
add_action(
	'template_redirect',
	function () {
		if ( 'POST' !== ( $_SERVER['REQUEST_METHOD'] ?? '' ) || empty( $_POST['fwg_token_action'] ) ) {
			return;
		}
		$action = sanitize_key( $_POST['fwg_token_action'] );
		$token  = sanitize_text_field( wp_unslash( $_POST['fwg_token'] ?? '' ) );
		$target = home_url( '/voranmeldung/' );
		$id     = fwg_find_by_token( $token );
		if ( ! $id || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ?? '' ) ), 'fwg_token_' . $token ) ) {
			wp_safe_redirect( add_query_arg( 'fwg', 'link-ungueltig', $target ) . '#formular' );
			exit;
		}
		if ( 'bestaetigen' === $action ) {
			update_post_meta( $id, 'fwg_updates', 1 );
			update_post_meta( $id, 'fwg_bestaetigt', 1 );
			update_post_meta( $id, 'fwg_bestaetigt_am', current_time( 'mysql' ) );
			wp_safe_redirect( add_query_arg( 'fwg', 'bestaetigt', $target ) . '#formular' );
		} else {
			update_post_meta( $id, 'fwg_updates', 0 );
			update_post_meta( $id, 'fwg_bestaetigt', 0 );
			update_post_meta( $id, 'fwg_abgemeldet_am', current_time( 'mysql' ) );
			wp_safe_redirect( add_query_arg( 'fwg', 'abgemeldet', $target ) . '#formular' );
		}
		exit;
	}
);
