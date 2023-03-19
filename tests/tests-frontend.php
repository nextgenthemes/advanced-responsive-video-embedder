<?php

use \Nextgenthemes\ARVE;

// phpcs:disable Squiz.Classes.ClassFileName.NoMatch
// phpcs:disable Squiz.Classes.ValidClassName.NotCamelCaps
class Tests_Frontend extends WP_UnitTestCase {

	public function test_global_id_on_html() {
		$this->assertStringContainsString( 'id="html"', get_language_attributes() );
	}
}
