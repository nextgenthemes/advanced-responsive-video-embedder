<?php
use function \Nextgenthemes\ARVE\shortcode;
use function \Nextgenthemes\ARVE\get_host_properties;

// phpcs:disable Squiz.PHP.CommentedOutCode.Found, Squiz.Classes.ClassFileName.NoMatch, Squiz.PHP.Classes.ValidClassName.NotCamelCaps, WordPress.PHP.DevelopmentFunctions.error_log_print_r, WordPress.PHP.DevelopmentFunctions.error_log_error_log
class Tests_ShortcodeVimeo extends WP_UnitTestCase {

	public function test_vimeo_time_and_sandbox() {

		$html = shortcode(
			array(
				'url' => 'https://vimeo.com/124400795#t=33',
			)
		);

		$this->assertStringNotContainsString( 'Error', $html );
		$this->assertMatchesRegularExpression( '@src="https://player.vimeo.com/.*#t=33"@', $html );
		$this->assertStringContainsString( 'allow-forms', $html );
	}
}
