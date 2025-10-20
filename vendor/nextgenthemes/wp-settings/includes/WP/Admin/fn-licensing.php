<?php

declare(strict_types = 1);

namespace Nextgenthemes\WP\Admin;

use Nextgenthemes\WP\Admin\EDD\PluginUpdater;
use function Nextgenthemes\WP\get_products;
use function Nextgenthemes\WP\str_contains_any;
use const Nextgenthemes\ARVE\VERSION;

/** @param array <string, int|float|string|bool> $options */
function init_edd_updaters( array $options ): void {

	$products = get_products();

	foreach ( $products as $product ) {

		if ( 'plugin' === $product['type'] && ! empty( $product['file'] ) ) {
			init_plugin_updater( $product, $options );
		}
	}
}

/**
 * @param array <string, int|float|string|bool> $product
 * @param array <string, int|float|string|bool> $options
 */
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

// phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralDomain
function activation_notices(): void {

	$products = get_products();

	foreach ( $products as $key => $value ) :

		if ( $value['active'] && ! $value['valid_key'] ) {
			$msg = sprintf(
				// Translators: %1$s product name. %2$s url to settings page
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
