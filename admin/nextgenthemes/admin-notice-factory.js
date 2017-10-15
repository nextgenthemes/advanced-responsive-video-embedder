/*global ajaxurl */
jQuery( function( $ ) {

  var id = $(this).closest('[data-nj-notice-id]').attr('data-nj-notice-id');

  jQuery.ajax({
    url: ajaxurl,
    data: {
      action: id
    }
  });

} );
