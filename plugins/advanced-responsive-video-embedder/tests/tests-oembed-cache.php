<?php

use \Nextgenthemes\ARVE;
use \Nextgenthemes\ARVE\Common;

// phpcs:disable Squiz.Classes.ClassFileName.NoMatch
// phpcs:disable Squiz.Classes.ValidClassName.NotCamelCaps
// phpcs:disable WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize
class Tests_OembedCache extends WP_UnitTestCase {

	public $check = 'data-arve-oembed';

	public function replace_with_arveunit1( $result, $data, $url ) {
		return str_replace( 'data-arve-oembed', 'arveunit1', $result );
	}

	public function replace_with_arveunit2( $result, $data, $url ) {
		return str_replace( 'data-arve-oembed', 'arveunit2', $result );
	}

	public function replace_script1( $result, $data, $url ) {
		return str_replace( 'script', 'arveunit1', $result );
	}

	public function replace_script2( $result, $data, $url ) {
		return str_replace( 'script', 'arveunit2', $result );
	}

	public function ttest_oembed_cache_rebuild() {
		global $post;

		add_filter( 'oembed_dataparse', [ $this, 'replace_with_arveunit1' ], 15, 3 );

		$post       = $this->factory()->post->create_and_get();
		$url        = 'https://vimeo.com/265932488';
		$key_suffix = md5( $url . serialize( wp_embed_defaults( $url ) ) );
		$cachekey   = '_oembed_' . $key_suffix;

		// build cache
		update_option( 'nextgenthemes_arve_oembed_recache', 1 );
		$actual = $GLOBALS['wp_embed']->shortcode( array(), $url );
		$this->assertContains( 'arveunit1', $actual, 'build-cache' );

		remove_filter( 'oembed_dataparse', [ $this, 'replace_with_arveunit1' ], 15 );
		add_filter( 'oembed_dataparse', [ $this, 'replace_with_arveunit2' ], 15, 3 );

		// retrieve cache - should NOT contain arve-cache-change1
		$actual_2 = $GLOBALS['wp_embed']->shortcode( array(), $url );
		$cache_2  = get_post_meta( $post->ID, $cachekey, true );
		$this->assertContains( 'arveunit1', $actual_2, 'Shortcode' );
		$this->assertContains( 'arveunit1', $cache_2, 'Cache' );

		// trigger cache rebuild - should contain option change
		update_option( 'nextgenthemes_arve_oembed_recache', time() + 99 );
		$actual_3 = $GLOBALS['wp_embed']->shortcode( array(), $url );
		$cache_3  = get_post_meta( $post->ID, $cachekey, true );
		$this->assertContains( 'arveunit2', $actual_3 );
		$this->assertContains( 'arveunit2', $cache_3 );

		// Cleanup.
		unset( $post );
	}

	public function test_oembed_cache_delete() {
		global $post;

		add_filter( 'oembed_dataparse', [ $this, 'replace_script1' ], 15, 3 );

		$post       = $this->factory()->post->create_and_get();
		$url        = 'https://vimeo.com/254034878';
		$key_suffix = md5( $url . serialize( wp_embed_defaults( $url ) ) );
		$cachekey   = '_oembed_' . $key_suffix;

		// build cache
		update_option( 'nextgenthemes_arve_oembed_recache', 1 );
		$actual = $GLOBALS['wp_embed']->shortcode( array(), $url );
		$this->assertContains( 'arveunit1', $actual, 'build-cache' );

		// trigger cache rebuild by deleting cache
		remove_filter( 'oembed_dataparse', [ $this, 'replace_script1' ], 15 );
		add_filter( 'oembed_dataparse', [ $this, 'replace_script2' ], 15, 3 );

		$this->assertNotEmpty( $this->check_db_for_oembed_cache() );
		ARVE\delete_oembed_cache();
		$this->assertEmpty( $this->check_db_for_oembed_cache() );

		$actual_3 = $GLOBALS['wp_embed']->shortcode( array(), $url );
		$cache_3  = get_post_meta( $post->ID, $cachekey, true );
		$this->assertContains( 'arveunit2', $actual_3, 'Shortcode' );
		$this->assertContains( 'arveunit2', $cache_3, 'Cache' );

		// Cleanup.
		unset( $post );
	}

	public function check_db_for_oembed_cache() {
		global $wpdb;
		return $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$wpdb->postmeta} WHERE meta_key LIKE %s AND meta_value LIKE %s",
				'%_oembed_%',
				'%' . $wpdb->esc_like( 'data-arve-oembed' ) . '%'
			)
		);
	}
}
