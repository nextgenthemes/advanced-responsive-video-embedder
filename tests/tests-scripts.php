<?php

/**
 * @group scripts
 */
class Tests_Scripts_And_Styles extends WP_UnitTestCase {

	/**
	 * Test if all the file hooks are working.
	 *
	 * @since 2.3.6
	 */
	public function test_hooks() {

		$this->assertNotFalse( has_action( 'wp_enqueue_scripts', 'arve_register_scripts' ) );
		$this->assertNotFalse( has_action( 'wp_enqueue_scripts', 'arve_register_styles' ) );

	}

	public function test_registered() {

		add_action( '' );

		$wp_styles  = wp_styles();
		$wp_scripts = wp_scripts();

		#$this->assertStringEndsWith(
			#'wp-content/plugins/advanced-responsive-video-embedder/public/arve.min.css',
			#$wp_styles->registered['advanced-responsive-video-embedder']->src
		#);

		$this->assertStringEndsWith(
			'wp-content/plugins/advanced-responsive-video-embedder/public/arve.min.js',
			$wp_scripts->registered['advanced-responsive-video-embedder']->src
		);
	}




}
