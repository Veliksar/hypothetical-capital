<?php
/**
 * Flexible Content layout: Content Block
 *
 * @package hypothetical_capital
 */

$content = get_sub_field( 'content' );

if ( ! $content ) {
	return;
}
?>

<section class="fc-block fc-block--content">
	<div class="fc-block__inner entry-content">
		<?php echo wp_kses_post( $content ); ?>
	</div>
</section>
