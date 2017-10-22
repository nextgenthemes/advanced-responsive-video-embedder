<?php



class Tests_Licensing_Pro extends WP_UnitTestCase {

	/**
	 * Test if all the file hooks are working.
	 *
	 * @since 2.3.6
	 */
	public function test_check_for_wp_proveders() {

		add_filters( 'oembed_providers', 'filter_wp_providers' );
	}

	public function filter_wp_providers( $providers ) {

		$props = arve_get_properties();

		foreach ( $props as $provider => $provider_properties ) :

			foreach ($providers as $regex => $url_and_data  ) {

				$error = false;

				if ( ( arve_contains( $regex, $provider ) || arve_contains( $url_and_data[0], $provider ) ) && empty( $provider_properties['use_oembed'] ) ) {
					$error = $provider;
				}

				$this->assertTrue( $error, $error );
			}

		endforeach;

		return $providers;
	}

}
