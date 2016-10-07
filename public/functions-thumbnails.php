<?php

function arv3_filter_atts_get_media_gallery_thumbnail( $atts ) {

  foreach ( $atts as $key => $value ) {
    $atts[ $key ] = sanitize_text_field( (string) $value );
  }

  if ( $detected_thumbnail = arv3_get_media_library_thumbnail( $atts['thumbnail'] ) ) {
    $atts['thumbnail_from_url'] = $detected_thumbnail['thumbnail_from_url'];
    $atts['srcset']             = $detected_thumbnail['srcset'];
    $atts['thumbnail']          = $detected_thumbnail['thumbnail'];
  }

  return $atts;
}

function arv3_get_media_library_thumbnail( $thumbnail ) {

  if ( empty( $thumbnail ) ) {
    return false;
  }

  $thumbnail_from_url = $srcset = false;

  if( is_numeric( $thumbnail ) ) {

    $thumbnail = arv3_get_attachment_image_url_or_srcset( 'url',    $thumbnail );
    $srcset    = arv3_get_attachment_image_url_or_srcset( 'srcset', $thumbnail );

  } elseif ( arv3_validate_url( $thumbnail ) ) {

    $thumbnail_from_url = true;

  } else {

    $thumbnail = new WP_Error( 'thumbnail', __( 'Not a valid thumbnail URL or Media ID given', ARVE_SLUG ) );
  }

  return compact( 'thumbnail', 'srcset', 'thumbnail_from_url' );
}

function arv3_get_attachment_image_url_or_srcset( $url_or_srcset, $thumbnail ) {

  if( $found = arv3_get_cached_attachment_image_url_or_srcset( $url_or_srcset, $thumbnail ) ) {

    return $found;

  } elseif ( 'url' == $url_or_srcset ) {

    return new WP_Error( 'wp thumbnail', __( 'No attachment with that ID', ARVE_SLUG ) );

  } else {

    return false;
  }
}

function arv3_get_cached_attachment_image_url_or_srcset( $url_or_srcset, $attachment_id ) {

  $options        = arv3_get_options();
  $transient_name = "arve_attachment_image_{$url_or_srcset}_{$attachment_id}";
  $transient      = get_transient( $transient_name );
  $time           = (int) $options['wp_image_cache_time'];

  if( false === $transient || $time <= 0  ) {

    if ( 'srcset' == $url_or_srcset ) {

      $out = wp_get_attachment_image_srcset( $attachment_id, 'small' );

    } elseif( 'url' == $url_or_srcset ) {

      $out = wp_get_attachment_image_url( $attachment_id, 'small' );
    }

    set_transient( $transient_name, (string) $out, $time );

  } else {

    $out = $transient;
  }

  return $out;
}
