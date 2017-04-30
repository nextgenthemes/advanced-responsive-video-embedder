<?php

class Tests_Licensing_Pro extends WP_UnitTestCase {

	/**
	 * Test if all the file hooks are working.
	 *
	 * @since 2.3.6
	 */
	public function test_key() {

		apply_filters( 'nextgenthemes_products', array( $this, 'add_product' ) );

	    update_option( 'nextgenthemes_test_key', 'key_in_option' );

	    $this->assertEquals( nextgenthemes_get_key( 'test' ), 'key_in_option' );

	    define( 'TEST_KEY', 'defined_key' );

	    $this->assertEquals( nextgenthemes_get_key( 'test' ), 'defined_key' );
	}


	public function add_product( $products ) {

		$products['test'] = array();

		return $products;
	}
}
