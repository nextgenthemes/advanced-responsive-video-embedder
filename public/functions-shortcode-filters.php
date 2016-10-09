<?php

function arv3_filter_atts_sanitise( $atts ) {

  if ( ! empty( $atts['src'] ) ) {
    $atts['url'] = $atts['src'];
  }

  foreach ( $atts as $key => $value ) {

    if ( null === $value ) {
      continue;
    }

    if( is_string( $value ) ) {
      $atts[ $key ] = sanitize_text_field( $value );
    } else {
      $atts[ $key ] = arv3_error( sprintf( __( '<code>%s</code> is not a string. Only Strings should be passed to the shortcode function' , ARVE_SLUG ), $key ) );
    }
  }

  return $atts;
}

function arv3_filter_atts_validate( $atts ) {

}

function arv3_filter_atts_get_media_gallery_thumbnail( $atts ) {

  if ( empty( $atts['thumbnail'] ) ) {
    return $atts;
  }

  if( is_numeric( $atts['thumbnail'] ) ) {

    $atts['thumbnail'] = arv3_get_attachment_image_url_or_srcset( 'url',    $atts['thumbnail'] );
    $atts['srcset']    = arv3_get_attachment_image_url_or_srcset( 'srcset', $atts['thumbnail'] );

  } elseif ( arv3_validate_url( $atts['thumbnail'] ) ) {

    $atts['thumbnail_from_url'] = true;

  } else {

    $atts['thumbnail'] = new WP_Error( 'thumbnail', __( 'Not a valid thumbnail URL or Media ID given', ARVE_SLUG ) );
  }

  return $atts;
}

function arv3_filter_atts_detect_provider_and_id_from_url( $atts ) {

	$properties = arv3_get_host_properties();

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

			return $atts;
		}

	endforeach;

	return $atts;
}

function arv3_filter_atts_detect_html5( $atts ) {

  if( ! empty( $atts['provider'] ) ) {
    return $atts;
	}

	$html5_extensions = array( 'm4v', 'mp4', 'ogv',	'webm' );

	foreach ( $html5_extensions as $ext ) :

		if ( ! empty( $atts[ $ext ] ) && $type = arv3_check_filetype( $atts[ $ext ], $ext ) ) {
			$atts['video_sources'][ $type ] = $atts[ $ext ];
		}

		if ( ! empty( $atts['url'] ) && arv3_ends_with( $atts['url'], ".$ext" ) ) {
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
	return $atts;
}
