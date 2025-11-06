<?php

declare(strict_types = 1);

class Tests_Legacy_Shortcodes extends WP_UnitTestCase {

	public function test_legacy_youtube_shortcode(): void {

		$html = do_shortcode( '[youtube id="p3XU6xQ0KtU" /]' );
		$this->assertStringContainsString( 'data-provider="youtube"', $html );
		$this->assertStringNotContainsStringIgnoringCase( 'Error', $html );
	}

	public function test_legacy_vimeo_shortcode(): void {

		$html = do_shortcode( '[vimeo id="354586612" /]' );
		$this->assertStringContainsString( 'data-provider="vimeo"', $html );
		$this->assertStringNotContainsStringIgnoringCase( 'Error', $html );
	}

	public function test_legacy_iframe_shortcode(): void {

		$html = do_shortcode( '[iframe id="https://example.com" /]' );
		$this->assertStringContainsString( 'data-provider="iframe"', $html );
		$this->assertStringNotContainsStringIgnoringCase( 'Error', $html );
	}
}
