<?php
use function Nextgenthemes\ARVE\shortcode;
// phpcs:disable Squiz.PHP.CommentedOutCode.Found
// phpcs:disable Squiz.Classes.ClassFileName.NoMatch
// phpcs:disable Squiz.PHP.Classes.ValidClassName.NotCamelCaps
class Tests_Embed_Shortcode extends WP_UnitTestCase {

	public function test_embed_sc_mp4() {

		$html = do_shortcode( '[embed]https://example.com/vid.mp4[/embed]' );
		$this->assertContains( 'class="arve-video', $html );
		$this->assertNotContains( 'Error', $html );
	}
}
