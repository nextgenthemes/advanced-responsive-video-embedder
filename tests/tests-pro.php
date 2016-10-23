<?php

/**
 * @group scripts
 */
class Tests_Pro extends WP_UnitTestCase {

	/**
	 * Test if all the file hooks are working.
	 *
	 * @since 2.3.6
	 */
	public function NO_test_install() {

		activate_plugin( 'arve-pro/arve-pro.php' );
		$this->assertTrue( is_plugin_active( 'arve-pro/arve-pro.php' ) );
		#$this->assertTrue( version_compare( ARVE_PRO_VERSION, ARVE_PRO_VERSION_REQUIRED, '>=' ) );
		deactivate_plugins( 'arve-pro/arve-pro.php' );
		$this->assertFalse( is_plugin_active( 'arve-pro/arve-pro.php' ) );
	}
}
