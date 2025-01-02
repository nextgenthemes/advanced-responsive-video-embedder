<?php

declare(strict_types = 1);

namespace Nextgenthemes\WP;

use function wp_interactivity_data_wp_context as data_wp_context;

class Settings {
	private static $no_reset_sections = array( 'random-video', 'keys' );

	private string $menu_parent_slug = 'options-general.php';
	private string $menu_title;
	private string $settings_page_title;
	private string $slashed_namespace;
	private string $slugged_namespace;
	private string $camel_namespace;
	private string $rest_namespace;
	private string $base_path;
	private string $base_url;
	private ?string $plugin_file;
	private array $tabs;

	private array $options;
	private array $options_defaults;
	private array $options_defaults_by_section;

	/**
	 * Each setting is a SettingValidator object
	 *
	 * @var SettingsData <string, SettingValidator>
	 */
	private SettingsData $settings;
	private array $defined_keys = array();

	public function __construct( array $args ) {

		$this->menu_parent_slug    = $args['menu_parent_slug'] ?? $this->menu_parent_slug;
		$this->base_url            = trailingslashit( $args['base_url'] );
		$this->base_path           = trailingslashit( $args['base_path'] );
		$this->plugin_file         = $args['plugin_file'] ?? null;
		$this->tabs                = $args['tabs'];
		$this->menu_title          = $args['menu_title'];
		$this->settings            = $args['settings'];
		$this->settings_page_title = $args['settings_page_title'];
		$this->slugged_namespace   = \sanitize_key( str_replace( '\\', '_', $args['namespace'] ) );
		$this->camel_namespace     = camel_case( \sanitize_key( $this->slugged_namespace ), '\\' );
		$this->slashed_namespace   = str_replace( '_', '/', $this->slugged_namespace );
		$this->rest_namespace      = $this->slugged_namespace . '/v1';

		$this->set_default_options();

		$this->options = (array) get_option( $this->slugged_namespace, array() );
		$this->options = $this->options + $this->options_defaults;

		add_action( 'admin_enqueue_scripts', array( $this, 'assets' ), 9 );
		add_action( 'rest_api_init', array( $this, 'register_rest_route' ) );
		add_action( 'admin_menu', array( $this, 'register_setting_page' ) );

		if ( $this->plugin_file ) {
			add_filter( 'plugin_action_links_' . plugin_basename( $this->plugin_file ), array( $this, 'add_action_links' ), 5, 1 );
		}
	}

	public function add_action_links( array $links ): array {

		$default_headers = array(
			'ActionLink'  => 'Action Link',
		);

		$plugin_data = get_file_data( $this->plugin_file, $default_headers, 'plugin' );

		if ( ! empty( $plugin_data['ActionLink'] ) ) {
			preg_match( '/(?<text>.*?)(?<url>https\S+)/i', $plugin_data['ActionLink'], $matches );
		}

		if ( ! empty( $matches['url'] ) && ! empty( $matches['text'] ) ) {
			$extra_links['ngt-action-link'] = sprintf(
				'<a href="%s"><strong style="display: inline;">%s</strong></a>',
				esc_url( $matches['url'] ),
				esc_html( $matches['text'] )
			);
		}

		$extra_links['ngt-settings'] = sprintf(
			'<a href="%s">%s</a>',
			esc_url( admin_url( 'options-general.php?page=' . $this->slugged_namespace ) ),
			esc_html__( 'Settings' ) // phpcs:ignore WordPress.WP.I18n.MissingArgDomain
		);

		return array_merge( $extra_links, $links );
	}

	private function check_option_and_shortcode( array $setting ): void {

		if ( 'nextgenthemes_arve' === $this->slugged_namespace
			&& ( ! isset( $setting['option'] ) || ! isset( $setting['shortcode'] ) )
		) {
			wp_trigger_error( __METHOD__, 'option or shortcode must be set in ARVE' );
		}
	}

	public function setup_license_options(): void {
		$this->set_defined_product_keys();
		add_action( 'admin_init', [ $this, 'action_admin_init' ], 0 );
	}

	public function action_admin_init(): void {
		Admin\activation_notices();
		Admin\init_edd_updaters( $this->options );
	}

	private function set_default_options(): void {

		foreach ( $this->settings->get_all() as $key => $setting ) {
			$this->options_defaults[ $key ]                             = $setting->default;
			$this->options_defaults_by_section[ $setting->tab ][ $key ] = $setting->default;
		}
	}

	/**
	 * @param mixed $value
	 */
	public function __set( string $name, $value ): void {

		if ( ! property_exists( __CLASS__, $name ) ) {
			wp_trigger_error( __METHOD__, "Trying to set property '$name', but it does not exits" );
			return;
		}

		$this->$name = $value;
	}

	public function add_edd_updaters(): void {
		add_action( 'admin_init', [ $this, 'action_edd_updaters' ], 0 );
	}

	public function action_edd_updaters(): void {
		Admin\init_edd_updaters( $this->options );
	}

	public function set_defined_product_keys(): void {

		$products = get_products();
		foreach ( $products as $p => $value ) {
			$defined_key = get_defined_key( $p );
			if ( $defined_key ) {
				$this->options[ $p ]  = $defined_key;
				$this->defined_keys[] = $p;
			}
		}
	}

	public function get_options(): array {
		$options = (array) get_option( $this->slugged_namespace, array() );
		$options = $options + $this->options_defaults;

		return apply_filters( $this->slashed_namespace . '/settings', $options );
	}

	public function get_options_defaults(): array {
		return $this->options_defaults;
	}

	public function get_settings(): SettingsData {
		return $this->settings;
	}

	public function save_options( array $options ): void {
		// remove all items from options that are not also in defaults.
		$options = array_diff_assoc( $options, $this->options_defaults );
		// store only the options that differ from the defaults.
		$options = array_intersect_key( $options, $this->options_defaults );

		update_option( $this->slugged_namespace, $options );
	}

	public function register_rest_route(): void {

		// register save options route
		register_rest_route(
			$this->rest_namespace,
			'/save',
			array(
				'methods'             => 'POST',
				'args'                => $this->settings->to_array(),
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
				'callback'            => function ( \WP_REST_Request $request ): \WP_REST_Response {
					$this->save_options( $request->get_params() );
					return rest_ensure_response( __( 'Options saved', 'advanced-responsive-video-embedder' ) );
				},
			)
		);

		// register EDD license action route
		register_rest_route(
			$this->rest_namespace,
			'/edd-license-action',
			array(
				'methods'             => 'POST',
				'args'                => array(
					'edd_store_url' => array(
						'type'     => 'string',
						'required' => true,
					),
					'option_key' => array(
						'type'     => 'string',
						'required' => true,
					),
					// edd api args below
					'edd_action' => array(
						'type'     => 'string',
						'required' => true,
					),
					'item_id' => array(
						'type'     => 'integer',
						'required' => true,
					),
					'license' => array(
						'type'     => 'string',
						'required' => true,
					),
				),
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
				'callback'            => function ( \WP_REST_Request $request ) {

					$p = $request->get_params();

					if ( ! in_array( $p['edd_action'], array( 'activate_license', 'deactivate_license', 'check_license' ), true ) ) {
						return new \WP_Error( 'invalid_action', 'Invalid action', array( 'status' => 500 ) );
					}

					$options = $this->get_options();
					$options[ $p['option_key'] ] = $p['license'];
					$options[ $p['option_key'] . '_status' ] = api_action( (int) $p['item_id'], $p['license'], $p['edd_action'], $p['edd_store_url'] );

					$this->save_options( $options );
					return rest_ensure_response( $options[ $p['option_key'] . '_status' ] );
				},
			)
		);

		if ( function_exists( '\Nextgenthemes\ARVE\delete_oembed_cache' ) ) {

			register_rest_route(
				$this->rest_namespace,
				'/delete-oembed-cache',
				array(
					'methods'             => 'POST',
					'permission_callback' => function () {
						#return true;
						return current_user_can( 'manage_options' );
					},
					'callback'            => function (): \WP_REST_Response {
						return rest_ensure_response( \Nextgenthemes\ARVE\delete_oembed_cache() );
					},
				)
			);
		}
	}

	public function assets( string $page ): void {

		$asset_info = Asset::deps_and_ver( $this->base_path . 'vendor/nextgenthemes/wp-shared/includes/WP/Admin/settings.js' );

		// always register this as the ARVE Shortcode dialog uses this.
		wp_register_script_module(
			'nextgenthemes-settings',
			$this->base_url . 'vendor/nextgenthemes/wp-shared/includes/WP/Admin/settings.js',
			$asset_info['dependencies'] + [ '@wordpress/interactivity' ],
			$asset_info['version']
		);

		// always register this as the ARVE Shortcode dialog uses styles from this.
		register_asset(
			array(
				'handle' => 'nextgenthemes-settings',
				'src'    => $this->base_url . 'vendor/nextgenthemes/wp-shared/includes/WP/Admin/settings.css',
				'path'   => __DIR__ . '/settings.css',
			)
		);

		// Check if we are currently viewing our setting page
		if ( ! str_ends_with( $page, $this->slugged_namespace ) ) {
			return;
		}

		wp_enqueue_script_module( 'nextgenthemes-settings' );
		wp_enqueue_style( 'nextgenthemes-settings' );
	}

	public function print_admin_page(): void {

		wp_enqueue_media();

		$sections_camel_keys = array_map_key( 'Nextgenthemes\WP\camel_case', $this->tabs );
		$active_tabs         = array_map( '__return_false', $sections_camel_keys );

		$active_tabs[ array_key_first( $active_tabs ) ] = true;

		wp_interactivity_config(
			$this->slugged_namespace,
			[
				'restUrl'        => get_rest_url( null, $this->rest_namespace ),
				'nonce'          => wp_create_nonce( 'wp_rest' ),
				'siteUrl'        => get_site_url(),
				'homeUrl'        => get_home_url(),
				'defaultOptions' => $this->options_defaults_by_section,
			]
		);

		wp_interactivity_state(
			$this->slugged_namespace,
			[
				'options'    => $this->options,
				'message'    => '',
				'help'       => true,
			]
		);

		ob_start();
		?>

		<div 
			class="wrap wrap--nextgenthemes"
			data-wp-interactive="<?= esc_attr( $this->slugged_namespace ); ?>"
			<?=
			data_wp_context( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				[
					'activeTabs' => $active_tabs,
					'help'       => true,
				]
			);
			?>
		>
			<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

			<?php if ( is_plugin_active( 'all-in-one-seo-pack/all_in_one_seo_pack.php' ) ) : ?>
				<p class="ngt-sidebar-box">
					<strong>
						<?php
						printf(
							kses_https_link(
								// translators: %1$s and %2$s are links
								__(
									'There is a compatibility issue with the All in One SEO Pack plugin that prevents this settings page from working. Please deactivate All in One SEO Pack temporarily to make ARVE Settings and contact their <a href="%1$s">support</a> / <a href="%2$s">support for pro users</a> to ask them if they can resolve this issue.',
									'advanced-responsive-video-embedder'
								),
							),
							'https://wordpress.org/support/plugin/all-in-one-seo-pack/#new-topic-0',
							'https://aioseo.com/login/?redirect_to=%2Faccount%2Fsupport%2F'
						);
						?>
					</strong>
				</p>
			<?php endif; ?>

			<h2 class="nav-tab-wrapper">
				<?php foreach ( $sections_camel_keys as $k => $v ) : ?>
					<button
						class="nav-tab"
						data-wp-on--click="actions.changeTab"
						data-wp-class--nav-tab-active="context.activeTabs.<?= esc_attr( $k ); ?>"
						<?= data_wp_context( [ 'tab' => $k ] ); // phpcs:ignore ?>
					>
						<?= esc_html( $v['title'] ); ?>
					</button>
				<?php endforeach; ?>
			</h2>

			<div class="ngt-settings-grid">

				<div class="ngt-settings-grid__content">

					<?php
					do_action( $this->slashed_namespace . '/admin/settings/content', $this );

					Admin\print_settings_blocks(
						$this->settings,
						$this->tabs
					);

					$this->print_reset_buttons();
					?>
				</div>

				<div class="ngt-settings-grid__sidebar">

					<p></p>
					<p><span data-wp-text="state.message"></span>&nbsp;</p>

					<pre data-wp-text="state.debug"></pre>

					<?php do_action( $this->slashed_namespace . '/admin/settings/sidebar', $this ); ?>
				</div>
			</div>
		</div>

		<?php
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo wp_interactivity_process_directives( ob_get_clean() );
	}

	private function print_reset_buttons(): void {
		?>
		<p>
			<?php
			foreach ( $this->tabs as $key => $tab ) {

				$reset_btn = $tab['reset_button'] ?? true;

				if ( ! $reset_btn ) {
					continue;
				}

				?>
				<button
					class="button button-secondary"
					type="button"
					data-wp-bind--hidden="!state.isActiveTab"
					data-wp-on--click="actions.resetOptionsSection"
					<?= data_wp_context( [ 'tab' => $key ] ); // phpcs:ignore ?>
				>
					<?php
					printf(
						// translators: Options section
						esc_html__( 'Reset %s section', 'advanced-responsive-video-embedder' ),
						esc_html( $tab['title'] )
					);
					?>
				</button>
				<?php
			}
			?>
			<span data-wp-text="state.message"></span>
		</p>
		<?php
	}

	public function register_setting_page(): void {

		$parent_slug = $this->menu_parent_slug;
		// The HTML Document title for our settings page.
		$page_title = $this->settings_page_title;
		// The menu item title for our settings page.
		$menu_title = $this->menu_title;
		// The user permission required to view our settings page.
		$capability = 'manage_options';
		// The URL slug for our settings page.
		$menu_slug = $this->slugged_namespace;
		// The callback function for rendering our settings page HTML.
		$callback = array( $this, 'print_admin_page' );

		add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $callback );
	}
}
