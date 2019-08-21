<?php
namespace Nextgenthemes\Admin\Settings;

use function Nextgenthemes\Asset\enqueue;
use function Nextgenthemes\Asset\plugin_or_theme_uri;
use function Nextgenthemes\Utils\ends_with;

class Setup {

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
			'content_function'         => false,
			'sidebar_content_function' => false,
			'menu_parent_slug'         => 'options-general.php',
		];

		$args = wp_parse_args( $args, $defaults );

		$this->settings                 = $args['settings'];
		$this->menu_title               = $args['menu_title'];
		$this->settings_page_title      = $args['settings_page_title'];
		$this->slugged_namespace        = sanitize_key( str_replace( '\\', '_', $args['namespace'] ) );
		$this->rest_namespace           = $this->slugged_namespace . '/v1';
		$this->rest_url                 = get_home_url() . '/wp-json/' . $this->rest_namespace;
		$this->menu_parent_slug         = $args['menu_parent_slug'];
		$this->content_function         = $args['content_function'];
		$this->sidebar_content_function = $args['sidebar_content_function'];

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

		enqueue( [
			'handle'    => 'nextgenthemes-vue',
			'src'       => plugin_or_theme_uri( 'nextgenthemes/dist/js/vue.min.js' ),
			'cdn_src'   => 'https://cdn.jsdelivr.net/npm/vue@2.5.17/dist/vue.min.js',
			'ver'       => '2.5.17',
			'integrity' => 'sha256-FtWfRI+thWlNz2sB3SJbwKx5PgMyKIVgwHCTwa3biXc='
		] );

		enqueue( [
			'handle' => 'nextgenthemes-settings',
			'src'    => plugin_or_theme_uri( 'nextgenthemes/dist/js/settings.js' ),
			'deps'   => [ 'nextgenthemes-vue', 'jquery' ]
		] );

		// Sending data to our plugin settings JS file
		wp_localize_script( 'nextgenthemes-settings', $this->slugged_namespace, [
			'nonce'    => wp_create_nonce( 'wp_rest' ),
			'rest_url' => $this->rest_url,
			'options'  => $this->options,
		] );

		enqueue( [
			'handle' => 'nextgenthemes-settings',
			'src'    => plugin_or_theme_uri( 'nextgenthemes/dist/css/settings.css' ),
		] );
	}

	public function print_settings_blocks() {

		// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
		foreach ( $this->settings as $key => $option ) {
			?>
			<div <?php echo block_attr( $key, $option ); ?>>
				<?php
				$function = __NAMESPACE__ . "\\print_{$option['type']}_field";
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
			<div class="ngt-grid">
				<div class="ngt-grid__content" id='nextgenthemes-vue'>
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
				<div class="ngt-grid__sidebar">
					<?php
					if ( $this->sidebar_content_function ) {
						$function = $this->sidebar_content_function;
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

		// Adding a new top level menu item.
		// add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $callback );

		/**
		 * Possible locations for adding our menu item
		 */
		$parent_slug = $this->menu_parent_slug;
		#$parent_slug = 'options-general.php';
		#$parent_slug = 'tools.php';
		#$parent_slug = 'edit.php';
		#$parent_slug = 'edit.php?post_type=page';

		add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $callback );
	}
}
