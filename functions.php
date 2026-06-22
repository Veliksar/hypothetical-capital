<?php
/**
 * hypothetical capital functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package hypothetical_capital
 */

if ( ! defined( 'HC_THEME_VERSION' ) ) {
	define( 'HC_THEME_VERSION', '1.0.0' );
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function hypothetical_capital_setup() {
	/*
		* Make theme available for translation.
		* Translations can be filed in the /languages/ directory.
		* If you're building a theme based on hypothetical capital, use a find and replace
		* to change 'hypothetical-capital' to the name of your theme in all the template files.
		*/
	load_theme_textdomain( 'hypothetical-capital', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
		* Let WordPress manage the document title.
		* By adding theme support, we declare that this theme does not use a
		* hard-coded <title> tag in the document head, and expect WordPress to
		* provide it for us.
		*/
	add_theme_support( 'title-tag' );

	/*
		* Enable support for Post Thumbnails on posts and pages.
		*
		* @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		*/
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus(
		array(
			'menu-1' => esc_html__( 'Primary', 'hypothetical-capital' ),
			'footer'   => esc_html__( 'Footer', 'hypothetical-capital' ),
		)
	);

	/*
		* Switch default core markup for search form, comment form, and comments
		* to output valid HTML5.
		*/
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	// Set up the WordPress core custom background feature.
	add_theme_support(
		'custom-background',
		apply_filters(
			'hypothetical_capital_custom_background_args',
			array(
				'default-color' => 'ffffff',
				'default-image' => '',
			)
		)
	);

	/**
	 * Add support for core custom logo.
	 *
	 * @link https://codex.wordpress.org/Theme_Logo
	 */
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);
}
add_action( 'after_setup_theme', 'hypothetical_capital_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function hypothetical_capital_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'hypothetical_capital_content_width', 1680 );
}
add_action( 'after_setup_theme', 'hypothetical_capital_content_width', 0 );

/**
 * Enqueue scripts and styles.
 */
function hypothetical_capital_scripts() {
	$theme_uri  = get_template_directory_uri();
	$theme_path = get_template_directory();

	wp_enqueue_style(
		'hypothetical-capital-fonts',
		'https://fonts.googleapis.com/css2?family=Albert+Sans:ital,wght@0,400;0,500;0,700;1,400&family=Playfair+Display:ital,wght@0,400;1,400&display=swap',
		array(),
		null
	);
	wp_enqueue_style( 'hypothetical-capital-style', get_stylesheet_uri(), array( 'hypothetical-capital-fonts' ), HC_THEME_VERSION );
	wp_style_add_data( 'hypothetical-capital-style', 'rtl', 'replace' );

	$custom_css_path = $theme_path . '/assets/css/custom.min.css';
	$custom_css_uri  = $theme_uri . '/assets/css/custom.min.css';
	$custom_css_ver  = file_exists( $custom_css_path ) ? (string) filemtime( $custom_css_path ) : HC_THEME_VERSION;

	wp_enqueue_style(
		'hypothetical-capital-custom',
		$custom_css_uri,
		array( 'hypothetical-capital-style' ),
		$custom_css_ver
	);

	$gsap_path = $theme_path . '/assets/js/lib/gsap.min.js';
	$gsap_uri  = $theme_uri . '/assets/js/lib/gsap.min.js';
	$gsap_ver  = file_exists( $gsap_path ) ? (string) filemtime( $gsap_path ) : HC_THEME_VERSION;

	wp_enqueue_script(
		'gsap',
		$gsap_uri,
		array(),
		$gsap_ver,
		true
	);

	$scroll_trigger_path = $theme_path . '/assets/js/lib/ScrollTrigger.min.js';
	$scroll_trigger_uri  = $theme_uri . '/assets/js/lib/ScrollTrigger.min.js';
	$scroll_trigger_ver  = file_exists( $scroll_trigger_path ) ? (string) filemtime( $scroll_trigger_path ) : HC_THEME_VERSION;

	wp_enqueue_script(
		'gsap-scrolltrigger',
		$scroll_trigger_uri,
		array( 'gsap' ),
		$scroll_trigger_ver,
		true
	);

	$lenis_path = $theme_path . '/assets/js/lib/lenis.min.js';
	$lenis_uri  = $theme_uri . '/assets/js/lib/lenis.min.js';
	$lenis_ver  = file_exists( $lenis_path ) ? (string) filemtime( $lenis_path ) : HC_THEME_VERSION;

	wp_enqueue_script(
		'lenis',
		$lenis_uri,
		array(),
		$lenis_ver,
		true
	);

	wp_enqueue_script( 'hypothetical-capital-navigation', $theme_uri . '/assets/js/navigation.js', array(), HC_THEME_VERSION, true );
	wp_localize_script(
		'hypothetical-capital-navigation',
		'hypotheticalCapitalNavigation',
		array(
			'mobileBreakpoint' => 768,
		)
	);

	$animations_path = $theme_path . '/assets/js/animations.js';
	$animations_uri  = $theme_uri . '/assets/js/animations.js';
	$animations_ver  = file_exists( $animations_path ) ? (string) filemtime( $animations_path ) : HC_THEME_VERSION;

	wp_enqueue_script(
		'hypothetical-capital-animations',
		$animations_uri,
		array( 'gsap' ),
		$animations_ver,
		true
	);

	$smooth_scroll_path = $theme_path . '/assets/js/smooth-scroll.js';
	$smooth_scroll_uri  = $theme_uri . '/assets/js/smooth-scroll.js';
	$smooth_scroll_ver  = file_exists( $smooth_scroll_path ) ? (string) filemtime( $smooth_scroll_path ) : HC_THEME_VERSION;

	wp_enqueue_script(
		'hypothetical-capital-smooth-scroll',
		$smooth_scroll_uri,
		array( 'lenis', 'gsap-scrolltrigger', 'hypothetical-capital-animations' ),
		$smooth_scroll_ver,
		true
	);

	$highlights_slider_path = $theme_path . '/assets/js/highlights-slider.js';
	$highlights_slider_uri  = $theme_uri . '/assets/js/highlights-slider.js';
	$highlights_slider_ver  = file_exists( $highlights_slider_path ) ? (string) filemtime( $highlights_slider_path ) : HC_THEME_VERSION;

	wp_enqueue_script(
		'hypothetical-capital-highlights-slider',
		$highlights_slider_uri,
		array( 'hypothetical-capital-animations' ),
		$highlights_slider_ver,
		true
	);

	$scroll_path = $theme_path . '/assets/js/scroll.js';
	$scroll_uri  = $theme_uri . '/assets/js/scroll.js';
	$scroll_ver  = file_exists( $scroll_path ) ? (string) filemtime( $scroll_path ) : HC_THEME_VERSION;

	wp_enqueue_script(
		'hypothetical-capital-scroll',
		$scroll_uri,
		array( 'hypothetical-capital-smooth-scroll', 'hypothetical-capital-animations' ),
		$scroll_ver,
		true
	);

	$stats_counter_path = $theme_path . '/assets/js/stats-counter.js';
	$stats_counter_uri  = $theme_uri . '/assets/js/stats-counter.js';
	$stats_counter_ver  = file_exists( $stats_counter_path ) ? (string) filemtime( $stats_counter_path ) : HC_THEME_VERSION;

	wp_enqueue_script(
		'hypothetical-capital-stats-counter',
		$stats_counter_uri,
		array( 'gsap-scrolltrigger', 'hypothetical-capital-animations', 'hypothetical-capital-smooth-scroll' ),
		$stats_counter_ver,
		true
	);

	$hero_intro_path = $theme_path . '/assets/js/hero-intro.js';
	$hero_intro_uri  = $theme_uri . '/assets/js/hero-intro.js';
	$hero_intro_ver  = file_exists( $hero_intro_path ) ? (string) filemtime( $hero_intro_path ) : HC_THEME_VERSION;

	wp_enqueue_script(
		'hypothetical-capital-hero-intro',
		$hero_intro_uri,
		array( 'hypothetical-capital-animations' ),
		$hero_intro_ver,
		true
	);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'hypothetical_capital_scripts' );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * ACF JSON sync and flexible content helpers.
 */
require get_template_directory() . '/inc/acf.php';
require get_template_directory() . '/inc/flexible-content.php';

/**
 * Theme SEO meta tags.
 */
require get_template_directory() . '/inc/seo-meta.php';

function hypothetical_capital_disable_admin_bar_on_mobile( $show ) {
	if ( wp_is_mobile() ) {
		return false;
	}
	return $show;
}
add_filter( 'show_admin_bar', 'hypothetical_capital_disable_admin_bar_on_mobile' );