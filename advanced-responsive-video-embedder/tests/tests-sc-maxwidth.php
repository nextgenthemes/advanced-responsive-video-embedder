<?php
use function \Nextgenthemes\ARVE\shortcode;
use function \Nextgenthemes\ARVE\get_host_properties;

// phpcs:disable Squiz.PHP.CommentedOutCode.Found, Squiz.Classes.ClassFileName.NoMatch, Squiz.PHP.Classes.ValidClassName.NotCamelCaps, WordPress.PHP.DevelopmentFunctions.error_log_print_r, WordPress.PHP.DevelopmentFunctions.error_log_error_log
class Tests_ShortcodeMaxWidth extends WP_UnitTestCase {

	public function test_maxwidth_option() {

		update_option( 'nextgenthemes_arve', [ 'maxwidth' => '555' ] );
		$html = shortcode(
			[
				'url' => 'https://example.com',
			]
		);

		$this->assertContains( 'max-width:555px', $html );
		$this->assertNotContains( 'Error', $html );
	}

	public function test_maxwidth_set() {

		$html = shortcode(
			[
				'url'      => 'https://example.com',
				'maxwidth' => '666',
			]
		);
		$this->assertContains( 'max-width:666px', $html );
		$this->assertNotContains( 'Error', $html );
	}

	public function test_align_maxwidth_option() {

		update_option( 'nextgenthemes_arve', [ 'align_maxwidth' => '444' ] );
		$output = shortcode(
			[
				'align' => 'left',
				'url'   => 'https://example.com',
			]
		);
		$this->assertNotContains( 'Error', $output );
		$this->assertContains( 'alignleft', $output );
		$this->assertContains( 'style="max-width:444px;"', $output );
	}

	public function test_align_maxwidth_default() {

		$output = shortcode(
			[
				'align' => 'left',
				'url'   => 'https://example.com',
			]
		);
		$this->assertNotContains( 'Error', $output );
		$this->assertContains( 'alignleft', $output );
		$this->assertContains( 'style="max-width:400px;"', $output );

		$output = shortcode(
			[
				'align' => 'right',
				'url'   => 'https://example.com',
			]
		);
		$this->assertNotContains( 'Error', $output );
		$this->assertContains( 'alignright', $output );
		$this->assertContains( 'style="max-width:400px;"', $output );

		$output = shortcode(
			[
				'align' => 'center',
				'url'   => 'https://example.com',
			]
		);
		$this->assertNotContains( 'Error', $output );
		$this->assertContains( 'aligncenter', $output );
		$this->assertContains( 'style="max-width:400px;"', $output );
	}
}
