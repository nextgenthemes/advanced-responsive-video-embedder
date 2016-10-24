<?php

class Tests_Shortcode extends WP_UnitTestCase {

	public function test_thumbnails() {

		$filename = dirname( __FILE__ ) . '/test-attachment.jpg';
		$contents = file_get_contents( $filename );

		$upload = wp_upload_bits( basename( $filename ), null, $contents );
		$this->assertTrue( empty( $upload['error'] ) );

		$attachment_id = parent::_make_attachment( $upload );

		$attr = array(
			'url' => 'https://www.youtube.com/watch?v=hRonZ4wP8Ys',
			'thumbnail' => (string) $attachment_id,
		);

		$this->assertRegExp( '#<meta itemprop="thumbnailUrl" content=".*test-attachment\.jpg#', arve_shortcode_arve( $attr ) );

		$attr['thumbnail'] = 'https://example.com/image.jpg';
		$this->assertContains( '<meta itemprop="thumbnailUrl" content="https://example.com/image.jpg"', arve_shortcode_arve( $attr ) );
	}

	public function test_shortcodes_are_registered() {
		global $shortcode_tags;

		$this->assertArrayHasKey( 'arve', $shortcode_tags );
		$this->assertArrayHasKey( 'youtube', $shortcode_tags );
		$this->assertArrayHasKey( 'vimeo', $shortcode_tags );
	}

	public function test_compare_shortcodes() {

		$arve_shortcode = arve_shortcode_arve( array(
			'url' => 'https://www.youtube.com/watch?v=hRonZ4wP8Ys'
		) );

		$old_shortcode = arve_shortcode_arve( array(
			'provider' => 'youtube',
			'id'       => 'hRonZ4wP8Ys',
		) );

		$this->assertEquals( $arve_shortcode, $old_shortcode );
	}

	public function test_modes() {

		$output = arve_shortcode_arve( array( 'url' => 'https://www.youtube.com/watch?v=hRonZ4wP8Ys' ) );

		$this->assertNotContains( 'Error', $output );
		$this->assertContains( 'data-arve-mode="normal"', $output );

		$modes = array( 'lazyload', 'lazyload-lightbox' );

		foreach ( $modes as $key => $mode ) {

			$output = arve_shortcode_arve( array( 'url' => 'https://www.youtube.com/watch?v=hRonZ4wP8Ys', 'mode' => $mode ) );
			$this->assertContains( 'Error', $output );
		}
	}

	public function test_attr() {

		$atts = array(
			'align'       => 'left',
			'autoplay'    => 'y',
			'description' => '    Description Test   ',
			'maxwidth'    => '333',
			'title'       => ' Test <title>  ',
			'upload_date' => '2016-10-22',
			'url'         => 'https://www.youtube.com/watch?v=hRonZ4wP8Ys',
		);

		$output = arve_shortcode_arve( $atts );

		$this->assertNotContains( 'Error', $output );

		$this->assertContains( 'alignleft', $output );
		$this->assertContains( 'autoplay=1', $output );
		$this->assertContains( '<span itemprop="description" class="arve-description arve-hidden">Description Test</span>', $output );
		$this->assertContains( 'style="max-width: 333px;"', $output );
		$this->assertContains( '<meta itemprop="name" content="Test &lt;title&gt;">', $output );
		$this->assertContains( '<meta itemprop="uploadDate" content="2016-10-22">', $output );
		$this->assertContains( 'src="https://www.youtube-nocookie.com/embed/hRonZ4wP8Ys', $output );
	}

	public function test_html5() {

		$html5_ext = array( 'mp4', 'm4v', 'webm', 'ogv' );

		foreach ( $html5_ext as $ext ) {

			$output = arve_shortcode_arve( array( 'url' => 'https://example.com/video.' . $ext ) );

			$this->assertNotContains( 'Error', $output );
			$this->assertNotContains( '<iframe', $output );
			$this->assertContains( 'data-arve-provider="html5"', $output );
			$this->assertContains( '<video', $output );

			$output = arve_shortcode_arve( array( $ext => 'https://example.com/video.' . $ext ) );

			$this->assertNotContains( 'Error', $output );
			$this->assertNotContains( '<iframe', $output );
			$this->assertContains( 'data-arve-provider="html5"', $output );
			$this->assertContains( '<video', $output );
		}

		$output = arve_shortcode_arve( array(
			'mp4'  => 'https://example.com/video.mp4',
			'ogv'  => 'https://example.com/video.ogv',
			'webm' => 'https://example.com/video.webm',
		) );

		$this->assertNotContains( 'Error', $output );
		$this->assertNotContains( '<iframe', $output );
		$this->assertContains( 'data-arve-provider="html5"', $output );
		$this->assertContains( '<video', $output );
		$this->assertContains( '<source type="video/ogg"', $output );
		$this->assertContains( '<source type="video/mp4"', $output );
		$this->assertContains( '<source type="video/webm"', $output );
	}

	public function test_iframe() {

		$output = arve_shortcode_arve( array( 'url' => 'https://example.com' ) );

		$this->assertNotContains( 'Error', $output );
		$this->assertRegExp( '#<iframe .*src="https://example\.com#', $output );
		$this->assertContains( 'data-arve-provider="iframe"', $output );
	}

	public function test_regex() {

		$properties = arve_get_host_properties();

		foreach( $properties as $provider => $host_props ) :

	    if ( empty( $host_props['test_urls'] ) || empty( $host_props['regex'] ) ) {
	      continue;
	    }

	    foreach( $host_props['test_urls'] as $urltest ) {

	      if ( is_array( $urltest ) ) {
					$url_to_test = $urltest[0];
		      $expected_id = $urltest[1];
	      } else {
					$expected_id = $url_to_test = $urltest;
				}

	      preg_match( '#' . $host_props['regex'] . '#i', $url_to_test, $matches );

				$this->assertArrayHasKey( 1, $matches, $provider );
	      $this->assertEquals( $matches[1], $expected_id, $provider );
	    }

	  endforeach;
	}

}
