<?php

declare(strict_types = 1);

// phpcs:disable WordPress.PHP.DevelopmentFunctions
// phpcs:disable WordPress.WP.AlternativeFunctions

/**
 * Prints the given variables to STDOUT.
 *
 * @param mixed ...$args The variables to print.
 */
function debug( ...$args ): void {

	foreach ( $args as $value ) {
		ob_start();
		var_dump( $value );
		fwrite( STDOUT, ob_get_clean() );
	}
}

/**
 * Log a variable to a file.
 *
 * @param mixed  $data     The variable to log.
 */
function logfile( ...$data ): void {

	$backtrace = debug_backtrace();
	$caller    = $backtrace[1];
	$msg       = $caller['line'] . ' ';

	foreach ( $data as $value ) {
		$msg .= var_export( $value, true ) . ' ';
	}

	$msg .= PHP_EOL . PHP_EOL;
	error_log( $msg . PHP_EOL, 3, $caller['file'] . '.log' );
}
