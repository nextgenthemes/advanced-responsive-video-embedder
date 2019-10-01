<?php

function arve_is_bool_option( $array ) {

	$yes_no = array(
		''    => 1,
		'yes' => 1,
		'no'  => 1
	);

	$check = array_diff_key( $array, $yes_no );

	if ( empty( $check ) ) {
		return 'bool';
	} else {
		return $array;
	}
}

function arve_get_pre_style() {
	return '';
}

function arve_load_plugin_textdomain() {

	load_plugin_textdomain(
		ARVE_SLUG,
		false,
		dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
	);
}

function arve_get_first_array_value( $array ) {
	reset( $array );
	$key = key( $array );
	return $array[ $key ];
}

function arve_prefix_array_keys( $keyprefix, $array ) {

	foreach ( $array as $key => $value ) {
		$array[ $keyprefix . $key ] = $value;
		unset( $array[ $key ] );
	}

	return $array;
}

function arve_check_filetype( $url, $ext ) {

	$check = wp_check_filetype( $url, wp_get_mime_types() );

	if ( strtolower( $check['ext'] ) === $ext ) {
		return $check['type'];
	} else {
		return false;
	}
}

/**
 * Calculates seconds based on youtube times
 *
 * @param     string $yttime   The '1h25m13s' part of youtube URLs
 *
 * @return    int   Starttime in seconds
 */
function arve_youtube_time_to_seconds( $yttime ) {

	$format  = false;
	$hours   = 0;
	$minutes = 0;
	$seconds = 0;

	$pattern['hms'] = '/([0-9]+)h([0-9]+)m([0-9]+)s/'; // hours, minutes, seconds
	$pattern['ms']  = '/([0-9]+)m([0-9]+)s/'; // minutes, seconds
	$pattern['h']   = '/([0-9]+)h/';
	$pattern['m']   = '/([0-9]+)m/';
	$pattern['s']   = '/([0-9]+)s/';

	foreach ( $pattern as $key => $value ) {

		preg_match( $value, $yttime, $result );

		if ( ! empty( $result ) ) {
			$format = $key;
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
function arve_aspect_ratio_to_percentage( $aspect_ratio ) {

	if ( is_wp_error( $aspect_ratio ) ) {
		return 52.25;
	}

	$a = explode( ':', $aspect_ratio );

	return ( ( $a[1] / $a[0] ) * 100 );
}

/**
 * Calculates
 *
 * @since     8.2.0
 */
function arve_calculate_height( $width, $aspect_ratio ) {

	$width        = (int) $width;
	$aspect_ratio = empty( $aspect_ratio ) ? '16:9' : $aspect_ratio;
	$percent      = arve_aspect_ratio_to_percentage( $aspect_ratio );

	if ( $width > 100 && $percent ) {
		return ( ( $width / 100 ) * $percent );
	}

	return false;
}
