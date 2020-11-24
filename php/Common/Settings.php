<?php
namespace Nextgenthemes\ARVE\Common;

class Settings {

	private $menu_title          = '';
	private $option_key          = '';
	private $slugged_namspace    = '';
	private $slashed_namspace    = '';
	private $rest_namespace      = '';
	private $rest_url            = '';
	private $settings            = [];
	private $settings_page_title = '';
	private $options_defaults    = [];

	public function __construct( $args ) {

		$defaults = [
			'menu_parent_slug' => 'options-general.php',
		];

		$args = wp_parse_args( $args, $defaults );

		$this->settings            = $args['settings'];
		$this->menu_title          = $args['menu_title'];
		$this->settings_page_title = $args['settings_page_title'];
		$this->slugged_namespace   = sanitize_key( str_replace( '\\', '_', $args['namespace'] ) );
		$this->slashed_namespace   = str_replace( '_', '/', $this->slugged_namespace );
		$this->rest_namespace      = $this->slugged_namespace . '/v1';
		$this->rest_url            = get_home_url() . '/wp-json/' . $this->rest_namespace;
		$this->menu_parent_slug    = $args['menu_parent_slug'];

		foreach ( $this->settings as $key => $value ) {
			$this->options_defaults[ $key ] = $value['default'];
		}

		$this->options = (array) get_option( $this->slugged_namespace, [] );
		$this->options = $this->options + $this->options_defaults;

		add_action( 'admin_enqueue_scripts', [ $this, 'assets' ] );
		add_action( 'rest_api_init', [ $this, 'register_rest_route' ] );
		add_action( 'admin_menu', [ $this, 'register_setting_page' ] );
	}

	public function set_defined_product_keys() {

		$products = get_products();
		foreach ( $products as $p => $value ) {
			$defined_key = get_defined_key( $p );
			if ( $defined_key ) {
				$this->options[ $p ] = $defined_key;
			}
		}
	}

	public function get_options() {
		$options = (array) get_option( $this->slugged_namespace, [] );
		$options = $options + $this->options_defaults;
		return $options;
	}

	public function get_options_defaults() {
		return $this->options_defaults;
	}

	public function save_options( $options ) {

		if ( 'nextgenthemes' === $this->slugged_namespace ) {

			$action            = json_decode( $options['action'] );
			$options['action'] = '';

			if ( $action ) {
				$product_id  = get_products()[ $action->product ]['id'];
				$product_key = $options[ $action->product ];

				$options[ $action->product . '_status' ] = api_action( $product_id, $product_key, $action->action );
			}
		} elseif ( 'nextgenthemes_arve' === $this->slugged_namespace ) {
			update_option( 'nextgenthemes_arve_oembed_recache', time() );
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
			[
				'methods'              => 'POST',
				'args'                 => $this->settings,
				'permission_callback' => function() {
					return current_user_can( 'manage_options' );
				},
				'callback'             => function( \WP_REST_Request $request ) {
					$this->save_options( $request->get_params() );
					die( '1' );
				},
			]
		);
	}

	public function assets( $page ) {

		// Check if we are currently viewing our setting page
		if ( ! ends_with( $page, $this->slugged_namespace ) ) {
			return;
		}

		enqueue_asset(
		 	[
				'handle' => 'nextgenthemes-settings',
		 		'src'    => plugin_or_theme_src( 'build/common/settings.css' ),
		 		'ver'    => plugin_or_theme_ver( \Nextgenthemes\ARVE\VERSION, 'build/common/settings.css' ),
		 	]
		);

		enqueue_asset(
			[
				'handle' => 'nextgenthemes-settings',
				'path'   => dirname( dirname( __DIR__ ) ) . '/build/common/settings.js',
				'src'    => plugin_or_theme_src( 'build/common/settings.js' ),
				'deps'   => [ 'jquery' ],
			]
		);

		// Sending data to our plugin settings JS file
		wp_localize_script(
			'nextgenthemes-settings',
			$this->slugged_namespace,
			[
				'nonce'    => wp_create_nonce( 'wp_rest' ),
				'rest_url' => $this->rest_url,
				'home_url' => get_home_url(),
				'options'  => $this->options,
			]
		);
	}

	public function print_settings_blocks() {

		// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
		foreach ( $this->settings as $key => $option ) {

			$field_type = isset( $option['ui'] ) ? $option['ui'] : $option['type'];

			if ( 'hidden' !== $field_type ) :
				?>
				<div <?php echo Admin\block_attr( $key, $option ); ?>>
					<?php

					$function = __NAMESPACE__ . "\\Admin\\print_{$field_type}_field";

					$function( $key, $option );

					if ( ! empty( $option['description'] ) ) {
						printf( '<p>%s</p>', $option['description'] );
					}
					?>
					<hr>
				</div>
				<?php
			endif;
		}
		// phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	public function print_admin_page() {
		?>
		<div class='wrap wrap--nextgenthemes'>
			<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
			<div class="ngt-settings-grid">
				<div class="ngt-settings-grid__content" id='nextgenthemes-vue'>
					<?php

					do_action( $this->slashed_namespace . '/admin/settings_header', $this );

					$this->print_settings_blocks();

					?>
					<p>
						<button
							@click='saveOptions'
							:disabled='isSaving'
							id='vpsp-submit-settings'
							class='button button-primary'>Save</button>
						<img
							v-if='isSaving == true'
							id='loading-indicator'
							class="wrap--nextgenthemes__loading-indicator"
							src='<?php echo esc_url( get_admin_url() . '/images/wpspin_light-2x.gif' ); ?>'
							alt='Loading indicator' />
					</p>
					<p v-if='message'>{{ message }}</p>
				</div>
				<div class="ngt-settings-grid__sidebar">
					<?php do_action( $this->slashed_namespace . '/admin/settings_sidebar', $this ); ?>
				</div>
			</div>
		</div>
		<?php
	}

	public function register_setting_page() {

		// The HTML Document title for our settings page.
		$page_title = $this->settings_page_title;
		// The menu item title for our settings page.
		$menu_title = $this->menu_title;
		// The user permission required to view our settings page.
		$capability = 'manage_options';
		// The URL slug for our settings page.
		$menu_slug = $this->slugged_namespace;
		// The callback function for rendering our settings page HTML.
		$callback    = [ $this, 'print_admin_page' ];
		$parent_slug = $this->menu_parent_slug;

		add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $callback );
	}
}
