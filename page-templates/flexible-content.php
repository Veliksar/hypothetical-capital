<?php
/**
 * Template Name: Flexible Content
 *
 * @package hypothetical_capital
 */

get_header();
?>

	<main id="primary" class="site-main site-main--flexible-content">

		<?php
		while ( have_posts() ) :
			the_post();
			hypothetical_capital_render_flexible_content();
		endwhile;
		?>

	</main>

<?php
get_footer();
