<?php

function arv3_enqueue_styles() {
  wp_enqueue_style( ARVE_SLUG, plugin_dir_url( __FILE__ ) . 'arve-public.css', array(), ARVE_VERSION, 'all' );
}

function arv3_register_scripts() {

  wp_register_script(
    'advanced-responsive-video-embedder',
    plugin_dir_url( __FILE__ ) . 'arve-public.js',
    array( 'jquery' ),
    ARVE_VERSION,
    true
  );
}
