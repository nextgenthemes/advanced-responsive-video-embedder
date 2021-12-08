<?php
use function Nextgenthemes\ARVE\shortcode;
// phpcs:disable Squiz.PHP.CommentedOutCode.Found
// phpcs:disable Squiz.Classes.ClassFileName.NoMatch
// phpcs:disable Squiz.PHP.Classes.ValidClassName.NotCamelCaps
class Tests_Embed_Shortcode extends WP_UnitTestCase {

	public function test_embed_sc_mp4() {

		$html = apply_filters( 'the_content', '[embed]https://www.example.com/wp-content/uploads/2020/05/sample-mp4-file.mp4[/embed]' );
		$this->assertStringContainsString( 'class="arve-video', $html );
		$this->assertStringNotContainsString( 'Error', $html );
	}

	public function test_video_sc_mp4() {

		$html = apply_filters( 'the_content', '[video src="https://www.example.com/wp-content/uploads/2020/05/sample-mp4-file.mp4" loop="on" /]' );
		$this->assertStringContainsString( 'class="arve-video', $html );
		$this->assertStringNotContainsString( 'Error', $html );
	}
}
