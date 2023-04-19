<?php
namespace Nextgenthemes\ARVE\Admin;

use \Nextgenthemes\ARVE;
use \Nextgenthemes\ARVE\Common;

function settings_content() {

	$link_code_only = array(
		'code' => array(),
		'a'    => array(
			'href'   => array(),
			'target' => array(),
			'title'  => array(),
		),
	);
	?>

	<template data-ngt-svelte-target=".ngt-section--urlparams">
		<p>
			<?php
			printf(
				// Translators: URL
				wp_kses( __( 'This parameters will be added to the <code>iframe src</code> urls, you can control the video players behavior with them. Please read <a href="%s" target="_blank">the documentation</a> on.', 'advanced-responsive-video-embedder' ), $link_code_only ),
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
	</template>

	<template data-ngt-svelte-target=".ngt-settings-grid__sidebar">
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

		print_outdated_php_box();
		?>
	</template>

	<template data-ngt-svelte-target=".ngt-section--pro">
		<?php print_premium_section_message(); ?>
	</template>

	<template data-ngt-svelte-target=".ngt-section--sticky-videos">
		<?php print_premium_section_message(); ?>
	</template>

	<template data-ngt-svelte-target=".ngt-section--random-video">
		<?php print_premium_section_message(); ?>
	</template>

	<template data-ngt-svelte-target=".ngt-section--debug">
		<?php #require_once __DIR__ . '/partials/debug-info.php'; ?>
	</template>

	<?php
}

function print_premium_section_message() {
	?>
		<p>
			<?php
			esc_html_e( 'You may already set options for addons but they will only take effect if the associated addons are installed.', 'advanced-responsive-video-embedder' );
			?>
		</p>
	<?php
}

function print_settings_box_html( $file ) {
	echo '<div class="ngt-sidebar-box">';
	readfile( __DIR__ . $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_readfile
	echo '</div>';
}

function print_outdated_php_box() {

	$link_only = array(
		'a' => array(
			'href'   => array(),
			'target' => array(),
			'title'  => array(),
		),
	);

	if ( \version_compare( PHP_VERSION, '8.2.5', '<' ) ) {
		?>
		<div class="ngt-sidebar-box">
			<p>
				<?php
				printf(
					// translators: URL
					wp_kses( __( 'Just a heads up, your PHP version %1$s is outdated and possibly insecure. See what versions are <a href="%2$s">good here</a>', 'advanced-responsive-video-embedder' ), $link_only ),
					esc_html( PHP_VERSION ),
					esc_url( 'https://www.php.net/supported-versions' )
				);
				?>
			</p>
		</div>
		<?php
	}
}


function filter_save_options( $options ) {

	$action            = json_decode( $options['action'] );
	$options['action'] = '';

	if ( $action ) {
		$product_id  = get_products()[ $action->product ]['id'];
		$product_key = $options[ $action->product ];

		$options[ $action->product . '_status' ] = api_action( $product_id, $product_key, $action->action );
	}

	return $option;
}

// unused, trigger recaching is rebuild is probably better, also there this leaves the times in the DB so will this even work?
function delete_oembed_caches() {

	global $wpdb;

	$wpdb->query(
		$wpdb->prepare(
			"DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE %s AND meta_value LIKE %s",
			'%_oembed_%',
			'%' . $wpdb->esc_like( 'id="arve-' ) . '%'
		)
	);
}
