<?php
use function \Nextgenthemes\ARVE\shortcode;
use function \Nextgenthemes\ARVE\get_host_properties;

// phpcs:disable Squiz.PHP.CommentedOutCode.Found, Squiz.Classes.ClassFileName.NoMatch, Squiz.PHP.Classes.ValidClassName.NotCamelCaps, WordPress.PHP.DevelopmentFunctions.error_log_print_r, WordPress.PHP.DevelopmentFunctions.error_log_error_log
class Tests_ShortcodeVideoFiles extends WP_UnitTestCase {

	public function test_av1_url() {

		$html = shortcode( array( 'url' => 'https://example.com/video.av1.mp4' ) );

		$this->assertStringContainsString( 'arve-embed', $html );
		$this->assertStringContainsString( '<video', $html );
		$this->assertStringContainsString( 'src="https://example.com/video.av1.mp4"', $html );
		$this->assertStringNotContainsString( 'Error', $html );
	}

	public function test_av1_arg() {

		$html = shortcode( array( 'av1mp4' => 'https://example.com/video.mp4' ) );

		$this->assertStringContainsString( 'arve-embed', $html );
		$this->assertStringContainsString( '<video', $html );
		$this->assertStringContainsString( 'src="https://example.com/video.mp4"', $html );
		$this->assertStringContainsString( 'video/mp4; codecs=av01.0.05M.08', $html );
		$this->assertStringNotContainsString( 'Error', $html );
	}

	public function test_do_not_override_wmv() {

		$html = do_shortcode( '[video wmv="https://example.com/video.wmv" /]' );
		$this->assertStringNotContainsString( 'id="arve-"', $html );
	}

	public function test_wp_video_mp4_with_poster() {

		$html = do_shortcode( '[video mp4="https://example.com/video.mp4" poster="https://example.com/poster.jpg" /]' );

		$this->assertStringContainsString( 'arve-embed', $html );
		$this->assertStringContainsString( '<video', $html );
		$this->assertStringContainsString( 'src="https://example.com/video.mp4"', $html );
		$this->assertStringContainsString( 'https://example.com/poster.jpg', $html );
		$this->assertStringNotContainsString( 'Error', $html );
	}

	public function test_html5() {

		$html5_ext = array( 'av1mp4', 'mp4', 'webm', 'ogv' );

		foreach ( $html5_ext as $ext ) {

			$with_url = shortcode( array( 'url' => 'https://example.com/video.' . $ext ) );
			$with_ext = shortcode( array( $ext => 'https://example.com/video.' . $ext ) );

			$this->assertStringNotContainsString( 'Error', $with_url );
			$this->assertStringNotContainsString( 'Error', $with_ext );
			$this->assertStringNotContainsString( '<iframe', $with_url );
			$this->assertStringNotContainsString( '<iframe', $with_ext );
			$this->assertStringContainsString( 'data-provider="html5"', $with_url );
			$this->assertStringContainsString( 'data-provider="html5"', $with_ext );
			$this->assertStringContainsString( '<video', $with_url );
			$this->assertStringContainsString( '<video', $with_ext );
			$this->assertStringContainsString( '<source type="video', $with_url );
			$this->assertStringContainsString( '<source type="video', $with_ext );

			$this->assertStringContainsString( '"contentURL":"', $with_url );
			$this->assertStringContainsString( '"contentURL":"', $with_ext );
		}

		$output = shortcode(
			array(
				'controlslist' => 'nofullscreen nodownload',
				'mp4'          => 'https://example.com/video.mp4',
				'ogv'          => 'https://example.com/video.ogv',
				'webm'         => 'https://example.com/video.webm',
				'thumbnail'    => 'https://example.com/image.jpg',
				'track_1'      => 'https://example.com/v-subtitles-en.vtt',
				'track_2'      => 'https://example.com/v-subtitles-de.vtt',
				'track_3'      => 'https://example.com/v-subtitles-es.vtt',
			)
		);

		$this->assertStringNotContainsString( 'Error', $output );
		$this->assertStringNotContainsString( '<iframe', $output );
		$this->assertStringNotContainsString( 'should-be-ignored.mp4', $output );
		$this->assertStringContainsString( 'data-provider="html5"', $output );
		$this->assertStringContainsString( '<video', $output );
		$this->assertStringContainsString( 'poster="https://example.com/image.jpg"', $output );
		$this->assertStringContainsString( '<source type="video/ogg" src="https://example.com/video.ogv">', $output );
		$this->assertStringContainsString( '<source type="video/mp4" src="https://example.com/video.mp4">', $output );
		$this->assertStringContainsString( '<source type="video/webm" src="https://example.com/video.webm">', $output );
		$this->assertStringContainsString( 'controlslist="nofullscreen nodownload"', $output );

		$this->assertStringContainsString( '<track default kind="subtitles" label="English" src="https://example.com/v-subtitles-en.vtt" srclang="en">', $output );
		$this->assertStringContainsString( '<track kind="subtitles" label="Deutsch" src="https://example.com/v-subtitles-de.vtt" srclang="de">', $output );
		$this->assertStringContainsString( '<track kind="subtitles" label="Español" src="https://example.com/v-subtitles-es.vtt" srclang="es">', $output );
	}
}
