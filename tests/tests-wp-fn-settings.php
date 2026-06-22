<?php

declare(strict_types = 1);

use function Nextgenthemes\WP\get_products;
use const Nextgenthemes\ARVE\ADDONS;

class Tests_AddonsBaseChecks extends WP_UnitTestCase {

	public function test_product_data(): void {

		$suite = \NGT_TESTSUITE;

		$this->assertNotEmpty( $suite );
		$this->assertIsString( $suite );

		if ( 'advanced-responsive-video-embedder' === $suite ) {
			return;
		}

		$products = get_products();

		$this->assertNotEmpty( $products );

		if ( 'arve-all' === $suite ) {
			$addons = ADDONS;
		} else {
			$addons = [ $suite => ADDONS[ $suite ] ];
		}

		$this->assertNotEmpty( $addons );

		foreach ( ADDONS as $addon_slug => $addon_data ) {

			$addon_slug = str_replace( '-', '_', $addon_slug );

			$this->assertNotEmpty( $products[ $addon_slug ]['file'], 'file' );
			$this->assertIsString( $products[ $addon_slug ]['file'], 'file' );

			$this->assertNotEmpty( $products[ $addon_slug ]['version'], 'version' );
			$this->assertIsString( $products[ $addon_slug ]['version'], 'version' );

			$this->assertNotEmpty( $products[ $addon_slug ]['active'], 'active' );
			$this->assertTrue( $products[ $addon_slug ]['active'], 'active not true' );

			$this->assertEquals( $products[ $addon_slug ]['type'], 'plugin' );
		}
	}
}
