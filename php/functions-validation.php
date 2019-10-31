<?php
namespace Nextgenthemes\ARVE;

function valid_url( $url ) {

	if ( Common\starts_with( $url, '//' ) ) {
		$url = 'https:' . $url;
	}

	if ( filter_var( $url, FILTER_VALIDATE_URL ) !== false ) {
		return true;
	}

	return false;
}

function validate_url( $a, $attr_name ) {

	if ( ! empty( $a[ $attr_name ] ) && ! valid_url( $a[ $attr_name ] ) ) {

		$error_msg = sprintf(
			// Translators: 1 URL 2 Attr name
			__( 'Invalid URL <code>%1$s</code> in <code>%2$s</code>', 'advanced-responsive-video-embedder' ),
			esc_html( $a[ $attr_name ] ),
			esc_html( $attr_name )
		);

		$a['errors']->add( $attr_name, $error_msg );
	}

	return $a;
}

function validate_aspect_ratio( $a ) {

	if ( empty( $a['aspect_ratio'] ) ) {
		return $a;
	}

	$ratio = explode( ':', $a['aspect_ratio'] );

	if ( empty( $ratio[0] ) || ! is_numeric( $ratio[0] ) ||
		empty( $ratio[1] ) || ! is_numeric( $ratio[1] )
	) {
		$a['errors']->add(
			'aspect_ratio',
			// Translators: attribute
			sprintf( __( 'Aspect ratio <code>%s</code> is not valid', 'advanced-responsive-video-embedder' ), $a['aspect_ratio'] )
		);

		$a['aspect_ratio'] = null;
	}

	return $a;
}

function bool_to_shortcode_string( $val ) {

	if ( false === $val ) {
		return 'n';
	}

	return (string) $val;
}

// phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh
function validate_bool( array $a, $attr_name ) {

	switch ( $a[ $attr_name ] ) {
		case 'true':
		case '1':
		case 'y':
		case 'yes':
		case 'on':
			$a[ $attr_name ] = true;
			break;
		case '':
		case null:
			$a[ $attr_name ] = null;
			break;
		case 'false':
		case '0':
		case 'n':
		case 'no':
		case 'off':
			$a[ $attr_name ] = false;
			break;
		default:
			$a['errors']->add(
				$attr_name,
				// Translators: %1$s = Attr Name, %2$s = Attribute array
				sprintf(
					// Translators: Attribute Name
					__( '%1$s <code>%2$s</code> not valid', 'advanced-responsive-video-embedder' ),
					esc_html( $attr_name ),
					esc_html( $a[ $attr_name ] )
				)
			);
			break;
	}//end switch

	return $a;
}

function validate_align( $a ) {

	switch ( $a['align'] ) {
		case null:
		case '':
		case 'none':
			$a['align'] = null;
			break;
		case 'left':
		case 'right':
		case 'center':
			break;
		default:
			$a['errors']->add(
				'align',
				// Translators: Alignment
				sprintf( __( 'Align <code>%s</code> not valid', 'advanced-responsive-video-embedder' ), esc_html( $a['align'] ) )
			);
			$a['align'] = null;
			break;
	}

	return $a;
}
