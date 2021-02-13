<?php
use function \Nextgenthemes\ARVE\shortcode;
use function \Nextgenthemes\ARVE\get_host_properties;

// phpcs:disable Squiz.PHP.CommentedOutCode.Found, Squiz.Classes.ClassFileName.NoMatch, Squiz.PHP.Classes.ValidClassName.NotCamelCaps, WordPress.PHP.DevelopmentFunctions.error_log_print_r, WordPress.PHP.DevelopmentFunctions.error_log_error_log
class Tests_Shortcode extends WP_UnitTestCase {

	public function test_arve_test_sc() {

		$html = do_shortcode( '[arve_test]' );

		$this->assertNotContains( 'Error', $html );
	}

	public function test_sc_overwrite() {

		add_filter(
			'nextgenthemes/arve/shortcode_override',
			function() {
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

		$this->assertNotContains( 'Error', $html );
		$this->assertContains( 'override', $html );
	}

	public function test_slashes_url() {

		$html = shortcode(
			array(
				'url' => '//example.com',
			)
		);

		$this->assertNotContains( 'Error', $html );
	}

	public function test_schema_enabled() {

		$html = shortcode( array( 'url' => 'https://example.com' ) );

		$this->assertNotContains( 'Error', $html );
		$this->assertContains( 'schema.org', $html );
		$this->assertContains( 'itemscope', $html );
		$this->assertContains( 'itemtype', $html );
		$this->assertContains( 'itemprop', $html );
	}

	public function logfile( $msg, $file ) {
		$msg = print_r( $msg, true );
		error_log( $msg . PHP_EOL, 3, "$file.log" );
	}

	public function oembed_log( $a ) {
		if ( $a['oembed_data'] ) {
			$this->logfile( $a['provider'], __FILE__ );
			$this->logfile( $a['oembed_data'], __FILE__ );
		}
		return $a;
	}

	public function test_api_data() {

		$properties = get_host_properties();

		// if ( ! getenv('CI') ) {
		// 	add_filter( 'shortcode_atts_arve', [ $this, 'oembed_log' ], 999 );
		// }

		foreach ( $properties as $provider => $v ) :

			// TODO: This generates a error on symphony/yaml
			if ( version_compare( $GLOBALS['wp_version'], '5.2.9', '<=' ) &&
				'dailymotion' === $provider
			) {
				continue;
			}

			if ( empty( $v['tests'] ) ) {
				continue;
			}

			$this->assertNotEmpty( $v['tests'] );
			$this->assertTrue( is_array( $v['tests'] ) );

			//phpcs:ignore
			fwrite( STDOUT, PHP_EOL );

			foreach ( $v['tests'] as $key => $test ) {

				//phpcs:ignore
				fwrite( STDOUT, print_r($test['url'], true) . PHP_EOL );
				$html = shortcode(
					array(
						'url'  => $test['url'],
						'mode' => 'normal',
					)
				);

				$this->assertNotContains( 'Error', $html );

				if ( 'html5' !== $provider ) {
					$this->assertContains( 'itemprop="embedURL', $html );
				} else {
					$this->assertContains( 'itemprop="contentURL', $html );
				}
			}
		endforeach;
	}

	public function test_sandbox() {

		$html = shortcode(
			array(
				'url' => 'https://example.com',
			)
		);

		$this->assertNotContains( 'Error', $html );
		$this->assertContains( 'sandbox="', $html );

		$html = shortcode(
			array(
				'url'     => 'https://example.com',
				'sandbox' => 'no',
			)
		);

		$this->assertNotContains( 'Error', $html );
		$this->assertNotContains( 'sandbox="', $html );
	}

	public function test_shortcodes_are_registered() {
		$this->assertArrayHasKey( 'arve', $GLOBALS['shortcode_tags'] );
		$this->assertArrayHasKey( 'youtube', $GLOBALS['shortcode_tags'] );
		$this->assertArrayHasKey( 'vimeo', $GLOBALS['shortcode_tags'] );
	}

	public function test_ted_talks_lang() {

		$html = shortcode(
			array( 'url' => 'https://www.ted.com/talks/auke_ijspeert_a_robot_that_runs_and_swims_like_a_salamander?language=de' )
		);

		$this->assertNotContains( 'Error', $html );
		$this->assertContains(
			'https://embed.ted.com/talks/lang/de/auke_ijspeert_a_robot_that_runs_and_swims_like_a_salamander',
			$html,
			$html
		);
	}

	public function test_attr() {

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

		$this->assertNotContains( 'Error', $output );

		$this->assertContains( 'alignleft', $output );
		#$this->assertContains( 'autoplay=1', $output );
		$this->assertContains( '<meta itemprop="description" content="Description Test">', $output );
		$this->assertContains( 'style="max-width:333px;"', $output );
		$this->assertContains( '<meta itemprop="name" content="Test &lt;title&gt;">', $output );
		$this->assertContains( '<meta itemprop="uploadDate" content="2016-10-22">', $output );
		$this->assertContains( '<meta itemprop="duration" content="PT1H2M3S">', $output );
		$this->assertContains( 'src="https://example.com', $output );
		$this->assertContains( '<a href="https://nextgenthemes.com/plugins/arve-pro/" title="Powered by ARVE Advanced Responsive Video Embedder WordPress plugin" class="arve-promote-link" target="_blank">ARVE</a>', $output );
	}

	public function test_iframe() {

		$output = shortcode( array( 'url' => 'https://example.com' ) );

		$this->assertNotContains( 'Error', $output );
		$this->assertRegExp( '#<iframe .*src="https://example\.com#', $output );
		$this->assertContains( 'data-provider="iframe"', $output );
	}

	public function test_regex() {

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
}