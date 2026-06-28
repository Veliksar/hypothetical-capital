<?php
/**
 * Flexible Content layout: Content Block
 *
 * @package hypothetical_capital
 */

$content          = get_sub_field( 'content' );
$width            = get_sub_field( 'width' ) ?: 'readable';
$alignment        = get_sub_field( 'alignment' ) ?: 'left';
$background       = get_sub_field( 'background' ) ?: 'white';
$padding_vertical = get_sub_field( 'padding_vertical' ) ?: 'default';
$custom_id        = get_sub_field( 'custom_id' );

if ( ! $content ) {
	return;
}

// Build classes.
$classes   = array( 'fc-block', 'fc-block--content' );
$classes[] = 'fc-block--content-width-' . $width;
$classes[] = 'fc-block--content-align-' . $alignment;
$classes[] = 'fc-block--content-bg-' . $background;
$classes[] = 'fc-block--content-pad-' . $padding_vertical;

// ID attribute.
$id_attr = '';
if ( $custom_id ) {
	$id_attr = ' id="' . esc_attr( sanitize_title( $custom_id ) ) . '"';
}
?>

<section class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"<?php echo $id_attr; ?>>
	<div class="fc-block__inner entry-content">
		<?php echo wp_kses_post( $content ); ?>
	</div>
</section>
