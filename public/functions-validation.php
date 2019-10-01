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

function arve_validate_aspect_ratio( $aspect_ratio ) {

	if ( empty( $aspect_ratio ) ) {
		return $aspect_ratio;
	}

	$a = explode( ':', $aspect_ratio );

	if ( ! empty( $a[0] ) && is_numeric( $a[0] ) && ! empty( $a[1] ) && is_numeric( $a[1] ) ) {
		return $aspect_ratio;
	}

	return new WP_Error(
		'Aspect ratio',
		sprintf( __( 'Aspect ratio <code>%s</code> is not valid', ARVE_SLUG ), $aspect_ratio )
	);
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
			return true;
		case null:
			return null;
		case 'false':
		case '0':
		case 'n':
		case 'no':
		case 'off':
			return false;
		default:
			return new WP_Error(
				$name,
				sprintf( __( '%1$s <code>%2$s</code> not valid', ARVE_SLUG ), $name, $val )
			);
	}
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

	if ( 'thumbnail' === $mode ) {
		$mode = 'lazyload-lightbox';
	}

	if ( 'veoh' === $mode ) {
		$mode = 'normal';
	}

	$supported_modes = arve_get_supported_modes();

	if ( ! array_key_exists( $mode, $supported_modes ) ) {

		$mode = 'normal';
	}

	return $mode;
}
