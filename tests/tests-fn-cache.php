<?php

declare(strict_types = 1);

use function Nextgenthemes\ARVE\delete_transients;

class Tests_Cache extends WP_UnitTestCase {

	public function test_delete_transients_no_match(): void {

		$result = delete_transients( 'nonexistent_prefix_xyz' );

		$this->assertSame( 'No transients deleted.', $result );
	}

	public function test_delete_transients_by_prefix(): void {

		set_transient( 'ngt_cache_test_a_one', 'a one' );
		set_transient( 'ngt_cache_test_a_two', 'a two' );
		set_transient( 'ngt_cache_test_b_one', 'b one' );

		$result = delete_transients( 'ngt_cache_test_a' );

		$this->assertSame( 'Deleted 2 transients.', $result );
		$this->assertFalse( get_transient( 'ngt_cache_test_a_one' ) );
		$this->assertFalse( get_transient( 'ngt_cache_test_a_two' ) );
		$this->assertSame( 'b one', get_transient( 'ngt_cache_test_b_one' ) );
	}

	public function test_delete_transients_by_prefix_and_contains(): void {

		set_transient( 'ngt_cache_test_c_one', 'apple' );
		set_transient( 'ngt_cache_test_c_two', 'banana' );
		set_transient( 'ngt_cache_test_c_three', 'apple pie' );

		$result = delete_transients( 'ngt_cache_test_c', 'apple' );

		$this->assertSame( 'Deleted 2 transients.', $result );
	}

	public function test_delete_transients_by_contains_only(): void {

		set_transient( 'ngt_cache_test_d_one', 'unique_val_x' );
		set_transient( 'ngt_cache_test_d_two', 'other' );

		$result = delete_transients( '', 'unique_val_x' );

		$this->assertSame( 'Deleted 1 transients.', $result );
		$this->assertFalse( get_transient( 'ngt_cache_test_d_one' ) );
		$this->assertSame( 'other', get_transient( 'ngt_cache_test_d_two' ) );
	}
}
