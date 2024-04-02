<?php declare(strict_types=1);
namespace Nextgenthemes\ARVE\Admin;

use Nextgenthemes\ARVE;
use Nextgenthemes\WP;

function add_media_button(): void {

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

	add_action( 'admin_footer', __NAMESPACE__ . '\create_shortcode_dialog' );

	WP\add_dep_to_script( 'alpinejs', 'arve-shortcode-dialog' );
	wp_enqueue_script('alpinejs');
}

function create_shortcode_dialog(): void {

	$settings = ARVE\settings( 'shortcode' );
	$data     = array();

	foreach ( ARVE\settings( 'shortcode' ) as $k => $v ) {
		$data['options'][ $k ] = '';
	}

	$data = wp_json_encode( $data );
	?>
	<dialog class="arve-sc-dialog ngt" x-data="arvedialog" x-ref="arvedialog" x-init="$watch( 'options', () => { updateShortcode() } )">

		<div class="arve-sc-dialog__wrap">

			<div class="arve-sc-dialog__header">
				<button class="arve-sc-dialog__close-btn" @click="toggleHelpTexts()">
					<span class="dashicons dashicons-editor-help"></span>
				</button>
				<button class="arve-sc-dialog__close-btn" autofocus @click="$refs.arvedialog.close()">&times;</button>
			</div>

			<div class="arve-sc-dialog__body">
				<?php
				\Nextgenthemes\WP\Admin\print_settings_blocks(
					$settings,
					ARVE\settings_sections(),
					ARVE\PREMIUM_SECTIONS,
					'arve',
					ARVE\PREMIUM_URL_PREFIX,
					'shortcode-dialog'
				);
				?>
			</div>

			<div class="arve-sc-dialog__footer">

				<p id="arve-shortcode" class="arve-shortcode" x-ref="arveShortcode" x-text="shortcode"></p>

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

		</div>
	</dialog>
	<?php
}

function print_shortcode_template(): void {

	$html = '[arve';

	// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
	foreach ( ARVE\settings( 'shortcode' ) as $key => $option ) {

		if ( ! $option['shortcode'] ) {
			continue;
		}

		$html .= "{{ vm.$key ? ' $key=\"' + vm.$key + '\"' : '' }}";
	}

	$html .= ' /]';

	echo $html;
}
