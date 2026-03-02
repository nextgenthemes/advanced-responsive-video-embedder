<?php

declare(strict_types = 1);

class Tests_Scripts_And_Styles extends WP_UnitTestCase {

	public function test_hooks(): void {

		$this->assertNotFalse( has_action( 'wp_enqueue_scripts', 'Nextgenthemes\ARVE\action_wp_enqueue_scripts' ) );
	}

	public function test_registered_style(): void {

		do_action( 'wp_enqueue_scripts' );

		$this->assertTrue( wp_style_is( 'arve', 'registered' ) );

		$this->assertStringContainsString(
			'advanced-responsive-video-embedder/build/main.css',
			wp_styles()->registered['arve']->src
		);
	}

	public function test_registered_script_module(): void {

		do_action( 'wp_enqueue_scripts' );

		// Test script module registration
		$wp_script_modules = wp_script_modules();

		debug( $wp_script_modules );

		$reflection          = new ReflectionClass( $wp_script_modules );
		$registered_property = $reflection->getProperty( 'registered' );
		$registered_property->setAccessible( true );
		$registered = $registered_property->getValue( $wp_script_modules );
		$this->assertTrue( isset( $registered['arve'] ) );
	}
}
