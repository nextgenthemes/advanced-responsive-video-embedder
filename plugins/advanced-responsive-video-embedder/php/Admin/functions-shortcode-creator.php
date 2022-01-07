<?php
namespace Nextgenthemes\ARVE\Admin;

use \Nextgenthemes\ARVE;
use \Nextgenthemes\ARVE\Common;

function add_media_button() {

	$options   = ARVE\options();
	$settings  = ARVE\shortcode_settings();
	$link_only = array(
		'a' => array(
			'href'   => array(),
			'target' => array(),
			'title'  => array(),
		),
	);

	foreach ( ARVE\shortcode_settings() as $k => $v ) {
		if ( $options['gutenberg_help'] ) {
			unset($settings[ $k ]['description']);
		}
	}
	?>

	<div id="arve-sc-dialog" hidden>
		<div id="arve-sc-vue">
			<?php
			Common\Admin\print_settings_blocks(
				$settings,
				ARVE\settings_sections(),
				ARVE\PREMIUM_SECTIONS,
				'shortcode-dialog'
			);
			?>
			<div id="arve-shortcode" class="arve-shortcode">
				<?php
				print_shortcode_template();
				?>
			</div>
		</div>
	</div>

	<?php
	printf(
		'<button id="arve-btn" title="%s" class="arve-btn button add_media" type="button"><span class="wp-media-buttons-icon arve-icon"></span> %s</button>',
		esc_attr__( 'ARVE Advanced Responsive Video Embedder', 'advanced-responsive-video-embedder' ),
		esc_html__( 'Video (ARVE)', 'advanced-responsive-video-embedder' )
	);
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
