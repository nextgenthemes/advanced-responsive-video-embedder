<?php

function arve_shortcode( $a, $content = null ) {

	/*
	if ( ! empty( $a['url'] ) && $mayme_arve_html = arve_check_for_embed( $a ) ) {
		return $mayme_arve_html;
	}
	*/

	$embed_check = new ARVE_Embed_Checker( $a );

	if ( ! empty( $a['url'] ) && $mayme_arve_html = $embed_check->check() ) {
		return $mayme_arve_html;
	}

	if ( defined( 'ARVE_DEBUG' ) ) {
		$a['append_text'] = 'No wp embed match';
	}

	if ( ! empty( $a['url'] ) && empty( $a['src'] ) ) {
		$a['src'] = $a['url'];
	}

	return arve_shortcode_arve( $input_atts, $content );
}

class ARVE_Embed_Checker {

	public $shortcode_args = array();

	public function __construct( $shortcode_args ) {
		$this->shortcode_args = $shortcode_args;
	}

	public function check() {
		add_filter( 'arve_oembed2args', array( $this, 'oembed2args' ) );
		$maybe_arve_html = $GLOBALS['wp_embed']->shortcode( array(), $this->shortcode_args['url'] );
		remove_filter( 'arve_oembed2args', array( $this, 'oembed2args' ) );

		if ( arve_contains( $maybe_arve_html, 'class="arve-wrapper' ) ) {
			return $maybe_arve_html;
		};

		return false;
	}

	public function oembed2args( $shortcode_args ) {
		$shortcode_args = array_merge( $shortcode_args, $this->shortcode_args );
		return $shortcode_args;
	}
}

function aarve_check_for_embed( $a ) {

	$url = $a['url'];
	unset( $a['url'] );

	foreach ( $a as $key => $value ) {
		if ( 'url' === $key ) {
			continue;
		}
		$url = add_query_arg( "arve[{$key}]", $value, $url );
	}

	$maybe_arve_html = $GLOBALS['wp_embed']->shortcode( array(), $url );

	if ( arve_contains( $maybe_arve_html, 'class="arve-wrapper' ) ) {
		return $maybe_arve_html;
	};

	return false;
}

function arve_add_iframe_parameters_to_url( $a ) {

	$iframe_parameters = array();

	if ( ! empty( $a['parameters'] ) && is_string( $a['parameters'] ) ) {
		wp_parse_str( $a['parameters'], $iframe_parameters );
	}

	foreach ( $iframe_parameters as $key => $value ) {
		$a['url'] = add_query_arg( "arve-ifp[{$key}]", $value, $url );
	}

	return $a;
}

function arve_shortcode_arve( $input_atts, $content = null ) {

	$errors     = '';
	$options    = arve_get_options();
	$properties = arve_get_host_properties();
	$input_atts = (array) $input_atts;

	$pairs = array(
		// arve visual options
		'align'             => $options['align'],
		'aspect_ratio'      => null,
		'arve_link'         => arve_bool_to_shortcode_string( $options['promote_link'] ),
		'disable_flash'     => null,
		'maxwidth'          => (string) $options['video_maxwidth'],
		'mode'              => $options['mode'],
		// url query
		'autoplay'          => arve_bool_to_shortcode_string( $options['autoplay'] ),
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

	for ( $n = 1; $n <= ARVE_NUM_TRACKS; $n++ ) {
		$pairs["track_{$n}"]       = null;
		$pairs["track_{$n}_label"] = null;
	}

	if ( $arve_shortcode ) {
		$pairs['url'] = null;
	} else {
		$pairs['provider'] = null;
		$pairs['id']       = null;

		if ( empty( $input_atts['provider'] ) || empty( $input_atts['id'] ) ) {
			return arve_error( __( 'id and provider shortcodes attributes are mandatory for old shortcodes. It is recommended to switch to new shortcodes that need only url', ARVE_SLUG ) );
		}
	}

	$atts = shortcode_atts( apply_filters( 'arve_shortcode_pairs', $pairs ), $input_atts, 'arve' );

	if ( $errors = arve_output_errors( $atts ) ) {
		return $errors . arve_get_debug_info( '', $atts, $input_atts );
	}

	$html['video']           = arve_video_or_iframe( $atts );
	$html['meta']            = arve_build_meta_html( $atts );
	$html['ad_link']         = arve_build_promote_link_html( $atts['arve_link'] );
	$html['embed_container'] = arve_arve_embed_container( $html['meta'] . $html['video'], $atts );

	$normal_embed = arve_arve_wrapper( $html['embed_container'] . $html['ad_link'], $atts );

	$output = apply_filters( 'arve_output', $normal_embed, $html, $atts );

	if ( empty( $output ) ) {
		return arve_error( 'The output is empty, this should not happen', ARVE_SLUG );
	} elseif ( is_wp_error( $output ) ) {
		return arve_error( $output->get_error_message() );
	}

	wp_enqueue_style( ARVE_SLUG );
	wp_enqueue_script( ARVE_SLUG );

	return arve_get_debug_info( $output, $atts, $input_atts ) . $output;
}

/**
 * Create all shortcodes at a late stage because people over and over again using this plugin toghter with jetback or
 * other plugins that handle shortcodes we will now overwrite all this suckers.
 *
 * @since    2.6.2
 *
 * @uses Advanced_Responsive_Video_Embedder_Create_Shortcodes()
 */
function arve_create_shortcodes() {

	$options = arve_get_options();

	foreach( $options['shortcodes'] as $provider => $shortcode ) {

		$function = function( $atts ) use ( $provider ) {
			$atts['provider'] = $provider;
			return arve_shortcode_arve( $atts, null, false );
		};

		add_shortcode( $shortcode, $function );
	}

	add_shortcode( 'arve', 'arve_shortcode' );
}

function arve_wp_video_shortcode_override( $out, $attr, $content, $instance ) {

	$options = arve_get_options();

	if( ! $options['wp_video_override'] || ! empty( $attr['wmv'] ) || ! empty( $attr['flv'] ) ) {
		return $out;
	}

	if( ! empty( $attr['poster'] ) ) {
		$attr['thumbnail'] = $attr['poster'];
	}

	return arve_shortcode_arve( $attr, null );
}
