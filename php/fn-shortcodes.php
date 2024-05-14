<?php declare(strict_types=1);
namespace Nextgenthemes\ARVE;

use function Nextgenthemes\WP\is_wp_debug;
use const Nextgenthemes\ARVE\ALLOWED_HTML;

function shortcode( array $a ): string {

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

		$oembed_data = extract_oembed_json( $maybe_arve_html, $a['url'], $a );

		if ( $oembed_data ) {
			$a['oembed_data']         = $oembed_data;
			$a['origin_data']['from'] = 'shortcode oembed_data detected';
		}
	}

	return build_video( $a );
}

function error( string $messages, string $code = '' ): string {

	$hide = false;

	if ( str_contains( $code, 'hidden' ) && ! is_wp_debug() ) {
		$hide = true;
	}

	$error_html = sprintf(
		PHP_EOL . PHP_EOL .
		'<span class="arve-error"%s><abbr title="Advanced Responsive Video Embedder">ARVE</abbr> %s</span>' .
		PHP_EOL,
		$hide ? 'hidden' : '',
		// translators: Error message
		sprintf( __( 'Error: %s', 'advanced-responsive-video-embedder' ), $messages ),
	);

	return wp_kses(
		$error_html,
		ALLOWED_HTML,
		array( 'http', 'https' )
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

		if ( ! empty( $data ) && ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_var_export
			$html .= sprintf( 'Data: %s', var_export( $data, true ) );
		}

		$html = error( $html );

		arve_errors()->remove($code);
	}

	return $html;
}

function build_video( array $input_atts ): string {

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
