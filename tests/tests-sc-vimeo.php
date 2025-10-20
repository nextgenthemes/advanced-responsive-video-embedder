<?php

declare(strict_types = 1);

use function Nextgenthemes\ARVE\shortcode;

class Tests_ShortcodeVimeo extends WP_UnitTestCase {

	/**
	 * @group vimeo
	 */
	public function test_vimeo_time_and_sandbox(): void {

		$html = shortcode(
			array(
				'url' => 'https://vimeo.com/265932452#t=5',
			)
		);

		$this->assertStringNotContainsString( 'Error', $html );
		$this->assertStringContainsString( 'data-oembed="1"', $html );
		$this->assertStringContainsString( 'referrerpolicy="strict-origin-when-cross-origin"', $html );
		$this->assertMatchesRegularExpression( '@src="https://player.vimeo.com/.*#t=5"@', $html );
		$this->assertStringContainsString( 'allow-forms', $html );
		$this->assertStringContainsString( '"uploadDate":"2018-04-21T21:08:10+00:00"', $html );

		$html = shortcode(
			array(
				'url'         => 'https://vimeo.com/265932452#t=5',
				'upload_date' => '2025-10-20',
			)
		);

		$this->assertStringContainsString( '"uploadDate":"2025-10-20T00:00:00+00:00"', $html );

		// Test with different timezone
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
