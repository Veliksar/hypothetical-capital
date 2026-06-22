<?php
/**
 * Corner-bracket action buttons (Get in touch + LinkedIn).
 *
 * @package hypothetical_capital
 *
 * @var array $args {
 *     @type string $prefix BEM block prefix, e.g. main-navigation or site-footer.
 * }
 */

$prefix        = isset( $args['prefix'] ) ? sanitize_html_class( $args['prefix'] ) : 'main-navigation';
$options       = hypothetical_capital_get_site_options();
$get_in_touch  = $options['get_in_touch'] ?? null;
$linkedin      = $options['linkedin'] ?? null;
$linkedin_icon = $options['linkedin_icon'] ?? null;
?>
<div class="<?php echo esc_attr( $prefix ); ?>__actions">
	<?php if ( ! empty( $get_in_touch['url'] ) ) : ?>
		<a
			class="<?php echo esc_attr( $prefix ); ?>__btn <?php echo esc_attr( $prefix ); ?>__btn--text"
			<?php echo hypothetical_capital_acf_link_attrs( $get_in_touch ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		>
			<?php echo esc_html( hypothetical_capital_acf_link_label( $get_in_touch, __( 'Get in touch', 'hypothetical-capital' ) ) ); ?>
		</a>
	<?php endif; ?>

	<?php if ( ! empty( $linkedin['url'] ) ) : ?>
		<a
			class="<?php echo esc_attr( $prefix ); ?>__btn <?php echo esc_attr( $prefix ); ?>__btn--icon"
			<?php echo hypothetical_capital_acf_link_attrs( $linkedin ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			aria-label="<?php echo esc_attr( hypothetical_capital_acf_link_label( $linkedin, __( 'LinkedIn', 'hypothetical-capital' ) ) ); ?>"
		>
			<?php if ( ! empty( $linkedin_icon['url'] ) ) : ?>
				<img
					class="<?php echo esc_attr( $prefix ); ?>__btn-icon"
					src="<?php echo esc_url( $linkedin_icon['url'] ); ?>"
					alt=""
					width="<?php echo esc_attr( $linkedin_icon['width'] ?? '' ); ?>"
					height="<?php echo esc_attr( $linkedin_icon['height'] ?? '' ); ?>"
				>
			<?php else : ?>
				<span class="<?php echo esc_attr( $prefix ); ?>__btn-icon-fallback" aria-hidden="true">in</span>
			<?php endif; ?>
		</a>
	<?php endif; ?>
</div>
