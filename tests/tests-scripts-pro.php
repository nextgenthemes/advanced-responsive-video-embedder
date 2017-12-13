<?php

/**
 * @group scripts
 */
class Tests_Scripts_Pro extends WP_UnitTestCase {

	/**
	 * Test if all the file hooks are working.
	 *
	 * @since 2.3.6
	 */
	public function test_hooks() {

		$this->assertNotFalse( has_action( 'wp_enqueue_scripts', 'arve_pro_action_register_styles' ) );
		$this->assertNotFalse( has_action( 'wp_enqueue_scripts', 'arve_pro_action_register_scripts' ) );

	}

	public function test_registered() {

		add_action( 'wp_head', array( __CLASS__, 'arve_pro_test_registered' ) );
	}

	public function arve_pro_test_registered() {

		$wp_styles = wp_styles();

		$this->assertStringEndsWith( 'wp-content/plugins/arve-pro/public/arve-pro.min.css',             $wp_styles->registered['arve-pro']->src );
		$this->assertStringEndsWith( 'wp-content/plugins/arve-pro/node_modules/lity/dist/lity.min.css', $wp_styles->registered['lity']->src );

		$wp_scripts = wp_scripts();

		$this->assertStringEndsWith( 'wp-content/plugins/arve-pro/public/arve-pro.min.js',             $wp_scripts->registered['arve-pro']->src );
		$this->assertStringEndsWith( 'wp-content/plugins/arve-pro/node_modules/lity/dist/lity.min.js', $wp_scripts->registered['lity']->src );
	}
}
