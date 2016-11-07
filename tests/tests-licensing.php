<?php

class Tests_Licensing_Pro extends WP_UnitTestCase {

	/**
	 * Test if all the file hooks are working.
	 *
	 * @since 2.3.6
	 */
	public function test_key() {

		add_filter( 'nextgenthemes_products', array( $this, 'add_product' ) );

    update_option( 'nextgenthemes_example_product_key', 'key_in_option' );
    $this->assertEquals( nextgenthemes_get_key( 'example_product' ), 'key_in_option' );

    define( 'EXAMPLE_PRODUCT_TWO_KEY', 'defined_key' );
    $this->assertEquals( nextgenthemes_get_key( 'example_product_two' ), 'defined_key' );
	}

	public function add_product( $products ) {

		$products['example_product']     = array();
		$products['example_product_two'] = array();

		return $products;
	}
}
