<?php

use function \Nextgenthemes\ARVE\shortcode;

// phpcs:disable Squiz.Classes.ClassFileName.NoMatch
// phpcs:disable Squiz.Classes.ValidClassName.NotCamelCaps
// phpcs:disable WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize
class Tests_OembedD extends WP_UnitTestCase {

	public function oembed_debug() {

		// $html = shortcode(
		// 	[
		// 		'url' => 'https://youtu.be/YPCUKFooqcA',
		// 		'oembed_debug' => true,
		// 	]
		// );

		$html = shortcode(
			[
				'url'          => 'https://youtu.be/YPCUKFooqcA',
				'oembed_debug' => true,
			]
		);

		$this->assertNotContains( 'Error', $html );
		$this->assertContains( 'nnn', $html );
	}
}
