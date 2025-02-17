<?php

declare(strict_types = 1);

class Tests_Scripts_And_Styles extends WP_UnitTestCase {

	public function test_hooks(): void {

		$this->assertNotFalse( has_action( 'wp_enqueue_scripts', 'Nextgenthemes\ARVE\action_wp_enqueue_scripts' ) );
	}

	public function test_scripts(): void {

		do_action( 'wp_enqueue_scripts' );

		$this->assertTrue( wp_style_is( 'arve', 'registered' ) );
		$this->assertTrue( wp_script_is( 'arve', 'registered' ) );

		$this->assertStringContainsString(
			'advanced-responsive-video-embedder/build/main.css',
			wp_styles()->registered['arve']->src
		);

		$this->assertStringContainsString(
			'advanced-responsive-video-embedder/build/main.js',
			wp_scripts()->registered['arve']->src
		);
	}
}
