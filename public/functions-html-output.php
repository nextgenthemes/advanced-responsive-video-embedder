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

	if ( ! empty( $_GET['arve-debug-attr'] ) ) {
		$html .= sprintf(
			'<pre style="%s">attr[%s]: %s</pre>',
			esc_attr( $pre_style ),
			esc_html( $_GET['arve-debug-attr'] ),
			arve_get_var_dump( $atts[ $_GET['arve-debug-attr'] ] )
		);
	}

	if ( isset( $_GET['arve-debug-atts'] ) ) {
		$html .= sprintf( '<pre style="%s">$atts: %s</pre>', esc_attr( $pre_style ), arve_get_var_dump( $input_atts ) );
		$html .= sprintf( '<pre style="%s">$arve: %s</pre>', esc_attr( $pre_style ), arve_get_var_dump( $atts ) );
	}

	if ( isset( $_GET['arve-debug-html'] ) ) {
		$html .= sprintf( '<pre style="%s">%s</pre>', esc_attr( $pre_style ), esc_html( $input_html ) );
	}

	return $html;
}

function arve_build_meta_html( $a ) {

		$meta = '';

		if ( ! empty( $a['sources'] ) ) {

			$first_source = arve_get_first_array_value( $a['sources'] );

			$meta .= sprintf( '<meta itemprop="contentURL" content="%s">', esc_attr( $first_source['src'] ) );
		}

		if ( ! empty( $a['iframe_src'] ) ) {
			$meta .= sprintf( '<meta itemprop="embedURL" content="%s">', esc_attr( $a['iframe_src'] ) );
		}

		if ( ! empty( $a['upload_date'] ) ) {
			$meta .= sprintf( '<meta itemprop="uploadDate" content="%s">', esc_attr( $a['upload_date'] ) );
		}

		if ( ! empty( $a['duration'] ) ) {
			$meta .= sprintf( '<meta itemprop="duration" content="PT%s">', esc_attr( $a['duration'] ) );
		}

		if ( ! empty( $a['rating'] ) ) {
			$meta .= '<span itemprop="aggregateRating" itemscope="" itemtype="http://schema.org/AggregateRating">';
			$meta .= sprintf( '<meta itemprop="ratingValue" content="%s">', esc_attr( $a['rating'] ) );
			if ( ! empty( $a['review_count'] ) ) {
				$meta .= sprintf( '<meta itemprop="reviewCount" content="%s">', esc_attr( $a['review_count'] ) );
			}
			$meta .= '</span>';
		}

		if( ! empty( $a['img_src'] ) ) :

			if( in_array( $a['mode'], array( 'lazyload', 'lazyload-lightbox' ) ) ) {

				$meta .= sprintf(
					'<img%s>',
					arve_attr( array(
						'class'           => 'arve-thumbnail',
						'data-object-fit' => true,
						'itemprop'        => 'thumbnailUrl',
						'src'             => $a['img_src'],
						'srcset'          => ! empty( $a['img_srcset'] ) ? $a['img_srcset'] : false,
						#'sizes'    => '(max-width: 700px) 100vw, 1280px',
						'alt'             => __( 'Video Thumbnail', 'advanced-responsive-video-embedder' ),
					) )
				);

			} else {

				$meta .= sprintf(
					'<meta%s>',
					arve_attr( array(
						'itemprop' => 'thumbnailUrl',
						'content'  => $a['img_src'],
					) )
				);
			}

		endif;

		if ( ! empty( $a['title'] ) && in_array( $a['mode'], array( 'lazyload', 'lazyload-lightbox' ) ) && empty( $a['hide_title'] ) ) {
			$meta .= '<h5 itemprop="name" class="arve-title">' . trim( $a['title'] ) . '</h5>';
		} elseif( ! empty( $a['title'] ) ) {
			$meta .= sprintf( '<meta itemprop="name" content="%s">', esc_attr( trim( $a['title'] ) ) );
		}

		if ( ! empty( $a['description'] ) ) {
			$meta .= sprintf( '<div itemprop="description" class="arve-description arve-hidden">%s</div>', esc_attr( trim( $a['description'] ) ) );
		}

		return $meta;
	}

function arve_build_promote_link_html( $arve_link ) {

	if ( $arve_link ) {
		return sprintf(
			'<a href="%s" title="%s" class="arve-promote-link" target="_blank">%s</a>',
			esc_url( 'https://nextgenthemes.com/plugins/advanced-responsive-video-embedder-pro/' ),
			esc_attr( __( 'Embedded with ARVE Advanced Responsive Video Embedder WordPress plugin', 'advanced-responsive-video-embedder') ),
			esc_html__( 'ARVE', 'advanced-responsive-video-embedder' )
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

function arve_arve_wrapper( $html, $atts ) {

	$element = ( 'link-lightbox' == $atts['mode'] ) ? 'span' : 'div';

	return sprintf(
		'<%s%s>%s</%s>',
		$element,
		arve_attr( $atts['wrapper_attr'] ),
		$html,
		$element
	);
}

function arve_video_or_iframe( $atts ) {

	switch( $atts['provider'] ) {

		case 'veoh':
			return arve_create_object( $atts );
			break;
		case 'html5':
			return arve_create_video_tag( $atts );
			break;
		default:
			return arve_create_iframe_tag( $atts );
			break;
	}
}

/**
 *
 *
 * @since    2.6.0
 */
function arve_create_iframe_tag( $a ) {

	if ( in_array( $a['mode'], array( 'lazyload', 'lazyload-lightbox', 'link-lightbox' ) ) ) {
		$html = sprintf(
			'<span class="arve-lazyload"%s></span>',
			arve_attr( arve_prefix_array_keys( 'data-', $a['iframe_attr'] ) )
		);
	} else {
		$html = sprintf( '<iframe%s></iframe>', arve_attr( $a['iframe_attr'] ) );
	}

	return apply_filters( 'arve_iframe_tag', $html, $a, $a['iframe_attr'] );
}

function arve_create_video_tag( $a ) {

	$html = sprintf(
		'<video%s>%s%s</video>',
		arve_attr( $a['video_attr'] ),
		$a['video_sources_html'],
		$a['video_tracks_html']
	);

	return apply_filters( 'arve_video_tag', $html, $a, $a['video_attr'] );
}

function arve_create_object_tag( $a ) {

	$html = sprintf(
		'<video%s>%s%s</video>',
		arve_attr( $a['video_attr'] ),
		$a['video_sources_html'],
		$a['video_tracks_html']
	);

	return apply_filters( 'arve_video_tag', $html, $a, $a['video_attr'] );
}

function arve_error( $message ) {

	return sprintf(
		'<p><strong>%s</strong> %s</p>',
		__('<abbr title="Advanced Responsive Video Embedder">ARVE</abbr> Error:', 'advanced-responsive-video-embedder' ),
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
