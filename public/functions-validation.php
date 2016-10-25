<?php

function arve_validate_url( $url ) {

  if ( arve_starts_with( $url, '//' ) ) {
    $url = 'https:' . $url;
  }

  if ( arve_starts_with( $url, 'http' ) && filter_var( $url, FILTER_VALIDATE_URL ) !== false ) {
    return true;
  }

  return false;
}

function arve_bool_to_shortcode_string( $val ) {

  if ( false === $val ) {
    return 'n';
  }

  return (string) $val;
}


function arve_validate_bool( $val, $name ) {

  switch ( $val ) {
    case 'true':
    case '1':
    case 'y':
    case 'yes':
    case 'on':
      $val = true;
      break;
    case null;
    case 'false':
    case '0':
    case 'n':
    case 'no':
    case 'off':
      $val = false;
      break;
    default:
      $val = new WP_Error( $name,
        sprintf( __( '%s <code>%s</code> not valid', 'advanced-responsive-video-embedder' ), $name, $val )
      );
      break;
  }

  return $val;
}

function arve_validate_align( $align ) {

  switch ( $align ) {
    case null:
    case '':
    case 'none':
      $align = null;
      break;
    case 'left':
    case 'right':
    case 'center':
      break;
    default:
      $align = new WP_Error( 'align', sprintf( __( 'Align <code>%s</code> not valid', ARVE_SLUG ), esc_html( $align ) ) );
      break;
  }

  return $align;
}

function arve_validate_mode( $mode, $provider ) {

  if ( 'thumbnail' == $mode ) {
    $mode = 'lazyload-lightbox';
  }

  if ( 'veoh' == $mode ) {
    $mode = 'normal';
  }

  $supported_modes = arve_get_supported_modes();

  if ( ! array_key_exists( $mode, $supported_modes ) ) {

    $mode = new WP_Error( 'mode', sprintf(
      __( 'Mode: <code>%s</code> is invalid or not supported. Note that you will need the Pro Addon activated for modes other than normal.', ARVE_SLUG ),
      esc_html( $mode )
    ) );
  }

  return $mode;
}
