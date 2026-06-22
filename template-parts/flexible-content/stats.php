<?php
/**
 * Flexible Content layout: Stats
 *
 * @package hypothetical_capital
 */

$content = get_sub_field( 'content' );
$image   = get_sub_field( 'image' );
$stats   = get_sub_field( 'stats' );
?>

<section class="fc-block fc-block--stats" id="stats">
	<?php if ( ! empty( $image['url'] ) ) : ?>
		<div class="fc-block__grid-lines fc-block__grid-lines--horizontal" aria-hidden="true">
			<span class="fc-block__grid-line fc-block__grid-line--horizontal"></span>
			<span class="fc-block__grid-line fc-block__grid-line--horizontal"></span>
		</div>
	<?php endif; ?>

	<div class="fc-block__inner">
		<div class="fc-block__columns">
			<div class="fc-block__column fc-block__column--left">
				<?php if ( $content ) : ?>
					<div class="fc-block__content">
						<?php echo wp_kses_post( $content ); ?>
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $image['url'] ) ) : ?>
					<figure class="fc-block__figure">
						<img
							class="fc-block__image"
							src="<?php echo esc_url( $image['url'] ); ?>"
							alt="<?php echo esc_attr( $image['alt'] ?: '' ); ?>"
							<?php if ( ! empty( $image['width'] ) ) : ?>
								width="<?php echo esc_attr( $image['width'] ); ?>"
							<?php endif; ?>
							<?php if ( ! empty( $image['height'] ) ) : ?>
								height="<?php echo esc_attr( $image['height'] ); ?>"
							<?php endif; ?>
						>
					</figure>
				<?php endif; ?>
			</div>

			<?php if ( ! empty( $stats ) ) : ?>
				<div class="fc-block__column fc-block__column--right">
					<div class="fc-block__stats">
						<?php foreach ( $stats as $stat ) : ?>
							<div class="fc-block__stat-card">
								<?php if ( ! empty( $stat['title'] ) ) : ?>
									<?php
									$stat_parts = hypothetical_capital_parse_stat_value( $stat['title'] );
									$stat_kses  = array(
										'span' => array(
											'class' => true,
										),
									);
									?>
									<div
										class="fc-block__stat-title"
										<?php if ( null !== $stat_parts ) : ?>
											data-stat-counter
											data-stat-value="<?php echo esc_attr( $stat_parts['value'] ); ?>"
											data-stat-prefix="<?php echo esc_attr( $stat_parts['prefix'] ); ?>"
											data-stat-suffix="<?php echo esc_attr( $stat_parts['suffix'] ); ?>"
											data-stat-decimals="<?php echo esc_attr( $stat_parts['decimals'] ); ?>"
										<?php endif; ?>
									>
										<?php
										echo wp_kses(
											null !== $stat_parts
												? hypothetical_capital_format_stat_title( $stat_parts, 0 )
												: hypothetical_capital_stat_title_html( $stat['title'] ),
											$stat_kses
										);
										?>
									</div>
								<?php endif; ?>

								<?php if ( ! empty( $stat['subtitle'] ) ) : ?>
									<div class="fc-block__stat-subtitle"><?php echo esc_html( $stat['subtitle'] ); ?></div>
								<?php endif; ?>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
