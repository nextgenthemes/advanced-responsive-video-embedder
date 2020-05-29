<?php
use function \Nextgenthemes\ARVE\shortcode;
use function \Nextgenthemes\ARVE\get_host_properties;

// phpcs:disable Squiz.PHP.CommentedOutCode.Found, Squiz.Classes.ClassFileName.NoMatch, Squiz.PHP.Classes.ValidClassName.NotCamelCaps, WordPress.PHP.DevelopmentFunctions.error_log_print_r, WordPress.PHP.DevelopmentFunctions.error_log_error_log
class Tests_ShortcodeVideoFiles extends WP_UnitTestCase {

	public function test_av1_url() {

		$html = shortcode( [ 'url' => 'https://example.com/video.av1.mp4' ] );

		$this->assertContains( 'arve-embed', $html );
		$this->assertContains( '<video', $html );
		$this->assertContains( 'src="https://example.com/video.av1.mp4"', $html );
		$this->assertNotContains( 'Error', $html );
	}

	public function test_av1_arg() {

		$html = shortcode( [ 'av1mp4' => 'https://example.com/video.mp4' ] );

		$this->assertContains( 'arve-embed', $html );
		$this->assertContains( '<video', $html );
		$this->assertContains( 'src="https://example.com/video.mp4"', $html );
		$this->assertContains( 'video/mp4; codecs=av01.0.05M.08', $html );
		$this->assertNotContains( 'Error', $html );
	}

	public function test_do_not_override_wmv() {

		$html = do_shortcode( '[video wmv="https://example.com/video.wmv" /]' );
		$this->assertNotContains( 'id="arve-"', $html );
	}

	public function test_wp_video_mp4_with_poster() {

		$html = do_shortcode( '[video mp4="https://example.com/video.mp4" poster="https://example.com/poster.jpg" /]' );

		$this->assertContains( 'arve-embed', $html );
		$this->assertContains( '<video', $html );
		$this->assertContains( 'src="https://example.com/video.mp4"', $html );
		$this->assertContains( 'https://example.com/poster.jpg', $html );
		$this->assertNotContains( 'Error', $html );
	}

	public function test_html5() {

		$html5_ext = [ 'av1mp4', 'mp4', 'webm', 'ogv' ];

		foreach ( $html5_ext as $ext ) {

			$with_url = shortcode( [ 'url' => 'https://example.com/video.' . $ext ] );
			$with_ext = shortcode( [ $ext => 'https://example.com/video.' . $ext ] );

			$this->assertNotContains( 'Error', $with_url );
			$this->assertNotContains( 'Error', $with_ext );
			$this->assertNotContains( '<iframe', $with_url );
			$this->assertNotContains( '<iframe', $with_ext );
			$this->assertContains( 'data-provider="html5"', $with_url );
			$this->assertContains( 'data-provider="html5"', $with_ext );
			$this->assertContains( '<video', $with_url );
			$this->assertContains( '<video', $with_ext );
			$this->assertContains( '<source type="video', $with_url );
			$this->assertContains( '<source type="video', $with_ext );
		}

		$output = shortcode(
			[
				'controlslist' => 'nofullscreen nodownload',
				'mp4'          => 'https://example.com/video.mp4',
				'ogv'          => 'https://example.com/video.ogv',
				'webm'         => 'https://example.com/video.webm',
				'thumbnail'    => 'https://example.com/image.jpg',
				'track_1'      => 'https://example.com/v-subtitles-en.vtt',
				'track_2'      => 'https://example.com/v-subtitles-de.vtt',
				'track_3'      => 'https://example.com/v-subtitles-es.vtt',
			]
		);

		$this->assertNotContains( 'Error', $output );
		$this->assertNotContains( '<iframe', $output );
		$this->assertNotContains( 'should-be-ignored.mp4', $output );
		$this->assertContains( 'data-provider="html5"', $output );
		$this->assertContains( '<video', $output );
		$this->assertContains( 'poster="https://example.com/image.jpg"', $output );
		$this->assertContains( '<source type="video/ogg" src="https://example.com/video.ogv">', $output );
		$this->assertContains( '<source type="video/mp4" src="https://example.com/video.mp4">', $output );
		$this->assertContains( '<source type="video/webm" src="https://example.com/video.webm">', $output );
		$this->assertContains( 'controlslist="nofullscreen nodownload"', $output );

		$this->assertContains( '<track default kind="subtitles" label="English" src="https://example.com/v-subtitles-en.vtt" srclang="en">', $output );
		$this->assertContains( '<track kind="subtitles" label="Deutsch" src="https://example.com/v-subtitles-de.vtt" srclang="de">', $output );
		$this->assertContains( '<track kind="subtitles" label="Español" src="https://example.com/v-subtitles-es.vtt" srclang="es">', $output );
	}
}
