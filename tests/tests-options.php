<?php
// phpcs:disable Squiz.PHP.CommentedOutCode.Found, Squiz.Classes.ClassFileName.NoMatch, Squiz.PHP.Classes.ValidClassName.NotCamelCaps, WordPress.PHP.DevelopmentFunctions.error_log_print_r, WordPress.PHP.DevelopmentFunctions.error_log_error_log
class Tests_ARVEProOptions extends WP_UnitTestCase {

	public function test_options_v8_to_v9() {

		update_option(
			'arve_options_main',
			array(
				'promote_link'   => true,
				'video_maxwidth' => '444',
				'align_maxwidth' => 333,
			)
		);
		update_option(
			'arve_options_params',
			array(
				'vimeo' => 'vimeo=123',
			)
		);
		update_option(
			'arve_options_pro',
			array(
				'disable_links' => true,
			)
		);

		update_option( 'nextgenthemes_arve_options_ver', '9.0.0-beta8' );
		\Nextgenthemes\ARVE\settings_instance();

		$options  = get_option( 'nextgenthemes_arve' );
		$expected = array(
			'disable_links'    => true,
			'url_params_vimeo' => 'vimeo=123',
			'maxwidth'         => 444,
			'align_maxwidth'   => 333,
			'arve_link'        => true,
		);

		$this->assertSame(
			'9.0.0-beta9',
			get_option( 'nextgenthemes_arve_options_ver' )
		);

		ksort( $options );
		ksort( $expected );
		$this->assertSame(
			$expected,
			$options
		);

		// Options should NOT be transfered again
		update_option(
			'arve_options_pro',
			array(
				'thumbnail_fallback' => 'https://fallback.test.url',
			)
		);
		update_option(
			'arve_options_main',
			array(
				'video_maxwidth' => '666',
			)
		);
		update_option(
			'arve_options_params',
			array(
				'youtube' => 'yt=23',
			)
		);

		\Nextgenthemes\ARVE\settings_instance();
		$options2 = get_option( 'nextgenthemes_arve' );

		ksort( $options2 );
		ksort( $expected );
		$this->assertSame(
			$expected,
			$options
		);

		// Force transfer again
		update_option( 'nextgenthemes_arve_options_ver', '1.0' );
		\Nextgenthemes\ARVE\settings_instance();

		$options3  = get_option( 'nextgenthemes_arve' );
		$expected2 = array(
			'url_params_youtube' => 'yt=23',
			'maxwidth'           => 666,
			'thumbnail_fallback' => 'https://fallback.test.url',
		);

		ksort( $options3 );
		ksort( $expected2 );
		$this->assertSame(
			$expected2,
			$options3
		);
	}
}
