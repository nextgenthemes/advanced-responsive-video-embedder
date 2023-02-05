<?php
namespace Nextgenthemes\ARVE\Admin;

use \Nextgenthemes\ARVE;
use \Nextgenthemes\ARVE\Common;

function add_media_button() {

	printf(
		'<button id="arve-btn" title="%s" class="arve-btn button add_media" type="button"><span class="wp-media-buttons-icon arve-icon"></span> %s</button>',
		esc_attr__( 'ARVE Advanced Responsive Video Embedder', 'advanced-responsive-video-embedder' ),
		esc_html__( 'Video (ARVE)', 'advanced-responsive-video-embedder' )
	);
}

function create_shortcode_dialog() {

	$options  = ARVE\options();
	$settings = ARVE\shortcode_settings();

	foreach ( ARVE\shortcode_settings() as $k => $v ) {
		if ( $options['gutenberg_help'] ) {
			unset($settings[ $k ]['description']);
		}
	}

	?>
	<dialog class="arve-sc-dialog">

		<button class="arve-sc-dialog__close-btn">&times;</button>

		<div id="arve-sc-vue">
			<?php
			Common\Admin\print_settings_blocks(
				$settings,
				ARVE\settings_sections(),
				ARVE\PREMIUM_SECTIONS,
				'shortcode-dialog'
			);
			?>
			<p id="arve-shortcode" class="arve-shortcode">
				<?php
				print_shortcode_template();
				?>
			</p>
		</div>

		<div>
			<button class="arve-sc-dialog__cancel-btn button-secondary"><?php esc_html_e( 'Cancel', 'advanced-responsive-video-embedder' ); ?></button>
			<button class="arve-sc-dialog__submit-btn button-primary"><?php esc_html_e( 'Insert Shortcode', 'advanced-responsive-video-embedder' ); ?></button>
		</div>
	</dialog>
	<?php
}

function print_shortcode_template() {

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
