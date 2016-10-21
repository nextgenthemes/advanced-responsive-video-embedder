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

		$atts['url']  = 'https://www.youtube.com/watch?v=hRonZ4wP8Ys';
		$atts['mode'] = 'normal';

		$output = arve_shortcode_arve( $atts );

		$this->assertNotContains( 'ARVE Error', $output, $output );
		$this->assertContains( 'data-arve-mode="normal"', arve_shortcode_arve( $atts ) );

		$modes = array( 'lazyload', 'lazyload-lightbox' );

		foreach ( $modes as $key => $mode ) {

			$this->assertContains( 'ARVE Error', $output );
		}
	}

}
