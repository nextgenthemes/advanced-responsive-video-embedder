<?php

class ARVE_URL_Function_Factory {

  function __call( $function_name, $params ) {

    $provider = $function_name;

    return arv3_url_detection_to_shortcode( $provider, $params[0], $params[1], $params[2], $params[3] );
  }
}
