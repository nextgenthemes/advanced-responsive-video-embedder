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

function validate_url( $a, $attr_name ) {

	if ( ! empty( $a[ $attr_name ] ) && ! valid_url( $a[ $attr_name ] ) ) {

		$error_msg = sprintf(
			// Translators: 1 URL 2 Attr name
			__( 'Invalid URL <code>%1$s</code> in <code>%2$s</code>', 'advanced-responsive-video-embedder' ),
			esc_html( $a[ $attr_name ] ),
			esc_html( $attr_name )
		);

		$a = add_error( $a, $attr_name, $error_msg, 'remove-all-filters' );
	}

	return $a;
}

function validate_aspect_ratio( $a ) {

	if ( empty( $a['aspect_ratio'] ) ) {
		return $a;
	}

	$a = explode( ':', $a['aspect_ratio'] );

	if ( ! empty( $a[0] )
		&& is_numeric( $a[0] )
		&& ! empty( $a[1] )
		&& is_numeric( $a[1] )
	) {
		return $a;
	}

	return add_error(
		$a,
		'aspect_ratio',
		// Translators: attribute
		sprintf( __( 'Aspect ratio <code>%s</code> is not valid', 'advanced-responsive-video-embedder' ), $a['aspect_ratio'] )
	);
}

function bool_to_shortcode_string( $val ) {

	if ( false === $val ) {
		return 'n';
	}

	return (string) $val;
}

// phpcs:disable Generic.Metrics.CyclomaticComplexity.TooHigh
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
			$a = add_error(
				$a,
				$attr_name,
				// phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_print_r
				// Translators: %1$s = Attr Name, %2$s = Attribute array
				sprintf(
					// Translators: Attribute Name
					__( '%1$s <code>%2$s</code> not valid', 'advanced-responsive-video-embedder' ),
					$attr_name,
					print_r( $a[ $attr_name ] )
				)
				// phpcs:enable
			);
			break;
	}//end switch

	return $a;
}
// phpcs:enable

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
			$a = add_error(
				$a,
				'align',
				// Translators: Alignment
				sprintf( __( 'Align <code>%s</code> not valid', 'advanced-responsive-video-embedder' ), esc_html( $align ) )
			);
			break;
	}

	return $a;
}
