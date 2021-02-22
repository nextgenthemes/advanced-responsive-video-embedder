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

function url_handler( $provider, array $matches, array $attr, $url, $rawattr ) {

	if ( is_array( $rawattr ) ) {
		$a = $rawattr;
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
