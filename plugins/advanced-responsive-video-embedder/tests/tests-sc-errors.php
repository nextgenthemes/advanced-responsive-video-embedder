<?php
use function \Nextgenthemes\ARVE\shortcode;
use function \Nextgenthemes\ARVE\get_host_properties;

// phpcs:disable Squiz.PHP.CommentedOutCode.Found, Squiz.Classes.ClassFileName.NoMatch, Squiz.PHP.Classes.ValidClassName.NotCamelCaps, WordPress.PHP.DevelopmentFunctions.error_log_print_r, WordPress.PHP.DevelopmentFunctions.error_log_error_log
class Tests_ShortcodeArgValidationErrors extends WP_UnitTestCase {

	public function test_mode_fallback() {

		$html = shortcode(
			array(
				'url'  => 'https://example.com',
				'mode' => 'lazyload',
			)
		);

		$this->markTestSkipped('must be revisited.');
		#$this->assertContains( 'Error', $html );
	}

	public function test_wrong_thumb_id() {

		$html = shortcode(
			array(
				'url'       => 'https://example.com',
				'thumbnail' => '666',
			)
		);
		$this->assertContains( 'Error', $html );
	}

	public function test_wrong_thumb_url() {

		$html = shortcode(
			array(
				'url'       => 'https://example.com',
				'thumbnail' => 'bullshit',
			)
		);
		$this->assertContains( 'Error', $html );
	}

	public function test_wrong_no_req_param() {

		#$this->expectException('Exception');

		$html = shortcode( array( 'bullshit' => 'bullshit' ) );
		$this->assertContains( 'Error', $html );
	}

	public function test_wrong_align() {

		$html = shortcode(
			array(
				'url'   => 'https://example.com',
				'align' => 'bullshit',
			)
		);
		$this->assertContains( 'Error', $html );
	}

	public function test_wrong_ar() {

		$html = shortcode(
			array(
				'url'          => 'https://example.com',
				'aspect_ratio' => '4',
			)
		);
		$this->assertContains( 'Error', $html );
	}

	public function test_legacy_yt_sc_no_id() {

		#$this->expectException('Exception');

		$html = do_shortcode( '[youtube title="testing" /]' );
		$this->assertContains( 'Error', $html );
	}

	public function test_empty_url_and_fatal_error() {

		#$this->expectException('Exception');

		$html = shortcode( array( 'url' => '' ) );
		$this->assertContains( 'Error', $html );

		$html = shortcode( array( 'url' => 'https://example.com' ) );
		$this->assertContains( '<iframe', $html );
	}

	public function test_wrong_url() {

		$html = shortcode( array( 'url' => 'bullshit' ) );
		$this->assertContains( 'Error', $html );
	}

	public function test_wrong_sandbox() {

		$html = shortcode(
			array(
				'url'     => 'example.com',
				'sandbox' => 'bullshit',
			)
		);
		$this->assertContains( 'Error', $html );
	}

	public function test_wrong_src() {

		#$this->expectException('Exception');

		$html = shortcode(
			array(
				'src' => '?dnt=1',
			)
		);
		$this->assertContains( 'Error', $html );
	}

	public function test_wrong_src_mismatch() {

		$html = shortcode(
			array(
				'provider' => 'vimeo',
				'url'      => 'https://vimeo.com/375438048',
				'src'      => '?dnt=1',
			)
		);
		$this->assertContains( 'Error', $html );
		$this->assertContains( '<span class="arve-error" hidden>', $html );
	}
}
