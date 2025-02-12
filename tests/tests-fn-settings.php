<?php

declare(strict_types = 1);

use function Nextgenthemes\ARVE\options;
use function Nextgenthemes\ARVE\default_options;

class Tests_Settings extends WP_UnitTestCase {

	public function data_options(): array {

		$data[]['options'] = options();
		$data[]['options'] = default_options();

		return $data;
	}

	/**
	 * Asserts that the following keys exist in the options array:
	 *
	 * @group options
	 * @dataProvider data_options
	 */
	public function test_settings_page( array $options ): void {
		$this->assertArrayHasKey( 'maxwidth', $options );
		$this->assertArrayHasKey( 'hide_title', $options );
		$this->assertArrayHasKey( 'align', $options );
		$this->assertArrayHasKey( 'legacy_shortcodes', $options );
	}
}
