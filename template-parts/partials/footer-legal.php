<?php
/**
 * Footer legal links and author credit.
 *
 * @package hypothetical_capital
 */

$options        = hypothetical_capital_get_site_options();
$terms_of_use   = $options['terms_of_use'] ?? null;
$privacy_policy = $options['privacy_policy'] ?? null;
$author         = $options['author'] ?? null;
?>
<div class="site-footer__legal-links">
	<?php if ( ! empty( $terms_of_use['url'] ) ) : ?>
		<a
			class="site-footer__legal-link"
			<?php echo hypothetical_capital_acf_link_attrs( $terms_of_use ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		>
			<?php echo esc_html( hypothetical_capital_acf_link_label( $terms_of_use ) ); ?>
		</a>
	<?php endif; ?>

	<?php if ( ! empty( $privacy_policy['url'] ) ) : ?>
		<a
			class="site-footer__legal-link"
			<?php echo hypothetical_capital_acf_link_attrs( $privacy_policy ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		>
			<?php echo esc_html( hypothetical_capital_acf_link_label( $privacy_policy ) ); ?>
		</a>
	<?php endif; ?>
</div>

<?php if ( $author ) : ?>
	<div class="site-footer__author">
		<?php echo wp_kses_post( $author ); ?>
	</div>
<?php endif; ?>
