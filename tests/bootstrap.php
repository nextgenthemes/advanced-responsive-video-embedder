<?php

// phpcs:disable WordPress.WP.GlobalVariablesOverride.Prohibited
$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
$_SERVER['SERVER_NAME']     = '';
$PHP_SELF                   = '/index.php';
$GLOBALS['PHP_SELF']        = '/index.php';
$_SERVER['PHP_SELF']        = '/index.php';

$_tests_dir = getenv( 'WP_TESTS_DIR' );
if ( ! $_tests_dir ) {
	$_tests_dir = '/tmp/wordpress-tests-lib';
}

require_once $_tests_dir . '/includes/functions.php';

function ci_manually_load_plugin() {
	# /home/travis/build/nextgenthemes/advanced-responsive-video-embedder
	require dirname( __DIR__ ) . '/advanced-responsive-video-embedder.php';
}
tests_add_filter( 'muplugins_loaded', 'ci_manually_load_plugin' );

require $_tests_dir . '/includes/bootstrap.php';

activate_plugin( 'advanced-responsive-video-embedder/advanced-responsive-video-embedder.php' );

$GLOBALS['current_user'] = new WP_User( 1 );
$GLOBALS['current_user']->set_role( 'administrator' );
wp_update_user(
	[
		'ID'         => 1,
		'first_name' => 'Admin',
		'last_name'  => 'User',
	]
);

// List of name of files inside
// specified folder
$files = glob( \Nextgenthemes\ARVE\PLUGIN_DIR . '/php/*.log' );

// Deleting all the files in the list
foreach ( $files as $file ) {
	if (is_file($file)) {
		unlink($file);
	}
}

function logfile( $name, $debug_var, $file ) {

	// if ( ! is_string( $debug_var ) ) {
	// 	ob_start();
	// 	var_dump( $debug_var );
	// 	$debug_var  = ob_get_clean();
	// 	$debug_var .= PHP_EOL;
	// }
	$log  = "$name ";
	$log .= var_export( $debug_var, true ) . PHP_EOL; //phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_var_export

	error_log( $log, 3, "$file.log" ); //phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
}
