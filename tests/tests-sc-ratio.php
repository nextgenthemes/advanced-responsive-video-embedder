<?php

declare(strict_types = 1);

use function Nextgenthemes\ARVE\shortcode;

class Tests_Ratio extends WP_UnitTestCase {

	public function test_ratio(): void {

		$html = shortcode(
			array(
				'url' => 'https://www.example.com',
			)
		);
		$this->assertStringNotContainsStringIgnoringCase( 'Error', $html );
		$this->assertStringContainsString( 'arve-embed--has-aspect-ratio', $html );
	}

	public function test_ratio_1by1(): void {

		$html = shortcode(
			array(
				'url'          => 'https://www.example.com',
				'aspect_ratio' => '1:1',
			)
		);
		$this->assertStringNotContainsStringIgnoringCase( 'Error', $html );
		$this->assertStringContainsString( 'style="aspect-ratio:1/1', $html );
	}

	public function test_ratio_1by4(): void {

		$html = shortcode(
			array(
				'url'          => 'https://www.example.com',
				'aspect_ratio' => '1:4',
			)
		);
		$this->assertStringNotContainsStringIgnoringCase( 'Error', $html );
		$this->assertStringContainsString( 'style="aspect-ratio:1/4', $html );
	}

	/**
	 * Test the YouTube short ratio function.
	 *
	 * @group yt-shorts
	 */
	public function test_youtube_short_ratio(): void {

		$html = shortcode(
			array(
				'url' => 'https://www.youtube.com/shorts/hgPa4VzuHdY',
			)
		);
		$this->assertStringNotContainsStringIgnoringCase( 'Error', $html );
		$this->assertStringContainsString( 'style="aspect-ratio:9/16', $html );
	}
}
