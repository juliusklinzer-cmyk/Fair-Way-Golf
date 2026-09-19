<?php
/**
 * Einmal-Installer für fair-way-golf.com auf dem Hetzner-Webhosting (Ersatz für Duplicator).
 * Wird von tools/deploy-prep.sh mit einem Token versehen nach _build/deploy/_fwg_install.php kopiert,
 * zusammen mit fwg-site.zip, fwg-prod.sql und der ausgefüllten wp-config.php in den leeren
 * Zielordner geladen und über HTTPS aufgerufen:
 *
 *   ?t=TOKEN&step=check     Umgebung prüfen (PHP, Erweiterungen, Dateien, DB-Verbindung, fwg_-Tabellen = 0)
 *   ?t=TOKEN&step=clear     alte Seite löschen: alles in diesem Ordner außer den Installationsdateien
 *   ?t=TOKEN&step=unzip     fwg-site.zip in den Ordner entpacken
 *   ?t=TOKEN&step=db        fwg-prod.sql importieren (Tabellen mit Präfix aus wp-config-new.php)
 *   ?t=TOKEN&step=activate  wp-config-new.php nach wp-config.php umbenennen (ab hier ist die Seite live)
 *   ?t=TOKEN&step=cleanup   Zip, SQL und diesen Installer löschen
 *
 * Die neue wp-config.php wird als wp-config-new.php hochgeladen, damit die alte Seite bis zum
 * Schritt „activate“ nicht mit fremden Einstellungen läuft. Arbeitet ausschließlich in seinem eigenen
 * Ordner (realpath-Prüfung), nie darüber. Kein Login, keine Formulare: Schutz nur über das lange Token.
 */

$token = '__TOKEN__';
if ( ! isset( $_GET['t'] ) || ! is_string( $_GET['t'] ) || ! hash_equals( $token, $_GET['t'] ) ) {
	http_response_code( 403 );
	exit( 'Forbidden' );
}

header( 'Content-Type: text/plain; charset=utf-8' );
header( 'X-Robots-Tag: noindex' );
set_time_limit( 0 );
ini_set( 'memory_limit', '512M' );
ini_set( 'display_errors', '1' );
error_reporting( E_ALL );

$dir  = realpath( __DIR__ );
$step = isset( $_GET['step'] ) ? preg_replace( '/[^a-z]/', '', (string) $_GET['step'] ) : '';
$zip  = $dir . '/fwg-site.zip';
$sql  = $dir . '/fwg-prod.sql';
$cfg  = file_exists( $dir . '/wp-config-new.php' ) ? $dir . '/wp-config-new.php' : $dir . '/wp-config.php';
$keep = array( basename( __FILE__ ), 'fwg-site.zip', 'fwg-prod.sql', 'wp-config-new.php' );

/** Löscht einen Pfad rekursiv, aber nur unterhalb von $dir; Symlinks werden entfernt, nicht verfolgt. */
function fwg_rm( string $path, string $dir, array &$stat ): void {
	if ( ! str_starts_with( $path, $dir . '/' ) ) {
		$stat['skipped']++;
		return;
	}
	if ( is_link( $path ) || is_file( $path ) ) {
		unlink( $path ) ? $stat['files']++ : $stat['failed']++;
		return;
	}
	if ( is_dir( $path ) ) {
		foreach ( scandir( $path ) as $e ) {
			if ( '.' !== $e && '..' !== $e ) {
				fwg_rm( $path . '/' . $e, $dir, $stat );
			}
		}
		rmdir( $path ) ? $stat['dirs']++ : $stat['failed']++;
	}
}

/** Liest DB-Konstanten aus wp-config.php, ohne WordPress zu laden. */
function fwg_db_config( string $cfg ): array {
	$src = file_get_contents( $cfg );
	$out = array();
	foreach ( array( 'DB_NAME', 'DB_USER', 'DB_PASSWORD', 'DB_HOST' ) as $k ) {
		if ( ! preg_match( "/define\(\s*'" . $k . "'\s*,\s*'((?:[^'\\\\]|\\\\.)*)'\s*\)/", $src, $m ) ) {
			exit( "wp-config.php: $k fehlt\n" );
		}
		$out[ $k ] = stripslashes( $m[1] );
		if ( str_starts_with( $out[ $k ], '<' ) ) {
			exit( "wp-config.php: $k ist noch ein Platzhalter\n" );
		}
	}
	$out['PREFIX'] = preg_match( "/\\\$table_prefix\s*=\s*'([a-z0-9_]+)'/i", $src, $m ) ? $m[1] : 'wp_';
	return $out;
}

/** Tabellen mit dem Präfix der neuen Installation (die alte Seite darf im selben Schema liegen). */
function fwg_prefix_tables( mysqli $db, string $prefix ): int {
	$res = $db->query( "SHOW TABLES LIKE '" . $db->real_escape_string( str_replace( '_', '\\_', $prefix ) ) . "%'" );
	return $res ? $res->num_rows : 0;
}

function fwg_connect( array $c ): mysqli {
	mysqli_report( MYSQLI_REPORT_OFF );
	$host = $c['DB_HOST'];
	$port = 3306;
	if ( str_contains( $host, ':' ) ) {
		[ $host, $port ] = explode( ':', $host, 2 );
		$port            = (int) $port;
	}
	$db = @new mysqli( $host, $c['DB_USER'], $c['DB_PASSWORD'], $c['DB_NAME'], $port );
	if ( $db->connect_error ) {
		exit( 'DB-Verbindung fehlgeschlagen: ' . $db->connect_error . "\n" );
	}
	$db->set_charset( 'utf8mb4' );
	return $db;
}

switch ( $step ) {
	case 'check':
		echo 'PHP ' . PHP_VERSION . "\n";
		foreach ( array( 'zip', 'mysqli', 'mbstring', 'gd', 'curl', 'intl' ) as $ext ) {
			echo str_pad( $ext, 10 ) . ( extension_loaded( $ext ) ? 'ok' : 'FEHLT' ) . "\n";
		}
		echo 'memory_limit ' . ini_get( 'memory_limit' ) . ', max_execution_time ' . ini_get( 'max_execution_time' ) . "\n";
		foreach ( array( $zip, $sql, $cfg ) as $f ) {
			echo str_pad( basename( $f ), 16 ) . ( file_exists( $f ) ? round( filesize( $f ) / 1024 ) . ' KB' : 'FEHLT' ) . "\n";
		}
		echo 'Ordner ' . $dir . ' beschreibbar: ' . ( is_writable( $dir ) ? 'ja' : 'NEIN' ) . "\n";
		if ( file_exists( $cfg ) ) {
			$c   = fwg_db_config( $cfg );
			$db  = fwg_connect( $c );
			$res = $db->query( 'SHOW TABLES' );
			echo 'DB-Verbindung ok, Präfix ' . $c['PREFIX'] . ': ' . fwg_prefix_tables( $db, $c['PREFIX'] ) . ' Tabellen (muss 0 sein), gesamt ' . $res->num_rows . " Tabellen im Schema\n";
			echo 'MySQL ' . $db->server_info . "\n";
		}
		break;

	case 'clear':
		$stat = array( 'files' => 0, 'dirs' => 0, 'failed' => 0, 'skipped' => 0 );
		foreach ( scandir( $dir ) as $e ) {
			if ( '.' === $e || '..' === $e || in_array( $e, $keep, true ) ) {
				continue;
			}
			fwg_rm( $dir . '/' . $e, $dir, $stat );
		}
		echo 'Alte Seite entfernt: ' . $stat['files'] . ' Dateien, ' . $stat['dirs'] . ' Ordner; ' . $stat['failed'] . " nicht löschbar\n";
		echo 'Übrig: ' . implode( ', ', array_values( array_diff( scandir( $dir ), array( '.', '..' ) ) ) ) . "\n";
		if ( $stat['failed'] > 0 ) {
			echo "Fehler: nicht alles ließ sich löschen\n";
		}
		break;

	case 'activate':
		if ( ! file_exists( $dir . '/wp-config-new.php' ) ) {
			exit( "wp-config-new.php fehlt\n" );
		}
		if ( file_exists( $dir . '/wp-config.php' ) ) {
			unlink( $dir . '/wp-config.php' );
		}
		echo rename( $dir . '/wp-config-new.php', $dir . '/wp-config.php' ) ? "wp-config.php aktiv\n" : "Fehler: Umbenennen fehlgeschlagen\n";
		break;

	case 'unzip':
		if ( ! file_exists( $zip ) ) {
			exit( "fwg-site.zip fehlt\n" );
		}
		$za = new ZipArchive();
		if ( true !== $za->open( $zip ) ) {
			exit( "Zip lässt sich nicht öffnen\n" );
		}
		$n = $za->numFiles;
		if ( ! $za->extractTo( $dir ) ) {
			exit( "Entpacken fehlgeschlagen\n" );
		}
		$za->close();
		echo "$n Einträge entpackt\n";
		foreach ( array( 'wp-settings.php', 'wp-content/themes/fairwaygolf/style.css', 'wp-content/mu-plugins/fwg-smtp.php', '.htaccess', 'wp-content/uploads/.htaccess' ) as $f ) {
			echo str_pad( $f, 48 ) . ( file_exists( "$dir/$f" ) ? 'ok' : 'FEHLT' ) . "\n";
		}
		break;

	case 'db':
		if ( ! file_exists( $sql ) ) {
			exit( "fwg-prod.sql fehlt\n" );
		}
		$c    = fwg_db_config( $cfg );
		$db   = fwg_connect( $c );
		$have = fwg_prefix_tables( $db, $c['PREFIX'] );
		if ( $have > 0 && empty( $_GET['force'] ) ) {
			exit( 'Es gibt schon ' . $have . ' Tabellen mit dem Präfix ' . $c['PREFIX'] . ". Abbruch; mit &force=1 werden sie laut Dump ersetzt.\n" );
		}
		$db->query( 'SET NAMES utf8mb4' );
		$db->query( 'SET foreign_key_checks = 0' );
		$fh = fopen( $sql, 'r' );
		$stmt = '';
		$count = 0;
		$errors = 0;
		while ( ( $line = fgets( $fh ) ) !== false ) {
			$trim = rtrim( $line );
			if ( '' === $stmt && ( '' === $trim || str_starts_with( $trim, '--' ) ) ) {
				continue;
			}
			$stmt .= $line;
			if ( str_ends_with( $trim, ';' ) ) {
				if ( ! $db->query( $stmt ) ) {
					++$errors;
					echo 'Fehler: ' . $db->error . ' bei: ' . substr( $stmt, 0, 120 ) . "\n";
				}
				++$count;
				$stmt = '';
			}
		}
		fclose( $fh );
		$db->query( 'SET foreign_key_checks = 1' );
		echo "$count Anweisungen, $errors Fehler, " . fwg_prefix_tables( $db, $c['PREFIX'] ) . ' Tabellen mit Präfix ' . $c['PREFIX'] . "\n";
		$opt = $db->query( 'SELECT option_name, option_value FROM `' . $c['PREFIX'] . "options` WHERE option_name IN ('siteurl','home','blogname')" );
		while ( $row = $opt->fetch_assoc() ) {
			echo str_pad( $row['option_name'], 10 ) . $row['option_value'] . "\n";
		}
		break;

	case 'cleanup':
		foreach ( array( $zip, $sql ) as $f ) {
			if ( file_exists( $f ) ) {
				echo basename( $f ) . ( unlink( $f ) ? ' gelöscht' : ' NICHT gelöscht' ) . "\n";
			}
		}
		if ( file_exists( $dir . '/wp-config-new.php' ) ) {
			echo "Achtung: wp-config-new.php ist noch nicht aktiviert\n";
		}
		echo basename( __FILE__ ) . ( unlink( __FILE__ ) ? ' gelöscht' : ' NICHT gelöscht' ) . "\n";
		break;

	default:
		echo "Schritte: check, clear, unzip, db, activate, cleanup\n";
}
