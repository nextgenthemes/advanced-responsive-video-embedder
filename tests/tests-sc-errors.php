<?php
use function \Nextgenthemes\ARVE\shortcode;
use function \Nextgenthemes\ARVE\get_host_properties;
use function \Nextgenthemes\ARVE\get_settings_instance;

// phpcs:disable Squiz.PHP.CommentedOutCode.Found, Squiz.Classes.ClassFileName.NoMatch, Squiz.PHP.Classes.ValidClassName.NotCamelCaps, WordPress.PHP.DevelopmentFunctions.error_log_print_r, WordPress.PHP.DevelopmentFunctions.error_log_error_log
class Tests_ShortcodeArgValidationErrors extends WP_UnitTestCase {

	public function test_mode_fallback() {

		$html = shortcode(
			[
				'url'  => 'https://example.com',
				'mode' => 'lazyload',
			]
		);
		$this->assertContains( 'Error', $html );
	}

	public function test_wrong_thumb_id() {

		$html = shortcode(
			[
				'url'       => 'https://example.com',
				'thumbnail' => '666',
			]
		);
		$this->assertContains( 'Error', $html );
	}

	public function test_wrong_thumb_url() {

		$html = shortcode(
			[
				'url'       => 'https://example.com',
				'thumbnail' => 'bullshit',
			]
		);
		$this->assertContains( 'Error', $html );
	}

	public function test_wrong_no_req_param() {

		$html = shortcode( [ 'bullshit' => 'bullshit' ] );
		$this->assertContains( 'Error', $html );
	}

	public function test_wrong_align() {

		$html = shortcode(
			[
				'url'   => 'https://example.com',
				'align' => 'bullshit',
			]
		);
		$this->assertContains( 'Error', $html );
	}

	public function test_wrong_ar() {

		$html = shortcode(
			[
				'url'          => 'https://example.com',
				'aspect_ratio' => '4',
			]
		);
		$this->assertContains( 'Error', $html );
	}

	public function test_legacy_yt_sc_no_id() {

		$html = do_shortcode( '[youtube title="testing" /]' );
		$this->assertContains( 'Error', $html );
	}

	public function test_empty_url() {

		$html = shortcode( [ 'url' => '' ] );
		$this->assertContains( 'Error', $html );
	}

	public function test_wrong_url() {

		$html = shortcode( [ 'url' => 'bullshit' ] );
		$this->assertContains( 'Error', $html );
	}

	public function test_wrong_sandbox() {

		$html = shortcode(
			[
				'url'     => 'example.com',
				'sandbox' => 'bullshit',
			]
		);
		$this->assertContains( 'Error', $html );
	}
}