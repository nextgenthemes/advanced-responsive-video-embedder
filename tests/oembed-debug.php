<?php

declare(strict_types = 1);

use function Nextgenthemes\ARVE\shortcode;
use function Nextgenthemes\ARVE\get_host_properties;

// phpcs:disable WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize
// phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_var_export
// phpcs:disable WordPress.WP.AlternativeFunctions.file_system_read_fwrite
// phpcs:disable WordPress.PHP.DiscouragedPHPFunctions.system_calls_system
// phpcs:disable WordPress.WP.AlternativeFunctions.file_system_operations_fwrite
// phpcs:disable WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents

class Tests_OembedD extends WP_UnitTestCase {

	/**
	 * @var array<string, mixed>
	 */
	private array $oembed_debug = [];

	public function test_oembed_debug(): void {

		$properties = PROVIDERS;
		foreach ( $properties as $provider => $v ) :

			fwrite( STDOUT, $provider . PHP_EOL );

			if ( empty( $v['tests'] ) ) {
				#$this->oembed_debug[ $test['url'] ] = null;
				continue;
			}

			foreach ( $v['tests'] as $test ) {
				$this->get_oembed_for_url( $test['url'] );

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
		$export = str_replace( '  ', "\t", $export );

		$file = __DIR__ . '/' . basename( __FILE__ ) . '-log.php';

		file_put_contents(
			$file,
			'<?php' . PHP_EOL . $export . ';' . PHP_EOL
		);

		system( "phpcbf $file" );
	}

	/**
	 * Retrieves oEmbed data for a given URL.
	 *
	 * @param  string  $url  The URL to get oEmbed data for.
	 * @return string        The shortcode output.
	 */
	public function get_oembed_for_url( string $url ): string {

		remove_filter( 'oembed_dataparse', '\Nextgenthemes\ARVE\filter_oembed_dataparse', PHP_INT_MAX );
		add_filter( 'oembed_dataparse', [ $this, 'filter_oembed_dataparse' ], PHP_INT_MAX, 3 );

		$this->oembed_debug[ $url ] = false;

		return $GLOBALS['wp_embed']->shortcode( [], $url );
	}

	/**
	 * Filters oEmbed data parse results for debugging.
	 *
	 * @param  string   $result  The oEmbed HTML result.
	 * @param  object   $data    The oEmbed data object.
	 * @param  string   $url     The URL being parsed.
	 * @return string            The oEmbed HTML result.
	 */
	public function filter_oembed_dataparse( string $result, object $data, string $url ): string {

		if ( ! empty( $data->description ) ) {
			$data->description = substr( $data->description, 0, 55 );
		}

		$this->oembed_debug[ $url ] = $data;

		return $result;
	}
}
