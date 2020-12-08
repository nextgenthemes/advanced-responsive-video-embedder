<?php
// phpcs:disable Squiz.PHP.CommentedOutCode.Found, Squiz.Classes.ClassFileName.NoMatch, Squiz.PHP.Classes.ValidClassName.NotCamelCaps, WordPress.PHP.DevelopmentFunctions.error_log_print_r, WordPress.PHP.DevelopmentFunctions.error_log_error_log
class Tests_Options extends WP_UnitTestCase {

	public function test_options_v8_to_v9() {

		update_option(
			'arve_options_main',
			[
				'promote_link'   => true,
				'video_maxwidth' => 444,
			]
		);
		update_option(
			'arve_options_params',
			[
				'vimeo' => 'vimeo=123',
			]
		);
		update_option(
			'arve_options_pro',
			[
				'disable_links' => true,
			]
		);

		update_option( 'nextgenthemes_arve_options_ver', '1.0' );
		\Nextgenthemes\ARVE\settings_instance();

		$options = get_option( 'nextgenthemes_arve' );
		$expected = [
			'disable_links'    => true,
			'url_params_vimeo' => 'vimeo=123',
			'maxwidth'         => 444,
			'arve_link'        => true,
		];

		$this->assertEquals(
			'9.0.0-beta8',
			get_option( 'nextgenthemes_arve_options_ver' )
		);

		ksort( $options );
		ksort( $expected );
		$this->assertEquals(
			$expected,
			$options
		);

		// Options should NOT be transfered again
		update_option(
			'arve_options_pro',
			[
				'thumbnail_fallback' => 'https://fallback.test.url',
			]
		);
		update_option(
			'arve_options_main',
			[
				'video_maxwidth' => 666,
			]
		);
		update_option(
			'arve_options_params',
			[
				'youtube' => 'yt=23',
			]
		);

		\Nextgenthemes\ARVE\settings_instance();
		$options2 = get_option( 'nextgenthemes_arve' );

		ksort( $options2 );
		ksort( $expected );
		$this->assertEquals(
			$expected,
			$options
		);

		// Force transfer again
		update_option( 'nextgenthemes_arve_options_ver', '1.0' );
		\Nextgenthemes\ARVE\settings_instance();

		$options3  = get_option( 'nextgenthemes_arve' );
		$expected2 = [
			'url_params_youtube' => 'yt=23',
			'maxwidth'           => 666,
			'thumbnail_fallback' => 'https://fallback.test.url',
		];

		ksort( $options3 );
		ksort( $expected2 );
		$this->assertEquals(
			$expected2,
			$options3
		);
	}

	public function test_init() {
		update_option( 'dddd', 'phpunitoptiontest' );
	}

	public function test_init_check() {

		$this->assertEquals(
			'phpunitoptiontest',
			get_option( 'dddd' )
		);
	}

}
