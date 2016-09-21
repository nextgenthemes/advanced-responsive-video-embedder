(function ( $ ) {
	'use strict';
	/*global ajaxurl */
	/*global sui */
  /*global alert */
	// Options Page
	//
	$('.arve-settings-section').each( function() {

		$(this).insertBefore( $(this).parent() );
	});

	$('.arve-settings-section').each( function() {

		var id	 = $(this).attr( 'id' );
		var classs = $(this).attr( 'class' );
		var title  = $(this).attr( 'title' );

		$(this).nextUntil( '.arve-settings-section' ).wrapAll( '<section id="' + id + '" class="' + classs + '" />' );

		$( '<a href="#" data-target="#' + id + '" class="nav-tab">' + title + '</a>' ).appendTo( '.arve-settings-tabs' );

		$(this).remove();
	});

	// Its hidden!
	var last_tab_input = $( '#arve_options_main\\[last_options_tab\\]' );

	$('.arve-settings-tabs a').on( 'click', function(e) {

		var target = $(this).attr('data-target');

		$('.arve-settings-section').show();
		$( target ).prependTo( '.arve-options-form' );
		$('.arve-settings-section').not( target ).hide();

		$( last_tab_input ).val( target );

		$('.arve-settings-tabs a').removeClass( 'nav-tab-active' );
		$(this).addClass( 'nav-tab-active' );

		e.preventDefault();
	} );

	/*
	if( last_tab_input.val() ) {

		var last_tab = last_tab_input.val();

		if( $( last_tab ).length === 0 ) {
			last_tab = '#arve-settings-section-main';
		}

		$('.arve-settings-tabs a[data-target='+last_tab+']').addClass( 'nav-tab-active' );

		$( last_tab ).prependTo( '.arve-options-form' );
		$('.arve-settings-section').not( last_tab ).hide();
	}
	*/

	$('[data-arve-image-upload]').click(function(e) {
		e.preventDefault();
		var target = $( this ).attr('data-arve-image-upload'),
		image = wp.media({
			title: 'Upload Image',
			// mutiple: true if you want to upload multiple files at once
			multiple: false
		}).open()
		.on('select', function(){
			// This will return the selected image from the Media Uploader, the result is an object
			var uploaded_image = image.state().get('selection').first();
			// We convert uploaded_image to a JSON object to make accessing it easier
			// Output to the console uploaded_image
			//console.log(uploaded_image);
			//console.log( $( this ) );
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

	/*
	$(window).on( 'keyup', function(e) {
		if ( e.ctrlKey && e.shiftKey && 'v' === e.key ) {
			open_arve_dialog();
		}
	} );
	*/

	$( '#arve-btn' ).on( 'click', function(e) {
		e.preventDefault();
		if( $( '#arve-btn[data-arve-sui]' ).length ) {
			open_arve_dialog();
		} else {
			alert( 'ARVE needs the "Shortcake (Shortcode UI)" plugin active for this fuctionality, please download this version https://nextgenthemes.com/shortcode-ui-0.7.0-alpha.zip and install from zip. Current wp.org version 0.6.2 has a issue.' );
		}
	} );

	function open_arve_dialog() {
		var arve_shortcode = sui.utils.shortcodeViewConstructor.parseShortcodeString( '[arve]' );
		wp.media({
			frame : 'post',
			state : 'shortcode-ui',
			currentShortcode : arve_shortcode
		}).open();
	}

}(jQuery));
