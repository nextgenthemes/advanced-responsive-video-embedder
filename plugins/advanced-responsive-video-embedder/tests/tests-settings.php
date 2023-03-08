<?php

use \Nextgenthemes\ARVE;
use \Nextgenthemes\ARVE\Admin;


// phpcs:disable Squiz.Classes.ClassFileName.NoMatch
// phpcs:disable Squiz.Classes.ValidClassName.NotCamelCaps
class Tests_Settings extends WP_UnitTestCase {

	public function test_settings_page() {

		$i       = ARVE\settings_instance();
		$options = $i->get_options();

		$this->assertArrayHasKey( 'maxwidth', $options );
	}
}
