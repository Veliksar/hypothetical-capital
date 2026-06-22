<?php
/**
 * Flexible Content layout: Highlights
 *
 * @package hypothetical_capital
 */

$content    = get_sub_field( 'content' );
$button     = get_sub_field( 'button' );
$highlights = get_sub_field( 'highlights' );
$arrow_uri  = get_template_directory_uri() . '/assets/img/arrow-right.svg';
?>

<section class="fc-block fc-block--highlights">
	<div class="fc-block__inner">
		<div class="fc-block__columns">
			<div class="fc-block__column fc-block__column--left">
				<?php if ( $content ) : ?>
					<div class="fc-block__content">
						<?php echo wp_kses_post( $content ); ?>
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $button['url'] ) ) : ?>
					<a class="fc-block__button" href="<?php echo esc_url( $button['url'] ); ?>"<?php echo ! empty( $button['target'] ) ? ' target="' . esc_attr( $button['target'] ) . '"' : ''; ?>>
						<?php echo esc_html( $button['title'] ?: $button['url'] ); ?>
					</a>
				<?php endif; ?>
			</div>

			<?php if ( ! empty( $highlights ) ) : ?>
				<div class="fc-block__column fc-block__column--right">
					<div class="fc-block__highlights-slider" data-highlights-slider data-next-slide-label="<?php esc_attr_e( 'Next slide', 'hypothetical-capital' ); ?>">
						<div class="fc-block__highlights-navigation">
							<div class="fc-block__highlights-nav" aria-label="<?php esc_attr_e( 'Highlights slider', 'hypothetical-capital' ); ?>">
								<button type="button" class="fc-block__highlights-arrow" data-slider-prev aria-label="<?php esc_attr_e( 'Previous slide', 'hypothetical-capital' ); ?>" disabled>
									<img class="fc-block__highlights-arrow-icon fc-block__highlights-arrow-icon--prev" src="<?php echo esc_url( $arrow_uri ); ?>" alt="" width="8" height="12" decoding="async">
								</button>
								<button type="button" class="fc-block__highlights-arrow" data-slider-next aria-label="<?php esc_attr_e( 'Next slide', 'hypothetical-capital' ); ?>">
									<img class="fc-block__highlights-arrow-icon" src="<?php echo esc_url( $arrow_uri ); ?>" alt="" width="8" height="12" decoding="async">
								</button>
							</div>
						</div>

						<div class="fc-block__highlights-viewport">
							<div class="fc-block__highlights-stage">
								<div class="fc-block__highlights-slots" aria-hidden="true">
									<div class="fc-block__highlights-slot fc-block__highlights-slot--primary">
										<span class="fc-block__highlights-slot-inner"></span>
									</div>
									<div class="fc-block__highlights-slot fc-block__highlights-slot--secondary">
										<span class="fc-block__highlights-slot-inner"></span>
									</div>
									<div class="fc-block__highlights-slot fc-block__highlights-slot--tertiary">
										<span class="fc-block__highlights-slot-inner"></span>
									</div>
								</div>

								<div class="fc-block__highlights-cards">
									<?php foreach ( $highlights as $index => $item ) : ?>
										<article class="fc-block__highlight-card" data-card-index="<?php echo esc_attr( (string) $index ); ?>">
										<?php if ( ! empty( $item['image']['url'] ) ) : ?>
											<figure class="fc-block__highlight-media">
												<img
													class="fc-block__highlight-image"
													src="<?php echo esc_url( $item['image']['url'] ); ?>"
													alt="<?php echo esc_attr( $item['image']['alt'] ?: '' ); ?>"
												>
											</figure>
										<?php endif; ?>

										<?php if ( ! empty( $item['logo']['url'] ) ) : ?>
											<div class="fc-block__highlight-logo">
												<img
													class="fc-block__highlight-logo-image"
													src="<?php echo esc_url( $item['logo']['url'] ); ?>"
													alt="<?php echo esc_attr( $item['logo']['alt'] ?: '' ); ?>"
													width="116"
													loading="lazy"
													decoding="async"
												>
											</div>
										<?php endif; ?>

										<div class="fc-block__highlight-overlay"></div>
										</article>
									<?php endforeach; ?>
								</div>
							</div>

							<div class="fc-block__highlights-caption">
								<?php foreach ( $highlights as $index => $item ) : ?>
									<?php if ( ! empty( $item['description'] ) ) : ?>
										<div class="fc-block__highlight-description" data-caption-index="<?php echo esc_attr( (string) $index ); ?>">
											<?php echo wp_kses_post( $item['description'] ); ?>
										</div>
									<?php endif; ?>
								<?php endforeach; ?>
							</div>
						</div>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
