<?php

declare(strict_types = 1);

use function Nextgenthemes\WP\get_products;

class Tests_AddonsBaseChecks extends WP_UnitTestCase {

	public function product_data(): array {

		foreach ( get_products() as $plugin => $data ) {
			$data[] = [
				'plugin' => $plugin,
				'data'   => $data,
			];
		}

		return $data;
	}
	/**
	 * Tests the integrity and structure of the product data.
	 *
	 * This test verifies that the list of products obtained from the global
	 * settings is not empty and ensures that the detected addons are in an
	 * array format. For each addon, it checks if the corresponding product
	 * entry has a valid file, version, and active status. It also confirms
	 * that the product type is 'plugin'.
	 */
	public function test_product_data(): void {

		$products = get_products();
		$addons   = $GLOBALS['arve_detected_addons'] ?? [];

		$this->assertNotEmpty( $products );
		$this->assertIsArray( $products );

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
