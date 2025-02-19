<?php

declare(strict_types = 1);

use function Nextgenthemes\WP\register_asset;

class Tests_Asset extends WP_UnitTestCase {

	/**
	 * @group asset
	 */
	public function test_register_asset_with_wrong_version(): void {

		try {
			register_asset(
				[
					'handle' => 'test',
					'src'    => 'https://example.org/script.js',
					'ver'    => true,
				]
			);
		} catch ( Throwable $e ) {
			$this->assertEquals( 'Nextgenthemes\WP\Asset::validate_ver(): Wrong version arg', $e->getMessage() );
		}
	}

	/**
	 * @group asset
	 */
	public function test_register_asset_with_wrong_inline_script(): void {

		try {
			register_asset(
				[
					'handle'               => 'test',
					'src'                  => 'https://example.org/script.js',
					'inline_script_before' => false,
				]
			);
		} catch ( Throwable $e ) {
			$this->assertEquals( 'Nextgenthemes\WP\Asset::validate_inline_script(): Wrong inline_script_xxxxx type', wp_strip_all_tags( $e->getMessage() ) );
		}
	}

	/**
	 * @group asset
	 */
	public function test_register_asset_with_inline_script(): void {

		register_asset(
			[
				'handle'               => 'handle-before',
				'src'                  => 'https://example.org/script.js',
				'inline_script_before' => 'console.log("b4");',
			]
		);

		register_asset(
			[
				'handle'               => 'handle-after',
				'src'                  => 'https://example.org/script.js',
				'inline_script_after'  => 'console.log("after");',
			]
		);

		$this->assertStringContainsString(
			'console.log("b4");',
			wp_scripts()->registered['handle-before']->extra['before'][1],
		);

		$this->assertStringContainsString(
			'console.log("after");',
			wp_scripts()->registered['handle-after']->extra['after'][1],
		);
	}

	/**
	 * @group asset
	 */
	public function test_register_asset_with_bullshit(): void {

		try {
			register_asset(
				[
					'bullshit' => 'bullshit',
					'src'      => 'https://example.org/script.js',
				]
			);
		} catch ( Throwable $e ) {
			$this->assertEquals( 'Nextgenthemes\WP\Asset::__construct(): Trying to set property bullshit, but it does not exist', wp_strip_all_tags( $e->getMessage() ) );
		}
	}
}
