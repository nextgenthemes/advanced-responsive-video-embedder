<?php
namespace nextgenthemes\admin;

function activation_notices() {

	$products = get_products();

	foreach ( $products as $key => $value ) {

		if( $value['active'] && ! $value['valid_key'] ) {

			$msg = sprintf(
				__( 'Hi there, thanks for your purchase. One last step, please activate your %s <a href="%s">here now</a>.', TEXTDOMAIN ),
				$value['name'],
				get_admin_url() . 'admin.php?page=nextgenthemes-licenses'
			);
			new \Nextgenthemes_Admin_Notice_Factory( $key . '-activation-notice', "<p>$msg</p>", HOUR_IN_SECONDS );
		}
	}
}

function php_below_56_notice() {

	$msg = sprintf(
		__( 'You use a PHP version below 5.6 ', TEXTDOMAIN ),
		PHP_VERSION
	);

	// new \Nextgenthemes_Admin_Notice_Factory( 'nextgenthemes-php-below-56-w', "<p>$msg</p>", false );
}
