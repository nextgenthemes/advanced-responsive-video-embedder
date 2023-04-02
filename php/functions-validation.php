<?php
namespace Nextgenthemes\ARVE;

function valid_url( string $url ): bool {

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

function bool_to_shortcode_string( bool $val ): string {

	if ( true === $val ) {
		return 'y';
	}

	return 'n';
}
