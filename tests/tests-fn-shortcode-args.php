<?php

declare(strict_types = 1);

use function Nextgenthemes\ARVE\get_video_type;
use function Nextgenthemes\ARVE\height_from_width_and_ratio;

class Tests_Shortcode_Args extends WP_UnitTestCase {

	/**
	 * @dataProvider data_get_video_type
	 */
	public function test_get_video_type( string $ext, string $expected ): void {
		$this->assertSame( $expected, get_video_type( $ext ) );
	}

	/**
	 * @return array<int,array{0: string, 1: string}>
	 */
	public function data_get_video_type(): array {
		return [
			[ 'mp4', 'video/mp4' ],
			[ 'webm', 'video/webm' ],
			[ 'ogv', 'video/ogg' ],
			[ 'ogm', 'video/ogg' ],
			[ 'av1mp4', 'video/mp4; codecs=av01.0.05M.08' ],
			[ 'm4v', 'video/x-m4v' ],
			[ 'wmv', 'video/x-wmv' ],
		];
	}

	public function test_height_from_width_and_ratio_valid(): void {
		$this->assertSame( 450.0, height_from_width_and_ratio( 800, '16:9' ) );
	}

	public function test_height_from_width_and_ratio_empty(): void {
		$this->assertSame( 0.0, height_from_width_and_ratio( 800, '' ) );
	}

	public function test_height_from_width_and_ratio_null(): void {
		$this->assertSame( 0.0, height_from_width_and_ratio( 800, null ) );
	}
}
