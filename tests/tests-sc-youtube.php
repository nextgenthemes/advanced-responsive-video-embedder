<?php
use function \Nextgenthemes\ARVE\shortcode;
use function \Nextgenthemes\ARVE\get_host_properties;

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

	public function test_yt_time_in_seconds() {

		$html = shortcode(
			[
				'url' => 'https://www.youtube.com/watch?v=--SCDa1zsYI&t=1621',
			]
		);
		$this->assertNotContains( 'Error', $html );
		$this->assertContains( '?start=1621', $html );
	}

	public function test_oembed_recache() {

		shortcode( [ 'url' => 'https://www.youtube.com/watch?v=--SCDa1zsYI&t=1m2s' ] );
		update_option( 'nextgenthemes_arve_oembed_recache', time() );
		$html = shortcode( [ 'url' => 'https://www.youtube.com/watch?v=--SCDa1zsYI&t=1m2s' ] );

		$this->assertNotContains( 'Error', $html );
	}
}
