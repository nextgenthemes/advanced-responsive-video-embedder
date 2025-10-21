#!/usr/bin/env php
<?php

declare(strict_types = 1);

namespace Nextgenthemes\ARVE;

// phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_var_export
// phpcs:disable WordPress.WP.AlternativeFunctions.file_system_read_file_put_contents
// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
// phpcs:disable WordPress.PHP.DiscouragedPHPFunctions.system_calls_system
// phpcs:disable Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
// phpcs:disable WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents

function __( string $a, string $b ): string {
	return $a;
}

/**
 * Converts a PHP expression into a string representation with a custom format.
 *
 * This function mimics the behavior of var_export() but applies a custom
 * formatting to the output. It formats arrays using square brackets and
 * adjusts indentation to use tabs instead of spaces.
 *
 * @param mixed $expression The expression to be exported.
 * @return string           The formatted export string.
 */
function pretty_var_export( $expression ): string {
	$export = var_export( $expression, true );
	$export = preg_replace( '/^([ ]*)(.*)/m', '$1$1$2', $export );
	$arr    = preg_split( "/\r\n|\n|\r/", $export );
	$arr    = preg_replace(
		[ '/\s*array\s\($/', '/\)(,)?$/', '/\s=>\s$/' ],
		[ '', ']$1', ' => [' ],
		$arr
	);
	$export = join( PHP_EOL, array_filter( [ '[' ] + $arr ) );
	$export = str_replace( '    ', "\t", $export );

	return $export;
}

$file  = dirname( __DIR__ ) . '/php/providers.php';
$hosts = require $file;
ksort( $hosts );

$content = '<?php return ' . pretty_var_export( $hosts ) . ';';
file_put_contents( $file, $content );

system( "phpcbf $file" );
