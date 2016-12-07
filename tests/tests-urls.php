<?php


class Tests_URLs extends WP_UnitTestCase {

	public function test_urls() {

    $output = apply_filters( 'the_content', 'https://www.youtube.com/watch?v=2an6-WVPuJU&arve[align]=left&arve[autoplay]=1&arve[maxwidth]=333&arve[title]=title' );

		$this->assertNotContains( 'Error', $output );

		$this->assertContains( 'alignleft', $output );
		$this->assertContains( 'autoplay=1', $output );
		$this->assertContains( 'style="max-width: 333px;"', $output );
		$this->assertContains( '<meta itemprop="name" content="title">', $output );
		$this->assertContains( 'src="https://www.youtube-nocookie.com/embed/2an6-WVPuJU', $output );
  }
}
