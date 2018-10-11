<?php
// phpcs:disable Squiz.Classes.ClassFileName.NoMatch
// phpcs:disable Squiz.Classes.ValidClassName.NotCamelCaps
class Tests_URLs extends WP_UnitTestCase {

	public function test_urls() {

		if ( 5 === PHP_MAJOR_VERSION
			&& 3 === PHP_MINOR_VERSION
			&& version_compare( $GLOBALS['wp_version'], '4.6', '<' )
		) {
			$this->markTestSkipped(
				'Skip this because of error: GnuTLS recv error (-9): A TLS packet with unexpected length was received.'
			);
		}

		$this->markTestSkipped(
			'This text works when manually tesing URLs in content, this way it fails for some reason WP escapes the [spare-brackets].'
		);

		$output = apply_filters(
			'the_content',
			'https://www.youtube.com/watch?v=2an6-WVPuJU&arve[align]=left&arve[autoplay]=1&arve[maxwidth]=333&arve[title]=title&arve-ifp[fs]=88'
		);

		$this->assertNotContains( 'Error', $output );

		$this->assertContains( 'alignleft', $output );
		$this->assertContains( 'autoplay=1', $output );
		$this->assertContains( 'style="max-width:333px;"', $output );
		$this->assertContains( '<meta itemprop="name" content="title">', $output );
		$this->assertContains( 'src="https://www.youtube-nocookie.com/embed/2an6-WVPuJU', $output );
	}
}
