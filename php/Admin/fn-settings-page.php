<?php declare(strict_types=1);
namespace Nextgenthemes\ARVE\Admin;

use Nextgenthemes\ARVE;
use Nextgenthemes\WP;

function settings_content(): void {

	?>
	<div data-wp-bind--hidden="!context.activeTabs.urlparams">
		<p>
			<?php
			echo wp_kses(
				sprintf(
					// Translators: URL
					__( 'This parameters will be added to the <code>iframe src</code> urls, you can control the video players behavior with them. Please read <a href="%s" target="_blank">the documentation</a> on.', 'advanced-responsive-video-embedder' ),
					esc_url( 'https://nextgenthemes.com/arve/documentation' )
				),
				array(
					'code' => array(),
					'a'    => array(
						'href'   => true,
						'target' => true,
						'title'  => true,
					),
				),
				array( 'http', 'https' )
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

	<div data-wp-bind--hidden="!context.activeTabs.debug">

		<p>
			<button data-wp-on--click="actions.deleteOembedCache" class="button-primary" style="margin-inline-end: 1em;">
				<?php esc_html_e( 'Delete oEmbed Cache', 'advanced-responsive-video-embedder' ); ?>
			</button>
			<span x-text="message"></span>
		</p>

		<?php require_once __DIR__ . '/partials/debug-info-textarea.php'; ?>
	</div>

	<?php if ( ! is_plugin_active( 'arve-pro/arve-pro.php' ) ) : ?>
		<p data-wp-bind--hidden="!context.activeTabs.pro">
			<?= pro_message( 'ARVE Pro', 'arve-pro' ); // phpcs:ignore ?>
		</p>
	<?php endif; ?>

	<?php if ( ! is_plugin_active( 'arve-privacy/arve-privacy.php' ) ) : ?>
		<p data-wp-bind--hidden="!context.activeTabs.privacy">
			<?= pro_message( 'ARVE Privacy', 'arve-privacy' ); // phpcs:ignore ?>
		</p>
	<?php endif; ?>

	<?php if ( ! is_plugin_active( 'arve-stick-videos/arve-sticky-videos.php' ) ) : ?>
		<p data-wp-bind--hidden="!context.activeTabs.sticky_videos">
			<?= pro_message( 'ARVE Sticky Videos', 'arve-stick-videos' ); // phpcs:ignore ?>
		</p>
	<?php endif; ?>

	<?php if ( ! is_plugin_active( 'arve-random-video/arve-random-video.php' ) ) : ?>
		<p data-wp-bind--hidden="!context.activeTabs.random_video">
			<?= pro_message( 'ARVE Random Video', 'arve-random-video' ); // phpcs:ignore ?>
		</p>
	<?php endif; ?>

	<?php
}

function pro_message( string $addon_name, string $slug ): string {
	return wp_kses(
		sprintf(
			// Translators: Addon Name
			__( '<strong>%s is not active.</strong> You may already set options for this addon but they will only take effect if its installed and activated later.', 'advanced-responsive-video-embedder' ),
			sprintf( '<a href="%s">%s</a>', 'https://nextgenthemes.com/plugins/' . $slug . '/', $addon_name )
		),
		array(
			'strong' => array(),
			'a'      => array( 'href' => true ),
		),
		array( 'http', 'https' )
	);
}

function get_addon_link( string $addon_name, string $slug ): string {
	return sprintf( '<a href="%s">%s</a>', 'https://nextgenthemes.com/plugins/' . $slug . '/', $addon_name );
}


function settings_sidebar(): void {

	if ( ! current_user_can('install_plugins') ) {
		echo '<div class="ngt-sidebar-box">';
		esc_html_e( 'Note that you are logged in with a user who that can\'t install plugins, ask someone who can if you are interested in ARVE Extensions.', 'advanced-responsive-video-embedder' );
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

	print_settings_box_html( '/partials/settings-sidebar-rate.html' );

	print_arve_news();
}

function print_settings_box_html( string $file ): void {
	echo '<div class="ngt-sidebar-box">';
	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	echo wp_kses( file_get_contents( __DIR__ . $file, false ), ARVE\ALLOWED_HTML );
	echo '</div>';
}

function print_arve_news(): void {

	$response = WP\remote_get_body_cached(
		add_query_arg(
			array(
				'per_page'    => 4,
				'page'        => 1,
				'orderby'     => 'date',
				'categories'  => 126,
			),
			'https://nextgenthemes.com/wp-json/wp/v2/posts'
		),
	);

	if ( is_wp_error( $response ) ) {
		esc_html_e( 'Error fetching news', 'advanced-responsive-video-embedder' );
		return;
	}

	$posts = json_decode( $response );

	if ( ! $posts ) {
		esc_html_e( 'No ARVE news posts', 'advanced-responsive-video-embedder' );
		return;
	}
	?>
	<div class="ngt-sidebar-box">
		<h3>
			<a href="https://nextgenthemes.com/category/arve/">
				<?php esc_html_e( 'ARVE Blog', 'advanced-responsive-video-embedder' ); ?>
			</a>
		</h3>
		<?php
		foreach ( $posts as $post ) {
			printf( '<h5><a href="%s">%s</a></h5>', esc_url( $post->link ), esc_html( $post->title->rendered ) );
			echo wp_kses( $post->excerpt->rendered, ARVE\ALLOWED_HTML );
		}
		?>
	</div>
	<?php
}

// unused, trigger re-caching is rebuild is probably better, also there this leaves the times in the DB so will this even work?
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
