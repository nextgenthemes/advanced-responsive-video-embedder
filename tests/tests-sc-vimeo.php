<?php
use function \Nextgenthemes\ARVE\shortcode;
use function \Nextgenthemes\ARVE\get_host_properties;

// phpcs:disable Squiz.PHP.CommentedOutCode.Found, Squiz.Classes.ClassFileName.NoMatch, Squiz.PHP.Classes.ValidClassName.NotCamelCaps, WordPress.PHP.DevelopmentFunctions.error_log_print_r, WordPress.PHP.DevelopmentFunctions.error_log_error_log
class Tests_ShortcodeVimeo extends WP_UnitTestCase {

	public function test_vimeo_time_and_sandbox() {

		$html = shortcode(
			[
				'url' => 'https://vimeo.com/124400795#t=33',
			]
		);

		$this->assertNotContains( 'Error', $html );
		$this->assertRegExp( '@src="https://player.vimeo.com/.*#t=33"@', $html );
		$this->assertContains( 'allow-forms', $html );
	}
}
