<?php declare(strict_types=1);
namespace Nextgenthemes\ARVE;

function validate_thumbnail( $id_or_url ): string {

	if ( '' === $id_or_url ) {
		return $id_or_url;
	}

	// attachment id
	if ( is_int( $id_or_url ) ) {
		// we cast to string here because we store this as string type and it would come as sting from a shortcode anyway
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

	arve_errors()->add( 'validate_thumbnail', $error_msg );
	arve_errors()->add_data( $id_or_url, 'validate_thumbnail' );

	return '';
}

function validate_url( string $argname, string $url ): string {

	if ( ! empty( $url ) && ! valid_url( $url ) ) {

		$error_msg = sprintf(
			// Translators: 1 URL 2 Attr name
			__( 'Invalid URL <code>%1$s</code> in <code>%2$s</code>', 'advanced-responsive-video-embedder' ),
			esc_html( $url ),
			esc_html( $argname )
		);

		arve_errors()->add( "validate_url $argname", $error_msg );
	}

	return $url;
}

function validate_bool( string $attr_name, mixed $value ): bool {

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
		// Translators: %1$s = Attr Name, %2$s = Attribute array
		sprintf(
			// Translators: Attribute Name
			__( '%1$s <code>%2$s</code> not valid', 'advanced-responsive-video-embedder' ),
			esc_html( $attr_name ),
			esc_html( $value )
		)
	);

	arve_errors()->add_data(
		compact( 'attr_name', 'value' ),
		$error_code
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
		// Translators: Alignment
		sprintf( __( 'Align <code>%s</code> not valid', 'advanced-responsive-video-embedder' ), esc_html( $align ) )
	);

	return '';
}

function validate_aspect_ratio( string $aspect_ratio ): string {

	if ( empty( $aspect_ratio ) ) {
		return $aspect_ratio;
	}

	$ratio = explode( ':', $aspect_ratio );

	if ( empty( $ratio[0] ) || false === filter_var( $ratio[0], FILTER_VALIDATE_FLOAT) ||
		empty( $ratio[1] ) || false === filter_var( $ratio[1], FILTER_VALIDATE_FLOAT)
	) {
		arve_errors()->add(
			'validate_aspect_ratio',
			// Translators: attribute
			sprintf( __( 'Aspect ratio <code>%s</code> is not valid', 'advanced-responsive-video-embedder' ), $aspect_ratio )
		);

		return '16:9';
	}

	return $aspect_ratio;
}

function validate_height( mixed $height ): mixed {

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

function validate_int( string $prop_name, mixed $value ) {

	if ( is_int( $value ) ) {
		return $value;
	}

	if ( is_string( $value ) && ctype_digit( $value ) ) {
		return (int) $value;
	}

	arve_errors()->add(
		"validate_int $prop_name",
		// Translators: attribute name, value
		sprintf(
			__( '%1$s: <code>%2$s</code> is not valid', 'advanced-responsive-video-embedder' ),
			$prop_name,
			$value
		)
	);

	return 0;
}

function valid_url( string $url ): bool {

	pd( __FUNCTION__ . " $url" );

	if ( empty( $url ) ) {
		return false;
	}

	if ( str_starts_with( $url, '//' ) ) {
		$url = 'https:' . $url;
	}

	if ( filter_var( $url, FILTER_VALIDATE_URL ) !== false ) {
		return true;
	}

	return false;
}
