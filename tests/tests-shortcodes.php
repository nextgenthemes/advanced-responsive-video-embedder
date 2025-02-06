<?php

declare(strict_types = 1);

use function Nextgenthemes\ARVE\shortcode;
use function Nextgenthemes\ARVE\get_host_properties;
use function Nextgenthemes\WP\remote_get_body;

// phpcs:disable Squiz.PHP.CommentedOutCode.Found, Squiz.Classes.ClassFileName.NoMatch, Squiz.PHP.Classes.ValidClassName.NotCamelCaps, WordPress.PHP.DevelopmentFunctions.error_log_print_r, WordPress.PHP.DevelopmentFunctions.error_log_error_log
class Tests_Shortcodes extends WP_UnitTestCase {

	public function test_arve_test_sc(): void {

		$html = do_shortcode( '[arve_test]' );

		$this->assertStringNotContainsString( 'Error', $html );
	}

	public function test_sc_overwrite(): void {

		add_filter(
			'nextgenthemes/arve/shortcode_override',
			function () {
				return 'override';
			},
			10,
			2
		);

		$html = shortcode(
			array(
				'url' => 'https://example.com',
			)
		);

		$this->assertStringNotContainsString( 'Error', $html );
		$this->assertStringContainsString( 'override', $html );
	}

	public function test_slashes_url(): void {

		$html = shortcode(
			array(
				'url' => '//example.com',
			)
		);

		$this->assertStringNotContainsString( 'Error', $html );
	}

	public function test_schema_enabled(): void {

		$html = shortcode( array( 'url' => 'https://example.com' ) );

		$this->assertStringNotContainsString( 'Error', $html );
		$this->assertStringContainsString( '<script type="application/ld+json">{"@context":"http:\/\/schema.org\/"', $html );
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function logfile( string $msg, string $file ): void {
		$msg = print_r( $msg, true );
		error_log( $msg . PHP_EOL, 3, "$file.log" );
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function oembed_log( array $a ): array {
		if ( $a['oembed_data'] ) {
			$this->logfile( $a['provider'], __FILE__ );
			$this->logfile( $a['oembed_data'], __FILE__ );
		}
		return $a;
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function check_link( string $provider, string $url ): void {

		if (
			! in_array(
				$provider,
				[
					'bannedvideo', # 403
					'ign', # 403
					'kickstarter', #403
					'rutube', #403
					'mailru', # 404
					'xtube', # nobody uses this
					'youku', # does not like this check
					'brightcove', #timeout
				],
				true
			)
		) {
			$html = remote_get_body( $url, [ 'timeout' => 10 ] );

			if ( is_wp_error( $html ) ) {
				pd( $html );
			}
			$this->assertTrue( ! is_wp_error( $html ) );
		}
	}

	/**
	 * @group api_data
	 */
	public function test_api_data(): void {

		$properties = get_host_properties();

		foreach ( $properties as $provider => $v ) :

			// Fails for some reason
			if ( 'dailymotion' === $provider && getenv( 'CI' ) ) {
				$this->markTestSkipped( 'skipped Dailymotion on Github.' );
				continue;
			}

			if ( empty( $v['tests'] ) ) {
				$this->markTestSkipped( 'no tests for ' . $provider );
				continue;
			}

			$this->assertNotEmpty( $v['tests'] );
			$this->assertTrue( is_array( $v['tests'] ) );

			//phpcs:ignore
			fwrite( STDOUT, PHP_EOL );

			foreach ( $v['tests'] as $key => $test ) {

				//phpcs:ignore
				fwrite( STDOUT, print_r( $test['url'], true ) . PHP_EOL );

				$html = shortcode(
					array(
						'url'  => $test['url'],
						'mode' => 'normal',
					)
				);

				$this->assertStringNotContainsString( 'Error', $html );

				if ( 'html5' !== $provider ) {
					$this->assertStringContainsString( '"embedURL":', $html );
				} else {
					$this->assertStringContainsString( '"contentURL":', $html );
				}

				if ( $v['oembed'] ) {

					$skip_vimeo_on_github = ( 'vimeo' === $provider ) && getenv( 'CI' );

					if ( $skip_vimeo_on_github ) {
						$this->markTestSkipped( 'skipped Vimeo oembed check on Github.' );
					} else {
						$this->assertStringContainsString( 'data-oembed="1"', $html );
					}
				} else {
					$this->assertStringNotContainsString( 'data-oembed="1"', $html );
				}
			}
		endforeach;
	}

	public function test_sandbox(): void {

		$html = shortcode(
			array(
				'url' => 'https://example.com',
			)
		);

		$this->assertStringNotContainsString( 'Error', $html );
		$this->assertStringContainsString( 'sandbox="', $html );

		$html = shortcode(
			array(
				'url'             => 'https://example.com',
				'encrypted_media' => 'y',
			)
		);

		$this->assertStringNotContainsString( 'Error', $html );
		$this->assertStringNotContainsString( 'sandbox="', $html );
	}

	public function test_shortcodes_are_registered(): void {
		$this->assertArrayHasKey( 'arve', $GLOBALS['shortcode_tags'] );
		$this->assertArrayHasKey( 'youtube', $GLOBALS['shortcode_tags'] );
		$this->assertArrayHasKey( 'vimeo', $GLOBALS['shortcode_tags'] );
	}

	public function test_ted_talks_lang(): void {

		$html = shortcode(
			array( 'url' => 'https://www.ted.com/talks/auke_ijspeert_a_robot_that_runs_and_swims_like_a_salamander?language=de' )
		);

		$this->assertStringNotContainsString( 'Error', $html );
		$this->assertStringContainsString(
			'https://embed.ted.com/talks/lang/de/auke_ijspeert_a_robot_that_runs_and_swims_like_a_salamander',
			$html,
			$html
		);
	}

	public function test_attr(): void {

		$output = shortcode(
			array(
				'align'       => 'left',
				'autoplay'    => 'y',
				'description' => '    Description Test   ',
				'maxwidth'    => '333',
				'thumbnail'   => 'https://example.com/image.jpg',
				'title'       => ' Test <title>  ',
				'upload_date' => '2016-10-22',
				'duration'    => 'PT1H2M3S',
				'url'         => 'https://example.com',
				'arve_link'   => 'y',
			)
		);

		$this->assertStringNotContainsString( 'Error', $output );
		$this->assertStringNotContainsString( 'srcset=', $output );

		$this->assertStringContainsString( 'alignleft', $output );
		#$this->assertStringContainsString( 'autoplay=1', $output );
		$this->assertStringContainsString( '"description":', $output );
		$this->assertStringContainsString( 'style="max-width:333px;"', $output );
		$this->assertStringContainsString( '"name":"Test', $output );
		$this->assertStringContainsString( '"uploadDate":"2016-10-22"', $output );
		$this->assertStringContainsString( '"duration":"PT1H2M3S"', $output );
		$this->assertStringContainsString( 'src="https://example.com', $output );
		$this->assertStringContainsString( '<a href="https://nextgenthemes.com/plugins/arve-pro/" title="Powered by Advanced Responsive Video Embedder WordPress plugin" class="arve-promote-link" target="_blank">ARVE</a>', $output );
	}

	public function test_iframe(): void {

		$output = shortcode( array( 'url' => 'https://example.com' ) );

		$this->assertStringNotContainsString( 'Error', $output );
		$this->assertMatchesRegularExpression( '#<iframe .*src="https://example\.com#', $output );
		$this->assertStringContainsString( 'data-provider="iframe"', $output );
	}

	/**
	 * @group regex
	 */
	public function test_regex(): void {

		$properties = \Nextgenthemes\ARVE\get_host_properties();

		$this->assertTrue( is_array( $properties ) );
		$this->assertNotEmpty( $properties );

		foreach ( $properties as $host_id => $host ) :

			$this->assertNotEmpty( $host, $host_id );
			$this->assertTrue( is_array( $host ), $host_id );

			if ( empty( $host['regex'] ) || ! empty( $host['oembed'] ) ) {
				continue;
			}

			$this->assertArrayHasKey( 'tests', $host, $host_id );
			$this->assertNotEmpty( $host['tests'], $host_id );
			$this->assertTrue( is_array( $host['tests'] ), $host_id );

			foreach ( $host['tests'] as $test ) :

				$this->assertNotEmpty( $test, $host_id );
				$this->assertTrue( is_array( $test ), $host_id );
				$this->assertArrayHasKey( 'id', $test, $host_id );
				$this->assertArrayHasKey( 'url', $test, $host_id );

				preg_match( $host['regex'], $test['url'], $matches );

				$this->assertNotEmpty( $matches, $test['url'] );
				$this->assertTrue( is_array( $matches ), $test['url'] );
				$this->assertArrayHasKey( 'id', $test, $test['url'] );
				$this->assertEquals( $matches['id'], $test['id'], $test['url'] );

				if ( 'brightcove' === $host_id ) {
					$this->assertEquals( $matches['account_id'], $test['account_id'] );
					$this->assertEquals( $matches['brightcove_player'], $test['brightcove_player'] );
					$this->assertEquals( $matches['brightcove_embed'], $test['brightcove_embed'] );
				}
			endforeach;

		endforeach;
	}

	/**
	 * Test the extraction of iframes from the provided URL.
	 *
	 * @group iframe-extraction
	 */
	public function test_iframe_extraction(): void {

		$html = shortcode(
			array(
				'url' => '<iframe src="https://example.com" width="640" height="320" frameborder="0" allowfullscreen></iframe>',
			)
		);

		$this->assertStringNotContainsString( 'Error', $html );
		$this->assertStringContainsString( 'aspect-ratio:640/320', $html );

		$p = new \WP_HTML_Tag_Processor( $html );

		$this->assertTrue( $p->next_tag( [ 'class_name' => 'arve' ] ), $html );
		$this->assertEquals( 'iframe', $p->get_attribute( 'data-provider' ) );

		$this->assertTrue( $p->next_tag( 'iframe' ), $html );
		$this->assertEquals( 'https://example.com', $p->get_attribute( 'src' ) );
	}
}
