<?php

function arve_register_styles() {

  arve_register_asset( array(
    'automin' => true,
    'handle'  => ARVE_SLUG,
    'src'     => plugin_dir_url( __FILE__ ) . 'arve.css'
  ) );
}

function arve_register_scripts() {

  arve_register_asset( array(
    'automin' => true,
    'handle'  => ARVE_SLUG,
    'src'     => plugin_dir_url( __FILE__ ) . 'arve.js',
    'deps'    => array( 'jquery' )
  ) );
}
