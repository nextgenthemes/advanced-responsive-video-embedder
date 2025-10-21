<?php

declare(strict_types = 1);

use function Nextgenthemes\WP\get_products;

class Tests_AddonsBaseChecks extends WP_UnitTestCase {

	public function test_product_data(): void {

		$products = get_products();
		$addons   = $GLOBALS['arve_detected_addons'] ?? [];

		$this->assertNotEmpty( $products );
		$this->assertNotEmpty( $addons );
		$this->assertIsArray( $addons );

		foreach ( $addons as $addon_slug ) {

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
