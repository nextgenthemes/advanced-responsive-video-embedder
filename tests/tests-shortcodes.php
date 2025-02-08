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

	public function host_properties(): array {

		foreach ( get_host_properties() as $provider => $provider_data ) {
			$data[] = [ $provider, $provider_data ];
		}

		return $data;
	}
	/**
	 * @group missing-data
	 * @dataProvider host_properties
	 */
	public function test_missing_data( string $provider, array $d ): void {
		$this->assertNotEmpty( $d['tests'] );
		$this->assertIsArray( $d['tests'] );

		$no_need_for_regex = in_array( $provider, [ 'html5', 'iframe' ], true );
		$no_id_detection   = in_array( $provider, [ 'rumble', 'tiktok' ], true );

		if ( ! $no_need_for_regex && ! $no_id_detection ) {
			$this->assertNotEmpty( $d['regex'] );
		}
	}

	/**
	 * Provides test data for video providers.
	 */
	public function url_test_data(): array {

		foreach ( get_host_properties() as $provider => $provider_data ) {

			foreach ( $provider_data['tests'] as $test_data ) {

				$data[] = [
					'provider' => $provider,
					'oembed'   => $provider_data['oembed'] ?? false,
					'url'      => $test_data['url'],
				];
			}
		}

		return $data;
	}
	/**
	 * @group api
	 * @dataProvider url_test_data
	 */
	public function test_api_data( string $provider, bool $oembed, string $url ): void {

		$html = shortcode(
			array(
				'url'  => $url,
				'mode' => 'normal',
			)
		);

		$this->assertStringNotContainsString( 'Error', $html );

		if ( 'html5' === $provider ) {
			$this->assertStringContainsString( '"contentURL":', $html );
		} else {
			$this->assertStringContainsString( '"embedURL":', $html );
		}

		#$vimeo_on_github = ( 'vimeo' === $provider ) && getenv( 'CI' );

		if ( $oembed ) {

			if ( 'vimeo' === $provider ) {
				$this->markTestSkipped( 'skipped Vimeo oembed check for now as it fails in PHPUNIT for some reason.' );
			}

			$this->assertStringContainsString( 'data-oembed="1"', $html );
		} else {
			$this->assertStringNotContainsString( 'data-oembed="1"', $html );
		}
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
		$this->assertStringContainsString( '<a href="https://nextgenthemes.com/plugins/arve-pro/" title="Powered by Advanced Responsive Video Embedder WordPress plugin" target="_blank">ARVE</a>', $output );
	}

	public function test_iframe(): void {

		$output = shortcode( array( 'url' => 'https://example.com' ) );

		$this->assertStringNotContainsString( 'Error', $output );
		$this->assertMatchesRegularExpression( '#<iframe .*src="https://example\.com#', $output );
		$this->assertStringContainsString( 'data-provider="iframe"', $output );
	}

	/**
	 * Provides test data for video providers.
	 */
	public function regex_test_data(): array {

		foreach ( get_host_properties() as $provider => $provider_data ) {

			if ( ! isset( $provider_data['regex'] ) ) {
				continue;
			}

			foreach ( $provider_data['tests'] as $test_data ) {

				$data[] = [
					'regex'             => $provider_data['regex'],
					'url'               => $test_data['url'],
					'id'                => $test_data['id'],
					'account_id'        => $test_data['account_id'] ?? null,
					'brightcove_player' => $provider_data['brightcove_player'] ?? null,
					'brightcove_embed'  => $provider_data['brightcove_embed'] ?? null,
				];
			}
		}

		return $data;
	}
	/**
	 * @dataProvider regex_test_data
	 * @group regex
	 */
	public function test_regex(
		string $regex,
		string $url,
		string $id,
		?string $account_id,
		?string $brightcove_player,
		?string $brightcove_embed
	): void {

		preg_match( $regex, $url, $matches );

		$this->assertNotEmpty( $matches, $url );
		$this->assertArrayHasKey( 'id', $matches, $url );
		$this->assertEquals( $matches['id'], $id, $url );

		if ( $account_id ) {
			$this->assertEquals( $matches['account_id'], $account_id );
		}

		if ( $brightcove_player && $brightcove_embed ) {
			$this->assertEquals( $matches['brightcove_player'], $brightcove_player );
			$this->assertEquals( $matches['brightcove_embed'], $brightcove_embed );
		}
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

		$p = new WP_HTML_Tag_Processor( $html );

		$this->assertTrue( $p->next_tag( [ 'class_name' => 'arve' ] ), $html );
		$this->assertEquals( 'iframe', $p->get_attribute( 'data-provider' ) );

		$this->assertTrue( $p->next_tag( 'iframe' ), $html );
		$this->assertEquals( 'https://example.com', $p->get_attribute( 'src' ) );
	}
}
