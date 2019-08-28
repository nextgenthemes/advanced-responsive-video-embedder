<?php
namespace Nextgenthemes\ARVE;

use function Nextgenthemes\Utils\starts_with;

function shortcode( array $a, $content = null ) {

	$override = apply_filters( 'nextgenthemes/arve/shortcode_override', '', $a, $content );

	if ( '' !== $override ) {
		return $override;
	}

	if ( ! empty( $a['url'] ) ) {

		$embed_check     = new EmbedChecker( $a );
		$mayme_arve_html = $embed_check->check();

		if ( $mayme_arve_html ) {
			return $mayme_arve_html;
		}
	}

	return build_video( $a, $content );
}

function test_shortcode( $a, $content = null ) {

	// phpcs:disable WordPress.Security.NonceVerification.Recommended
	if ( empty( $_GET['provider'] ) ) {
		return;
	}

	$html      = '';
	$providers = get_host_properties();
	$provider  = sanitize_text_field( wp_unslash( $_GET['provider'] ) );
	// phpcs:enable WordPress.Security.NonceVerification.Recommended

	if ( empty( $provider ) ) {
		return 'put provider in url arg';
	}

	foreach ( $providers[ $provider ]['tests'] as $key => $value ) {

		$sc    = sprintf( '[arve url="%s" mode="lazyload" maxwidth="500" title="Custom Title Test" /]', $value['url'] );
		$html .= do_shortcode( $sc );
		$sc    = sprintf( '[arve url="%s" mode="lightbox" maxwidth="200" /]', $value['url'] );
		$html .= do_shortcode( $sc );
	}

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

		$error_html = sprintf(
			'<p><strong>%s</strong><br>',
			__( '<abbr title="Advanced Responsive Video Embedder">ARVE</abbr> Error(s):', 'advanced-responsive-video-embedder' )
		);

		foreach ( $a['errors']->get_error_messages() as $key => $value ) {
			$error_html .= "$value<br>";
		}

		$error_html .= '</p>';

		$html .= $error_html;

		if ( '' !== $a['errors']->get_error_message( 'fatal' ) ) {
			$html .= get_debug_info( $html, $a, $input_atts );
			return $html;
		}
	}

	$html .= build_html( $a );
	$html .= get_debug_info( $html, $a, $input_atts );
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

	$options = options();

	if ( $options['legacy_shortcodes'] ) {

		$shortcode_options = wp_parse_args( get_option( 'arve_options_shortcodes', [] ), shortcode_option_defaults() );

		foreach ( $shortcode_options as $provider => $shortcode ) {

			$function = function( $atts ) use ( $provider ) {
				$a['provider'] = $provider;
				return shortcode( $a );
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
