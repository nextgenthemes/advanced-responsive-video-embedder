<?php

declare(strict_types = 1);

namespace Nextgenthemes\ARVE\Admin;

use function Nextgenthemes\ARVE\is_dev_mode;
use function Nextgenthemes\WP\remote_get_body_cached;
use const Nextgenthemes\ARVE\ALLOWED_HTML;

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
				array( 'https' )
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

		<?php print_debug_errors(); ?>

		<?php
		printf(
			'<p><a href="%s">%s</a></p>',
			esc_url( admin_url( 'site-health.php?tab=debug' ) ),
			esc_html__( 'Site Health Debug Info', 'advanced-responsive-video-embedder' )
		);
		?>

		<p>
			<button data-wp-on--click="actions.deleteCaches" data-wp-context='{ "type": "oembed", "like": "arve_cachetime" }' class="button-secondary">
				<?php esc_html_e( 'Delete oEmbed caches with ARVE data', 'advanced-responsive-video-embedder' ); ?>
			</button>
			<span data-wp-text="state.message"></span>
			<p>
				<?php esc_html_e( 'Causes regeneration of videos ARVE supports.', 'advanced-responsive-video-embedder' ); ?>
			</p>
		</p>
		<p>
			<button data-wp-on--click="actions.deleteCaches" data-wp-context='{ "type": "oembed" }' class="button-secondary">
				<?php esc_html_e( 'Delete entire oEmbed cache', 'advanced-responsive-video-embedder' ); ?>
			</button>
			<span data-wp-text="state.message"></span>
			<p>
				<?php esc_html_e( 'This includes embeds (X embeds ...) that are not handled by ARVE. Most likely not needed for ARVE. Causes regeneration of all embeds.', 'advanced-responsive-video-embedder' ); ?>
			</p>
		</p>
	
		<?php if ( wp_using_ext_object_cache() ) : ?>

			<p>
				<button data-wp-on--click="actions.deleteCaches" data-wp-context='{ "type": "wp_cache_flush" }' class="button-secondary">
					<?php esc_html_e( 'Flush Object Cache (includes all transients)', 'advanced-responsive-video-embedder' ); ?>
				</button>
				<span data-wp-text="state.message"></span>
				<p>
					<?php print_transient_message(); ?>
				</p>
				<p>
					<?php esc_html_e( 'Usually oembed caches are stored in post_meta or in the oembed post type they *can* be transients and your WP install uses external object cache for transients.', 'advanced-responsive-video-embedder' ); ?>
				</p>
			</p>

		<?php else : ?>

			<p>
				<button
					data-wp-on--click="actions.deleteCaches"
					data-wp-context='{ "type": "transients", "prefix": "ngt_www.googleapis.com/youtube", "like": "/youtube/v3/getting-started#quota" }'
					class="button-secondary"
				>
					<?php esc_html_e( 'Delete YouTube API Transients', 'advanced-responsive-video-embedder' ); ?>
				</button>
				<span data-wp-text="state.message"></span>
				<p>
					<?php print_transient_message(); ?>
				</p>
			</p>

		<?php endif; ?>
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

	<p data-wp-bind--hidden="!context.activeTabs.privacy">
		<?php
		echo wp_kses(
			__( 'If you serve your site to european users you <strong>must</strong> comply with GDPR/DSGVO. The ARVE Privacy Addon automatically adds the required 3rd party content notice to your embeds in lazyload and lightbox modes! No option needed, you just need to have the Privacy Addon active!', 'advanced-responsive-video-embedder' ),
			array(
				'strong' => array(),
			),
			array( 'https' )
		);
		?>
	</p>

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

function print_transient_message(): void {
	esc_html_e( 'ARVE Pro uses transients to store YouTube data API response data like video description and upload date or error messages from the calls. Make sure you also delete the oEmbed cache if you delete the transients!', 'advanced-responsive-video-embedder' );
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
		array( 'https' )
	);
}

function get_addon_link( string $addon_name, string $slug ): string {
	return sprintf( '<a href="%s">%s</a>', 'https://nextgenthemes.com/plugins/' . $slug . '/', $addon_name );
}


function settings_sidebar(): void {

	if ( ! current_user_can( 'install_plugins' ) ) {
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
	echo wp_kses( file_get_contents( __DIR__ . $file, false ), ALLOWED_HTML, array( 'https' ) );
	echo '</div>';
}

function print_arve_news(): void {

	$response = remote_get_body_cached(
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
			echo wp_kses( $post->excerpt->rendered, ALLOWED_HTML );
		}
		?>
	</div>
	<?php
}

function print_debug_errors(): void {

	$youtube_api_error = get_option( 'arve_youtube_api_error' );

	if ( ! is_wp_error( $youtube_api_error ) ) {
		return;
	}

	#$code    = $youtube_api_error->get_error_code();
	$message = $youtube_api_error->get_error_message();
	$data    = $youtube_api_error->get_error_data();

	if ( ! empty( $data['response_code'] ) && 403 === $data['response_code'] ) {

		$message .= '<br>';
		$message .= wp_strip_all_tags( get_json_body_error_message( $youtube_api_error ) );
		$message .= sprintf(
			// Translators: %1$s URL to tutorial video, %2$s URL to ARVE settings page
			__( ' <a href="%1$s" target="_blank">Sign up for your own API key</a> and enter it in <a href="%2$s">ARVE Pro Settings</a> to avoid limits.', 'advanced-responsive-video-embedder' ),
			'https://www.youtube.com/watch?v=EPeDTRNKAVo',
			esc_url( admin_url( 'options-general.php?page=nextgenthemes_arve' ) )
		);
	}

	if ( is_dev_mode() ) {
		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_var_export
		$message .= '<pre><code>' . esc_html( var_export( $data['response']['body'], true ) ) . '</code></pre>';
	}

	echo wp_kses( $message, ALLOWED_HTML, array( 'https' ) );
}

function get_json_body_error_message( \WP_Error $error ): string {

	$data = $error->get_error_data();

	if ( empty( $data['response']['body'] ) ) {
		return '';
	}

	try {
		// Decode JSON with explicit error throwing
		$decoded = json_decode( $data['response']['body'], true, 512, JSON_THROW_ON_ERROR ) ?? null;

		// Return message if it exists, false otherwise
		return $decoded['error']['message'] ?? '';
	} catch ( \JsonException $e ) {
		// Catch JSON-specific exceptions
		return '';
	}
}
