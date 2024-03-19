<?php
use function Nextgenthemes\ARVE\shortcode;
use function Nextgenthemes\ARVE\get_host_properties;
use function Nextgenthemes\WP\remote_get_body;

// phpcs:disable Squiz.PHP.CommentedOutCode.Found, Squiz.Classes.ClassFileName.NoMatch, Squiz.PHP.Classes.ValidClassName.NotCamelCaps, WordPress.PHP.DevelopmentFunctions.error_log_print_r, WordPress.PHP.DevelopmentFunctions.error_log_error_log
class Tests_Blocks extends WP_UnitTestCase {

	/**
	 * @group blocks
	 */
	public function test_class_and_title(): void {

		try {
			$html = do_blocks( '<!-- wp:nextgenthemes/arve-block {"url":"https://example.com","title":"Block Testing Title","mode":"normal","className":"extra-cls extra-cls-two"} /-->' );
		} catch ( \Exception $e ) {
			// We need this for WP 6.2 and PHP >= 8.3
			$this->assertStringContainsString( 'Calling get_class() without arguments is deprecated', $e->getMessage() );
		}

		$this->assertStringNotContainsString( 'Error', $html );
		$this->assertStringContainsString( 'extra-cls extra-cls-two', $html );
		$this->assertStringContainsString( 'Block Testing Title', $html );
	}

	/**
	 * Test that the aspect ratio classes are removed
	 *
	 * @group blocks
	 */
	public function test_aspect_ratio_class_removals(): void {

		// we need the_content filter or the URL will not be transformed to embed code
		$html = apply_filters(
			'the_content',
			'<!-- wp:embed {"url":"https://www.youtube.com/watch?v=c7M4mBVgP3Y","type":"video","providerNameSlug":"youtube","responsive":true,"className":"wp-embed-aspect-16-9 wp-has-aspect-ratio"} -->
			<figure class="wp-block-embed is-type-video is-provider-youtube wp-block-embed-youtube wp-embed-aspect-16-9 wp-has-aspect-ratio"><div class="wp-block-embed__wrapper">
			https://www.twitch.tv/imaqtpie
			</div></figure>
			<!-- /wp:embed -->'
		);

		$this->assertStringNotContainsString( 'Error', $html );
		$this->assertStringNotContainsString( 'wp-has-aspect-ratio', $html );
		$this->assertStringNotContainsString( 'wp-embed-aspect-16-9', $html );
		$this->assertStringNotContainsString( 'wp-block-embed__wrapper', $html );
		$this->assertStringContainsString( 'arve-embed', $html );
		$this->assertStringContainsString( 'data-provider="twitch"', $html );
	}
}
