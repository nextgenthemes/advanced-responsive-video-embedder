<?php

declare(strict_types = 1);

namespace Nextgenthemes\ARVE;

use function Nextgenthemes\WP\valid_url;

/**
 * @param string|int $id_or_url
 */
function validate_thumbnail( $id_or_url ): string {

	if ( '' === $id_or_url ) {
		return $id_or_url;
	}

	// attachment id
	if ( is_int( $id_or_url ) ) {
		// we cast to string here because we store this as string type and it would come as string from a shortcode anyway
		return (string) $id_or_url;
	}

	// attachment id as string
	if ( is_string( $id_or_url ) && ctype_digit( $id_or_url ) ) {
		return $id_or_url;
	}

	// url
	if ( is_string( $id_or_url ) && ! empty( $id_or_url ) && valid_url( $id_or_url ) ) {
		return $id_or_url;
	}

	$error_msg = sprintf(
		// Translators: 1 URL 2 Attr name
		__( 'Invalid Thumbnail <code>%1$s</code>', 'advanced-responsive-video-embedder' ),
		esc_html( $id_or_url ),
	);

	arve_errors()->add( 'validate_thumbnail', $error_msg, $id_or_url );

	return '';
}

/**
 * Validates a URL and returns the validated URL or an error message. Upgrades // to https:// if needed.
 *
 * @param string $arg_name The name of the argument being validated.
 * @param string $value The value of the argument being validated. Can be an URL or a HTML string with the a embed code.
 * @return string The validated URL or an error message.
 */
function validate_url( string $arg_name, string $url ): string {

	if ( '' === $url ) {
		return $url;
	}

	$url = valid_url( $url );

	if ( ! $url ) { // invalid url

		$error_msg = sprintf(
			// Translators: 1 URL 2 Attr name
			__( 'Invalid URL <code>%1$s</code> in <code>%2$s</code>', 'advanced-responsive-video-embedder' ),
			esc_html( $url ),
			esc_html( $arg_name )
		);

		arve_errors()->add( "validate_url $arg_name", $error_msg );
	}

	return (string) $url;
}

/**
 * @param mixed $value
 */
function validate_type_bool( string $attr_name, $value ): bool {

	if ( is_bool( $value ) ) {
		return $value;
	}

	if ( in_array(
		$value,
		[
			'true',
			'1',
			'y',
			'yes',
			'on',
		],
		true
	) ) {
		return true;
	}

	if ( in_array(
		$value,
		[
			'false',
			'0',
			'n',
			'no',
			'off',
		],
		true
	) ) {
		return false;
	}

	$error_code = "validate_bool $attr_name";

	arve_errors()->add(
		$error_code,
		sprintf(
			// Translators: %1$s = attribute name, %2$s = attribute value
			__( '%1$s <code>%2$s</code> not valid', 'advanced-responsive-video-embedder' ),
			esc_html( $attr_name ),
			esc_html( $value )
		),
		compact( 'attr_name', 'value' )
	);

	return false;
}

function validate_align( string $align ): string {

	switch ( $align ) {
		case '':
		case 'none':
			return '';
		case 'left':
		case 'right':
		case 'center':
		case 'wide':
		case 'full':
			return $align;
	}

	arve_errors()->add(
		'validate_align',
		// Translators: %s is align value
		sprintf( __( 'Align <code>%s</code> not valid', 'advanced-responsive-video-embedder' ), esc_html( $align ) )
	);

	return '';
}

function validate_aspect_ratio( ?string $aspect_ratio ): ?string {

	// first time we set it it will be an empty string
	if ( '' === $aspect_ratio || null === $aspect_ratio ) {
		return $aspect_ratio;
	}

	$ratio = explode( ':', $aspect_ratio );

	if ( empty( $ratio[0] ) || false === filter_var( $ratio[0], FILTER_VALIDATE_FLOAT ) ||
		empty( $ratio[1] ) || false === filter_var( $ratio[1], FILTER_VALIDATE_FLOAT )
	) {
		arve_errors()->add(
			'validate_aspect_ratio',
			// Translators: %s is aspect_ratio value
			sprintf( __( 'Aspect ratio <code>%s</code> is not valid', 'advanced-responsive-video-embedder' ), $aspect_ratio ),
		);

		return '16:9';
	}

	// valid aspect ratio
	return $aspect_ratio;
}

/**
 * @param mixed $height
 *
 * @return mixed
 */
function validate_height( $height ) {

	if ( ! is_numeric( $height ) ) {
		arve_errors()->add(
			'validate_height',
			// Translators: attribute
			sprintf( __( 'Height not numeric', 'advanced-responsive-video-embedder' ), $height )
		);
		return 0;
	}

	return $height;
}

/**
 * @param mixed $value
 */
function validate_type_int( string $prop_name, $value ): int {

	if ( is_int( $value ) ) {
		return $value;
	}

	if ( is_string( $value ) && ctype_digit( $value ) ) {
		return (int) $value;
	}

	arve_errors()->add(
		"validate_int $prop_name",
		sprintf(
			// translators: attribute name, value
			__( '%1$s: <code>%2$s</code> is not valid', 'advanced-responsive-video-embedder' ),
			$prop_name,
			$value
		)
	);

	return 0;
}
