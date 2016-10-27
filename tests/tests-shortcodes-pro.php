<?php

class Tests_Shortcode_Pro extends WP_UnitTestCase {

	public function test_oembed_thumbnail_and_title() {

		$properties = arve_get_host_properties();

		remove_filter( 'shortcode_atts_arve',    'arve_pro_filter_atts_img_src', 8 );
		remove_filter( 'shortcode_atts_arve',    'arve_pro_filter_atts_img_src_srcset', 9 );

		foreach ( $properties as $provider => $props ) :

			if ( empty( $values['tests'] ) ) {
				continue;
			}

			foreach ( $values['tests'] as $key => $test ) {

				$attr = array(
					'url'  => $test['url'],
					'mode' => 'lazyload',
				);

				#if( isset( $props['auto_title'] ) && $props['auto_title'] ) {
				if( isset( $test['oembed_title'] ) ) {
					$this->assertNotContains( 'Error', arve_shortcode_arve( $attr ) );
					$this->assertContains( '<h5 itemprop="name" class="arve-title">' . $test['oembed_title'] . '</h5>', arve_shortcode_arve( $attr ) );
				}
				if( isset( $test['oembed_img'] ) ) {
					$this->assertNotContains( 'Error', arve_shortcode_arve( $attr ) );
					$this->assertRegex( '#<img [^>]*src="' . $test['oembed_img'] . '#', arve_shortcode_arve( $attr ) );
				}
			}
		endforeach;

		add_filter( 'shortcode_atts_arve',    'arve_pro_filter_atts_img_src', 8 );
		add_filter( 'shortcode_atts_arve',    'arve_pro_filter_atts_img_src_srcset', 9 );
	}

	public function test_api_calls() {

		$properties = arve_get_host_properties();

		foreach ( $properties as $provider => $props ) :

			if ( empty( $values['tests'] ) ) {
				continue;
			}

			foreach ( $values['tests'] as $key => $test ) {

				$attr = array(
					'url'  => $test['url'],
					'mode' => 'lazyload',
				);

				#if( isset( $props['auto_title'] ) && $props['auto_title'] ) {
				if( isset( $test['api_title'] ) ) {
					$this->assertNotContains( 'Error', arve_shortcode_arve( $attr ) );
					$this->assertContains( '<h5 itemprop="name" class="arve-title">' . $test['api_title'] . '</h5>', arve_shortcode_arve( $attr ) );
				}
				if( isset( $test['api_img'] ) ) {
					$this->assertNotContains( 'Error', arve_shortcode_arve( $attr ) );
					$this->assertRegex( '#<img [^>]*src="' . $test['api_img'] . '#', arve_shortcode_arve( $attr ) );
				}
			}
		endforeach;
	}

	public function test_thumbnail_byattachment_and_url() {

		$filename = dirname( __FILE__ ) . '/test-attachment-2.jpg';
		$contents = file_get_contents( $filename );

		$upload = wp_upload_bits( basename( $filename ), null, $contents );
		$this->assertTrue( empty( $upload['error'] ) );

		$attachment_id = parent::_make_attachment( $upload );

		$attr = array(
			'url'       => 'https://www.youtube.com/watch?v=hRonZ4wP8Ys',
			'thumbnail' => (string) $attachment_id,
			'title'     => 'title test', # to prevent oembed call for title
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
		$attr  = array(
			'url'       => 'https://www.youtube.com/watch?v=hRonZ4wP8Ys',
			'title'     => 'something',
			'thumbnail' => 'https://example.com/i.jpg',
		 );

		foreach ( $modes as $mode ) {

			$attr['mode'] = $mode;

			$this->assertNotContains( 'Error', arve_shortcode_arve( $attr ) );
			$this->assertContains( sprintf( 'data-arve-mode="%s"', $mode ), arve_shortcode_arve( $attr ), "mode: $mode" );
		}
	}
}
