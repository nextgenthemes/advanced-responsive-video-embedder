<?php

function arv3_load_plugin_textdomain() {

  load_plugin_textdomain(
    ARVE_SLUG,
    false,
    dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
  );
}

function arv3_filter_atts_sanitise( $atts ) {

  foreach ( $atts as $key => $value ) {
    $atts[ $key ] = sanitize_text_field( (string) $value );
  }

  return $atts;
}

function arv3_get_first_array_value( $array ) {
  reset( $array );
  $key = key( $array );
  return $array[ $key ];
}

function arv3_prefix_array_keys( $keyprefix, $array ) {

  foreach( $array as $k => $v ) {
      $array[ $keyprefix . $k ] = $v;
      unset( $array[ $k ] );
  }

  return $array;
}

/**
 * Calculates seconds based on youtube times
 *
 * @param     string $yttime   The '1h25m13s' part of youtube URLs
 *
 * @return    int   Starttime in seconds
 */
function arv3_youtube_time_to_seconds( $yttime ) {

  $format = false;
  $hours  = $minutes = $seconds = 0;

  $pattern['hms'] = '/([0-9]+)h([0-9]+)m([0-9]+)s/'; // hours, minutes, seconds
  $pattern['ms']  =          '/([0-9]+)m([0-9]+)s/'; // minutes, seconds
  $pattern['h']   = '/([0-9]+)h/';
  $pattern['m']   = '/([0-9]+)m/';
  $pattern['s']   = '/([0-9]+)s/';

  foreach ( $pattern as $k => $v ) {

    preg_match( $v, $yttime, $result );

    if ( ! empty( $result ) ) {
      $format = $k;
      break;
    }
  }

  switch ( $format ) {
    case 'hms':
      $hours   = $result[1];
      $minutes = $result[2];
      $seconds = $result[3];
      break;
    case 'ms':
      $minutes = $result[1];
      $seconds = $result[2];
      break;
    case 'h':
      $hours = $result[1];
      break;
    case 'm':
      $minutes = $result[1];
      break;
    case 's':
      $seconds = $result[1];
      break;
    default:
      return false;
  }

  return ( $hours * 60 * 60 ) + ( $minutes * 60 ) + $seconds;
}

/**
 * Calculates padding percentage value for a particular aspect ratio
 *
 * @since     4.2.0
 *
 * @param     string $aspect_ratio '4:3' or percentage value with percent sign
 *
 * @return    float
 */
function arv3_aspect_ratio_to_padding( $aspect_ratio ) {

  $aspect_ratio = explode( ':', $aspect_ratio );

  if ( is_numeric( $aspect_ratio[0] ) && is_numeric( $aspect_ratio[1] ) ) {
    return ( ( $aspect_ratio[1] / $aspect_ratio[0] ) * 100 );
  } else {
    return false;
  }
}
