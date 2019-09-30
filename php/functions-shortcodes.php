<?php
namespace Nextgenthemes\ARVE;

use function Nextgenthemes\ARVE\Common\starts_with;

function shortcode( $a, $content = null ) {

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

function test_shortcode( $atts, $content = null ) {

	$html      = '';
	$providers = get_host_properties();
	$host      = sanitize_text_field( wp_unslash( empty( $_GET['provider'] ) ? '' : $_GET['provider'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$host      = empty( $atts['provider'] ) ? $host : $atts['provider'];

	if ( 'all' === $host ) {

		$count = 0;

		foreach ( $providers as $k => $v ) {

			$count++;

			if ( $count > 7 ) {
				break;
			}

			if ( empty( $v['tests'] ) ) {
				continue;
			}

			$html .= basic_tests( $v['tests'] );
		}
	} else {

		if ( empty( $providers[ $host ]['tests'] ) ) {
			$html .= 'no tests for ' . $host;
		} else {
			$html .= basic_tests( $providers[ $host ]['tests'] );
		}
	}

	return $html;
}

function basic_tests( $tests ) {

	$html = '';

	/*
	foreach ( $tests as $key => $value ) {
		$sc    = sprintf( '[arve url="%s" mode="lightbox" maxwidth="200" /]', $value['url'] );
		$html .= do_shortcode( $sc );
	}
	*/

	$sc .= sprintf( '[arve url="%s" mode="lazyload" maxwidth="400" /]', $tests[0]['url'] );

	$html .= "[$sc]<br>";
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

	wp_enqueue_style( 'arve' );
	wp_enqueue_script( 'arve' );

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

		$shortcode_options = wp_parse_args( get_option( 'arve_options_shortcodes', [] ), shortcode_option_defaults() );

		foreach ( $shortcode_options as $provider => $shortcode ) {

			$function = function( $a ) use ( $provider, $properties ) {

				$a['provider'] = $provider;

				if ( ! empty( $properties[ $provider ]['rebuild_url'] ) ) {
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
		! empty( $attr['flv'] )
	) {
		return $out;
	}

	if ( ! empty( $attr['poster'] ) ) {
		$attr['thumbnail'] = $attr['poster'];
	}

	return build_video( $attr );
}
