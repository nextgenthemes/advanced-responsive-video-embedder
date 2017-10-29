<?php

class Tests_Oembed extends WP_UnitTestCase {

	/**
	 * Test if all the file hooks are working.
	 *
	 * @since 2.3.6
	 */
	public function test_check_for_wp_providers() {

		$check = arve_oembed_provider_check();

		$this->assertFalse( $check, json_encode( $check ) );
	}
}
