<?php

declare(strict_types = 1);

use function Nextgenthemes\ARVE\Admin\plugin_ver_status;

class Tests_Debug_Info extends WP_UnitTestCase {

	public static function setUpBeforeClass(): void {
		parent::setUpBeforeClass();

		require_once constant( 'Nextgenthemes\ARVE\PLUGIN_DIR' ) . '/php/Admin/fn-debug-info.php';
	}

	public function test_plugin_ver_status_not_installed(): void {
		$this->assertSame(
			'NOT INSTALLED',
			plugin_ver_status( 'nonexistent-plugin/nonexistent.php' )
		);
	}
}
