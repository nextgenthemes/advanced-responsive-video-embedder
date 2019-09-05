<?php
namespace Nextgenthemes\ARVE\Common\Admin\EDD;

/**
 * Theme updater admin page and functions.
 *
 * @package EDD Sample Theme
 */

class ThemeUpdaterAdmin {

	/**
	 * Variables required for the theme updater
	 *
	 * @since 1.0.0
	 * @type string
	 */
	protected $remote_api_url = null;
	protected $theme_slug     = null;
	protected $version        = null;
	protected $author         = null;
	protected $download_id    = null;
	protected $renew_url      = null;
	protected $strings        = null;

	/**
	 * Initialize the class.
	 *
	 * @since 1.0.0
	 */
	function __construct( $config = array(), $strings = array() ) {

		$config = wp_parse_args(
			$config,
			array(
				'remote_api_url' => 'http://easydigitaldownloads.com',
				'theme_slug'     => get_template(),
				'item_name'      => '',
				'license'        => '',
				'version'        => '',
				'author'         => '',
				'download_id'    => '',
				'renew_url'      => '',
			)
		);

		// Set config arguments
		$this->remote_api_url = $config['remote_api_url'];
		$this->item_name      = $config['item_name'];
		$this->theme_slug     = sanitize_key( $config['theme_slug'] );
		$this->version        = $config['version'];
		$this->author         = $config['author'];
		$this->download_id    = $config['download_id'];
		$this->renew_url      = $config['renew_url'];

		// Populate version fallback
		if ( '' == $config['version'] ) {
			$theme         = wp_get_theme( $this->theme_slug );
			$this->version = $theme->get( 'Version' );
		}

		// Strings passed in from the updater config
		$this->strings = $strings;

		add_action( 'admin_init', array( $this, 'updater' ) );
		add_action( 'admin_init', array( $this, 'register_option' ) );
		add_action( 'admin_init', array( $this, 'license_action' ) );
		add_action( 'admin_menu', array( $this, 'license_menu' ) );
		add_action( 'update_option_' . $this->theme_slug . '_license_key', array( $this, 'activate_license' ), 10, 2 );
		add_filter( 'http_request_args', array( $this, 'disable_wporg_request' ), 5, 2 );

	}

	/**
	 * Creates the updater class.
	 *
	 * since 1.0.0
	 */
	function updater() {

		/* If there is no valid license key status, don't allow updates. */
		if ( get_option( $this->theme_slug . '_license_key_status', false ) != 'valid' ) {
			return;
		}

		new Nextgenthemes\Symbiosis\Edd\ThemeUpdater(
			array(
				'remote_api_url' => $this->remote_api_url,
				'version'        => $this->version,
				'license'        => trim( get_option( $this->theme_slug . '_license_key' ) ),
				'item_name'      => $this->item_name,
				'author'         => $this->author,
			),
			$this->strings
		);
	}

	/**
	 * Adds a menu item for the theme license under the appearance menu.
	 *
	 * since 1.0.0
	 */
	function license_menu() {

		$strings = $this->strings;

		add_submenu_page(
			'symbiosis_admin_page',
			$strings['theme-license'],
			$strings['theme-license'],
			'manage_options',
			$this->theme_slug . '-license',
			array( $this, 'license_page' )
		);
	}

	/**
	 * Outputs the markup used on the theme license page.
	 *
	 * since 1.0.0
	 */
	function license_page() {

		$strings = $this->strings;

		$license = trim( get_option( $this->theme_slug . '_license_key' ) );
		$status  = get_option( $this->theme_slug . '_license_key_status', false );

		// Checks license status to display under license key
		if ( ! $license ) {
			$message = $strings['enter-key'];
		} else {
			// delete_transient( $this->theme_slug . '_license_message' );
			if ( ! get_transient( $this->theme_slug . '_license_message', false ) ) {
				set_transient( $this->theme_slug . '_license_message', $this->check_license(), ( 60 * 60 * 24 ) );
			}
			$message = get_transient( $this->theme_slug . '_license_message' );
		}
		?>
		<div class="wrap">
			<h2><?php echo $strings['theme-license']; ?></h2>
			<form method="post" action="options.php">

				<?php settings_fields( $this->theme_slug . '-license' ); ?>

				<table class="form-table">
					<tbody>

						<tr valign="top">
							<th scope="row" valign="top">
								<?php echo $strings['license-key']; ?>
							</th>
							<td>
								<input id="<?php echo $this->theme_slug; ?>_license_key" name="<?php echo $this->theme_slug; ?>_license_key" type="text" class="regular-text" value="<?php echo esc_attr( $license ); ?>" />
								<p class="description">
									<?php echo $message; ?>
								</p>
							</td>
						</tr>

						<?php if ( $license ) { ?>
						<tr valign="top">
							<th scope="row" valign="top">
								<?php echo $strings['license-action']; ?>
							</th>
							<td>
								<?php
								wp_nonce_field( $this->theme_slug . '_nonce', $this->theme_slug . '_nonce' );
								if ( 'valid' == $status ) {
									?>
									<input type="submit" class="button-secondary" name="<?php echo $this->theme_slug; ?>_license_deactivate" value="<?php esc_attr_e( $strings['deactivate-license'], 'symbiosis' ); ?>"/>
								<?php } else { ?>
									<input type="submit" class="button-secondary" name="<?php echo $this->theme_slug; ?>_license_activate" value="<?php esc_attr_e( $strings['activate-license'], 'symbiosis' ); ?>"/>
									<?php
								}
								?>
							</td>
						</tr>
						<?php } ?>

					</tbody>
				</table>
				<?php submit_button(); ?>
			</form>
		<?php
	}

	/**
	 * Registers the option used to store the license key in the options table.
	 *
	 * since 1.0.0
	 */
	function register_option() {
		register_setting(
			$this->theme_slug . '-license',
			$this->theme_slug . '_license_key',
			array( $this, 'sanitize_license' )
		);
	}

	/**
	 * Sanitizes the license key.
	 *
	 * since 1.0.0
	 *
	 * @param string $new License key that was submitted.
	 * @return string $new Sanitized license key.
	 */
	function sanitize_license( $new ) {

		$old = get_option( $this->theme_slug . '_license_key' );

		if ( $old && $old != $new ) {
			// New license has been entered, so must reactivate
			delete_option( $this->theme_slug . '_license_key_status' );
			delete_transient( $this->theme_slug . '_license_message' );
		}

		return $new;
	}

	/**
	 * Makes a call to the API.
	 *
	 * @since 1.0.0
	 *
	 * @param array $api_params to be used for wp_remote_get.
	 * @return array $response decoded JSON response.
	 */
	function get_api_response( $api_params ) {

		// Call the custom API.
		$response = wp_remote_post(
			$this->remote_api_url,
			array(
				'timeout'   => 15,
				'sslverify' => false,
				'body'      => $api_params,
			)
		);

		// Make sure the response came back okay.
		if ( is_wp_error( $response ) ) {
			return false;
		}

		$response = json_decode( wp_remote_retrieve_body( $response ) );

		return $response;
	}

	/**
	 * Activates the license key.
	 *
	 * @since 1.0.0
	 */
	function activate_license() {

		$license = trim( get_option( $this->theme_slug . '_license_key' ) );

		// Data to send in our API request.
		$api_params = array(
			'edd_action' => 'activate_license',
			'license'    => $license,
			'item_name'  => urlencode( $this->item_name ),
		);

		$license_data = $this->get_api_response( $api_params );

		// $response->license will be either "active" or "inactive"
		if ( $license_data && isset( $license_data->license ) ) {
			update_option( $this->theme_slug . '_license_key_status', $license_data->license );
			delete_transient( $this->theme_slug . '_license_message' );
		}

	}

	/**
	 * Deactivates the license key.
	 *
	 * @since 1.0.0
	 */
	function deactivate_license() {

		// Retrieve the license from the database.
		$license = trim( get_option( $this->theme_slug . '_license_key' ) );

		// Data to send in our API request.
		$api_params = array(
			'edd_action' => 'deactivate_license',
			'license'    => $license,
			'item_name'  => urlencode( $this->item_name ),
		);

		$license_data = $this->get_api_response( $api_params );

		// $license_data->license will be either "deactivated" or "failed"
		if ( $license_data && ( $license_data->license == 'deactivated' ) ) {
			delete_option( $this->theme_slug . '_license_key_status' );
			delete_transient( $this->theme_slug . '_license_message' );
		}
	}

	/**
	 * Constructs a renewal link
	 *
	 * @since 1.0.0
	 */
	function get_renewal_link() {

		// If a renewal link was passed in the config, use that
		if ( '' != $this->renew_url ) {
			return $this->renew_url;
		}

		// If download_id was passed in the config, a renewal link can be constructed
		$license_key = trim( get_option( $this->theme_slug . '_license_key', false ) );
		if ( '' != $this->download_id && $license_key ) {
			$url  = esc_url( $this->remote_api_url );
			$url .= '/checkout/?edd_license_key=' . $license_key . '&download_id=' . $this->download_id;
			return $url;
		}

		// Otherwise return the remote_api_url
		return $this->remote_api_url;

	}



	/**
	 * Checks if a license action was submitted.
	 *
	 * @since 1.0.0
	 */
	function license_action() {

		if ( isset( $_POST[ $this->theme_slug . '_license_activate' ] ) ) {
			if ( check_admin_referer( $this->theme_slug . '_nonce', $this->theme_slug . '_nonce' ) ) {
				$this->activate_license();
			}
		}

		if ( isset( $_POST[ $this->theme_slug . '_license_deactivate' ] ) ) {
			if ( check_admin_referer( $this->theme_slug . '_nonce', $this->theme_slug . '_nonce' ) ) {
				$this->deactivate_license();
			}
		}

	}

	/**
	 * Checks if license is valid and gets expire date.
	 *
	 * @since 1.0.0
	 *
	 * @return string $message License status message.
	 */
	function check_license() {

		$license = trim( get_option( $this->theme_slug . '_license_key' ) );
		$strings = $this->strings;

		$api_params = array(
			'edd_action' => 'check_license',
			'license'    => $license,
			'item_name'  => urlencode( $this->item_name ),
			'url'        => home_url(),
		);

		$license_data = $this->get_api_response( $api_params );

		// If response doesn't include license data, return
		if ( ! isset( $license_data->license ) ) {
			$message = $strings['license-unknown'];
			return $message;
		}

		// We need to update the license status at the same time the message is updated
		if ( $license_data && isset( $license_data->license ) ) {
			update_option( $this->theme_slug . '_license_key_status', $license_data->license );
		}

		// Get expire date
		$expires = false;
		if ( isset( $license_data->expires ) ) {
			$expires    = date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires ) );
			$renew_link = '<a href="' . esc_url( $this->get_renewal_link() ) . '" target="_blank">' . $strings['renew'] . '</a>';
		}

		// Get site counts
		$site_count    = $license_data->site_count;
		$license_limit = $license_data->license_limit;

		// If unlimited
		if ( 0 == $license_limit ) {
			$license_limit = $strings['unlimited'];
		}

		if ( $license_data->license == 'valid' ) {
			$message = $strings['license-key-is-active'] . ' ';
			if ( $expires ) {
				$message .= sprintf( $strings['expires%s'], $expires ) . ' ';
			}
			if ( $site_count && $license_limit ) {
				$message .= sprintf( $strings['%1$s/%2$-sites'], $site_count, $license_limit );
			}
		} elseif ( $license_data->license == 'expired' ) {
			if ( $expires ) {
				$message = sprintf( $strings['license-key-expired-%s'], $expires );
			} else {
				$message = $strings['license-key-expired'];
			}
			if ( $renew_link ) {
				$message .= ' ' . $renew_link;
			}
		} elseif ( $license_data->license == 'invalid' ) {
			$message = $strings['license-keys-do-not-match'];
		} elseif ( $license_data->license == 'inactive' ) {
			$message = $strings['license-is-inactive'];
		} elseif ( $license_data->license == 'disabled' ) {
			$message = $strings['license-key-is-disabled'];
		} elseif ( $license_data->license == 'site_inactive' ) {
			// Site is inactive
			$message = $strings['site-is-inactive'];
		} else {
			$message = $strings['license-status-unknown'];
		}

		return $message;
	}

	/**
	 * Disable requests to wp.org repository for this theme.
	 *
	 * @since 1.0.0
	 */
	function disable_wporg_request( $r, $url ) {

		// If it's not a theme update request, bail.
		if ( 0 !== strpos( $url, 'https://api.wordpress.org/themes/update-check/1.1/' ) ) {
			 return $r;
		}

		 // Decode the JSON response
		 $themes = json_decode( $r['body']['themes'] );

		 // Remove the active parent and child themes from the check
		 $parent = get_option( 'template' );
		 $child  = get_option( 'stylesheet' );
		 unset( $themes->themes->$parent );
		 unset( $themes->themes->$child );

		 // Encode the updated JSON response
		 $r['body']['themes'] = json_encode( $themes );

		 return $r;
	}

}
