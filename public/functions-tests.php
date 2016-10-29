<?php

function arve_shortcode_tests( $content ) {

  if ( empty( $_GET['arve-test'] ) ) {
    return $content;
  }

  $host = $_GET['arve-test'];

  if ( empty ( $_GET['arve-testurl'] ) ) {
    $n = 0;
  } else {
    $n = (int) $_GET['arve-testurl'];
  }

  $properties = arve_get_host_properties();
  $out = '<h4>ARVE Shortcode Tests</h4>';

  $url_to_test = $properties[ $host ]['tests'][ $n ]['url'];
  $id_to_test  = $properties[ $host ]['tests'][ $n ]['id'];

  #$scs[] = "\n$url_or_shortcode\n";
  $scs[] = sprintf( '[arve url="%s" mode="normal"]', $url_to_test );
  $scs[] = sprintf( '[arve url="%s" mode="lazyload"]', $url_to_test );
  $scs[] = sprintf( '[arve url="%s" mode="lazyload" maxwidth="350" grow="y"]', $url_to_test );
  $scs[] = sprintf( '[arve url="%s" mode="lazyload" maxwidth="350" grow="n"]', $url_to_test );
  $scs[] = sprintf( '[arve url="%s" mode="lazyload" align="left"] Lorum Lorum Lorum Lorum Lorum Lorum Lorum Lorum Lorum<div style="clear: both;"></div>', $url_to_test );
  $scs[] = sprintf( '[arve url="%s" mode="lazyload-lightbox" maxwdith="350" title="ARVE" description="description test"]', $url_to_test );

  #$scs[] = sprintf( PHP_EOL . "\n\r%s\n\r" .  PHP_EOL, $url_to_test );

  $scs[] = sprintf( '[%s id="%s" mode="normal"]',   $host, $id_to_test );
  $scs[] = sprintf( '[%s id="%s" mode="lazyload"]', $host, $id_to_test );

  foreach ( $scs as $sc ) {

    $code = str_replace( array( '[', ']' ), array( '[[', ']]' ), $sc );
    $out .= sprintf( '<p><code>%s</code></p>%s', esc_html( $code ), $sc );
  }

  return $out;
}

function arve_regex_tests( $content ) {

  if ( ! isset( $_GET['arve-regex-tests'] ) ) {
    return $content;
  }

  $properties = arve_get_host_properties();
  $out = '<h4>ARVE Regex Tests</h4>';

  foreach( $properties as $provider => $host_props ) :

    if ( empty( $host_props['test_urls'] ) ) {
      continue;
    }

    foreach( $host_props['test_urls'] as $urltest ) {

      if ( ! is_array( $urltest ) ) {
        continue;
      }

      $url_to_test = $urltest[0];
      $expected_id = $urltest[1];

      preg_match( '#' . $host_props['regex'] . '#i', $url_to_test, $matches );

      if ( $matches[1] != $expected_id ) {
        $out .= arve_error( sprintf( 'Test <code>%s | %s</code> failed.<br>Match was: <code>%s</code>', $url_to_test, $expected_id, $matches[1] ) );
      }
    }

  endforeach;

  return $out;
}

# Align
function arve_shortcode_atts_align_left( $atts ) {

  $atts['align'] = 'left';
  return $atts;
}

function arve_shortcode_atts_align_right( $atts ) {

  $atts['align'] = 'right';
  return $atts;
}

function arve_shortcode_atts_align_center( $atts ) {

  $atts['align'] = 'center';
  return $atts;
}

function arve_shortcode_atts_maxwidth_300( $atts ) {

  $atts['maxwidth'] = '300';
  return $atts;
}

function arve_shortcode_atts_title( $atts ) {

  $atts['title'] = 'Custom Title';
  return $atts;
}

function arve_shortcode_atts_description( $atts ) {

  $atts['description'] = 'Custom Description';
  return $atts;
}

function arve_shortcode_atts_upload_date( $atts ) {

  $atts['upload_date'] = '2016-02-01';
  return $atts;
}

function arve_shortcode_atts_autoplay_on( $atts ) {

  $atts['autoplay'] = 'y';
  return $atts;
}

function arve_shortcode_atts_autoplay_off( $atts ) {

  $atts['autoplay'] = 'n';
  return $atts;
}

function arve_shortcode_atts_arve_link_on( $atts ) {

  $atts['arve_link'] = 'on';
  return $atts;
}

function arve_shortcode_atts_arve_link_off( $atts ) {

  $atts['arve_link'] = 'off';
  return $atts;
}

function arve_shortcode_atts_mode_normal( $atts ) {

  $atts['mode'] = 'normal';
  return $atts;
}
