<?php declare(strict_types=1);
namespace Nextgenthemes\WP;

use Exception;

class Settings {
	private static $no_reset_sections = array( 'debug', 'random-video', 'keys' );

	private string $menu_parent_slug;
	private string $menu_title;
	private string $settings_page_title;
	private string $slashed_namespace;
	private string $slugged_namespace;
	private string $rest_namespace;
	private string $base_path;
	private string $base_url;

	public array $sections;
	private array $options;
	private array $options_defaults;
	private array $premium_sections;
	private array $settings;

	public function __construct( $args ) {

		$defaults = array(
			'menu_parent_slug'    => 'options-general.php',
			'sections'            => array( 'main' => 'Main' ),
			'premium_sections'    => array(),
			'settings_page_title' => 'Default Page Title',
			'default_menu_title'  => 'Default Menu Title',
		);

		$args = wp_parse_args( $args, $defaults );

		$this->base_url  = $args['base_url'];
		$this->base_path = $args['base_path'];

		$this->settings            = $args['settings'];
		$this->sections            = $args['sections'];
		$this->premium_sections    = $args['premium_sections'];
		$this->menu_title          = $args['menu_title'];
		$this->settings_page_title = $args['settings_page_title'];
		$this->slugged_namespace   = sanitize_key( str_replace( '\\', '_', $args['namespace'] ) );
		$this->slashed_namespace   = str_replace( '_', '/', $this->slugged_namespace );
		$this->rest_namespace      = $this->slugged_namespace . '/v1';
		$this->menu_parent_slug    = $args['menu_parent_slug'];

		$this->set_namespaces( $args['namespace'] );
		$this->set_default_options();

		$this->options = (array) get_option( $this->slugged_namespace, array() );
		$this->options = $this->options + $this->options_defaults;

		add_action( 'admin_enqueue_scripts', array( $this, 'assets' ) );
		add_action( 'rest_api_init', array( $this, 'register_rest_route' ) );
		add_action( 'admin_menu', array( $this, 'register_setting_page' ) );
	}

	private function set_default_options() {

		foreach ( $this->settings as $key => $value ) {

			$type = $this->get_php_type_from_setting( $key );

			if ( gettype( $value['default'] ) !== $type ) {

				if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
					d($key, $type, $value['default']);
				}

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

	public static function has_bool_default_options( array $array ) {

		return ! array_diff_key(
			$array,
			array(
				''      => true,
				'true'  => true,
				'false' => true,
			)
		);
	}

	private function set_namespaces( string $namespace ) {
		$this->slugged_namespace = sanitize_key( str_replace( '\\', '_', $namespace ) );
		$this->slashed_namespace = str_replace( '_', '/', $this->slugged_namespace );
		$this->rest_namespace    = $this->slugged_namespace . '/v1';
	}

	public function __set($name, $value) {

		if ( ! property_exists( __CLASS__, $name ) ) {
			throw new Exception( "Trying to set property '$name', but it does not exits" );
		}

		$this->$name = $value;
	}

	public function set_defined_product_keys() {

		$products = \Nextgenthemes\ARVE\Common\get_products();
		foreach ( $products as $p => $value ) {
			$defined_key = \Nextgenthemes\ARVE\Common\get_defined_key( $p );
			if ( $defined_key ) {
				$this->options[ $p ] = $defined_key;
			}
		}
	}

	public function get_options() {
		$options = (array) get_option( $this->slugged_namespace, array() );
		$options = $options + $this->options_defaults;
		return $options;
	}

	public function get_options_defaults() {
		return $this->options_defaults;
	}

	public function save_options( $options ) {

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

	public function register_rest_route() {

		register_rest_route(
			$this->rest_namespace,
			'/save',
			array(
				'methods'             => 'POST',
				'args'                => $this->settings,
				'permission_callback' => function() {
					return current_user_can( 'manage_options' );
				},
				'callback'            => function( \WP_REST_Request $request ) {
					$this->save_options( $request->get_params() );
					die( '1' );
				},
			)
		);
	}

	public function assets( $page ) {

		enqueue_asset(
			array(
				'handle' => 'nextgenthemes-settings',
				'src'    => trailingslashit( $this->base_url ) . 'fake-vendor/nextgenthemes/WP_Settings/public/build/bundle.css',
				'path'   => trailingslashit( $this->base_path ) . 'fake-vendor/nextgenthemes/WP_Settings/public/build/bundle.css',
			)
		);

		// Check if we are currently viewing our setting page
		if ( ! str_ends_with( $page, $this->slugged_namespace ) ) {
			return;
		}

		$settings_data = array(
			'options'  => $this->options,
			'home_url' => get_home_url(),
			'rest_url' => esc_url( get_rest_url( null, $this->rest_namespace ) ),
			'nonce'    => wp_create_nonce( 'wp_rest' ),
			'settings' => $this->settings,
			'sections' => $this->sections,
		);

		enqueue_asset(
			array(
				'handle'               => 'nextgenthemes-settings',
				'src'                  => trailingslashit( $this->base_url ) . 'fake-vendor/nextgenthemes/WP_Settings/public/build/bundle.js',
				'path'                 => trailingslashit( $this->base_path ) . 'fake-vendor/nextgenthemes/WP_Settings/public/build/bundle.js',
				'deps'                 => array( 'jquery' ),
				'inline_script_before' => "var {$this->slugged_namespace} = " . \wp_json_encode( $settings_data ) . ';',
			)
		);
	}

	private function print_settings_tabs() {
		?>
		<h2 class="nav-tab-wrapper">
			<a @click='showAllSectionsButDebug()'
				class="nav-tab">All Options</button>
			<?php
			foreach ( $this->sections as $slug => $name ) :
				$classes = in_array( $slug, $this->premium_sections, true ) ? 'nav-tab nav-tab--ngt-highlight' : 'nav-tab';
				?>
				<a @click="showSection('<?php echo esc_attr( $slug ); ?>')"
					class="<?php echo esc_attr( $classes ); ?>"
					v-bind:class='{ "nav-tab-active": sectionsDisplayed["<?php echo esc_attr( $slug ); ?>"] }'>
					<?php echo esc_html( $name ); ?>
				</a>
			<?php endforeach; ?>
		</h2>
		<?php
	}

	public function print_save_section() {
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

	private function print_paid_section_message() {

		if ( empty( $this->premium_sections ) ) {
			return;
		}

		foreach ( $this->premium_sections as $slug ) {
			$d_sections[] = sprintf( "sectionsDisplayed['%s']", esc_attr( $slug ) );
		}

		$v_show = implode( ' || ', $d_sections );
		?>
		<div class="ngt-block" v-show="<?php echo $v_show; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>">
			<p><?php esc_html_e( 'You may already set options for addons but they will only take effect if the associated addons are installed.', 'advanced-responsive-video-embedder' ); ?>
			</p>
		</div>
		<?php
	}

	private function print_reset_bottons() {
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

	public function print_errors() {
		?>
		<div class="ngt-block"
			v-if="errors.length">
			<strong>Please correct the following error(s):</strong>
			<ul>
				<li v-for="error in errors">{{ error }}</li>
			</ul>
		</div>
		<?php
	}

	public function print_admin_page() {
		?>
		<div class="wrap wrap--nextgenthemes">
			<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

			<div id="ngt-settings-svelte"></div>

			<?php
			do_action( $this->slashed_namespace . '/admin/settings/content', $this );
			?>

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

	public function register_setting_page() {

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
