<?php

declare(strict_types = 1);

namespace Nextgenthemes\ARVE;

use WP_UnitTestCase;

class Tests_Admin extends WP_UnitTestCase {

	/**
	 * Test that the transient options are deleted when the admin page is accessed.
	 *
	 * @group transients
	 */
	public function test_transient_deletion(): void {

		set_transient( 'ngt_phpunit_test_1_one', '1 one' );
		set_transient( 'ngt_phpunit_test_1_two', '1 two' );
		set_transient( 'ngt_phpunit_test_2_one', '2 one' );
		set_transient( 'ngt_phpunit_test_2_two', '2 two' );
		set_transient( 'ngt_phpunit_test_3_one', '3 one' );
		set_transient( 'ngt_phpunit_test_3_two', '3 two' );

		$this->assertEquals(
			'Deleted 2 transients.',
			delete_transients( 'ngt_phpunit_test_1' ),
			'Failed to delete transients with prefix "ngt_phpunit_test_1".'
		);

		$this->assertEquals(
			'Deleted 1 transients.',
			delete_transients( 'ngt_phpunit_test_2', 'one' ),
			'Failed to delete transients with prefix "ngt_phpunit_test_2" and containing "one".'
		);

		$this->assertEquals(
			'Deleted 2 transients.',
			delete_transients( '', 'two' ),
			'Failed to delete transients with containing "two".'
		);

		if ( version_compare( $GLOBALS['wp_version'], '6.9-beta1', '<' ) ) {

			// This finds TWO transients since >=6.9-beta1 for some reason.
			$this->assertEquals(
				'Deleted 1 transients.',
				delete_transients( '', '' ),
				'Failed to delete transients with no prefix and no containing string.'
			);
		}
	}
}
