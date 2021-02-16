<?php
namespace Nextgenthemes\ARVE\Admin;

use const \Nextgenthemes\ARVE\PRO_VERSION_REQUIRED;

use \Nextgenthemes\ARVE;
use \Nextgenthemes\ARVE\Common\Admin\Notices;

use function \Nextgenthemes\ARVE\Common\ver;
use function \Nextgenthemes\ARVE\Common\attr;
use function \Nextgenthemes\ARVE\Common\kses_basic;
use function \Nextgenthemes\ARVE\Common\enqueue_asset;

const ALLOWED_HTML = array(
	'a'      => array(
		'href'   => array(),
		'target' => array(),
		'title'  => array(),
	),
	'p'      => array(),
	'br'     => array(),
	'em'     => array(),
	'strong' => array(),
	'code'   => array(),
	'ul'     => array(),
	'li'     => array(),
);

function action_admin_init_setup_messages() {

	$pro_ver = false;

	if ( defined( 'ARVE_PRO_VERSION' ) ) {
		$pro_ver = ARVE_PRO_VERSION;
	} elseif ( defined( '\Nextgenthemes\ARVE\Pro\VERSION' ) ) {
		$pro_ver = \Nextgenthemes\ARVE\Pro\VERSION;
	}

	if ( $pro_ver && version_compare( PRO_VERSION_REQUIRED, $pro_ver, '>' ) ) {
		$msg = sprintf(
			// Translators: %1$s Pro Version required
			__( 'Your ARVE Pro Addon is outdated, you need version %1$s or later. If you have setup your license <a href="%2$s">here</a> semi auto updates should work (Admin panel notice and auto install on confirmation). If not please <a href="%3$s">report it</a> and manually update as <a href="%4$s">described here.</a>', 'advanced-responsive-video-embedder' ),
			ARVE\PRO_VERSION_REQUIRED,
			esc_url( get_admin_url() . 'options-general.php?page=nextgenthemes' ),
			'https://nextgenthemes.com/support/',
			'https://nextgenthemes.com/plugins/arve/documentation/installing-and-license-management/'
		);

		Notices::instance()->register_notice(
			'ngt-arve-outdated-pro-v' . PRO_VERSION_REQUIRED,
			'notice-error',
			wp_kses( $msg, ALLOWED_HTML ),
			array(
				'cap' => 'update_plugins',
			)
		);
	}

	if ( display_pro_ad() ) {
		Notices::instance()->register_notice(
			'ngt-arve-addon-ad',
			'notice-info',
			wp_kses( ad_html(), ALLOWED_HTML ),
			array(
				'cap' => 'install_plugins',
			)
		);
	}
}

function ad_html() {

	$html = esc_html__( 'Hi, this is Nico(las Jonas) the author of the ARVE Advanced Responsive Video Embedder plugin. If you are interrested in additional features and/or want to support the work I do on this plugin please consider buying the Pro Addon.', 'advanced-responsive-video-embedder' );

	$html = "<p>$html</p><ul>";

	$lis = array(
		__( '<strong>Disable links in embeds</strong><br>For example: Clicking on a title in a YouTube embed will not open a new popup/tab/window. <strong>Prevent providers from leading your visitors away from your site!</strong>', 'advanced-responsive-video-embedder' ),
		__( '<strong>Lazyload mode</strong><br>Make your site load <strong>faster</strong> by loading only a image instead of the entire video player on pageload.', 'advanced-responsive-video-embedder' ),
		__( '<strong>Lightbox</strong><br>Shows the Video in a Lightbox after clicking a preview image or link.', 'advanced-responsive-video-embedder' ),
		sprintf(
			// Translators: URLs
			__( 'Expand on click and more, for a <strong><a href="%1$s">complete feature list</a></strong> please visit the <strong><a href="%1$s">official site</a></strong>.', 'advanced-responsive-video-embedder' ),
			esc_url( 'https://nextgenthemes.com/plugins/arve-pro/' )
		),
	);

	foreach ( $lis as $li ) {
		$html .= "<li>$li</li>";
	}
	$html .= '</ul>';

	return $html;
}

function display_pro_ad() {

	$inst = (int) get_option( 'arve_install_date' );

	if ( get_user_meta( get_current_user_id(), 'arve_dismiss_pro_notice' ) ||
		is_plugin_active( 'arve-pro/arve-pro.php' ) ||
		time() < strtotime( '+3 weeks', $inst )
	) {
		return false;
	}

	return true;
}

function widget_text() {

	echo '<p>';
	printf( '<a href="%s">Documentation</a>, ', 'https://nextgenthemes.com/plugins/arve/documentation/' );
	printf( '<a href="%s">Support</a>, ', 'https://nextgenthemes.com/support/' );
	printf(
		'<a href="%s">%s</a>',
		esc_url( admin_url( 'admin.php?page=advanced-responsive-video-embedder' ) ),
		esc_html__( 'Settings', 'advanced-responsive-video-embedder' )
	);
	echo '</p>';

	printf( '<a href="%s">ARVE Pro Addon Features</a>:', 'https://nextgenthemes.com/plugins/arve-pro/' );

	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_readfile
	echo wp_kses( ad_html(), ALLOWED_HTML );
}

function add_dashboard_widget() {

	if ( ! display_pro_ad() ) {
		return false;
	}

	wp_add_dashboard_widget(
		'arve_dashboard_widget',              // Widget slug.
		'Advanced Responsive Video Embedder', // Title.
		__NAMESPACE__ . '\widget_text'        // Display function.
	);

	if ( 'index.php' === $GLOBALS['pagenow'] ) {
		// Get the regular dashboard widgets array.
		// (which has our new widget already but at the end).
		$normal_dashboard = $GLOBALS['wp_meta_boxes']['dashboard']['normal']['core'];
		// Backup and delete our new dashboard widget from the end of the array.
		$arve_widget_backup = array( 'arve_dashboard_widget' => $normal_dashboard['arve_dashboard_widget'] );
		unset( $normal_dashboard['arve_dashboard_widget'] );
		// Merge the two arrays together so our widget is at the beginning.
		$sorted_dashboard = array_merge( $arve_widget_backup, $normal_dashboard );
		// Save the sorted array back into the original metaboxes.
		// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		$GLOBALS['wp_meta_boxes']['dashboard']['normal']['core'] = $sorted_dashboard;
	}
}

function add_action_links( $links ) {

	if ( ! is_plugin_active( 'arve-pro/arve-pro.php' ) ) {

		$extra_links['buy_pro_addon'] = sprintf(
			'<a href="%s"><strong style="display: inline;">%s</strong></a>',
			'https://nextgenthemes.com/plugins/arve-pro/',
			__( 'Buy Pro Addon', 'advanced-responsive-video-embedder' )
		);
	}

	$extra_links['donate'] = sprintf(
		'<a href="https://nextgenthemes.com/donate/"><strong style="display: inline;">%s</strong></a>',
		esc_html__( 'Donate', 'advanced-responsive-video-embedder' )
	);

	$extra_links['settings'] = sprintf(
		'<a href="%s">%s</a>',
		esc_url( admin_url( 'options-general.php?page=nextgenthemes_arve' ) ),
		esc_html__( 'Settings', 'advanced-responsive-video-embedder' )
	);

	return array_merge( $extra_links, $links );
}

function add_media_button() {

	$options   = ARVE\options();
	$link_only = array(
		'a' => array(
			'href'   => array(),
			'target' => array(),
			'title'  => array(),
		),
	);
	add_thickbox();
	?>

	<div id="arve-thickbox" style="display:none;">
		<p>
			<?php
			printf(
				// translators: URL
				wp_kses( __( 'This button can open an optional ARVE a Shortcode creation dialog. ARVE needs the <a href="%s">Shortcode UI plugin</a> active for this fuctionality. It helps creating shortcodes and provides a preview in the Editor. But sadly Shortcode UI is not maintained anymore and there have been some know issues with Shortcode UI.', 'advanced-responsive-video-embedder' ), $link_only ),
				esc_url( network_admin_url( 'plugin-install.php?s=Shortcode+UI&tab=search&type=term' ) )
			);
			?>
		</p>
		<p>
			<?php
			printf(
				// translators: URL
				wp_kses( __( 'It is perfectly fine to pass on this and <a href="%s">manually</a> write shortcodes or don\'t use shortcodes at all, but it makes things easier. And if you ever switch to Gutenberg there is a ARVE Block all the settings in the sidebar waiting for you.', 'advanced-responsive-video-embedder' ), $link_only ),
				esc_url( 'https://nextgenthemes.com/plugins/arve/documentation/' )
			);
			?>
		</p>
	</div>
	<?php
	printf(
		'<button id="arve-btn" title="%s" data-mode="%s" class="arve-btn button add_media" type="button"><span class="wp-media-buttons-icon arve-icon"></span> %s</button>',
		esc_attr__( 'ARVE Advanced Responsive Video Embedder', 'advanced-responsive-video-embedder' ),
		esc_attr( $options['mode'] ),
		esc_html__( 'Embed Video (ARVE)', 'advanced-responsive-video-embedder' )
	);
}

function register_shortcode_ui() {

	$settings = ARVE\shortcode_settings();

	foreach ( $settings as $k => $v ) :

		if ( 'boolean' === $v['type'] ) {
			$v['type'] = 'select';

			if ( isset( $v['option'] ) && true === $v['option'] ) {
				$v['options'] = array(
					array(
						'value' => '',
						'label' => esc_html__( 'Default (settings page)', 'advanced-responsive-video-embedder' ),
					),
					array(
						'value' => 'yes',
						'label' => esc_html__( 'Yes', 'advanced-responsive-video-embedder' ),
					),
					array(
						'value' => 'no',
						'label' => esc_html__( 'No', 'advanced-responsive-video-embedder' ),
					),
				);
			} else {
				$v['options'] = array(
					array(
						'value' => 'no',
						'label' => esc_html__( 'No', 'advanced-responsive-video-embedder' ),
					),
					array(
						'value' => 'yes',
						'label' => esc_html__( 'Yes', 'advanced-responsive-video-embedder' ),
					),
				);
			}
		}
		if ( 'string' === $v['type'] ) {
			$v['type'] = 'text';
		}
		if ( 'integer' === $v['type'] ) {
			$v['type'] = 'number';
		}
		if ( ! empty( $v['placeholder'] ) ) {
			$v['meta']['placeholder'] = $v['placeholder'];
		}

		$v['attr'] = $k;
		$attrs[]   = $v;
	endforeach;

	shortcode_ui_register_for_shortcode(
		'arve',
		array(
			'label'         => esc_html( 'ARVE' ),
			'listItemImage' => 'dashicons-format-video',
			'attrs'         => $attrs,
		)
	);
}

function admin_enqueue_styles() {

	enqueue_asset(
		array(
			'handle' => 'advanced-responsive-video-embedder',
			'src'    => plugins_url( 'build/admin.css', ARVE\PLUGIN_FILE ),
			'ver'    => ver( ARVE\VERSION, 'build/admin.css', ARVE\PLUGIN_FILE ),
		)
	);
}

function admin_enqueue_scripts() {

	enqueue_asset(
		array(
			'handle' => 'arve-admin',
			'src'    => plugins_url( 'build/admin.js', ARVE\PLUGIN_FILE ),
			'path'   => ARVE\PLUGIN_DIR . '/build/admin.js',
			'deps'   => array( 'jquery' ),
		)
	);

	if ( is_plugin_active( 'shortcode-ui/shortcode-ui.php' ) ) {
		enqueue_asset(
			array(
				'handle' => 'arve-admin-sc-ui',
				'path'   => ARVE\PLUGIN_DIR . '/build/shortcode-ui.js',
				'src'    => plugins_url( 'build/shortcode-ui.js', ARVE\PLUGIN_FILE ),
				'deps'   => array( 'shortcode-ui' ),
			)
		);
	}
}

function action_admin_bar_menu( $admin_bar ) {

	if ( current_user_can( 'manage_options' ) && ARVE\options()['admin_bar_menu'] ) {

		$admin_bar->add_menu(
			array(
				'id'    => 'arve-settings',
				'title' => 'ARVE',
				'href'  => get_admin_url() . 'options-general.php?page=nextgenthemes_arve',
				'meta'  => array( 'title' => __( 'Advanced Responsive Video Embedder Settings', 'advanced-responsive-video-embedder' ) ),
			)
		);
	}
}
