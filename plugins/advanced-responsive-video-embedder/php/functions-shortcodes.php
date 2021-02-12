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

		$maybe_arve_html = $GLOBALS['wp_embed']->shortcode( $a, $a['url'] );

		if ( str_contains( $maybe_arve_html, 'class="arve' ) ) {
			return $maybe_arve_html;
		}
	}

	return build_video( $a, $content );
}

function error( $msg, $code = '' ) {

	return sprintf(
		'<span class="arve-error"%s><abbr title="%s">ARVE</abbr> %s</span><br>' . PHP_EOL,
		'hidden' === $code ? ' hidden' : '',
		__( 'Advanced Responsive Video Embedder', 'advanced-responsive-video-embedder' ),
		// translators: Error message
		sprintf( __( 'Error: %s', 'advanced-responsive-video-embedder' ), $msg )
	);
}

function get_error_html( array $a ) {

	$html = '';

	foreach ( $a['errors']->get_error_codes() as $code ) {
		foreach ( $a['errors']->get_error_messages( $code ) as $key => $message ) {
			$html .= error( $message, $code );
		}
	}

	return $html;
}

function build_video( array $input_atts ) {

	$html = '';
	$a    = [];

	try {
		$a = shortcode_atts( shortcode_pairs(), $input_atts, 'arve' );
		Common\check_product_keys();
		$a = process_shortcode_args( $a );

		ksort( $a );
		ksort( $input_atts );

		$html .= get_error_html( $a );
		$html .= build_html( $a );
		$html .= get_debug_info( $html, $a, $input_atts );

		wp_enqueue_style( 'arve' );
		wp_enqueue_script( 'arve' );

		return apply_filters( 'nextgenthemes/arve/html', $html, $a );

	} catch ( \Exception $e ) {

		if ( ! isset( $a['errors'] ) ) {
			$a['errors'] = new WP_Error();
		}

		$a['errors']->add( $e->getCode(), $e->getMessage() );

		$html .= get_error_html( $a );
		$html .= get_debug_info( '', $a, $input_atts );

		return $html;
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

		$shortcode_options = wp_parse_args( get_option( 'arve_options_shortcodes', array() ), shortcode_option_defaults() );

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
	}

	if ( isset( $attr['loop'] ) ) {
		$attr['loop'] = bool_to_shortcode_string( $attr['loop'] );
	}

	if ( ! empty( $attr['poster'] ) ) {
		$attr['thumbnail'] = $attr['poster'];
	}

	return build_video( $attr );
}
