<?php
namespace Nextgenthemes\ARVE\Common\Admin;

use \Nextgenthemes\ARVE\Common;

// phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralDomain
function activation_notices() {

	$products = get_products();

	foreach ( $products as $key => $value ) {

		if ( $value['active'] && ! $value['valid_key'] ) {

			$msg = sprintf(
				// Translators: First %1$s is product name.
				__( 'Hi there, thanks for your purchase. One last step, please activate your %1$s <a href="%2$s">here now</a>.', 'advanced-responsive-video-embedder' ),
				$value['name'],
				get_admin_url() . 'admin.php?page=nextgenthemes-licenses'
			);
			new NoticeFactory( $key . '-activation-notice', "<p>$msg</p>", HOUR_IN_SECONDS );
		}
	}
}

function php_outdated() {

	$msg = sprintf(
		// Translators: %s = PHP Version
		__( 'ARVE/Nextgenthemes requre at least PHP version 5.6! Your PHP version is %s and has reached End Of Life (insecure and slow). You should ask your host to update it for you not only to make ARVE work but to make your site faster and more secure. Preferably 7.x', 'advanced-responsive-video-embedder' ),
		PHP_VERSION
	);

	new NoticeFactory( 'arve-php-outdated', "<p>$msg</p>", false );
}
