<?php

class Tests_Shortcode_Pro extends WP_UnitTestCase {

	public function test_auto_thumb_and_title() {

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

				if( isset( $props['auto_title'] ) && $props['auto_title'] ) {
					$this->assertNotContains( 'Error', arve_shortcode_arve( $attr ) );
					$this->assertContains( '<h5 itemprop="name" class="arve-title">', arve_shortcode_arve( $attr ) );
				}
				if( isset( $props['auto_thumbnail'] ) && $props['auto_thumbnail'] ) {
					$this->assertNotContains( 'Error', arve_shortcode_arve( $attr ) );
					$this->assertContains( '<img class="arve-thumbnail" data-object-fit itemprop="thumbnailUrl" src="', arve_shortcode_arve( $attr ) );
				}
			}
		endforeach;
	}

	public function test_latest_youtube() {

		global $wp_version;

		if(
			(
				5 == PHP_MAJOR_VERSION && 3 == PHP_MINOR_VERSION &&
				( version_compare( $wp_version, '4.4', '==' ) || version_compare( $wp_version, '4.5', '==' ) )
			) ||
			defined('HHVM_VERSION')
		) {
			$this->markTestSkipped( 'Fails on HHVM and this php, wp combinations' );
		}

		$attr = array(
			'url'  => 'https://www.youtube.com/channel/UChwwoeOZ3EJPobW83dgQfAg',
			'mode' => 'lazyload',
		);

		$this->assertNotContains( 'Error', arve_shortcode_arve( $attr ) );
		$this->assertContains( '<h5 itemprop="name" class="arve-title">', arve_shortcode_arve( $attr ) );
	}


	public function test_autoplay() {

		$options = arve_pro_get_options();

		$attr = array(
			'url'       => 'https://example.com',
			'thumbnail' => 'https://example.com/example.jpg',
			'mode'      => 'lazyload',
		);

		$this->assertNotContains( 'Error', arve_shortcode_arve( $attr ) );
		$this->assertContains( 'autoplay=1', arve_shortcode_arve( $attr ) );

		unset( $attr['thumbnail'] );

		$this->assertNotContains( 'Error', arve_shortcode_arve( $attr ) );
		$this->assertContains( 'autoplay=0', arve_shortcode_arve( $attr ) );
	}

	public function NO_test_oembed_thumbnail_and_title() {

		$properties = arve_get_host_properties();

		remove_filter( 'shortcode_atts_arve',    'arve_pro_sc_filter_img_src', 8 );
		remove_filter( 'shortcode_atts_arve',    'arve_pro_sc_filter_img_src_srcset', 9 );

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
					$this->assertContains(
						sprintf( '<img class="arve-thumbnail" data-object-fit itemprop="thumbnailUrl" src="%s" alt="Video Thumbnail">', $test['oembed_img'] ),
						arve_shortcode_arve( $attr )
					);
				}
				if( isset( $test['oembed_img_start'] ) ) {
					$this->assertNotContains( 'Error', arve_shortcode_arve( $attr ) );
					$this->assertRegex( '#<img [^>]*src="' . $test['oembed_img_start'] . '#', arve_shortcode_arve( $attr ) );
				}
			}
		endforeach;

		add_filter( 'shortcode_atts_arve',    'arve_pro_sc_filter_img_src', 8 );
		add_filter( 'shortcode_atts_arve',    'arve_pro_sc_filter_img_src_srcset', 9 );
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
		$this->assertContains( 'data-mode="lazyload"', arve_shortcode_arve( $attr ) );
		$this->assertContains( 'data-grow', arve_shortcode_arve( $attr ) );
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
			$this->assertContains( sprintf( 'data-mode="%s"', $mode ), arve_shortcode_arve( $attr ), "mode: $mode" );
		}
	}

	public function test_disable_links() {

		$attr = array(
			'url' => 'https://www.example.com'
		 );

		$this->assertNotContains( 'Error', arve_shortcode_arve( $attr ) );
		$this->assertNotContains( ' sandbox="', arve_shortcode_arve( $attr ) );

		$attr  = array(
			'url'           => 'https://www.example.com',
			'disable_flash' => 'y'
		 );

		$this->assertNotContains( 'Error', arve_shortcode_arve( $attr ) );
		$this->assertContains( ' sandbox="allow-scripts allow-same-origin allow-presentation allow-popups"', arve_shortcode_arve( $attr ) );

		$attr = array(
			'url'           => 'https://www.example.com',
			'disable_flash' => 'y',
			'disable_links' => 'y'
		 );

		$this->assertNotContains( 'Error', arve_shortcode_arve( $attr ) );
		$this->assertContains( ' sandbox="allow-scripts allow-same-origin allow-presentation"', arve_shortcode_arve( $attr ) );
	}
}
