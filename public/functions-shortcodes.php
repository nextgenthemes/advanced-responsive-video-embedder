<?php

function arv3_shortcode_arve( $atts ) {

  $options = arv3_get_options();
  $atts    = (array) $atts;

  if ( array_key_exists( 0, $atts ) ) {
    return arv3_error( __( 'Your shortcode is wrong, possibly a missing quotation mark.', ARVE_SLUG ) );
  }

  $errors = '';

  $pairs = apply_filters( 'arve_shortcode_pairs', array(
    'align'        => (string) $options['align'],
    'arve_link'    => (string) $options['promote_link'],
    'aspect_ratio' => null,
    'autoplay'     => $options['autoplay'],
    'description'  => null,
    'iframe_name'  => null,
    'maxwidth'     => $options['video_maxwidth'],
    'mode'         => (string) $options['mode'],
    'parameters'   => null,
    'thumbnail'    => null,
    'title'        => null,
    'upload_date'  => null,
    'url'          => null,
    'src'          => null, # Just a alias for url to make it simple
    # self hosted
    'm4v'      => null,
    'mp4'      => null,
    'ogv'      => null,
    'webm'     => null,
    'preload'  => 'metadata',
    'controls' => 'y',
    'loop'     => 'n',
    # TED only
    'lang'     => null,
    # Vimeo only
    'start'    => null,
    # Old Shortcodes / URL embeds
    'id'       => null,
    'provider' => null,
    # deprecated
    'link_text' => null,
  ) );

  #d($pairs);

  $v = shortcode_atts( $pairs, $atts, 'arve' );

  if ( ! empty( $v['src'] ) ) {
    $v['url'] = $v['src'];
  }

  if ( $self_hosted_detected = arv3_detect_self_hosted( $v['provider'], $v['url'] ) ) {
    $v = $self_hosted_detected;
  }

  if ( empty( $v['provider'] ) ) {

    $v['provider'] = 'iframe';

    if ( ! empty( $v['id'] ) && empty( $v['url'] ) ) {
      $v['iframe_src'] = $v['id'];
    } else {
      $v['iframe_src'] = $v['url'];
    }
  }

  $v['align']        = arv3_validate_align( $v['align'], $v['provider'] );
  $v['maxwidth']     = (int) $v['maxwidth'];
  $v['maxwidth']     = (int) arv3_maxwidth_when_aligned( $v['maxwidth'], $v['align'] );
  $v['mode']         = arv3_validate_mode( $v['mode'], $v['provider'] );
  $v['autoplay']     = arv3_validate_bool( $v['autoplay'],  'autoplay' );
  $v['arve_link']    = arv3_validate_bool( $v['arve_link'], 'arve_link' );
  $v['loop']         = arv3_validate_bool( $v['loop'],      'loop' );
  $v['controls']     = arv3_validate_bool( $v['controls'],  'controls' );
  $v['id']           = arv3_id_fixes( $v['id'], $v['provider'] );
  $v['aspect_ratio'] = arv3_get_default_aspect_ratio( $v['aspect_ratio'], $v['provider'], $v['mode'] );
  $v['aspect_ratio'] = arv3_aspect_ratio_fixes( $v['aspect_ratio'], $v['provider'], $v['mode'] );
  $v['iframe_src']   = arv3_build_iframe_src( $v['provider'], $v['id'], $v['lang'] );
  $v['iframe_src']   = arv3_add_query_args_to_iframe_src( $v['parameters'], $v['iframe_src'], $v['provider'] );
  $v['iframe_src']   = arv3_autoplay_query_arg( $v['autoplay'], $v['iframe_src'], $v['provider'], $v['mode'] );

  if ( 'vimeo' == $v['provider'] && ! empty( $v['start'] ) ) {
    $v['iframe_src'] .= '#t=' . (int) $v['start'];
    $v['iframe_src'] .= '#t=' . (int) $v['start'];
  }

  $v['embed_id'] = arv3_create_embed_id( $v );

  if ( $errors = arv3_output_errors( $atts, $v ) ) {
    return $errors;
  }

  $debug_info    = arv3_get_debug_info( $atts, $v );
  $arve_video    = arv3_video_or_iframe( $v );
  $meta_html     = arv3_build_meta_html( $v );
  $arve_link     = arv3_build_promote_link_html( $v['arve_link'] );
  $arve_play_btn = function_exists( 'arve_pro_play_btn' ) ? arve_pro_play_btn( $v ) : '';
  $webtorrent_status = function_exists( 'arve_webtorrent_status' ) ? arve_webtorrent_status( $v['webtorrent'] ) : '';

  if ( 'link-lightbox' == $v['mode'] ) {
    $containers  = arve_pro_lity_container( $meta_html . $arve_video, $v );
  } elseif ( 'lazyload-lightbox' == $v['mode'] ) {
    $containers  = arve_pro_lity_container( $arve_video, $v );
    $containers .= arv3_arve_embed_container( $meta_html . $arve_play_btn, $v );
  } else {
    $containers = arv3_arve_embed_container( $meta_html . $arve_video . $arve_play_btn, $v );
  }

  $final_embed = arv3_arve_wrapper( $containers . $arve_link . $webtorrent_status, $v );

  $output = apply_filters( 'arve_output', $debug_info . $final_embed, $v );

  if ( empty( $output ) ) {
    return arv3_error( 'The output is empty, this should not happen' );
  } elseif ( is_wp_error( $output ) ) {
    return arv3_error( $output->get_error_message() );
  }

  wp_enqueue_script( 'advanced-responsive-video-embedder' );
  return $output;
}

/**
 * Create all shortcodes at a late stage because people over and over again using this plugin toghter with jetback or
 * other plugins that handle shortcodes we will now overwrite all this suckers.
 *
 * @since    2.6.2
 *
 * @uses Advanced_Responsive_Video_Embedder_Create_Shortcodes()
 */
function arv3_create_shortcodes() {

  $options = arv3_get_options();
  $function_factory = new ARVE_Shortcode_Function_Factory;

  foreach( $options['shortcodes'] as $provider => $shortcode ) {
    /* Would require php 5.3.0
    $function = function( $atts ) use ( $provider ) {

      $atts['provider'] = $provider;
      return arv3_shortcode_arve( $atts );
    };
    add_shortcode( $shortcode, $function );
    */
    add_shortcode( $shortcode, array( $function_factory, $provider ) );
  }

  add_shortcode( 'arve',                'arv3_shortcode_arve' );
  add_shortcode( 'arve-supported',      'arv3_shortcode_arve_supported' );
  add_shortcode( 'arve-supported-list', 'arv3_shortcode_arve_supported_list' );
  add_shortcode( 'arve-params',         'arv3_shortcode_arve_params' );
}

function arv3_shortcode_arve_supported() {

  $providers = arv3_get_host_properties();
  // unset deprecated and doubled
  unset( $providers['dailymotionlist'] );
  unset( $providers['iframe'] );

  $out  = '<h3 id="video-host-support">Video Host Support</h3>';
  $out .= '<p>The limiting factor of the following features is not ARVE but what the prividers offer.</p>';
  $out .= '<table class="table table-sm table-hover">';
  $out .= '<tr>';
  $out .= '<th></th>';
  $out .= '<th>Provider</th>';
  $out .= '<th>Requires<br>embed code</th>';
  $out .= '<th>SSL</th>';
  $out .= '<th>Requires Flash</th>';
  $out .= '<th>Auto Thumbnail<br>(Pro Addon)</th>';
  $out .= '<th>Auto Title<br>(Pro Addon)</th>';
  $out .= '</tr>';
  $out .= '<tr>';
  $out .= '<td></td>';
  $out .= '<td colspan="6"><a href="https://nextgenthemes.com/plugins/advanced-responsive-video-embedder-pro/documentation/#general-iframe-embedding">All providers with responsive iframe embed codes</a></td>';
  $out .= '</tr>';

  $count = 1;

  foreach ( $providers as $key => $values ) {

    if ( ! isset( $values['name'] ) )
      $values['name'] = $key;

    $out .= '<tr>';
    $out .= sprintf( '<td>%d</td>', $count++ );
    $out .= sprintf( '<td>%s</td>', esc_html( $values['name'] ) );
    $out .= sprintf( '<td>%s</td>', ( isset( $values['no_url_embeds'] ) && $values['no_url_embeds'] ) ? '' : '&#x2713;' );
    $out .= sprintf( '<td>%s</td>', ( isset( $values['embed_url'] ) && arv3_starts_with( $values['embed_url'], 'https' ) ) ? '&#x2713;' : '' );
    $out .= sprintf( '<td>%s</td>', ! empty( $values['requires_flash'] ) ? '&#x2713;' : '' );
    $out .= sprintf( '<td>%s</td>', ( isset( $values['auto_thumbnail'] ) && $values['auto_thumbnail'] ) ? '&#x2713;' : '' );
    $out .= sprintf( '<td>%s</td>', ( isset( $values['auto_title'] )     && $values['auto_title'] )     ? '&#x2713;' : '' );
    $out .= '</tr>';
  }

  $out .= '<tr>';
  $out .= '<td></td>';
  $out .= '<td colspan="6"><a href="https://nextgenthemes.com/plugins/advanced-responsive-video-embedder-pro/documentation/#general-iframe-embedding">All providers with responsive iframe embed codes</a></td>';
  $out .= '</tr>';
  $out .= '</table>';

  return $out;
}

function arv3_shortcode_arve_supported_list() {

  $providers = arv3_get_host_properties();
  // unset deprecated and doubled
  unset( $providers['dailymotionlist'] );
  unset( $providers['iframe'] );

  $lis = '';

  foreach ( $providers as $key => $values ) {
    $lis .= sprintf( '<li>%s</li>', esc_html( $values['name'] ) );
  }

  return '<ol>'. $lis . '<li><a href="https://nextgenthemes.com/plugins/advanced-responsive-video-embedder-pro/documentation/#general-iframe-embedding">All providers with responsive iframe embed codes</a></li></ol>';
}

function arv3_shortcode_arve_params() {

  $attrs = arv3_get_settings_definitions();

  if( function_exists( 'arve_pro_get_settings_definitions' ) ) {
    $attrs = array_merge( $attrs, arve_pro_get_settings_definitions() );
  }

  $out  = '<table class="table table-hover table-arve-params">';
  $out .= '<tr>';
  $out .= '<th>Parameter</th>';
  $out .= '<th>Function</th>';
  $out .= '</tr>';

  foreach ( $attrs as $key => $values ) {

    if( isset( $values['hide_from_sc'] ) && $values['hide_from_sc'] ) {
      continue;
    }

    $desc = '';
    unset( $values['options'][''] );
    unset( $choices );

    if ( ! empty( $values['options'] ) ) {
      foreach ($values['options'] as $key => $value) {
        $choices[] = sprintf( '<code>%s</code>', $key );
      }
      $desc .= __('Options: ', ARVE_SLUG ) . implode( ', ', $choices ) . '<br>';
    }

    if ( ! empty( $values['description'] ) )
      $desc .= $values['description'];

    if ( ! empty( $values['meta']['placeholder'] ) )
      $desc .= $values['meta']['placeholder'];

    $out .= '<tr>';
    $out .= sprintf( '<td>%s</td>', $values['attr'] );
    $out .= sprintf( '<td>%s</td>', $desc );
    $out .= '</tr>';
  }

  $out .= '</table>';

  return $out;
}

function arv3_wp_video_shortcode_override( $out, $attr, $content, $instance ) {

  $options = arv3_get_options();

  if( empty( $options['wp_video_override'] ) && ! empty( $attr['wmv'] ) && ! empty( $attr['flv'] ) ) {
    return $out;
  }

  $attr[ 'provider' ] = 'self_hosted';

  if( empty( $attr['poster'] ) ) {
    $attr['thumbnail'] = $attr['poster'];
  }

  return arv3_shortcode_arve( $attr );
}
