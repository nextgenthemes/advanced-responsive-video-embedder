<?php declare(strict_types=1);
namespace Nextgenthemes\WP;

/**
 * Get the value of a specific attribute from an HTML string.
 *
 * @param array $query argument for WP_HTML_Tag_Processor::next_tag
 * @param string $attribute attribute to look for
 * @param string $html HTML string to parse
 * @return string|null attribute value or null if not found or empty
 */
function get_attribute_value_from_html_tag( array $query, string $attribute, string $html ): ?string {

	$wphtml = new \WP_HTML_Tag_Processor( $html );

	if ( $wphtml->next_tag( $query ) ) {

		$attr_value = $wphtml->get_attribute( $attribute );

		if ( is_string( $attr_value ) && ! empty( $attr_value) ) {
			return $attr_value;
		}
	}

	return null;
}

/**
 * Checks if any of the needles are contained within the haystack.
 *
 * @param string $haystack The string to search in.
 * @param array $needles An array of strings to search for.
 */
function str_contains_any( string $haystack, array $needles ): bool {

	foreach ( $needles as $needle ) {

		if ( str_contains( $haystack, $needle ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Removes the query string from the given URL.
 *
 * @param string $url The input URL
 * @return string The URL without the query string
 */
function remove_url_query( string $url ): string {

	$parsed_url = parse_url( $url );

	if ( ! $parsed_url ) {
		return $url;
	}

	$scheme   = isset( $parsed_url['scheme'] ) ? $parsed_url['scheme'] . '://' : '';
	$host     = isset( $parsed_url['host'] ) ? $parsed_url['host'] : '';
	$port     = isset( $parsed_url['port'] ) ? ':' . $parsed_url['port'] : '';
	$user     = isset( $parsed_url['user'] ) ? $parsed_url['user'] : '';
	$pass     = isset( $parsed_url['pass'] ) ? ':' . $parsed_url['pass'] : '';
	$pass     = ( $user || $pass ) ? "$pass@" : '';
	$path     = isset( $parsed_url['path'] ) ? $parsed_url['path'] : '';
	$fragment = isset( $parsed_url['fragment'] ) ? '#' . $parsed_url['fragment'] : '';

	return "$scheme$user$pass$host$port$path$fragment";
}

/**
 * Convert a string with dashes to camel case.
 *
 * @param string $string The input string with dashes.
 * @param bool $capitalize_first_character Whether to capitalize the first character.
 * @return string The converted camel case string.
 */
function dashes_to_camel_case( string $string, bool $capitalize_first_character = false ): string {

	$str = str_replace( '-', '', ucwords( $string, '-' ) );

	if ( ! $capitalize_first_character ) {
		$str = lcfirst( $str );
	}

	return $str;
}

/**
 * Removes the specified suffix from the given string.
 *
 * @param string $haystack The input string
 * @param string $needle The suffix to be removed
 * @return string The modified string
 */
function remove_suffix( string $haystack, string $needle ): string {

	if ( str_ends_with($haystack, $needle) ) {
		return substr($haystack, 0, strlen($haystack) - strlen($needle));
	}

	return $haystack;
}

/**
 * Validates a URL.
 *
 * @param string $url The URL to be validated.
 */
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
