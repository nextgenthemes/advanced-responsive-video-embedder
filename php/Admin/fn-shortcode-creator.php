<?php

declare(strict_types = 1);

namespace Nextgenthemes\ARVE\Admin;

use function Nextgenthemes\ARVE\settings;
use function Nextgenthemes\ARVE\settings_tabs;
use function Nextgenthemes\WP\Admin\print_settings_blocks;

const DIALOG_NAMESPACE = 'nextgenthemes_arve_dialog';

function add_media_button(): void {

	foreach ( settings( 'shortcode' )->get_all() as $k => $setting ) {

		if ( 'boolean' === $setting->type && ! $setting->option ) {
			$options[ $k ] = $setting->default;
		} else {
			$options[ $k ] = '';
		}
	}

	wp_enqueue_script_module( 'nextgenthemes-settings' );
	wp_interactivity_config(
		DIALOG_NAMESPACE,
		[
			'nonce'          => wp_create_nonce( 'wp_rest' ),
			'siteUrl'        => get_site_url(),
			'homeUrl'        => get_home_url(),
			'defaultOptions' => array(),
		]
	);

	wp_interactivity_state(
		DIALOG_NAMESPACE,
		[
			'options'    => $options,
			'shortcode'  => '[arve url="" /]',
			'message'    => '',
			'help'       => false,
		]
	);

	ob_start();
	?>
	<button
		id="arve-btn"
		title="<?php esc_attr_e( 'Advanced Responsive Video Embedder', 'advanced-responsive-video-embedder' ); ?>"
		class="arve-btn button add_media"
		type="button"
		data-wp-interactive="<?= esc_attr( DIALOG_NAMESPACE ); ?>"
		data-wp-on--click="actions.openShortcodeDialog"
	>
	<span class="wp-media-buttons-icon arve-icon"></span> 
		<?php esc_html_e( 'Video (ARVE)', 'advanced-responsive-video-embedder' ); ?>
	</button>
	<?php
	echo wp_interactivity_process_directives( ob_get_clean() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

	add_action( 'admin_footer', __NAMESPACE__ . '\create_shortcode_dialog' );
}

function create_shortcode_dialog(): void {

	ob_start();

	?>
	<dialog 
		class="arve-sc-dialog"
		data-wp-interactive="<?= esc_attr( DIALOG_NAMESPACE ); ?>"
		data-wp-watch="callbacks.updateShortcode"
	>
		<div class="arve-sc-dialog__wrap">

			<div class="arve-sc-dialog__header">

				<button type="button" class="media-modal-close" data-wp-on--click="actions.toggleHelp">
					<span class="media-modal-icon dashicons dashicons-editor-help">
						<span class="screen-reader-text">
							Toggle Help
						</span>
					</span>
				</button>

				<button type="button" class="media-modal-close" data-wp-on--click="actions.closeShortcodeDialog">
					<span class="media-modal-icon">
						<span class="screen-reader-text">
							Close dialog
						</span>
					</span>
				</button>

			</div>

			<div class="arve-sc-dialog__body">
				<?php
				print_settings_blocks(
					settings( 'shortcode' ),
					settings_tabs(),
					'shortcode-dialog'
				);
				?>
			</div>

			<div class="arve-sc-dialog__footer">

				<p id="arve-shortcode" class="arve-shortcode" data-wp-text="state.shortcode"></p>

				<button
					type="button"
					class="arve-sc-dialog__cancel-btn button-secondary"
					data-wp-on--click="actions.closeShortcodeDialog"
					autofocus
				>
					<?php esc_html_e( 'Cancel', 'advanced-responsive-video-embedder' ); ?>
				</button>
				<button
					type="button"
					class="arve-sc-dialog__submit-btn button-primary"
					data-wp-on--click="actions.insertShortcode"
				>
					<?php esc_html_e( 'Insert Shortcode', 'advanced-responsive-video-embedder' ); ?>
				</button>
			</div>

		</div>
	</dialog>
	<?php
	echo wp_interactivity_process_directives( ob_get_clean() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
