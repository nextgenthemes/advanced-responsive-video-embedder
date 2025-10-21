#!/usr/bin/env php
<?php

declare(strict_types = 1);

/**
 * Executes a system command with optional arguments.
 *
 * @param string $command The system command to execute
 * @return string The output of the system command
 */
function run_cmd( string $command ): string {

	$GLOBALS['verbose'] = $GLOBALS['verbose'] ?? true;

	if ( $GLOBALS['verbose'] ) {
		echo "Executing: $command" . PHP_EOL;
		$out = system( $command, $exit_code );
	} else {
		$out = exec( $command, $unused_output, $exit_code );
	}

	if ( 0 !== $exit_code || false === $out ) {
		echo "Exit Code: $exit_code" . PHP_EOL;
		exit( $exit_code );
	}

	return $out;
}

function cmd( string $command, string ...$values ): string {

	foreach ( $values as &$value ) {
		$value = escapeshellarg( $value );
	}

	if ( 0 === count( $values ) ) {
		return run_cmd( $command );
	} else {
		return run_cmd( sprintf( $command, ...$values ) );
	}
}

/**
 * Creates a string of long command line arguments from an array.
 *
 * Example: --post_type=page --post_title="Hello World"
 *
 * @param array<string, string|int> $args
 */
function cmd_args( array $args ): string {

	$out = '';

	foreach ( $args as $k => $v ) {
		$out .= " --$k=" . escapeshellarg( (string) $v );
	}

	return $out;
}

function bootstrap_wp(): void {
	// Simulate server vars for CLI
	$_SERVER['HTTP_HOST']   = 'localhost';
	$_SERVER['SERVER_NAME'] = 'localhost';

	// Bootstrap WordPress
	define( 'WP_USE_THEMES', false );
	define( 'ABSPATH', getenv( 'WP_CORE_DIR' ) . '/' );

	require_once ABSPATH . 'wp-load.php';
}
