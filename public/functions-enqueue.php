<?php

function arve_enqueue() {

  wp_enqueue_style( ARVE_SLUG, plugin_dir_url( __FILE__ ) . 'arve-public.css', array(), ARVE_VERSION, 'all' );

  $options = arve_get_options();

  if ( (int) $options["video_maxwidth"] > 0 ) {
    wp_add_inline_style( 'custom-style', sprintf( '.arve-wrapper{max-width:%dpx;}', $options['video_maxwidth'] ) );
  }

  wp_register_script(
    ARVE_SLUG,
    plugin_dir_url( __FILE__ ) . 'arve-public.js',
    array( 'jquery' ),
    ARVE_VERSION,
    true
  );
}
