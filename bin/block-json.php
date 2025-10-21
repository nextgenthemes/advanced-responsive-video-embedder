#!/usr/bin/env php
<?php

declare(strict_types = 1);

use function Nextgenthemes\ARVE\settings;
use const Nextgenthemes\ARVE\VIEW_SCRIPT_HANDLES;
use const Nextgenthemes\ARVE\VERSION;

init();

function init(): void {
	require_once __DIR__ . '/fn-common-shell.php';
	require_once dirname( __DIR__ ) . '/vendor/nextgenthemes/wp-settings/includes/WP/SettingsData.php';
	require_once dirname( __DIR__ ) . '/vendor/nextgenthemes/wp-settings/includes/WP/SettingValidator.php';
	require_once dirname( __DIR__ ) . '/vendor/nextgenthemes/wp-settings/includes/WP/fn-settings.php';
	require_once dirname( __DIR__ ) . '/advanced-responsive-video-embedder.php';
	require_once dirname( __DIR__ ) . '/php/fn-misc.php';
	require_once dirname( __DIR__ ) . '/php/fn-settings.php';

	bootstrap_wp();

	echo 'Updating block.json...' . PHP_EOL;

	$settings = settings( 'gutenberg_block' )->get_all();

	foreach ( $settings as $key => $setting ) {

		$attr[ $key ] = array(
			'type' => $setting->type,
		);
	}

	$attr['thumbnail_url'] = array( 'type' => 'string' );

	update_block_json( $attr );
}

/**
 * Updates the block.json file with attributes and configuration.
 *
 * Reads the existing block.json file, updates it with the provided attributes,
 * editor styles, view scripts, view styles, and version, then writes it back.
 *
 * @param  array<string, array<string, string>>  $attr  Block attributes to update.
 */
function update_block_json( array $attr ): void {

	$file = dirname( __DIR__ ) . '/src/block.json';
	$json = file_get_contents( $file );

	try {
		$json = json_decode( $json, true, 15, JSON_THROW_ON_ERROR );
	} catch ( \JsonException $exception ) {
		die( esc_html( $exception->getMessage() ) );
	}

	if ( empty( $json ) ) {
		die( esc_html( 'Empty JSON' ) );
	}

	$json['attributes']  = $attr;
	$json['editorStyle'] = array_merge( array( 'arve-block' ), VIEW_SCRIPT_HANDLES );
	$json['viewScript']  = VIEW_SCRIPT_HANDLES;
	$json['viewStyle']   = VIEW_SCRIPT_HANDLES;
	$json['version']     = VERSION;

	file_put_contents( $file, json_encode( $json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) );
}
