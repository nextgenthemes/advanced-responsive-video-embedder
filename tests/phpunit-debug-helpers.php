<?php

declare(strict_types = 1);

// phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_var_dump
// phpcs:disable WordPress.WP.AlternativeFunctions
// phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_print_r
// phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_error_log
// phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_var_export

/**
 * Prints the given variables to STDOUT.
 *
 * @param mixed ...$args The variables to print.
 */
function debug( ...$args ): void {

	// if ( is_string( $value )  ) {
	//  ob_start();
	//  var_dump( $value );
	//  $value = ob_get_clean();
	// }

	foreach ( $args as $value ) {
		ob_start();
		var_dump( $value );
		fwrite( STDOUT, ob_get_clean() );
	}
}

/**
 * Writes a log message to a specified file.
 *
 * @param mixed $name The name of the log message.
 * @param mixed $variable The variable to be logged.
 * @param string $file The file path for the log.
 */
function logfile( $name, $variable, string $file ): void {
	// if ( ! is_string( $msg ) ) {
	//  ob_start();
	//  var_dump( $msg );
	//  $msg  = ob_get_clean();
	//  $msg .= PHP_EOL;
	// }
	$msg = "$name " . var_export( $variable, true ) . PHP_EOL;

	error_log( $msg . PHP_EOL, 3, "$file.log" );
}

/**
 * Removes a log file if it exists.
 *
 * @param string $file The name of the log file to be removed
 */
function rm_logfile( string $file ): void {

	$file = "$file.log";

	if ( is_file( $file ) ) {
		unlink( $file );
	}
}
