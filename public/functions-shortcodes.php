<?php

function arve_shortcode_arve( $atts ) {

  if ( array_key_exists( 0, $atts ) ) {
    return arve_error( __( 'Your shortcode is wrong, possibly a missing quotation mark. Or a attribute without a value.', ARVE_SLUG ) );
  }

  $errors  = '';
  $options = arve_get_options();
  $atts    = (array) $atts;

  $pairs = apply_filters( 'arve_shortcode_pairs', array(
    'align'        => $options['align'],
    'arve_link'    => arve_bool_to_shortcode_string( $options['promote_link'] ),
    'aspect_ratio' => null,
    'autoplay'     => arve_bool_to_shortcode_string( $options['autoplay'] ),
    'description'  => null,
    'iframe_name'  => null,
    'maxwidth'     => (string) $options['video_maxwidth'],
    'mode'         => $options['mode'],
    'parameters'   => null,
    'thumbnail'    => null,
    'title'        => null,
    'upload_date'  => null,
    'url'          => null,
    'src'          => null, # Just a alias for url to make it simple
    # <video>
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
    # deprecated, title should be used
    'link_text' => null,
  ) );

  #d($pairs);

  $v = shortcode_atts( $pairs, $atts, 'arve' );

  if ( $errors = arve_output_errors( $atts, $v ) ) {
    return $errors;
  }

  if ( empty( $v['provider'] ) ) {

    $v['provider'] = 'iframe';

    if ( ! empty( $v['id'] ) && empty( $v['url'] ) ) {
      $v['iframe_src'] = $v['id'];
    } else {
      $v['iframe_src'] = $v['url'];
    }
  }

  $v['align']        = arve_validate_align( $v['align'], $v['provider'] );
  $v['mode']         = arve_validate_mode( $v['mode'], $v['provider'] );
  $v['autoplay']     = arve_validate_bool( $v['autoplay'],  'autoplay' );
  $v['arve_link']    = arve_validate_bool( $v['arve_link'], 'arve_link' );
  $v['loop']         = arve_validate_bool( $v['loop'],      'loop' );
  $v['controls']     = arve_validate_bool( $v['controls'],  'controls' );

  $v['maxwidth']     = (int) $v['maxwidth'];
  $v['maxwidth']     = (int) arve_maxwidth_when_aligned( $v['maxwidth'], $v['align'] );
  $v['id']           = arve_id_fixes( $v['id'], $v['provider'] );
  $v['aspect_ratio'] = arve_get_default_aspect_ratio( $v['aspect_ratio'], $v['provider'], $v['mode'] );
  $v['aspect_ratio'] = arve_aspect_ratio_fixes(       $v['aspect_ratio'], $v['provider'], $v['mode'] );

  $v['iframe_src']   = arve_build_iframe_src( $v['provider'], $v['id'], $v['lang'] );
  $v['iframe_src']   = arve_add_query_args_to_iframe_src( $v['parameters'], $v['iframe_src'], $v['provider'] );
  $v['iframe_src']   = arve_autoplay_query_arg( $v['autoplay'], $v['iframe_src'], $v['provider'], $v['mode'] );

  if ( 'vimeo' == $v['provider'] && ! empty( $v['start'] ) ) {
    $v['iframe_src'] .= '#t=' . (int) $v['start'];
    $v['iframe_src'] .= '#t=' . (int) $v['start'];
  }

  $v['embed_id'] = arve_create_embed_id( $v );

  if ( $errors = arve_output_errors( $atts, $v ) ) {
    return $errors;
  }

  $debug_info    = arve_get_debug_info( $atts, $v );
  $arve_video    = arve_video_or_iframe( $v );
  $meta_html     = arve_build_meta_html( $v );
  $arve_link     = arve_build_promote_link_html( $v['arve_link'] );
  $arve_play_btn = function_exists( 'arve_pro_play_btn' ) ? arve_pro_play_btn( $v ) : '';
  $webtorrent_status = function_exists( 'arve_webtorrent_status' ) ? arve_webtorrent_status( $v['webtorrent'] ) : '';

  if ( 'link-lightbox' == $v['mode'] ) {
    $containers  = arve_pro_lity_container( $meta_html . $arve_video, $v );
  } elseif ( 'lazyload-lightbox' == $v['mode'] ) {
    $containers  = arve_pro_lity_container( $arve_video, $v );
    $containers .= arve_arve_embed_container( $meta_html . $arve_play_btn, $v );
  } else {
    $containers = arve_arve_embed_container( $meta_html . $arve_video . $arve_play_btn, $v );
  }

  $final_embed = arve_arve_wrapper( $containers . $arve_link . $webtorrent_status, $v );

  $output = apply_filters( 'arve_output', $debug_info . $final_embed, $v );

  if ( empty( $output ) ) {
    return arve_error( 'The output is empty, this should not happen' );
  } elseif ( is_wp_error( $output ) ) {
    return arve_error( $output->get_error_message() );
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
function arve_create_shortcodes() {

  $options = arve_get_options();
  $function_factory = new ARVE_Shortcode_Function_Factory;

  foreach( $options['shortcodes'] as $provider => $shortcode ) {
    /* Would require php 5.3.0
    $function = function( $atts ) use ( $provider ) {
      $atts['provider'] = $provider;
      return arve_shortcode_arve( $atts );
    };
    add_shortcode( $shortcode, $function );
    */
    add_shortcode( $shortcode, array( $function_factory, $provider ) );
  }

  add_shortcode( 'arve',                'arve_shortcode_arve' );
  add_shortcode( 'arve-supported',      'arve_shortcode_arve_supported' );
  add_shortcode( 'arve-supported-list', 'arve_shortcode_arve_supported_list' );
  add_shortcode( 'arve-params',         'arve_shortcode_arve_params' );
}

function arve_shortcode_arve_supported() {

  $providers = arve_get_host_properties();
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
    $out .= sprintf( '<td>%s</td>', ( isset( $values['embed_url'] ) && arve_starts_with( $values['embed_url'], 'https' ) ) ? '&#x2713;' : '' );
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

function arve_shortcode_arve_supported_list() {

  $providers = arve_get_host_properties();
  // unset deprecated and doubled
  unset( $providers['dailymotionlist'] );
  unset( $providers['iframe'] );

  $lis = '';

  foreach ( $providers as $key => $values ) {
    $lis .= sprintf( '<li>%s</li>', esc_html( $values['name'] ) );
  }

  return '<ol>'. $lis . '<li><a href="https://nextgenthemes.com/plugins/advanced-responsive-video-embedder-pro/documentation/#general-iframe-embedding">All providers with responsive iframe embed codes</a></li></ol>';
}

function arve_shortcode_arve_params() {

  $attrs = arve_get_settings_definitions();

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

function arve_wp_video_shortcode_override( $out, $attr, $content, $instance ) {

  $options = arve_get_options();

  if( empty( $options['wp_video_override'] ) && ! empty( $attr['wmv'] ) && ! empty( $attr['flv'] ) ) {
    return $out;
  }

  $attr[ 'provider' ] = 'html5';

  if( empty( $attr['poster'] ) ) {
    $attr['thumbnail'] = $attr['poster'];
  }

  return arve_shortcode_arve( $attr );
}
