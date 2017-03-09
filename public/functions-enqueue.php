<?php

function arve_enqueue_styles() {

  $options = arve_get_options();

  wp_enqueue_style( ARVE_SLUG, plugin_dir_url( __FILE__ ) . 'arve-public.css', array(), ARVE_VERSION, 'all' );

  if ( (int) $options["video_maxwidth"] > 0 ) {
    wp_add_inline_style( ARVE_SLUG, sprintf( '.arve-wrapper{max-width:%dpx;}', $options['video_maxwidth'] ) );
  }
}

function arve_register_scripts() {

  wp_register_script(
    ARVE_SLUG,
    plugin_dir_url( __FILE__ ) . 'arve-public.js',
    array( 'jquery' ),
    ARVE_VERSION,
    true
  );
}
