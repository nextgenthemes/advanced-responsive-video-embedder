<?php
use function \Nextgenthemes\ARVE\shortcode;
use function \Nextgenthemes\ARVE\get_host_properties;

// phpcs:disable Squiz.PHP.CommentedOutCode.Found, Squiz.Classes.ClassFileName.NoMatch, Squiz.PHP.Classes.ValidClassName.NotCamelCaps, WordPress.PHP.DevelopmentFunctions.error_log_print_r, WordPress.PHP.DevelopmentFunctions.error_log_error_log, WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
class Tests_ShortcodeThumbnail extends WP_UnitTestCase {

	public function test_thumbnail_by_upload_id() {
		$filename = \Nextgenthemes\ARVE\PLUGIN_DIR . '/.wordpress-org/icon-128x128.png';
		$contents = file_get_contents( $filename );

		$upload = wp_upload_bits( basename( $filename ), null, $contents );
		$this->assertTrue( empty( $upload['error'] ) );

		$attachment_id = parent::_make_attachment( $upload );

		$html = shortcode(
			array(
				'url'       => 'https://example.com/video.mp4',
				'thumbnail' => (string) $attachment_id,
				'title'     => 'Something',
			)
		);

		$this->assertMatchesRegularExpression( '#"thumbnailUrl":"http.*icon-128x128#', $html );
		$this->assertStringNotContainsString( 'Error', $html );
	}

	public function test_thumbnail_by_url() {

		$html = shortcode(
			array(
				'url'       => 'https://example.com/video2.mp4',
				'thumbnail' => 'https://example.com/image.jpg',
			)
		);

		$this->assertStringContainsString( '"thumbnailUrl":"https:\/\/example.com\/image.jpg"', $html );
		$this->assertStringNotContainsString( 'Error', $html );
	}
}
