<?php
// phpcs:disable SlevomatCodingStandard.TypeHints
if ( ! function_exists( 'nextgenthemes_maybe_add_async_or_defer' ) ) {

	add_action( 'script_loader_tag', 'nextgenthemes_maybe_add_async_or_defer', 10, 2 );
	/**
	 * Adds async/defer attributes to enqueued / registered scripts.
	 *
	 * If #12009 lands in WordPress, this function can no-op since it would be handled in core.
	 *
	 * @link https://github.com/WordPress/wordpress-develop/blob/trunk/src/wp-content/themes/twentytwenty/classes/class-twentytwenty-script-loader.php
	 * @link https://core.trac.wordpress.org/ticket/12009
	 *
	 * @param string $tag    The script tag.
	 * @param string $handle The script handle.
	 * @return string Script HTML string.
	 */
	function nextgenthemes_maybe_add_async_or_defer( string $tag, string $handle ): string {
		foreach ( array( 'async', 'defer' ) as $attr ) {
			if ( ! wp_scripts()->get_data( $handle, $attr ) ) {
				continue;
			}
			// Prevent adding attribute when already added in #12009.
			if ( ! preg_match( ":\s$attr(=|>|\s):", $tag ) ) {
				$tag = preg_replace( ':(?=></script>):', " $attr", $tag, 1 );
			}
			// Only allow async or defer, not both.
			break;
		}

		return $tag;
	}
}
