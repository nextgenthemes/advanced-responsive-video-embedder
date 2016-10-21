<?php


/**
 * @group edd_shortcode
 */
class Tests_Shortcode extends WP_UnitTestCase {

	protected $_payment_id = null;

	protected $_post = null;

	protected $_payment_key = null;

	public function setUp() {
		parent::setUp();

		#$this->_user_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
		#wp_set_current_user( $this->_user_id );

		#$post_id = $this->factory->post->create( array( 'post_title' => 'ARVE Shortcode Test', 'post_status' => 'publish' ) );
	}

	public function tearDown() {
		parent::tearDown();
	}

	public function test_shortcodes_are_registered() {
		global $shortcode_tags;

		$this->assertArrayHasKey( 'arve', $shortcode_tags );
		$this->assertArrayHasKey( 'youtube', $shortcode_tags );
		$this->assertArrayHasKey( 'vimeo', $shortcode_tags );
	}

	public function test_arve_shortcode() {

		$arve_shortcode = arve_shortcode_arve( array(
			'url' => 'https://www.youtube.com/watch?v=hRonZ4wP8Ys'
		) );

		$old_shortcode = arve_shortcode_arve( array(
			'provider' => 'youtube',
			'id'       => 'hRonZ4wP8Ys',
		) );

		$this->assertEquals( $arve_shortcode, $old_shortcode );
	}

	public function test_modes() {

		$output = arve_shortcode_arve( array( 'url' => 'https://www.youtube.com/watch?v=hRonZ4wP8Ys' ) );

		$this->assertNotContains( 'ARVE Error', $output, $output );
		$this->assertContains( 'data-arve-mode="normal"', $output );

		$modes = array( 'lazyload', 'lazyload-lightbox' );

		foreach ( $modes as $key => $mode ) {

			$output = arve_shortcode_arve( array( 'url' => 'https://www.youtube.com/watch?v=hRonZ4wP8Ys', 'mode' => $mode ) );
			$this->assertContains( 'Error', $output );
		}
	}

	public function test_html5() {

		$html5_ext = array( 'mp4', 'm4v', 'webm', 'ogv' );

		foreach ( $html5_ext as $ext ) {

			$output = arve_shortcode_arve( array( 'url' => 'https://example.com/video.' . $ext ) );

			$this->assertNotContains( 'Error', $output, $output );
			$this->assertNotContains( '<iframe', $output, $output );
			$this->assertContains( 'data-arve-provider="html5"', $output );
			$this->assertContains( '<video', $output );
		}
	}

	public function test_regex() {

		$properties = arve_get_host_properties();

		foreach( $properties as $provider => $host_props ) :

	    if ( empty( $host_props['test_urls'] ) || empty( $host_props['regex'] ) ) {
	      continue;
	    }

	    foreach( $host_props['test_urls'] as $urltest ) {

	      if ( ! is_array( $urltest ) ) {
	        continue;
	      }

	      $url_to_test = $urltest[0];
	      $expected_id = $urltest[1];

	      preg_match( '#' . $host_props['regex'] . '#i', $url_to_test, $matches );

	      $this->assertEquals( $matches[1], $expected_id );
	    }

	  endforeach;
	}

}
