<?php
use Nextgenthemes\ARVE;

class Tests_Settings extends WP_UnitTestCase {

	public function test_settings_page(): void {
		$this->assertArrayHasKey( 'maxwidth', ARVE\options() );
	}
}
