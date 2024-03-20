<?php

// This is a WP 6.4 function, ARVE Supports 6.2 with this
if ( ! function_exists( 'wp_trigger_error' ) ) {
	/**
	 * Generates a user-level error/warning/notice/deprecation message.
	 *
	 * Generates the message when `WP_DEBUG` is true.
	 *
	 * @since 6.4.0
	 *
	 * @param string $function_name The function that triggered the error.
	 * @param string $message       The message explaining the error.
	 *                              The message can contain allowed HTML 'a' (with href), 'code',
	 *                              'br', 'em', and 'strong' tags and http or https protocols.
	 *                              If it contains other HTML tags or protocols, the message should be escaped
	 *                              before passing to this function to avoid being stripped {@see wp_kses()}.
	 * @param int    $error_level   Optional. The designated error type for this error.
	 *                              Only works with E_USER family of constants. Default E_USER_NOTICE.
	 */
	function wp_trigger_error( string $function_name, string $message, int $error_level = E_USER_NOTICE ): void {

		// Bail out if WP_DEBUG is not turned on.
		if ( ! WP_DEBUG ) {
			return;
		}

		/**
		 * Fires when the given function triggers a user-level error/warning/notice/deprecation message.
		 *
		 * Can be used for debug backtracking.
		 *
		 * @since 6.4.0
		 *
		 * @param string $function_name The function that was called.
		 * @param string $message       A message explaining what has been done incorrectly.
		 * @param int    $error_level   The designated error type for this error.
		 */
		do_action( 'wp_trigger_error_run', $function_name, $message, $error_level );

		if ( ! empty( $function_name ) ) {
			$message = sprintf( '%s(): %s', $function_name, $message );
		}

		$message = wp_kses(
			$message,
			array(
				'a'      => array( 'href' => true ),
				'br'     => array(),
				'code'   => array(),
				'em'     => array(),
				'strong' => array(),
			),
			array( 'http', 'https' )
		);

		// phpcs:ignore
		\trigger_error( $message, $error_level );
	}
}
