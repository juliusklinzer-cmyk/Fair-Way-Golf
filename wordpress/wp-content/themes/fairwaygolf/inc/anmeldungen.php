<?php
/**
 * Anmeldungen als eigener Inhaltstyp: Liste mit Filtern, Detailansicht,
 * CSV-Export und Rundmail an Gruppen. Alles bleibt in der eigenen WordPress-Datenbank.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'init',
	function () {
		register_post_type(
			'fwg_anmeldung',
			array(
				'labels'              => array(
					'name'          => 'Anmeldungen',
					'singular_name' => 'Anmeldung',
					'menu_name'     => 'Anmeldungen',
					'all_items'     => 'Alle Anmeldungen',
					'edit_item'     => 'Anmeldung ansehen',
					'search_items'  => 'Anmeldungen durchsuchen',
					'not_found'     => 'Noch keine Anmeldungen. Sobald jemand ein Formular abschickt, erscheint der Eintrag hier.',
				),
				'public'              => false,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'show_in_rest'        => false,
				'exclude_from_search' => true,
				'publicly_queryable'  => false,
				'menu_icon'           => 'dashicons-flag',
				'menu_position'       => 5,
				'supports'            => array( 'title' ),
				'capability_type'     => 'post',
				'capabilities'        => array( 'create_posts' => 'do_not_allow' ),
				'map_meta_cap'        => true,
			)
		);
	}
);

/** Speichert eine Anmeldung. Gibt die Post-ID oder 0 zurück. */
function fwg_save_anmeldung( array $data, string $quelle = '' ): int {
	$who   = trim( ( $data['vorname'] ?? '' ) . ' ' . ( $data['nachname'] ?? '' ) );
	$label = fwg_form_types()[ $data['typ'] ] ?? $data['typ'];
	$title = ( $who ? $who : ( $data['anlage'] ?? ( $data['platz'] ?? 'Anonym' ) ) ) . ' · ' . $label;
	// Suchbarer Inhalt (Admin-Suche greift auf Titel/Inhalt zu).
	$search = implode( ' ', array_filter( array( $data['email'] ?? '', $data['ort'] ?? '', $data['club'] ?? '', $data['anlage'] ?? '', $data['platz'] ?? '', implode( ' ', (array) ( $data['wunschplaetze'] ?? array() ) ) ) ) );
	$id     = wp_insert_post(
		array(
			'post_type'    => 'fwg_anmeldung',
			'post_status'  => 'publish',
			'post_title'   => $title,
			'post_content' => $search,
		),
		true
	);
	if ( is_wp_error( $id ) || ! $id ) {
		return 0;
	}
	$token = strtolower( wp_generate_password( 32, false ) );
	update_post_meta( $id, 'fwg_token', $token );
	update_post_meta( $id, 'fwg_quelle', $quelle );
	foreach ( array( 'typ', 'vorname', 'nachname', 'email', 'ort', 'club', 'kategorie', 'wunschplaetze', 'updates', 'anlage', 'telefon', 'kontaktweg', 'rolle', 'platz', 'nachricht' ) as $k ) {
		if ( isset( $data[ $k ] ) ) {
			update_post_meta( $id, 'fwg_' . $k, $data[ $k ] );
		}
	}
	update_post_meta( $id, 'fwg_bestaetigt', 0 );
	return (int) $id;
}

/** Daten einer Anmeldung als Array. */
function fwg_get_anmeldung( int $id ): array {
	$out = array();
	foreach ( array( 'typ', 'vorname', 'nachname', 'email', 'ort', 'club', 'kategorie', 'wunschplaetze', 'updates', 'bestaetigt', 'anlage', 'telefon', 'kontaktweg', 'rolle', 'platz', 'nachricht', 'quelle', 'bestaetigt_am', 'abgemeldet_am' ) as $k ) {
		$out[ $k ] = get_post_meta( $id, 'fwg_' . $k, true );
	}
	return $out;
}

/* ---------- Listenansicht ---------- */

add_filter(
	'manage_fwg_anmeldung_posts_columns',
	function () {
		return array(
			'cb'        => '<input type="checkbox">',
			'title'     => 'Name',
			'typ'       => 'Formular',
			'email'     => 'E-Mail',
			'ort'       => 'Ort / Anlage',
			'wunsch'    => 'Wunschplätze',
			'kategorie' => 'Modell',
			'updates'   => 'Updates',
			'date'      => 'Eingang',
		);
	}
);

add_action(
	'manage_fwg_anmeldung_posts_custom_column',
	function ( $col, $id ) {
		$d = fwg_get_anmeldung( (int) $id );
		switch ( $col ) {
			case 'typ':
				echo esc_html( fwg_form_types()[ $d['typ'] ] ?? $d['typ'] );
				break;
			case 'email':
				echo $d['email'] ? '<a href="mailto:' . esc_attr( $d['email'] ) . '">' . esc_html( $d['email'] ) . '</a>' : '·';
				break;
			case 'ort':
				echo esc_html( $d['anlage'] ? $d['anlage'] : ( $d['ort'] ? $d['ort'] : ( $d['platz'] ? $d['platz'] : '·' ) ) );
				break;
			case 'wunsch':
				echo esc_html( is_array( $d['wunschplaetze'] ) && $d['wunschplaetze'] ? implode( ', ', $d['wunschplaetze'] ) : ( $d['rolle'] ? ( fwg_rollen()[ $d['rolle'] ] ?? $d['rolle'] ) : '·' ) );
				break;
			case 'kategorie':
				echo esc_html( $d['kategorie'] ? ( fwg_kategorien()[ $d['kategorie'] ] ?? $d['kategorie'] ) : '·' );
				break;
			case 'updates':
				if ( 'voranmeldung' !== $d['typ'] ) {
					echo '·';
				} elseif ( ! $d['updates'] ) {
					echo 'nein';
				} else {
					echo $d['bestaetigt'] ? '<span style="color:#005949;font-weight:600">bestätigt</span>' : '<span style="color:#8a6d00">offen</span>';
				}
				break;
		}
	},
	10,
	2
);

/** Filter über der Liste: Formular und Update-Status. */
add_action(
	'restrict_manage_posts',
	function ( $type ) {
		if ( 'fwg_anmeldung' !== $type ) {
			return;
		}
		$cur_typ = isset( $_GET['fwg_typ'] ) ? sanitize_key( $_GET['fwg_typ'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$cur_upd = isset( $_GET['fwg_upd'] ) ? sanitize_key( $_GET['fwg_upd'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		echo '<select name="fwg_typ"><option value="">Alle Formulare</option>';
		foreach ( fwg_form_types() as $k => $label ) {
			echo '<option value="' . esc_attr( $k ) . '"' . selected( $cur_typ, $k, false ) . '>' . esc_html( $label ) . '</option>';
		}
		echo '</select> <select name="fwg_upd"><option value="">Updates: alle</option>';
		foreach ( array( 'bestaetigt' => 'Updates bestätigt', 'offen' => 'Updates offen', 'nein' => 'keine Updates' ) as $k => $label ) {
			echo '<option value="' . esc_attr( $k ) . '"' . selected( $cur_upd, $k, false ) . '>' . esc_html( $label ) . '</option>';
		}
		echo '</select>';
	}
);

add_action(
	'pre_get_posts',
	function ( $q ) {
		if ( ! is_admin() || ! $q->is_main_query() || 'fwg_anmeldung' !== $q->get( 'post_type' ) ) {
			return;
		}
		$meta = array();
		$typ  = isset( $_GET['fwg_typ'] ) ? sanitize_key( $_GET['fwg_typ'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$upd  = isset( $_GET['fwg_upd'] ) ? sanitize_key( $_GET['fwg_upd'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( $typ ) {
			$meta[] = array( 'key' => 'fwg_typ', 'value' => $typ );
		}
		if ( 'bestaetigt' === $upd ) {
			$meta[] = array( 'key' => 'fwg_bestaetigt', 'value' => 1 );
			$meta[] = array( 'key' => 'fwg_updates', 'value' => 1 );
		} elseif ( 'offen' === $upd ) {
			$meta[] = array( 'key' => 'fwg_updates', 'value' => 1 );
			$meta[] = array( 'key' => 'fwg_bestaetigt', 'value' => 0 );
		} elseif ( 'nein' === $upd ) {
			$meta[] = array( 'key' => 'fwg_updates', 'value' => 1, 'compare' => '!=' );
		}
		if ( $meta ) {
			$q->set( 'meta_query', $meta ); // phpcs:ignore WordPress.DB.SlowDBQuery
		}
	}
);

/* ---------- Detailansicht (nur lesen) ---------- */

add_action(
	'add_meta_boxes_fwg_anmeldung',
	function () {
		add_meta_box(
			'fwg_daten',
			'Angaben',
			function ( $post ) {
				$d = fwg_get_anmeldung( (int) $post->ID );
				echo '<style>.fwg-admin-table td{padding:6px 12px 6px 0;vertical-align:top}.fwg-admin-table td:first-child{color:#646970;white-space:nowrap}</style>';
				echo str_replace( '<table style="border-collapse:collapse;font-size:15px">', '<table class="fwg-admin-table">', fwg_data_table( $d ) );
				echo '<p style="margin-top:12px;color:#646970">Quelle: ' . esc_html( $d['quelle'] ? $d['quelle'] : '·' );
				if ( 'voranmeldung' === $d['typ'] ) {
					echo ' · Updates: ' . ( $d['updates'] ? ( $d['bestaetigt'] ? 'bestätigt am ' . esc_html( $d['bestaetigt_am'] ) : 'offen (noch nicht bestätigt)' ) : 'nicht gewünscht' );
					if ( $d['abgemeldet_am'] ) {
						echo ' · abgemeldet am ' . esc_html( $d['abgemeldet_am'] );
					}
				}
				echo '</p>';
			},
			'fwg_anmeldung',
			'normal',
			'high'
		);
	}
);

/* ---------- CSV-Export ---------- */

add_action(
	'admin_menu',
	function () {
		add_submenu_page( 'edit.php?post_type=fwg_anmeldung', 'Export', 'Export (CSV)', 'manage_options', 'fwg-export', 'fwg_export_page' );
		add_submenu_page( 'edit.php?post_type=fwg_anmeldung', 'Rundmail', 'Rundmail', 'manage_options', 'fwg-rundmail', 'fwg_rundmail_page' );
	}
);

function fwg_export_page(): void {
	$url = wp_nonce_url( admin_url( 'admin-post.php?action=fwg_export' ), 'fwg_export' );
	echo '<div class="wrap"><h1>Export als CSV</h1><p>Alle Anmeldungen als Tabelle (semikolongetrennt, UTF-8, öffnet direkt in Excel).</p>';
	echo '<p><a class="button button-primary" href="' . esc_url( $url ) . '">CSV herunterladen</a></p></div>';
}

add_action(
	'admin_post_fwg_export',
	function () {
		if ( ! current_user_can( 'manage_options' ) || ! check_admin_referer( 'fwg_export' ) ) {
			wp_die( 'Keine Berechtigung.' );
		}
		$ids = get_posts( array( 'post_type' => 'fwg_anmeldung', 'post_status' => 'any', 'posts_per_page' => -1, 'fields' => 'ids', 'orderby' => 'date', 'order' => 'DESC' ) );
		nocache_headers();
		header( 'Content-Type: text/csv; charset=UTF-8' );
		header( 'Content-Disposition: attachment; filename="fair-way-golf-anmeldungen-' . gmdate( 'Y-m-d' ) . '.csv"' );
		$out  = fopen( 'php://output', 'w' );
		$cols = array( 'Eingang', 'Formular', 'Vorname', 'Nachname', 'E-Mail', 'Ort', 'Aktueller Club', 'Modell', 'Wunschplätze', 'Updates', 'Bestätigt', 'Golfanlage', 'Telefon', 'Kontaktweg', 'Rolle', 'Lieblingsplatz', 'Nachricht', 'Quelle' );
		fwrite( $out, "\xEF\xBB\xBF" );
		fputcsv( $out, $cols, ';', '"', '' );
		// Schutz vor Formel-Injektion in Excel: Zellen, die mit = + - @ oder Steuerzeichen beginnen, bekommen ein Apostroph.
		$safe = static function ( $v ) {
			$v = (string) $v;
			return preg_match( '/^[=+\-@\t\r]/', $v ) ? "'" . $v : $v;
		};
		foreach ( $ids as $id ) {
			$d = fwg_get_anmeldung( (int) $id );
			fputcsv(
				$out,
				array_map( $safe, array(
					get_the_date( 'd.m.Y H:i', $id ), fwg_form_types()[ $d['typ'] ] ?? $d['typ'], $d['vorname'], $d['nachname'], $d['email'], $d['ort'], $d['club'],
					$d['kategorie'] ? ( fwg_kategorien()[ $d['kategorie'] ] ?? $d['kategorie'] ) : '', is_array( $d['wunschplaetze'] ) ? implode( ', ', $d['wunschplaetze'] ) : '',
					$d['updates'] ? 'ja' : 'nein', $d['bestaetigt'] ? 'ja' : 'nein', $d['anlage'], $d['telefon'], $d['kontaktweg'] ? ( fwg_kontaktwege()[ $d['kontaktweg'] ] ?? $d['kontaktweg'] ) : '',
					$d['rolle'] ? ( fwg_rollen()[ $d['rolle'] ] ?? $d['rolle'] ) : '', $d['platz'], $d['nachricht'], $d['quelle'],
				) ), ';', '\"', '' );
		}
		fclose( $out );
		exit;
	}
);

/* ---------- Rundmail (nur an bestätigte Update-Einwilligungen) ---------- */

/** Empfänger: ausschließlich Voranmeldungen mit gewünschten UND bestätigten Updates. */
function fwg_rundmail_empfaenger(): array {
	$meta = array(
		array( 'key' => 'fwg_typ', 'value' => 'voranmeldung' ),
		array( 'key' => 'fwg_updates', 'value' => 1 ),
		array( 'key' => 'fwg_bestaetigt', 'value' => 1 ),
	);
	$ids  = get_posts( array( 'post_type' => 'fwg_anmeldung', 'post_status' => 'any', 'posts_per_page' => -1, 'fields' => 'ids', 'meta_query' => $meta ) ); // phpcs:ignore WordPress.DB.SlowDBQuery
	$list = array();
	foreach ( $ids as $id ) {
		$email = get_post_meta( $id, 'fwg_email', true );
		if ( is_email( $email ) && ! isset( $list[ strtolower( $email ) ] ) ) {
			$list[ strtolower( $email ) ] = array( 'id' => (int) $id, 'email' => $email, 'vorname' => get_post_meta( $id, 'fwg_vorname', true ), 'token' => get_post_meta( $id, 'fwg_token', true ) );
		}
	}
	return array_values( $list );
}

/** Baut die Rundmail für einen Empfänger, immer mit Abmeldelink. */
function fwg_rundmail_body( string $text, array $r ): string {
	$body  = '<p>Hallo ' . esc_html( $r['vorname'] ? $r['vorname'] : 'Golfer' ) . ',</p><p>' . nl2br( esc_html( $text ) ) . '</p><p>Viele Grüße<br>Julius</p>';
	$body .= '<p style="color:#4a5651;font-size:13px;margin-top:24px">Du bekommst diese Mail, weil du dich bei Fair-Way-Golf vorangemeldet und Updates bestätigt hast. <a href="' . esc_url( add_query_arg( 'fwg_abmelden', $r['token'], home_url( '/voranmeldung/' ) ) ) . '" style="color:#005949">Keine Updates mehr</a></p>';
	return $body;
}

/** Versendet einen Stapel per Cron (Batches à 40, damit kein PHP-Timeout entsteht). */
add_action(
	'fwg_rundmail_batch',
	function ( $job_id ) {
		$job = get_option( 'fwg_rundmail_job_' . sanitize_key( $job_id ) );
		if ( ! $job || empty( $job['queue'] ) ) {
			return;
		}
		$key = 'fwg_rundmail_job_' . sanitize_key( $job_id );
		if ( function_exists( 'set_time_limit' ) ) {
			@set_time_limit( 0 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors
		}
		update_option( 'fwg_rundmail_last_batch', current_time( 'mysql' ), false );
		// Stapel à 25; nach jeder Mail speichern, damit ein Abbruch keine Mail doppelt schickt.
		for ( $i = 0; $i < 25 && $job['queue']; $i++ ) {
			$r = array_shift( $job['queue'] );
			if ( fwg_send_mail( $r['email'], $job['subject'], fwg_mail_wrap( $job['subject'], fwg_rundmail_body( $job['text'], $r ) ) ) ) {
				$job['sent']++;
			} else {
				$job['failed']++;
			}
			update_option( $key, $job, false );
		}
		if ( $job['queue'] ) {
			$next = wp_schedule_single_event( time() + 60, 'fwg_rundmail_batch', array( $job_id ), true );
			if ( true !== $next ) {
				$job['error'] = 'Nächster Stapel konnte nicht eingeplant werden.';
				update_option( $key, $job, false );
			}
		} else {
			$job['done'] = current_time( 'mysql' );
			update_option( $key, $job, false );
		}
	}
);

/** Verarbeitet das Rundmail-Formular und leitet danach um (kein doppelter Versand bei F5). */
add_action(
	'admin_init',
	function () {
		if ( 'POST' !== ( $_SERVER['REQUEST_METHOD'] ?? '' ) || ! isset( $_POST['fwg_rundmail_form'] ) ) {
			return;
		}
		check_admin_referer( 'fwg_rundmail' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'Keine Berechtigung.' );
		}
		$back    = admin_url( 'edit.php?post_type=fwg_anmeldung&page=fwg-rundmail' );
		$subject = sanitize_text_field( wp_unslash( $_POST['betreff'] ?? '' ) );
		$text    = sanitize_textarea_field( wp_unslash( $_POST['text'] ?? '' ) );
		$mode    = isset( $_POST['fwg_test'] ) ? 'test' : ( isset( $_POST['fwg_senden'] ) ? 'senden' : '' );
		$msg     = 'leer';
		if ( '' !== $subject && '' !== $text && 'test' === $mode ) {
			$me  = array( 'id' => 0, 'email' => wp_get_current_user()->user_email, 'vorname' => wp_get_current_user()->display_name, 'token' => 'test' );
			$msg = fwg_send_mail( $me['email'], '[Test] ' . $subject, fwg_mail_wrap( $subject, fwg_rundmail_body( $text, $me ) ) ) ? 'test-ok' : 'test-fehler';
		} elseif ( '' !== $subject && '' !== $text && 'senden' === $mode ) {
			$list = fwg_rundmail_empfaenger();
			if ( ! $list ) {
				$msg = 'keine';
			} else {
				$job_id = gmdate( 'Ymd-His' );
				$key    = 'fwg_rundmail_job_' . $job_id;
				update_option( $key, array( 'subject' => $subject, 'text' => $text, 'queue' => $list, 'total' => count( $list ), 'sent' => 0, 'failed' => 0, 'started' => current_time( 'mysql' ), 'done' => '', 'error' => '' ), false );
				if ( true === wp_schedule_single_event( time() + 5, 'fwg_rundmail_batch', array( $job_id ), true ) ) {
					$msg = 'geplant';
				} else {
					delete_option( $key );
					$msg = 'cron-fehler';
				}
			}
		}
		wp_safe_redirect( add_query_arg( 'fwg_msg', $msg, $back ) );
		exit;
	}
);

function fwg_rundmail_page(): void {
	$msg     = isset( $_GET['fwg_msg'] ) ? sanitize_key( $_GET['fwg_msg'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$notices = array(
		'leer'        => array( 'error', 'Betreff und Text dürfen nicht leer sein.' ),
		'test-ok'     => array( 'success', 'Testmail an ' . wp_get_current_user()->user_email . ' verschickt.' ),
		'test-fehler' => array( 'error', 'Testmail konnte nicht verschickt werden. Prüfe die SMTP-Einstellungen.' ),
		'keine'       => array( 'warning', 'Keine Empfänger mit bestätigter Update-Einwilligung.' ),
		'geplant'     => array( 'success', 'Rundmail eingeplant. Der Versand läuft im Hintergrund in Stapeln à 25 pro Minute; den Stand siehst du unten.' ),
		'cron-fehler' => array( 'error', 'Der Versand konnte nicht eingeplant werden (WP-Cron). Nichts wurde verschickt.' ),
	);
	$notice  = isset( $notices[ $msg ] ) ? '<div class="notice notice-' . $notices[ $msg ][0] . '"><p>' . esc_html( $notices[ $msg ][1] ) . '</p></div>' : '';
	$last    = (string) get_option( 'fwg_rundmail_last_batch', '' );
	$count   = count( fwg_rundmail_empfaenger() );
	echo '<div class="wrap"><h1>Rundmail</h1>' . $notice; // phpcs:ignore WordPress.Security.EscapeOutput
	if ( defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON ) {
		echo '<p class="description">Versand über den externen Cron-Aufruf von wp-cron.php. Letzter Versandlauf: ' . esc_html( $last ? $last : 'noch keiner' ) . '. Bleibt ein Lauf auf „läuft“ stehen, den Cron-Job (alle 15 Minuten) prüfen.</p>';
	}
	echo '<p>Schickt eine Info-Mail an alle Voranmeldungen, die Updates gewünscht und per Klick bestätigt haben. Jede Mail enthält einen Abmeldelink. Andere Gruppen (Golfanlagen, Unterstützer, Lieblingsplatz) bekommen keine Rundmails; die erreichst du persönlich per E-Mail.</p>';
	echo '<form method="post"><input type="hidden" name="fwg_rundmail_form" value="1">';
	wp_nonce_field( 'fwg_rundmail' );
	echo '<table class="form-table"><tr><th>Empfänger</th><td><strong>' . (int) $count . '</strong> bestätigte Update-Einwilligungen</td></tr>';
	echo '<tr><th><label for="fwg-betreff">Betreff</label></th><td><input id="fwg-betreff" name="betreff" type="text" class="regular-text" required></td></tr>';
	echo '<tr><th><label for="fwg-text">Text</label></th><td><textarea id="fwg-text" name="text" rows="10" class="large-text" required></textarea><p class="description">Beginnt automatisch mit „Hallo Vorname,“ und endet mit „Viele Grüße, Julius“. Reiner Text, Absätze per Leerzeile.</p></td></tr></table>';
	echo '<p><button class="button" name="fwg_test" value="1">Testmail an mich</button> <button class="button button-primary" name="fwg_senden" value="1" onclick="return confirm(\'Rundmail wirklich an alle ' . (int) $count . ' Empfänger schicken?\')">Rundmail senden</button></p>';
	echo '</form>';
	// Letzte Versandläufe
	global $wpdb;
	$jobs = $wpdb->get_col( "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE 'fwg_rundmail_job_%' ORDER BY option_name DESC LIMIT 5" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
	if ( $jobs ) {
		echo '<h2>Letzte Versandläufe</h2><table class="widefat striped" style="max-width:720px"><thead><tr><th>Gestartet</th><th>Betreff</th><th>Gesendet</th><th>Fehler</th><th>Status</th></tr></thead><tbody>';
		foreach ( $jobs as $name ) {
			$j = get_option( $name );
			if ( ! $j ) {
				continue;
			}
			echo '<tr><td>' . esc_html( $j['started'] ) . '</td><td>' . esc_html( $j['subject'] ) . '</td><td>' . (int) $j['sent'] . ' / ' . (int) $j['total'] . '</td><td>' . (int) $j['failed'] . '</td><td>' . ( $j['done'] ? 'fertig' : 'läuft' ) . '</td></tr>';
		}
		echo '</tbody></table>';
	}
	echo '</div>';
}
