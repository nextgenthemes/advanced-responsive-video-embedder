<?php
namespace Nextgenthemes\Admin\Settings;

use Nextgenthemes\Asset;
use Nextgenthemes\Utils;

class Setup {

	private $menu_title          = '';
	private $option_key          = '';
	private $options_defaults    = [];
	private $project_namspace    = '';
	private $rest_namespace      = '';
	private $rest_url            = '';
	private $settings            = [];
	private $settings_page_title = '';
	public $options              = [];

	public function __construct( $args ) {

		$defaults = [];

		$args = wp_parse_args( $args, $defaults );

		$this->settings            = $args['settings'];
		$this->menu_title          = $args['menu_title'];
		$this->settings_page_title = $args['settings_page_title'];
		$this->slugged_namespace   = sanitize_key( str_replace( '\\', '_', $args['namespace'] ) );
		$this->rest_namespace      = $this->slugged_namespace . '/v1';
		$this->rest_url            = get_site_url() . '/wp-json/' . $this->rest_namespace;

		foreach ( $this->settings as $key => $value ) {
			$this->options_defaults[ $key ] = $value['default'];
		}

		$this->options = (array) get_option( $this->slugged_namespace );
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
		if ( ! Utils\ends_with( $page, $this->slugged_namespace ) ) {
			return;
		}

		// Vue.js
		Asset\enqueue( [
			'handle'    => 'nextgenthemes-vue',
			'src'       => Asset\plugin_or_theme_uri( 'vendor/nextgenthemes/common/dist/js/vue.min.js' ),
			'cdn_src'   => 'https://cdn.jsdelivr.net/npm/vue@2.5.17/dist/vue.min.js',
			'ver'       => '2.5.17',
			'integrity' => 'sha256-FtWfRI+thWlNz2sB3SJbwKx5PgMyKIVgwHCTwa3biXc='
		] );

		Asset\enqueue( [
			'handle' => 'nextgenthemes-settings',
			'src'    => Asset\plugin_or_theme_uri( 'vendor/nextgenthemes/common/dist/js/settings.js' ),
			'deps'   => [ 'nextgenthemes-vue', 'jquery' ]
		] );
		// Sending data to our plugin settings JS file
		wp_localize_script( 'nextgenthemes-settings', $this->slugged_namespace, [
			'nonce'    => wp_create_nonce( 'wp_rest' ),
			'rest_url' => $this->rest_url,
			'options'  => $this->options,
		] );

		Asset\enqueue( [
			'handle' => 'nextgenthemes-settings',
			'src'    => Asset\plugin_or_theme_uri( 'vendor/nextgenthemes/common/dist/js/settings.css' ),
		] );
	}

	public function print_admin_page() {
		?>

		<div class='wrap wrap--nextgenthemes'>
			<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
			<div id='nextgenthemes-vue'>
				<?php

				foreach ( $this->settings as $key => $option ) {
					$function = __NAMESPACE__ . "\\print_{$option['type']}_field";
					$function( $key, $option );

					if ( ! empty( $option['description'] ) ) {
						printf( '<p>%s</p>', esc_html( $option['description'] ) );
					}

					echo '<hr>';
				}

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
						class="wp-tweak-loading-indicator"
						src='<?php echo esc_url( get_admin_url() ); ?>/images/wpspin_light-2x.gif'
						alt='Loading indicator' />
				</p>
				<p v-if='message'>{{ message }}</p>
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
		 * Other possible locations for adding our menu item
		 */
		#$parent_slug = 'options-general.php';
		$parent_slug = 'tools.php';
		#$parent_slug = 'edit.php';
		#$parent_slug = 'edit.php?post_type=page';

		add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $callback );
	}
}
