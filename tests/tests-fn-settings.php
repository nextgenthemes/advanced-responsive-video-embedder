<?php

declare(strict_types = 1);

use function Nextgenthemes\ARVE\options;
use function Nextgenthemes\ARVE\default_options;

class Tests_Settings extends WP_UnitTestCase {

	public function test_options_is_array(): void {
		$this->assertIsArray( options() );
		$this->assertIsArray( default_options() );
	}

	/**
	 * @dataProvider data_options
	 */
	public function test_options_contain_key( string $key ): void {
		$opts = options();
		$this->assertArrayHasKey( $key, $opts );
	}

	/**
	 * @dataProvider data_options
	 */
	public function test_defaults_contain_key( string $key ): void {
		$defaults = default_options();
		$this->assertArrayHasKey( $key, $defaults );
	}

	/**
	 * @return array<int,array{0: string}>
	 */
	public function data_options(): array {
		return [
			[ 'maxwidth' ],
			[ 'align' ],
			[ 'hide_title' ],
			[ 'legacy_shortcodes' ],
			[ 'autoplay' ],
			[ 'always_enqueue_assets' ],
		];
	}
}
