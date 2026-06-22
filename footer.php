<?php
/**
 * The template for displaying the footer
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package hypothetical_capital
 */

$site_options = hypothetical_capital_get_site_options();
$copyright    = $site_options['copyright'] ?? null;
?>

	<footer id="colophon" class="site-footer"<?php echo hypothetical_capital_footer_style_attr(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
		<div class="site-footer__inner">
			<div class="site-footer__column site-footer__column--brand">
				<div class="site-footer__brand">
					<?php if ( has_custom_logo() ) : ?>
						<?php the_custom_logo(); ?>
					<?php else : ?>
						<a class="site-footer__title" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
							<?php bloginfo( 'name' ); ?>
						</a>
					<?php endif; ?>
				</div>

				<?php
				get_template_part(
					'template-parts/partials/corner-bracket-actions',
					null,
					array(
						'prefix' => 'site-footer',
					)
				);
				?>

				<?php if ( $copyright ) : ?>
					<div class="site-footer__copyright">
						<?php echo wp_kses_post( $copyright ); ?>
					</div>
				<?php endif; ?>
			</div>

			<div class="site-footer__column site-footer__column--nav">
				<?php
				wp_nav_menu(
					array(
						'theme_location'  => 'footer',
						'menu_id'         => 'footer-menu',
						'menu_class'      => 'site-footer__nav',
						'container'       => 'nav',
						'container_class' => 'site-footer__navigation',
						'fallback_cb'     => false,
						'depth'           => 2,
					)
				);
				?>
			</div>

			<div class="site-footer__column site-footer__column--legal">
				<?php get_template_part( 'template-parts/partials/footer-legal' ); ?>
			</div>
		</div>

		<button
			type="button"
			class="site-footer__to-up"
			data-scroll-to-top
			aria-label="<?php esc_attr_e( 'Back to top', 'hypothetical-capital' ); ?>"
		>
			<img
				class="site-footer__to-up-icon"
				src="<?php echo esc_url( get_theme_file_uri( 'assets/img/arrow-right.svg' ) ); ?>"
				alt=""
				width="8"
				height="12"
				decoding="async"
			>
		</button>
	</footer>
</div>

<?php wp_footer(); ?>

</body>
</html>
