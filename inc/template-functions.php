<?php
/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package hypothetical_capital
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function hypothetical_capital_body_classes( $classes ) {
	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	return $classes;
}
add_filter( 'body_class', 'hypothetical_capital_body_classes' );

/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
 */
function hypothetical_capital_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">', esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}
add_action( 'wp_head', 'hypothetical_capital_pingback_header' );

/**
 * Parses a stat value into numeric and formatting parts.
 *
 * @param string $title Stat value from ACF.
 * @return array|null
 */
function hypothetical_capital_parse_stat_value( $title ) {
	$title = wp_strip_all_tags( (string) $title );

	if ( '' === $title || ! preg_match( '/^([^\d]*?)([\d]+(?:\.[\d]+)?)(.*)$/u', $title, $matches ) ) {
		return null;
	}

	$number = $matches[2];

	return array(
		'prefix'   => $matches[1],
		'value'    => (float) $number,
		'suffix'   => $matches[3],
		'decimals' => false !== strpos( $number, '.' ) ? strlen( substr( strrchr( $number, '.' ), 1 ) ) : 0,
	);
}

/**
 * Formats parsed stat parts for display.
 *
 * @param array $parts Parsed stat parts.
 * @param float $value Numeric value to render.
 * @return string Safe HTML.
 */
function hypothetical_capital_format_stat_title( $parts, $value ) {
	$formatted_value = $parts['decimals'] > 0
		? number_format( (float) $value, (int) $parts['decimals'], '.', '' )
		: (string) (int) round( (float) $value );

	$output = esc_html( $parts['prefix'] . $formatted_value );

	if ( '%' === $parts['suffix'] ) {
		return $output . '<span class="fc-block__stat-title-unit">%</span>';
	}

	return $output . esc_html( $parts['suffix'] );
}

/**
 * Formats a stat value for display, wrapping trailing unit symbols when needed.
 *
 * @param string $title Stat value from ACF.
 * @return string Safe HTML.
 */
function hypothetical_capital_stat_title_html( $title ) {
	$parts = hypothetical_capital_parse_stat_value( $title );

	if ( null !== $parts ) {
		return hypothetical_capital_format_stat_title( $parts, $parts['value'] );
	}

	return esc_html( wp_strip_all_tags( (string) $title ) );
}

/**
 * Returns href and target attributes for an ACF link field.
 *
 * @param array|null $link ACF link field value.
 * @return string Safe HTML attributes or empty string.
 */
function hypothetical_capital_acf_link_attrs( $link ) {
	if ( empty( $link['url'] ) ) {
		return '';
	}

	$attrs = ' href="' . esc_url( $link['url'] ) . '"';

	if ( ! empty( $link['target'] ) ) {
		$attrs .= ' target="' . esc_attr( $link['target'] ) . '"';
	}

	return $attrs;
}

/**
 * Cached ACF option fields used across header and footer.
 *
 * @return array<string, mixed>
 */
function hypothetical_capital_get_site_options() {
	static $options = null;

	if ( null !== $options ) {
		return $options;
	}

	if ( ! function_exists( 'get_field' ) ) {
		$options = array();
		return $options;
	}

	$options = array(
		'footer_color'   => get_field( 'footer_color', 'option' ),
		'get_in_touch'   => get_field( 'get_in_touch', 'option' ),
		'terms_of_use'   => get_field( 'terms_of_use', 'option' ),
		'privacy_policy' => get_field( 'privacy_policy', 'option' ),
		'copyright'      => get_field( 'copy', 'option' ),
		'author'         => get_field( 'author', 'option' ),
		'linkedin'       => get_field( 'linkedin', 'option' ),
		'linkedin_icon'  => get_field( 'linkedin_icon', 'option' ),
	);

	return $options;
}

/**
 * Inline style attribute for customizable footer background.
 *
 * @return string Safe HTML attribute fragment or empty string.
 */
function hypothetical_capital_footer_style_attr() {
	$footer_color = hypothetical_capital_get_site_options()['footer_color'] ?? null;

	if ( empty( $footer_color ) ) {
		return '';
	}

	return sprintf( ' style="--site-footer-bg: %s;"', esc_attr( $footer_color ) );
}

/**
 * Display label for an ACF link field.
 *
 * @param array|null $link     ACF link field value.
 * @param string     $fallback Fallback label when title is empty.
 * @return string
 */
function hypothetical_capital_acf_link_label( $link, $fallback = '' ) {
	if ( empty( $link['url'] ) ) {
		return $fallback;
	}

	if ( ! empty( $link['title'] ) ) {
		return $link['title'];
	}

	return '' !== $fallback ? $fallback : $link['url'];
}
