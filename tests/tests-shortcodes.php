<?php

class Tests_Shortcode extends WP_UnitTestCase {

	public function test_sandbox() {

		$attr = array(
			'url'       => 'https://www.youtube.com/watch?v=hRonZ4wP8Ys',
			'thumbnail' => 'https://example.com/image.jpg',
			'title'     => 'title'
		);

		$this->assertNotContains( 'Error', arve_shortcode( $attr ) );
		$this->assertContains( 'sandbox="allow-scripts allow-same-origin allow-presentation allow-popups"', arve_shortcode( $attr ) );

		$attr = array(
			'url'       => 'https://vimeo.com/214300845',
			'thumbnail' => 'https://example.com/image.jpg',
			'title'     => 'title'
		);

		$this->assertNotContains( 'Error', arve_shortcode( $attr ) );
		$this->assertContains( 'sandbox="allow-scripts allow-same-origin allow-presentation allow-popups allow-forms"', arve_shortcode( $attr ) );
	}

	public function test_thumbnails() {

		$filename = dirname( __FILE__ ) . '/test-attachment.jpg';
		$contents = file_get_contents( $filename );

		$upload = wp_upload_bits( basename( $filename ), null, $contents );
		$this->assertTrue( empty( $upload['error'] ) );

		$attachment_id = parent::_make_attachment( $upload );

		$attr = array(
			'url'       => 'https://www.youtube.com/watch?v=hRonZ4wP8Ys',
			'thumbnail' => (string) $attachment_id,
			'title'     => 'Something',
		);

		$this->assertRegExp( '#<meta itemprop="thumbnailUrl" content=".*test-attachment\.jpg#', arve_shortcode( $attr ) );

		$attr['thumbnail'] = 'https://example.com/image.jpg';
		$this->assertContains( '<meta itemprop="thumbnailUrl" content="https://example.com/image.jpg"', arve_shortcode( $attr ) );
	}

	public function test_shortcodes_are_registered() {
		global $shortcode_tags;

		$this->assertArrayHasKey( 'arve', $shortcode_tags );
		$this->assertArrayHasKey( 'youtube', $shortcode_tags );
		$this->assertArrayHasKey( 'vimeo', $shortcode_tags );
	}

	public function old_test_compare_shortcodes() {

		$atts = array(
			'id'        => 'hRonZ4wP8Ys',
			'provider'  => 'youtube',
			'thumbnail' => 'https://example.com/image.jpg',
			'title'     => 'Something',
			'url'       => 'https://www.youtube.com/watch?v=hRonZ4wP8Ys',
		);

		$new_atts = $old_atts = $atts;

		$this->assertEquals(
			arve_shortcode( $old_atts, null, false ),
			arve_shortcode( $new_atts )
		);

		unset( $old_atts['url'] );

		unset( $new_atts['id'] );
		unset( $new_atts['provider'] );

		$this->assertEquals(
			arve_shortcode( $old_atts, null, false ),
			arve_shortcode( $new_atts )
		);
	}

	public function NO_test_modes() {

		$output = arve_shortcode( array( 'url' => 'https://www.youtube.com/watch?v=hRonZ4wP8Ys' ) );

		$this->assertNotContains( 'Error', $output );
		$this->assertContains( 'data-mode="normal"', $output );

		$modes = array( 'lazyload', 'lazyload-lightbox' );

		foreach ( $modes as $key => $mode ) {

			$output = arve_shortcode( array( 'url' => 'https://www.youtube.com/watch?v=hRonZ4wP8Ys', 'mode' => $mode ) );
			$this->assertContains( 'Error', $output );
		}
	}

	public function test_attr() {

		$atts = array(
			'align'       => 'left',
			'autoplay'    => 'y',
			'description' => '    Description Test   ',
			'maxwidth'    => '333',
			'thumbnail'   => 'https://example.com/image.jpg',
			'title'       => ' Test <title>  ',
			'upload_date' => '2016-10-22',
			'url'         => 'https://www.youtube.com/watch?v=hRonZ4wP8Ys',
		);

		$output = arve_shortcode( $atts );

		$this->assertNotContains( 'Error', $output );

		$this->assertContains( 'alignleft', $output );
		$this->assertContains( 'autoplay=1', $output );
		$this->assertContains( '<span itemprop="description" class="arve-description arve-hidden">Description Test</span>', $output );
		$this->assertContains( 'style="max-width:333px;"', $output );
		$this->assertContains( '<meta itemprop="name" content="Test &lt;title&gt;">', $output );
		$this->assertContains( '<meta itemprop="uploadDate" content="2016-10-22">', $output );
		$this->assertContains( 'src="https://www.youtube.com/embed/hRonZ4wP8Ys', $output );
		$this->assertContains( ' sandbox="allow-scripts allow-same-origin allow-presentation allow-popups"', $output );
	}

	public function test_html5() {

		$html5_ext = array( 'mp4', 'm4v', 'webm', 'ogv' );

		foreach ( $html5_ext as $ext ) {

			$with_src = arve_shortcode( array( 'url' => 'https://example.com/video.' . $ext ) );
			$with_ext = arve_shortcode( array( $ext => 'https://example.com/video.' . $ext ) );

			$this->assertNotContains( 'Error', $with_src );
			$this->assertNotContains( 'Error', $with_ext );
			$this->assertNotContains( '<iframe', $with_src );
			$this->assertNotContains( '<iframe', $with_ext );
			$this->assertContains( 'data-provider="html5"', $with_src );
			$this->assertContains( 'data-provider="html5"', $with_ext );
			$this->assertContains( '<video', $with_src );
			$this->assertContains( '<video', $with_ext );
			$this->assertContains( 'controlslist="nodownload"', $with_src );
			$this->assertContains( 'controlslist="nodownload"', $with_ext );
		}

		$attr = array(
			'url'          => 'https://example.com/video.mp4',
			'controlslist' => 'nofullscreen whatever',
		 );

		$this->assertContains( 'controlslist="nofullscreen whatever"', arve_shortcode( $attr ) );

		$output = arve_shortcode( array(
			'mp4'       => 'https://example.com/video.mp4',
			'ogv'       => 'https://example.com/video.ogv',
			'webm'      => 'https://example.com/video.webm',
			'thumbnail' => 'https://example.com/image.jpg',
		) );

		// $output2 = wp_video_shortcode( array(
		// 	'mp4'       => 'https://example.com/video.mp4',
		// 	'ogv'       => 'https://example.com/video.ogv',
		// 	'webm'      => 'https://example.com/video.webm',
		// 	'poster'    => 'https://example.com/image.jpg',
		// ) );
		//
		// $this->assertEquals( $output, $output2 );

		$this->assertNotContains( 'Error', $output );
		$this->assertNotContains( '<iframe', $output );
		$this->assertContains( 'data-provider="html5"', $output );
		$this->assertContains( '<video', $output );
		$this->assertContains( 'poster="https://example.com/image.jpg"', $output );
		$this->assertContains( '<source type="video/ogg" src="https://example.com/video.ogv">', $output );
		$this->assertContains( '<source type="video/mp4" src="https://example.com/video.mp4">', $output );
		$this->assertContains( '<source type="video/webm" src="https://example.com/video.webm">', $output );
		$this->assertContains( 'controlslist="nodownload"', $output );
	}

	public function test_iframe() {

		$output = arve_shortcode( array( 'url' => 'https://example.com' ) );

		$this->assertNotContains( 'Error', $output );
		$this->assertRegExp( '#<iframe .*src="https://example\.com#', $output );
		$this->assertContains( 'data-provider="iframe"', $output );
	}

	public function test_regex() {

		$properties = arve_get_host_properties();

		$this->assertTrue( is_array( $properties ) );
		$this->assertNotEmpty( $properties );

		foreach( $properties as $host_id => $host ) :

			$this->assertNotEmpty( $host, $host_id );
			$this->assertTrue( is_array( $host ), $host_id );

			if ( empty( $host['regex'] ) ) {
				continue;
			}

			$this->assertArrayHasKey( 'tests', $host, $host_id );
			$this->assertNotEmpty( $host['tests'], $host_id );
			$this->assertTrue( is_array( $host['tests'] ), $host_id );

			foreach( $host['tests'] as $test ) :

				$this->assertNotEmpty( $test, $host_id );
				$this->assertTrue( is_array( $test ), $host_id );
				$this->assertArrayHasKey( 'id',  $test, $host_id );
				$this->assertArrayHasKey( 'url', $test, $host_id );

				preg_match( '#' . $host['regex'] . '#i', $test['url'], $matches );

				// fwrite( STDERR, 'Regex' . PHP_EOL );
				// fwrite( STDERR, print_r( $host['regex'], true ) );
				// fwrite( STDERR, PHP_EOL );
				// fwrite( STDERR, 'URL from test' . PHP_EOL );
				// fwrite( STDERR, print_r( $test['url'], true ) );
				// fwrite( STDERR, PHP_EOL );
				// fwrite( STDERR, 'Matches' . PHP_EOL );
				// fwrite( STDERR, print_r( $matches, true ) );
				// fwrite( STDERR, PHP_EOL );

				$this->assertNotEmpty( $matches,         $test['url'] );
				$this->assertTrue( is_array( $matches ), $test['url'] );
				$this->assertArrayHasKey( 'id', $test,   $test['url'] );
				$this->assertEquals( $matches['id'], $test['id'], $test['url'] );

				if ( 'brightcove' == $host_id ) {
					$this->assertEquals( $matches['brightcove_account'], $test['brightcove_account'] );
					$this->assertEquals( $matches['brightcove_player'],  $test['brightcove_player'] );
					$this->assertEquals( $matches['brightcove_embed'],   $test['brightcove_embed'] );
				}

			endforeach;

		endforeach;
	}

	public function regex2() {

		add_filter( 'shortcode_atts_arve', array( $this, 'check_regex_detection' ) );

		$properties = arve_get_host_properties();

		foreach( $properties as $host_id => $host ) :

		    if ( empty( $host['regex'] ) ) {
				continue;
		    }

		    foreach( $host['tests'] as $test ) {

				$this->$current_test;

				shortcode_arve( array(
					'url' => $test['url']
				) );
	    	}

		endforeach;
	}

	function check_regex_detection( $atts ) {

		$this->assertEquals( $atts['id'] );
	}

	public function test_disable_flash() {

		$attr = array( 'url' => 'https://example.com' );
		$this->assertNotContains( 'Error', arve_shortcode( $attr) );
		$this->assertRegExp( '#<iframe .*src="https://example\.com#', arve_shortcode( $attr) );
		$this->assertContains( 'data-provider="iframe"', arve_shortcode( $attr ) );
		$this->assertNotContains( 'sandbox="', arve_shortcode( $attr ) );

		$attr['disable_flash'] = 'y';

		$this->assertNotContains( 'Error', arve_shortcode( $attr) );
		$this->assertRegExp( '#<iframe .*src="https://example\.com#', arve_shortcode( $attr) );
		$this->assertContains( 'data-provider="iframe"', arve_shortcode( $attr ) );
		$this->assertContains( 'sandbox="', arve_shortcode( $attr ) );
	}

	public function test_dropbox_html5() {

		$attr = array( 'url' => 'https://www.dropbox.com/s/ocqf9u5pn9b4ox0/Oops%20I%20dropped%20my%20Hoop.mp4' );

		$this->assertNotContains( 'Error', arve_shortcode( $attr) );

		$this->assertRegExp( '#<video .*src="https://www\.dropbox\.com/s/ocqf9u5pn9b4ox0/Oops%20I%20dropped%20my%20Hoop\.mp4\?dl=1#', arve_shortcode( $attr) );
		$this->assertContains( 'data-provider="html5"', arve_shortcode( $attr ) );
	}

	public function test_no_wrapper_id() {

		$attr = array();

		$this->assertNotContains( 'Error: Element ID could not be build, please report this bug.', arve_shortcode( $attr ) );
	}
}
