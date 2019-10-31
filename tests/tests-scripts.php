<?php

use \Nextgenthemes\ARVE;

// phpcs:disable Squiz.Classes.ClassFileName.NoMatch
// phpcs:disable Squiz.Classes.ValidClassName.NotCamelCaps
class Tests_Scripts_And_Styles extends WP_UnitTestCase {

	public function test_hooks() {

		$this->assertNotFalse( has_action( 'wp_enqueue_scripts', 'Nextgenthemes\ARVE\action_wp_enqueue_scripts' ) );
	}

	public function test_scripts() {

		do_action( 'wp_enqueue_scripts' );

		$this->assertTrue( wp_script_is( 'arve-main', 'registered' ) );
		$this->assertTrue( wp_style_is( 'arve-main', 'registered' ) );
	}
}
