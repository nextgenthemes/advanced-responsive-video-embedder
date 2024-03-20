<?php declare(strict_types=1);
namespace Nextgenthemes\ARVE;

function create_url_handlers(): void {

	$properties = get_host_properties();

	foreach ( $properties as $provider => $values ) {

		$function = function ( $matches, $attr, $url, $rawattr ) use ( $provider ) {
			return url_handler( $provider, $matches, $attr, $url, $rawattr );
		};

		if ( ! empty( $values['regex'] ) && empty( $values['oembed'] ) ) {
			wp_embed_register_handler( 'arve_' . $provider, $values['regex'], $function );
		}
	}
}

/**
 * Callback for wp_embed_register_handler
 *
 * @param string $provider The lower case provider name
 * @param array  $matches  The RegEx matches from the provided regex when calling
 *                          wp_embed_register_handler().
 * @param array  $attr     Embed attributes.
 * @param string $url      The original URL that was matched by the regex.
 * @param array  $rawattr  The original unmodified attributes.
 *
 * @return string  The embed HTML.
 */
function url_handler( string $provider, array $matches, array $attr, string $url, array $rawattr ): string {

	if ( is_array( $rawattr ) ) {
		$a = $rawattr;
	}

	if ( is_wp_error( $url ) ) {
		$a['errors'] = $url;
	}

	$a['provider']    = $provider;
	$a['url']         = $url;
	$a['origin_data'] = [
		'from'    => 'url_handler',
		'matches' => $matches,
		'attr'    => $attr,
		'rawattr' => $rawattr,
	];

	return build_video( $a );
}
