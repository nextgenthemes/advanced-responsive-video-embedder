<?php

declare(strict_types = 1);

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
 * @param string $provider                      The lower case provider name
 * @param array <int|string, string>  $matches  The RegEx matches from the provided regex when calling wp_embed_register_handler().
 * @param array <int|sting, mixed>    $attr     Embed attributes.
 * @param string                      $url      The original URL that was matched by the regex.
 * @param array <int|string, mixed>   $rawattr  The original unmodified attributes.
 *
 * @return string  The embed HTML.
 */
function url_handler( string $provider, array $matches, array $attr, string $url, array $rawattr ): string {

	$a['provider']            = $provider;
	$a['url']                 = $url;
	$a['origin_data']['from'] = 'url_handler';

	$a['origin_data'][ __FUNCTION__ ]['matches'] = $matches;
	$a['origin_data'][ __FUNCTION__ ]['attr']    = $attr;
	$a['origin_data'][ __FUNCTION__ ]['rawattr'] = $rawattr;

	return build_video( $a );
}
