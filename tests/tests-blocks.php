<?php
use function \Nextgenthemes\ARVE\shortcode;
use function \Nextgenthemes\ARVE\get_host_properties;
use function \Nextgenthemes\ARVE\Common\remote_get_body;

// phpcs:disable Squiz.PHP.CommentedOutCode.Found, Squiz.Classes.ClassFileName.NoMatch, Squiz.PHP.Classes.ValidClassName.NotCamelCaps, WordPress.PHP.DevelopmentFunctions.error_log_print_r, WordPress.PHP.DevelopmentFunctions.error_log_error_log
class Tests_Blocks extends WP_UnitTestCase {

	public function test_class_and_title() {

		$html = do_blocks( '<!-- wp:nextgenthemes/arve-block {"url":"https://example.com","title":"Block Testing Title","mode":"normal","className":"extra class names"} /-->' );

		$this->assertStringNotContainsString( 'Error', $html );
		$this->assertStringContainsString( 'extra class names', $html );
		$this->assertStringContainsString( 'Block Testing Title', $html );
	}
}
