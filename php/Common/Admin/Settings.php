<?php
namespace Nextgenthemes\ARVE\Common\Admin;

use \Nextgenthemes\ARVE\Common;

class Settings {

	private $menu_title          = '';
	private $option_key          = '';
	private $project_namspace    = '';
	private $rest_namespace      = '';
	private $rest_url            = '';
	private $settings            = [];
	private $settings_page_title = '';
	public $options              = [];
	public $options_defaults     = [];

	public function __construct( $args ) {

		$defaults = [
			'content_function' => false,
			'sidebar_function' => false,
			'menu_parent_slug' => 'options-general.php',
		];

		$args = wp_parse_args( $args, $defaults );

		$this->settings            = $args['settings'];
		$this->menu_title          = $args['menu_title'];
		$this->settings_page_title = $args['settings_page_title'];
		$this->slugged_namespace   = sanitize_key( str_replace( '\\', '_', $args['namespace'] ) );
		$this->rest_namespace      = $this->slugged_namespace . '/v1';
		$this->rest_url            = get_home_url() . '/wp-json/' . $this->rest_namespace;
		$this->menu_parent_slug    = $args['menu_parent_slug'];
		$this->content_function    = $args['content_function'];
		$this->sidebar_function    = $args['sidebar_function'];

		foreach ( $this->settings as $key => $value ) {
			$this->options_defaults[ $key ] = $value['default'];
		}

		$this->options = (array) get_option( $this->slugged_namespace, [] );
		$this->options = $this->options + $this->options_defaults;

		add_action( 'admin_enqueue_scripts', [ $this, 'assets' ] );
		add_action( 'rest_api_init', [ $this, 'register_rest_route' ] );
		add_action( 'admin_menu', [ $this, 'register_setting_page' ] );
	}

	public function save_options( $options ) {

		$action = json_decode( $options['action'] );
		$options['action'] = '';

		if ( $action ) {
			$product_id = get_products()[ $action->product ]['id'];
			$key_status = api_action( $product_id, $options[ $action->product ], $action->action );
			logfile( $action->product, __FILE__ );
			logfile( $product_id, __FILE__ );
			logfile( $key_status, __FILE__ );
			Common\update_key_status( $action->product, $key_status );
		}

		$options = array_diff_assoc( $options, $this->options_defaults );
		// remove all items from options that are not also in defaults.
		$options = array_intersect_key( $options, $this->options_defaults );
		// store only the options that differ from the defaults.

		update_option( $this->slugged_namespace, $options );
	}

	public function register_rest_route() {

		register_rest_route(
			$this->rest_namespace,
			'/save',
			[
				'methods'              => 'POST',
				'args'                 => $this->settings,
				'permissions_callback' => function() {
					return current_user_can( 'manage_options' );
				},
				'callback'             => function( \WP_REST_Request $request ) {

					api_action( 1253, 'e6cab7097dbfe39174c2310a86b2854d', 'deactivate' );

					$this->save_options( $request->get_params() );
					die( '1' );
				},
			]
		);
	}

	public function assets( $page ) {

		// Check if we are currently viewing our setting page
		if ( ! Common\ends_with( $page, $this->slugged_namespace ) ) {
			return;
		}

		Common\enqueue(
			[
				'handle' => 'nextgenthemes-settings',
				'src'    => Common\plugin_or_theme_src( 'dist/common/css/settings.css' ),
				'ver'    => Common\plugin_or_theme_ver( \Nextgenthemes\ARVE\VERSION, 'dist/common/css/settings.css' ),
			]
		);

		Common\enqueue(
			[
				'handle' => 'nextgenthemes-settings',
				'src'    => Common\plugin_or_theme_src( 'dist/common/js/settings.js' ),
				'ver'    => Common\plugin_or_theme_ver( \Nextgenthemes\ARVE\VERSION, 'dist/common/js/settings.js' ),
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
				'options'  => $this->options,
			]
		);
	}

	public function print_settings_blocks() {

		// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
		foreach ( $this->settings as $key => $option ) {
			?>
			<div <?php echo block_attr( $key, $option ); ?>>
				<?php
				$field_type = isset( $option['ui'] ) ? $option['ui'] : $option['type'];

				$function = __NAMESPACE__ . "\\print_{$field_type}_field";

				$function( $key, $option );

				if ( ! empty( $option['description'] ) ) {
					printf( '<p>%s</p>', $option['description'] );
				}
				?>
				<hr>
			</div>
			<?php
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

					if ( $this->content_function ) {
						$function = $this->content_function;
						$function();
					}

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
					<?php
					if ( $this->sidebar_function ) {
						$function = $this->sidebar_function;
						$function();
					}
					?>
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
		$callback = [ $this, 'print_admin_page' ];

		$parent_slug = $this->menu_parent_slug;

		add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $callback );
	}
}
