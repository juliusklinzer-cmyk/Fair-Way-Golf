<?php
/**
 * Fallback-Template: es gibt keine Beiträge, alles sind Seiten.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();
?>
<section class="fwg-section">
	<div class="fwg-container fwg-container--narrow fwg-prose">
		<?php if ( have_posts() ) : ?>
			<?php while ( have_posts() ) : the_post(); ?>
				<h1><?php the_title(); ?></h1>
				<?php the_content(); ?>
			<?php endwhile; ?>
		<?php else : ?>
			<h1>Hier ist noch nichts</h1>
			<p><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Zur Startseite</a></p>
		<?php endif; ?>
	</div>
</section>
<?php
get_footer();
