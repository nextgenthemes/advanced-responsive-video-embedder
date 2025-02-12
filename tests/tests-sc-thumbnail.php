<?php

declare(strict_types = 1);

use function Nextgenthemes\ARVE\shortcode;

class Tests_ShortcodeThumbnail extends WP_UnitTestCase {

	public function test_thumbnail_by_upload_id(): void {
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

	public function test_thumbnail_by_url(): void {

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
