<?php

/**
 * Advanced_Responsive_Video_Embedder.
 *
 * @package   Advanced_Responsive_Video_Embedder
 * @author    Nicolas Jonas
 * @license   GPL-3.0
 * @link      http://nextgenthemes.com
 * @copyright Copyright (c) 2014 Nicolas Jonas
 */
class Advanced_Responsive_Video_Embedder_Create_Shortcodes {

	/**
	 *
	 * @since    4.4.0
	 */	
	function __construct( $provider ) {

		$arve = Advanced_Responsive_Video_Embedder::get_instance();
		$this->options = $arve->get_options();

		$this->provider = $provider;

		$this->create_shortcode();
	}

	/**
	 *
	 * @since    2.6.0
	 */	
	public function create_shortcode() {

		add_shortcode( $this->options['shortcodes'][ $this->provider ], array( $this, 'shortcode' ) );
	}

	/**
	 *
	 * @since    4.4.0
	 */
	public function shortcode( $atts ) {

		$shortcode_atts = shortcode_atts( array(
			'align'        => '',
			'autoplay'     => '',
			'aspect_ratio' => '',
			'end'          => '',
			'id'           => '',
			'maxw'         => '',
			'maxwidth'     => '',
			'mode'         => '',
			'parameters'   => '',
			'start'        => '',
		), $atts );

		$arve = Advanced_Responsive_Video_Embedder::get_instance();

		return $arve->build_embed( $this->provider, $shortcode_atts );
	}
}