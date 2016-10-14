<?php

class ARVE_Shortcode_Function_Factory {

  function __call( $function_name, $params ) {

    $atts             = $params[0];
    $atts['provider'] = $function_name;

    return arve_shortcode_arve( $atts, null, false );
  }
}
