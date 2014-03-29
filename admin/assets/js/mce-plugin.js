// closure to avoid namespace collision
(function() {

	// creates the plugin
	tinymce.create('tinymce.plugins.arve', {
		// creates control instances based on the control's id.
		// our button's id is "arve_button"
		createControl : function(id, controlManager) {
			if ( id == 'arve_button' ) {
				// creates the button
				var button = controlManager.createButton('arve_button', {
					title : 'Embed Videos', // title of the button
					image : '../wp-content/plugins/advanced-responsive-video-embedder/admin/assets/img/tinymce-icon.png',  // path to the button's image
					onclick : function() {
						// triggers the thickbox
						var width = jQuery(window).width(),
						H = jQuery(window).height(),
						W = ( 720 < width ) ? 720 : width;
						W = W - 80;
						H = H - 84;
						tb_show( 'Advanced Responsive Video Embedder Shortcode Creater', '#TB_inline?width=' + W + '&height=' + H + '&inlineId=arve-form' );
					}
				});
				return button;
			}
			return null;
		}
	});

	tinymce.PluginManager.add('arve', tinymce.plugins.arve);

})();

jQuery(document).ready(function($) {

	$.ajax({
		type: 'GET',
		url: 'admin-ajax.php',
		data: { action: 'get_arve_form' },
		success: function( response ) {
			// var table = $(response).find('table');
			$(response).appendTo('body').hide();

			var create_shortcode = function() {

				if ( ( $('#arve-provider').val() === '' ) || ( $('#arve-id').val() === '' ) ) {
					return;
				}

				// defines the options and their default values
				// again, this is not the most elegant way to do this
				// but well, this gets the job done nonetheless
				var options = {
					'id'         : '',
					'mode'       : '',
					'align'      : '',
					'autoplay'   : '',
					'maxwidth'   : ''
				};

				var shortcode = '[' + $('#arve-provider').val();

				for( var index in options) {
					var value = $('#arve-' + index).val();

					// attaches the attribute to the shortcode only if it's different from the default value
					if ( value !== options[index] )
						shortcode += ' ' + index + '="' + value + '"';
				}

				shortcode += ']';

				return shortcode;
			};

			var info = $("#arve-url-info");
			info.dialog( {
				'dialogClass'   : 'wp-dialog',
				'modal'         : true,
				'autoOpen'      : false,
				'closeOnEscape' : true,
				'buttons'       : {
					"Close": function() {
						$(this).dialog('close');
					}
				}
			});

			$("#arve-open-url-info").click( function( event ) {
				event.preventDefault();
				info.dialog('open');
			});

			$("#arve-show-more").click( function( event ) {
				event.preventDefault();
				$('.arve-hidden').fadeIn();
			});

			// handles the click event of the submit button
			$('#arve-submit').click( function( event ){

				event.preventDefault();

				if ( ($('#arve-id').val() === '') || ($('#arve-id').val() === 'nothing matched') ) {
					alert('no id');
					return;
				}

				if ( $('#arve-provider').val() === '' )  {
					alert('no provider selected');
					return;
				}

				// inserts the shortcode into the active editor
				tinyMCE.activeEditor.execCommand('mceInsertContent', 0, create_shortcode());

				// closes Thickbox
				tb_remove();
			});

			var detect_id = function( code ) {

				//var regExp;
				var embed_regex = new Object(); 
				var output      = new Object();
				var match;

				$.each(arve_regex_list, function(provider, regex) {

					regex = new RegExp(regex,"i");

					match = code.match( regex );
					
					if ( match && match[1] ) {
						output.provider = provider;
						output.videoid  = match[1];
						return false;
					}

				});

				if( ! $.isEmptyObject(output) ) {
					return output;
				}

				// MTV services
				embed_regex.comedycentral = /comedycentral\.com:([a-z0-9\-]{36})/i;
				embed_regex.gametrailers  = /gametrailers\.com:([a-z0-9\-]{36})/i;
				embed_regex.spike         = /spike\.com:([a-z0-9\-]{36})/i;

				embed_regex.flickr        = /flickr\.com\/photos\/[a-zA-Z0-9@_\-]+\/([0-9]+)/i;
				embed_regex.videojug      = /videojug\.com\/embed\/([a-z0-9\-]{36})/i;
				embed_regex.bliptv        = /blip\.tv\/play\/([a-z0-9]+)/i;
				embed_regex.movieweb      = /movieweb\.com\/v\/([a-z0-9]{14})/i

				embed_regex.iframe        = /src=(?:'|")(https?:\/\/(www\.)?[^'"]+)/i

				$.each(embed_regex, function(provider, regex) {

					match = code.match( regex );
					
					if ( match && match[1] ) {
						output.provider = provider;
						output.videoid  = match[1];
						return false;
					}

				});

				if( ! $.isEmptyObject(output) ) {
					return output;
				}

				return 'nothing matched';
			};

			$('#arve-url').bind('keyup mouseup change', function() {

				var response = detect_id( $(this).val() );

				if ( 'nothing matched' == response ) {
					return;
				}

				$("#arve-provider option").each(function () {
					if ( $(this).html() == response.provider ) {
						$(this).attr("selected", "selected");
						return;
					}
				});

				$('#arve-id').val( response.videoid );
			});

			$( '#arve-url, #arve-provider, #arve-id, #arve-maxwidth, #arve-mode, #arve-align, #arve-autoplay' ).bind( 'keyup mouseup change', function() {

				shortcode = create_shortcode();

				if ( shortcode ) {
					$('#arve-shortcode').html( shortcode );
				} else {
					$('#arve-shortcode').html( '-' );
				}

			});

		}
	});

}); // $(document).ready(function() end