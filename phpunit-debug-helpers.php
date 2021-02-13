<?php
// phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_var_dump
// phpcs:disable WordPress.WP.AlternativeFunctions.file_system_read_fwrite
// phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_error_log
// phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_var_export

function logfile( $name, $var, $file ) {
	// if ( ! is_string( $msg ) ) {
	// 	ob_start();
	// 	var_dump( $msg );
	// 	$msg  = ob_get_clean();
	// 	$msg .= PHP_EOL;
	// }
	$msg = "$name " . var_export( $var, true ) . PHP_EOL;

	error_log( $msg . PHP_EOL, 3, "$file.log" );
}

function rm_logfile( $file ) {

	$file = "$file.log";

	if ( is_file( $file ) ) {
		unlink( $file );
	}
}

function pd( $var ) {

	ob_start();
	var_dump( $var );

	fwrite( STDOUT, ob_get_clean() . PHP_EOL );
}
