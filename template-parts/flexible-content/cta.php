<?php
/**
 * Flexible Content layout: CTA
 *
 * @package hypothetical_capital
 */

$heading = get_sub_field( 'heading' );
$text    = get_sub_field( 'text' );
$button  = get_sub_field( 'button' );
?>

<section class="fc-block fc-block--cta">
	<div class="fc-block__inner">
		<?php if ( $heading ) : ?>
			<h2 class="fc-block__heading"><?php echo esc_html( $heading ); ?></h2>
		<?php endif; ?>

		<?php if ( $text ) : ?>
			<p class="fc-block__text"><?php echo esc_html( $text ); ?></p>
		<?php endif; ?>

		<?php if ( ! empty( $button['url'] ) ) : ?>
			<a class="fc-block__button" href="<?php echo esc_url( $button['url'] ); ?>"<?php echo ! empty( $button['target'] ) ? ' target="' . esc_attr( $button['target'] ) . '"' : ''; ?>>
				<?php echo esc_html( $button['title'] ?: $button['url'] ); ?>
			</a>
		<?php endif; ?>
	</div>
</section>
