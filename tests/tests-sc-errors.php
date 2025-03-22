<?php

declare(strict_types = 1);

use function Nextgenthemes\ARVE\shortcode;
use function Nextgenthemes\ARVE\build_video;

class Tests_ShortcodeArgValidationErrors extends WP_UnitTestCase {

	public function test_wrong_thumb_id(): void {

		$html = shortcode(
			array(
				'url'       => 'https://example.com',
				'thumbnail' => '666',
			)
		);
		$this->assertStringContainsString( 'Error', $html );
	}

	public function test_wrong_thumb_url(): void {

		$html = shortcode(
			array(
				'url'       => 'https://example.com',
				'thumbnail' => 'bullshit',
			)
		);
		$this->assertStringContainsString( 'Error', $html );
	}

	public function test_wrong_no_req_param(): void {

		#$this->expectException('Exception');

		$html = shortcode( array( 'bullshit' => 'bullshit' ) );
		$this->assertStringContainsString( 'Error', $html );
	}

	public function test_wrong_align(): void {

		$html = shortcode(
			array(
				'url'   => 'https://example.com',
				'align' => 'bullshit',
			)
		);
		$this->assertStringContainsString( 'Error', $html );
	}

	public function test_wrong_ar(): void {

		$html = shortcode(
			array(
				'url'          => 'https://example.com',
				'aspect_ratio' => '4',
			)
		);
		$this->assertStringContainsString( 'Error', $html );
	}

	public function test_legacy_yt_sc_no_id(): void {

		#$this->expectException('Exception');

		$html = do_shortcode( '[youtube title="testing" /]' );
		$this->assertStringContainsString( 'Error', $html );
	}

	public function test_empty_url(): void {
		$html = shortcode( array( 'url' => '' ) );
		$this->assertStringContainsString( 'Error', $html );
	}

	public function test_unknown_url(): void {
		$html = shortcode( array( 'url' => 'https://example.com' ) );
		$this->assertStringContainsString( '<iframe', $html );
	}

	public function test_wrong_url(): void {

		$html = shortcode( array( 'url' => 'bullshit' ) );
		$this->assertStringContainsString( 'Error', $html );
	}

	public function test_wrong_encrypted_media(): void {

		$html = shortcode(
			array(
				'url'             => 'example.com',
				'encrypted_media' => 'bullshit',
			)
		);
		$this->assertStringContainsString( 'Error', $html );
	}

	public function test_wrong_src(): void {

		#$this->expectException('Exception');

		$html = shortcode(
			array(
				'src' => '?dnt=1',
			)
		);
		$this->assertStringContainsString( 'Error', $html );
	}

	/**
	 * @group oembed-data-trigger
	 */
	public function TODO_test_oembed_data_error_trigger(): void {

		$od = new StdClass();

		$od->provider_name = 'Unknown';
		$od->test_error    = 'err-testing-str';
		$od->other_error   = 'another-error-str';

		$html = build_video(
			[
				'url'         => 'http://example.com',
				'oembed_data' => $od,
			]
		);

		$this->assertStringContainsString( 'Error', $html );
		$this->assertStringContainsString( 'err-testing-str', $html );
		$this->assertStringContainsString( 'another-error-str', $html );
	}
}
