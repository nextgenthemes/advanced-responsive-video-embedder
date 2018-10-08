<?php
namespace Nextgenthemes\ARVE;

function shortcode( array $a, $content = null ) {

	/*
	if ( ! empty( $a['url'] ) && $mayme_arve_html = check_for_embed( $a ) ) {
		return $mayme_arve_html;
	}
	*/

	$embed_check = new \Nextgenthemes\ARVE\EmbedChecker( $a );

	if ( ! empty( $a['url'] ) && $mayme_arve_html = $embed_check->check() ) {
		return $mayme_arve_html;
	}

	if ( defined( 'ARVE_DEBUG' ) ) {
		$a['append_text'] = 'No wp embed match';
	}

	if ( ! empty( $a['url'] ) && empty( $a['src'] ) ) {
		$a['src'] = $a['url'];
	}

	return shortcode_arve( $a, $content );
}

function aarve_check_for_embed( array $a ) {

	$url = $a['url'];
	unset( $a['url'] );

	foreach ( $a as $key => $value ) {
		if ( 'url' === $key ) {
			continue;
		}
		$url = add_query_arg( "arve[{$key}]", $value, $url );
	}

	$maybe_arve_html = $GLOBALS['wp_embed']->shortcode( array(), $url );

	if ( \Nextgenthemes\Utils\contains( $maybe_arve_html, 'class="arve-wrapper' ) ) {
		return $maybe_arve_html;
	};

	return false;
}

function add_iframe_parameters_to_url( array $a ) {

	$iframe_parameters = array();

	if ( ! empty( $a['parameters'] ) && is_string( $a['parameters'] ) ) {
		wp_parse_str( $a['parameters'], $iframe_parameters );
	}

	foreach ( $iframe_parameters as $key => $value ) {
		$a['url'] = add_query_arg( "arve-ifp[{$key}]", $value, $url );
	}

	return $a;
}

function shortcode_arve( array $input_atts, $content = null ) {

	$errors     = '';
	$options    = get_options();
	$properties = get_host_properties();

	$pairs = array(
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
	);

	for ( $n = 1; $n <= NUM_TRACKS; $n++ ) {
		$pairs["track_{$n}"]       = null;
		$pairs["track_{$n}_label"] = null;
	}

	$atts = shortcode_atts(
		apply_filters( 'arve_shortcode_pairs', $pairs ),
		$input_atts,
		'arve'
	);

	if ( $errors = output_errors( $atts ) ) {
		return $errors . get_debug_info( '', $atts, $input_atts );
	}

	$html['video']           = video_or_iframe( $atts );
	$html['meta']            = build_meta_html( $atts );
	$html['ad_link']         = build_promote_link_html( $atts['arve_link'] );
	$html['embed_container'] = embed_container( $html['meta'] . $html['video'], $atts );

	$normal_embed = wrapper( $html['embed_container'] . $html['ad_link'], $atts );

	$output = apply_filters( 'arve_output', $normal_embed, $html, $atts );

	if ( empty( $output ) ) {
		return error( 'The output is empty, this should not happen', TEXTDOMAIN );
	} elseif ( is_wp_error( $output ) ) {
		return error( $output->get_error_message() );
	}

	wp_enqueue_style( TEXTDOMAIN );
	wp_enqueue_script( TEXTDOMAIN );

	return get_debug_info( $output, $atts, $input_atts ) . $output;
}

/**
 * Create all shortcodes at a late stage because people over and over again using this plugin toghter with jetback or
 * other plugins that handle shortcodes we will now overwrite all this suckers.
 *
 * @since    2.6.2
 *
 * @uses Advanced_Responsive_Video_Embedder_Create_Shortcodes()
 */
function create_shortcodes() {

	$options = get_options();

	foreach( $options['shortcodes'] as $provider => $shortcode ) {

		$function = function( $atts ) use ( $provider ) {
			$atts['provider'] = $provider;
			return shortcode_arve( $atts, null, false );
		};

		add_shortcode( $shortcode, $function );
	}

	add_shortcode( 'arve', 'arve_shortcode' );
}

function wp_video_shortcode_override( $out, $attr, $content, $instance ) {

	$options = get_options();

	if( ! $options['wp_video_override'] || ! empty( $attr['wmv'] ) || ! empty( $attr['flv'] ) ) {
		return $out;
	}

	if( ! empty( $attr['poster'] ) ) {
		$attr['thumbnail'] = $attr['poster'];
	}

	return shortcode_arve( $attr, null );
}
