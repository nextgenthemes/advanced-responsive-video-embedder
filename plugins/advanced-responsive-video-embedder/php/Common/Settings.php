<?php
namespace Nextgenthemes\ARVE\Common;

class Settings {
	private static $no_reset_sections = array( 'debug', 'random-video', 'keys' );

	public $sections             = array();
	private $menu_title          = '';
	private $option_key          = '';
	private $options             = array();
	private $options_by_tag      = array();
	private $options_defaults    = array();
	private $premium_sections    = array();
	private $rest_namespace      = '';
	private $settings            = array();
	private $settings_page_title = '';
	private $slashed_namspace    = '';
	private $slugged_namspace    = '';

	public function __construct( $args ) {

		$defaults = array(
			'menu_parent_slug'    => 'options-general.php',
			'sections'            => array( 'main' => 'Main' ),
			'premium_sections'    => array(),
			'settings_page_title' => 'Default Page Title',
			'default_menu_title'  => 'Default Menu Title',
		);

		$args = wp_parse_args( $args, $defaults );

		$this->settings            = $args['settings'];
		$this->sections            = $args['sections'];
		$this->premium_sections    = $args['premium_sections'];
		$this->menu_title          = $args['menu_title'];
		$this->settings_page_title = $args['settings_page_title'];
		$this->slugged_namespace   = sanitize_key( str_replace( '\\', '_', $args['namespace'] ) );
		$this->slashed_namespace   = str_replace( '_', '/', $this->slugged_namespace );
		$this->rest_namespace      = $this->slugged_namespace . '/v1';
		$this->menu_parent_slug    = $args['menu_parent_slug'];

		foreach ( $this->settings as $key => $value ) {
			$this->options_defaults[ $key ] = $value['default'];
		}

		$this->options = (array) get_option( $this->slugged_namespace, array() );
		$this->options = $this->options + $this->options_defaults;

		add_action( 'admin_enqueue_scripts', array( $this, 'assets' ) );
		add_action( 'rest_api_init', array( $this, 'register_rest_route' ) );
		add_action( 'admin_menu', array( $this, 'register_setting_page' ) );
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
			$options['action'] = '';

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

		// Check if we are currently viewing our setting page
		if ( ! str_ends_with( $page, $this->slugged_namespace ) ) {
			return;
		}

		enqueue_asset(
			array(
				'handle' => 'nextgenthemes-settings',
				'src'    => plugin_or_theme_src( 'build/settings.css' ),
				'path'   => dirname( dirname( __DIR__ ) ) . '/build/settings.css',
			)
		);

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
				'handle'            => 'nextgenthemes-settings',
				'src'               => plugin_or_theme_src( 'build/settings.js' ),
				'path'              => dirname( dirname( __DIR__ ) ) . '/build/settings.js',
				'deps'              => array( 'jquery' ),
				'inline_script'     => "var {$this->slugged_namespace} = " . \wp_json_encode( $settings_data ) . ';',
				'inline_script_pos' => 'before',
			)
		);
	}

	public function print_settings_blocks() {

		$description_allowed_html = array(
			'a'      => array(
				'href'   => array(),
				'target' => array(),
				'title'  => array(),
			),
			'br'     => array(),
			'em'     => array(),
			'strong' => array(),
			'code'   => array(),
		);

		// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
		foreach ( $this->settings as $key => $option ) {

			$option['premium']  = in_array( $option['tag'], $this->premium_sections, true );
			$option['tag_name'] = $this->sections[ $option['tag'] ];
			$field_type         = isset( $option['ui'] ) ? $option['ui'] : $option['type'];
			$block_class        = "ngt-option-block ngt-option-block--$key ngt-option-block--{$option['tag']}";

			if ( 'hidden' !== $field_type ) :
				?>
				<div 
					class="<?php echo esc_attr( $block_class ); ?>"
					v-show="sectionsDisplayed['<?php echo esc_attr( $option['tag'] ); ?>']"
				>
				<!-- <div <?php #echo Admin\block_attr( $key, $option ); ?>> -->
					<?php

					$function = __NAMESPACE__ . "\\Admin\\print_{$field_type}_field";

					$function( $key, $option );

					if ( ! empty( $option['description'] ) ) {
						printf(
							'<p>%s</p>',
							wp_kses( $option['description'], $description_allowed_html )
						);
					}
					?>
					<hr>
				</div>
				<?php
			endif;
		}
	}

	private function print_settings_tabs() {
		?>
		<h2 class="nav-tab-wrapper">
			<a @click='showAllSectionsButDebug()' class="nav-tab">All Options</button>
			<?php
			foreach ( $this->sections as $slug => $name ) :

				$classes = in_array( $slug, $this->premium_sections, true ) ? 'nav-tab nav-tab--ngt-highlight' : 'nav-tab';
				?>
				<a 
					@click="showSection('<?php echo esc_attr( $slug ); ?>')"
					class="<?php echo esc_attr( $classes ); ?>"
					v-bind:class='{ "nav-tab-active": sectionsDisplayed["<?php echo esc_attr( $slug ); ?>"] }'
				>
					<?php echo esc_html( $name ); ?>
				</a>
			<?php endforeach; ?>
		</h2>
		<?php
	}

	public function print_save_section() {
		?>
		<p v-show="onlySectionDisplayed !== 'debug'">
			<button
				@click='saveOptions'
				:disabled='isSaving'
				class='button button-primary'
			>
				Save
			</button>
			<strong v-if='message'>{{ message }}</strong>
			<img
				v-if='isSaving == true'
				class="wrap--nextgenthemes__loading-indicator"
				src='<?php echo esc_url( get_admin_url() . '/images/wpspin_light-2x.gif' ); ?>'
				alt='Loading indicator'
			/>
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
		<div class="ngt-block" v-show="<?php echo $v_show; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" >
			<p><?php esc_html_e( 'You may already set options for addons but they will only take effect if the associated addons are installed.', 'advanced-responsive-video-embedder' ); ?></p>
		</div>
		<?php
	}

	private function print_debug_info_block() {
		?>
		<div class="ngt-block" v-show="sectionsDisplayed.debug">
			<?php require_once __DIR__ . '/Admin/partials/debug-info.php'; ?>
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
				<button
					@click="resetOptions('<?php echo esc_attr( $key ); ?>')"
					:disabled='isSaving'
					class='button button--ngt-reset button-secondary'
					v-show="sectionsDisplayed['<?php echo esc_attr( $key ); ?>']"
				>
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
		<div class="ngt-block" v-if="errors.length">
			<strong>Please correct the following error(s):</strong>
			<ul>
				<li v-for="error in errors">{{ error }}</li>
			</ul>
		</div>
		<?php
	}

	public function print_outdated_php_msg() {

		$link_only = array(
			'a' => array(
				'href'   => array(),
				'target' => array(),
				'title'  => array(),
			),
		);

		if ( \version_compare( PHP_VERSION, '5.6.40', '<=' ) ) {
			?>
			<div class="ngt-sidebar-box">
				<p>
					<?php
					printf(
						// translators: PHP version, URL, Contact URL
						wp_kses( __( 'Your PHP version %1$s is very <a href="%2$s">outdated, insecure and slow</a>. No pressure, this plugin will continue to work with PHP 5.6, but at some undecided point I like to use features from PHP 7. If you can not update for some reason please tell <a href="%3$s">tell me</a>. WordPress itself planned to require PHP 7 in a feature release but decided not to persue this for now because so many people still run on outdated versions. WordPress already has beta support for 8.0 but I would not go with 8.0 just yet.', 'advanced-responsive-video-embedder' ), $link_only ),
						esc_html( PHP_VERSION ),
						esc_url( 'https://www.php.net/supported-versions' ),
						esc_url( 'https://nextgenthemes.com/contact/' )
					);
					?>
				</p>
			</div>
			<?php
		} elseif ( \version_compare( PHP_VERSION, '7.3.26', '<' ) ) {
			?>
			<div class="ngt-sidebar-box">
				<p>
					<?php
					printf(
						// translators: URL
						wp_kses( __( 'Just a heads up, your PHP version %1$s is outdated and potentially insecure. See what versions are <a href="%2$s">good here</a>. WordPress already has beta support for 8.0 but I would not go with 8.0 just yet.', 'advanced-responsive-video-embedder' ), $link_only ),
						esc_html( PHP_VERSION ),
						esc_url( 'https://www.php.net/supported-versions' )
					);
					?>
				</p>
			</div>
			<?php
		}
	}

	public function print_admin_page() {
		?>
		<div class='wrap wrap--nextgenthemes' id='nextgenthemes-vue'>
			<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
			<?php $this->print_settings_tabs(); ?>
			<div class="ngt-settings-grid">
				<div class="ngt-settings-grid__content" >
					<?php
					do_action( $this->slashed_namespace . '/admin/settings/content', $this );
					$this->print_paid_section_message();
					$this->print_save_section();
					$this->print_debug_info_block();
					$this->print_settings_blocks();
					$this->print_save_section();
					$this->print_reset_bottons();
					?>
				</div>
				<div class="ngt-settings-grid__sidebar">
					<?php
					do_action( $this->slashed_namespace . '/admin/settings/sidebar', $this );
					$this->print_outdated_php_msg();
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
		$callback    = array( $this, 'print_admin_page' );
		$parent_slug = $this->menu_parent_slug;

		add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $callback );
	}
}
