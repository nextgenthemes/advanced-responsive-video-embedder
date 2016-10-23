<?php

function arve_get_var_dump( $var ) {
	ob_start();
	var_dump( $var );
	return ob_get_clean();
};

function arve_get_debug_info( $arve, $input_atts ) {

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

	if ( ! empty( $_GET['arve-debug-arg'] ) ) {
		$html .= sprintf(
			'<pre>arg[%s]: %s</pre>',
			esc_html( $_GET['arve-debug-arg'] ),
			arve_get_var_dump( $arve[ $_GET['arve-debug-arg'] ] )
		);
	}

	if ( isset( $_GET['arve-debug'] ) ) {
		$html .= sprintf( '<pre>$atts: %s</pre>', arve_get_var_dump( $input_atts ) );
		$html .= sprintf( '<pre>$arve: %s</pre>', arve_get_var_dump( $arve ) );
	}

	return $html;
}

function arve_build_meta_html( $arve ) {

		$meta = '';

		if ( ! empty( $arve['sources'] ) ) {

			$first_source = arve_get_first_array_value( $arve['sources'] );

			$meta .= sprintf( '<meta itemprop="contentURL" content="%s">', esc_attr( $first_source['src'] ) );
		}

		if ( ! empty( $arve['iframe_src'] ) ) {
			$meta .= sprintf( '<meta itemprop="embedURL" content="%s">', esc_attr( $arve['iframe_src'] ) );
		}

		if ( ! empty( $arve['upload_date'] ) ) {
			$meta .= sprintf( '<meta itemprop="uploadDate" content="%s">', esc_attr( $arve['upload_date'] ) );
		}

		if( ! empty( $arve['thumbnail'] ) ) :

			if( in_array( $arve['mode'], array( 'lazyload', 'lazyload-lightbox' ) ) ) {

				$meta .= sprintf(
					'<img%s>',
					arve_attr( array(
						'class'           => 'arve-thumbnail',
						'data-object-fit' => true,
						'itemprop'        => 'thumbnailUrl',
						'src'             => $arve['thumbnail'],
						'srcset'          => $arve['thumbnail_srcset'],
						#'sizes'    => '(max-width: 700px) 100vw, 1280px',
						'alt'             => __( 'Video Thumbnail', 'advanced-responsive-video-embedder' ),
					) )
				);

			} else {

				$meta .= sprintf(
					'<meta%s>',
					arve_attr( array(
						'itemprop' => 'thumbnailUrl',
						'content'  => $arve['thumbnail'],
					) )
				);
			}

		endif;

		if ( ! empty( $arve['title'] ) && in_array( $arve['mode'], array( 'lazyload', 'lazyload-lightbox' ) ) && empty( $arve['hide_title'] ) ) {
			$meta .= '<h5 itemprop="name" class="arve-title">' . esc_html( trim( $arve['title'] ) ) . '</h5>';
		} elseif( ! empty( $arve['title'] ) ) {
			$meta .= sprintf( '<meta itemprop="name" content="%s">', esc_attr( trim( $arve['title'] ) ) );
		}

		if ( ! empty( $arve['description'] ) ) {
			$meta .= '<span itemprop="description" class="arve-description arve-hidden">' . esc_html( trim( $arve['description'] ) ) . '</span>';
		}

		return $meta;
	}

function arve_build_promote_link_html( $arve_link ) {

		if ( $arve_link ) {
			return sprintf(
				'<a href="%s" title="%s" class="arve-promote-link">%s</a>',
				esc_url( 'https://nextgenthemes.com/plugins/advanced-responsive-video-embedder-pro/' ),
				esc_attr( __('Embedded with ARVE Advanced Responsive Video Embedder WordPress plugin', 'advanced-responsive-video-embedder') ),
				esc_html__( 'ARVE', 'advanced-responsive-video-embedder' )
			);
		}

		return '';
	}


function arve_arve_embed_container( $html, $arve ) {

		$attr['class'] = 'arve-embed-container';

		if( ! empty( $arve['aspect_ratio'] ) ) {
			$attr['style'] = sprintf( 'height: 0; padding-bottom: %F%%;', arve_aspect_ratio_to_padding( $arve['aspect_ratio'] ) );
		}

		return sprintf( '<div%s>%s</div>', arve_attr( $attr ), $html );
	}

function arve_arve_wrapper( $output, $arve ) {

		$wrapper_class = sprintf(
			'arve-wrapper%s%s%s',
			empty( $arve['hover_effect'] ) ? '' : ' arve-hover-effect-' . $arve['hover_effect'],
			empty( $arve['align'] )        ? '' : ' align' . $arve['align'],
			( 'link-lightbox' == $arve['mode'] ) ? ' arve-hidden' : ''
		);

		$attr = array(
			'id'                   => $arve['embed_id'],
			'class'                => $wrapper_class,
			'data-arve-grow'       => ( 'lazyload' === $arve['mode'] && $arve['grow'] ) ? '' : null,
			'data-arve-mode'       => $arve['mode'],
			'data-arve-provider'   => $arve['provider'],
			'data-arve-webtorrent' => empty( $arve['webtorrent'] ) ? false : $arve['webtorrent'],
			'data-arve-autoplay'   => ( 'webtorrent' == $arve['provider'] && $arve['autoplay'] ) ? true : false,
			'data-arve-controls'   => ( 'webtorrent' == $arve['provider'] && $arve['controls'] ) ? true : false,
			#'data-arve-maxwidth'  => empty( $arve['maxwidth'] ) ? false : sprintf( '%dpx',             $arve['maxwidth'] ),
			'style'                => empty( $arve['maxwidth'] ) ? false : sprintf( 'max-width: %dpx;', $arve['maxwidth'] ),
			// Schema.org
			'itemscope' => '',
			'itemtype'  => 'http://schema.org/VideoObject',
		);

		return sprintf(
			'<div%s>%s</div>',
			arve_attr( $attr ),
			$output
		);
	}

function arve_video_or_iframe( $arve ) {

	if ( 'veoh' == $arve['provider'] ) {

		return arve_create_object( $arve );

	} elseif ( 'html5' == $arve['provider'] ) {

		return arve_create_video_tag( $arve );

	} elseif( 'webtorrent' == $arve['provider'] ) {

		return '<div class="arve-webtorrent-progress-bar"></div>';

	} else {

		return arve_create_iframe_tag( $arve );
	}
}

	/**
	 *
	 *
	 * @since    2.6.0
	 */
function arve_create_iframe_tag( $arve ) {

	$options    = arve_get_options();
	$properties = arve_get_host_properties();

	#d($arve);

	$iframe_attr = array(
		'allowfullscreen' => '',
		'class'       => 'arve-iframe fitvidsignore',
		'frameborder' => '0',
		'name'        => $arve['iframe_name'],
		'sandbox'     => empty( $arve['iframe_sandbox'] ) ? 'allow-scripts allow-same-origin allow-popups' : $arve['iframe_sandbox'],
		'scrolling'   => 'no',
		'src'         => $arve['iframe_src'],
		'height'      => is_feed() ? 480 : false,
		'width'       => is_feed() ? 853 : false,
	);

	if ( ! empty( $properties[ $arve['provider'] ]['requires_flash'] ) ) {
		$iframe_attr['sandbox'] = false;
	}

	if ( in_array( $arve['mode'], array( 'lazyload', 'lazyload-lightbox', 'link-lightbox' ) ) ) {
		$lazyload_iframe_attr = arve_prefix_array_keys( 'data-', $iframe_attr );

		$iframe = sprintf( '<div class="arve-lazyload"%s></div>', arve_attr( $lazyload_iframe_attr ) );
	} else {
		$iframe = sprintf( '<iframe%s></iframe>', arve_attr( $iframe_attr ) );
	}

	return $iframe;
}

function arve_error( $message ) {

	return sprintf(
		'<p><strong>%s</strong> %s</p>',
		__('<abbr title="Advanced Responsive Video Embedder">ARVE</abbr> Error:', ARVE_SLUG ),
		$message
	);
}

function arve_print_styles() {

  $options = arve_get_options();

  if ( (int) $options["video_maxwidth"] > 0 ) {
    $css = sprintf( '.arve-wrapper { max-width: %dpx; }', $options['video_maxwidth'] );

    echo '<style type="text/css">' . $css . "</style>\n";
  }
}

function arve_create_video_tag( $arve ) {

	$soures_html = '';

	if ( in_array( $arve['mode'], array( 'lazyload', 'lazyload-lightbox' ) ) ) {
		$arve['autoplay'] = null;
	}

	$video_attr = array(
		'autoplay' => $arve['autoplay'],
		'class'    => 'arve-video',
		'controls' => $arve['controls'],
		'loop'     => $arve['loop'],
		'poster'   => $arve['thumbnail'],
		'preload'  => $arve['preload'],
		'src'      => isset( $arve['video_src'] ) ? $arve['video_src'] : false,

		'width'    => is_feed() ? 853 : false,
		'height'   => is_feed() ? 480 : false,
	);

	if ( isset( $arve['video_sources'] ) ) {

		foreach ( $arve['video_sources'] as $key => $value ) {
			$soures_html .= sprintf( '<source type="%s" src="%s">', $key, $value );
		}
	}

	return sprintf(
		'<video%s>%s%s</video>',
		arve_attr( $video_attr, 'video' ),
		$soures_html,
		$arve['video_tracks']
	);
}

function arve_output_errors( $arve ) {

	$errors = '';

	foreach ( $arve as $key => $value ) {
		if( is_wp_error( $value ) ) {
			$errors .= arve_error( $value->get_error_message() );
		}
	}

	return $errors;
}
