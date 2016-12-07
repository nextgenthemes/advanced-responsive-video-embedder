<?php

function arve_filter_atts_set_fixed_dimensions( $atts ) {

  $width = 480;

	if( empty( $atts['aspect_ratio'] ) ) {
		$ratio = 56.25;
	} else {
		$ratio = (float) arve_aspect_ratio_to_percentage( $atts['aspect_ratio'] );
	}

	$atts['width']  = $width;
	$atts['height'] = ( $width / 100 ) * $ratio;

	return $atts;
}

function arve_filter_atts_sanitise( $atts ) {

  if ( ! empty( $atts['src'] ) ) {
    $atts['url'] = $atts['src'];
  }

  foreach ( $atts as $key => $value ) {

    if ( null === $value ) {
      continue;
    }

    if( ! is_string( $value ) ) {
      $atts[ $key ] = arve_error( sprintf( __( '<code>%s</code> is not a string. Only Strings should be passed to the shortcode function' , ARVE_SLUG ), $key ) );
    }
  }

  return $atts;
}

function arve_filter_atts_missing_attribute_check( $atts ) {

  # Old shortcodes
  if ( ! array_key_exists( 'url' , $atts ) ) {
    return $atts;
  }

  $required_attributes   = arve_get_html5_attributes();
  $required_attributes[] = 'url';

  $array = array_intersect_key( $atts, array_flip( $required_attributes ) );

  if( count( array_filter( $array ) ) != count( $array ) ) {
    $atts['missing_atts_error'] = arve_error( sprintf(
      esc_html__( 'The [arve] shortcode needs one of this attributes %s', ARVE_SLUG ),
      implode( $required_attributes ) )
    );
  }

  return $atts;
}

function arve_filter_atts_validate( $atts ) {

  if ( ! empty( $atts['url'] ) && ! arve_validate_url( $atts['url'] ) ) {
    $atts['url'] = new WP_Error( 'thumbnail', sprintf( __( '<code>%s</code> is not a valid url', ARVE_SLUG ), esc_html( $atts['url'] ) ) );
  }

  $atts['align']         = arve_validate_align( $atts['align'], $atts['provider'] );
  $atts['mode']          = arve_validate_mode( $atts['mode'],   $atts['provider'] );
  $atts['autoplay']      = arve_validate_bool( $atts['autoplay'],  'autoplay' );
  $atts['arve_link']     = arve_validate_bool( $atts['arve_link'], 'arve_link' );
  $atts['loop']          = arve_validate_bool( $atts['loop'],      'loop' );
  $atts['controls']      = arve_validate_bool( $atts['controls'],  'controls' );
  $atts['disable_flash'] = arve_validate_bool( $atts['disable_flash'], 'disable_flash' );

  $atts['maxwidth']     = (int) $atts['maxwidth'];
  $atts['maxwidth']     = (int) arve_maxwidth_when_aligned( $atts['maxwidth'], $atts['align'] );
  $atts['id']           = arve_id_fixes( $atts['id'], $atts['provider'] );
  $atts['aspect_ratio'] = arve_get_default_aspect_ratio( $atts['aspect_ratio'], $atts['provider'], $atts['mode'] );
  $atts['aspect_ratio'] = arve_aspect_ratio_fixes(       $atts['aspect_ratio'], $atts['provider'], $atts['mode'] );

  return $atts;
}

function arve_filter_atts_generate_embed_id( $atts ) {

	foreach ( array( 'id', 'mp4', 'm4v', 'webm', 'ogv', 'url', 'webtorrent' ) as $att ) {

		if ( ! empty( $atts[ $att ] ) && is_string( $atts[ $att ] ) ) {

			$atts['embed_id'] = preg_replace( '/[^-a-zA-Z0-9]+/', '', $atts[ $att ] );
			$atts['embed_id'] = str_replace(
				array( 'https', 'http', 'wp-contentuploads' ),
				'',
				$atts['embed_id']
			);
			break;
		}
	}

	if ( empty( $atts['embed_id'] ) ) {
		$atts['embed_id'] = new WP_Error( 'embed_id', __( 'Element ID could not be build, please report this bug.', ARVE_SLUG ) );
    return $atts;
	} else {
    $atts['embed_id'] = 'video-' . $atts['embed_id'];
  }

	return $atts;
}

function arve_filter_atts_get_media_gallery_thumbnail( $atts ) {

  if ( empty( $atts['thumbnail'] ) ) {
    return $atts;
  }

  if( is_numeric( $atts['thumbnail'] ) ) {

    $attchment_id = $atts['thumbnail'];

    $atts['img_src']    = arve_get_attachment_image_url_or_srcset( 'url',    $attchment_id );
    $atts['img_srcset'] = arve_get_attachment_image_url_or_srcset( 'srcset', $attchment_id );

  } elseif ( arve_validate_url( $atts['thumbnail'] ) ) {

    $atts['img_src']    = $atts['thumbnail'];
    $atts['img_srcset'] = false;

  } else {

    $atts['img_src'] = new WP_Error( 'thumbnail', __( 'Not a valid thumbnail URL or Media ID given', ARVE_SLUG ) );
  }

  return $atts;
}

function arve_filter_atts_detect_provider_and_id_from_url( $atts ) {

	$properties = arve_get_host_properties();

	if ( ! empty( $atts['provider'] ) || empty( $atts['url'] ) ) {
		return $atts;
	}

	foreach ( $properties as $provider => $values ) :

		if ( empty( $values['regex'] ) ) {
			continue;
		}

		preg_match( '#' . $values['regex'] . '#i', $atts['url'], $matches );

		if ( ! empty( $matches[1] ) ) {

			$atts['id']       = $matches[1];
			$atts['provider'] = $provider;

      if ( ! empty( $matches['id'] ) && ! empty( $matches['account_id'] ) ) {
        $atts['id']         = $matches['id'];
        $atts['account_id'] = $matches['account_id'];
      }

			return $atts;
		}

	endforeach;

	return $atts;
}

function arve_filter_atts_detect_query_args( $atts ) {

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

function arve_filter_atts_detect_youtube_playlist( $atts ) {

  if(
    'youtube' != $atts['provider'] ||
    ( empty( $atts['url'] ) && empty( $atts['id'] ) )
  ) {
    return $atts;
  }

  if( empty( $atts['url'] ) ) {
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

function arve_filter_atts_detect_html5( $atts ) {

  if( ! empty( $atts['provider'] ) && 'html5' != $atts['provider'] ) {
    return $atts;
	}

	$html5_extensions = arve_get_html5_attributes();

	foreach ( $html5_extensions as $ext ) :

		if ( ! empty( $atts[ $ext ] ) && $type = arve_check_filetype( $atts[ $ext ], $ext ) ) {
			$atts['video_sources'][ $type ] = $atts[ $ext ];
		}

		if ( ! empty( $atts['url'] ) && arve_ends_with( $atts['url'], ".$ext" ) ) {
			$atts['video_src'] = $atts['url'];
			/*
			$parse_url = parse_url( $atts['url'] );
			$pathinfo  = pathinfo( $parse_url['path'] );

			$url_ext         = $pathinfo['extension'];
			$url_without_ext = $parse_url['scheme'] . '://' . $parse_url['host'] . $path_without_ext;
			*/
		}

	endforeach;

	if( empty( $atts['video_src'] ) && empty( $atts['video_sources'] ) ) {
    return $atts;
	}

  $atts['provider'] = 'html5';
  $atts['video_sources_html'] = '';

  if ( isset( $atts['video_sources'] ) ) {

		foreach ( $atts['video_sources'] as $key => $value ) {
			$atts['video_sources_html'] .= sprintf( '<source type="%s" src="%s">', $key, $value );
		}
	}

	return $atts;
}

function arve_filter_atts_iframe_fallback( $atts ) {

  if ( empty( $atts['provider'] ) ) {

    $atts['provider'] = 'iframe';

    if ( empty( $atts['id'] ) && ! empty( $atts['url'] ) ) {
      $atts['id'] = $atts['url'];
    }
  }

  return $atts;
}

function arve_filter_atts_build_iframe_src( $atts ) {

  if ( in_array( $atts['provider'], array( 'html5', 'webtorrent' ) ) ) {
    return $atts;
  }

  $atts['iframe_src'] = arve_build_iframe_src( $atts );
  $atts['iframe_src'] = arve_add_query_args_to_iframe_src( $atts );
  $atts['iframe_src'] = arve_add_autoplay_query_arg( $atts );

  if ( 'vimeo' == $atts['provider'] && ! empty( $atts['start'] ) ) {
    $atts['iframe_src'] .= '#t=' . (int) $atts['start'];
    $atts['iframe_src'] .= '#t=' . (int) $atts['start'];
  }

  return $atts;
}

function arve_filter_atts_build_subtitles( $atts ) {

	if ( 'html5' != $atts['provider'] ) {
		return $atts;
	}

	$atts[ "video_tracks" ] = '';

	for ( $n = 1; $n <= ARVE_NUM_TRACKS; $n++ ) {

		if ( empty( $atts[ "track_{$n}" ] ) ) {
			return $atts;
		}

		preg_match( '#-(captions|chapters|descriptions|metadata|subtitles)-([a-z]{2}).vtt$#i', $atts[ "track_{$n}" ], $matches );

		if ( empty( $matches[1] ) ) {
			$atts[ "track_{$n}" ] = new WP_Error( 'track', __( 'Track kind or language code could not detected from filename', ARVE_SLUG ) );
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

		$atts[ "video_tracks" ] .= sprintf( '<track%s>', arve_attr( $attr) );
	}

	return $atts;
}
