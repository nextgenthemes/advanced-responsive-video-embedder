<?php
namespace Nextgenthemes\ARVE\Common\Utils;

function contains( $haystack, $needle ) {
	return strpos( $haystack, $needle ) !== false;
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
