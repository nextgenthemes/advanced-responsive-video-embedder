<?php
use function \Nextgenthemes\ARVE\shortcode;
use function \Nextgenthemes\ARVE\get_host_properties;

// phpcs:disable Squiz.PHP.CommentedOutCode.Found, Squiz.Classes.ClassFileName.NoMatch, Squiz.PHP.Classes.ValidClassName.NotCamelCaps, WordPress.PHP.DevelopmentFunctions.error_log_print_r, WordPress.PHP.DevelopmentFunctions.error_log_error_log
class Tests_ShortcodeArgs extends WP_UnitTestCase {

	public function test_yt_args() {

		$html = shortcode(
			array(
				'url' => 'https://www.youtube.com/watch?v=--SCDa1zsYI',
				'title' => '-TITLE-',
				'description' => '-DESCRIPTION-',
				'align' => 'center',
			)
		);
		$this->assertNotContains( 'Error', $html );
		$this->assertContains( 'data-oembed="1"', $html );
		$this->assertContains( '-TITLE-', $html );
		$this->assertContains( '-DESCRIPTION-', $html );
		$this->assertContains( 'aligncenter', $html );

		$html = shortcode(
			array(
				'url' => 'https://www.youtube.com/watch?v=--SCDa1zsYI',
				'title' => '-TITLE-',
				'description' => '-DESCRIPTION-',
				'align' => 'center',
			)
		);
		$this->assertNotContains( 'Error', $html );
		$this->assertContains( 'data-oembed="1"', $html );
		$this->assertContains( '-TITLE-', $html );
		$this->assertContains( '-DESCRIPTION-', $html );
		$this->assertContains( 'aligncenter', $html );
	}
}
