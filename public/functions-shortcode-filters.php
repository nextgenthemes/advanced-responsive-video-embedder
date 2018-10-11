<?php
namespace Nextgenthemes\ARVE;

function get_wrapper_id( array $a ) {

	static $wrapper_ids = array();
	$wrapper_id         = null;

	foreach ( [ 'id', 'mp4', 'm4v', 'webm', 'ogv', 'url', 'random_video_url', 'webtorrent' ] as $att ) {

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

function sc_filter_attr( array $a ) {

	$wrapper_id = get_wrapper_id( $a );

	if ( empty( $wrapper_id ) ) {
		$a['wrapper_id_error'] = new WP_Error( 'wrapper_id', __( 'Wrapper ID could not be build, please report this bug.', 'advanced-responsive-video-embedder' ) );
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

		$a['video_attr'] = array(
			// WP
			'autoplay'           => in_array( $a['mode'], [ 'lazyload', 'lazyload-lightbox', 'link-lightbox' ], true ) ? false : $a['autoplay'],
			'controls'           => $a['controls'],
			'controlslist'       => $a['controlslist'],
			'loop'               => $a['loop'],
			'preload'            => $a['preload'],
			'width'              => empty( $a['width'] ) ? false : $a['width'],
			'height'             => empty( $a['height'] ) ? false : $a['height'],
			'poster'             => empty( $a['img_src'] ) ? false : $a['img_src'],
			'src'                => empty( $a['video_src'] ) ? false : $a['video_src'],
			// ARVE only
			'class'              => 'arve-video fitvidsignore',
			'muted'              => $a['muted'],
			'playsinline'        => $a['playsinline'],
			'webkit-playsinline' => $a['playsinline'],
		);

	} else {

		$properties = get_host_properties();
		$options    = options();
		$iframe_src = build_iframe_src( $a );
		$iframe_src = add_query_args_to_iframe_src( $iframe_src, $a );
		$iframe_src = add_autoplay_query_arg( $iframe_src, $a );

		if ( 'vimeo' === $a['provider'] && ! empty( $a['start'] ) ) {
			$iframe_src .= '#t=' . (int) $a['start'];
		}

		$a['iframe_attr'] = [
			'allow'           => 'autoplay; encrypted-media; fullscreen',
			'allowfullscreen' => '',
			'class'           => 'arve-iframe fitvidsignore',
			'frameborder'     => '0',
			'name'            => $a['iframe_name'],
			'sandbox'         => 'allow-scripts allow-same-origin allow-presentation allow-popups',
			'scrolling'       => 'no',
			'src'             => $iframe_src,
			'width'           => empty( $a['width'] ) ? false : $a['width'],
			'height'          => empty( $a['height'] ) ? false : $a['height'],
		];

		if ( 'vimeo' === $a['provider'] ) {
			$a['iframe_attr']['sandbox'] .= ' allow-forms';
		}

		$properties['iframe']['requires_flash'] = $options['iframe_flash'];

		if ( null === $a['disable_flash'] && $properties[ $a['provider'] ]['requires_flash'] ) {
			$a['iframe_attr']['sandbox'] = false;
		}
	}//end if

	return $a;
}

function sc_filter_validate( array $a ) {

	$a['align'] = validate_align( $a['align'], $a['provider'] );

	$a['mode'] = validate_mode( $a['mode'], $a['provider'] );

	$a['autoplay']      = validate_bool( $a['autoplay'], 'autoplay' );
	$a['arve_link']     = validate_bool( $a['arve_link'], 'arve_link' );
	$a['loop']          = validate_bool( $a['loop'], 'loop' );
	$a['controls']      = validate_bool( $a['controls'], 'controls' );
	$a['disable_flash'] = validate_bool( $a['disable_flash'], 'disable_flash' );
	$a['muted']         = validate_bool( $a['muted'], 'muted' );
	$a['playsinline']   = validate_bool( $a['playsinline'], 'playsinline' );
	$a['maxwidth']      = (int) $a['maxwidth'];
	$a['maxwidth']      = (int) maxwidth_when_aligned( $a['maxwidth'], $a['align'] );
	$a['id']            = id_fixes( $a['id'], $a['provider'] );
	$a['aspect_ratio']  = get_default_aspect_ratio( $a['aspect_ratio'], $a );
	$a['aspect_ratio']  = aspect_ratio_fixes( $a['aspect_ratio'], $a['provider'], $a['mode'] );
	$a['aspect_ratio']  = validate_aspect_ratio( $a['aspect_ratio'] );

	return $a;
}

function sc_filter_set_fixed_dimensions( array $a ) {

	if ( ! empty( $a['oembed_data']->width ) ) {
		$width = $a['oembed_data']->width;
	} else {
		$width = 640;
	}

	$a['width']  = $width;
	$a['height'] = calculate_height( $width, $a['aspect_ratio'] );

	return $a;
}

function sc_filter_autoplay_off_after_ran_once( array $a ) {

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

function sc_filter_sanitise( array $a ) {

	if ( ! empty( $a['src'] ) ) {
		$a['url'] = $a['src'];
	}

	foreach ( $a as $key => $value ) {

		if ( 'oembed_data' === $key || 'parameters' === $key || null === $value ) {
			continue;
		}

		if ( '' === $value ) {
			$a[ $key ] = null;
		}
	}

	return $a;
}

function sc_filter_missing_attribute_check( array $a ) {

	// Old shortcodes
	if ( ! array_key_exists( 'url', $a ) ) {
		return $a;
	}

	$required_attributes   = get_html5_attributes();
	$required_attributes[] = 'url';

	$array = array_intersect_key( $a, array_flip( $required_attributes ) );

	if ( count( array_filter( $array ) ) !== count( $array ) ) {

		$a['missing_atts_error'] = error( sprintf(
			// Translators: Attributes.
			esc_html__( 'The [arve] shortcode needs one of this attributes %s', 'advanced-responsive-video-embedder' ),
			implode( $required_attributes ) )
		);
	}

	return $a;
}

function sc_filter_get_media_gallery_thumbnail( array $a ) {

	if ( empty( $a['thumbnail'] ) ) {
		return $a;
	}

	if ( is_numeric( $a['thumbnail'] ) ) {

		$attchment_id    = $a['thumbnail'];
		$a['img_src']    = get_attachment_image_url_or_srcset( 'url', $attchment_id );
		$a['img_srcset'] = get_attachment_image_url_or_srcset( 'srcset', $attchment_id );

	} elseif ( arve_validate_url( $a['thumbnail'] ) ) {

		$a['img_src']    = $a['thumbnail'];
		$a['img_srcset'] = false;

	} else {

		$a['img_src'] = new WP_Error( 'thumbnail', __( 'Not a valid thumbnail URL or Media ID given', 'advanced-responsive-video-embedder' ) );
	}

	return $a;
}

function sc_filter_get_media_gallery_video( array $a ) {

	$html5_ext = get_html5_attributes();

	foreach ( $html5_ext as $ext ) {

		if ( ! empty( $a[ $ext ] ) && is_numeric( $a[ $ext ] ) ) {
			$a[ $ext ] = wp_get_attachment_url( $a[ $ext ] );
		}
	}

	return $a;
}

function sc_filter_detect_provider_and_id_from_url( array $a ) {

	$properties = get_host_properties();

	if ( ! empty( $a['provider'] ) || empty( $a['url'] ) ) {
		return $a;
	}

	foreach ( $properties as $host_id => $host ) :

		if ( empty( $host['regex'] ) ) {
			continue;
		}

		$preg_match = preg_match( $host['regex'], $a['url'], $matches );

		if ( 1 !== $preg_match ) {
			continue;
		}

		foreach ( $matches as $key => $value ) {

			if ( is_string( $key ) ) {
				$a['provider'] = $host_id;
				$a[ $key ]     = $matches[ $key ];
			}
		}
	endforeach;

	return $a;
}

function sc_filter_detect_query_args( array $a ) {

	if ( empty( $a['url'] ) ) {
		return $a;
	}

	$to_extract = array(
		'brightcove' => array( 'videoId', 'something' ),
	);

	foreach ( $to_extract as $provider => $parameters ) {

		if ( $provider !== $a['provider'] ) {
			return $a;
		}

		$query_array = url_query_array( $a['url'] );

		foreach ( $parameters as $key => $parameter ) {

			$att_name = $a['provider'] . "_$parameter";

			if ( empty( $query_array[ $parameter ] ) ) {
				$a[ $att_name ] = new WP_Error( $att_name, "$parameter not found in URL" );
			} else {
				$a[ $att_name ] = $query_array[ $parameter ];
			}
		}
	}

	return $a;
}

function sc_filter_detect_youtube_playlist( array $a ) {

	if ( 'youtube' !== $a['provider']
		|| ( empty( $a['url'] ) && empty( $a['id'] ) )
	) {
		return $a;
	}

	if ( empty( $a['url'] ) ) {
		// Not a url but it will work
		$url = str_replace( array( '&list=', '&amp;list=' ), '?list=', $a['id'] );
	} else {
		$url = $a['url'];
	}

	$query_array = url_query_array( $url );

	if ( empty( $query_array['list'] ) ) {
		return $a;
	}

	$a['id'] = strtok( $a['id'], '?' );
	$a['id'] = strtok( $a['id'], '&' );

	$a['youtube_playlist_id'] = $query_array['list'];
	$a['parameters']         .= 'list=' . $query_array['list'];

	return $a;
}

function get_video_type( $ext ) {

	switch ( $ext ) {
		case 'ogv':
		case 'ogm':
			return 'video/ogg';
		default:
			return 'video/' . $ext;
	}
}

function sc_filter_detect_html5( array $a ) {

	if ( ! empty( $a['provider'] ) && 'html5' !== $a['provider'] ) {
		return $a;
	}

	$html5_extensions        = get_html5_attributes();
	$a['video_sources_html'] = '';

	foreach ( $html5_extensions as $ext ) :

		if ( ! empty( $a[ $ext ] ) ) {

			if ( \Nextgenthemes\Utils\starts_with( $a[ $ext ], 'https://www.dropbox.com' ) ) {
				$a[ $ext ] = add_query_arg( 'dl', 1, $a[ $ext ] );
			}

			$a['video_sources_html'] .= sprintf( '<source type="%s" src="%s">', get_video_type( $ext ), $a[ $ext ] );
		}

		if ( ! empty( $a['url'] ) && arve_ends_with( $a['url'], ".$ext" ) ) {

			if ( \Nextgenthemes\Utils\starts_with( $a['url'], 'https://www.dropbox.com' ) ) {
				$a['url'] = add_query_arg( 'dl', 1, $a['url'] );
			}

			$a['video_src'] = $a['url'];

			/*
			$parse_url = parse_url( $a['url'] );
			$pathinfo  = pathinfo( $parse_url['path'] );

			$url_ext         = $pathinfo['extension'];
			$url_without_ext = $parse_url['scheme'] . '://' . $parse_url['host'] . $path_without_ext;
			*/
		}
	endforeach;

	if ( empty( $a['video_src'] ) && empty( $a['video_sources_html'] ) ) {
		return $a;
	}

	$a['provider'] = 'html5';

	return $a;
}

function sc_filter_iframe_fallback( array $a ) {

	if ( empty( $a['provider'] ) ) {

		$a['provider'] = 'iframe';

		if ( empty( $a['id'] ) && ! empty( $a['url'] ) ) {
			$a['id'] = $a['url'];
		}
	}

	return $a;
}

function sc_filter_build_tracks_html( array $a ) {

	if ( 'html5' !== $a['provider'] ) {
		return $a;
	}

	$a['video_tracks_html'] = '';

	for ( $n = 1; $n <= ARVE_NUM_TRACKS; $n++ ) {

		if ( empty( $a[ "track_{$n}" ] ) ) {
			return $a;
		}

		preg_match( '#-(?<type>captions|chapters|descriptions|metadata|subtitles)-(?<lang>[a-z]{2}).vtt$#i', $a[ "track_{$n}" ], $matches );

		if ( empty( $matches[1] ) ) {
			$a[ "track_{$n}" ] = new WP_Error( 'track', __( 'Track kind or language code could not detected from filename', 'advanced-responsive-video-embedder' ) );
			return $a;
		}

		$label = empty( $a[ "track_{$n}_label" ] ) ? get_language_name_from_code( $matches['lang'] ) : $a[ "track_{$n}_label" ];

		$attr = array(
			'default' => ( 1 === $n ) ? true : false,
			'kind'    => $matches['type'],
			'label'   => $label,
			'src'     => $a[ "track_{$n}" ],
			'srclang' => $matches['lang'],
		);

		$a['video_tracks_html'] .= sprintf( '<track%s>', \Nextgenthemes\Utils\attr( $attr ) );
	}//end for

	return $a;
}
