<?php

declare(strict_types = 1);

namespace Nextgenthemes\ARVE\phpUnit;

use SimpleXMLElement;

$kint = getenv( 'KINT_DEBUG_PLUGIN_FILE' );

if ( $kint ) {
	require_once $kint;
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

/**
 * Returns the list of plugins to activate for the test suite.
 *
 * @return string[] Array of plugin slugs to activate.
 */
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

/**
 * Filters array values that start with a given prefix.
 *
 * @param  array<mixed>  $arr     Array to filter.
 * @param  string        $prefix  Prefix to match.
 * @return array<mixed>           Filtered array containing only values starting with the prefix.
 */
function arr_values_starts_with( array $arr, string $prefix ): array {
	return array_filter( $arr, fn( $value ) => str_starts_with( $value, $prefix ) );
}

/**
 * This PHP function takes a delimiter string as input and converts it into an array.
 * It removes any leading or trailing spaces from each element and filters out any empty
 * elements from the resulting array.
 *
 * @param string   $str       The input comma-separated string
 * @param string   $delimiter The delimiter to use. Space will NOT work!
 * @return array<int,string>  The resulting array
 */
function str_to_array( string $str, string $delimiter = ',' ): array {

	// Trim spaces from each element
	$arr = array_map( 'trim', explode( $delimiter, $str ) );

	// Filter out empty elements
	$arr = array_filter(
		$arr,
		fn ( string $s ): bool => (bool) strlen( $s )
	);

	// Remove duplicate elements
	$arr = array_unique( $arr );

	return $arr;
}

/**
 * Extracts test suite names from the phpunit.xml configuration file.
 *
 * @return string[] Array of test suite names.
 */
function get_testsuites_from_phpunit_config(): array {

	$xml              = new SimpleXMLElement( file_get_contents( dirname( __DIR__ ) . '/phpunit.xml' ) );
	$test_suites      = $xml->testsuites->testsuite;
	$test_suite_names = [];

	foreach ( $test_suites as $test_suite ) {
		$test_suite_names[] = (string) $test_suite->attributes()->name;
	}

	return $test_suite_names;
}
