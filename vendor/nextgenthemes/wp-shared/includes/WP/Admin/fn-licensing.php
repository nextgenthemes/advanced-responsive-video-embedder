<?php

declare(strict_types = 1);

namespace Nextgenthemes\WP\Admin;

use Nextgenthemes\WP\Admin\EDD\PluginUpdater;
use Nextgenthemes\WP\Admin\EDD\ThemeUpdater;
use const Nextgenthemes\ARVE\VERSION;
use function Nextgenthemes\WP\get_products;
use function Nextgenthemes\WP\str_contains_any;

function init_edd_updaters( array $options ): void {

	$products = get_products();

	foreach ( $products as $product ) {

		if ( 'plugin' === $product['type'] && ! empty( $product['file'] ) ) {
			init_plugin_updater( $product, $options );
		} elseif ( 'theme' === $product['type'] ) {
			init_theme_updater( $product, $options );
		}
	}
}

function init_plugin_updater( array $product, array $options ): void {

	// setup the updater
	new PluginUpdater(
		'https://nextgenthemes.com',
		$product['file'],
		array(
			'license' => $options[ $product['slug'] ],
			'beta'    => str_contains_any( VERSION, [ 'alpha', 'beta' ] ),
			'version' => $product['version'],
			'item_id' => $product['id'],
			'author'  => $product['author'],
		)
	);
}

function init_theme_updater( array $product, array $options ): void {

	new ThemeUpdater(
		array(
			'license'        => $options[ $product['slug'] ],
			'remote_api_url' => 'https://nextgenthemes.com',
			'version'        => $product['version'],
			'item_id'        => $product['id'],
			'author'         => $product['author'],
			'theme_slug'     => $product['slug'],
			'download_id'    => $product['download_id'], // Optional, used for generating a license renewal link
			#'renew_url'     => $product['renew_link'], // Optional, allows for a custom license renewal link
		),
		array(
			'theme-license'             => __( 'Theme License', 'advanced-responsive-video-embedder' ),
			'enter-key'                 => __( 'Enter your theme license key.', 'advanced-responsive-video-embedder' ),
			'license-key'               => __( 'License Key', 'advanced-responsive-video-embedder' ),
			'license-action'            => __( 'License Action', 'advanced-responsive-video-embedder' ),
			'deactivate-license'        => __( 'Deactivate License', 'advanced-responsive-video-embedder' ),
			'activate-license'          => __( 'Activate License', 'advanced-responsive-video-embedder' ),
			'status-unknown'            => __( 'License status is unknown.', 'advanced-responsive-video-embedder' ),
			'renew'                     => __( 'Renew?', 'advanced-responsive-video-embedder' ),
			'unlimited'                 => __( 'unlimited', 'advanced-responsive-video-embedder' ),
			'license-key-is-active'     => __( 'License key is active.', 'advanced-responsive-video-embedder' ),
			// Translators: Date
			'expires%s'                 => __( 'Expires %s.', 'advanced-responsive-video-embedder' ),
			'expires-never'             => __( 'Lifetime License.', 'advanced-responsive-video-embedder' ),
			// Translators: x of x sites activated
			'%1$s/%2$-sites'            => __( 'You have %1$s / %2$s sites activated.', 'advanced-responsive-video-embedder' ),
			// Translators: Date
			'license-key-expired-%s'    => __( 'License key expired %s.', 'advanced-responsive-video-embedder' ),
			'license-key-expired'       => __( 'License key has expired.', 'advanced-responsive-video-embedder' ),
			'license-keys-do-not-match' => __( 'License keys do not match.', 'advanced-responsive-video-embedder' ),
			'license-is-inactive'       => __( 'License is inactive.', 'advanced-responsive-video-embedder' ),
			'license-key-is-disabled'   => __( 'License key is disabled.', 'advanced-responsive-video-embedder' ),
			'site-is-inactive'          => __( 'Site is inactive.', 'advanced-responsive-video-embedder' ),
			'license-status-unknown'    => __( 'License status is unknown.', 'advanced-responsive-video-embedder' ),
			'update-notice'             => __( "Updating this theme will lose any customizations you have made. 'Cancel' to stop, 'OK' to update.", 'advanced-responsive-video-embedder' ),
			// phpcs:ignore
			'update-available'          => __( '<strong>%1$s %2$s</strong> is available. <a href="%3$s" class="thickbox" title="%4s">Check out what\'s new</a> or <a href="%5$s"%6$s>update now</a>.', 'advanced-responsive-video-embedder' ),
		)
	);
}

// phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralDomain
function activation_notices(): void {

	$products = get_products();

	foreach ( $products as $key => $value ) :

		if ( $value['active'] && ! $value['valid_key'] ) {
			$msg = sprintf(
				// Translators: First %1$s is product name.
				__( 'Hi there, thanks for your purchase. One last step, please activate your %1$s <a href="%2$s">here now</a>.', 'advanced-responsive-video-embedder' ),
				$value['name'],
				get_admin_url() . 'options-general.php?page=nextgenthemes'
			);

			Notices::instance()->register_notice(
				"ngt-$key-activation-notice",
				'notice-info',
				'<p>' . $msg . '</p>',
				array(
					'cap' => 'change_options',
				)
			);
		}
	endforeach;
}
