<?php

declare(strict_types = 1);

use function Nextgenthemes\ARVE\shortcode;

// phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_var_export
class Tests_NoAddons extends WP_UnitTestCase {

	/**
	 * Tests the fallback behavior of the shortcode when the mode is set to 'lazyload' but ARVE Pro is not active.
	 *
	 * @group no-addons
	 */
	public function test_mode_fallback(): void {

		if ( function_exists( 'Nextgenthemes\ARVE\Pro\init' ) ) {
			$this->markTestSkipped( 'This test should is only run when ARVE Pro is not active.' );
		}

		$html = shortcode(
			array(
				'url'  => 'https://example.com',
				'mode' => 'lazyload',
			)
		);

		$this->assertStringContainsStringIgnoringCase( 'Error', $html );
		$this->assertStringContainsString( 'lazyload not available', $html );
		$this->assertStringContainsString( 'data-mode="normal"', $html );
	}

	/**
	 * Tests the fallback behavior of the shortcode when the mode is set to 'lazyload' but ARVE Pro is not active.
	 *
	 * @group yt-seo
	 * @group no-addons
	 */
	public function test_youtube_seo_data(): void {

		if ( function_exists( 'Nextgenthemes\ARVE\Pro\init' ) ) {
			$this->markTestSkipped( 'This test should is only run when ARVE Pro is not active.' );
		}

		$html = shortcode(
			array(
				'url'  => 'https://www.youtube.com/watch?v=hRonZ4wP8Ys',
				'mode' => 'normal',
			)
		);

		$this->assertStringNotContainsStringIgnoringCase( 'Error', $html );
		$json_ld = $this->extract_json_ld( $html );
		$this->assertNotInstanceOf( 'WP_Error', $json_ld );
		$this->assertIsArray( $json_ld );

		$this->assertArrayHasKey( '@id', $json_ld, var_export( $json_ld, true ) );
		$this->assertArrayHasKey( '@context', $json_ld, var_export( $json_ld, true ) );
		$this->assertArrayHasKey( 'type', $json_ld, var_export( $json_ld, true ) );
		$this->assertArrayHasKey( 'embedURL', $json_ld, var_export( $json_ld, true ) );

		$this->assertArrayNotHasKey( 'name', $json_ld, var_export( $json_ld, true ) );
		$this->assertArrayNotHasKey( 'description', $json_ld, var_export( $json_ld, true ) );
	}

	/**
	 * Extracts JSON-LD data from the provided HTML string.
	 *
	 * This function looks for a <script> tag within the HTML that contains JSON-LD data,
	 * attempts to decode it, and returns the resulting array. If decoding fails, it returns
	 * a WP_Error object with the error message.
	 *
	 * @param string $html The HTML content from which to extract JSON-LD data.
	 *
	 * @return array<mixed>|WP_Error The decoded JSON-LD data as an array, or a WP_Error object if decoding fails.
	 */
	public function extract_json_ld( string $html ) {

		$p = new WP_HTML_Tag_Processor( $html );
		$p->next_tag( 'SCRIPT' );

		try {
			$json_ld = json_decode( $p->get_modifiable_text(), true, 2, JSON_THROW_ON_ERROR );
		} catch ( JsonException $e ) {
			$json_ld = new WP_Error( 'json-decode-error', 'JSON Error: ' . $e->getMessage() );
		}

		return $json_ld;
	}
}
