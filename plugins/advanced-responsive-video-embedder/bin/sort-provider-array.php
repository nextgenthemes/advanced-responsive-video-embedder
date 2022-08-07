#!/usr/bin/env php
<?php
namespace Nextgenthemes\ARVE;

// phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_var_export
// phpcs:disable WordPress.WP.AlternativeFunctions.file_system_read_file_put_contents
// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
// phpcs:disable WordPress.PHP.DiscouragedPHPFunctions.system_calls_system
function __( string $a, string $b ): string {
	return $a;
}
function varexport($expression, $return = false) {
	$export = var_export($expression, true);
	$export = preg_replace('/^([ ]*)(.*)/m', '$1$1$2', $export);
	$array  = preg_split("/\r\n|\n|\r/", $export);
	$array  = preg_replace([ '/\s*array\s\($/', '/\)(,)?$/', '/\s=>\s$/' ], [ null, ']$1', ' => [' ], $array);
	$export = join(PHP_EOL, array_filter([ '[' ] + $array));
	$export = join(PHP_EOL, array_filter([ '[' ] + $array));
	$export = str_replace('    ', "\t", $export);
	if ( $return ) {
		return $export;
	} else {
		echo $export;
	}
}

$file  = dirname( __DIR__ ) . '/php/providers.php';
$hosts = require $file;
ksort( $hosts );

$content = '<?php return ' . varexport( $hosts, true ) . ';';
file_put_contents( $file, $content );

system( "phpcbf $file" );
