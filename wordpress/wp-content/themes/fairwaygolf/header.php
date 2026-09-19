<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<a class="fwg-skip" href="#inhalt">Zum Inhalt springen</a>
<?php wp_body_open(); ?>
<header class="fwg-header">
	<div class="fwg-header__inner">
		<?php echo fwg_logo( 'fwg-header__logo' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		<nav class="fwg-nav" aria-label="Hauptnavigation">
			<a href="<?php echo esc_url( home_url( '/#so-gehts' ) ); ?>" class="fwg-nav__link">So funktioniert es</a>
			<a href="<?php echo esc_url( home_url( '/#modelle' ) ); ?>" class="fwg-nav__link">Modelle</a>
			<a href="<?php echo esc_url( home_url( '/golfplaetze/' ) ); ?>" class="<?php echo esc_attr( fwg_nav_class( 'golfplaetze' ) ); ?>">Für Golfanlagen</a>
			<a href="<?php echo esc_url( home_url( '/unterstuetzen/' ) ); ?>" class="<?php echo esc_attr( fwg_nav_class( 'unterstuetzen' ) ); ?>">Unterstützen</a>
			<a href="<?php echo esc_url( home_url( '/ueber-uns/' ) ); ?>" class="<?php echo esc_attr( fwg_nav_class( 'ueber-uns' ) ); ?>">Über uns</a>
		</nav>
		<a href="<?php echo esc_url( home_url( '/voranmeldung/#formular' ) ); ?>" class="fwg-btn fwg-btn--primary fwg-btn--sm fwg-header__cta"><span>Voranmelden</span></a>
		<button type="button" class="fwg-burger" data-fwg-menu-open aria-label="Menü öffnen" aria-expanded="false" aria-controls="fwg-menu">
			<?php echo fwg_icon( 'list' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</button>
	</div>
</header>

<div id="fwg-menu" class="fwg-menu" data-fwg-menu hidden>
	<div class="fwg-menu__head">
		<?php echo fwg_logo( 'fwg-menu__logo' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		<button type="button" class="fwg-menu__close" data-fwg-menu-close aria-label="Menü schließen"><?php echo fwg_icon( 'x' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
	</div>
	<nav class="fwg-menu__nav" aria-label="Mobile Navigation">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Start <?php echo fwg_icon( 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
		<a href="<?php echo esc_url( home_url( '/#so-gehts' ) ); ?>">So funktioniert es <?php echo fwg_icon( 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
		<a href="<?php echo esc_url( home_url( '/#modelle' ) ); ?>">Modelle <?php echo fwg_icon( 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
		<a href="<?php echo esc_url( home_url( '/golfplaetze/' ) ); ?>">Für Golfanlagen <?php echo fwg_icon( 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
		<a href="<?php echo esc_url( home_url( '/unterstuetzen/' ) ); ?>">Unterstützen <?php echo fwg_icon( 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
		<a href="<?php echo esc_url( home_url( '/ueber-uns/' ) ); ?>">Über uns <?php echo fwg_icon( 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
	</nav>
	<div class="fwg-menu__cta">
		<a href="<?php echo esc_url( home_url( '/voranmeldung/#formular' ) ); ?>" class="fwg-btn fwg-btn--lime fwg-btn--block"><span>Voranmelden</span><?php echo fwg_icon( 'arrow-right', 'fwg-btn__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
		<p>Fragen? <a href="mailto:<?php echo esc_attr( FWG_CONTACT_EMAIL ); ?>"><?php echo esc_html( FWG_CONTACT_EMAIL ); ?></a></p>
	</div>
</div>

<main id="inhalt" class="fwg-main">
