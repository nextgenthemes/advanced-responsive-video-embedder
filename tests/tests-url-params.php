<?php
use function \Nextgenthemes\ARVE\shortcode;
use function \Nextgenthemes\ARVE\get_host_properties;

// phpcs:disable Squiz.PHP.CommentedOutCode.Found, Squiz.Classes.ClassFileName.NoMatch, Squiz.PHP.Classes.ValidClassName.NotCamelCaps, WordPress.PHP.DevelopmentFunctions.error_log_print_r, WordPress.PHP.DevelopmentFunctions.error_log_error_log
class Tests_URLParams extends WP_UnitTestCase {

	public function test_vimeo() {

		update_option( 'nextgenthemes_arve', [ 'url_params_vimeo' => 'title=0&byline=0&portrait=0' ] );
		$html = shortcode(
			[
				'url' => 'https://vimeo.com/265932488',
			]
		);

		$this->assertContains( 'title=0&amp;byline=0&amp;portrait=0', $html );
		$this->assertNotContains( 'Error', $html );

		$html = shortcode(
			[
				'url'        => 'https://vimeo.com/265932488',
				'parameters' => 'title=1&byline=1&portrait=0',
			]
		);

		$this->assertContains( 'title=1&amp;byline=1&amp;portrait=0', $html );
		$this->assertNotContains( 'Error', $html );
		update_option( 'nextgenthemes_arve', [] );
	}
}
