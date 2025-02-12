<?php

declare(strict_types = 1);

use function Nextgenthemes\ARVE\shortcode;
use function Nextgenthemes\ARVE\get_host_properties;

class Tests_URLParams extends WP_UnitTestCase {

	public function test_vimeo(): void {

		update_option( 'nextgenthemes_arve', array( 'url_params_vimeo' => 'title=0&byline=0&portrait=0' ) );
		$html = shortcode(
			array(
				'url' => 'https://vimeo.com/265932488',
			)
		);

		$this->assertStringContainsString( 'title=0&amp;byline=0&amp;portrait=0', $html );
		$this->assertStringNotContainsString( 'Error', $html );

		$html = shortcode(
			array(
				'url'        => 'https://vimeo.com/265932488',
				'parameters' => 'title=1&byline=1&portrait=0',
			)
		);

		$this->assertStringContainsString( 'title=1&amp;byline=1&amp;portrait=0', $html );
		$this->assertStringNotContainsString( 'Error', $html );
	}
}
