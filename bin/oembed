#!/usr/bin/env php
<?php
use Symfony\Component\Yaml\Yaml;

require_once dirname( __DIR__ ) . '/vendor/autoload.php';
// phpcs:disable Squiz.PHP.DiscouragedFunctions.Discouraged
// phpcs:disable Squiz.PHP.CommentedOutCode.Found
// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
// phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_var_dump
// phpcs:disable WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

function provider( $provider ) {

	$data      = Yaml::parseFile( getenv( 'HOME' ) . "/dev/build/oembed/providers/$provider.yml" );
	$endpoints = $data[0]['endpoints'][0];

	foreach ( $endpoints['schemes'] as $key => $value ) {

		$url = $endpoints['url'];
		$url = str_replace( 'twitch.tv/v4', 'twitch.tv/v5', $url );
		$url = str_replace( '{format}', 'json', $url );

		echo "wp_oembed_add_provider( '$value', '{$url}' );" . PHP_EOL;
	}

	$example_data = file_get_contents( $endpoints['example_urls'][0] );
	// var_dump( json_decode( $example_data, true ) );
}

foreach ( [
	'twitch',
	'wistia',
	'dtube',
] as $key => $provider ) {
	provider( $provider );
}
