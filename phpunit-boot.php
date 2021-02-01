<?php
/**
 * PHPUnit bootstrap file
 *
 * @package Arve_Pro
 */
require_once __DIR__ . '/phpunit-debug-helpers.php';

$_tests_dir = getenv( 'WP_TESTS_DIR' );

if ( ! $_tests_dir ) {
	$_tests_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';
}

if ( ! file_exists( $_tests_dir . '/includes/functions.php' ) ) {
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo "Could not find $_tests_dir/includes/functions.php, have you run bin/install-wp-tests.sh ?" . PHP_EOL;
	exit( 1 );
}

// Give access to tests_add_filter() function.
require_once $_tests_dir . '/includes/functions.php';

/**
 * Manually load the plugin being tested.
 */
function _manually_load_plugin() {

	require __DIR__ . '/advanced-responsive-video-embedder/advanced-responsive-video-embedder.php';
	require __DIR__ . '/arve-random-video/tests/activate.php';

	foreach ( glob('arve-*', GLOB_ONLYDIR ) as $dirname ) {
		require __DIR__ . "/$dirname/$dirname.php";
		$GLOBALS['arve_detected_addons'][] = $dirname;
	}
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

// Start up the WP testing environment.
require $_tests_dir . '/includes/bootstrap.php';
