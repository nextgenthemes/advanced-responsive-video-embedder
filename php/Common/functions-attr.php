<?php
namespace Nextgenthemes\ARVE\Common;

function attr( array $attr = [] ) {

	$html = '';

	foreach ( $attr as $key => $value ) {

		if ( false === $value || null === $value ) {
			continue;
		} elseif ( '' === $value || true === $value ) {
			$html .= sprintf( ' %s', esc_html( $key ) );
		} elseif ( in_array( $key, [ 'href', 'data-href', 'src', 'data-src' ], true ) ) {
			$html .= sprintf( ' %s="%s"', esc_html( $key ), esc_url( $value ) );
		} else {
			$html .= sprintf( ' %s="%s"', esc_html( $key ), esc_attr( $value ) );
		}
	}

	return $html;
}
