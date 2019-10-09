<?php
namespace Nextgenthemes\ARVE;

function gcd( $a, $b ) {
	return $b ? gcd( $b, $a % $b ) : $a;
}

function is_bool_option( $array ) {

	$yes_no = [
		''    => 1,
		'yes' => 1,
		'no'  => 1,
	];

	$check = array_diff_key( $array, $yes_no );

	if ( empty( $check ) ) {
		return 'bool';
	} else {
		return $array;
	}
}

function get_pre_style() {
	return '';
}

function load_textdomain() {

	\load_plugin_textdomain(
		'advanced-responsive-video-embedder',
		false,
		dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
	);
}

function get_first_array_value( array $array ) {
	reset( $array );
	$key = key( $array );
	return $array[ $key ];
}

function prefix_array_keys( $keyprefix, array $array ) {

	foreach ( $array as $key => $value ) {
		$array[ $keyprefix . $key ] = $value;
		unset( $array[ $key ] );
	}

	return $array;
}

function check_filetype( $url, $ext ) {

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
 * @param string $yttime   The '1h25m13s' part of youtube URLs.
 *
 * @return int Starttime in seconds.
 */
function youtube_time_to_seconds( $yttime ) {

	$format  = false;
	$hours   = 0;
	$minutes = 0;
	$seconds = 0;

	$pattern['hms'] = '/([0-9]+)h([0-9]+)m([0-9]+)s/'; // hours, minutes, seconds
	$pattern['ms']  = '/([0-9]+)m([0-9]+)s/';          // minutes, seconds
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
	}//end switch

	return ( $hours * 60 * 60 ) + ( $minutes * 60 ) + $seconds;
}

/**
 * Calculates padding percentage value for a particular aspect ratio
 *
 * @param string $aspect_ratio '4:3' or percentage value with percent sign.
 *
 * @since 4.2.0
 *
 * @return float
 */
function aspect_ratio_to_percentage( $aspect_ratio ) {

	$a          = explode( ':', $aspect_ratio );
	$percentage = ( $a[1] / $a[0] ) * 100;

	return $percentage;
}

function calculate_height( $width, $aspect_ratio ) {

	$width        = (int) $width;
	$aspect_ratio = empty( $aspect_ratio ) ? '16:9' : $aspect_ratio;
	$percent      = aspect_ratio_to_percentage( $aspect_ratio );

	if ( $width > 100 && $percent ) {
		return ( ( $width / 100 ) * $percent );
	}

	return false;
}
