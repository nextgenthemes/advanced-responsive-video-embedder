<?php
use function \Nextgenthemes\ARVE\shortcode;
use function \Nextgenthemes\ARVE\get_host_properties;
use function \Nextgenthemes\ARVE\get_settings_instance;

// phpcs:disable Squiz.PHP.CommentedOutCode.Found, Squiz.Classes.ClassFileName.NoMatch, Squiz.PHP.Classes.ValidClassName.NotCamelCaps, WordPress.PHP.DevelopmentFunctions.error_log_print_r, WordPress.PHP.DevelopmentFunctions.error_log_error_log
class Tests_ShortcodeYoutube extends WP_UnitTestCase {

	public function test_yt_time_h_m_s() {

		$html = shortcode(
			[
				'url' => 'https://www.youtube.com/watch?v=--SCDa1zsYI&t=1h1m2s',
			]
		);
		$this->assertNotContains( 'Error', $html );
		$this->assertContains( '?start=3662', $html );
	}

	public function test_yt_time_h_s() {

		$html = shortcode(
			[
				'url' => 'https://www.youtube.com/watch?v=--SCDa1zsYI&t=1h2s',
			]
		);
		$this->assertNotContains( 'Error', $html );
		$this->assertContains( '?start=3602', $html );
	}

	public function test_yt_time_m_s() {

		$html = shortcode(
			[
				'url' => 'https://www.youtube.com/watch?v=--SCDa1zsYI&t=1m2s',
			]
		);
		$this->assertNotContains( 'Error', $html );
		$this->assertContains( '?start=62', $html );
	}
}
