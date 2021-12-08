<?php
use function \Nextgenthemes\ARVE\shortcode;
use function \Nextgenthemes\ARVE\get_host_properties;

// phpcs:disable Squiz.PHP.CommentedOutCode.Found, Squiz.Classes.ClassFileName.NoMatch, Squiz.PHP.Classes.ValidClassName.NotCamelCaps, WordPress.PHP.DevelopmentFunctions.error_log_print_r, WordPress.PHP.DevelopmentFunctions.error_log_error_log
class Tests_ShortcodeYoutube extends WP_UnitTestCase {

	public function test_yt_time_h_m_s() {

		$html = shortcode(
			array(
				'url' => 'https://www.youtube.com/watch?v=--SCDa1zsYI&t=1h1m2s',
			)
		);
		$this->assertStringNotContainsString( 'Error', $html );
		$this->assertStringContainsString( '?start=3662', $html );
	}

	public function test_yt_time_h_s() {

		$html = shortcode(
			array(
				'url' => 'https://www.youtube.com/watch?v=--SCDa1zsYI&t=1h2s',
			)
		);
		$this->assertStringNotContainsString( 'Error', $html );
		$this->assertStringContainsString( '?start=3602', $html );
	}

	public function test_yt_time_m_s() {

		$html = shortcode(
			array(
				'url' => 'https://www.youtube.com/watch?v=--SCDa1zsYI&t=1m2s',
			)
		);
		$this->assertStringNotContainsString( 'Error', $html );
		$this->assertStringContainsString( '?start=62', $html );
	}

	public function test_yt_time_in_seconds() {

		$html = shortcode(
			array(
				'url' => 'https://www.youtube.com/watch?v=--SCDa1zsYI&t=1621',
			)
		);
		$this->assertStringNotContainsString( 'Error', $html );
		$this->assertStringContainsString( '?start=1621', $html );
	}
}
