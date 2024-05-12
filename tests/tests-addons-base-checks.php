<?php
use function Nextgenthemes\ARVE\shortcode;

// phpcs:disable Squiz.PHP.CommentedOutCode.Found
// phpcs:disable Squiz.Classes.ClassFileName.NoMatch
// phpcs:disable Squiz.PHP.Classes.ValidClassName.NotCamelCaps
class Tests_AddonsBaseChecks extends WP_UnitTestCase {

	public function test_product_data(): void {

		$products = \Nextgenthemes\WP\get_products();
		$addons   = $GLOBALS['arve_detected_addons'] ?? [];

		$this->assertNotEmpty( $products );
		$this->assertTrue( is_array( $addons ) );

		foreach ( $addons as $addon_dirname ) {

			$p = str_replace( '-', '_', $addon_dirname );

			$this->assertNotEmpty( $products[ $p ]['file'], 'file' );
			$this->assertTrue( is_string( $products[ $p ]['file'] ), 'file' );

			$this->assertNotEmpty( $products[ $p ]['version'], 'version' );
			$this->assertTrue( is_string( $products[ $p ]['version'] ), 'version' );

			$this->assertNotEmpty( $products[ $p ]['active'], 'active' );
			$this->assertTrue( $products[ $p ]['active'], 'active not true' );

			$this->assertEquals( $products[ $p ]['type'], 'plugin' );
		}
	}
}
