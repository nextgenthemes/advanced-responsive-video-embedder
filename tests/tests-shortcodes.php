<?php
use function Nextgenthemes\ARVE\shortcode;

// phpcs:disable Squiz.Classes.ClassFileName.NoMatch
// phpcs:disable Squiz.Classes.ValidClassName.NotCamelCaps
class Tests_Shortcode extends WP_UnitTestCase {

	public function change_option( $key, $val ) {
		$options         = get_option( 'arve_options_main' );
		$options[ $key ] = $val;
		update_option( 'arve_options_main', $options );
	}

	public function test_sandbox() {
		$attr = [
			'url' => 'https://example.com',
		];

		$this->assertNotContains( 'Error', shortcode( $attr ) );
		$this->assertContains(
			'sandbox="',
			shortcode( $attr ),
			$attr['url']
		);

		$attr['sandbox'] = 'false';

		$this->assertNotContains( 'Error', shortcode( $attr ) );
		$this->assertNotContains( 'sandbox="', shortcode( $attr ), $attr['url'] );
	}

	public function test_sandbox_vimeo() {

		$attr = array( 'url' => 'https://vimeo.com/214300845' );

		$this->assertNotContains( 'Error', shortcode( $attr ) );
		$this->assertContains(
			'allow-forms',
			shortcode( $attr ),
			$attr['url']
		);
	}

	public function test_thumbnails() {

		$filename = dirname( __FILE__ ) . '/test-attachment.jpg';
		// phpcs:disable WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$contents = file_get_contents( $filename );

		$upload = wp_upload_bits( basename( $filename ), null, $contents );
		$this->assertTrue( empty( $upload['error'] ) );

		$attachment_id = parent::_make_attachment( $upload );

		$attr = [
			'url'       => 'https://example.com/video.mp4',
			'thumbnail' => (string) $attachment_id,
			'title'     => 'Something',
		];

		$this->assertRegExp( '#<meta itemprop="thumbnailUrl" content=".*test-attachment\.jpg#', shortcode( $attr ) );

		$attr = [
			'url'       => 'https://example.com/video2.mp4',
			'thumbnail' => 'https://example.com/image.jpg',
		];

		$this->assertContains( '<meta itemprop="thumbnailUrl" content="https://example.com/image.jpg"', shortcode( $attr ) );
	}

	public function test_shortcodes_are_registered() {
		$this->assertArrayHasKey( 'arve', $GLOBALS['shortcode_tags'] );
		$this->assertArrayHasKey( 'youtube', $GLOBALS['shortcode_tags'] );
		$this->assertArrayHasKey( 'vimeo', $GLOBALS['shortcode_tags'] );
	}

	public function old_test_compare_shortcodes() {

		$atts = [
			'id'        => 'hRonZ4wP8Ys',
			'provider'  => 'youtube',
			'thumbnail' => 'https://example.com/image.jpg',
			'title'     => 'Something',
			'url'       => 'https://www.youtube.com/watch?v=hRonZ4wP8Ys',
		];

		$new_atts = $atts;
		$old_atts = $atts;

		$this->assertEquals(
			shortcode( $old_atts ),
			shortcode( $new_atts )
		);

		unset( $old_atts['url'] );

		unset( $new_atts['id'] );
		unset( $new_atts['provider'] );

		$this->assertEquals(
			shortcode( $old_atts ),
			shortcode( $new_atts )
		);
	}

	public function NO_test_modes() {

		$output = shortcode( [ 'url' => 'https://www.youtube.com/watch?v=hRonZ4wP8Ys' ] );

		$this->assertNotContains( 'Error', $output );
		$this->assertContains( 'data-mode="normal"', $output );

		$modes = [ 'lazyload', 'lazyload-lightbox' ];

		foreach ( $modes as $key => $mode ) {

			$output = shortcode( [
				'url'  => 'https://www.youtube.com/watch?v=hRonZ4wP8Ys',
				'mode' => $mode
			] );
			$this->assertContains( 'Error', $output );
		}
	}

	public function test_attr() {

		$output = shortcode( [
			'align'       => 'left',
			'autoplay'    => 'y',
			'description' => '    Description Test   ',
			'maxwidth'    => '333',
			'thumbnail'   => 'https://example.com/image.jpg',
			'title'       => ' Test <title>  ',
			'upload_date' => '2016-10-22',
			'duration'    => '1H2M3S',
			'url'         => 'https://example.com',
		] );

		$this->assertNotContains( 'Error', $output );

		$this->assertContains( 'alignleft', $output );
		#$this->assertContains( 'autoplay=1', $output );
		$this->assertContains( '<meta itemprop="description" content="Description Test">', $output );
		$this->assertContains( 'style="max-width:333px;"', $output );
		$this->assertContains( '<meta itemprop="name" content="Test &lt;title&gt;">', $output );
		$this->assertContains( '<meta itemprop="uploadDate" content="2016-10-22">', $output );
		$this->assertContains( '<meta itemprop="duration" content="PT1H2M3S">', $output );
		$this->assertContains( 'src="https://example.com', $output );
	}

	public function test_html5() {

		$html5_ext = [ 'mp4', 'm4v', 'webm', 'ogv' ];

		foreach ( $html5_ext as $ext ) {

			$with_src = shortcode( [ 'url' => 'https://example.com/video.' . $ext ] );
			$with_ext = shortcode( [ $ext => 'https://example.com/video.' . $ext ] );

			$this->assertNotContains( 'Error', $with_src );
			$this->assertNotContains( 'Error', $with_ext );
			$this->assertNotContains( '<iframe', $with_src );
			$this->assertNotContains( '<iframe', $with_ext );
			$this->assertContains( 'data-provider="html5"', $with_src );
			$this->assertContains( 'data-provider="html5"', $with_ext );
			$this->assertContains( '<video', $with_src );
			$this->assertContains( '<video', $with_ext );
		}

		$attr = [
			'url'          => 'https://example.com/video.mp4',
			'controlslist' => 'nofullscreen nodownload',
		];

		$this->assertContains( 'controlslist="nofullscreen nodownload"', shortcode( $attr ) );

		$output = shortcode( [
			'mp4'       => 'https://example.com/video.mp4',
			'ogv'       => 'https://example.com/video.ogv',
			'webm'      => 'https://example.com/video.webm',
			'thumbnail' => 'https://example.com/image.jpg',
			'track_1'   => 'https://example.com/v-subtitles-en.vtt',
			'track_2'   => 'https://example.com/v-subtitles-de.vtt',
			'track_3'   => 'https://example.com/v-subtitles-es.vtt',
		] );

		// $output2 = wp_video_shortcode( array(
		// 'mp4'       => 'https://example.com/video.mp4',
		// 'ogv'       => 'https://example.com/video.ogv',
		// 'webm'      => 'https://example.com/video.webm',
		// 'poster'    => 'https://example.com/image.jpg',
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

		$this->assertContains( '<track default kind="subtitles" label="English" src="https://example.com/v-subtitles-en.vtt" srclang="en">', $output );
		$this->assertContains( '<track kind="subtitles" label="Deutsch" src="https://example.com/v-subtitles-de.vtt" srclang="de">', $output );
		$this->assertContains( '<track kind="subtitles" label="Español" src="https://example.com/v-subtitles-es.vtt" srclang="es">', $output );
	}

	public function test_iframe() {

		$output = shortcode( [ 'url' => 'https://example.com' ] );

		$this->assertNotContains( 'Error', $output );
		$this->assertRegExp( '#<iframe .*src="https://example\.com#', $output );
		$this->assertContains( 'data-provider="iframe"', $output );
	}

	public function test_regex() {

		$properties = \Nextgenthemes\ARVE\get_host_properties();

		$this->assertTrue( is_array( $properties ) );
		$this->assertNotEmpty( $properties );

		foreach ( $properties as $host_id => $host ) :

			$this->assertNotEmpty( $host, $host_id );
			$this->assertTrue( is_array( $host ), $host_id );

			if ( empty( $host['regex'] ) ) {
				continue;
			}

			$this->assertArrayHasKey( 'tests', $host, $host_id );
			$this->assertNotEmpty( $host['tests'], $host_id );
			$this->assertTrue( is_array( $host['tests'] ), $host_id );

			foreach ( $host['tests'] as $test ) :

				$this->assertNotEmpty( $test, $host_id );
				$this->assertTrue( is_array( $test ), $host_id );
				$this->assertArrayHasKey( 'id',  $test, $host_id );
				$this->assertArrayHasKey( 'url', $test, $host_id );

				preg_match( $host['regex'], $test['url'], $matches );

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

				if ( 'brightcove' === $host_id ) {
					$this->assertEquals( $matches['account_id'], $test['account_id'] );
					$this->assertEquals( $matches['brightcove_player'], $test['brightcove_player'] );
					$this->assertEquals( $matches['brightcove_embed'], $test['brightcove_embed'] );
				}
			endforeach;

		endforeach;
	}

	public function regex2() {

		add_filter( 'shortcode_atts_arve', [ $this, 'check_regex_detection' ] );

		$properties = \Nextgenthemes\ARVE\get_host_properties();

		foreach ( $properties as $host_id => $host ) :

			if ( empty( $host['regex'] ) ) {
				continue;
			}

			foreach ( $host['tests'] as $test ) {

				$this->$current_test;

				shortcode( [
					'url' => $test['url']
				] );
			}
		endforeach;
	}

	public function check_regex_detection( $atts ) {

		$this->assertEquals( $atts['id'] );
	}

	public function test_dropbox_html5() {

		$attr = [ 'url' => 'https://www.dropbox.com/s/ocqf9u5pn9b4ox0/Oops%20I%20dropped%20my%20Hoop.mp4' ];

		$this->assertNotContains( 'Error', shortcode( $attr) );

		$this->assertRegExp( '#<video .*src="https://www\.dropbox\.com/s/ocqf9u5pn9b4ox0/Oops%20I%20dropped%20my%20Hoop\.mp4\?dl=1#', shortcode( $attr) );
		$this->assertContains( 'data-provider="html5"', shortcode( $attr ) );
	}
}
