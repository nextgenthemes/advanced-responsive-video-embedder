<?php

declare(strict_types = 1);

use function Nextgenthemes\ARVE\tracks_html;
use function Nextgenthemes\ARVE\html_id;
use function Nextgenthemes\ARVE\remove_embed_block_aspect_ratio;
use function Nextgenthemes\ARVE\error_wrap;
use function Nextgenthemes\ARVE\debug_pre;

class Tests_Html_Output extends WP_UnitTestCase {

	public function test_tracks_html_multiple_tracks(): void {

		$tracks = [
			[
				'default' => true,
				'kind'    => 'subtitles',
				'label'   => 'English',
				'src'     => 'https://example.com/en.vtt',
				'srclang' => 'en',
			],
			[
				'default' => false,
				'kind'    => 'subtitles',
				'label'   => 'German',
				'src'     => 'https://example.com/de.vtt',
				'srclang' => 'de',
			],
		];

		$html = tracks_html( $tracks );

		$this->assertStringContainsString( 'https://example.com/en.vtt', $html );
		$this->assertStringContainsString( 'https://example.com/de.vtt', $html );
		$this->assertStringContainsString( 'srclang="en"', $html );
		$this->assertStringContainsString( 'srclang="de"', $html );
	}

	public function test_html_id_already_present(): void {

		$result = html_id( 'class="video" id="custom"' );
		$this->assertStringContainsString( 'id="custom"', $result );
		$this->assertStringNotContainsString( 'id="html"', $result );
	}

	public function test_html_id_appends_when_missing(): void {

		$result = html_id( 'class="video"' );
		$this->assertStringContainsString( 'id="html"', $result );
		$this->assertStringContainsString( 'class="video"', $result );
	}

	public function test_html_id_empty_string(): void {

		$result = html_id( '' );
		$this->assertSame( ' id="html"', $result );
	}

	public function test_remove_embed_block_aspect_ratio_no_arve(): void {

		$input = '<figure class="wp-block-embed is-type-video"><div class="wp-block-embed__wrapper">https://example.com/video</div></figure>';

		$this->assertSame( $input, remove_embed_block_aspect_ratio( $input ) );
	}

	public function test_remove_embed_block_aspect_ratio_with_arve(): void {

		$input  = '<figure class="wp-block-embed is-type-video arve-embed wp-has-aspect-ratio"><div class="wp-block-embed__wrapper wp-embed-aspect-16-9">https://example.com/video</div></figure>';
		$output = remove_embed_block_aspect_ratio( $input );

		$this->assertStringNotContainsString( 'wp-has-aspect-ratio', $output );
		$this->assertStringContainsString( 'display: contents;', $output );
	}

	public function test_error_wrap_returns_string(): void {

		$result = error_wrap( 'Test error', 'test-code' );

		$this->assertStringContainsString( 'Test error', $result );
		$this->assertStringContainsString( 'arve-error', $result );
	}

	public function test_debug_pre_dark(): void {

		$result = debug_pre( 'var_dump content', true );

		$this->assertStringContainsString( 'arve-debug--dark', $result );
		$this->assertStringContainsString( 'var_dump content', $result );
	}

	public function test_debug_pre_light(): void {

		$result = debug_pre( 'info' );

		$this->assertStringNotContainsString( 'arve-debug--dark', $result );
		$this->assertStringContainsString( 'info', $result );
	}
}
