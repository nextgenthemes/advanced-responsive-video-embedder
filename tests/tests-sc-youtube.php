<?php
use function Nextgenthemes\ARVE\shortcode;
use function Nextgenthemes\ARVE\get_host_properties;

// phpcs:disable Squiz.PHP.CommentedOutCode.Found, Squiz.Classes.ClassFileName.NoMatch, Squiz.PHP.Classes.ValidClassName.NotCamelCaps, WordPress.PHP.DevelopmentFunctions.error_log_print_r, WordPress.PHP.DevelopmentFunctions.error_log_error_log
class Tests_ShortcodeYoutube extends WP_UnitTestCase {

	/**
	 * @group yt-time
	 */
	public function test_yt_time_h_m_s(): void {

		$html = shortcode(
			array(
				'url' => 'https://www.youtube.com/watch?v=--SCDa1zsYI&t=1h1m2s',
			)
		);
		$this->assertStringNotContainsString( 'Error', $html );
		$this->assertStringContainsString( '?start=3662', $html );
		$this->assertStringContainsString( 'referrerpolicy="strict-origin-when-cross-origin"', $html );
	}

	/**
	 * @group yt-time
	 */
	public function test_yt_time_h_s(): void {

		$html = shortcode(
			array(
				'url' => 'https://www.youtube.com/watch?v=--SCDa1zsYI&t=1h2s',
			)
		);
		$this->assertStringNotContainsString( 'Error', $html );
		$this->assertStringContainsString( '?start=3602', $html );
		$this->assertStringContainsString( 'referrerpolicy="strict-origin-when-cross-origin"', $html );
	}

	/**
	 * @group yt-time
	 */
	public function test_yt_time_m_s(): void {

		$html = shortcode(
			array(
				'url' => 'https://www.youtube.com/watch?v=--SCDa1zsYI&t=1m2s',
			)
		);
		$this->assertStringNotContainsString( 'Error', $html );
		$this->assertStringContainsString( '?start=62', $html );
		$this->assertStringContainsString( 'referrerpolicy="strict-origin-when-cross-origin"', $html );
	}

	/**
	 * @group yt-time
	 */
	public function test_yt_time_in_seconds(): void {

		$html = shortcode(
			array(
				'url' => 'https://www.youtube.com/watch?v=--SCDa1zsYI&t=1621',
			)
		);

		$this->assertStringNotContainsString( 'Error', $html );
		$this->assertStringContainsString( '?start=1621', $html );
		$this->assertStringContainsString( 'referrerpolicy="strict-origin-when-cross-origin"', $html );
	}

	/**
	 * A description of the entire PHP function.
	 *
	 * @group yt-loop
	 */
	public function test_youtube_loop(): void {

		$html = shortcode(
			array(
				'url'  => 'https://www.youtube.com/watch?v=5R0LrCfXQjQ',
				'loop' => 'yes',
			)
		);

		$this->assertStringContainsString( 'loop=1', $html );
		$this->assertStringContainsString( 'playlist=5R0LrCfXQjQ', $html );
		$this->assertStringNotContainsString( 'Error', $html );

		$html = shortcode(
			array(
				'url'  => 'https://www.youtube.com/watch?list=PLMUvgtCRyn-6obmhiDS4n5vYQN3bJRduk', // buggy url actually
				'loop' => 'yes',
			)
		);

		$this->assertStringContainsString( 'loop=1', $html );
		$this->assertStringContainsString( '/videoseries', $html );
		$this->assertStringNOTContainsString( 'playlist=', $html );
		$this->assertStringNotContainsString( 'Error', $html );
		$this->assertStringContainsString( 'referrerpolicy="strict-origin-when-cross-origin"', $html );
	}
}
