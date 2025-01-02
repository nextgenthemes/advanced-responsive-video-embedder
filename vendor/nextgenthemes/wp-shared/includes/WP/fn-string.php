<?php

declare(strict_types = 1);

namespace Nextgenthemes\WP;

/**
 * Get the value of a specific attribute from an HTML string.
 *
 * @param array $query argument for WP_HTML_Tag_Processor::next_tag
 * @param string $attribute attribute to look for
 * @param string $html HTML string to parse
 * @return string|null attribute value or null if not found or empty
 */
function get_attribute_from_html_tag( array $query, string $attribute, string $html ): ?string {

	$wphtml = new \WP_HTML_Tag_Processor( $html );

	if ( $wphtml->next_tag( $query ) ) {

		$attr_value = $wphtml->get_attribute( $attribute );

		if ( is_string( $attr_value ) && ! empty( $attr_value ) ) {
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

	return $scheme . $user . $pass . $host . $port . $path . $fragment;
}

/**
 * Convert a string to camel case.
 *
 * @param string $str The input string with dashes.
 * @param string $separator The separators to use.
 * @param bool $capitalize_first_character Whether to capitalize the first character.
 * @return string The converted camel case string.
 */
function camel_case( string $str, string $separator = '-', bool $capitalize_first_character = false ): string {

	if ( strlen( $separator ) !== 1 ) {
		throw new \InvalidArgumentException( 'Separator must be a single character.' );
	}

	$str = str_replace( $separator, '', ucwords( $str, $separator ) );

	if ( ! $capitalize_first_character ) {
		$str = lcfirst( $str );
	}

	return $str;
}

function kses_https_links( string $html ): string {

	return wp_kses(
		$html,
		array(
			'a' => array(
				'href'   => true,
				'target' => true,
				'class'  => true,
			),
		),
		array( 'https' )
	);
}

/**
 * Removes the specified suffix from the given string.
 *
 * @param string $haystack The input string
 * @param string $needle The suffix to be removed
 * @return string The modified string
 */
function remove_suffix( string $haystack, string $needle ): string {

	if ( str_ends_with( $haystack, $needle ) ) {
		return substr( $haystack, 0, strlen( $haystack ) - strlen( $needle ) );
	}

	return $haystack;
}

/**
 * Validates a URL. Returns the URL back if it is valid. Upgrades // to https:// if needed.
 *
 * @param string $url The URL to be validated.
 * @return string|null url back if valid or null if invalid
 */
function valid_url( string $url ): ?string {

	if ( empty( $url ) ) {
		return null;
	}

	if ( str_starts_with( $url, '//' ) ) {
		$url = 'https:' . $url;
	}

	if ( filter_var( $url, FILTER_VALIDATE_URL ) !== false ) {
		return $url;
	}

	return null;
}

/**
 * Returns the file extension from a given URL.
 *
 * @param string $url The URL from which to extract the file extension
 * @return string The file extension, or an empty string if none is found
 */
function get_file_extension( string $url ): string {
	// Return the file extension or an empty string if there is none
	return pathinfo( (string) parse_url( $url, PHP_URL_PATH ), PATHINFO_EXTENSION );
}

/**
 * Replaces the extension of the given filename with the new extension.
 *
 * @param string $filename The original filename including the path.
 * @param string $new_extension The new extension to replace the existing one.
 * @return string The modified filename with the new extension.
 */
function replace_extension( string $filename, string $new_extension ): string {
	$info = pathinfo( $filename );
	$dir  = $info['dirname'] ? $info['dirname'] . DIRECTORY_SEPARATOR : '';

	return $dir . $info['filename'] . '.' . $new_extension;
}

/**
 * Retrieves the value of a specific query argument from the given URL.
 *
 * @param string $url The URL containing the query parameters.
 * @param string $arg The name of the query argument to retrieve.
 * @return string|null The value of the specified query argument, or null if it is not found.
 */
function get_url_arg( string $url, string $arg ): ?string {

	$query_string = parse_url( $url, PHP_URL_QUERY );

	if ( empty( $query_string ) ) {
		return null;
	}

	parse_str( $query_string, $query_args );

	return $query_args[ $arg ] ?? null;
}
