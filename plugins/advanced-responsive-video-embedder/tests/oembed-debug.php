<?php

use function \Nextgenthemes\ARVE\shortcode;
use function \Nextgenthemes\ARVE\get_host_properties;

// phpcs:disable Squiz.Classes.ClassFileName.NoMatch
// phpcs:disable Squiz.Classes.ValidClassName.NotCamelCaps
// phpcs:disable WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize
// phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_var_export
// phpcs:disable WordPress.WP.AlternativeFunctions.file_system_read_fwrite
// phpcs:disable WordPress.PHP.DiscouragedPHPFunctions.system_calls_system

class Tests_OembedD extends WP_UnitTestCase {

	private $oembed_debug = [];

	public function test_oembed_debug() {

		$properties = get_host_properties();
		foreach ( $properties as $provider => $v ) :

			fwrite( STDOUT, $provider . PHP_EOL );

			if ( empty( $v['tests'] ) ) {
				$this->oembed_debug[ $test['url'] ] = null;
				continue;
			}

			foreach ( $v['tests'] as $test ) {
				$this->get_oembed_for_url($test['url']);

				if ( ! empty( $v['oembed'] ) &&
					empty( $this->oembed_debug[ $test['url'] ] )
				) {
					fwrite( STDOUT, $test['url'] . ' marked as having oembed data but not having it' . PHP_EOL );
				}

				if ( empty( $v['oembed'] ) &&
					! empty( $this->oembed_debug[ $test['url'] ] )
				) {
					fwrite( STDOUT, $test['url'] . ' marked as not having oembed data but having it' . PHP_EOL );
				}
			}
		endforeach;

		$export = var_export( $this->oembed_debug, true );
		$export = str_replace('  ', "\t", $export);

		$file = __DIR__ . '/' . basename( __FILE__ ) . '-log.php';

		// phpcs:ignore
		file_put_contents(
			$file,
			'<?php' . PHP_EOL . $export . ';' . PHP_EOL
		);

		system( "phpcbf $file" );
	}

	public function get_oembed_for_url( $url ) {

		remove_filter( 'oembed_dataparse', 'Nextgenthemes\ARVE\filter_oembed_dataparse', 11, 3 );
		add_filter( 'oembed_dataparse', [ $this, 'filter_oembed_dataparse' ], PHP_INT_MAX, 3 );

		$this->oembed_debug[ $url ] = false;

		return $GLOBALS['wp_embed']->shortcode( [], $url );
	}

	public function filter_oembed_dataparse( $result, $data, $url ) {

		if ( ! empty( $data->description ) ) {
			$data->description = substr($data->description, 0, 55);
		}

		$this->oembed_debug[ $url ] = $data;

		return $result;
	}
}
