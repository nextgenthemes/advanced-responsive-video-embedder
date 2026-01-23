<?php

declare(strict_types = 1);

namespace Nextgenthemes\ARVE;

use const Nextgenthemes\ARVE\ALLOWED_HTML;

/**
 * Processes the shortcode attributes and builds the video html.
 *
 * @param array <string, mixed> $a The array of shortcode attributes.
 *
 * @return string|\WP_REST_Response The generated video output.
 */
function shortcode( array $a ) {

	$a['origin_data'][ __FUNCTION__ ]['start'] = 'start';

	foreach ( $a as $k => $v ) {
		if ( '' === $v ) {
			unset( $a[ $k ] );
		}
	}

	$override = apply_filters( 'nextgenthemes/arve/shortcode_override', '', $a, 'not used' );

	if ( '' !== $override ) {
		return $override;
	}

	$a = apply_filters( 'nextgenthemes/arve/shortcode_args', $a );

	if ( ! empty( $a['url'] ) ) {

		remove_filter( 'embed_oembed_html', __NAMESPACE__ . '\filter_embed_oembed_html', OEMBED_HTML_PRIORITY );
		$maybe_arve_html = $GLOBALS['wp_embed']->shortcode( array(), $a['url'] );
		add_filter( 'embed_oembed_html', __NAMESPACE__ . '\filter_embed_oembed_html', OEMBED_HTML_PRIORITY, 4 );

		$oembed_data = extract_oembed_data( $maybe_arve_html );

		if ( $oembed_data ) {
			$a['oembed_data'] = $oembed_data;

			$a['origin_data'][ __FUNCTION__ ]['oembed_data'] = 'shortcode oembed_data detected';
			$a['origin_data'][ __FUNCTION__ ]['cache']       = delete_oembed_caches_when_missing_data( $oembed_data );
		}
	}

	return build_video( $a );
}

function is_dev_mode(): bool {
	return (
		( defined( 'WP_DEBUG' ) && WP_DEBUG )
		|| wp_get_development_mode()
		|| 'development' === wp_get_environment_type()
		|| 'local' === wp_get_environment_type()
	);
}


/**
 * Builds a video based on the input attributes.
 *
 * @param array <string, mixed> $input_atts The input attributes for the video.
 *
 * @return string|\WP_REST_Response The built video.
 */
function build_video( array $input_atts ) {

	// If maxwidth is not set, use width as alias
	if ( empty( $input_atts['maxwidth'] ) && ! empty( $input_atts['width'] ) ) {
		$input_atts['maxwidth'] = $input_atts['width'];
	}

	$video = new Video( $input_atts );
	return $video->build_video();
}

/**
 * @return array <string, mixed>
 */
function shortcode_option_defaults(): array {

	$shortcodes = array();
	$properties = PROVIDERS;
	unset( $properties['video'] );

	foreach ( $properties as $provider => $values ) {

		if ( ! empty( $values['embed_url'] ) ) {
			$shortcodes[ $provider ] = $provider;
		}
	}

	return $shortcodes;
}

function create_shortcodes(): void {

	$options = options();

	add_shortcode( 'arve', __NAMESPACE__ . '\shortcode' );

	if ( $options['legacy_shortcodes'] ) {
		create_legacy_shortcodes();
	}
}

function create_legacy_shortcodes(): void {

	$properties        = PROVIDERS;
	$shortcode_options = wp_parse_args( get_option( 'arve_options_shortcodes', array() ), shortcode_option_defaults() );

	foreach ( $shortcode_options as $provider => $shortcode ) {

		$closure_name = __FUNCTION__ . '__closure';
		$function     = function ( $a ) use ( $provider, $properties, $closure_name ) {

			$a['provider'] = $provider;

			if ( ! empty( $properties[ $provider ]['rebuild_url'] ) && ! empty( $a['id'] ) ) {

				$a['url'] = sprintf( $properties[ $provider ]['rebuild_url'], $a['id'] );
				unset( $a['id'] );
				$a['origin_data'][ $closure_name ]['rebuild_url'] = 'rebuild_url';

				return shortcode( $a );
			} else {

				$a['origin_data'][ $closure_name ]['create_legacy_shortcodes'] = 'create_legacy_shortcodes';

				return build_video( $a );
			}
		};

		add_shortcode( $shortcode, $function );
	}
}

/**
 * @param array <string, string> $attr
 */
function wp_video_shortcode_override( string $out, array $attr ): string {

	$options = options();

	if (
		! $options['wp_video_override'] ||
		empty( $attr ) ||
		! empty( $attr['wmv'] ) ||
		! empty( $attr['flv'] ) ||
		disabled_on_feeds()
	) {
		return $out;
	}

	if ( empty( $attr['url'] ) && ! empty( $attr['src'] ) ) {
		$attr['url'] = $attr['src'];
	}

	if ( ! empty( $attr['poster'] ) ) {
		$attr['thumbnail'] = $attr['poster'];
	}

	return build_video( $attr );
}
