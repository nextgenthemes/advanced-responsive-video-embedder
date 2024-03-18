<?php declare(strict_types=1);
namespace Nextgenthemes\ARVE\Admin;

use Nextgenthemes\ARVE;
use Nextgenthemes\WP;

function settings_content(): void {

	?>
	<div x-show="'urlparams' === tab">
		<p>
			<?php
			printf(
				wp_kses(
					// Translators: URL
					__( 'This parameters will be added to the <code>iframe src</code> urls, you can control the video players behavior with them. Please read <a href="%s" target="_blank">the documentation</a> on.', 'advanced-responsive-video-embedder' ),
					array(
						'code' => array(),
						'a'    => array(
							'href'   => true,
							'target' => true,
							'title'  => true,
						),
					),
					array( 'http', 'https' )
				),
				esc_url( 'https://nextgenthemes.com/arve/documentation' )
			);
			?>
		</p>
		<p>
			See 
			<a target="_blank" href="https://developers.google.com/youtube/player_parameters#Parameters">Youtube Parameters</a>,
			<a target="_blank" href="http://www.dailymotion.com/doc/api/player.html#parameters">Dailymotion Parameters</a>,
			<a target="_blank" href="https://developer.vimeo.com/player/embedding">Vimeo Parameters</a>
		</p>
	</div>

	<div x-show="'debug' === tab">
		<?php require_once __DIR__ . '/partials/debug-info-textarea.php'; ?>
	</div>

	<div x-show="['pro', 'random-video', 'sticky-videos'].includes(tab)">
		<?php print_premium_section_message(); ?>
	</div>
	<?php
}

function settings_sidebar(): void {
	?>

	<?php
	if ( ! current_user_can('install_plugins') ) {
		echo '<div class="ngt-sidebar-box">';
		esc_html_e( 'Note that you are logged in with a user who that can\'t install plugins, ask someone who can if you are interrested in ARVE Extensions.', 'advanced-responsive-video-embedder' );
		echo '</div>';
	}

	if ( ! is_plugin_active( 'arve-pro/arve-pro.php' ) ) {
		print_settings_box_html( '/partials/settings-sidebar-pro.html' );
	}

	if ( ! is_plugin_active( 'arve-sticky-videos/arve-sticky-videos.php' ) ) {
		print_settings_box_html( '/partials/settings-sidebar-sticky-videos.html' );
	}

	if ( ! is_plugin_active( 'arve-random-video/arve-random-video.php' ) ) {
		print_settings_box_html( '/partials/settings-sidebar-random-video.html' );
	}

	if ( ! is_plugin_active( 'arve-amp/arve-amp.php' ) ) {
		print_settings_box_html( '/partials/settings-sidebar-amp.html' );
	}

	print_settings_box_html( '/partials/settings-sidebar-rate.html' );
	?>

	<?php
}

function print_premium_section_message(): void {
	?>
		<p>
			<?php
			esc_html_e( 'You may already set options for addons but they will only take effect if the associated addons are installed.', 'advanced-responsive-video-embedder' );
			?>
		</p>
	<?php
}

function print_settings_box_html( string $file ): void {
	echo '<div class="ngt-sidebar-box">';
	readfile( __DIR__ . $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_readfile
	echo '</div>';
}

function filter_save_options( array $options ): array {

	$action            = json_decode( $options['action'] );
	$options['action'] = '';

	if ( $action ) {
		$product_id  = WP\get_products()[ $action->product ]['id'];
		$product_key = $options[ $action->product ];

		$options[ $action->product . '_status' ] = WP\api_action( $product_id, $product_key, $action->action );
	}

	return $options;
}

// unused, trigger recaching is rebuild is probably better, also there this leaves the times in the DB so will this even work?
function delete_oembed_caches(): void {

	global $wpdb;

	$wpdb->query(
		$wpdb->prepare(
			"DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE %s AND meta_value LIKE %s",
			'%_oembed_%',
			'%' . $wpdb->esc_like( 'id="arve-' ) . '%'
		)
	);
}
