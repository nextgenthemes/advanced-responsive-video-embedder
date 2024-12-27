<?php

declare(strict_types = 1);

namespace Nextgenthemes\ARVE;

use function Nextgenthemes\WP\is_wp_debug;
use const Nextgenthemes\ARVE\ALLOWED_HTML;

/**
 * Processes the shortcode attributes and builds the video html.
 *
 * @param array $a The array of shortcode attributes.
 * @return string|WP_REST_Response The generated video output.
 */
function shortcode( array $a ) {

	$a['errors']              = new \WP_Error();
	$a['origin_data']['from'] = 'shortcode';

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
			$a['oembed_data']         = $oembed_data;
			$a['origin_data']['from'] = 'shortcode oembed_data detected';
		}
	}

	return build_video( $a );
}

function is_dev_mode(): bool {
	return (
		( defined( 'WP_DEBUG' ) && \WP_DEBUG )
		|| wp_get_development_mode()
		|| 'development' === wp_get_environment_type()
		|| 'local' === wp_get_environment_type()
	);
}

function error( string $messages, string $code = '' ): string {

	$error_html = sprintf(
		'<div class="arve-error alignwide" data-error-code="%s">
			 <abbr title="%s">ARVE</abbr> %s
		</div>',
		$code,
		'Advanced Responsive Video Embedder',
		// translators: Error message
		sprintf( __( 'Error: %s', 'advanced-responsive-video-embedder' ), $messages ),
	);

	return wp_kses(
		PHP_EOL . PHP_EOL . $error_html . PHP_EOL,
		ALLOWED_HTML,
		array( 'https' )
	);
}

function get_error_html(): string {

	$html     = '';
	$messages = '';

	foreach ( arve_errors()->get_error_codes() as $code ) {

		$message = '';

		foreach ( arve_errors()->get_error_messages( $code ) as $key => $message ) {
			$messages .= sprintf( '%s<br>', $message );
		}

		$html .= $messages;
		$data  = arve_errors()->get_error_data( $code );

		if ( ! empty( $data ) && is_dev_mode() ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_var_export
			$html .= sprintf( '<pre>%s</pre>', var_export( $data, true ) );
		}

		$html = error( $html );

		arve_errors()->remove( $code );
	}

	return $html;
}

/**
 * Builds a video based on the input attributes.
 *
 * @param array $input_atts The input attributes for the video.
 * @return string|WP_REST_Response The built video.
 */
function build_video( array $input_atts ) {

	if ( ! empty( $input_atts['errors'] ) ) {
		arve_errors()->merge_from( $input_atts['errors'] );
	}

	// If maxwidth is not set, use width as alias
	if ( empty( $input_atts['maxwidth'] ) && ! empty( $input_atts['width'] ) ) {
		$input_atts['maxwidth'] = $input_atts['width'];
	}

	$video = new Video( $input_atts );
	return $video->build_video();
}

function shortcode_option_defaults(): array {

	$properties = get_host_properties();
	unset( $properties['video'] );

	foreach ( $properties as $provider => $values ) {

		if ( ! empty( $values['embed_url'] ) ) {
			$shortcodes[ $provider ] = $provider;
		}
	}

	return $shortcodes;
}

function create_shortcodes(): void {

	$options    = options();
	$properties = get_host_properties();

	if ( $options['legacy_shortcodes'] ) {

		$shortcode_options = wp_parse_args( get_option( 'arve_options_shortcodes', array() ), shortcode_option_defaults() );

		foreach ( $shortcode_options as $provider => $shortcode ) {

			$function = function ( $a ) use ( $provider, $properties ) {

				$a['provider'] = $provider;

				if ( ! empty( $properties[ $provider ]['rebuild_url'] ) && ! empty( $a['id'] ) ) {
					$a['url'] = sprintf( $properties[ $provider ]['rebuild_url'], $a['id'] );
					unset( $a['id'] );
					$a['origin_data']['from'] = 'create_shortcodes rebuild_url';
					return shortcode( $a );
				} else {
					$a['origin_data']['from'] = 'create_shortcodes';
					return build_video( $a );
				}
			};

			add_shortcode( $shortcode, $function );
		}
	}

	add_shortcode( 'arve', __NAMESPACE__ . '\shortcode' );
}

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
