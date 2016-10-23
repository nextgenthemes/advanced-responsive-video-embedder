<?php

/**
 * @group scripts
 */
class Tests_Scripts_Pro extends WP_UnitTestCase {

	/**
	 * Test if all the file hooks are working.
	 *
	 * @since 2.3.6
	 */
	public function test_file_hooks() {

		$this->assertNotFalse( has_action( 'wp_enqueue_scripts', 'arve_pro_action_register_scripts' ) );
	}
}
