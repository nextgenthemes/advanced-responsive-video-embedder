<?php
// phpcs:disable Squiz.Classes.ClassFileName.NoMatch
// phpcs:disable Squiz.Classes.ValidClassName.NotCamelCaps
class Tests_Scripts_And_Styles extends WP_UnitTestCase {

	public function test_hooks() {

		$this->assertNotFalse( has_action( 'wp_enqueue_scripts', 'arve_register_scripts' ) );
		$this->assertNotFalse( has_action( 'wp_enqueue_scripts', 'arve_register_styles' ) );
	}

	public function test_registered() {

		add_action( 'wp_head', array( __CLASS__, 'registered_helper' ) );
	}

	public function registered_helper() {

		$wp_styles = wp_styles();

		$this->assertStringEndsWith( 'advanced-responsive-video-embedder/dist/css/arve.css', $wp_styles->registered['advanced-responsive-video-embedder']->src );

		$wp_scripts = wp_scripts();

		$this->assertStringEndsWith( 'advanced-responsive-video-embedder/dist/js/arve.js', $wp_scripts->registered['advanced-responsive-video-embedder']->src );
	}
}
