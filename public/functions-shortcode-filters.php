<?php

function arve_load_vimeo_api( $a ) {

	require_once ARVE_PATH . '/vendor/autoload.php';

	return $a;
}

function arve_get_wrapper_id( $a ) {

	static $wrapper_ids = array();
	$wrapper_id         = null;

	foreach ( array( 'id', 'mp4', 'm4v', 'webm', 'ogv', 'url', 'random_video_url', 'webtorrent' ) as $att ) {

		if ( ! empty( $a[ $att ] ) && is_string( $a[ $att ] ) ) {
			$wrapper_id = 'arve-' . $a[ $att ];
			$wrapper_id = preg_replace( '/[^a-zA-Z0-9-]/', '', $wrapper_id );
			break;
		}
	}

	if ( empty( $wrapper_id ) ) {
		return null;
	} else {
		$wrapper_ids[] = $wrapper_id;
	}

	if ( in_array( $wrapper_id, $wrapper_ids, true ) ) {
		$id_counts = array_count_values( $wrapper_ids );
		$id_count  = $id_counts[ $wrapper_id ];

		if ( $id_count >= 2 ) {
			$wrapper_id .= '-' . $id_count;
		}
	}

	return $wrapper_id;
}

function arve_sc_filter_attr( $a ) {

	$wrapper_id = arve_get_wrapper_id( $a );

	if ( empty( $wrapper_id ) ) {
		$a['wrapper_id_error'] = new WP_Error( 'wrapper_id', __( 'Wrapper ID could not be build, please report this bug.', ARVE_SLUG ) );
	}

	$align_class = empty( $a['align'] ) ? '' : ' align' . $a['align'];

	$a['wrapper_attr'] = array(
		'class'         => "arve-wrapper$align_class",
		'data-mode'     => $a['mode'],
		'data-provider' => $a['provider'],
		'id'            => $wrapper_id,
		'style'         => empty( $a['maxwidth'] ) ? false : sprintf( 'max-width:%dpx;', $a['maxwidth'] ),
		// Schema.org
		'itemscope'     => '',
		'itemtype'      => 'http://schema.org/VideoObject',
	);

	if ( 'html5' === $a['provider'] ) {

		$autoplay = in_array( $a['mode'], array( 'lazyload', 'lazyload-lightbox', 'link-lightbox' ), true ) ? false : $a['autoplay'];

		$a['video_attr'] = array(
			# WP
			'autoplay'           => $autoplay,
			'controls'           => $a['controls'],
			'controlslist'       => $a['controlslist'],
			'loop'               => $a['loop'],
			'preload'            => $a['preload'],
			'width'              => empty( $a['width'] ) ? false : $a['width'],
			'height'             => empty( $a['height'] ) ? false : $a['height'],
			'poster'             => empty( $a['img_src'] ) ? false : $a['img_src'],
			'src'                => empty( $a['video_src'] ) ? false : $a['video_src'],
			# ARVE only
			'class'              => 'arve-video fitvidsignore',
			'muted'              => $autoplay ? 'automuted' : $a['muted'],
			'playsinline'        => $a['playsinline'],
			'webkit-playsinline' => $a['playsinline'],
		);

	} else {

		$properties = arve_get_host_properties();
		$options    = arve_get_options();
		$iframe_src = arve_build_iframe_src( $a );
		$iframe_src = arve_add_query_args_to_iframe_src( $iframe_src, $a );
		$iframe_src = arve_add_autoplay_query_arg( $iframe_src, $a );

		if ( 'vimeo' === $a['provider'] && ! empty( $a['start'] ) ) {
			$iframe_src .= '#t=' . (int) $a['start'];
		}

		$a['iframe_attr'] = array(
			'allow'           => 'accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture',
			'allowfullscreen' => '',
			'class'           => 'arve-iframe fitvidsignore',
			'frameborder'     => '0',
			'name'            => $a['iframe_name'],
			'sandbox'         => 'allow-scripts allow-same-origin allow-presentation allow-popups allow-popups-to-escape-sandbox',
			'scrolling'       => 'no',
			'src'             => $iframe_src,
			'width'           => empty( $a['width'] ) ? false : $a['width'],
			'height'          => empty( $a['height'] ) ? false : $a['height'],
		);

		if ( 'vimeo' === $a['provider'] ) {
			$a['iframe_attr']['sandbox'] .= ' allow-forms';
		}

		if ( false === $a['sandbox'] ) {
			$a['iframe_attr']['sandbox'] = false;
		}
	}

	return $a;
}

function arve_sc_filter_validate( $a ) {

	$a['align'] = arve_validate_align( $a['align'], $a['provider'] );

	$a['mode'] = arve_validate_mode( $a['mode'], $a['provider'] );

	$a['autoplay']    = arve_validate_bool( $a['autoplay'], 'autoplay' );
	$a['arve_link']   = arve_validate_bool( $a['arve_link'], 'arve_link' );
	$a['loop']        = arve_validate_bool( $a['loop'], 'loop' );
	$a['controls']    = arve_validate_bool( $a['controls'], 'controls' );
	$a['sandbox']     = arve_validate_bool( $a['sandbox'], 'sandbox' );
	$a['muted']       = arve_validate_bool( $a['muted'], 'muted' );
	$a['playsinline'] = arve_validate_bool( $a['playsinline'], 'playsinline' );

	$a['maxwidth'] = (int) $a['maxwidth'];
	$a['maxwidth'] = (int) arve_maxwidth_when_aligned( $a['maxwidth'], $a['align'] );

	$a['id'] = arve_id_fixes( $a['id'], $a['provider'] );

	$a['aspect_ratio'] = arve_get_default_aspect_ratio( $a['aspect_ratio'], $a['provider'] );
	$a['aspect_ratio'] = arve_aspect_ratio_fixes( $a['aspect_ratio'], $a['provider'], $a['mode'] );
	$a['aspect_ratio'] = arve_validate_aspect_ratio( $a['aspect_ratio'] );

	return $a;
}

function arve_sc_filter_set_fixed_dimensions( $a ) {

	$width = 480;

	$a['width']  = $width;
	$a['height'] = arve_calculate_height( $width, $a['aspect_ratio'] );

	return $a;
}

function arve_sc_filter_sanitise( $atts ) {

	if ( ! empty( $atts['src'] ) ) {
		$atts['url'] = $atts['src'];
	}

	foreach ( $atts as $key => $value ) {

		$atts[ $key ] = (string) $value;

		if ( '' === $value ) {
			$atts[ $key ] = null;
		}
	}

	return $atts;
}

function arve_sc_filter_missing_attribute_check( $atts ) {

	# Old shortcodes
	if ( ! array_key_exists( 'url', $atts ) ) {
		return $atts;
	}

	$required_attributes   = arve_get_html5_attributes();
	$required_attributes[] = 'url';

	$array = array_intersect_key( $atts, array_flip( $required_attributes ) );

	if ( count( array_filter( $array ) ) !== count( $array ) ) {

		$atts['missing_atts_error'] = arve_error(
			sprintf(
				esc_html__( 'The [arve] shortcode needs one of this attributes %s', ARVE_SLUG ),
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

	if ( is_numeric( $atts['thumbnail'] ) ) {

		$attchment_id = $atts['thumbnail'];

		$atts['img_src']    = arve_get_attachment_image_url_or_srcset( 'url',    $attchment_id );
		$atts['img_srcset'] = arve_get_attachment_image_url_or_srcset( 'srcset', $attchment_id );

	} elseif ( arve_validate_url( $atts['thumbnail'] ) ) {

		$atts['img_src'] = $atts['thumbnail'];

	} else {

		$atts['img_src'] = new WP_Error( 'thumbnail', __( 'Not a valid thumbnail URL or Media ID given', ARVE_SLUG ) );
	}

	return $atts;
}

function arve_sc_filter_get_media_gallery_video( $atts ) {

	$html5_ext = arve_get_html5_attributes();

	foreach ( $html5_ext as $ext ) {

		if ( ! empty( $atts[ $ext ] ) && is_numeric( $atts[ $ext ] ) ) {
			$atts[ $ext ] = wp_get_attachment_url( $atts[ $ext ] );
		}
	}

	return $atts;
}

function arve_sc_filter_detect_provider_and_id_from_url( $atts ) {

	$properties = arve_get_host_properties();

	if ( ! empty( $atts['provider'] ) || empty( $atts['url'] ) ) {
		return $atts;
	}

	foreach ( $properties as $host_id => $host ) :

		if ( empty( $host['regex'] ) ) {
			continue;
		}

		$preg_match = preg_match( '#' . $host['regex'] . '#i', $atts['url'], $matches );

		if ( 1 !== $preg_match ) {
			continue;
		}

		foreach ( $matches as $key => $value ) {

			if ( is_string( $key ) ) {
				$atts['provider'] = $host_id;
				$atts[ $key ]     = $matches[ $key ];
			}
		}

	endforeach;

	return $atts;
}

function arve_sc_filter_detect_query_args( $atts ) {

	if ( empty( $atts['url'] ) ) {
		return $atts;
	}

	$to_extract = array(
		'brightcove' => array( 'videoId', 'something' ),
	);

	foreach ( $to_extract as $provider => $parameters ) {

		if ( $provider !== $atts['provider'] ) {
			return $atts;
		}

		$query_array = arve_url_query_array( $atts['url'] );

		foreach ( $parameters as $key => $parameter ) {

			$att_name = $atts['provider'] . "_$parameter";

			if ( empty( $query_array[ $parameter ] ) ) {
				$atts[ $att_name ] = new WP_Error( $att_name, "$parameter not found in URL" );
			} else {
				$atts[ $att_name ] = $query_array[ $parameter ];
			}
		}
	}

	return $atts;
}

function arve_sc_filter_detect_youtube_playlist( $atts ) {

	if (
		'youtube' !== $atts['provider'] ||
		( empty( $atts['url'] ) && empty( $atts['id'] ) )
	) {
		return $atts;
	}

	if ( empty( $atts['url'] ) ) {
		# Not a url but it will work
		$url = str_replace( array( '&list=', '&amp;list=' ), '?list=', $atts['id'] );
	} else {
		$url = $atts['url'];
	}

	$query_array = arve_url_query_array( $url );

	if ( empty( $query_array['list'] ) ) {
		return $atts;
	}

	$atts['id'] = strtok( $atts['id'], '?' );
	$atts['id'] = strtok( $atts['id'], '&' );

	$atts['youtube_playlist_id'] = $query_array['list'];
	$atts['parameters']         .= 'list=' . $query_array['list'];

	return $atts;
}

function arve_get_video_type( $ext ) {

	switch ( $ext ) {
		case 'ogv':
		case 'ogm':
			return 'video/ogg';
		default:
			return 'video/' . $ext;
	}
}

function arve_sc_filter_detect_html5( $atts ) {

	if ( ! empty( $atts['provider'] ) && 'html5' !== $atts['provider'] ) {
		return $atts;
	}

	$html5_extensions           = arve_get_html5_attributes();
	$atts['video_sources_html'] = '';

	foreach ( $html5_extensions as $ext ) :

		if ( ! empty( $atts[ $ext ] ) ) {

			if ( arve_starts_with( $atts[ $ext ], 'https://www.dropbox.com' ) ) {
				$atts[ $ext ] = add_query_arg( 'dl', 1, $atts[ $ext ] );
			}

			$atts['video_sources_html'] .= sprintf( '<source type="%s" src="%s">', arve_get_video_type( $ext ), $atts[ $ext ] );
		}

		if ( ! empty( $atts['url'] ) && arve_ends_with( $atts['url'], ".$ext" ) ) {

			if ( arve_starts_with( $atts['url'], 'https://www.dropbox.com' ) ) {
				$atts['url'] = add_query_arg( 'dl', 1, $atts['url'] );
			}

			$atts['video_src'] = $atts['url'];
		}

	endforeach;

	if ( empty( $atts['video_src'] ) && empty( $atts['video_sources_html'] ) ) {
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

	if ( 'html5' !== $atts['provider'] ) {
		return $atts;
	}

	$atts['video_tracks_html'] = '';

	for ( $n = 1; $n <= ARVE_NUM_TRACKS; $n++ ) {

		if ( empty( $atts[ "track_{$n}" ] ) ) {
			return $atts;
		}

		preg_match( '#-(?<type>captions|chapters|descriptions|metadata|subtitles)-(?<lang>[a-z]{2}).vtt$#i', $atts[ "track_{$n}" ], $matches );

		if ( empty( $matches[1] ) ) {
			$atts[ "track_{$n}" ] = new WP_Error( 'track', __( 'Track kind or language code could not detected from filename', ARVE_SLUG ) );
			return $atts;
		}

		$label = empty( $atts[ "track_{$n}_label" ] ) ? arve_get_language_name_from_code( $matches['lang'] ) : $atts[ "track_{$n}_label" ];

		$attr = array(
			'default' => ( 1 === $n ) ? true : false,
			'kind'    => $matches['type'],
			'label'   => $label,
			'src'     => $atts[ "track_{$n}" ],
			'srclang' => $matches['lang'],
		);

		$atts['video_tracks_html'] .= sprintf( '<track%s>', arve_attr( $attr ) );
	}

	return $atts;
}
