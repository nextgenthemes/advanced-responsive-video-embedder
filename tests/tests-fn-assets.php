<?php

declare(strict_types = 1);

use function Nextgenthemes\ARVE\add_async_to_script_modules;
use function Nextgenthemes\ARVE\add_styles_to_mce;

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

		$wp_script_modules = wp_script_modules();

		$reflection          = new ReflectionClass( $wp_script_modules );
		$registered_property = $reflection->getProperty( 'registered' );

		if ( PHP_VERSION_ID < 80100 ) {
			$registered_property->setAccessible( true );
		}

		$registered = $registered_property->getValue( $wp_script_modules );
		$this->assertTrue( isset( $registered['arve'] ) );
	}

	public function test_add_async_to_script_modules_arve(): void {

		$tag    = '<script type="module" src="https://example.com/arve.js"></script>';
		$result = add_async_to_script_modules( $tag, 'arve' );

		$this->assertStringContainsString( 'type="module" async', $result );
	}

	public function test_add_async_to_script_modules_other_handle(): void {

		$tag    = '<script type="module" src="https://example.com/other.js"></script>';
		$result = add_async_to_script_modules( $tag, 'jquery' );

		$this->assertStringNotContainsString( ' async', $result );
	}

	public function test_add_styles_to_mce_returns_string(): void {

		do_action( 'wp_enqueue_scripts' );

		$result = add_styles_to_mce( '' );

		$this->assertIsString( $result );
	}

	public function test_add_styles_to_mce_appends_to_existing(): void {

		do_action( 'wp_enqueue_scripts' );

		$result = add_styles_to_mce( 'existing.css' );

		$this->assertStringStartsWith( 'existing.css', $result );
	}
}
