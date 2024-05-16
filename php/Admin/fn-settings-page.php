<?php declare(strict_types=1);
namespace Nextgenthemes\ARVE\Admin;

use Nextgenthemes\ARVE;
use Nextgenthemes\WP;

function settings_content(): void {

	?>
	<div x-show="'urlparams' === tab">
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

	<div x-show="'debug' === tab">

		<div style="margin-top: 1.2rem; margin-bottom: 1.2rem;">
			<button @click="deleteOembedCache();" class="button-primary" style="margin-inline-end: 1em;">
				<?php esc_html_e( 'Delete oEmbed Cache', 'advanced-responsive-video-embedder' ); ?>
			</button>
			<span x-text="message"></span>
		</div>

		<?php require_once __DIR__ . '/partials/debug-info-textarea.php'; ?>
	</div>

	<div x-show="['pro', 'privacy', 'random-video', 'sticky-videos'].includes(tab)">
		<p>
			<?php
			echo wp_kses(
				sprintf(
					// Translators: URL
					__( 'You may already set options for addons but they will only take effect if the associated addons are installed. If not done already, enter your license keys <a href="%s">here</a>', 'advanced-responsive-video-embedder' ),
					esc_url( admin_url( 'options-general.php?page=nextgenthemes' ) )
				),
				array( 'a' => array( 'href' => true ) ),
				array( 'http', 'https' )
			);
			?>
		</p>
	</div>
	<?php
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
		)
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
