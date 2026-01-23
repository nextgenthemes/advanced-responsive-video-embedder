<?php

declare(strict_types = 1);

namespace Nextgenthemes\ARVE;

use WP_Error;

function arve_errors(): WP_Error {

	static $instance = null;

	if ( null === $instance ) {
		$instance = new WP_Error();
	}

	return $instance;
}

/** @param mixed $data */
function is_wp_error_array( $data ): bool {
	return is_array( $data ) &&
		isset( $data['code'] ) &&
		isset( $data['message'] );
}

/**
 * @deprecated Use PROVIDERS constant directly, kept for addon compatibility
 * @phpstan-ignore-next-line
 */
function get_host_properties(): array {
	return PROVIDERS;
}

/**
 * @param array <string, mixed> $a
 */
function is_card( array $a ): bool {

	$is_ll_mode = in_array( $a['mode'], [ 'lazyload', 'lightbox' ], true );

	return ( $is_ll_mode && 'card' === $a['lazyload_style'] );
}

/**
 * Calculate the greatest common divisor of the given aspect ratio and return the simplified aspect ratio.
 * When the aspect ratio in invalid contains floating point value, the original aspect ratio will be returned.
 *
 * @param string $aspect_ratio The input aspect ratio in the format 'width:height'
 * @return string              The simplified aspect ratio in the format 'newWidth:newHeight'
 */
function aspect_ratio_gcd( string $aspect_ratio ): string {

	list( $width, $height ) = explode( ':', $aspect_ratio );

	if ( ctype_digit( $width ) && ctype_digit( $height ) ) {

		$gcd          = gcd( (int) $width, (int) $height );
		$aspect_ratio = $width / $gcd . ':' . $height / $gcd;
	}

	return $aspect_ratio;
}

/**
 * Calculate the greatest common divisor of two numbers using the Euclidean algorithm.
 */
function gcd( int $a, int $b ): int {
	return $b ? gcd( $b, $a % $b ) : $a;
}

/**
 * Calculates seconds based on youtube times if needed
 *
 * @param string $time The 't=1h25m13s' or t=123 part of youtube URLs.
 *
 * @return int         Starttime in seconds.
 */
function youtube_time_to_seconds( string $time ): int {

	if ( is_numeric( $time ) ) {
		return (int) $time;
	}

	$pattern = '/' .
		'(?<h>[0-9]+h)?' .
		'(?<m>[0-9]+m)?' .
		'(?<s>[0-9]+s)?/';

	preg_match( $pattern, $time, $matches );

	foreach ( array( 'h', 'm', 's' ) as $m ) {
		if ( ! isset( $matches[ $m ] ) ) {
			$matches[ $m ] = 0;
		}
	}

	return ( (int) $matches['h'] * 60 * 60 ) +
		( (int) $matches['m'] * 60 ) +
		(int) $matches['s'];
}

/**
 * Calculate the new height based on the old width, old height, and new width.
 *
 * @return float            The new height
 */
function new_height( float $old_width, float $old_height, int $new_width ): float {
	$aspect_num = $old_width / $old_height;
	$new_height = $new_width / $aspect_num;

	return $new_height;
}

/**
 * Calculates padding percentage value for a particular aspect ratio
 *
 * @param string $aspect_ratio Example '4:3'
 *
 * @since 4.2.0
 *
 */
function aspect_ratio_to_percentage( string $aspect_ratio ): float {

	list(  $width, $height ) = explode( ':', $aspect_ratio );
	$percentage              = ( (int) $height / (int) $width ) * 100;

	return $percentage;
}

function disabled_on_feeds(): bool {
	return is_feed() && ! options()['feed'] ? true : false;
}

function is_valid_date_time( string $datetime_str ): bool {
	try {
		new \DateTime( $datetime_str );
		return true;
	} catch ( \Exception $e ) {

		arve_errors()->add(
			__FUNCTION__,
			sprintf(
				// Translators: %s is the invalid datetime string.
				__( 'Invalid datetime: <code>%s</code>', 'advanced-responsive-video-embedder' ),
				$datetime_str
			),
			[
				'code'    => $e->getCode(),
				'message' => $e->getMessage(),
			]
		);

		return false;
	}
}

/**
 * Checks if a datetime string contains timezone information.
 *
 * @param string $datetime_str The datetime string to check.
 * @return bool                True if timezone is present, false otherwise.
 */
function has_timezone( string $datetime_str ): bool {
	// Match common timezone formats:
	// Z - Zulu time (UTC)
	// +00:00 or -05:00 - Offset format
	// +0000 or -0500 - Compact offset format
	// GMT, UTC, EST, etc. - Named timezones (3-5 letters)
	// America/New_York - IANA timezone identifiers
	return preg_match( '/(?:Z|[+-]\d{2}:?\d{2}|(?:GMT|UTC)|[A-Z]{3,5}|[A-Za-z]+\/[A-Za-z_]+)$/i', $datetime_str ) === 1;
}

/**
 * Normalizes a datetime string to ATOM format.
 *
 * @param string  $datetime_str      The datetime string to normalize.
 * @param string  $fallback_timezone The fallback timezone 'UTC' or 'WP' for WordPress timezone.
 * @return string                    The normalized datetime string in ATOM format.
 */
function normalize_datetime_to_atom( string $datetime_str, string $fallback_timezone ): string {

	if ( ! is_valid_date_time( $datetime_str ) ) {
		return $datetime_str;
	}

	switch ( strtolower( $fallback_timezone ) ) {
		case 'wp':
			$fallback_timezone = wp_timezone();
			break;
		case 'utc':
			$fallback_timezone = new \DateTimeZone( 'UTC' );
			break;
		default:
			throw new \InvalidArgumentException( 'Invalid fallback timezone' );
	}

	$timezone = has_timezone( $datetime_str ) ? null : $fallback_timezone;
	$dt       = new \DateTime( $datetime_str, $timezone );

	return $dt->format( \DateTime::ATOM );
}

/**
 * @param string|int $time
 */
function seconds_to_iso8601_duration( $time ): string {
	$units = array(
		'Y' => 365 * 24 * 3600,
		'D' => 24 * 3600,
		'H' => 3600,
		'M' => 60,
		'S' => 1,
	);

	$str     = 'P';
	$is_time = false;

	foreach ( $units as  $unit_name => $unit ) {
		$quot  = intval( $time / $unit );
		$time -= $quot * $unit;
		$unit  = $quot;
		if ( $unit > 0 ) {
			if ( ! $is_time && in_array( $unit_name, array( 'H', 'M', 'S' ), true ) ) { // There may be a better way to do this
				$str    .= 'T';
				$is_time = true;
			}
			$str .= strval( $unit ) . $unit_name;
		}
	}

	return $str;
}

/**
 * Check if Gutenberg is enabled.
 * Must be used not earlier than plugins_loaded action fired.
 *
 */
function is_gutenberg(): bool {

	$gutenberg    = false;
	$block_editor = false;

	if ( has_filter( 'replace_editor', 'gutenberg_init' ) ) {
		// Gutenberg is installed and activated.
		$gutenberg = true;
	}

	if ( version_compare( $GLOBALS['wp_version'], '5.0-beta', '>' ) ) {
		// Block editor.
		$block_editor = true;
	}

	if ( ! $gutenberg && ! $block_editor ) {
		return false;
	}

	if ( ! class_exists( 'Classic_Editor' ) ) {
		return true;
	}

	$use_block_editor = ( get_option( 'classic-editor-replace' ) === 'no-replace' );

	return $use_block_editor;
}

function is_amp(): bool {
	return function_exists( __NAMESPACE__ . '\AMP\is_amp' ) && AMP\is_amp();
}

/**
 * Register oEmbed Widget.
 *
 * @param \Elementor\Widgets_Manager $widgets_manager Elementor widgets manager.
 */
function register_elementor_widget( \Elementor\Widgets_Manager $widgets_manager ): void {

	require_once __DIR__ . '/ElementorWidget.php';

	$widgets_manager->register( new ElementorWidget() );
}

function translation( string $context ): string {

	switch ( $context ) {
		// Pro
		case 'link_removed':
			return __( 'link removed', 'advanced-responsive-video-embedder' );
		case 'video':
			return __( 'Video', 'advanced-responsive-video-embedder' );
		case 'play_video': // deprecated
			return __( 'Play video', 'advanced-responsive-video-embedder' );
		case 'latest_video_from_youtube_channel_could_not_be_detected':
			// Translators: %s URL
			return __( 'Latest video from <a href="%s">YouTube channel</a> could not be detected.', 'advanced-responsive-video-embedder' );
		case 'open_lightbox_with_video': // deprecated
			return __( 'Open lightbox with video', 'advanced-responsive-video-embedder' );
		case 'play_video_%':
			// Translators: %s Video Title
			return __( 'Play video: %s', 'advanced-responsive-video-embedder' );
		// Privacy
		case 'arve_cached_thumbnail_for':
			// Translators: %1$s URL, %2$s title.
			return __( 'ARVE cached thumbnail for %s', 'advanced-responsive-video-embedder' );
		case 'by_clicking_play_you_consent':
			// Translators: %1$s domain name, %2$s URL, %3$s privacy policy URL, %4$s privacy policy title.
			return __(
				'By clicking play, you consent to load content from %1$s in a <a href="%2$s">privacy enhanced iframe</a> and setting a cookie on this site to store your choice. See <a href="%3$s">%4$s</a>.',
				'advanced-responsive-video-embedder'
			);
		default:
			arve_errors()->add(
				'no-translation',
				sprintf(
					// Translators: %s translation key
					__( 'Unknown translation key <code>%s</code>', 'advanced-responsive-video-embedder' ),
					$context
				)
			);
			return '';
	}
}
