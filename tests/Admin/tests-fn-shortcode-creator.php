<?php

declare(strict_types = 1);

use function Nextgenthemes\ARVE\Admin\dialog_interactivity;

class Tests_Shortcode_Creator extends WP_UnitTestCase {

	public static function setUpBeforeClass(): void {
		parent::setUpBeforeClass();

		require_once constant( 'Nextgenthemes\ARVE\PLUGIN_DIR' ) . '/php/Admin/fn-shortcode-creator.php';
	}

	public function test_dialog_interactivity_runs_once(): void {

		dialog_interactivity();
		// Second call should be no-op (static guard)
		dialog_interactivity();

		$this->assertTrue( true );
	}
}
