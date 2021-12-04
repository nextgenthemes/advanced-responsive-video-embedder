<?php
use function Nextgenthemes\ARVE\shortcode;
// phpcs:disable Squiz.PHP.CommentedOutCode.Found
// phpcs:disable Squiz.Classes.ClassFileName.NoMatch
// phpcs:disable Squiz.PHP.Classes.ValidClassName.NotCamelCaps
class Tests_Legacy_Shortcodes extends WP_UnitTestCase {

	public function test_legacy_youtube_shortcode() {

		$html = do_shortcode( '[youtube id="p3XU6xQ0KtU" /]' );
		$this->assertStringContainsString( 'data-provider="youtube"', $html );
		$this->assertStringNotContainsString( 'Error', $html );
	}

	public function test_legacy_vimeo_shortcode() {

		$html = do_shortcode( '[vimeo id="354586612" /]' );
		$this->assertStringContainsString( 'data-provider="vimeo"', $html );
		$this->assertStringNotContainsString( 'Error', $html );
	}

	public function test_legacy_iframe_shortcode() {

		$html = do_shortcode( '[iframe id="https://example.com" /]' );
		$this->assertStringContainsString( 'data-provider="iframe"', $html );
		$this->assertStringNotContainsString( 'Error', $html );
	}
}
