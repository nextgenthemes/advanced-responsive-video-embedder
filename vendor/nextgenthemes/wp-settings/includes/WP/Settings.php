<?php

declare(strict_types = 1);

namespace Nextgenthemes\WP;

use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use function wp_interactivity_data_wp_context as data_wp_context;

/**
 * @phpstan-import-type NgtSettingValue from SettingValidator
 */
class Settings {
	/**
	 * The slug of the parent menu under which the settings menu will appear.
	 */
	private string $menu_parent_slug;

	private string $menu_title;
	private string $settings_page_title;

	/**
	 * The namespace with slashes for internal use.
	 * Gets generated from a php namespace.
	 * example: nextgenthemes/arve
	 */
	private string $slashed_namespace;

	/**
	 * The namespace with slashes for internal use.
	 * Gets generated from a php namespace.
	 * example: nextgenthemes-arve
	 */
	private string $slugged_namespace;

	/**
	 * Flag to indicate if the current instance is for ARVE.
	 */
	private bool $is_arve;

	/**
	 * The REST API namespace.
	 */
	private string $rest_namespace;

	/**
	 * The base path of the plugin.
	 */
	private string $base_path;

	/**
	 * The base URL of the plugin.
	 */
	private string $base_url;

	/**
	 * The plugin file path, if available.
	 */
	private ?string $plugin_file;

	/**
	 * Tabs for the Setting Page
	 *
	 * @var array{
	 *     string: array{
	 *         title: string,
	 *         premium_link?: string,
	 *         reset_button?: bool
	 *     }
	 * }
	 *
	 * Example:
	 *      $tags = array(
	 *          'tab_name' => array(
	 *              'title'        => __( 'Tab Name', 'slug' ),
	 *              'premium_link' => sprintf( // optional parameter
	 *                  '<a href="%s">%s</a>',
	 *                  'https://nextgenthemes.com/plugins/arve-random-video/',
	 *                  __( 'Random Videos Addon', 'slug' )
	 *              ),
	 *              'reset_button' => false, // optional parameter, true by default
	 *          ),
	 *      );
	 */
	private array $tabs;

	/**
	 * Array of current option values.
	 *
	 * @var array <string, NgtSettingValue>
	 */
	private array $options;

	/**
	 * Array of default option values.
	 *
	 * @var array <string, NgtSettingValue>
	 */
	private array $options_defaults;

	/**
	 * Array of default option values organized by section.
	 *
	 * @var array <string, array<string, NgtSettingValue>>
	 */
	private array $options_defaults_by_section;

	/**
	 * Each setting is a SettingValidator object
	 */
	private SettingsData $settings;

	/**
	 * @var array <string, string>
	 */
	private array $defined_keys = array();

	/**
	 * @param array <int|string, mixed> $args
	 */
	public function __construct( array $args ) {

		$this->menu_parent_slug    = $args['menu_parent_slug'] ?? 'options-general.php';
		$this->base_url            = trailingslashit( $args['base_url'] );
		$this->base_path           = trailingslashit( $args['base_path'] );
		$this->plugin_file         = $args['plugin_file'] ?? null;
		$this->tabs                = $args['tabs'];
		$this->menu_title          = $args['menu_title'];
		$this->settings            = $args['settings'];
		$this->settings_page_title = $args['settings_page_title'];
		$this->slugged_namespace   = sanitize_key( str_replace( '\\', '_', $args['namespace'] ) );
		$this->slashed_namespace   = str_replace( '_', '/', $this->slugged_namespace );
		$this->rest_namespace      = $this->slugged_namespace . '/v1';
		$this->is_arve             = 'nextgenthemes_arve' === $this->slugged_namespace;

		$this->set_default_options();
		$this->set_options();

		add_action( 'admin_enqueue_scripts', array( $this, 'assets' ), 9 );
		add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
		add_action( 'admin_menu', array( $this, 'register_setting_page' ) );

		if ( $this->plugin_file ) {
			add_filter( 'plugin_action_links_' . plugin_basename( $this->plugin_file ), array( $this, 'add_action_links' ), 5, 1 );
		}

		add_filter( "option_{$this->slugged_namespace}", [ $this, 'get_options_with_defaults' ], 10, 1 );
		add_filter( "pre_update_option_{$this->slugged_namespace}", [ $this, 'pre_update_options' ], 10, 2 );
	}

	/**
	 * Handle option updates for namespaced options
	 *
	 * @param mixed $new_options
	 * @param mixed $old_options
	 *
	 * @return array <string, NgtSettingValue> The updated option value
	 */
	public function pre_update_options( $new_options, $old_options ): array {

		$new_options = is_array( $new_options ) ? $new_options : array();
		$old_options = is_array( $old_options ) ? $old_options : array();

		// Merge new values with existing options
		$updated_options = array_merge( $old_options, $new_options );
		// remove all items match exact key-value pairs in defaults
		$updated_options = array_diff_assoc( $updated_options, $this->options_defaults );
		// remove all items that have a key that is not in defaults
		$updated_options = array_intersect_key( $updated_options, $this->options_defaults );

		return $updated_options;
	}

	/**
	 * Set options from database with proper filtering
	 */
	private function set_options(): void {
		$stored_options = (array) get_option( $this->slugged_namespace, array() );

		// Remove all items that have a key that is not in defaults
		$this->options = array_intersect_key( $stored_options, $this->options_defaults );

		// Merge with defaults to ensure all keys exist
		$this->options = $this->options + $this->options_defaults;
	}

	/**
	 * Handle option updates for namespaced options
	 *
	 * @param mixed $options
	 *
	 * @return array <string, NgtSettingValue> The updated option value
	 */
	public function get_options_with_defaults( $options ): array {

		// in case the options are saved as the wrong type (we could also fatal error this by tying it to array)
		$options = is_array( $options ) ? $options : array();
		$options = $options + $this->options_defaults;

		return $options;
	}

	/**
	 * @param array <int|string, string> $links
	 * @return array <int|string, string> Modified links
	 */
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

	/**
	 * @return array <string, NgtSettingValue>
	 */
	public function get_options(): array {
		$options = (array) get_option( $this->slugged_namespace, array() );

		// Why the fuck is this needed for unit tests? The get_options_with_defaults seems not to be called in the unit tests
		$options = $options + $this->options_defaults;

		return apply_filters( $this->slashed_namespace . '/options', $options );
	}

	/**
	 * @return array <string, NgtSettingValue>
	 */
	public function get_options_defaults(): array {
		return $this->options_defaults;
	}

	public function get_settings(): SettingsData {
		return $this->settings;
	}

	/**
	 * @param array <string, NgtSettingValue> $options
	 */
	public function save_options( array $options ): void {
		update_option( $this->slugged_namespace, $options );
	}

	/**
	 * @param NgtSettingValue $value
	 */
	public function update_option( string $key, $value ): void {
		$options         = $this->get_options();
		$options[ $key ] = $value;
		$this->save_options( $options );
	}

	private function register_settings_with_rest(): void {

		$schema_properties = array();

		foreach ( $this->settings->get_all() as $key => $setting ) {
			$schema_properties[ $key ]['type']    = $setting->type;
			$schema_properties[ $key ]['default'] = $setting->default;
		}

		// register settings to be updatable with the default /wp-json/wp/v2/settings endpoint
		register_setting(
			$this->slashed_namespace . '_options_group', // Group name.
			$this->slugged_namespace, // Single option name for the entire array.
			array(
				'type'                => 'object',
				'label'               => $this->slugged_namespace . ' Settings',
				'description'         => 'Settings for ' . $this->slugged_namespace,
				'show_in_rest'        => array(
					'schema' => array(
						'type'                 => 'object', // Matches the 'type' above.
						'properties'           => $schema_properties, // Define types for each key in the array.
						'additionalProperties' => false, // Optional: Prevent extra keys.
					),
				),
			)
		);
	}

	public function register_rest_routes(): void {

		$this->register_settings_with_rest();

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
				'callback'            => function ( WP_REST_Request $request ): WP_REST_Response {
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
				/** @return WP_Error|WP_REST_Response */
				'callback'            => function ( WP_REST_Request $request ) {

					$p = $request->get_params();

					if ( ! in_array( $p['edd_action'], array( 'activate_license', 'deactivate_license', 'check_license' ), true ) ) {
						return new WP_Error( 'invalid_action', 'Invalid action', array( 'status' => 500 ) );
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
				'/delete-caches',
				array(
					'methods'             => 'POST',
					'args'                => array(
						'type' => array(
							'required' => true,
							'type'     => 'string',
							'default'  => '',
						),
						'like' => array(
							'required' => false,
							'type'     => 'string',
							'default'  => '',
						),
						'not_like' => array(
							'required' => false,
							'type'     => 'string',
							'default'  => '',
						),
						'prefix' => array(
							'required' => false,
							'type'     => 'string',
							'default'  => '',
						),
						'delete_option' => array(
							'required' => false,
							'type'     => 'string',
							'default'  => '',
						),
					),
					'permission_callback' => function () {
						return current_user_can( 'manage_options' );
					},
					/** @return WP_Error|WP_REST_Response */
					'callback'            => function ( WP_REST_Request $request ) {

						$p = $request->get_params();

						if ( ! empty( $p['delete_option'] ) ) {
							delete_option( $p['delete_option'] );
							// just do this silently and continue to so we can clear caches at the same time.
						}

						switch ( $p['type'] ) {
							case 'oembed':
								return rest_ensure_response( \Nextgenthemes\ARVE\delete_oembed_cache( $p['like'], $p['not_like'] ) );
							case 'transients':
								return rest_ensure_response( \Nextgenthemes\ARVE\delete_transients( $p['prefix'], $p['like'] ) );
							case 'flush_object_cache':
							case 'wp_cache_flush':
								return rest_ensure_response( wp_cache_flush() );
							default:
								return ( new WP_Error( 'invalid_type', 'Invalid type', array( 'status' => 500 ) ) );
						}
					},
				)
			);
		}
	}

	public function assets( string $page ): void {

		$asset_data = require $this->base_path . 'vendor/nextgenthemes/wp-settings/build/settings.asset.php';

		// always register this as the ARVE Shortcode dialog uses this.
		wp_register_script_module(
			'nextgenthemes-settings',
			$this->base_url . 'vendor/nextgenthemes/wp-settings/build/settings.js',
			$asset_data['dependencies'],
			$asset_data['version']
		);

		// always register this as the ARVE Shortcode dialog uses styles from this.
		wp_register_style(
			'nextgenthemes-settings',
			$this->base_url . 'vendor/nextgenthemes/wp-settings/build/settings.css',
			array(),
			$asset_data['version'],
		);

		$page_for_this_namespace = str_ends_with( $page, $this->slugged_namespace );

		// Check if we are currently viewing our setting page
		if ( ! $this->is_arve && ! $page_for_this_namespace ) {
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
				'restUrl'         => get_rest_url( null, $this->rest_namespace ),
				'restSettingsUrl' => get_rest_url( null, '/wp/v2/settings' ),
				'nonce'           => wp_create_nonce( 'wp_rest' ),
				'siteUrl'         => get_site_url(),
				'homeUrl'         => get_home_url(),
				'defaultOptions'  => $this->options_defaults_by_section,
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

		<div class="wrap wrap--nextgenthemes">

			<div class="ngt-width-limiter">
				<h1><?= esc_html( get_admin_page_title() ); ?></h1>
			</div>

			<div
				class="ngt-settings-interactive"
				data-wp-interactive="<?= esc_attr( $this->slugged_namespace ); ?>"
				<?php
				echo data_wp_context( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					[
						'activeTabs' => $active_tabs,
						'help'       => true,
						'settings'   => $this->settings->to_array(),
					]
				);
				?>
			>
				<h2 class="nav-tab-wrapper ngt-full-width">
					<div class="ngt-width-limiter">
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
					</div>
				</h2>

				<div class="ngt-settings-bg ngt-full-width">
					<div class="ngt-settings-grid ngt-width-limiter">

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
						
					</div><!-- .ngt-settings-grid -->
				</div><!-- .ngt-settings-bg -->

			</div><!-- .ngt-settings-interactive -->
		</div><!-- .wrap--nextgenthemes -->

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

		add_submenu_page(
			$this->menu_parent_slug,
			$this->settings_page_title,        // The HTML Document title for our settings page.
			$this->menu_title,
			'manage_options',                  // The user permission required to view our settings page.
			$this->slugged_namespace,          // The URL slug for our settings page.
			array( $this, 'print_admin_page' ) // The callback function for rendering our settings page HTML.
		);
	}
}
