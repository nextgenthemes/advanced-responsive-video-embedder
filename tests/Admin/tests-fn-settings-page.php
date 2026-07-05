<?php

declare(strict_types = 1);

use function Nextgenthemes\ARVE\Admin\get_json_body_error_message;
use function Nextgenthemes\ARVE\Admin\pro_message;
use function Nextgenthemes\ARVE\Admin\get_addon_link;

class Tests_Settings_Page extends WP_UnitTestCase {

	public static function setUpBeforeClass(): void {
		parent::setUpBeforeClass();

		require_once constant( 'Nextgenthemes\ARVE\PLUGIN_DIR' ) . '/php/Admin/fn-settings-page.php';
	}

	public function test_get_json_body_error_message_no_body(): void {
		$error = new \WP_Error( 'test', 'Error' );
		$this->assertSame( '', get_json_body_error_message( $error ) );
	}

	public function test_get_json_body_error_message_with_message(): void {

		$error = new \WP_Error(
			'test',
			'Error',
			[
				'response' => [
					'body' => wp_json_encode( [ 'error' => [ 'message' => 'API limit exceeded' ] ] ),
				],
			]
		);

		$this->assertSame( 'API limit exceeded', get_json_body_error_message( $error ) );
	}

	public function test_get_json_body_error_message_invalid_json(): void {

		$error = new \WP_Error(
			'test',
			'Error',
			[
				'response' => [
					'body' => 'not-json',
				],
			]
		);

		$this->assertSame( '', get_json_body_error_message( $error ) );
	}

	public function test_pro_message_contains_addon_name(): void {

		$result = pro_message( 'Test Addon', 'test-addon' );

		$this->assertStringContainsString( 'Test Addon', $result );
	}

	public function test_get_addon_link_contains_url(): void {

		$result = get_addon_link( 'My Addon', 'my-addon' );

		$this->assertStringContainsString( 'https://nextgenthemes.com/plugins/my-addon/', $result );
		$this->assertStringContainsString( 'My Addon', $result );
	}
}
