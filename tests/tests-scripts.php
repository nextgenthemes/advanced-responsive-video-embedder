<?php
// phpcs:disable Squiz.Classes.ClassFileName.NoMatch
// phpcs:disable Squiz.Classes.ValidClassName.NotCamelCaps
class Tests_Scripts_And_Styles extends WP_UnitTestCase {

	public function test_hooks() {

		$this->assertNotFalse( has_action( 'wp_enqueue_scripts', 'Nextgenthemes\ARVE\register_assets' ) );
	}

	public function test_registered() {

		add_action(
			'wp_head',
			function() {

				$wp_styles  = wp_styles();
				$wp_scripts = wp_scripts();

				$this->assertStringEndsWith(
					'advanced-responsive-video-embedder/dist/css/arve.css',
					$wp_styles->registered['arve-main']->src
				);

				$this->assertStringEndsWith(
					'advanced-responsive-video-embedder/dist/js/arve.js',
					$wp_scripts->registered['arve-main']->src
				);
			}
		);
	}
}
