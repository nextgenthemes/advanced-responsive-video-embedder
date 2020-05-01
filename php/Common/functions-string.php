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
