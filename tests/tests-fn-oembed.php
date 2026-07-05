<?php

declare(strict_types = 1);

use function Nextgenthemes\ARVE\sane_provider_name;
use function Nextgenthemes\ARVE\yt_srcset;
use function Nextgenthemes\ARVE\vimeo_referer;
use function Nextgenthemes\ARVE\remove_youtube_si_param;

class Tests_Oembed extends WP_UnitTestCase {

	/**
	 * @dataProvider data_sane_provider_name
	 */
	public function test_sane_provider_name( string $input, string $expected ): void {
		$this->assertSame( $expected, sane_provider_name( $input ) );
	}

	/**
	 * @return array<int,array{0: string, 1: string}>
	 */
	public function data_sane_provider_name(): array {
		return [
			[ 'YouTube', 'youtube' ],
			[ 'Vimeo', 'vimeo' ],
			[ 'Dailymotion', 'dailymotion' ],
			[ 'Wistia Inc', 'wistia' ],
			[ 'Rumble.com', 'rumble' ],
			[ 'Twitch', 'twitch' ],
			[ 'Facebook', 'facebook' ],
		];
	}

	public function test_yt_srcset_empty(): void {
		$this->assertSame( '', yt_srcset( [] ) );
	}

	public function test_yt_srcset_full(): void {
		$input = [
			120 => 'https://example.com/120.jpg',
			320 => 'https://example.com/320.jpg',
		];

		$expected = 'https://example.com/120.jpg 120w, https://example.com/320.jpg 320w';

		$this->assertSame( $expected, yt_srcset( $input ) );
	}

	public function test_vimeo_referer_adds_referer(): void {
		$args = [ 'headers' => [] ];
		$url  = 'https://vimeo.com/265932452';

		$result = vimeo_referer( $args, $url );

		$this->assertSame( site_url(), $result['headers']['Referer'] );
	}

	public function test_vimeo_referer_non_vimeo(): void {
		$args = [ 'headers' => [] ];
		$url  = 'https://youtube.com/watch?v=abc123';

		$result = vimeo_referer( $args, $url );

		$this->assertArrayNotHasKey( 'Referer', $result['headers'] );
	}

	public function test_remove_youtube_si_param_removes_si(): void {
		$provider = 'https://www.youtube.com/oembed';
		$url      = 'https://www.youtube.com/watch?v=abc123&si=xyz789';

		$result = remove_youtube_si_param( $provider, $url );

		$this->assertStringNotContainsString( 'si=', $result );
	}

	public function test_remove_youtube_si_param_no_si(): void {
		$provider = 'https://www.youtube.com/oembed';
		$url      = 'https://www.youtube.com/watch?v=abc123';

		$result = remove_youtube_si_param( $provider, $url );

		$this->assertStringContainsString( 'url=', $result );
		$this->assertStringContainsString( urlencode( 'https://www.youtube.com/watch?v=abc123' ), $result );
	}

	public function test_remove_youtube_si_param_non_youtube(): void {
		$provider = 'https://vimeo.com/oembed';
		$url      = 'https://vimeo.com/265932452';

		$this->assertSame( $provider, remove_youtube_si_param( $provider, $url ) );
	}
}
