<?php
// phpcs:disable Squiz.Classes.ClassFileName.NoMatch
// phpcs:disable Squiz.Classes.ValidClassName.NotCamelCaps
class Tests_URLs extends WP_UnitTestCase {

	public function test_urls() {

		$output = apply_filters( 'the_content', 'https://www.youtube.com/watch?v=2an6-WVPuJU' );

		$this->assertNotContains( 'Error', $output );
		$this->assertContains( 'src="https://www.youtube-nocookie.com/embed/2an6-WVPuJU', $output );
	}
}
