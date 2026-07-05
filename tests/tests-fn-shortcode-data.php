<?php

declare(strict_types = 1);

use function Nextgenthemes\ARVE\url_query_array;
use function Nextgenthemes\ARVE\get_language_name_from_code;

class Tests_Shortcode_Data extends WP_UnitTestCase {

	public function test_url_query_array_empty(): void {
		$this->assertSame( [], url_query_array( 'https://example.com' ) );
	}

	public function test_url_query_array_full(): void {
		$result = url_query_array( 'https://example.com?foo=bar&baz=qux' );
		$this->assertSame( 'bar', $result['foo'] );
		$this->assertSame( 'qux', $result['baz'] );
	}

	public function test_url_query_array_no_url(): void {
		$this->assertSame( [], url_query_array( 'not-a-url' ) );
	}

	/**
	 * @dataProvider data_get_language_name_from_code
	 */
	public function test_get_language_name_from_code( string $code, string $expected ): void {
		$this->assertSame( $expected, get_language_name_from_code( $code ) );
	}

	/**
	 * @return array<int,array{0: string, 1: string}>
	 */
	public function data_get_language_name_from_code(): array {
		return [
			[ 'en', 'English' ],
			[ 'de', 'Deutsch' ],
			[ 'fr', 'Français' ],
			[ 'es', 'Español' ],
			[ 'ja', '日本語' ],
			[ 'ar', 'العربية' ],
		];
	}

	public function test_get_language_name_from_code_unknown(): void {
		$caught = false;

		try {
			get_language_name_from_code( 'zz' );
		} catch ( \Throwable $e ) {
			$caught = true;
		}

		$this->assertTrue( $caught );
	}
}
