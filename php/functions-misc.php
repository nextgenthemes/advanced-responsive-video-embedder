<?php
namespace Nextgenthemes\ARVE;

function gcd( $a, $b ) {
	return $b ? gcd( $b, $a % $b ) : $a;
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

	$matches['h'] = 0;
	$matches['m'] = 0;
	$matches['s'] = 0;

	$pattern = '/' .
		'(?<h>[0-9]+h)?' .
		'(?<m>[0-9]+m)?' .
		'(?<s>[0-9]+s)?/';

	preg_match( $pattern, $yttime, $matches );

	return ( (int) $matches['h'] * 60 * 60 ) +
		( (int) $matches['m'] * 60 ) +
		(int) $matches['s'];
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

function disabled_on_feeds() {
	return is_feed() && ! options()['feed'] ? true : false;
}
