<?php

declare(strict_types = 1);

use function Nextgenthemes\ARVE\shortcode;
use function Nextgenthemes\ARVE\get_host_properties;

// phpcs:disable Squiz.PHP.CommentedOutCode.Found, Squiz.Classes.ClassFileName.NoMatch, Squiz.PHP.Classes.ValidClassName.NotCamelCaps, WordPress.PHP.DevelopmentFunctions.error_log_print_r, WordPress.PHP.DevelopmentFunctions.error_log_error_log
class Tests_ShortcodeVideoFiles extends WP_UnitTestCase {

	public function test_av1_url(): void {

		$html = shortcode( array( 'url' => 'https://example.com/video.av1.mp4' ) );

		$this->assertStringContainsString( 'arve-embed', $html );
		$this->assertStringContainsString( '<video', $html );
		$this->assertStringContainsString( 'src="https://example.com/video.av1.mp4"', $html );
		$this->assertStringNotContainsStringIgnoringCase( 'Error', $html );
	}

	public function test_av1_arg(): void {

		$html = shortcode( array( 'av1mp4' => 'https://example.com/video.mp4' ) );

		$this->assertStringContainsString( 'arve-embed', $html );
		$this->assertStringContainsString( '<video', $html );
		$this->assertStringContainsString( 'src="https://example.com/video.mp4"', $html );
		$this->assertStringContainsString( 'video/mp4; codecs=av01.0.05M.08', $html );
		$this->assertStringNotContainsStringIgnoringCase( 'Error', $html );
	}

	public function test_do_not_override_wmv(): void {

		$html = do_shortcode( '[video wmv="https://example.com/video.wmv" /]' );
		$this->assertStringNotContainsString( 'id="arve-"', $html );
	}

	public function test_wp_video_mp4_with_poster(): void {

		$html = do_shortcode( '[video mp4="https://example.com/video.mp4" poster="https://example.com/poster.jpg" /]' );

		$this->assertStringContainsString( 'arve-embed', $html );
		$this->assertStringContainsString( '<video', $html );
		$this->assertStringContainsString( 'src="https://example.com/video.mp4"', $html );
		$this->assertStringContainsString( 'https://example.com/poster.jpg', $html );
		$this->assertStringNotContainsStringIgnoringCase( 'Error', $html );
	}

	public function test_html5_url_av1mp4(): void {

		$html = shortcode( array( 'url' => 'https://example.com/video.av1mp4' ) );

		$this->assertStringNotContainsStringIgnoringCase( 'Error', $html );
		$this->assertStringNotContainsString( '<iframe', $html );
		$this->assertStringContainsString( 'data-provider="html5"', $html );
		$this->assertStringContainsString( '<video', $html );
		$this->assertStringContainsString( '<source type="video', $html );
		$this->assertStringContainsString( '"contentURL":"', $html );
	}

	public function test_html5_ext_av1mp4(): void {

		$html = shortcode( array( 'av1mp4' => 'https://example.com/video.av1mp4' ) );

		$this->assertStringNotContainsStringIgnoringCase( 'Error', $html );
		$this->assertStringNotContainsString( '<iframe', $html );
		$this->assertStringContainsString( 'data-provider="html5"', $html );
		$this->assertStringContainsString( '<video', $html );
		$this->assertStringContainsString( '<source type="video', $html );
		$this->assertStringContainsString( '"contentURL":"', $html );
	}

	public function test_html5_url_mp4(): void {

		$html = shortcode( array( 'url' => 'https://example.com/video.mp4' ) );

		$this->assertStringNotContainsStringIgnoringCase( 'Error', $html );
		$this->assertStringNotContainsString( '<iframe', $html );
		$this->assertStringContainsString( 'data-provider="html5"', $html );
		$this->assertStringContainsString( '<video', $html );
		$this->assertStringContainsString( '<source type="video', $html );
		$this->assertStringContainsString( '"contentURL":"', $html );
	}

	public function test_html5_ext_mp4(): void {

		$html = shortcode( array( 'mp4' => 'https://example.com/video.mp4' ) );

		$this->assertStringNotContainsStringIgnoringCase( 'Error', $html );
		$this->assertStringNotContainsString( '<iframe', $html );
		$this->assertStringContainsString( 'data-provider="html5"', $html );
		$this->assertStringContainsString( '<video', $html );
		$this->assertStringContainsString( '<source type="video', $html );
		$this->assertStringContainsString( '"contentURL":"', $html );
	}

	public function test_html5_url_webm(): void {

		$html = shortcode( array( 'url' => 'https://example.com/video.webm' ) );

		$this->assertStringNotContainsStringIgnoringCase( 'Error', $html );
		$this->assertStringNotContainsString( '<iframe', $html );
		$this->assertStringContainsString( 'data-provider="html5"', $html );
		$this->assertStringContainsString( '<video', $html );
		$this->assertStringContainsString( '<source type="video', $html );
		$this->assertStringContainsString( '"contentURL":"', $html );
	}

	public function test_html5_ext_webm(): void {

		$html = shortcode( array( 'webm' => 'https://example.com/video.webm' ) );

		$this->assertStringNotContainsStringIgnoringCase( 'Error', $html );
		$this->assertStringNotContainsString( '<iframe', $html );
		$this->assertStringContainsString( 'data-provider="html5"', $html );
		$this->assertStringContainsString( '<video', $html );
		$this->assertStringContainsString( '<source type="video', $html );
		$this->assertStringContainsString( '"contentURL":"', $html );
	}

	public function test_html5_url_ogv(): void {

		$html = shortcode( array( 'url' => 'https://example.com/video.ogv' ) );

		$this->assertStringNotContainsStringIgnoringCase( 'Error', $html );
		$this->assertStringNotContainsString( '<iframe', $html );
		$this->assertStringContainsString( 'data-provider="html5"', $html );
		$this->assertStringContainsString( '<video', $html );
		$this->assertStringContainsString( '<source type="video', $html );
		$this->assertStringContainsString( '"contentURL":"', $html );
	}

	public function test_html5_ext_ogv(): void {

		$html = shortcode( array( 'ogv' => 'https://example.com/video.ogv' ) );

		$this->assertStringNotContainsStringIgnoringCase( 'Error', $html );
		$this->assertStringNotContainsString( '<iframe', $html );
		$this->assertStringContainsString( 'data-provider="html5"', $html );
		$this->assertStringContainsString( '<video', $html );
		$this->assertStringContainsString( '<source type="video', $html );
		$this->assertStringContainsString( '"contentURL":"', $html );
	}

	public function test_html5_multi_source(): void {

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

		$this->assertStringNotContainsStringIgnoringCase( 'Error', $output );
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
