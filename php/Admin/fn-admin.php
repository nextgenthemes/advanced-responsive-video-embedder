<?php

declare(strict_types = 1);

namespace Nextgenthemes\ARVE\Admin;

use Nextgenthemes\WP\Admin\Notices;

use function Nextgenthemes\ARVE\is_gutenberg;
use function Nextgenthemes\ARVE\settings;
use function Nextgenthemes\ARVE\settings_tabs;
use function Nextgenthemes\ARVE\options;

use function Nextgenthemes\WP\enqueue_asset;
use function Nextgenthemes\WP\remote_get_json_cached;
use function Nextgenthemes\WP\str_contains_any;
use function Nextgenthemes\WP\register_asset;
use function Nextgenthemes\WP\kses_https_links;

use const Nextgenthemes\ARVE\PRO_VERSION_REQUIRED;
use const Nextgenthemes\ARVE\PLUGIN_DIR;
use const Nextgenthemes\ARVE\PLUGIN_FILE;
use const Nextgenthemes\ARVE\ALLOWED_HTML;
use const Nextgenthemes\ARVE\VERSION;

function action_admin_init_setup_messages(): void {

	if ( defined( '\Nextgenthemes\ARVE\Pro\VERSION' ) && version_compare( PRO_VERSION_REQUIRED, \Nextgenthemes\ARVE\Pro\VERSION, '>' ) ) {
		$msg = sprintf(
			// Translators: %1$s Pro Version required
			__( 'Your ARVE Pro Addon is outdated, you need version %1$s or later. If you have setup your license <a href="%2$s">here</a> semi auto updates should work (Admin panel notice and auto install on confirmation). If not please <a href="%3$s">report it</a> and manually update as <a href="%4$s">described here.</a>', 'advanced-responsive-video-embedder' ),
			PRO_VERSION_REQUIRED,
			esc_url( get_admin_url() . 'options-general.php?page=nextgenthemes' ),
			'https://nextgenthemes.com/support/',
			'https://nextgenthemes.com/plugins/arve/documentation/installation/'
		);

		if ( str_contains_any( VERSION, array( 'alpha', 'beta' ) ) ) {
			$msg = sprintf(
				// Translators: %1$s Pro Version required
				__( 'Your ARVE Pro Addon is outdated, you need version %1$s or later. Pre release updates my need a manual update. Download from <a href="%2$s">your account</a>.', 'advanced-responsive-video-embedder' ),
				PRO_VERSION_REQUIRED,
				esc_url( get_admin_url() . 'options-general.php?page=nextgenthemes' ),
				'https://nextgenthemes.com/my-account/'
			);
		}

		Notices::instance()->register_notice(
			'ngt-arve-outdated-pro-v' . PRO_VERSION_REQUIRED,
			'notice-error',
			wp_kses( $msg, ALLOWED_HTML, array( 'htts', 'https' ) ),
			array(
				'cap' => 'update_plugins',
			)
		);
	}

	if ( display_pro_ad() ) {
		Notices::instance()->register_notice(
			'ngt-arve-addon-ad',
			'notice-info',
			wp_kses( ad_html(), ALLOWED_HTML, array( 'htts', 'https' ) ),
			array(
				'cap' => 'install_plugins',
			)
		);
	}

	$object_cache_msg = get_option( 'arve_object_cache_msg' );

	if ( $object_cache_msg ) {

		Notices::instance()->register_notice(
			'arve_object_cache_msg',
			'notice-warning',
			wp_kses(
				$object_cache_msg,
				ALLOWED_HTML,
				array( 'htts', 'https' )
			),
			array(
				'cap'   => 'manage_options',
				'scope' => 'global',
			)
		);

		Notices::instance()->restore_notice( 'arve_object_cache_msg' );
		delete_option( 'arve_object_cache_msg' );
	}

	$beta_ver = get_latest_beta();

	if ( str_contains_any( VERSION, array( 'alpha', 'beta' ) ) && version_compare( VERSION, $beta_ver, '<' ) ) {

		Notices::instance()->register_notice(
			"ngt-arve-beta-$beta_ver",
			'notice-info',
			wp_kses(
				sprintf(
					// Translators: %1$s URL, %2$s version tag.
					__( 'Latest ARVE pre-release is out! Please do a manual update.<br>(1) Download <a href="%1$s">arve-%2$s.zip</a>. (2) Go to Plugins > Add New > Upload Plugin. (3) Install. (4) <a href="%3$s">nextgenthemes.com/support/</a> if something goes wrong.', 'advanced-responsive-video-embedder' ),
					esc_url( "https://github.com/nextgenthemes/advanced-responsive-video-embedder/releases/download/$beta_ver/advanced-responsive-video-embedder-$beta_ver.zip" ),
					esc_html( $beta_ver ),
					esc_url( 'https://nextgenthemes.com/support/' )
				),
				ALLOWED_HTML,
				array( 'https' )
			),
			array(
				'cap' => 'install_plugins',
			)
		);
	}

	if ( is_gutenberg() &&
		! is_plugin_active( 'gutenberg/gutenberg.php' ) &&
		version_compare( $GLOBALS['wp_version'], '6.6-beta2', '<' )
	) {
		Notices::instance()->register_notice(
			'ngt-arve-need-gb2',
			'notice-info',
			wp_kses(
				sprintf(
					// Translators: %1$s URL, %2$s version tag.
					__( 'For the ARVE Block to work you currently need the <a href="%1$s">Gutenberg plugin</a> active or <a href="$2$s">WP 6.6-RC2</a> or later. Reason is unknown at the time of writing this.', 'advanced-responsive-video-embedder' ),
					\admin_url( 'plugin-install.php?s=Gutenberg%2520Team&tab=search&type=term' ),
					esc_url( 'https://wordpress.org/news/2024/07/wordpress-6-6-rc2/' )
				),
				ALLOWED_HTML,
				array( 'https' )
			),
			array(
				'cap' => 'install_plugins',
			)
		);
	}

	if ( is_plugin_active( 'classic-editor/classic-editor.php' ) &&
		version_compare( $GLOBALS['wp_version'], '6.6-beta2', '<' )
	) {
		Notices::instance()->register_notice(
			'ngt-arve-need-classic-editor-needs-6.6',
			'notice-info',
			wp_kses(
				sprintf(
					// Translators: %s URL.
					__(
						'Apologies, for the ARVE button in Classic Editor to work you need WP 6.6 that is about to release 2024-07-16. Three not ideal options for the time being:
						<ul>
							<li>
								You can create the shortcodes manually and wait for the regular 6.6 update.
							</li>
							<li>
								Update WordPress already to the 6.6 release candidate 2 (basically ready) <a href="%s">6.6-RC2</a>.
							</li>
							<li>
								Downgrade ARVE to 10.1.1 (with WP-Rollback for example).
							</li>
						</ul>',
						'advanced-responsive-video-embedder'
					),
					esc_url( 'https://wordpress.org/news/2024/07/wordpress-6-6-rc2/' )
				),
				ALLOWED_HTML,
				array( 'https' )
			),
			array(
				'cap' => 'install_plugins',
			)
		);
	}

	if ( is_plugin_active( 'all-in-one-seo-pack/all_in_one_seo_pack.php' )
		&& ! is_gutenberg()
	) {
		Notices::instance()->register_notice(
			'aio-seo-notice',
			'notice-info',
			sprintf(
				kses_https_links(
					// Translators: %s URL
					__(
						'There is compatibility issue with All In One SEO Pack effecting ARVE. With the AIO-SEO plugin active the "Embed Video (ARVE)" button will not work in Classic Editor. Please contact the AIO-SEO plugin <a href="%1$s">support</a> / <a href="%2$s">support for pro users</a> if they can fix this issue.',
						'advanced-responsive-video-embedder'
					),
				),
				'https://wordpress.org/support/plugin/all-in-one-seo-pack/#new-topic-0',
				'https://aioseo.com/login/?redirect_to=%2Faccount%2Fsupport%2F'
			)
		);
	}
}

function get_latest_beta(): string {

	$ver    = '10.0.0-alpha9';
	$gh_tag = remote_get_json_cached(
		'https://api.github.com/repos/nextgenthemes/advanced-responsive-video-embedder/releases/latest',
		array(),
		'tag_name',
		HOUR_IN_SECONDS
	);

	if ( ! is_wp_error( $gh_tag ) && str_contains_any( $gh_tag, array( 'alpha', 'beta' ) ) ) {
		$ver = $gh_tag;
	}

	return $ver;
}

function ad_html(): string {

	$html = esc_html__( 'Hi, this is Nico(las Jonas) the author of the ARVE Advanced Responsive Video Embedder plugin. If you are interested in additional features and/or want to support the work I do on this plugin please consider buying the Pro Addon.', 'advanced-responsive-video-embedder' );

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

function display_pro_ad(): bool {

	$inst = (int) get_option( 'arve_install_date' );

	if ( get_user_meta( get_current_user_id(), 'arve_dismiss_pro_notice' ) ||
		is_plugin_active( 'arve-pro/arve-pro.php' ) ||
		time() < strtotime( '+3 weeks', $inst )
	) {
		return false;
	}

	return true;
}

function widget_text(): void {

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
	echo wp_kses( ad_html(), ALLOWED_HTML, array( 'htts', 'https' ) );
}

function register_shortcode_ui(): void {

	$settings = settings( 'shortcode' )->to_array();

	foreach ( $settings as $k => $v ) :

		switch ( $v['type'] ) {
			case 'boolean':
				$v['type']    = 'select';
				$v['options'] = array(
					array(
						'value' => '',
						'label' => esc_html__( 'Default', 'advanced-responsive-video-embedder' ),
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
				break;
			case 'string':
				$v['type'] = 'text';
				break;
			case 'integer':
				$v['type'] = 'number';
				break;
		}

		if ( 'thumbnail' === $k ) {
			$v['type']        = 'attachment';
			$v['libraryType'] = array( 'image' );
			$v['addButton']   = __( 'Select Image', 'advanced-responsive-video-embedder' );
			$v['frameTitle']  = __( 'Select Image', 'advanced-responsive-video-embedder' );
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

function add_dashboard_widget(): void {

	if ( ! display_pro_ad() ) {
		return;
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

function add_action_links( array $links ): array {

	if ( ! is_plugin_active( 'arve-pro/arve-pro.php' ) ) {

		$links['buy_pro_addon'] = sprintf(
			'<a href="%s"><strong style="display: inline;">%s</strong></a>',
			'https://nextgenthemes.com/plugins/arve-pro/',
			__( 'Get Lazyload, Lightbox and more', 'advanced-responsive-video-embedder' )
		);
	}

	return $links;
}

function admin_enqueue_styles(): void {

	enqueue_asset(
		array(
			'handle' => 'advanced-responsive-video-embedder',
			'src'    => plugins_url( 'build/admin.css', PLUGIN_FILE ),
			'path'   => PLUGIN_DIR . '/build/admin.css',
			'deps'   => array( 'nextgenthemes-settings' ),
		)
	);
}

function get_first_glob( string $pattern ): string {

	$res = glob( $pattern, GLOB_NOSORT | GLOB_ERR );

	if ( empty( $res[0] ) ) {
		return '';
	}

	return $res[0];
}

function admin_enqueue_scripts(): void {

	foreach ( settings( 'shortcode' )->get_all() as $k => $v ) {
		$options[ $k ] = '';
	}

	$settings_data = array(
		'options'          => $options,
		'nonce'            => wp_create_nonce( 'wp_rest' ),
		'settings'         => settings( 'shortcode' )->to_array(),
		'tabs'             => settings_tabs(),
	);

	if ( ! is_gutenberg() ) {

		register_asset(
			array(
				'handle'               => 'arve-shortcode-dialog',
				'src'                  => plugins_url( '/src/shortcode-dialog.js', PLUGIN_FILE ),
				'path'                 => PLUGIN_DIR . '/src/shortcode-dialog.js',
				'inline_script_before' => $settings_data,
				'strategy'             => 'defer',
			)
		);
	}

	enqueue_asset(
		array(
			'handle'               => 'arve-admin',
			'src'                  => plugins_url( 'build/admin.js', PLUGIN_FILE ),
			'path'                 => PLUGIN_DIR . '/build/admin.js',
			'inline_script_before' => 'var arveSCSettings = ' . \wp_json_encode( $settings_data ) . ';',
		)
	);

	if ( is_plugin_active( 'shortcode-ui/shortcode-ui.php' ) ) {
		enqueue_asset(
			array(
				'handle' => 'arve-admin-sc-ui',
				'path'   => PLUGIN_DIR . '/build/shortcode-ui.js',
				'src'    => plugins_url( 'build/shortcode-ui.js', PLUGIN_FILE ),
				'deps'   => array( 'shortcode-ui' ),
			)
		);
	}
}

function action_admin_bar_menu( \WP_Admin_Bar $admin_bar ): void {

	if ( current_user_can( 'manage_options' ) && options()['admin_bar_menu'] ) {

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
