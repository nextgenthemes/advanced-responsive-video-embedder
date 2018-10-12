#!/usr/bin/env php
<?php
use Symfony\Component\Yaml\Yaml;

require_once dirname( __DIR__ ) . '/vendor/autoload.php';
// phpcs:disable Squiz.PHP.DiscouragedFunctions.Discouraged
// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
// phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_var_dump
function provider( $provider ) {

	$data      = Yaml::parseFile( getenv( 'HOME' ) . "/dev/oembed/providers/$provider.yml" );
	$endpoints = $data[0]['endpoints'][0];

	foreach ( $endpoints['schemes'] as $key => $value ) {
		echo "\twp_oembed_add_provider( '$value', '{$endpoints['url']}' );" . PHP_EOL;
	}

	$example_data = file_get_contents( $endpoints['example_urls'][0] );
	// var_dump( json_decode( $example_data, true ) );
}

$providers = [
	'twitch',
	'ustream',
];

foreach ( $providers as $key => $provider ) {
	provider( $provider );
}
