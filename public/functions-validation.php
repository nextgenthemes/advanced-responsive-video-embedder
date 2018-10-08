<?php
namespace Nextgenthemes\ARVE;

function validate_url( $url ) {

	if ( \Nextgenthemes\Utils\starts_with( $url, '//' ) ) {
		$url = 'https:' . $url;
	}

	if ( \Nextgenthemes\Utils\starts_with( $url, 'http' ) && filter_var( $url, FILTER_VALIDATE_URL ) !== false ) {
		return true;
	}

	return false;
}

function validate_aspect_ratio( $aspect_ratio ) {

	if ( empty( $aspect_ratio ) ) {
		return $aspect_ratio;
	}

	$a = explode( ':', $aspect_ratio );

	if ( ! empty( $a[0] ) && is_numeric( $a[0] ) && ! empty( $a[1] ) && is_numeric( $a[1] ) ) {
		return $aspect_ratio;
	}

	return new WP_Error( 'Aspect ratio',
		// Transltors: Aspect Ratio
		sprintf( __( 'Aspect ratio <code>%s</code> is not valid', 'advanced-responsive-video-embedder' ), $aspect_ratio )
	);
}

function bool_to_shortcode_string( $val ) {

	if ( false === $val ) {
		return 'n';
	}

	return (string) $val;
}

function validate_bool( $val, $name ) {

	switch ( $val ) {
		case 'true':
		case '1':
		case 'y':
		case 'yes':
		case 'on':
			return true;
			break;
		case null;
			return null;
			break;
		case 'false':
		case '0':
		case 'n':
		case 'no':
		case 'off':
			return false;
			break;
		default:
			return new WP_Error( $name,
				sprintf( __( '%s <code>%s</code> not valid', 'advanced-responsive-video-embedder' ), $name, $val )
			);
			break;
	}
}

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
			break;
		default:
			$align = new WP_Error( 'align', sprintf( __( 'Align <code>%s</code> not valid', 'advanced-responsive-video-embedder' ), esc_html( $align ) ) );
			break;
	}

	return $align;
}

function validate_mode( $mode, $provider ) {

	if ( 'thumbnail' === $mode ) {
		$mode = 'lazyload-lightbox';
	}

	if ( 'veoh' === $mode ) {
		$mode = 'normal';
	}

	$supported_modes = get_supported_modes();

	if ( ! array_key_exists( $mode, $supported_modes ) ) {

		#$mode = new WP_Error( 'mode', sprintf(
		#	__( 'Mode: <code>%s</code> is invalid or not supported. Note that you will need the Pro Addon activated for modes other than normal.', 'advanced-responsive-video-embedder' ),
		#	esc_html( $mode )
		#) );

		$mode = 'normal';
	}

	return $mode;
}
