<?php
namespace Nextgenthemes\ARVE;

function shortcode( array $a, $content = null ) {

	if ( ! empty( $a['url'] ) ) {

		$embed_check     = new \Nextgenthemes\ARVE\EmbedChecker( $a );
		$mayme_arve_html = $embed_check->check();

		if ( $mayme_arve_html ) {
			return $mayme_arve_html;
		}
	}

	return build_video( $a, $content );
}


function add_iframe_parameters_to_url( array $a ) {

	$iframe_parameters = [];

	if ( ! empty( $a['parameters'] ) && is_string( $a['parameters'] ) ) {
		wp_parse_str( $a['parameters'], $iframe_parameters );
	}

	foreach ( $iframe_parameters as $key => $value ) {
		$a['url'] = add_query_arg( "arve-ifp[{$key}]", $value, $url );
	}

	return $a;
}

function build_video( array $input_atts, $content = null ) {

	$errors     = '';
	$options    = options();
	$properties = get_host_properties();

	$pairs = [
		// arve visual options
		'align'             => $options['align'],
		'aspect_ratio'      => null,
		'arve_link'         => bool_to_shortcode_string( $options['promote_link'] ),
		'disable_flash'     => null,
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

	for ( $n = 1; $n <= NUM_TRACKS; $n++ ) {
		$pairs[ "track_{$n}" ]       = null;
		$pairs[ "track_{$n}_label" ] = null;
	}

	$atts = shortcode_atts(
		apply_filters( 'arve_shortcode_pairs', $pairs ),
		$input_atts,
		'arve'
	);

	$errors = output_errors( $atts );

	if ( $errors ) {
		return $errors . get_debug_info( '', $atts, $input_atts );
	}

	$html['video']           = video_or_iframe( $atts );
	$html['meta']            = build_meta_html( $atts );
	$html['ad_link']         = build_promote_link_html( $atts['arve_link'] );
	$html['embed_container'] = embed_container( $html['meta'] . $html['video'], $atts );

	$normal_embed = wrapper( $html['embed_container'] . $html['ad_link'], $atts );

	$output = apply_filters( 'arve_output', $normal_embed, $html, $atts );

	if ( empty( $output ) ) {
		return error( 'The output is empty, this should not happen', 'advanced-responsive-video-embedder' );
	} elseif ( is_wp_error( $output ) ) {
		return error( $output->get_error_message() );
	}

	wp_enqueue_style( 'advanced-responsive-video-embedder' );
	wp_enqueue_script( 'advanced-responsive-video-embedder' );

	return get_debug_info( $output, $atts, $input_atts ) . $output;
}

function create_shortcodes() {

	$options = options();

	foreach ( $options['shortcodes'] as $provider => $shortcode ) {

		$function = function( $atts ) use ( $provider ) {
			$atts['provider'] = $provider;
			return build_video( $atts );
		};

		add_shortcode( $shortcode, $function );
	}

	add_shortcode( 'arve', __NAMESPACE__ . '\shortcode' );
}

function wp_video_shortcode_override( $out, $attr, $content, $instance ) {

	$options = options();

	if ( ! $options['wp_video_override'] || ! empty( $attr['wmv'] ) || ! empty( $attr['flv'] ) ) {
		return $out;
	}

	if ( ! empty( $attr['poster'] ) ) {
		$attr['thumbnail'] = $attr['poster'];
	}

	return build_video( $attr );
}
