<?php

declare(strict_types = 1);

namespace Nextgenthemes\ARVE;

use Nextgenthemes\WP\Settings;
use Nextgenthemes\WP\SettingsData;
use function Nextgenthemes\WP\nextgenthemes_settings_instance;

/**
 * This is used to initialize the settings without
 * returning the instance for using it on the init action.
 * Actions are not supposed to return values.
 */
function create_settings_instance(): void {
	settings_instance();
}

function settings_instance(): Settings {

	static $instance = null;

	if ( null === $instance ) {

		$instance = new Settings(
			array(
				'namespace'           => __NAMESPACE__,
				'settings'            => settings( 'settings_page' ),
				'tabs'                => settings_tabs(),
				'menu_title'          => __( 'ARVE', 'advanced-responsive-video-embedder' ),
				'settings_page_title' => __( 'ARVE Settings', 'advanced-responsive-video-embedder' ),
				'plugin_file'         => PLUGIN_FILE,
				'base_url'            => plugins_url( '', PLUGIN_FILE ),
				'base_path'           => PLUGIN_DIR,
			)
		);
	}

	return $instance;
}

/**
 * @return array <string, bool|string|int>
 */
function options(): array {
	return settings_instance()->get_options();
}

/**
 * @return array <string, bool|string|int>
 */
function default_options(): array {
	return settings_instance()->get_options_defaults();
}

/**
 * @return array <string, array<string, string|false>>
 */
function settings_tabs(): array {

	return array(
		'main' => [
			'title' => __( 'Main', 'advanced-responsive-video-embedder' ),
		],
		'pro' => [
			'title'        => __( 'Pro', 'advanced-responsive-video-embedder' ),
			'premium_link' => sprintf(
				'<a href="%s">%s</a>',
				'https://nextgenthemes.com/plugins/arve-pro/',
				__( 'Pro Addon', 'advanced-responsive-video-embedder' )
			),
		],
		'privacy' => [
			'title'        => __( 'Extra Privacy', 'advanced-responsive-video-embedder' ),
			'premium_link' => sprintf(
				'<a href="%s">%s</a>',
				'https://nextgenthemes.com/plugins/arve-privacy/',
				__( 'Privacy Addon', 'advanced-responsive-video-embedder' )
			),
		],
		'sticky_videos' => [
			'title'        => __( 'Sticky Videos', 'advanced-responsive-video-embedder' ),
			'premium_link' => sprintf(
				'<a href="%s">%s</a>',
				'https://nextgenthemes.com/plugins/arve-sticky-videos/',
				__( 'Sticky Videos Addon', 'advanced-responsive-video-embedder' )
			),
		],
		'random_video' => [
			'title'        => __( 'Random Video', 'advanced-responsive-video-embedder' ),
			'premium_link' => sprintf(
				'<a href="%s">%s</a>',
				'https://nextgenthemes.com/plugins/arve-random-video/',
				__( 'Random Videos Addon', 'advanced-responsive-video-embedder' )
			),
			'reset_button' => false,
		],
		'urlparams' => [
			'title' => __( 'URL Parameters', 'advanced-responsive-video-embedder' ),
		],
		'html5' => [
			'title' => __( 'Video Files', 'advanced-responsive-video-embedder' ),
		],
		'debug' => [
			'title' => __( 'Debug', 'advanced-responsive-video-embedder' ),
		],
	);
}

function init_nextgenthemes_settings(): void {

	nextgenthemes_settings_instance(
		plugins_url( '', PLUGIN_FILE ),
		PLUGIN_DIR
	);
}

function settings( string $context = 'settings_page' ): SettingsData {

	$settings = settings_data();

	if ( in_array( $context, array( 'gutenberg_block', 'shortcode' ), true ) ) {

		foreach ( $settings->get_all() as $k => $s ) {
			if ( ! $s->shortcode ) {
				$settings->remove( $k );
				continue;
			}

			if ( 'boolean' === $s->type && $s->option ) {
				$s->bool_option_to_select();
			}
		}
	}

	switch ( $context ) {
		case 'gutenberg_block':
			$settings->remove( 'maxwidth' );
			break;
		case 'settings_page':
			foreach ( $settings->get_all() as $k => $s ) {
				if ( ! $s->option ) {
					$settings->remove( $k );
				}
			}
			break;
	}

	return $settings;
}

function get_arg_type( string $arg_name ): ?string {

	$setting = settings_data()->get( $arg_name );

	if ( ! $setting ) {
		return null;
	}

	switch ( $setting->type ) {
		case 'string':
			return 'string';
		case 'boolean':
			return 'bool';
		case 'integer':
			return 'int';
		default:
			return null;
	}
}

function settings_data(): SettingsData {

	$settings = array_merge(
		SettingsDefinitions::main_settings(),
		SettingsDefinitions::html5_settings(),
		SettingsDefinitions::random_video_settings(),
		SettingsDefinitions::pro_settings(),
		SettingsDefinitions::privacy_settings(),
		SettingsDefinitions::url_params_settings(),
		SettingsDefinitions::sticky_settings(),
		SettingsDefinitions::debug_settings(),
	);

	$order = [
		'url'                           => 'required',
		'random_video_url'              => 'required',
		'random_video_urls'             => 'required',
		'loop'                          => '',
		'muted'                         => '',
		'controls'                      => '',
		'parameters'                    => '',
		'credentialless'                => '',
		'title'                         => 'data',
		'description'                   => 'data',
		'upload_date'                   => 'data',
		'thumbnail'                     => 'visual',
		'align'                         => '',
		'arve_link'                     => 'data',
		'duration'                      => 'data',
		'autoplay'                      => '',
		'seo_data'                      => 'data',
		'maxwidth'                      => '',
		'align_maxwidth'                => '',
		'aspect_ratio'                  => '',
		'mode'                          => 'visual',
		'lazyload_style'                => 'visual',
		'thumbnail_fallback'            => 'visual',
		'thumbnail_post_image_fallback' => 'visual',
		'hide_title'                    => 'visual',
		'grow'                          => 'visual',
		'fullscreen'                    => 'visual',
		'play_icon_style'               => 'visual',
		'hover_effect'                  => 'visual',
		'lightbox_maxwidth'             => 'visual',
		'disable_links'                 => 'functional',
		'volume'                        => 'functional',
		'reset_after_played'            => '',
		'lightbox_aspect_ratio'         => 'visual',
		'invidious'                     => 'privacy',
		'invidious_instance'            => 'privacy',
		'invidious_parameters'          => 'privacy',
		'cache_thumbnails'              => 'privacy',
		'encrypted_media'               => 'privacy',
		'allow_referrer'                => 'privacy',
		'sticky'                        => 'sticky_videos',
		'sticky_width'                  => 'sticky_videos',
		'sticky_max_width'              => 'sticky_videos',
		'sticky_gap'                    => 'sticky_videos',
		'sticky_navbar_selector'        => 'sticky_videos',
		'sticky_on_mobile'              => 'sticky_videos',
		'sticky_position'               => 'sticky_videos',
		'controlslist'                  => 'functional',
		'show_src_mismatch_errors'      => '',
		'vimeo_api_id'                  => '',
		'vimeo_api_secret'              => '',
		'youtube_nocookie'              => 'functional',
		'wp_video_override'             => 'wordpress',
		'always_enqueue_assets'         => 'wordpress',
		'admin_bar_menu'                => 'wordpress',
		'feed'                          => 'wordpress',
		'gutenberg_help'                => 'wordpress',
		'vimeo_api_token'               => '',
		'youtube_data_api_key'          => 'data',
		'legacy_shortcodes'             => 'wordpress',
	];

	$order = array_merge( $order, SettingsDefinitions::url_params_settings() );

	$settings_keys    = array_keys( $settings );
	$order_keys       = array_keys( $order );
	$missing_in_order = array_diff( $settings_keys, $order_keys );

	if ( ! empty( $missing_in_order ) ) {
		wp_trigger_error( __FUNCTION__, 'Missing keys in order array: ' . implode( ', ', $missing_in_order ) );
	}

	foreach ( $settings as $key => $setting ) {
		if ( ! isset( $order[ $key ] ) ) {
			wp_trigger_error( __FUNCTION__, "Key {$key} not found in order array" );
		}

		$settings[ $key ]['category'] = $order[ $key ];
	}

	$settings = new SettingsData( $settings, true );

	return $settings;
}
