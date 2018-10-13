<?php
namespace Nextgenthemes\ARVE;

use function Nextgenthemes\Utils\starts_with;
use function Nextgenthemes\Utils\ends_with;

function valid_url( $url ) {

	if ( starts_with( $url, '//' ) ) {
		$url = 'https:' . $url;
	}

	if ( filter_var( $url, FILTER_VALIDATE_URL ) !== false ) {
		return true;
	}

	return false;
}

function validate_url( $url, $attr_name ) {

	if ( ! empty( $url ) && ! valid_url( $url ) ) {
		return new \WP_Error( "invalid url $attr_name",
			sprintf(
				// Translators: 1 URL 2 Attr name
				__( 'Invalid URL <code>%1$s</code> in <code>%2$s</code>', 'advanced-responsive-video-embedder' ),
				esc_html( $url ),
				esc_html( $attr_name )
			)
		);
	}

	return $url;
}

function validate_aspect_ratio( $aspect_ratio ) {

	if ( empty( $aspect_ratio ) ) {
		return $aspect_ratio;
	}

	$a = explode( ':', $aspect_ratio );

	if ( ! empty( $a[0] )
		&& is_numeric( $a[0] )
		&& ! empty( $a[1] )
		&& is_numeric( $a[1] )
	) {
		return $aspect_ratio;
	}

	return new \WP_Error( 'Aspect ratio',
		// Translators: Aspect Ratio
		sprintf( __( 'Aspect ratio <code>%s</code> is not valid', 'advanced-responsive-video-embedder' ), $aspect_ratio )
	);
}

function bool_to_shortcode_string( $val ) {

	if ( false === $val ) {
		return 'n';
	}

	return (string) $val;
}

// phpcs:disable Generic.Metrics.CyclomaticComplexity.TooHigh
function validate_bool( $val, $name ) {

	switch ( $val ) {
		case 'true':
		case '1':
		case 'y':
		case 'yes':
		case 'on':
			return true;
		case '':
		case null:
			return null;
		case 'false':
		case '0':
		case 'n':
		case 'no':
		case 'off':
			return false;
		default:
			return new \WP_Error(
				$name,
				// Translators: 1 Shortcode attr name, 2 Value
				sprintf( __( '%1$s <code>%2$s</code> not valid', 'advanced-responsive-video-embedder' ), $name, $val )
			);
	}//end switch
}
// phpcs:enable

function validate_align( $align ) {

	switch ( $align ) {
		case null:
		case '':
		case 'none':
			$align = null;
			break;
		case 'left':
		case 'right':
		case 'center':
			$align = $align;
			break;
		default:
			$align = new \WP_Error(
				'align',
				// Translators: Alignment
				sprintf( __( 'Align <code>%s</code> not valid', 'advanced-responsive-video-embedder' ), esc_html( $align ) )
			);
			break;
	}

	return $align;
}
