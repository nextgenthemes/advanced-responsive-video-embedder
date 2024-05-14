<?php
use function Nextgenthemes\ARVE\shortcode;

class Tests_ShortcodeVimeo extends WP_UnitTestCase {

	public function test_vimeo_time_and_sandbox(): void {

		$html = shortcode(
			array(
				'url' => 'https://vimeo.com/124400795#t=33',
			)
		);

		$this->assertStringNotContainsString( 'Error', $html );
		$this->assertStringNotContainsString( 'referrerpolicy', $html );
		$this->assertMatchesRegularExpression( '@src="https://player.vimeo.com/.*#t=33"@', $html );
		$this->assertStringContainsString( 'allow-forms', $html );
	}
}
