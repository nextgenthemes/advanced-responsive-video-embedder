<?php

use \Nextgenthemes\ARVE;

// phpcs:disable Squiz.Classes.ClassFileName.NoMatch
// phpcs:disable Squiz.Classes.ValidClassName.NotCamelCaps
class Tests_OembedCache extends WP_UnitTestCase {

	public function test_oembed_cache_reset() {

		global $post;

		$post       = $this->factory()->post->create_and_get();
		$url        = 'https://vimeo.com/265932488';
		$key_suffix = md5( $url . serialize( wp_embed_defaults( $url ) ) );
		$cachekey   = '_oembed_' . $key_suffix;

		update_option( 'nextgenthemes_arve', [ 'maxwidth' => '444' ] );

		$actual = $GLOBALS['wp_embed']->shortcode( array(), $url );

		update_option( 'nextgenthemes_arve', [ 'maxwidth' => '555' ] );

		#sleep(1);
		$actual_2 = $GLOBALS['wp_embed']->shortcode( array(), $url );
		$cached_2 = get_post_meta( $post->ID, $cachekey, true );

		update_option( 'nextgenthemes_arve_oembed_recache', time() + 1 );

		$actual_3 = $GLOBALS['wp_embed']->shortcode( array(), $url );
		$cached_3 = get_post_meta( $post->ID, $cachekey, true );

		// Cleanup.
		unset( $post );

		$this->assertContains( '444px', $actual );

		$this->assertContains( '444px', $actual_2 );
		$this->assertContains( '444px', $cached_2 );

		$this->assertContains( '555px', $actual_3 );
		$this->assertContains( '555px', $cached_3 );
	}
}
