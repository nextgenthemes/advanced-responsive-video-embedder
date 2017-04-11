<?php

function arve_html_id( $html_attr ) {

	if( ! arve_contains( $html_attr, 'id=' ) ) {
		$html_attr .= ' id="arve"';
	}

	return $html_attr;
}

function arve_get_var_dump( $var ) {
	ob_start();
	var_dump( $var );
	return ob_get_clean();
};

function arve_get_debug_info( $input_html, $atts, $input_atts ) {

	$html = '';

	if ( isset( $_GET['arve-debug-options'] ) ) {

		static $show_options_debug = true;

		$options               = get_option( 'arve_options_main' );
		$options['shortcodes'] = get_option( 'arve_options_shortcodes' );
		$options['params']     = get_option( 'arve_options_params' );

		if ( $show_options_debug ) {
			$html .= sprintf( 'Options: <pre>%s</pre>', arve_get_var_dump( $options ) );
		}
		$show_options_debug = false;
	}

	$pre_style = ''
		. 'background-color: #111;'
		. 'color: #eee;'
		. 'font-size: 15px;'
		. 'white-space: pre-wrap;'
		. 'word-wrap: break-word;';

	if ( ! empty( $_GET['arve-debug-arg'] ) ) {
		$html .= sprintf(
			'<pre style="%s">arg[%s]: %s</pre>',
			esc_attr( $pre_style ),
			esc_html( $_GET['arve-debug-arg'] ),
			arve_get_var_dump( $atts[ $_GET['arve-debug-arg'] ] )
		);
	}

	if ( isset( $_GET['arve-debug-atts'] ) ) {
		$html .= sprintf( '<pre style="%s">$atts: %s</pre>', esc_attr( $pre_style ), arve_get_var_dump( $input_atts ) );
		$html .= sprintf( '<pre style="%s">$arve: %s</pre>', esc_attr( $pre_style ), arve_get_var_dump( $atts ) );
	}

	if ( isset( $_GET['arve-debug-html'] ) ) {
		$html .= sprintf( '<pre style="%s"">%s</pre>', esc_attr( $pre_style ), esc_html( $input_html ) );
	}

	return $html;
}

function arve_build_meta_html( $atts ) {

		$meta = '';

		if ( ! empty( $atts['sources'] ) ) {

			$first_source = arve_get_first_array_value( $atts['sources'] );

			$meta .= sprintf( '<meta itemprop="contentURL" content="%s">', esc_attr( $first_source['src'] ) );
		}

		if ( ! empty( $atts['iframe_src'] ) ) {
			$meta .= sprintf( '<meta itemprop="embedURL" content="%s">', esc_attr( $atts['iframe_src'] ) );
		}

		if ( ! empty( $atts['upload_date'] ) ) {
			$meta .= sprintf( '<meta itemprop="uploadDate" content="%s">', esc_attr( $atts['upload_date'] ) );
		}

		if( ! empty( $atts['img_src'] ) ) :

			if( in_array( $atts['mode'], array( 'lazyload', 'lazyload-lightbox' ) ) ) {

				$meta .= sprintf(
					'<img%s>',
					arve_attr( array(
						'class'           => 'arve-thumbnail',
						'data-object-fit' => true,
						'itemprop'        => 'thumbnailUrl',
						'src'             => $atts['img_src'],
						'srcset'          => ! empty( $atts['img_srcset'] ) ? $atts['img_srcset'] : false,
						#'sizes'    => '(max-width: 700px) 100vw, 1280px',
						'alt'             => __( 'Video Thumbnail', ARVE_SLUG ),
					) )
				);

			} else {

				$meta .= sprintf(
					'<meta%s>',
					arve_attr( array(
						'itemprop' => 'thumbnailUrl',
						'content'  => $atts['img_src'],
					) )
				);
			}

		endif;

		if ( ! empty( $atts['title'] ) && in_array( $atts['mode'], array( 'lazyload', 'lazyload-lightbox' ) ) && empty( $atts['hide_title'] ) ) {
			$meta .= '<h5 itemprop="name" class="arve-title">' . trim( $atts['title'] ) . '</h5>';
		} elseif( ! empty( $atts['title'] ) ) {
			$meta .= sprintf( '<meta itemprop="name" content="%s">', esc_attr( trim( $atts['title'] ) ) );
		}

		if ( ! empty( $atts['description'] ) ) {
			$meta .= '<span itemprop="description" class="arve-description arve-hidden">' . esc_html( trim( $atts['description'] ) ) . '</span>';
		}

		return $meta;
	}

function arve_build_promote_link_html( $arve_link ) {

	if ( $arve_link ) {
		return sprintf(
			'<a href="%s" title="%s" class="arve-promote-link" target="_blank">%s</a>',
			esc_url( 'https://nextgenthemes.com/plugins/advanced-responsive-video-embedder-pro/' ),
			esc_attr( __( 'Embedded with ARVE Advanced Responsive Video Embedder WordPress plugin', ARVE_SLUG) ),
			esc_html__( 'ARVE', ARVE_SLUG )
		);
	}

	return '';
}

function arve_arve_embed_container( $html, $atts ) {

	$attr['class'] = 'arve-embed-container';

	if ( false === $atts['aspect_ratio'] ) {
		$attr['style'] = 'height:auto;padding:0';
	} else {
		$attr['style'] = sprintf( 'padding-bottom:%F%%', arve_aspect_ratio_to_percentage( $atts['aspect_ratio'] ) );
	}

	return sprintf( '<div%s>%s</div>', arve_attr( $attr ), $html );
}

function arve_arve_wrapper( $output, $atts ) {

	$wrapper_class = sprintf(
		'arve-wrapper%s%s%s',
		empty( $atts['hover_effect'] ) ? '' : ' arve-hover-effect-' . $atts['hover_effect'],
		empty( $atts['align'] )        ? '' : ' align' . $atts['align'],
		( 'link-lightbox' == $atts['mode'] ) ? ' arve-hidden' : ''
	);

	$attr = array(
		'id'                   => $atts['embed_id'],
		'class'                => $wrapper_class,
		'data-arve-grow'       => ( 'lazyload' === $atts['mode'] && $atts['grow'] ) ? '' : null,
		'data-arve-mode'       => $atts['mode'],
		'data-arve-provider'   => $atts['provider'],
		'data-arve-webtorrent' => empty( $atts['webtorrent'] ) ? false : $atts['webtorrent'],
		'data-arve-autoplay'   => ( 'webtorrent' == $atts['provider'] && $atts['autoplay'] ) ? true : false,
		'data-arve-controls'   => ( 'webtorrent' == $atts['provider'] && $atts['controls'] ) ? true : false,
		#'data-arve-maxwidth'  => empty( $atts['maxwidth'] ) ? false : sprintf( '%dpx',             $atts['maxwidth'] ),
		'style'                => empty( $atts['maxwidth'] ) ? false : sprintf( 'max-width:%dpx;', $atts['maxwidth'] ),
		// Schema.org
		'itemscope' => '',
		'itemtype'  => 'http://schema.org/VideoObject',
	);

	return sprintf(
		'<%s%s>%s</%s>',
		( 'link-lightbox' == $atts['mode'] ) ? 'span' : 'div',
		arve_attr( $attr ),
		$output,
		( 'link-lightbox' == $atts['mode'] ) ? 'span' : 'div'
	);
}

function arve_video_or_iframe( $atts ) {

	if ( 'veoh' == $atts['provider'] ) {

		return arve_create_object( $atts );

	} elseif ( 'html5' == $atts['provider'] ) {

		return arve_create_video_tag( $atts );

	} elseif( 'webtorrent' == $atts['provider'] ) {

		return '<div class="arve-webtorrent-progress-bar"></div>';

	} else {

		return arve_create_iframe_tag( $atts );
	}
}

/**
 *
 *
 * @since    2.6.0
 */
function arve_create_iframe_tag( $atts ) {

	$options    = arve_get_options();
	$properties = arve_get_host_properties();

	$iframe_attr = array(
		'allowfullscreen' => '',
		'class'       => 'arve-iframe fitvidsignore',
		'frameborder' => '0',
		'name'        => $atts['iframe_name'],
		'scrolling'   => 'no',
		'src'         => $atts['iframe_src'],

		'width'       => ! empty( $atts['width'] )  ? $atts['width']  : false,
		'height'      => ! empty( $atts['height'] ) ? $atts['height'] : false,
	);

	if ( null === $atts['disable_flash'] ) {
		$atts['disable_flash'] = (bool) $properties[ $atts['provider'] ]['requires_flash'] ? false : true;
	}

	if ( $atts['disable_flash'] ) {
		$iframe_attr['sandbox'] = empty( $atts['iframe_sandbox'] ) ? 'allow-scripts allow-same-origin allow-popups' : $atts['iframe_sandbox'];
	}

	if ( in_array( $atts['mode'], array( 'lazyload', 'lazyload-lightbox', 'link-lightbox' ) ) ) {
		$lazyload_iframe_attr = arve_prefix_array_keys( 'data-', $iframe_attr );

		$output = sprintf( '<span class="arve-lazyload"%s></span>', arve_attr( $lazyload_iframe_attr ) );
	} else {
		$output = sprintf( '<iframe%s></iframe>', arve_attr( $iframe_attr ) );
	}

	return apply_filters( 'arve_iframe_tag', $output, $atts, $iframe_attr );
}

function arve_create_video_tag( $atts ) {

	$sources_html = '';

	if ( in_array( $atts['mode'], array( 'lazyload', 'lazyload-lightbox' ) ) ) {
		$atts['autoplay'] = null;
	}

	$video_attr = array(
		'autoplay' => in_array( $atts['mode'], array( 'lazyload', 'lazyload-lightbox', 'link-lightbox' ) ) ? false : $atts['autoplay'],
		'class'    => 'arve-video fitvidsignore',
		'controls' => $atts['controls'],
		'loop'     => $atts['loop'],
		'poster'   => isset( $atts['img_src'] ) ? $atts['img_src'] : false,
		'preload'  => $atts['preload'],
		'src'      => isset( $atts['video_src'] ) ? $atts['video_src'] : false,

		'width'    => ! empty( $atts['width'] )  ? $atts['width'] :  false,
		'height'   => ! empty( $atts['height'] ) ? $atts['height'] : false,
	);

	$output = sprintf(
		'<video%s>%s%s</video>',
		arve_attr( $video_attr, 'video' ),
		$atts['video_sources_html'],
		$atts['video_tracks']
	);

	return apply_filters( 'arve_video_tag', $output, $atts, $video_attr );
}

function arve_error( $message ) {

	return sprintf(
		'<p><strong>%s</strong> %s</p>',
		__('<abbr title="Advanced Responsive Video Embedder">ARVE</abbr> Error:', ARVE_SLUG ),
		$message
	);
}

function arve_output_errors( $atts ) {

	$errors = '';

	foreach ( $atts as $key => $value ) {
		if( is_wp_error( $value ) ) {
			$errors .= arve_error( $value->get_error_message() );
		}
	}

	return $errors;
}
