<?php

if ( ! function_exists( 'str_contains' ) ) {

	function str_contains( $haystack, $needle ) {
		return false !== strpos( $haystack, $needle );
	}
}

if ( ! function_exists( 'str_starts_with' ) ) {

	function str_starts_with( $haystack, $needle ) {
		return $haystack[0] === $needle[0] ? strncmp( $haystack, $needle, strlen( $needle ) ) === 0 : false;
	}
}

if ( ! function_exists( 'str_ends_with' ) ) {

	function str_ends_with( $haystack, $needle ) {

		if ( '' === $needle ) {
			return true;
		}

		// search forward starting from end minus needle length characters
		$diff = strlen( $haystack ) - strlen( $needle );

		return $diff >= 0 && strpos( $haystack, $needle, $diff ) !== false;
	}
}
