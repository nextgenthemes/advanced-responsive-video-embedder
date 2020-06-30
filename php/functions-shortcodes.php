<?php
namespace Nextgenthemes\ARVE;

use function Nextgenthemes\ARVE\Common\starts_with;

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

function test_shortcode( $atts = null, $content = null ) {

	$html         = '';
	$providers    = get_host_properties();
	$get_provider = sanitize_text_field( wp_unslash( empty( $_GET['arve-provider-test'] ) ? '' : $_GET['arve-provider-test'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	if ( $get_provider ) {

		if ( empty( $providers[ $get_provider ]['tests'] ) ) {
			$html .= 'no tests for ' . $get_provider;
		} else {
			$html .= basic_tests( $providers[ $get_provider ]['tests'] );
		}
	}

	$html .= '<ul>';
	foreach ( $providers as $provider => $v ) {

		$url   = add_query_arg( $GLOBALS['wp']->query_vars, home_url( $GLOBALS['wp']->request ) );
		$url   = add_query_arg( 'arve-provider-test', $provider, $url );
		$html .= sprintf( '<li><a href="%s">Test %s</a></li>', $url, $provider );
	}
	$html .= '</ul>';

	return $html;
}

function basic_tests( $tests ) {

	$html  = '';
	$modes = [ 'normal', 'lazyload', 'lightbox' ];

	foreach ( $tests as $key => $value ) {
		$sc    = sprintf( '[arve url="%s" mode="lazyload" maxwidth="300" /]', $value['url'], $modes[ array_rand( $modes ) ] );
		$html .= do_shortcode( $sc );
	}

	$html .= "<code>[$sc]</code><br>";
	$html .= do_shortcode( $sc );
	$html .= '<br>';

	return $html;
}

function build_video( array $input_atts ) {

	for ( $n = 1; $n <= NUM_TRACKS; $n++ ) {
		$pairs[ "track_{$n}" ]       = null;
		$pairs[ "track_{$n}_label" ] = null;
	}

	$a    = shortcode_atts( shortcode_pairs(), $input_atts, 'arve' );
	$html = '';

	ksort( $a );
	ksort( $input_atts );

	if ( ! empty( $a['errors'] ) ) {

		foreach ( $a['errors']->get_error_messages() as $key => $message ) {
			$html .= sprintf(
				'%s %s<br>',
				__( '<abbr title="Advanced Responsive Video Embedder">ARVE</abbr> Error:', 'advanced-responsive-video-embedder' ),
				$message
			);
		}

		if ( '' !== $a['errors']->get_error_message( 'fatal' ) ) {
			$html .= get_debug_info( $html, $a, $input_atts );
			return $html;
		}
	}

	$html .= build_html( $a );
	$html .= get_debug_info( $html, $a, $input_atts );

	wp_enqueue_script( 'arve' );

	return apply_filters( 'nextgenthemes/arve/html', $html, $a );
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
	add_shortcode( 'arve_test', __NAMESPACE__ . '\test_shortcode' );
}

function wp_video_shortcode_override( $out, $attr ) {

	$options = options();

	if ( ! $options['wp_video_override'] ||
		! empty( $attr['wmv'] ) ||
		! empty( $attr['flv'] ) ||
		disabled_on_feeds()
	) {
		return $out;
	}

	if ( ! empty( $attr['url'] )
		&& ! empty( $attr['src'] )
	) {
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
