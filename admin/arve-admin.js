(function ( $ ) {
	'use strict';
	/*global ajaxurl */
	/*global sui */
  /*global tb_show */

	// Options Page
	$('.arve-settings-section').each( function() {

		$(this).insertBefore( $(this).parent() );
	});

	$('.arve-settings-section').each( function() {

		var id	   = $(this).attr( 'id' );
		var classs = $(this).attr( 'class' );
		var title  = $(this).attr( 'title' );

		$(this).nextUntil( '.arve-settings-section' ).wrapAll( '<section id="' + id + '" class="' + classs + '" />' );

		$( '<a href="#" data-target="#' + id + '" class="nav-tab">' + title + '</a>' ).appendTo( '.arve-settings-tabs' );

		$(this).remove();
	});

	function show_tab( target ) {

		$('.arve-settings-section').show();
		$( target ).prependTo( '.arve-options-form' );
		$('.arve-settings-section').not( target ).hide();
		$('.arve-settings-tabs a').removeClass( 'nav-tab-active' );
		$('.arve-settings-tabs a[data-target="' + target + '"]').addClass( 'nav-tab-active' );
	}

	if ( $( '#arve_options_main\\[last_settings_tab\\]' ).length && $( '#arve_options_main\\[last_settings_tab\\]' ).val().length ) {
		show_tab( $( '#arve_options_main\\[last_settings_tab\\]' ).val() );
	}

	$(document).on( 'click', '.arve-settings-tabs a', function(e) {

		e.preventDefault();
		var target = $(this).attr('data-target');
		show_tab( target );
		$( '#arve_options_main\\[last_settings_tab\\]' ).val( target );
	} );

	$(document).on( 'click', '[data-image-upload]', function(e) {

		e.preventDefault();
		var target = $( this ).attr('data-image-upload');
		var image = wp.media({
			title: 'Upload Image',
			// mutiple: true if you want to upload multiple files at once
			multiple: false
		}).open()
		.on('select', function(){
			// This will return the selected image from the Media Uploader, the result is an object
			var uploaded_image = image.state().get('selection').first();
			// We convert uploaded_image to a JSON object to make accessing it easier
			// Output to the console uploaded_image
			var attachment_id = uploaded_image.toJSON().id;
			// Let's assign the url value to the input field
			$( target ).val(attachment_id);
		});
	});

	$(document).on( 'click', '.arve-pro-notice .notice-dismiss', function() {

		jQuery.ajax({
			url: ajaxurl,
			data: {
				action: 'arve_ajax_dismiss_pro_notice'
			}
		});
	});

	$(document).on( 'click', '[data-nj-notice-id] .notice-dismiss', function() {

    var id = $(this).closest('[data-nj-notice-id]').attr('data-nj-notice-id');

		jQuery.ajax({
			url: ajaxurl,
			data: {
				action: id
			}
		});
	});

	$(document).on( 'click', '#arve-btn', function() {

		if ( typeof( sui ) !== 'undefined' ) {

			var arve_shortcode = sui.utils.shortcodeViewConstructor.parseShortcodeString( '[arve]' );

			wp.media({
				frame : 'post',
				state : 'shortcode-ui',
				currentShortcode : arve_shortcode
			}).open();

		} else {

			tb_show( 'ARVE Optional Features', '#TB_inline?inlineId=arve-thickbox' );
		}

	} );

}(jQuery));
