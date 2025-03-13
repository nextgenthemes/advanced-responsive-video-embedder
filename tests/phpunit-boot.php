<?php

declare(strict_types = 1);

namespace Nextgenthemes\ARVE\phpUnit;

use SimpleXMLElement;

if ( is_file( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
}

require_once __DIR__ . '/phpunit-debug-helpers.php';

$_tests_dir = getenv( 'WP_TESTS_DIR' );

if ( ! $_tests_dir ) {
	$_tests_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';
}

// Forward custom PHPUnit Polyfills configuration to PHPUnit bootstrap file.
$_phpunit_polyfills_path = getenv( 'WP_TESTS_PHPUNIT_POLYFILLS_PATH' );
if ( false !== $_phpunit_polyfills_path ) {
	define( 'WP_TESTS_PHPUNIT_POLYFILLS_PATH', $_phpunit_polyfills_path );
}

if ( ! file_exists( "{$_tests_dir}/includes/functions.php" ) ) {
	echo "Could not find {$_tests_dir}/includes/functions.php, have you run bin/install-wp-tests.sh ?" . PHP_EOL; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	exit( 1 );
}

// Give access to tests_add_filter() function.
require_once "{$_tests_dir}/includes/functions.php";

tests_add_filter( 'muplugins_loaded', __NAMESPACE__ . '\manually_load_plugins' );

// Start up the WP testing environment.
require "{$_tests_dir}/includes/bootstrap.php";

function manually_load_plugins(): void {

	require_once dirname( __DIR__, 2 ) . '/arve-pro/tests/phpunit-helpers.php';

	activate_arve_addons();

	$active_plugins = plugins_to_activate();

	debug( 'Plugins to activate', $active_plugins );

	define( 'NGT_PHPUNIT_ACTIVE_PLUGINS', $active_plugins );

	foreach ( $active_plugins as $slug ) {

		$file = dirname( __DIR__, 2 ) . "/$slug/$slug.php";

		if ( ! is_file( $file ) ) {
			echo "Plugin $slug not found" . PHP_EOL; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			exit( 1 );
		}

		if ( str_starts_with( $slug, 'arve-' ) ) {
			$GLOBALS['arve_detected_addons'][] = $slug;
		}

		require_once $file;
	}
}

function plugins_to_activate(): array {

	$plugins = getenv( 'NGT_PHPUNIT_LOAD_PLUGINS' );

	if ( empty( $plugins ) ) {
		return get_testsuites_from_phpunit_config();
	}

	$plugins = str_to_array( $plugins );

	// For ARVE addons also activate the ARVE
	if ( arr_values_starts_with( $plugins, 'arve-' )
		&& ! in_array( 'advanced-responsive-video-embedder', $plugins, true )
	) {
		$plugins[] = 'advanced-responsive-video-embedder';
	}

	return $plugins;
}

function arr_values_starts_with( array $arr, string $prefix ): array {
	return array_filter( $arr, fn( $value ) => str_starts_with( $value, $prefix ) );
}

/**
 * Converts a comma-separated string into an array.
 *
 * Each element in the resulting array is first trimmed of any leading or trailing spaces.
 * The array is then filtered to remove any empty elements.
 * Finally, the resulting array is optionally made unique.
 *
 * @param string $str The input comma-separated string
 * @param bool $unique Whether or not to make the resulting array unique. Defaults to true.
 * @return array The resulting array
 */
function str_to_array( string $str, bool $unique = true ): array {
	$array = array_map( 'trim', explode( ',', $str ) );
	$array = array_filter( $array, 'strlen' );

	if ( $unique ) {
		$array = array_unique( $array );
	}

	return $array;
}

function get_testsuites_from_phpunit_config(): array {

	$xml              = new SimpleXMLElement( file_get_contents( dirname( __DIR__ ) . '/phpunit.xml' ) );
	$test_suites      = $xml->testsuites->testsuite;
	$test_suite_names = [];

	foreach ( $test_suites as $test_suite ) {
		$test_suite_names[] = (string) $test_suite->attributes()->name;
	}

	return $test_suite_names;
}
