<?php

use \Nextgenthemes\ARVE;
use \Nextgenthemes\ARVE\Common;


// phpcs:disable Squiz.Classes.ClassFileName.NoMatch
// phpcs:disable Squiz.Classes.ValidClassName.NotCamelCaps
// phpcs:disable WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize
class Tests_OembedCache extends WP_UnitTestCase {

	public function test_oembed_cache_rebuild() {
		global $post;

		$post       = $this->factory()->post->create_and_get();
		$url        = 'https://vimeo.com/265932488';
		$key_suffix = md5( $url . serialize( wp_embed_defaults( $url ) ) );
		$cachekey   = '_oembed_' . $key_suffix;

		// build cache
		update_option( 'nextgenthemes_arve_oembed_recache', 1 );
		update_option( 'nextgenthemes_arve', array( 'maxwidth' => '444' ) );
		$actual = $GLOBALS['wp_embed']->shortcode( array(), $url );
		$this->assertContains( '444px', $actual );

		// retrieve cache - should NOT contain option change
		update_option( 'nextgenthemes_arve', array( 'maxwidth' => '555' ) );
		$actual_2 = $GLOBALS['wp_embed']->shortcode( array(), $url );
		$cache_2  = get_post_meta( $post->ID, $cachekey, true );
		$this->assertContains( '444px', $actual_2 );
		$this->assertContains( '444px', $cache_2 );

		// trigger cache rebuild - should contain option change
		update_option( 'nextgenthemes_arve_oembed_recache', time() + 99 );
		$actual_3 = $GLOBALS['wp_embed']->shortcode( array(), $url );
		$cache_3  = get_post_meta( $post->ID, $cachekey, true );
		$this->assertContains( '555px', $actual_3 );
		$this->assertContains( '555px', $cache_3 );

		// Cleanup.
		unset( $post );
	}

	public function test_oembed_cache_delete() {
		global $post;

		$post       = $this->factory()->post->create_and_get();
		$url        = 'https://vimeo.com/265932488';
		$key_suffix = md5( $url . serialize( wp_embed_defaults( $url ) ) );
		$cachekey   = '_oembed_' . $key_suffix;

		// build cache
		update_option( 'nextgenthemes_arve_oembed_recache', 1 );
		update_option( 'nextgenthemes_arve', array( 'maxwidth' => '444' ) );
		$actual = $GLOBALS['wp_embed']->shortcode( array(), $url );
		$this->assertContains( '444px', $actual );

		update_option( 'nextgenthemes_arve', array( 'maxwidth' => '555' ) );

		// trigger cache rebuild by deleting cache
		$this->assertNotEmpty( $this->check_db_for_oembed_cache() );
		ARVE\delete_oembed_cache();
		$this->assertEmpty( $this->check_db_for_oembed_cache() );

		// TODO, why is this not working in above test?
		$actual_4 = $GLOBALS['wp_embed']->shortcode( array(), $url );
		$cache_4  = get_post_meta( $post->ID, $cachekey, true );
		$this->assertContains( '555px', $actual_4 );
		$this->assertContains( '555px', $cache_4 );

		// Cleanup.
		unset( $post );
	}

	public function check_db_for_oembed_cache() {
		global $wpdb;
		return $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$wpdb->postmeta} WHERE meta_key LIKE %s AND (meta_value LIKE %s OR meta_value LIKE %s)",
				'%_oembed_%',
				'%' . $wpdb->esc_like( 'id="arve' ) . '%',
				'%' . $wpdb->esc_like( 'Advanced Responsive Video Embedder' ) . '%'
			)
		);
	}
}
