<?php
namespace Nextgenthemes\ARVE\Common;

function contains_any( $haystack, array $needles ) {

	foreach ( $needles as $needle ) {

		if ( contains( $haystack, $needle ) ) {
			return true;
		}
	}

	return false;
}

function contains( $haystack, $needle ) {
	return false !== strpos( $haystack, $needle );
}

function starts_with( $haystack, $needle ) {
	return $haystack[0] === $needle[0] ? strncmp( $haystack, $needle, strlen( $needle ) ) === 0 : false;
}

function ends_with( $haystack, $needle ) {
	// search forward starting from end minus needle length characters
	if ( '' === $needle ) {
		return true;
	}

	$diff = strlen( $haystack ) - strlen( $needle );

	return $diff >= 0 && strpos( $haystack, $needle, $diff ) !== false;
}

function remove_url_query( $url ) {

	$parsed_url = parse_url( $url );

	if ( ! $parsed_url ) {
		return $url;
	}

	$scheme   = isset( $parsed_url['scheme'] )   ? $parsed_url['scheme'] . '://' : '';
	$host     = isset( $parsed_url['host'] )     ? $parsed_url['host'] : '';
	$port     = isset( $parsed_url['port'] )     ? ':' . $parsed_url['port'] : '';
	$user     = isset( $parsed_url['user'] )     ? $parsed_url['user'] : '';
	$pass     = isset( $parsed_url['pass'] )     ? ':' . $parsed_url['pass']  : '';
	$pass     = ( $user || $pass )               ? "$pass@" : '';
	$path     = isset( $parsed_url['path'] )     ? $parsed_url['path'] : '';
	$fragment = isset( $parsed_url['fragment'] ) ? '#' . $parsed_url['fragment'] : '';

	return "$scheme$user$pass$host$port$path$fragment";
}
