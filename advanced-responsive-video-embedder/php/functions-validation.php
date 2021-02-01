<?php
namespace Nextgenthemes\ARVE;

function valid_url( $url ) {

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

function bool_to_shortcode_string( $val ) {

	if ( false === $val ) {
		return 'n';
	}

	return (string) $val;
}
