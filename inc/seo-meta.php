<?php
/**
 * Theme SEO meta tags.
 *
 * @package hypothetical_capital
 */

function hypothetical_capital_seo_is_delegated() {
	return defined( 'WPSEO_VERSION' )
		|| class_exists( 'RankMath', false )
		|| defined( 'AIOSEO_VERSION' )
		|| defined( 'SQ_VERSION' );
}

function hypothetical_capital_normalize_meta_text( $text, $word_limit = 30 ) {
	$text = wp_strip_all_tags( (string) $text );
	$text = preg_replace( '/\s+/u', ' ', $text );
	$text = trim( $text );

	if ( '' === $text ) {
		return '';
	}

	return wp_trim_words( $text, $word_limit, '' );
}

function hypothetical_capital_get_flexible_hero_row( $post_id = 0 ) {
	if ( ! function_exists( 'get_field' ) ) {
		return null;
	}

	$post_id = $post_id ? (int) $post_id : get_queried_object_id();
	$rows    = get_field( 'flexible_content', $post_id );

	if ( ! is_array( $rows ) ) {
		return null;
	}

	foreach ( $rows as $row ) {
		if ( ( $row['acf_fc_layout'] ?? '' ) === 'hero' ) {
			return $row;
		}
	}

	return null;
}

function hypothetical_capital_get_flexible_hero_text( $post_id = 0 ) {
	$hero_row = hypothetical_capital_get_flexible_hero_row( $post_id );

	if ( empty( $hero_row['content'] ) ) {
		return '';
	}

	return hypothetical_capital_normalize_meta_text( $hero_row['content'] );
}

function hypothetical_capital_get_meta_description() {
	if ( hypothetical_capital_seo_is_delegated() ) {
		return '';
	}

	$description = '';

	if ( is_singular() && function_exists( 'get_field' ) ) {
		$custom = get_field( 'meta_description' );

		if ( is_string( $custom ) && '' !== trim( $custom ) ) {
			$description = hypothetical_capital_normalize_meta_text( $custom, 35 );
		}
	}

	if ( '' === $description && ( is_front_page() || is_home() ) && function_exists( 'get_field' ) ) {
		$default = get_field( 'default_meta_description', 'option' );

		if ( is_string( $default ) && '' !== trim( $default ) ) {
			$description = hypothetical_capital_normalize_meta_text( $default, 35 );
		}
	}

	if ( '' === $description && is_singular() ) {
		$post = get_queried_object();

		if ( $post instanceof WP_Post ) {
			if ( has_excerpt( $post ) ) {
				$description = hypothetical_capital_normalize_meta_text( get_the_excerpt( $post ) );
			}

			if ( '' === $description ) {
				$description = hypothetical_capital_get_flexible_hero_text( $post->ID );
			}

			if ( '' === $description && ! empty( $post->post_content ) ) {
				$description = hypothetical_capital_normalize_meta_text( $post->post_content );
			}
		}
	}

	if ( '' === $description && ( is_front_page() || is_home() ) ) {
		$description = hypothetical_capital_normalize_meta_text( get_bloginfo( 'description', 'raw' ) );
	}

	if ( '' === $description && is_front_page() ) {
		$front_page_id = (int) get_option( 'page_on_front' );

		if ( $front_page_id ) {
			$description = hypothetical_capital_get_flexible_hero_text( $front_page_id );
		}
	}

	if ( '' === $description && ( is_category() || is_tag() || is_tax() ) ) {
		$term_description = term_description();

		if ( $term_description ) {
			$description = hypothetical_capital_normalize_meta_text( $term_description );
		}
	}

	if ( '' === $description && is_archive() ) {
		$description = hypothetical_capital_normalize_meta_text( get_the_archive_description() );
	}

	if ( '' === $description && is_search() ) {
		$description = hypothetical_capital_normalize_meta_text(
			sprintf(
				__( 'Search results for "%s".', 'hypothetical-capital' ),
				get_search_query()
			)
		);
	}

	if ( '' === $description ) {
		$site_name = get_bloginfo( 'name', 'raw' );
		$tagline   = get_bloginfo( 'description', 'raw' );

		if ( $tagline ) {
			$description = hypothetical_capital_normalize_meta_text( $site_name . ' - ' . $tagline );
		} else {
			$description = hypothetical_capital_normalize_meta_text( $site_name );
		}
	}

	return apply_filters( 'hypothetical_capital_meta_description', $description );
}

function hypothetical_capital_get_canonical_url() {
	if ( is_singular() ) {
		return get_permalink();
	}

	if ( is_front_page() ) {
		return home_url( '/' );
	}

	if ( is_home() ) {
		$posts_page_id = (int) get_option( 'page_for_posts' );

		return $posts_page_id ? get_permalink( $posts_page_id ) : home_url( '/' );
	}

	if ( is_category() || is_tag() || is_tax() ) {
		$term_link = get_term_link( get_queried_object() );

		if ( is_wp_error( $term_link ) ) {
			return '';
		}

		$paged = max( 1, (int) get_query_var( 'paged' ) );

		return $paged > 1 ? add_query_arg( 'paged', $paged, $term_link ) : $term_link;
	}

	return '';
}

function hypothetical_capital_get_og_image_url() {
	if ( is_singular() && has_post_thumbnail() ) {
		$image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large' );

		if ( $image ) {
			return $image[0];
		}
	}

	if ( is_singular() ) {
		$hero_row = hypothetical_capital_get_flexible_hero_row( get_queried_object_id() );

		if ( ! empty( $hero_row['background_image']['url'] ) ) {
			return $hero_row['background_image']['url'];
		}
	}

	if ( is_front_page() ) {
		$front_page_id = (int) get_option( 'page_on_front' );

		if ( $front_page_id ) {
			$hero_row = hypothetical_capital_get_flexible_hero_row( $front_page_id );

			if ( ! empty( $hero_row['background_image']['url'] ) ) {
				return $hero_row['background_image']['url'];
			}
		}
	}

	$logo_id = (int) get_theme_mod( 'custom_logo' );

	if ( $logo_id ) {
		$image = wp_get_attachment_image_src( $logo_id, 'full' );

		if ( $image ) {
			return $image[0];
		}
	}

	if ( function_exists( 'get_field' ) ) {
		$default_image = get_field( 'default_og_image', 'option' );

		if ( ! empty( $default_image['url'] ) ) {
			return $default_image['url'];
		}
	}

	return '';
}

function hypothetical_capital_output_seo_meta_tags() {
	if ( hypothetical_capital_seo_is_delegated() ) {
		return;
	}

	$description = hypothetical_capital_get_meta_description();
	$title       = wp_get_document_title();
	$url         = hypothetical_capital_get_canonical_url();
	$image       = hypothetical_capital_get_og_image_url();
	$type        = is_singular() ? 'article' : 'website';

	if ( $description ) {
		echo '<meta name="description" content="' . esc_attr( $description ) . '">' . "\n";
	}

	echo '<meta property="og:locale" content="' . esc_attr( get_locale() ) . '">' . "\n";
	echo '<meta property="og:type" content="' . esc_attr( $type ) . '">' . "\n";
	echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";

	if ( $description ) {
		echo '<meta property="og:description" content="' . esc_attr( $description ) . '">' . "\n";
	}

	if ( $url ) {
		echo '<meta property="og:url" content="' . esc_url( $url ) . '">' . "\n";
		echo '<link rel="canonical" href="' . esc_url( $url ) . '">' . "\n";
	}

	echo '<meta property="og:site_name" content="' . esc_attr( get_bloginfo( 'name' ) ) . '">' . "\n";

	if ( $image ) {
		echo '<meta property="og:image" content="' . esc_url( $image ) . '">' . "\n";
		echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
		echo '<meta name="twitter:image" content="' . esc_url( $image ) . '">' . "\n";
	} else {
		echo '<meta name="twitter:card" content="summary">' . "\n";
	}

	echo '<meta name="twitter:title" content="' . esc_attr( $title ) . '">' . "\n";

	if ( $description ) {
		echo '<meta name="twitter:description" content="' . esc_attr( $description ) . '">' . "\n";
	}
}
add_action( 'wp_head', 'hypothetical_capital_output_seo_meta_tags', 1 );
