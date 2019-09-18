<?php
namespace Nextgenthemes\ARVE\Common;

function get_url_arg( $url, $arg ) {
	$parts = parse_url( $url );
	parse_str( $parts['query'], $query );
	return empty( $query[ $arg ] ) ? '' : $query[ $arg ];
}

function get_constant( $const_name ) {
	return defined( $const_name ) ? constant( $const_name ) : false;
}

function is_wp_debug() {
	return get_constant( 'WP_DEBUG' );
}

function get_array_key_by_value( $array, $field, $value ) {

	foreach ( $array as $key => $array_value ) {

		if ( $array_value[ $field ] === $value ) {
			return $key;
		}
	}

	return false;
}

function products() {

	$products = array(
		'arve_pro'          => array(
			'namespace' => 'ARVE\Pro',
			'name'      => 'ARVE Pro',
			'id'        => 1253,
			'type'      => 'plugin',
			'author'    => 'Nicolas Jonas',
			'url'       => 'https://nextgenthemes.com/plugins/arve-pro/',
		),
		'arve_amp'          => array(
			'namespace' => 'ARVE\AMP',
			'name'      => 'ARVE AMP',
			'id'        => 16941,
			'type'      => 'plugin',
			'author'    => 'Nicolas Jonas',
			'url'       => 'https://nextgenthemes.com/plugins/arve-amp/',
		),
		'arve_random_video' => array(
			'namespace' => 'ARVE\RandomVideo',
			'name'      => 'ARVE Random Video',
			'id'        => 31933,
			'type'      => 'plugin',
			'author'    => 'Nicolas Jonas',
			'url'       => 'https://nextgenthemes.com/plugins/arve-random-video/',
		)
	);

	return apply_filters( 'nextgenthemes_products', $products );
}

function setup_licensing() {

	$products = products();

	foreach ( $products as $key => $value ) {
		$settings[ $key ] = [
			'default' => '',
			'option'  => true,
			'tag'     => 'main',
			// translators: %s is Product name
			'label'   => sprintf( esc_html__( '%s license Key', 'advanced-responsive-video-embedder' ), $value['name'] ),
			'type'    => 'string'
		];

		$settings[ $key . '_status' ] = [
			'default' => '',
			'option'  => true,
			'tag'     => 'main',
			// translators: %s is Product name
			'label'   => sprintf( esc_html__( '%s license Status', 'advanced-responsive-video-embedder' ), $value['name'] ),
			'type'    => 'string'
		];
	}

	$settings_instance = new Admin\Setup(
		[
			'namespace'           => __NAMESPACE__ . '__ll_one',
			'settings'            => $settings,
			'menu_title'          => esc_html__( 'NGT Licenses', 'advanced-responsive-video-embedder' ),
			'settings_page_title' => esc_html__( 'NGT Licenses', 'advanced-responsive-video-embedder' )
		]
	);

	$licenses          = Licenses::get_instance();
	$licenses->options = $settings_instance->options;

	#d( Licenses::get_instance()->options );

	#$key = get_key( 'arve-pro', 'option_only' );

	#ddd( $key );

	#d( Admin\api_action( 1253, '2b1b213fc5f86e6cc45eb51731af4138' ) );
}
