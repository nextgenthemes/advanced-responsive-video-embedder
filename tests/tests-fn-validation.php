<?php

declare(strict_types = 1);

use function Nextgenthemes\ARVE\validate_thumbnail;
use function Nextgenthemes\ARVE\validate_url;
use function Nextgenthemes\ARVE\validate_type_bool;
use function Nextgenthemes\ARVE\validate_align;
use function Nextgenthemes\ARVE\validate_aspect_ratio;
use function Nextgenthemes\ARVE\validate_height;
use function Nextgenthemes\ARVE\validate_type_int;

class Tests_Validation extends WP_UnitTestCase {

	public function test_validate_thumbnail_empty(): void {
		$this->assertSame( '', validate_thumbnail( '' ) );
	}

	public function test_validate_thumbnail_int(): void {
		$this->assertSame( '42', validate_thumbnail( 42 ) );
	}

	public function test_validate_thumbnail_digit_string(): void {
		$this->assertSame( '123', validate_thumbnail( '123' ) );
	}

	public function test_validate_thumbnail_valid_url(): void {
		$this->assertSame( 'https://example.com/image.jpg', validate_thumbnail( 'https://example.com/image.jpg' ) );
	}

	public function test_validate_thumbnail_invalid(): void {
		$this->assertSame( '', validate_thumbnail( 'not-a-url-or-id' ) );
	}

	public function test_validate_url_empty(): void {
		$this->assertSame( '', validate_url( 'url', '' ) );
	}

	public function test_validate_url_valid(): void {
		$this->assertSame( 'https://example.com', validate_url( 'url', 'https://example.com' ) );
	}

	public function test_validate_url_invalid(): void {
		$this->assertSame( '', validate_url( 'url', 'not-a-url' ) );
	}

	/**
	 * @dataProvider data_validate_type_bool_true
	 */
	public function test_validate_type_bool_true( $value ): void {
		$this->assertTrue( validate_type_bool( 'test', $value ) );
	}

	/**
	 * @dataProvider data_validate_type_bool_false
	 */
	public function test_validate_type_bool_false( $value ): void {
		$this->assertFalse( validate_type_bool( 'test', $value ) );
	}

	/**
	 * @return array<int,array{0: mixed}>
	 */
	public function data_validate_type_bool_true(): array {
		return [
			[ true ],
			[ 'true' ],
			[ '1' ],
			[ 'y' ],
			[ 'yes' ],
			[ 'on' ],
		];
	}

	/**
	 * @return array<int,array{0: mixed}>
	 */
	public function data_validate_type_bool_false(): array {
		return [
			[ false ],
			[ 'false' ],
			[ '0' ],
			[ 'n' ],
			[ 'no' ],
			[ 'off' ],
			[ 'invalid' ],
		];
	}

	public function test_validate_align_empty(): void {
		$this->assertSame( '', validate_align( '' ) );
	}

	public function test_validate_align_none(): void {
		$this->assertSame( '', validate_align( 'none' ) );
	}

	public function test_validate_align_valid(): void {
		$this->assertSame( 'left', validate_align( 'left' ) );
		$this->assertSame( 'right', validate_align( 'right' ) );
		$this->assertSame( 'center', validate_align( 'center' ) );
		$this->assertSame( 'wide', validate_align( 'wide' ) );
		$this->assertSame( 'full', validate_align( 'full' ) );
	}

	public function test_validate_align_invalid(): void {
		$this->assertSame( '', validate_align( 'invalid' ) );
	}

	public function test_validate_aspect_ratio_null(): void {
		$this->assertNull( validate_aspect_ratio( null ) );
	}

	public function test_validate_aspect_ratio_empty(): void {
		$this->assertSame( '', validate_aspect_ratio( '' ) );
	}

	public function test_validate_aspect_ratio_valid(): void {
		$this->assertSame( '16:9', validate_aspect_ratio( '16:9' ) );
	}

	public function test_validate_aspect_ratio_invalid(): void {
		$this->assertSame( '16:9', validate_aspect_ratio( 'invalid' ) );
	}

	public function test_validate_height_numeric(): void {
		$this->assertSame( 480, validate_height( 480 ) );
	}

	public function test_validate_height_non_numeric(): void {
		$this->assertSame( 0, validate_height( 'abc' ) );
	}

	public function test_validate_type_int_int(): void {
		$this->assertSame( 42, validate_type_int( 'test', 42 ) );
	}

	public function test_validate_type_int_digit_string(): void {
		$this->assertSame( 42, validate_type_int( 'test', '42' ) );
	}

	public function test_validate_type_int_invalid(): void {
		$this->assertSame( 0, validate_type_int( 'test', 'abc' ) );
	}
}
