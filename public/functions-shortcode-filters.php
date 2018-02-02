<?php

function arve_sc_filter_attr( $a ) {

	$wrapper_id = null;

	foreach ( array( 'id', 'mp4', 'm4v', 'webm', 'ogv', 'src', 'webtorrent' ) as $att ) {

		if ( ! empty( $a[ $att ] ) && is_string( $a[ $att ] ) ) {

			$wrapper_id = preg_replace( '/[^-a-zA-Z0-9]+/', '', $a[ $att ] );
			$wrapper_id = str_replace(
				array( 'https', 'http', 'wp-contentuploads' ),
				'',
				$wrapper_id
			);
			$wrapper_id = 'video-' . $wrapper_id;
			break;
		}
	}

	if ( empty( $wrapper_id ) ) {
		$a['wrapper_id_error'] = new WP_Error( 'embed_id', __( 'Element ID could not be build, please report this bug.', 'advanced-responsive-video-embedder' ) );
	}

	static $i = 0;
	$i++;

	$align_class = empty( $a['align'] ) ? '' : ' align' . $a['align'];

	$a['wrapper_attr'] = array(
		'class'         => "arve-wrapper$align_class",
		'data-mode'     => $a['mode'],
		'data-provider' => $a['provider'],
		'id'            => "arve-video-{$wrapper_id}",
		'style'         => empty( $a['maxwidth'] ) ? false : sprintf( 'max-width:%dpx;', $a['maxwidth'] ),
		// Schema.org
		'itemscope' => '',
		'itemtype'  => 'http://schema.org/VideoObject',
	);

	if( 'html5' == $a['provider'] ) {

		$a['video_attr'] = array(
			# WP
			'autoplay'     => in_array( $a['mode'], array( 'lazyload', 'lazyload-lightbox', 'link-lightbox' ) ) ? false : $a['autoplay'],
			'controls'     => $a['controls'],
			'controlslist' => $a['controlslist'],
			'loop'         => $a['loop'],
			'preload'      => $a['preload'],
			'width'        => empty( $a['width'] )     ? false : $a['width'],
			'height'       => empty( $a['height'] )    ? false : $a['height'],
			'poster'       => empty( $a['img_src'] )   ? false : $a['img_src'],
			'src'          => empty( $a['video_src'] ) ? false : $a['video_src'],
			# ARVE only
			'class'       => 'arve-video fitvidsignore',
			'muted'       => $a['muted'],
			'playsinline' => $a['playsinline'],
			'webkit-playsinline' => $a['playsinline'],
		);

	} else {

		$properties = arve_get_host_properties();

		if ( empty( $a['src'] ) ) {
			$a['src'] = arve_build_iframe_src( $a );
		}

		$a['src'] = arve_add_query_args_to_iframe_src( $a['src'], $a );
		$a['src'] = arve_add_autoplay_query_arg( $a['src'], $a );

		if ( 'vimeo' == $a['provider'] && ! empty( $a['start'] ) ) {
			$a['src'] .= '#t=' . (int) $a['start'];
		}

		$iframe_sandbox = 'allow-scripts allow-same-origin allow-presentation allow-popups';

		if ( 'vimeo' == $a['provider'] ) {
			$iframe_sandbox .= ' allow-forms';
		}

		if ( null === $a['disable_flash'] && $properties[ $a['provider'] ]['requires_flash'] ) {
			$iframe_sandbox = false;
		}

		$a['iframe_attr'] = array(
			'allowfullscreen' => '',
			'class'       => 'arve-iframe fitvidsignore',
			'frameborder' => '0',
			'name'        => $a['iframe_name'],
			'scrolling'   => 'no',
			'src'         => $a['src'],
			'sandbox'     => $iframe_sandbox,
			'width'       => empty( $a['width'] )  ? false : $a['width'],
			'height'      => empty( $a['height'] ) ? false : $a['height'],
		);
	}

	return $a;
}

function arve_sc_filter_validate( $a ) {

	if ( ! empty( $a['src'] ) && ! arve_validate_url( $a['src'] ) ) {
		$a['src'] = new WP_Error( 'thumbnail', sprintf( __( '<code>%s</code> is not a valid url', 'advanced-responsive-video-embedder' ), esc_html( $a['src'] ) ) );
	}

	$a['align'] = arve_validate_align( $a['align'], $a['provider'] );

	$a['mode'] = arve_validate_mode( $a['mode'], $a['provider'] );

	$a['autoplay']      = arve_validate_bool( $a['autoplay'], 'autoplay' );
	$a['arve_link']     = arve_validate_bool( $a['arve_link'], 'arve_link' );
	$a['loop']          = arve_validate_bool( $a['loop'], 'loop' );
	$a['controls']      = arve_validate_bool( $a['controls'], 'controls' );
	$a['disable_flash'] = arve_validate_bool( $a['disable_flash'], 'disable_flash' );
	$a['muted']         = arve_validate_bool( $a['muted'], 'muted' );
	$a['playsinline']   = arve_validate_bool( $a['playsinline'], 'playsinline' );

	$a['maxwidth'] = (int) $a['maxwidth'];
	$a['maxwidth'] = (int) arve_maxwidth_when_aligned( $a['maxwidth'], $a['align'] );

	$a['id'] = arve_id_fixes( $a['id'], $a['provider'] );

	$a['aspect_ratio'] = arve_get_default_aspect_ratio( $a['aspect_ratio'], $a );
	$a['aspect_ratio'] = arve_aspect_ratio_fixes( $a['aspect_ratio'], $a['provider'], $a['mode'] );
	$a['aspect_ratio'] = arve_validate_aspect_ratio( $a['aspect_ratio'] );

	return $a;
}

function arve_sc_filter_set_fixed_dimensions( $a ) {

	if ( ! empty( $a['oembed_data']->width ) ) {
		$width = $a['oembed_data']->width;
	} else {
		$width = 640;
	}

	$a['width']  = $width;
	$a['height'] = arve_calculate_height( $width, $a['aspect_ratio'] );

	return $a;
}

function arve_sc_filter_autoplay_off_after_ran_once( $a ) {

	if ( 'normal' !== $a['mode'] ) {
		return $a;
	}

	static $did_run = false;

	if ( $did_run ) {
		$a['autoplay'] = false;
	}

	if ( ! $did_run && $a['autoplay'] ) {
		$did_run = true;
	}

	return $a;
}

function arve_sc_filter_sanitise( $atts ) {

	foreach ( $atts as $key => $value ) {

		if ( 'oembed_data' === $key || 'parameters' === $key || null === $value ) {
			continue;
		}

		if( ! is_string( $value ) ) {
			$atts[ $key ] = arve_error( sprintf( __( '<code>%s</code> is not a string. Only Strings should be passed to the shortcode function', 'advanced-responsive-video-embedder' ), $key ) );
		}
	}

	return $atts;
}

function arve_sc_filter_missing_attribute_check( $atts ) {

	$required_attributes   = arve_get_html5_attributes();
	$required_attributes[] = 'src';
	$required_attributes[] = 'id';
	$required_attributes[] = 'provider';

	$array = array_intersect_key( $atts, array_flip( $required_attributes ) );

	if( count( array_filter( $array ) ) != count( $array ) ) {

		$atts['missing_atts_error'] = arve_error(
			sprintf(
				esc_html__( 'The [arve] shortcode needs one of this attributes %s', 'advanced-responsive-video-embedder' ),
				implode( $required_attributes )
			)
		);
	}

	return $atts;
}

function arve_sc_filter_get_media_gallery_thumbnail( $atts ) {

	if ( empty( $atts['thumbnail'] ) ) {
		return $atts;
	}

	if( is_numeric( $atts['thumbnail'] ) ) {

		if( $found_url = wp_get_attachment_image_url( $atts['thumbnail'], 'small' ) ) {
			$atts['img_src'] = $found_url;
		} else {
			$atts['img_src'] = new WP_Error( 'wp thumbnail', __( 'No attachment with that ID', 'advanced-responsive-video-embedder' ) );
		}

		if( $found_srcset = wp_get_attachment_image_srcset( $atts['thumbnail'], 'small' ) ) {
			$atts['img_srcset'] = $found_srcset;
		} else {
			$atts['img_srcset'] = new WP_Error( 'wp thumbnail', __( 'No attachment with that ID', 'advanced-responsive-video-embedder' ) );
		}

	} elseif ( arve_validate_url( $atts['thumbnail'] ) ) {

		$atts['img_src']    = $atts['thumbnail'];
		$atts['img_srcset'] = false;

	} else {

		$atts['img_src'] = new WP_Error( 'thumbnail', __( 'Not a valid thumbnail URL or Media ID given', 'advanced-responsive-video-embedder' ) );
	}

	return $atts;
}

function arve_sc_filter_get_media_gallery_video( $atts ) {

	$html5_ext = arve_get_html5_attributes();

	foreach ( $html5_ext as $ext ) {

		if( ! empty( $atts[ $ext ] ) && is_numeric( $atts[ $ext ] ) ) {
			$atts[ $ext ] = wp_get_attachment_url( $atts[ $ext ] );
		}
	}

	return $atts;
}

function arve_sc_filter_detect_query_args( $atts ) {

	if( empty( $atts['url'] ) ) {
		return $atts;
	}

	$to_extract = array(
		'brightcove' => array( 'videoId', 'something' ),
	);

	foreach ( $to_extract as $provider => $parameters ) {

		if( $provider != $atts['provider'] ) {
			return $atts;
		}

		$query_array = arve_url_query_array( $atts['url'] );

		foreach ( $parameters as $key => $parameter ) {

			$att_name = $atts['provider'] . "_$parameter";

			if( empty( $query_array[ $parameter ] ) ) {
				$atts[ $att_name ] = new WP_Error( $att_name, "$parameter not found in URL" );
			} else {
				$atts[ $att_name ] = $query_array[ $parameter ];
			}
		}
	}

	return $atts;
}

function arve_sc_filter_detect_youtube_playlist( $atts ) {

	if(
		'youtube' != $atts['provider'] ||
		( empty( $atts['url'] ) && empty( $atts['id'] ) )
	) {
		return $atts;
	}

	if( empty($atts['url']) ) {
		# Not a url but it will work
		$url = str_replace( array( '&list=', '&amp;list=' ), '?list=', $atts['id'] );
	} else {
		$url = $atts['url'];
	}

	$query_array = arve_url_query_array( $url );

	if( empty( $query_array['list'] ) ) {
		return $atts;
	}

	$atts['id'] = strtok( $atts['id'], '?' );
	$atts['id'] = strtok( $atts['id'], '&' );

	$atts['youtube_playlist_id'] = $query_array['list'];
	$atts['parameters']         .= 'list=' . $query_array['list'];

	return $atts;
}

function arve_sc_filter_detect_html5( $atts ) {

	$html5_extensions   = arve_get_html5_attributes();
	$html5_extensions[] = 'src';

	$atts['video_sources_html'] = '';

	foreach ( $html5_extensions as $ext ):

		if ( ! empty( $atts[ $ext ] ) && $type = arve_check_filetype( $atts[ $ext ], $ext) ) {

			if ( arve_starts_with( $atts[ $ext ], 'https://www.dropbox.com' ) ) {
				$atts[ $ext ] = add_query_arg( 'dl', 1, $atts[ $ext ] );
			}

			$atts['video_sources_html'] .= sprintf( '<source type="%s" src="%s">', $type, $atts[ $ext ] );
		}

		if ( ! empty( $atts['src'] ) && arve_ends_with( $atts['src'], ".$ext" ) ) {

			if ( arve_starts_with( $atts['src'], 'https://www.dropbox.com' ) ) {
				$atts['src'] = add_query_arg( 'dl', 1, $atts['src'] );
			}

			$atts['video_src'] = $atts['src'];
			/*
			$parse_url = parse_url( $atts['url'] );
			$pathinfo  = pathinfo( $parse_url['path'] );

			$url_ext         = $pathinfo['extension'];
			$url_without_ext = $parse_url['scheme'] . '://' . $parse_url['host'] . $path_without_ext;
			*/
		}

	endforeach;

	if( empty( $atts['video_src'] ) && empty( $atts['video_sources_html'] ) ) {
		unset( $atts['video_sources_html'] );
		return $atts;
	}

	$atts['provider'] = 'html5';

	return $atts;
}

function arve_sc_filter_iframe_fallback( $atts ) {

	if ( empty( $atts['provider'] ) ) {

		$atts['provider'] = 'iframe';

		if ( empty( $atts['id'] ) && ! empty( $atts['url'] ) ) {
			$atts['id'] = $atts['url'];
		}
	}

	return $atts;
}

function arve_sc_filter_build_tracks_html( $atts ) {

	if ( 'html5' != $atts['provider'] ) {
		return $atts;
	}

	$atts['video_tracks_html'] = '';

	for ( $n = 1; $n <= 10; $n++ ) {

		if ( empty( $atts[ "track_{$n}" ] ) ) {
			return $atts;
		}

		preg_match( '#-(captions|chapters|descriptions|metadata|subtitles)-([a-z]{2}).vtt$#i', $atts[ "track_{$n}" ], $matches );

		if ( empty( $matches[1] ) ) {
			$atts[ "track_{$n}" ] = new WP_Error( 'track', __( 'Track kind or language code could not detected from filename', 'advanced-responsive-video-embedder' ) );
			return $atts;
		}

		$label = empty( $atts[ "track_{$n}_label" ] ) ? arve_get_language_name_from_code( $matches[2] ) : $atts[ "track_{$n}_label" ];

		$attr = array(
			'default' => ( 1 === $n ) ? true : false,
			'kind'    => $matches[1],
			'label'   => $label,
			'src'     => $atts[ "track_{$n}" ],
			'srclang' => $matches[2],
		);

		$atts['video_tracks_html'] .= sprintf( '<track%s>', arve_attr( $attr) );
	}

	return $atts;
}
