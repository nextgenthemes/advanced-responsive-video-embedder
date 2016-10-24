<?php

class Tests_Shortcode_Pro extends WP_UnitTestCase {

	public function test_thumbnails() {

		#$this->assertTrue( is_plugin_active( 'arve-pro/arve-pro.php' ) );
		$this->assertTrue( function_exists( 'arve_init' ) );
		$this->assertTrue( function_exists( 'arve_pro_init' ) );
		$this->assertTrue( function_exists( 'arve_pro_activation_hook' ) );
		$this->assertTrue( function_exists( 'arve_pro_filter_modes' ) );

		global $_where;

		$this->assertContains( 'blubber', $_where );

		$filename = dirname( __FILE__ ) . '/test-attachment-2.jpg';
		$contents = file_get_contents( $filename );

		$upload = wp_upload_bits( basename( $filename ), null, $contents );
		$this->assertTrue( empty( $upload['error'] ) );

		$attachment_id = parent::_make_attachment( $upload );

		$attr = array(
			'url'       => 'https://www.youtube.com/watch?v=hRonZ4wP8Ys',
			'thumbnail' => (string) $attachment_id,
			'mode'      => 'lazyload',
		);

		#$this->assertNotContains( 'Error', arve_shortcode_arve( $attr ) );
		#$this->assertRegExp( '#<img.*src=".*test-attachment-2\.jpg#', arve_shortcode_arve( $attr ) );

		$attr['thumbnail'] = 'https://example.com/image.jpg';
		#$this->assertContains( '<meta itemprop="thumbnailUrl" content="https://example.com/image.jpg"', arve_shortcode_arve( $attr ) );
	}

	public function NO_test_lazyload() {

		$attr = array(
			'url'       => 'https://www.youtube.com/watch?v=hRonZ4wP8Ys',
			'thumbnail' => 'https://example.com/image.jpg',
			'mode'      => 'lazyload',
		);

		$this->assertNotContains( 'Error', arve_shortcode_arve( $attr ) );
		$this->assertContains( 'data-arve-mode="lazyload"', arve_shortcode_arve( $attr ) );
		$this->assertContains( 'data-arve-grow', arve_shortcode_arve( $attr ) );
	}

	public function NO_test_modes() {

		$modes = array( 'lazyload', 'lazyload-lightbox', 'link-lazyload' );
		$atts  = array( 'url' => 'https://www.youtube.com/watch?v=hRonZ4wP8Ys' );

		foreach ( $modes as $key => $mode ) {

			$attr['mode'] = $mode;

			$this->assertNotContains( 'Error', arve_shortcode_arve( $attr ) );
			$this->assertContains( sprintf( 'data-arve-mode="%s"', $mode ), arve_shortcode_arve( $attr ) );
		}

		deactivate_plugins( 'arve-pro/arve-pro.php' );
	}
}
