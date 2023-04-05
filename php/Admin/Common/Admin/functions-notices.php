<?php
namespace Nextgenthemes\ARVE\Common\Admin;

use \Nextgenthemes\ARVE\Common;
use \Nextgenthemes\ARVE\Common\Admin\Notices;

// phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralDomain
function activation_notices() {

	$products = Common\get_products();

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
