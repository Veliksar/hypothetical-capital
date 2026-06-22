<?php
/**
 * ACF configuration.
 *
 * @package hypothetical_capital
 */

/**
 * Set the local JSON save path for ACF field groups.
 *
 * @return string
 */
function hypothetical_capital_acf_json_save_path() {
	return get_template_directory() . '/acf-json';
}
add_filter( 'acf/settings/save_json', 'hypothetical_capital_acf_json_save_path' );

/**
 * Load ACF field groups only from the theme JSON directory.
 *
 * @param array $paths Existing load paths.
 * @return array
 */
function hypothetical_capital_acf_json_load_paths( $paths ) {
	return array( get_template_directory() . '/acf-json' );
}
add_filter( 'acf/settings/load_json', 'hypothetical_capital_acf_json_load_paths' );

/**
 * Align JSON modified timestamps with the database after a successful sync.
 *
 * ACF shows "Sync available" while JSON modified is greater than the DB post
 * modified time. Manual sync does not rewrite the JSON file, so inflated JSON
 * timestamps would otherwise leave the status stuck on "Sync available".
 *
 * @return void
 */
function hypothetical_capital_acf_align_json_modified_with_database() {
	if ( ! is_admin() || ! function_exists( 'acf_get_field_group' ) || ! function_exists( 'acf_json_encode' ) ) {
		return;
	}

	$json_dir = get_template_directory() . '/acf-json';

	if ( ! is_dir( $json_dir ) ) {
		return;
	}

	$json_files = glob( $json_dir . '/*.json' );

	if ( ! $json_files ) {
		return;
	}

	foreach ( $json_files as $file ) {
		$json_group = json_decode( file_get_contents( $file ), true );

		if ( empty( $json_group['key'] ) ) {
			continue;
		}

		$db_group = acf_get_field_group( $json_group['key'] );

		if ( empty( $db_group['ID'] ) ) {
			continue;
		}

		$db_modified   = (int) get_post_modified_time( 'U', true, $db_group['ID'] );
		$json_modified = (int) ( $json_group['modified'] ?? 0 );

		if ( $json_modified <= $db_modified ) {
			continue;
		}

		$json_group['modified'] = $db_modified;
		file_put_contents( $file, acf_json_encode( $json_group ) . PHP_EOL );
	}
}
add_action( 'acf/init', 'hypothetical_capital_acf_align_json_modified_with_database', 99 );
