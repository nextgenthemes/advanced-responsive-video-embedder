<?php
use function \Nextgenthemes\ARVE\shortcode;
use function \Nextgenthemes\ARVE\get_host_properties;

// phpcs:disable Squiz.PHP.CommentedOutCode.Found, Squiz.Classes.ClassFileName.NoMatch, Squiz.PHP.Classes.ValidClassName.NotCamelCaps, WordPress.PHP.DevelopmentFunctions.error_log_print_r, WordPress.PHP.DevelopmentFunctions.error_log_error_log
class Tests_ShortcodeMaxWidth extends WP_UnitTestCase {


	// TODO these tests fail since tehre is no maxwidth with Gutenberd anymore. Maybe install Classic Editor for tests and enabled again.

	// public function test_maxwidth_option() {

	// 	update_option( 'nextgenthemes_arve', array( 'maxwidth' => '555' ) );
	// 	$html = shortcode(
	// 		array(
	// 			'url' => 'https://example.com',
	// 		)
	// 	);

	// 	$this->assertStringContainsString( 'max-width:555px', $html );
	// 	$this->assertStringNotContainsString( 'Error', $html );
	// }

	// public function test_maxwidth_set() {

	// 	$html = shortcode(
	// 		array(
	// 			'url'      => 'https://example.com',
	// 			'maxwidth' => '666',
	// 		)
	// 	);
	// 	$this->assertStringContainsString( 'max-width:666px', $html );
	// 	$this->assertStringNotContainsString( 'Error', $html );
	// }

	public function test_align_maxwidth_option() {

		update_option( 'nextgenthemes_arve', array( 'align_maxwidth' => '444' ) );
		$output = shortcode(
			array(
				'align' => 'left',
				'url'   => 'https://example.com',
			)
		);
		$this->assertStringNotContainsString( 'Error', $output );
		$this->assertStringContainsString( 'alignleft', $output );
		$this->assertStringContainsString( 'style="max-width:444px;"', $output );
	}

	public function test_align_maxwidth_default() {

		$output = shortcode(
			array(
				'align' => 'left',
				'url'   => 'https://example.com',
			)
		);
		$this->assertStringNotContainsString( 'Error', $output );
		$this->assertStringContainsString( 'alignleft', $output );
		$this->assertStringContainsString( 'style="max-width:400px;"', $output );

		$output = shortcode(
			array(
				'align' => 'right',
				'url'   => 'https://example.com',
			)
		);
		$this->assertStringNotContainsString( 'Error', $output );
		$this->assertStringContainsString( 'alignright', $output );
		$this->assertStringContainsString( 'style="max-width:400px;"', $output );

		$output = shortcode(
			array(
				'align' => 'center',
				'url'   => 'https://example.com',
			)
		);
		$this->assertStringNotContainsString( 'Error', $output );
		$this->assertStringContainsString( 'aligncenter', $output );
		$this->assertStringContainsString( 'style="max-width:400px;"', $output );
	}
}
