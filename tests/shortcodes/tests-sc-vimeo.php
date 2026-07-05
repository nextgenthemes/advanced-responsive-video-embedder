<?php

declare(strict_types = 1);

use function Nextgenthemes\ARVE\shortcode;

class Tests_ShortcodeVimeo extends WP_UnitTestCase {

	/**
	 * @group vimeo
	 */
	public function test_vimeo_upload_date_override(): void {

		$html = shortcode(
			array(
				'url'         => 'https://vimeo.com/265932452#t=5',
				'upload_date' => '2025-10-20',
			)
		);

		$this->assertStringContainsString( '"uploadDate":"2025-10-20T00:00:00+00:00"', $html );
	}

	/**
	 * @group vimeo
	 */
	public function test_vimeo_upload_date_timezone(): void {

		update_option( 'timezone_string', 'America/New_York' );

		$html = shortcode(
			array(
				'url'         => 'https://vimeo.com/265932452#t=5',
				'upload_date' => '2025-10-20',
			)
		);

		$this->assertStringContainsString( '"uploadDate":"2025-10-20T00:00:00-04:00"', $html );
	}
}
