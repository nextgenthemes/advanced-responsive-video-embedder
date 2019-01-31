<?php
namespace Nextgenthemes\ARVE;

function shortcode( array $a, $content = null ) {

	if ( ! empty( $a['url'] ) ) {

		$embed_check     = new EmbedChecker( $a );
		$mayme_arve_html = $embed_check->check();

		if ( $mayme_arve_html ) {
			return $mayme_arve_html;
		}
	}

	return build_video( $a, $content );
}

function old_pairs_for_test() {

	$options = options();

	return [
		// arve visual options
		'align'             => $options['align'],
		'aspect_ratio'      => null,
		'arve_link'         => bool_to_shortcode_string( $options['promote_link'] ),
		'disable_sandbox'   => 'n',
		'maxwidth'          => (string) $options['video_maxwidth'],
		'mode'              => $options['mode'],
		// url query
		'autoplay'          => bool_to_shortcode_string( $options['autoplay'] ),
		'parameters'        => null,
		// old shortcodes, manual, no oembed
		'provider'          => null,
		'id'                => null,
		'account_id'        => null,
		'brightcove_player' => 'default',
		'brightcove_embed'  => 'default',
		// Essential + schema
		'url'               => null,
		'src'               => null,
		'thumbnail'         => null,
		// schema
		'description'       => null,
		'duration'          => null,
		'title'             => null,
		'upload_date'       => null,
		// <video>
		'controls'          => 'y',
		'controlslist'      => empty( $options['controlslist'] ) ? null : (string) $options['controlslist'],
		'loop'              => 'n',
		'm4v'               => null,
		'mp4'               => null,
		'muted'             => null,
		'ogv'               => null,
		'playsinline'       => null,
		'preload'           => 'metadata',
		'webm'              => null,
		// TED only
		'lang'              => null,
		// Vimeo only
		'start'             => null,
		// deprecated, title should be used
		'link_text'         => null,
		// misc
		'oembed_data'       => null,
		'iframe_name'       => null,
		// debug
		'append_text'       => null,
	];
}

function build_video( array $input_atts ) {

	for ( $n = 1; $n <= NUM_TRACKS; $n++ ) {
		$pairs[ "track_{$n}" ]       = null;
		$pairs[ "track_{$n}_label" ] = null;
	}

	$a = shortcode_atts( shortcode_pairs(), $input_atts, 'arve' );

	if ( $a['errors']->get_error_code() ) {
		$error_html  = __( 'ARVE Error(s):', 'advanced-responsive-video-embedder' );
		$error_html .= get_error_messages();
		$error_html .= get_debug_info( '', $a, $input_atts );

		return $error_html;
	}

	$output = build_video_html( $a );

	if ( is_wp_error( $output ) ) {
		return error( $output->get_error_message() );
	}

	return $output . get_debug_info( $output, $a, $input_atts );
}

function build_video_html( array $a ) {

	$pieces                  = (object) array();
	$pieces->arve__embed     = arve__embed( build_inner_html( $a ), $a );
	$pieces->arve_inner_html = $pieces->arve__embed . build_promote_link_html( $a['arve_link'] );

	$pieces->arve = build_tag(
		array(
			'name'    => 'arve',
			'tag'     => 'div',
			'content' => $pieces->arve_inner_html,
			'attr'    => array(
				'class'         => empty( $a['align'] ) ? 'arve-wrapper' : 'arve-wrapper align' . $a['align'],
				'data-mode'     => $a['mode'],
				'data-provider' => $a['provider'],
				'id'            => $a['wrapper_id'],
				'style'         => empty( $a['maxwidth'] ) ? false : sprintf( 'max-width:%dpx;', $a['maxwidth'] ),
				// Schema.org
				'itemscope'     => '',
				'itemtype'      => 'http://schema.org/VideoObject'
			)
		),
		$a
	);

	return apply_filters( 'nextgenthemes/arve/video_html', $pieces->arve, $pieces, $a );
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
}

function wp_video_shortcode_override( $out, $attr ) {

	$options = options();

	if ( ! $options['wp_video_override']
		|| ! empty( $attr['wmv'] )
		|| ! empty( $attr['flv'] )
	) {
		return $out;
	}

	if ( ! empty( $attr['poster'] ) ) {
		$attr['thumbnail'] = $attr['poster'];
	}

	return build_video( $attr );
}
