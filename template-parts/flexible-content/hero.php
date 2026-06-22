<?php
/**
 * Flexible Content layout: Hero
 *
 * @package hypothetical_capital
 */

$content               = get_sub_field( 'content' );
$cta                   = get_sub_field( 'cta' );
$background_media_type = get_sub_field( 'background_media_type' ) ?: 'image';
$background_image      = get_sub_field( 'background_image' );
$background_video      = get_sub_field( 'background_video' );

$section_classes = array( 'fc-block', 'fc-block--hero' );

if ( 'video' === $background_media_type && ! empty( $background_video['url'] ) ) {
	$section_classes[] = 'fc-block--hero-has-video';
} elseif ( ! empty( $background_image['url'] ) ) {
	$section_classes[] = 'fc-block--hero-has-image';
}
?>

<section class="<?php echo esc_attr( implode( ' ', $section_classes ) ); ?>">
	<div class="fc-block__grid-lines" aria-hidden="true">
		<span class="fc-block__grid-line"></span>
		<span class="fc-block__grid-line"></span>
		<span class="fc-block__grid-line"></span>
		<span class="fc-block__grid-line"></span>
		<span class="fc-block__grid-line"></span>
	</div>

	<?php if ( 'video' === $background_media_type && ! empty( $background_video['url'] ) ) : ?>
		<video class="fc-block__media" autoplay muted loop playsinline>
			<source src="<?php echo esc_url( $background_video['url'] ); ?>" type="<?php echo esc_attr( $background_video['mime_type'] ?? 'video/mp4' ); ?>">
		</video>
	<?php elseif ( ! empty( $background_image['url'] ) ) : ?>
		<img
			class="fc-block__media"
			src="<?php echo esc_url( $background_image['url'] ); ?>"
			alt=""
			decoding="async"
		>
	<?php endif; ?>

	<div class="fc-block__overlay"></div>

	<div class="fc-block__inner">
		<?php if ( $content ) : ?>
			<div class="fc-block__content entry-content">
				<?php echo wp_kses_post( $content ); ?>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $cta['url'] ) ) : ?>
			<a class="fc-block__cta" href="<?php echo esc_url( $cta['url'] ); ?>"<?php echo ! empty( $cta['target'] ) ? ' target="' . esc_attr( $cta['target'] ) . '"' : ''; ?>>
				<?php echo esc_html( $cta['title'] ?: $cta['url'] ); ?>
			</a>
		<?php endif; ?>
	</div>
</section>
