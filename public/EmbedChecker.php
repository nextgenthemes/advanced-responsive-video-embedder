<?php
namespace Nextgenthemes\ARVE;

class EmbedChecker {

	public $shortcode_args = [];

	public function __construct( array $shortcode_args ) {
		$this->shortcode_args = $shortcode_args;
	}

	public function check() {

		add_filter( 'nextgenthemes/arve/oembed2args', [ $this, 'oembed2args' ] );
		$maybe_arve_html = $GLOBALS['wp_embed']->shortcode( $this->shortcode_args, $this->shortcode_args['url'] );
		remove_filter( 'nextgenthemes/arve/oembed2args', [ $this, 'oembed2args' ] );

		if ( false !== strpos( $maybe_arve_html, 'class="arve' ) ) {
			return $maybe_arve_html;
		};

		return false;
	}

	public function oembed2args( array $shortcode_args ) {
		$shortcode_args = array_merge( $shortcode_args, $this->shortcode_args );
		return $shortcode_args;
	}
}
