<?php
namespace Nextgenthemes\ARVE;

function create_url_handlers() {

	$properties = get_host_properties();

	foreach ( $properties as $provider => $values ) {

		$function = function( $matches, $attr, $url, $rawattr ) use ( $provider ) {
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
 * @return string  The embed HTML.
 */
function url_handler( $provider, array $matches, array $attr, $url, $rawattr ) {

	if ( is_array( $rawattr ) ) {
		$a = $rawattr;
	}

	$errors = new \WP_Error();

	if ( is_wp_error( $url ) ) {
		$errors = $url;
	}

	$a['provider'] = $provider;
	$a['url']      = $url;
	$origin_data   = [
		'from'    => 'url_handler',
		'matches' => $matches,
		'attr'    => $attr,
		'rawattr' => $rawattr,
	];

	$video = new Video( $a, $origin_data, null, $errors );
	return $video->build_video();
}
