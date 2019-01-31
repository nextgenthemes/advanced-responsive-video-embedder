( function( $ ) {
	'use strict';
	/*global ajaxurl */
	/*global sui */
	/*global tb_show */

	// Options Page
	$( '.arve-settings-section' ).each( function() {

		$( this ).insertBefore( $( this ).parent() );
	});

	$( '.arve-settings-section' ).each( function() {

		var id	   = $( this ).attr( 'id' );
		var classs = $( this ).attr( 'class' );
		var title  = $( this ).attr( 'title' );

		$( this ).nextUntil( '.arve-settings-section' ).wrapAll( '<section id="' + id + '" class="' + classs + '" />' );

		$( '<a href="#" data-target="#' + id + '" class="nav-tab">' + title + '</a>' ).appendTo( '.arve-settings-tabs' );

		$( this ).remove();
	});

	function showTab( target ) {

		$( '.arve-settings-section' ).show();
		$( target ).prependTo( '.arve-options-form' );
		$( '.arve-settings-section' ).not( target ).hide();
		$( '.arve-settings-tabs a' ).removeClass( 'nav-tab-active' );
		$( '.arve-settings-tabs a[data-target="' + target + '"]' ).addClass( 'nav-tab-active' );
	}

	if ( $( '#arve_options_main\\[last_settings_tab\\]' ).length && $( '#arve_options_main\\[last_settings_tab\\]' ).val().length ) {
		showTab( $( '#arve_options_main\\[last_settings_tab\\]' ).val() );
	}

	$( document ).on( 'click', '.arve-settings-tabs a', function( e ) {

		var target = $( this ).attr( 'data-target' );
		showTab( target );
		$( '#arve_options_main\\[last_settings_tab\\]' ).val( target );
		e.preventDefault();
	});

	$( document ).on( 'click', '[data-image-upload]', function( e ) {
		var target = $( this ).attr( 'data-image-upload' );
		var image  = wp.media({
			title: 'Upload Image',

			// mutiple: true if you want to upload multiple files at once
			multiple: false
		})
		.open()
		.on( 'select', function() {

			// This will return the selected image from the Media Uploader, the result is an object
			var uploadedImage = image.state().get( 'selection' ).first();

			// We convert uploadedImage to a JSON object to make accessing it easier
			// Output to the console uploadedImage
			var attachmentID = uploadedImage.toJSON().id;

			// Let's assign the url value to the input field
			$( target ).val( attachmentID );
		});
		e.preventDefault();
	});

	$( document ).on( 'click', '#arve-btn', function() {

		var arveShortcode;

		if ( 'undefined' !== typeof( sui ) ) {

			arveShortcode = sui.utils.shortcodeViewConstructor.parseShortcodeString( '[arve]' );

			wp.media({
				frame: 'post',
				state: 'shortcode-ui',
				currentShortcode: sui.utils.shortcodeViewConstructor.parseShortcodeString( '[arve]' )
			}).open();

		} else {

			tb_show( 'ARVE Optional Features', '#TB_inline?inlineId=arve-thickbox' );
		}

	});

}( jQuery ) );
