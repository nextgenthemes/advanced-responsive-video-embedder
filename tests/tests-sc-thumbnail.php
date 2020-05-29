<?php
use function \Nextgenthemes\ARVE\shortcode;
use function \Nextgenthemes\ARVE\get_host_properties;

// phpcs:disable Squiz.PHP.CommentedOutCode.Found, Squiz.Classes.ClassFileName.NoMatch, Squiz.PHP.Classes.ValidClassName.NotCamelCaps, WordPress.PHP.DevelopmentFunctions.error_log_print_r, WordPress.PHP.DevelopmentFunctions.error_log_error_log
class Tests_ShortcodeThumbnail extends WP_UnitTestCase {

	public function test_thumbnails() {

		$filename = dirname( __FILE__ ) . '/test-attachment.jpg';
		// phpcs:disable WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$contents = file_get_contents( $filename );
		// phpcs:enable WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

		$upload = wp_upload_bits( basename( $filename ), null, $contents );
		$this->assertTrue( empty( $upload['error'] ) );

		$attachment_id = parent::_make_attachment( $upload );

		$attr = [
			'url'       => 'https://example.com/video.mp4',
			'thumbnail' => (string) $attachment_id,
			'title'     => 'Something',
		];

		$this->assertRegExp( '#<meta itemprop="thumbnailUrl" content=".*test-attachment#', shortcode( $attr ) );

		$attr = [
			'url'       => 'https://example.com/video2.mp4',
			'thumbnail' => 'https://example.com/image.jpg',
		];

		$this->assertContains( '<meta itemprop="thumbnailUrl" content="https://example.com/image.jpg"', shortcode( $attr ) );
	}
}
