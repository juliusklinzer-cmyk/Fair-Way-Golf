<?php
/**
 * Inhaltsseiten (Impressum, Datenschutz, AGB, Barrierefreiheit): schmale Lesespalte.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();
?>
<?php while ( have_posts() ) : the_post(); ?>
<section class="fwg-section fwg-pagehead">
	<div class="fwg-container fwg-container--narrow">
		<h1 class="fwg-h2"><?php the_title(); ?></h1>
	</div>
</section>
<section class="fwg-section fwg-section--flush">
	<div class="fwg-container fwg-container--narrow fwg-prose">
		<?php the_content(); ?>
	</div>
</section>
<?php endwhile; ?>
<?php
get_footer();
