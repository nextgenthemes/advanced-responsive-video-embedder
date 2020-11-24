<?php
namespace Nextgenthemes\ARVE;

function aspect_ratio_gcd( $aspect_ratio ) {

	list( $width, $height ) = explode( ':', $aspect_ratio );
	$gcd = gcd( $width, $height );

	$aspect_ratio = $width / $gcd . ':' . $height / $gcd;

	return $aspect_ratio;
}

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

function new_height( $old_width, $old_height, $new_width ) {

	$aspect_num   = $old_width / $old_height;
	$new_height   = $new_width / $aspect_num;

	return $new_height;
}

function new_height_from_aspect_ratio( $new_width, $aspect_ratio ) {

	list( $old_width, $old_height ) = explode( ':', $aspect_ratio );

	return new_height( $old_width, $old_height, $new_width );
}

/**
 * Calculates padding percentage value for a particular aspect ratio
 *
 * @param string $aspect_ratio example '4:3'
 *
 * @since 4.2.0
 *
 * @return float
 */
function aspect_ratio_to_percentage( $aspect_ratio ) {

	list( $width, $height ) = explode( ':', $aspect_ratio );
	$percentage             = ( $height / $width ) * 100;

	return $percentage;
}

function disabled_on_feeds() {
	return is_feed() && ! options()['feed'] ? true : false;
}

function seconds_to_iso8601_duration( $time ) {
    $units = array(
        'Y' => 365*24*3600,
        'D' =>     24*3600,
        'H' =>        3600,
        'M' =>          60,
        'S' =>           1,
    );

    $str = 'P';
    $istime = false;

    foreach ( $units as $unitName => &$unit ) {
        $quot  = intval($time / $unit);
        $time -= $quot * $unit;
        $unit  = $quot;
        if ( $unit > 0 ) {
            if ( ! $istime && in_array($unitName, array('H', 'M', 'S'))) { // There may be a better way to do this
                $str .= 'T';
                $istime = true;
            }
            $str .= strval($unit) . $unitName;
        }
    }

    return $str;
}
