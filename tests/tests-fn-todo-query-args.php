<?php

declare(strict_types = 1);

use function Nextgenthemes\ARVE\query_args;
use function Nextgenthemes\ARVE\youtube_query_args;
use function Nextgenthemes\ARVE\vimeo_query_args;
use function Nextgenthemes\ARVE\dailymotion_query_args;

class Tests_Query_Args extends WP_UnitTestCase {

	public static function setUpBeforeClass(): void {
		parent::setUpBeforeClass();

		require_once constant( 'Nextgenthemes\ARVE\PLUGIN_DIR' ) . '/php/fn-todo-query-args.php';
	}

	public function test_query_args_has_providers(): void {
		$args = query_args();
		$this->assertArrayHasKey( 'youtube', $args );
		$this->assertArrayHasKey( 'vimeo', $args );
		$this->assertArrayHasKey( 'dailymotion', $args );
	}

	public function test_youtube_query_args_structure(): void {
		$args = youtube_query_args();
		$this->assertNotEmpty( $args );

		foreach ( $args as $arg ) {
			$this->assertArrayHasKey( 'attr', $arg );
			$this->assertArrayHasKey( 'type', $arg );
			$this->assertArrayHasKey( 'name', $arg );
			$this->assertArrayHasKey( 'description', $arg );
		}
	}

	public function test_youtube_query_args_has_expected(): void {
		$args   = youtube_query_args();
		$attrs  = array_column( $args, 'attr' );
		$expect = [ 'autoplay', 'controls', 'loop', 'rel', 'start' ];

		foreach ( $expect as $attr ) {
			$this->assertContains( $attr, $attrs, "YouTube query arg '$attr' not found" );
		}
	}

	public function test_vimeo_query_args_structure(): void {
		$args = vimeo_query_args();

		foreach ( $args as $arg ) {
			$this->assertArrayHasKey( 'attr', $arg );
			$this->assertArrayHasKey( 'type', $arg );
			$this->assertArrayHasKey( 'name', $arg );
		}
	}

	public function test_vimeo_query_args_has_expected(): void {
		$args   = vimeo_query_args();
		$attrs  = array_column( $args, 'attr' );
		$expect = [ 'autoplay', 'byline', 'title', 'portrait', 'color', 'loop' ];

		foreach ( $expect as $attr ) {
			$this->assertContains( $attr, $attrs, "Vimeo query arg '$attr' not found" );
		}
	}

	public function test_dailymotion_query_args_structure(): void {
		$args = dailymotion_query_args();

		foreach ( $args as $arg ) {
			$this->assertArrayHasKey( 'attr', $arg );
			$this->assertArrayHasKey( 'type', $arg );
			$this->assertArrayHasKey( 'name', $arg );
		}
	}

	public function test_dailymotion_query_args_has_expected(): void {
		$args   = dailymotion_query_args();
		$attrs  = array_column( $args, 'attr' );
		$expect = [ 'autoplay', 'highlight', 'info', 'logo', 'related', 'start' ];

		foreach ( $expect as $attr ) {
			$this->assertContains( $attr, $attrs, "Dailymotion query arg '$attr' not found" );
		}
	}
}
