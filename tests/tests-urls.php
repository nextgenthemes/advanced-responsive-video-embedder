<?php

declare(strict_types = 1);

// phpcs:disable Squiz.Classes.ClassFileName.NoMatch
// phpcs:disable Squiz.Classes.ValidClassName.NotCamelCaps
class Tests_URLs extends WP_UnitTestCase {

	public function test_urls(): void {

		$output = apply_filters( 'the_content', 'https://vimeo.com/265932452' );

		$this->assertStringNotContainsStringIgnoringCase( 'Error', $output );
		$this->assertStringContainsString( 'src="https://player.vimeo.com/video/265932452', $output );
	}
}
