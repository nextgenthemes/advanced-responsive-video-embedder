<?php

/**
 * @group scripts
 */
class Tests_Scripts extends WP_UnitTestCase {

	/**
	 * Test if all the file hooks are working.
	 *
	 * @since 2.3.6
	 */
	public function test_file_hooks() {

		$this->assertNotFalse( has_action( 'wp_enqueue_scripts', 'arve_register_scripts' ) );
	}



}
