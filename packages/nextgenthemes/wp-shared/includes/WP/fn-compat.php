<?php
/**
 * These are based on symfony/polyfill-php80. The aim of this is to have a minimalistic polyfill
 * for only the str_* functions of PHP 8.0. Requires php 7.1 or higher.
 *
 * @link https://github.com/symfony/polyfill-php80/blob/1.x/bootstrap.php
 * @link https://github.com/symfony/polyfill-php80/blob/1.x/Php80.php
 *
 * @license GPL-3.0
 * @copyright (c) Fabien Potencier <fabien@symfony.com>, (c) 2024 Nicolas Jonas nextgenthemes.com
 *
 * @author Ion Bazan <ion.bazan@gmail.com>
 * @author Nico Oelgart <nicoswd@gmail.com>
 * @author Nicolas Grekas <p@tchwork.com>
 * @author Nicolas Jonas nextgenthemes.com
 */

// < PHP 7.1
if ( \PHP_VERSION_ID < 70100 ) {
	exit( 'The str_contains, str_starts_with and str_ends_with polyfills require PHP 7.1 or higher.' );
}

// >= PHP 8.0
if ( \PHP_VERSION_ID >= 80000 ) {
	return;
}

if ( ! function_exists('str_contains') ) {
	function str_contains( ?string $haystack, ?string $needle ): bool {

		$haystack = $haystack ?? '';
		$needle   = $needle ?? '';

		return '' === $needle || false !== strpos($haystack, $needle);
	}
}
if ( ! function_exists('str_starts_with') ) {
	function str_starts_with( ?string $haystack, ?string $needle ): bool {

		$haystack = $haystack ?? '';
		$needle   = $needle ?? '';

		return 0 === strncmp($haystack, $needle, \strlen($needle));
	}
}
if ( ! function_exists('str_ends_with') ) {
	function str_ends_with( ?string $haystack, ?string $needle ): bool {

		$haystack = $haystack ?? '';
		$needle   = $needle ?? '';

		if ( '' === $needle || $needle === $haystack ) {
			return true;
		}

		if ( '' === $haystack ) {
			return false;
		}

		$needle_length = \strlen($needle);

		return $needle_length <= \strlen($haystack) && 0 === substr_compare($haystack, $needle, -$needle_length);
	}
}
