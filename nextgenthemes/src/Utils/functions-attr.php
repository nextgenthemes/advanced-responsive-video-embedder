<?php
namespace Nextgenthemes\Utils;

function attr( array $attr = [], $dailymotion = false ) {

	$html = '';

	foreach ( $attr as $key => $value ) {

		if ( false === $value || null === $value ) {
			continue;
		} elseif ( '' === $value || true === $value ) {
			$html .= sprintf( ' %s', esc_html( $key ) );
		} elseif ( in_array( $key, [ 'href', 'data-href', 'src', 'data-src' ], true ) ) {

			if ( $dailymotion ) {
				$value = str_replace( 'jukebox?list%5B0%5D', 'jukebox?list[]', esc_url( $value ) );
			} else {
				$value = esc_url( $value );
			}

			$html .= sprintf( ' %s="%s"', esc_html( $key ), $value );
		} else {
			$html .= sprintf( ' %s="%s"', esc_html( $key ), esc_attr( $value ) );
		}
	}

	return $html;
}
