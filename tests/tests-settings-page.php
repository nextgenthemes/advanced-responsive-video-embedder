<?php

use \Nextgenthemes\ARVE;
use \Nextgenthemes\ARVE\Admin;


// phpcs:disable Squiz.Classes.ClassFileName.NoMatch
// phpcs:disable Squiz.Classes.ValidClassName.NotCamelCaps
class Tests_SettingsPage extends WP_UnitTestCase {

	public function test_settings_page() {

		$i = ARVE\settings_instance();

		ob_start();
		$i->print_admin_page();
		$html = ob_get_clean();

		$this->assertContains( 'maxwidth', $html );
	}
}
