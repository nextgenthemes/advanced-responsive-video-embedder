<?php

function arve_register_styles() {

  arve_register_asset( array(
    'automin' => true,
    'handle'  => ARVE_SLUG,
    'src'     => plugin_dir_url( __FILE__ ) . 'arve.css'
  ) );
}

function arve_print_maxwidth_style() {

  $options = arve_get_options();

  if ( (int) $options["video_maxwidth"] > 0 ) {
    wp_add_inline_style( ARVE_SLUG, sprintf( '.arve-wrapper{max-width:%dpx;}', $options['video_maxwidth'] ) );
  }
}

function arve_register_scripts() {

  arve_register_asset( array(
    'automin' => true,
    'handle'  => ARVE_SLUG,
    'src'     => plugin_dir_url( __FILE__ ) . 'arve.js',
    'deps'    => array( 'jquery' )
  ) );
}
