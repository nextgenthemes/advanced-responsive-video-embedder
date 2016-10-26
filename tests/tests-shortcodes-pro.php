<?php

class Tests_Shortcode_Pro extends WP_UnitTestCase {

	public function test_thumbnails() {

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

		$this->assertNotContains( 'Error', arve_shortcode_arve( $attr ) );
		$this->assertRegExp( '#<img.*src=".*test-attachment-2\.jpg#', arve_shortcode_arve( $attr ) );

		$attr['thumbnail'] = 'https://example.com/image.jpg';
		$this->assertNotContains( 'Error', arve_shortcode_arve( $attr ) );
		$this->assertRegExp( '#<img .*src="https://example.com/image.jpg"#', arve_shortcode_arve( $attr ) );
	}

	public function test_lazyload() {

		$attr = array(
			'url'       => 'https://www.youtube.com/watch?v=hRonZ4wP8Ys',
			'thumbnail' => 'https://example.com/image.jpg',
			'title'     => 'title test', # to prevent oembed call for title
			'mode'      => 'lazyload',
		);

		$this->assertNotContains( 'Error', arve_shortcode_arve( $attr ) );
		$this->assertContains( 'data-arve-mode="lazyload"', arve_shortcode_arve( $attr ) );
		$this->assertContains( 'data-arve-grow', arve_shortcode_arve( $attr ) );
	}

	public function test_modes() {

		$modes = array( 'lazyload', 'lazyload-lightbox', 'link-lightbox' );
		$attr  = array( 'url' => 'https://www.youtube.com/watch?v=hRonZ4wP8Ys' );

		foreach ( $modes as $mode ) {

			$attr['mode'] = $mode;

			$this->assertNotContains( 'Error', arve_shortcode_arve( $attr ) );
			$this->assertContains( sprintf( 'data-arve-mode="%s"', $mode ), arve_shortcode_arve( $attr ), "mode: $mode" );
		}
	}
}
