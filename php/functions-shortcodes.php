<?php
namespace Nextgenthemes\ARVE;

function shortcode( $a ) {

	$a                   = (array) $a;
	$errors              = new \WP_Error();
	$origin_data['from'] = 'shortcode';
	$oembed_data         = null;

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

		$oembed_data = extract_oembed_json( $maybe_arve_html, $a['url'], $errors );

		if ( $oembed_data ) {
			$origin_data['from'] = 'shortcode oembed_data detected';
		}
	}

	$video = new Video( $a, $origin_data, $oembed_data, $errors );
	return $video->build_video();
}

function error( $msg, $code = '' ) {

	return sprintf(
		PHP_EOL . PHP_EOL .'<span class="arve-error"%s><abbr title="%s">ARVE</abbr> %s<br></span>' . PHP_EOL,
		'hidden' === $code ? ' hidden' : '',
		__( 'Advanced Responsive Video Embedder', 'advanced-responsive-video-embedder' ),
		// translators: Error message
		sprintf( __( 'Error: %s', 'advanced-responsive-video-embedder' ), $msg )
	);
}

function old_get_error_html( array $a ) {

	$html = '';

	foreach ( $a['errors']->get_error_codes() as $code ) {
		foreach ( $a['errors']->get_error_messages( $code ) as $key => $message ) {
			$html .= error( $message, $code );
		}
	}

	return $html;
}

function shortcode_option_defaults() {

	$properties = get_host_properties();
	unset( $properties['video'] );

	foreach ( $properties as $provider => $values ) {

		if ( ! empty( $values['embed_url'] ) ) {
			$shortcodes[ $provider ] = $provider;
		}
	}

	return $shortcodes;
}

function create_shortcodes() {

	$options    = options();
	$properties = get_host_properties();

	if ( $options['legacy_shortcodes'] ) {

		$shortcode_options = wp_parse_args( get_option( 'arve_options_shortcodes', array() ), shortcode_option_defaults() );

		foreach ( $shortcode_options as $provider => $shortcode ) {

			$function = function( $a ) use ( $provider, $properties ) {

				$a['provider'] = $provider;

				if ( ! empty( $properties[ $provider ]['rebuild_url'] ) && ! empty( $a['id'] ) ) {
					$a['url'] = sprintf( $properties[ $provider ]['rebuild_url'], $a['id'] );
					unset( $a['id'] );
					$origin_data['from'] = 'create_shortcodes rebuild_url';
					return shortcode( $a );
				} else {
					$origin_data['from'] = 'create_shortcodes';

					$video = new Video( $a, $origin_data );
					return $video->build_video();
				}
			};

			add_shortcode( $shortcode, $function );
		}
	}

	add_shortcode( 'arve', __NAMESPACE__ . '\shortcode' );
}

// TODO sometimes $attr is string, investigate when and what it is exacly
function wp_video_shortcode_override( $out, $attr ) {

	$options = options();

	if (
		! $options['wp_video_override'] ||
		empty( $attr ) ||
		! is_array( $attr ) ||
		! empty( $attr['wmv'] ) ||
		! empty( $attr['flv'] ) ||
		disabled_on_feeds()
	) {
		return $out;
	}

	if ( empty( $attr['url'] ) && ! empty( $attr['src'] ) ) {
		$attr['url'] = $attr['src'];
	}

	if ( isset( $attr['loop'] ) ) {
		$attr['loop'] = bool_to_shortcode_string( $attr['loop'] );
	}

	if ( ! empty( $attr['poster'] ) ) {
		$attr['thumbnail'] = $attr['poster'];
	}

	$video = new Video( $attr );
	return $video->build_video();
}
