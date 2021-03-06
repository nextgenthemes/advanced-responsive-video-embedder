<?php
use function \Nextgenthemes\ARVE\shortcode;
use function \Nextgenthemes\ARVE\get_host_properties;

// phpcs:disable Squiz.PHP.CommentedOutCode.Found, Squiz.Classes.ClassFileName.NoMatch, Squiz.PHP.Classes.ValidClassName.NotCamelCaps, WordPress.PHP.DevelopmentFunctions.error_log_print_r, WordPress.PHP.DevelopmentFunctions.error_log_error_log
class Tests_Ratio extends WP_UnitTestCase {

	public function test_ratio() {

		$html = shortcode(
			array(
				'url' => 'https://www.example.com',
			)
		);
		$this->assertNotContains( 'Error', $html );
		$this->assertContains( 'arve-embed--16by9', $html );
	}

	public function test_ratio_1by1() {

		$html = shortcode(
			array(
				'url'          => 'https://www.example.com',
				'aspect_ratio' => '1:1',
			)
		);
		$this->assertNotContains( 'Error', $html );
		$this->assertContains( 'padding-top:100.000000%', $html );
	}

	public function test_ratio_1by3() {

		$html = shortcode(
			array(
				'url'          => 'https://www.example.com',
				'aspect_ratio' => '1:4',
			)
		);
		$this->assertNotContains( 'Error', $html );
		$this->assertContains( 'padding-top:400.000000%', $html );
	}
}
