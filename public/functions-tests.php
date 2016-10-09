<?php

# Align
function arv3_shortcode_atts_align_left( $atts ) {

  $atts['align'] = 'left';
  return $atts;
}

function arv3_shortcode_atts_align_right( $atts ) {

  $atts['align'] = 'right';
  return $atts;
}

function arv3_shortcode_atts_align_center( $atts ) {

  $atts['align'] = 'center';
  return $atts;
}

function arv3_shortcode_atts_maxwidth_300( $atts ) {

  $atts['maxwidth'] = '300';
  return $atts;
}

function arv3_shortcode_atts_title( $atts ) {

  $atts['title'] = 'Custom Title';
  return $atts;
}

function arv3_shortcode_atts_description( $atts ) {

  $atts['description'] = 'Custom Description';
  return $atts;
}

function arv3_shortcode_atts_upload_date( $atts ) {

  $atts['upload_date'] = '2016-02-01';
  return $atts;
}

function arv3_shortcode_atts_autoplay_on( $atts ) {

  $atts['autoplay'] = 'y';
  return $atts;
}

function arv3_shortcode_atts_autoplay_off( $atts ) {

  $atts['autoplay'] = 'n';
  return $atts;
}

function arv3_shortcode_atts_arve_link_on( $atts ) {

  $atts['arve_link'] = 'on';
  return $atts;
}

function arv3_shortcode_atts_arve_link_off( $atts ) {

  $atts['arve_link'] = 'off';
  return $atts;
}

function arv3_shortcode_atts_mode_normal( $atts ) {

  $atts['mode'] = 'normal';
  return $atts;
}
