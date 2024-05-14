<?php declare(strict_types=1);
namespace Nextgenthemes\WP;

use Exception;

class Settings {
	private static $no_reset_sections = array( 'debug', 'random-video', 'keys' );

	private string $menu_parent_slug = 'options-general.php';
	private string $menu_title;
	private string $settings_page_title;
	private string $slashed_namespace;
	private string $slugged_namespace;
	private string $rest_namespace;
	private string $base_path;
	private string $base_url;
	private string $premium_url_prefix = '';

	private array $sections = array( 'main' => 'Main' );
	private array $options;
	private array $options_defaults;
	private array $premium_sections = array();
	private array $settings;
	private array $defined_keys = array();

	public function __construct( array $args ) {

		$optional_args = [ 'menu_parent_slug', 'sections', 'premium_sections', 'premium_url_prefix' ];

		foreach ( $optional_args as $optional_arg ) {

			if ( isset( $args[ $optional_arg ] ) ) {
				$this->$optional_arg = $args[ $optional_arg ];
			}
		}

		$this->base_url  = trailingslashit( $args['base_url'] );
		$this->base_path = trailingslashit( $args['base_path'] );

		$this->settings            = $args['settings'];
		$this->sections            = $args['sections'];
		$this->menu_title          = $args['menu_title'];
		$this->settings_page_title = $args['settings_page_title'];
		$this->slugged_namespace   = \sanitize_key( str_replace( '\\', '_', $args['namespace'] ) );
		$this->slashed_namespace   = str_replace( '_', '/', $this->slugged_namespace );
		$this->rest_namespace      = $this->slugged_namespace . '/v1';

		$this->set_default_options();

		$this->options = (array) get_option( $this->slugged_namespace, array() );
		$this->options = $this->options + $this->options_defaults;

		add_action( 'admin_enqueue_scripts', array( $this, 'assets' ), 9 );
		add_action( 'rest_api_init', array( $this, 'register_rest_route' ) );
		add_action( 'admin_menu', array( $this, 'register_setting_page' ) );
	}

	public function setup_license_options(): void {
		$this->set_defined_product_keys();
		add_action( 'admin_init', [ $this, 'action_admin_init' ], 0 );
	}

	public function action_admin_init(): void {
		Admin\activation_notices();
		Admin\init_edd_updaters( $this->options );
	}

	private function set_namespaces( string $namespace ): void {
		$this->slugged_namespace = sanitize_key( str_replace( '\\', '_', $namespace ) );
		$this->slashed_namespace = str_replace( '_', '/', $this->slugged_namespace );
		$this->rest_namespace    = $this->slugged_namespace . '/v1';
	}

	private function set_default_options(): void {

		foreach ( $this->settings as $key => $value ) {

			$type = $this->get_php_type_from_setting( $key );

			if ( gettype( $value['default'] ) !== $type ) {
				throw new Exception( "Default value for '$key' has wring type" );
			}

			$this->options_defaults[ $key ] = $value['default'];
		}
	}

	public function get_php_type_from_setting( string $setting_key ): string {

		$setting_props = $this->settings[ $setting_key ];

		if ( 'select' === $setting_props['type'] ) {
			if ( ! empty( $setting_props['options'] ) && $this->has_bool_default_options( $setting_props['options'] ) ) {
				return 'boolean';
			}
			return 'string';
		}

		return $setting_props['type'];
	}

	public static function has_bool_default_options( array $array ): bool {

		return ! array_diff_key(
			$array,
			array(
				''      => true,
				'true'  => true,
				'false' => true,
			)
		);
	}

	/**
	 * @param mixed $value
	 */
	public function __set( string $name, $value ): void {

		if ( ! property_exists( __CLASS__, $name ) ) {
			throw new Exception( "Trying to set property '$name', but it does not exits" );
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

	public function save_options( array $options ): void {

		if ( 'nextgenthemes' === $this->slugged_namespace ) {

			$action            = json_decode( $options['action'] );
			$options['action'] = 'str';

			if ( $action ) {
				$product_id  = get_products()[ $action->product ]['id'];
				$product_key = $options[ $action->product ];

				$options[ $action->product . '_status' ] = api_action( $product_id, $product_key, $action->action );
			}
		}

		// remove all items from options that are not also in defaults.
		$options = array_diff_assoc( $options, $this->options_defaults );
		// store only the options that differ from the defaults.
		$options = array_intersect_key( $options, $this->options_defaults );

		update_option( $this->slugged_namespace, $options );
	}

	public function register_rest_route(): void {

		register_rest_route(
			$this->rest_namespace,
			'/save',
			array(
				'methods'             => 'POST',
				'args'                => $this->settings,
				'permission_callback' => function() {
					return current_user_can( 'manage_options' );
				},
				'callback'            => function( \WP_REST_Request $request ): void {
					$this->save_options( $request->get_params() );
					die( '1' );
				},
			)
		);

		if ( function_exists( '\Nextgenthemes\ARVE\delete_oembed_cache' ) ) {

			register_rest_route(
				$this->rest_namespace,
				'/delete-oembed-cache',
				array(
					'methods'             => 'POST',
					'permission_callback' => function() {
						return current_user_can( 'manage_options' );
					},
					'callback'            => function( \WP_REST_Request $request ) {
						
						$deleted_rows = \Nextgenthemes\ARVE\delete_oembed_cache();

						if ( false === $deleted_rows ) {
							return new \WP_Error( 'rest_delete_oembed_cache_failed', 'Could not delete oEmbed cache', array( 'status' => 500 ) );
						}

						return rest_ensure_response( $deleted_rows );
					}
				)
			);
		}
	}

	public function assets( string $page ): void {

		// register to use in other places
		register_asset(
			array(
				'handle'   => 'alpinejs',
				'src'      => $this->base_url . 'vendor/nextgenthemes/wp-shared/includes/WP/Admin/alpine.js',
				'path'     => __DIR__ . '/alpine.js',
				'strategy' => 'defer',
			)
		);

		// always load this as the ARVE Shortcode dialog uses styles from this.
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

		$settings_data = array(
			'options'          => $this->options,
			'home_url'         => get_home_url(),
			'restUrl'          => esc_url( get_rest_url( null, $this->rest_namespace ) ),
			'nonce'            => wp_create_nonce( 'wp_rest' ),
			'settings'         => $this->settings,
			'sections'         => $this->sections,
			'premiumSections'  => $this->premium_sections,
			'premiumUrlPrefix' => $this->premium_url_prefix,
			'definedKeys'      => $this->defined_keys,
		);

		register_asset(
			array(
				'handle'               => 'nextgenthemes-settings',
				'src'                  => $this->base_url . 'vendor/nextgenthemes/wp-shared/includes/WP/Admin/settings.js',
				'path'                 => __DIR__ . '/settings.js',
				'deps'                 => array( 'jquery' ),
				'inline_script_before' => "var {$this->slugged_namespace} = " . \wp_json_encode( $settings_data ) . ';',
				'strategy'             => 'defer',
			)
		);

		add_dep_to_script( 'alpinejs', 'nextgenthemes-settings' );
		wp_enqueue_script( 'alpinejs' );

		wp_enqueue_style( 'nextgenthemes-settings' );
	}

	private function get_index_js_filename(): void {

		$index_js_glob  = glob( $this->base_path . 'vendor/nextgenthemes/wp-shared/dist/assets/index-*.js' );
		$index_css_glob = glob( $this->base_path . 'vendor/nextgenthemes/wp-shared/dist/assets/index-*.css' );

		if ( empty( $index_js_glob[0] ) ) {
			return;
		}
	}

	private function print_settings_tabs(): void {
		?>
		<h2 class="nav-tab-wrapper">
			<a @click='showAllSectionsButDebug()'
				class="nav-tab">All Options</button>
			<?php
			foreach ( $this->sections as $slug => $name ) :
				$classes = in_array( $slug, $this->premium_sections, true ) ? 'nav - tab nav - tab--ngt - highlight' : 'nav - tab';
				?>
				<a @click="showSection(' < ? php echo esc_attr( $slug ); ?>')"
					class="<?php echo esc_attr( $classes ); ?>"
					v-bind:class='{ "nav-tab-active": sectionsDisplayed["<?php echo esc_attr( $slug ); ?>"] }'>
					<?php echo esc_html( $name ); ?>
				</a>
			<?php endforeach; ?>
		</h2>
		<?php
	}

	public function print_save_section(): void {
		?>
		<p v-show="onlySectionDisplayed !== 'debug'">
			<button @click='saveOptions'
				:disabled='isSaving'
				class='button button-primary'>
				Save
			</button>
			<strong v-if='message'>{{ message }}</strong>
			<img v-if='isSaving == true'
				class="wrap--nextgenthemes__loading-indicator"
				src='<?php echo esc_url( get_admin_url() . '/images/wpspin_light-2x.gif' ); ?>'
				alt='Loading indicator' />
		</p>
		<?php
	}

	private function print_reset_bottons(): void {
		?>
		<p>
		<?php
		foreach ( $this->sections as $key => $label ) {

			if ( in_array( $key, self::$no_reset_sections, true ) ) {
				continue;
			}

			?>
				<button @click="resetOptions('<?php echo esc_attr( $key ); ?>')"
					:disabled='isSaving'
					class='button button--ngt-reset button-secondary'
					v-show="sectionsDisplayed['<?php echo esc_attr( $key ); ?>']">
				<?php
				printf(
					// translators: Options section
					esc_html__( 'Reset %s section', 'advanced-responsive-video-embedder' ),
					esc_html( $label )
				);
				?>
				</button>
				<?php
		}
		?>
		</p>
		<?php
	}

	public function print_admin_page(): void {

		wp_enqueue_media();

		?>
		<div class="wrap wrap--nextgenthemes"
			x-data='<?php echo wp_json_encode( [ 'tab' => array_key_first( $this->sections ) ], JSON_PRETTY_PRINT, JSON_HEX_APOS ); ?>'
		>
			<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

			<h2 class="nav-tab-wrapper" >
				<div x-data='<?php echo wp_json_encode( [ 'sections' => $this->sections ], JSON_PRETTY_PRINT, JSON_HEX_APOS ); ?>'>
					<template x-for="[id, value] in Object.entries(sections)">
						<button class="nav-tab" :class="id === tab ? 'nav-tab-active' : ''" @click="tab = id">
							<span x-text="value"></span>
						</button>
					</template>
				</div>
			</h2>

			<div class="ngt-settings-grid" x-data="ngtsettings" x-init="$watch('options', value => saveOptions())">

				<div class="ngt-settings-grid__content">

					<?php do_action( $this->slashed_namespace . '/admin/settings/content', $this ); ?>

					<?php
					$prefix = str_replace( 'nextgenthemes_', '', $this->slugged_namespace );

					Admin\print_settings_blocks(
						$this->settings,
						$this->sections,
						$this->premium_sections,
						$prefix,
						$this->premium_url_prefix
					);
					?>

				</div>

				<div class="ngt-settings-grid__sidebar">

					<p></p>
					<p><span x-text="message"></span>&nbsp;</p>

					<?php do_action( $this->slashed_namespace . '/admin/settings/sidebar', $this ); ?>
				</div>
			</div>

			<?php
			/*
			do_action( $this->slashed_namespace . '/admin/settings/content', $this );
			$this->print_paid_section_message();
			$this->print_save_section();
			$this->print_debug_info_block();
			$this->print_save_section();
			$this->print_reset_bottons();
			*/
			?>
		</div>
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
