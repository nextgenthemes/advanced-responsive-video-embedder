<?php

declare(strict_types = 1);

use function Nextgenthemes\ARVE\youtube_time_to_seconds;
use function Nextgenthemes\ARVE\aspect_ratio_gcd;
use function Nextgenthemes\ARVE\gcd;
use function Nextgenthemes\ARVE\aspect_ratio_to_percentage;
use function Nextgenthemes\ARVE\new_height;
use function Nextgenthemes\ARVE\is_wp_error_array;
use function Nextgenthemes\ARVE\is_valid_date_time;
use function Nextgenthemes\ARVE\has_timezone;
use function Nextgenthemes\ARVE\normalize_datetime_to_atom;
use function Nextgenthemes\ARVE\seconds_to_iso8601_duration;
use function Nextgenthemes\ARVE\is_card;

class Tests_Misc extends WP_UnitTestCase {

	/**
	 * @dataProvider data_youtube_time_to_seconds
	 */
	public function test_youtube_time_to_seconds( string $input, int $expected ): void {
		$this->assertSame( $expected, youtube_time_to_seconds( $input ) );
	}

	/**
	 * @return array<int,array{0: string, 1: int}>
	 */
	public function data_youtube_time_to_seconds(): array {
		return [
			[ '123', 123 ],
			[ '1h25m13s', 5113 ],
			[ '1h', 3600 ],
			[ '30s', 30 ],
			[ '5m', 300 ],
			[ '1h30m', 5400 ],
			[ '1m30s', 90 ],
			[ '0s', 0 ],
		];
	}

	/**
	 * @dataProvider data_aspect_ratio_gcd
	 */
	public function test_aspect_ratio_gcd( string $input, string $expected ): void {
		$this->assertSame( $expected, aspect_ratio_gcd( $input ) );
	}

	/**
	 * @return array<int,array{0: string, 1: string}>
	 */
	public function data_aspect_ratio_gcd(): array {
		return [
			[ '16:9', '16:9' ],
			[ '4:3', '4:3' ],
			[ '1920:1080', '16:9' ],
			[ '100:75', '4:3' ],
			[ '1:1', '1:1' ],
			[ '16.5:9', '16.5:9' ],
		];
	}

	public function test_gcd(): void {
		$this->assertSame( 1, gcd( 16, 9 ) );
		$this->assertSame( 120, gcd( 1920, 1080 ) );
		$this->assertSame( 25, gcd( 100, 75 ) );
		$this->assertSame( 7, gcd( 7, 0 ) );
	}

	public function test_aspect_ratio_to_percentage(): void {
		$this->assertSame( 56.25, aspect_ratio_to_percentage( '16:9' ) );
		$this->assertSame( 75.0, aspect_ratio_to_percentage( '4:3' ) );
		$this->assertSame( 100.0, aspect_ratio_to_percentage( '1:1' ) );
	}

	public function test_new_height(): void {
		$this->assertSame( 450.0, new_height( 16, 9, 800 ) );
		$this->assertSame( 300.0, new_height( 4, 3, 400 ) );
	}

	public function test_is_wp_error_array_true(): void {
		$this->assertTrue( is_wp_error_array( [ 'code' => 'error', 'message' => 'test' ] ) );
	}

	/**
	 * @dataProvider data_is_wp_error_array_false
	 */
	public function test_is_wp_error_array_false( $data ): void {
		$this->assertFalse( is_wp_error_array( $data ) );
	}

	/**
	 * @return array<int,array{0: mixed}>
	 */
	public function data_is_wp_error_array_false(): array {
		return [
			[ 'string' ],
			[ 42 ],
			[ [ 'code' => 'only-code' ] ],
			[ [ 'message' => 'only-msg' ] ],
			[ [] ],
		];
	}

	public function test_is_valid_date_time_valid(): void {
		$this->assertTrue( is_valid_date_time( '2025-10-20' ) );
		$this->assertTrue( is_valid_date_time( '2025-10-20T12:00:00+00:00' ) );
	}

	public function test_is_valid_date_time_invalid(): void {
		$this->assertFalse( is_valid_date_time( 'not-a-date' ) );
	}

	public function test_has_timezone_zulu(): void {
		$this->assertTrue( has_timezone( '2025-10-20T00:00:00Z' ) );
	}

	public function test_has_timezone_offset(): void {
		$this->assertTrue( has_timezone( '2025-10-20T00:00:00+05:00' ) );
		$this->assertTrue( has_timezone( '2025-10-20T00:00:00-0400' ) );
	}

	public function test_has_timezone_none(): void {
		$this->assertFalse( has_timezone( '2025-10-20' ) );
	}

	public function test_normalize_datetime_to_atom_with_tz(): void {
		$this->assertSame(
			'2025-10-20T12:00:00+00:00',
			normalize_datetime_to_atom( '2025-10-20T12:00:00+00:00', 'UTC' )
		);
	}

	public function test_normalize_datetime_to_atom_utc_fallback(): void {
		$this->assertSame(
			'2025-10-20T00:00:00+00:00',
			normalize_datetime_to_atom( '2025-10-20', 'UTC' )
		);
	}

	public function test_normalize_datetime_to_atom_invalid(): void {
		$this->assertSame( 'not-a-date', normalize_datetime_to_atom( 'not-a-date', 'UTC' ) );
	}

	public function test_seconds_to_iso8601_duration(): void {
		$this->assertSame( 'PT1H1M1S', seconds_to_iso8601_duration( 3661 ) );
		$this->assertSame( 'PT1H', seconds_to_iso8601_duration( 3600 ) );
		$this->assertSame( 'PT1M30S', seconds_to_iso8601_duration( 90 ) );
		$this->assertSame( 'P', seconds_to_iso8601_duration( 0 ) );
		$this->assertSame( 'PT30S', seconds_to_iso8601_duration( 30 ) );
	}

	public function test_is_card_lazyload_card(): void {
		$this->assertTrue( is_card( [ 'mode' => 'lazyload', 'lazyload_style' => 'card' ] ) );
	}

	public function test_is_card_lightbox_card(): void {
		$this->assertTrue( is_card( [ 'mode' => 'lightbox', 'lazyload_style' => 'card' ] ) );
	}

	public function test_is_card_normal_mode(): void {
		$this->assertFalse( is_card( [ 'mode' => 'normal', 'lazyload_style' => 'card' ] ) );
	}

	public function test_is_card_non_card_style(): void {
		$this->assertFalse( is_card( [ 'mode' => 'lazyload', 'lazyload_style' => 'basic' ] ) );
	}
}
