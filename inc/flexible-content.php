<?php
/**
 * Flexible Content renderer.
 *
 * @package hypothetical_capital
 */

/**
 * Render ACF Flexible Content rows for the current post.
 *
 * @param string $field_name ACF flexible content field name.
 * @return void
 */
function hypothetical_capital_render_flexible_content( $field_name = 'flexible_content' ) {
	if ( ! function_exists( 'have_rows' ) || ! have_rows( $field_name ) ) {
		return;
	}

	while ( have_rows( $field_name ) ) {
		the_row();

		$layout = get_row_layout();

		if ( ! $layout ) {
			continue;
		}

		get_template_part( 'template-parts/flexible-content/' . $layout );
	}
}
