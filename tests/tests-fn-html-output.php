<?php

declare(strict_types = 1);

class Tests_Frontend extends WP_UnitTestCase {

	public function test_global_id_on_html(): void {
		$this->assertStringContainsString( 'id="html"', get_language_attributes() );
	}
}
