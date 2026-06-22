<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package hypothetical_capital
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'hypothetical-capital' ); ?></a>

	<header id="masthead" class="site-header">
		<div class="site-header__inner">
			<div class="site-header__bar">
				<div class="site-branding">
					<?php
					the_custom_logo();

					$hypothetical_capital_title_class = 'site-title';

					if ( has_custom_logo() ) {
						$hypothetical_capital_title_class .= ' screen-reader-text';
					}

					if ( is_front_page() && is_home() ) :
						?>
						<h1 class="<?php echo esc_attr( $hypothetical_capital_title_class ); ?>"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
						<?php
					else :
						?>
						<p class="<?php echo esc_attr( $hypothetical_capital_title_class ); ?>"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
						<?php
					endif;

					$hypothetical_capital_description = get_bloginfo( 'description', 'display' );
					if ( $hypothetical_capital_description || is_customize_preview() ) :
						?>
						<p class="site-description"><?php echo $hypothetical_capital_description; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
					<?php endif; ?>
				</div><!-- .site-branding -->

				<nav id="site-navigation" class="main-navigation">
					<button class="menu-toggle" aria-controls="mobile-menu-panel" aria-expanded="false">
						<span class="screen-reader-text"><?php esc_html_e( 'Primary Menu', 'hypothetical-capital' ); ?></span>
						<span class="menu-toggle__icon" aria-hidden="true"></span>
					</button>
					<div id="mobile-menu-panel" class="main-navigation__panel" aria-hidden="true">
						<div class="main-navigation__menu">
							<?php
							wp_nav_menu(
								array(
									'theme_location' => 'menu-1',
									'menu_id'        => 'primary-menu',
								)
							);
							?>
						</div>
						<?php
						get_template_part(
							'template-parts/partials/corner-bracket-actions',
							null,
							array(
								'prefix' => 'main-navigation',
							)
						);
						?>
					</div>
				</nav><!-- #site-navigation -->
			</div><!-- .site-header__bar -->
		</div><!-- .site-header__inner -->
	</header><!-- #masthead -->
