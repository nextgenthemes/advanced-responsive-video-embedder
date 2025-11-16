<?php

declare(strict_types = 1);

namespace Nextgenthemes\ARVE;

use WP_CLI;
use WP_CLI_Command;
use function Nextgenthemes\WP\str_contains_any;
use function WP_CLI\Utils\format_items;
use function WP_CLI\Utils\get_flag_value;

class CLI extends WP_CLI_Command {

	/**
	 * Display raw settings data from settings_data() function in a table.
	 *
	 * @when after_wp_load
	 *
	 * @param array<string, mixed> $args
	 * @param array<string, mixed> $assoc_args
	 */
	public function raw_settings( array $args, array $assoc_args ): void {
		$raw_settings = settings_data()->to_array();
		$items        = [];

		// Transform raw settings into table rows
		foreach ( $raw_settings as $setting ) {

			$sc_no_option = $setting['shortcode'] && ! $setting['option'];

			if ( ! empty( $assoc_args['sc-no-option'] ) && ! $sc_no_option ) {
				continue;
			}

			foreach ( $setting as $s_key => $s_value ) {
				$setting[ $s_key ] = is_bool( $s_value ) ? ( $s_value ? 'true' : 'false' ) : $s_value;
			}

			$items[] = $setting;
		}

		// Sort items by key
		usort(
			$items,
			function ( $a, $b ) {
				return strcasecmp( $a['option_key'], $b['option_key'] );
			}
		);

		$exclude = ! empty( $assoc_args['exclude'] ) ?
			array_map( 'trim', explode( ',', $assoc_args['exclude'] ) )
			: [];

		if ( ! empty( $exclude ) ) {
			$items = array_filter(
				$items,
				function ( $item ) use ( $exclude ) {
					return ! str_contains_any( $item['option_key'], $exclude );
				}
			);
		}

		// Get fields to display
		$default_fields = [ 'option_key', 'type', 'shortcode', 'option' ];
		$fields         = ! empty( $assoc_args['fields'] )
			? array_map( 'trim', explode( ',', $assoc_args['fields'] ) )
			: $default_fields;

		// Use WP_CLI format_items
		format_items(
			get_flag_value( $assoc_args, 'format', 'table' ),
			$items,
			$fields
		);
	}

	/**
	 * Outputs block.json with latest data from the plugin.
	 */
	public function block_json(): void {
		$settings = settings( 'gutenberg_block' )->get_all();

		foreach ( $settings as $key => $setting ) {

			$attr[ $key ] = array(
				'type' => $setting->type,
			);
		}

		$attr['thumbnail_url'] = array( 'type' => 'string' );

		$json = $this->update_block_json( $attr );

		$this->pretty_json_output( $json );
	}

	/**
	 * Updates block.json data with latest data from the plugin.
	 *
	 * @param  array<string, mixed>  $attr  Block attributes to update.
	 *
	 * @return array<string, mixed>         Updated block.json data.
	 */
	private static function update_block_json( array $attr ): array {

		$file = PLUGIN_DIR . '/src/block/block.json';
		$json = file_get_contents( $file );

		try {
			$json = json_decode( $json, true, 15, JSON_THROW_ON_ERROR );
		} catch ( \JsonException $exception ) {
			WP_CLI::error( $exception->getMessage() );
		}

		if ( empty( $json ) ) {
			WP_CLI::error( 'Empty JSON' );
		}

		$json['version']          = VERSION;
		$json['attributes']       = $attr;
		$json['editorStyle']      = array_merge( [ 'file:./index.css' ], VIEW_SCRIPT_HANDLES );
		$json['viewScript']       = VIEW_SCRIPT_HANDLES;
		$json['viewScriptModule'] = VIEW_SCRIPT_HANDLES;
		$json['viewStyle']        = VIEW_SCRIPT_HANDLES;

		return $json;
	}

	/**
	 * @param array<array-key, mixed> $data
	 */
	private static function pretty_json_output( array $data ): void {
		$json = wp_json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
		WP_CLI::line( $json );
	}

	public function delete_oembed_cache(): void {
		WP_CLI::line( delete_oembed_cache() );
	}
}
