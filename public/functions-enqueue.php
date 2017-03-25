<?php

function arve_register_styles() {

  $min = arve_get_min_suffix();

  arve_register_asset( array(
    'handle'  => ARVE_SLUG,
    'src'     => ARVE_PUBLIC_URL .  "arve$min.css",
  ) );
}

function arve_register_scripts() {

  $min = arve_get_min_suffix();

  arve_register_asset( array(
    'handle'  => ARVE_SLUG,
    'src'     => ARVE_PUBLIC_URL . "arve$min.js",
    'deps'    => array( 'jquery' ),
  ) );
}
