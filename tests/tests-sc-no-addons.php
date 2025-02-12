<?php

declare(strict_types = 1);

use function Nextgenthemes\ARVE\shortcode;

class Tests_NoAddons extends WP_UnitTestCase {

	public function setUp(): void {
		parent::setUp();
		remove_action( 'plugins_loaded', '\Nextgenthemes\ARVE\Pro\init', 15 );
	}

	/**
	 * Tests the fallback behavior of the shortcode when the mode is set to 'lazyload' but ARVE Pro is not active.
	 *
	 * @group no-addons
	 */
	public function test_mode_fallback(): void {

		$html = shortcode(
			array(
				'url'  => 'https://example.com',
				'mode' => 'lazyload',
			)
		);

		$this->assertStringContainsString( 'Error', $html );
		$this->assertStringContainsString( 'lazyload not available', $html );
		$this->assertStringContainsString( 'data-mode="normal"', $html );
	}
}
