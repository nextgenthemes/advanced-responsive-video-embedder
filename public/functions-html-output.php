<?php

function arv3_get_var_dump( $var ) {
	ob_start();
	var_dump( $var );
	return ob_get_clean();
};

function arv3_get_debug_info( $atts, $v ) {

	$html = '';

	if ( isset( $_GET['arve-debug-options'] ) ) {

		static $show_options_debug = true;

		$options               = get_option( 'arve_options_main' );
		$options['shortcodes'] = get_option( 'arve_options_shortcodes' );
		$options['params']     = get_option( 'arve_options_params' );

		if ( $show_options_debug ) {
			$html .= sprintf( 'Options: <pre>%s</pre>', arv3_get_var_dump( $options ) );
		}
		$show_options_debug = false;
	}

	if ( ! empty( $_GET['arve-debug-arg'] ) ) {
		$html .= sprintf(
			'<pre>arg[%s]: %s</pre>',
			esc_html( $_GET['arve-debug-arg'] ),
			arv3_get_var_dump( $v[ $_GET['arve-debug-arg'] ] )
		);
	}

	if ( isset( $_GET['arve-debug'] ) ) {
		$html .= sprintf( '<pre>$atts: %s</pre>', arv3_get_var_dump( $atts ) );
		$html .= sprintf( '<pre>$v: %s</pre>', arv3_get_var_dump( $v ) );
	}

	return $html;
}

function arv3_build_meta_html( $v ) {

		$meta = '';

		if ( ! empty( $v['sources'] ) ) {

			$first_source = arv3_get_first_array_value( $v['sources'] );

			$meta .= sprintf( '<meta itemprop="contentURL" content="%s">', esc_attr( $first_source['src'] ) );
		}

		if ( ! empty( $v['iframe_src'] ) ) {
			$meta .= sprintf( '<meta itemprop="embedURL" content="%s">', esc_attr( $v['iframe_src'] ) );
		}

		if ( ! empty( $v['upload_date'] ) ) {
			$meta .= sprintf( '<meta itemprop="uploadDate" content="%s">', esc_attr( $v['upload_date'] ) );
		}

		if( ! empty( $v['thumbnail'] ) ) :

			if( in_array( $v['mode'], array( 'lazyload', 'lazyload-lightbox' ) ) ) {

				$meta .= sprintf(
					'<img %s>',
					arv3_attr( array(
						'class'    => 'arve-thumbnail',
						'itemprop' => 'thumbnailUrl',
						'src'      => $v['thumbnail'],
						'srcset'   => $v['thumbnail_srcset'],
						#'sizes'    => '(max-width: 700px) 100vw, 1280px',
						'alt'      => __( 'Video Thumbnail', 'advanced-responsive-video-embedder' ),
					) )
				);

			} else {

				$meta .= sprintf(
					'<meta %s>',
					arv3_attr( array(
						'itemprop' => 'thumbnailUrl',
						'content'  => $v['thumbnail'],
					) )
				);
			}

		endif;

		if ( ! empty( $v['title'] ) && in_array( $v['mode'], array( 'lazyload', 'lazyload-lightbox' ) ) && empty( $v['hide_title'] ) ) {
			$meta .= '<h5 itemprop="name" class="arve-title">' . esc_html( trim( $v['title'] ) ) . '</h5>';
		} elseif( ! empty( $v['title'] ) ) {
			$meta .= sprintf( '<meta itemprop="name" content="%s">', esc_attr( trim( $v['title'] ) ) );
		}

		if ( ! empty( $v['description'] ) ) {
			$meta .= '<span itemprop="description" class="arve-description arve-hidden">' . esc_html( trim( $v['description'] ) ) . '</span>';
		}

		return $meta;
	}

function arv3_build_promote_link_html( $arve_link ) {

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


function arv3_arve_embed_container( $html, $v ) {

		$attr['class'] = 'arve-embed-container';

		if( ! empty( $v['aspect_ratio'] ) ) {
			$attr['style'] = sprintf( 'height: 0; padding-bottom: %F%%;', arv3_aspect_ratio_to_padding( $v['aspect_ratio'] ) );
		}

		return sprintf( '<div%s>%s</div>', arv3_attr( $attr ), $html );
	}

function arv3_arve_wrapper( $output, $v ) {

		$wrapper_class = sprintf(
			'arve-wrapper%s%s%s',
			empty( $v['hover_effect'] ) ? '' : ' arve-hover-effect-' . $v['hover_effect'],
			empty( $v['align'] )        ? '' : ' align' . $v['align'],
			( 'link-lightbox' == $v['mode'] ) ? ' arve-hidden' : ''
		);

		$attr = array(
			'id'                   => 'video-' . $v['embed_id'],
			'class'                => $wrapper_class,
			'data-arve-grow'       => ( 'lazyload' === $v['mode'] && $v['grow'] ) ? '' : null,
			'data-arve-mode'       => $v['mode'],
			'data-arve-host'       => $v['provider'],
			'data-arve-webtorrent' => empty( $v['webtorrent'] ) ? false : $v['webtorrent'],
			'data-arve-autoplay'   => ( 'webtorrent' == $v['provider'] && $v['autoplay'] ) ? true : false,
			'data-arve-controls'   => ( 'webtorrent' == $v['provider'] && $v['controls'] ) ? true : false,
			#'data-arve-maxwidth'  => empty( $v['maxwidth'] ) ? false : sprintf( '%dpx',             $v['maxwidth'] ),
			'style'                => empty( $v['maxwidth'] ) ? false : sprintf( 'max-width: %dpx;', $v['maxwidth'] ),
			// Schema.org
			'itemscope' => '',
			'itemtype'  => 'http://schema.org/VideoObject',
		);

		return sprintf(
			'<div%s>%s</div>',
			arv3_attr( $attr ),
			$output
		);
	}

function arv3_video_or_iframe( $v ) {

	if ( 'veoh' == $v['provider'] ) {

		return arv3_create_object( $v );

	} elseif ( 'html5' == $v['provider'] ) {

		return arv3_create_video_tag( $v );

	} elseif( 'webtorrent' == $v['provider'] ) {

		return '<div class="arve-webtorrent-progress-bar"></div>';

	} else {

		return arv3_create_iframe_tag( $v );
	}
}

	/**
	 *
	 *
	 * @since    2.6.0
	 */
function arv3_create_iframe_tag( $v ) {

	$options    = arv3_get_options();
	$properties = arv3_get_host_properties();

	$iframe_attr = array(
		'allowfullscreen' => '',
		'class'       => 'arve-iframe fitvidsignore',
		'frameborder' => '0',
		'name'        => $v['iframe_name'],
		'sandbox'     => empty( $v['iframe_sandbox'] ) ? 'allow-scripts allow-same-origin allow-popups' : $v['iframe_sandbox'],
		'scrolling'   => 'no',
		'src'         => $v['iframe_src'],

		'height'      => is_feed() ? 480 : false,
		'width'       => is_feed() ? 853 : false,
	);

	if ( ! empty( $properties[ $v['provider'] ]['requires_flash'] ) ) {
		$iframe_attr['sandbox'] = false;
	}

	if ( in_array( $v['mode'], array( 'lazyload', 'lazyload-lightbox', 'link-lightbox' ) ) ) {
		$lazyload_iframe_attr = arv3_prefix_array_keys( 'data-', $iframe_attr );

		$iframe = sprintf( '<div class="arve-lazyload"%s></div>', arv3_attr( $lazyload_iframe_attr ) );
	} else {
		$iframe = sprintf( '<iframe%s></iframe>', arv3_attr( $iframe_attr ) );
	}

	return $iframe;
}

function arv3_error( $message ) {

	return sprintf(
		'<p><strong>%s</strong> %s</p>',
		__('<abbr title="Advanced Responsive Video Embedder">ARVE</abbr> Error:', ARVE_SLUG ),
		$message
	);
}

function arv3_print_styles() {

  $options = arv3_get_options();

  if ( (int) $options["video_maxwidth"] > 0 ) {
    $css = sprintf( '.arve-wrapper { max-width: %dpx; }', $options['video_maxwidth'] );

    echo '<style type="text/css">' . $css . "</style>\n";
  }
}

function arv3_create_video_tag( $v ) {

	$soures_html = '';

	if ( in_array( $v['mode'], array( 'lazyload', 'lazyload-lightbox' ) ) ) {
		$v['autoplay'] = null;
	}

	$video_attr = array(
		'autoplay' => $v['autoplay'],
		'class'    => 'arve-video',
		'controls' => $v['controls'],
		'loop'     => $v['loop'],
		'poster'   => $v['thumbnail'],
		'preload'  => $v['preload'],
		'src'      => isset( $v['video_src'] ) ? $v['video_src'] : false,

		'width'    => is_feed() ? 853 : false,
		'height'   => is_feed() ? 480 : false,
	);

	if ( isset( $v['video_sources'] ) ) {

		foreach ( $v['video_sources'] as $key => $value ) {
			$soures_html .= sprintf( '<source type="%s" src="%s">', $key, $value );
		}
	}

	return sprintf(
		'<video%s>%s</video>',
		arv3_attr( $video_attr ),
		$soures_html
	);
}
