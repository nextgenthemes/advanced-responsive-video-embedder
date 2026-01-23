#!/usr/bin/env php
<?php

declare(strict_types = 1);

namespace Nextgenthemes\ARVE;

$root = dirname( __DIR__ );
require_once $root . '/php/providers.php';
require_once $root . '/php/fn-misc.php';
require_once __DIR__ . '/fn-common-shell.php';

echo 'Building Readme ...' . PHP_EOL;

update_tested_with_wp_version( $root );
write_supported_providers_md( $root );

cmd( "cat $root/readme/*.md $root/changelog.md > $root/readme.txt" );

convert_description_ext_md_to_html( $root );

replace_in_file(
	"$root/php/Admin/partials/settings-sidebar-pro.html",
	'<code>wporg</code>',
	'<code>settingspage</code>'
);

function replace_in_file( string $file, string $str, string $rep ): void {
	$file_content = file_get_contents( $file );
	$file_content = str_replace( $str, $rep, $file_content );
	file_put_contents( $file, $file_content );
}

function update_tested_with_wp_version( string $root ): void {

	$wp_version_json = file_get_contents( 'https://api.wordpress.org/core/version-check/1.7/' );

	if ( false === $wp_version_json ) {
		echo 'Failed to fetch WordPress version from API.' . PHP_EOL;
		return;
	}

	$wp_data = json_decode( $wp_version_json, true );

	if ( ! isset( $wp_data['offers'][0]['version'] ) ) {
		echo 'Failed to parse WordPress version from API response.' . PHP_EOL;
		return;
	}

	$latest_wp_version = $wp_data['offers'][0]['version'];
	$header_file       = $root . '/readme/01-header.md';
	$header_content    = file_get_contents( $header_file );
	$updated_content   = preg_replace(
		'/^Tested up to:\s*[\d.]+$/m',
		'Tested up to: ' . $latest_wp_version,
		$header_content
	);

	file_put_contents( $header_file, $updated_content );
	echo "Updated WordPress version to: $latest_wp_version" . PHP_EOL;
}

function write_supported_providers_md( string $root ): void {

	$list = [];

	foreach ( PROVIDERS as $key => $value ) {
		$list[] = $value['name'];
	}

	$md  = "\n#### Supported Providers ####\n\n";
	$md .= "[All providers with iframe embed codes](https://nextgenthemes.com/plugins/arve/documentation/#general-iframe-embedding)\n";
	$md .= strip_tags( implode( ', ', $list ) );

	file_put_contents( $root . '/readme/13-description-supported-providers.md', $md );
}

function convert_description_ext_md_to_html( string $root ): void {

	foreach ( glob( $root . '/readme/*description-ext-*.md' ) as $filename ) {

		\preg_match( '/description-ext-([^.]+)/', basename( $filename ), $matches );

		$html_filename = 'settings-sidebar-' . $matches[1] . '.html';
		cmd( "markdown $filename > $root/php/Admin/partials/$html_filename" );
	}
}
