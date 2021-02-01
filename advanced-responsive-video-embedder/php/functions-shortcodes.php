<?php
namespace Nextgenthemes\ARVE;

function shortcode( $a, $content = null ) {

	$a = (array) $a;

	foreach ( $a as $k => $v ) {
		if ( '' === $v ) {
			unset( $a[ $k ] );
		}
	}

	$override = apply_filters( 'nextgenthemes/arve/shortcode_override', '', $a, $content );

	if ( '' !== $override ) {
		return $override;
	}

	$a['errors'] = new \WP_Error();
	$a           = apply_filters( 'nextgenthemes/arve/shortcode_args', $a );

	if ( ! empty( $a['url'] ) ) {

		$embed_check     = new EmbedChecker( $a );
		$mayme_arve_html = $embed_check->check();

		if ( $mayme_arve_html ) {
			return $mayme_arve_html;
		}
	}

	return build_video( $a, $content );
}

function error( $msg, $code = '' ) {

	return sprintf(
		'<span class="arve-error"%s><abbr title="%s">ARVE</abbr> %s</span>',
		'hidden' === $code ? ' hidden' : '',
		__( 'Advanced Responsive Video Embedder', 'advanced-responsive-video-embedder' ),
		// translators: Error message
		sprintf( __( 'Error: %s', 'advanced-responsive-video-embedder' ), $msg )
	);
}

function add_error_html( array $a ) {

	$html = '';

	foreach ( $a['errors']->get_error_codes() as $code ) {
		foreach ( $a['errors']->get_error_messages( $code ) as $key => $message ) {
			$html .= error( $message, $code );
		}
	}

	return $html;
}

function build_video( array $input_atts ) {

	$a    = array();
	$html = '';

	try {
		Common\check_product_keys();

		$a = shortcode_atts( shortcode_pairs(), $input_atts, 'arve' );
		ksort( $a );
		ksort( $input_atts );

		$build_args = new ShortcodeArgs( $a['errors'] );
		$a          = $build_args->get_done( $a );

		$html .= add_error_html( $a );
		$html .= build_html( $a );
		$html .= get_debug_info( $html, $a, $input_atts );

		wp_enqueue_script( 'arve' );

		return apply_filters( 'nextgenthemes/arve/html', $html, $a );

	} catch ( \Exception $e ) {
		return error( $e->getMessage(), $e->getCode() ) .
			get_debug_info( '', $a, $input_atts );
	}
}

function arg_filters( array $a ) {

	$args = new ShortcodeArgs( $a );

	return $args->get_done();
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

		$shortcode_options = wp_parse_args( get_option( 'arve_options_shortcodes', [] ), shortcode_option_defaults() );

		foreach ( $shortcode_options as $provider => $shortcode ) {

			$function = function( $a ) use ( $provider, $properties ) {

				$a['provider'] = $provider;

				if ( ! empty( $properties[ $provider ]['rebuild_url'] ) && ! empty( $a['id'] ) ) {
					$a['url'] = sprintf( $properties[ $provider ]['rebuild_url'], $a['id'] );
					unset( $a['id'] );
					return shortcode( $a );
				} else {
					$a['legacy_sc'] = 'Legacy Shortcode';
					return build_video( $a );
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
		unset( $attr['src'] );
	}

	if ( isset( $attr['loop'] ) ) {
		$attr['loop'] = bool_to_shortcode_string( $attr['loop'] );
	}

	if ( ! empty( $attr['poster'] ) ) {
		$attr['thumbnail'] = $attr['poster'];
	}

	return build_video( $attr );
}
