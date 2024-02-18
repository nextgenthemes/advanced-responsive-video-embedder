<?php declare(strict_types=1);
namespace Nextgenthemes\ARVE\Admin;

use \Nextgenthemes\ARVE;

use function Nextgenthemes\ARVE\shortcode_settings;
use function \Nextgenthemes\WP\Admin\print_settings_blocks;

function add_media_button(): void {

	wp_enqueue_script( 'arve-shortcode-dialog' );
	wp_enqueue_style( 'arve-shortcode-dialog' );

	?>
	<button
		id="arve-btn"
		title="<?php esc_html_e( 'Advanced Responsive Video Embedder', 'advanced-responsive-video-embedder' ); ?>"
		class="arve-btn button add_media"
		type="button"
		x-data
		@click="document.querySelector('[x-ref=arvedialog]').showModal()"
	>
		<span class="wp-media-buttons-icon arve-icon"></span> 
		<?php esc_html_e( 'Video (ARVE)', 'advanced-responsive-video-embedder' ); ?>
	</button>
	<?php

	$GLOBALS['arve-print-dialog'] = true;
}

function create_shortcode_dialog(): void {

	if ( empty( $GLOBALS['arve-print-dialog'] ) ) {
		return;
	}

	wp_enqueue_script('arve-shortcode-dialog');
	wp_enqueue_script('alpinejs');

	$options  = ARVE\options();
	$settings = ARVE\shortcode_settings();
	$data     = array();

	foreach ( ARVE\shortcode_settings() as $k => $v ) {
		if ( $options['gutenberg_help'] ) {
			unset($settings[ $k ]['description']);
		}

		$data['options'][ $k ] = '';
	}

	$data = wp_json_encode( $data );
	?>
	<dialog class="arve-sc-dialog ngt" x-data="arvedialog" x-ref="arvedialog" x-init="$watch( 'options', () => { updateShortcode() } )">

		<button class="arve-sc-dialog__close-btn" autofocus @click="$refs.arvedialog.close()">&times;</button>

		<div class="grid">
			<?php
			print_settings_blocks(
				$settings,
				ARVE\settings_sections(),
				ARVE\PREMIUM_SECTIONS,
				'shortcode-dialog'
			);
			?>
		</div>

		<p id="arve-shortcode" class="arve-shortcode" x-ref="arveShortcode" x-text="shortcode"></p>

		<div>
			<button 
				class="arve-sc-dialog__cancel-btn button-secondary"
				@click="$refs.arvedialog.close()"
			>
				<?php esc_html_e( 'Cancel', 'advanced-responsive-video-embedder' ); ?>
			</button>
			<button 
				class="arve-sc-dialog__submit-btn button-primary"
				@click="() => {
					window.wp.media.editor.insert( shortcode );
					$refs.arvedialog.close();
				}"
			>
				<?php esc_html_e( 'Insert Shortcode', 'advanced-responsive-video-embedder' ); ?>
			</button>
		</div>
	</dialog>
	<?php
}

function print_shortcode_template(): void {

	$html = '[arve';

	// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
	foreach ( ARVE\shortcode_settings() as $key => $option ) {

		if ( ! $option['shortcode'] ) {
			continue;
		}

		$html .= "{{ vm.$key ? ' $key=\"' + vm.$key + '\"' : '' }}";
	}

	$html .= ' /]';

	echo $html;
}
