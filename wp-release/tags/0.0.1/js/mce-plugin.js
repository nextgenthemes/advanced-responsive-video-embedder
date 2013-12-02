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
					image : '../wp-content/plugins/advanced-responsive-video-embedder/img/tinymce-icon.png',  // path to the button's image
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
		success: function(response){
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
			$("#arve-open-url-info").click(function(event) {
				event.preventDefault();
				info.dialog('open');
			});

			$("#arve-show-more").click(function () {
				$('.arve-hidden').fadeIn();
			});

			// handles the click event of the submit button
			$('#arve-submit').click(function(){

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

			var getid = function(code){
				var regExp,
				match,
				output = new Array(2);

				regExp = /vimeo\.com\/(?:(?:channels\/[A-z]+\/)|(?:groups\/[A-z]+\/videos\/))?([0-9]+)/i;
				match = code.match(regExp);
				if ( match && match[1] ) {
					output[0] = 'vimeo';
					output[1] = match[1];
					return output;
				}

				regExp = /(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i;
				match = code.match(regExp);
				if ( match && match[1] ) {
					output[0] = 'youtube';
					output[1] = match[1];
					return output;
				}

				regExp = /metacafe\.com\/(?:watch|fplayer)\/(\d+)/i;
				match = code.match(regExp);
				if ( match && match[1] ) {
					output[0] = 'metacafe';
					output[1] = match[1];
					return output;
				}

				regExp = /veoh\.com\/watch\/([a-z0-9]+)/i;
				match = code.match(regExp);
				if ( match && match[1] ) {
					output[0] = 'veoh';
					output[1] = match[1];
					return output;
				}

				regExp = /dailymotion\.com\/(?:video|hub)\/([a-z0-9]{2,7})/i;
				match = code.match(regExp);
				if ( match && match[1] ) {
					output[0] = 'dailymotion';
					output[1] = match[1];
					return output;
				}

				// dailymotion with # in url
				regExp = /dailymotion\.com\/(?:video|hub)\/[a-z0-9]{2,7}_[a-z0-9_\-]+#video=([a-z0-9]{2,7})/i;
				match = code.match(regExp);
				if ( match && match[1] ) {
					output[0] = 'dailymotion';
					output[1] = match[1];
					return output;
				}

				// dailymotion playlist
				regExp = /dailymotion\.com\/(?:playlist\/|widget\/jukebox\?list\[\]=%2Fplaylist%2F)([a-z0-9]+)/i;
				match = code.match(regExp);
				if ( match && match[1] ) {
					output[0] = 'dailymotionlist';
					output[1] = match[1];
					return output;
				}

				regExp = /flickr\.com\/photos\/[a-zA-Z0-9@_\-]+\/([0-9]+)/i;
				match = code.match(regExp);
				if ( match && match[1] ) {
					output[0] = 'flickr';
					output[1] = match[1];
					return output;
				}

				// only embed code
				regExp = /blip\.tv\/play\/([a-z0-9]+)/i;
				match = code.match(regExp);
				if ( match && match[1] ) {
					output[0] = 'bliptv';
					output[1] = match[1];
					return output;
				}
			
				regExp = /collegehumor\.com\/video\/([0-9]{7})/i;
				match = code.match(regExp);
				if ( match && match[1] ) {
					output[0] = 'collegehumor';
					output[1] = match[1];
					return output;
				}

				regExp = /snotr\.com\/video\/([0-9]+)/i;
				match = code.match(regExp);
				if ( match && match[1] ) {
					output[0] = 'snotr';
					output[1] = match[1];
					return output;
				}

				regExp = /videojug\.com\/embed\/([a-z0-9-]{36})/i;
				match = code.match(regExp);
				if ( match && match[1] ) {
					output[0] = 'videojug';
					output[1] = match[1];
					return output;
				}

				regExp = /ustream\.tv\/(?:channel|embed|recorded)\/(?:recorded\/)?([0-9]{8})/i;
				match = code.match(regExp);
				if ( match && match[1] ) {
					output[0] = 'ustream';
					output[1] = match[1];
					return output;
				}

				// usteam highlight url
				regExp = /ustream\.tv\/(?:channel|embed|recorded)\/(?:recorded\/)?[0-9]{8}\/highlight\/([0-9]+)/i;
				match = code.match(regExp);
				if ( match && match[1] ) {
					output[0] = 'ustream';
					output[1] = match[1];
					return output;
				}

				regExp = /viddler\.com\/(?:embed|v)\/([0-9a-z]{8})/i;
				match = code.match(regExp);
				if ( match && match[1] ) {
					output[0] = 'viddler';
					output[1] = match[1];
					return output;
				}

				regExp = /myvideo\.de\/(?:watch|embed)\/([0-9]{7})/i;
				match = code.match(regExp);
				if ( match && match[1] ) {
					output[0] = 'myvideo';
					output[1] = match[1];
					return output;
				}

				regExp = /liveleak\.com\/(?:view\?i|ll_embed\?f)=([0-9a-z\_]+)/i;
				match = code.match(regExp);
				if ( match && match[1] ) {
					output[0] = 'liveleak';
					output[1] = match[1];
					return output;
				}

				regExp = /funnyordie\.com\/videos\/([0-9a-z]{10})/i;
				match = code.match(regExp);
				if ( match && match[1] ) {
					output[0] = 'liveleak';
					output[1] = match[1];
					return output;
				}

				regExp = /archive\.org\/(?:details|embed)\/([0-9a-z]+)/i;
				match = code.match(regExp);
				if ( match && match[1] ) {
					output[0] = 'archiveorg';
					output[1] = match[1];
					return output;
				}

				regExp = /myspace\.com\/video\/(?:[a-z\/\-]+)?([0-9]{9})/i;
				match = code.match(regExp);
				if ( match && match[1] ) {
					output[0] = 'myspace';
					output[1] = match[1];
					return output;
				}

				regExp = /movieweb\.com\/v\/([a-z0-9]{14})/i;
				match = code.match(regExp);
				if ( match && match[1] ) {
					output[0] = 'movieweb';
					output[1] = match[1];
					return output;
				}

				regExp = /comedycentral\.com:([a-z0-9\-]{36})/i;
				match = code.match(regExp);
				if ( match && match[1] ) {
					output[0] = 'comedycentral';
					output[1] = match[1];
					return output;
				}

				regExp = /gametrailers\.com:([a-z0-9\-]{36})/i;
				match = code.match(regExp);
				if ( match && match[1] ) {
					output[0] = 'gametrailers';
					output[1] = match[1];
					return output;
				}

				regExp = /spike\.com:([a-z0-9\-]{36})/i;
				match = code.match(regExp);
				if ( match && match[1] ) {
					output[0] = 'spike';
					output[1] = match[1];
					return output;
				}

				regExp = /src="http:\/\/([a-z]+\.yahoo\.com\/video\/[a-z0-9-]+)\.html\?/i;
				match = code.match(regExp);
				if ( match && match[1] ) {
					output[0] = 'yahoo';
					output[1] = match[1];
					return output;
				}

				regExp = /src="(http[^"]+)"/i;
				match = code.match(regExp);
				if ( match && match[1] ) {
					output[0] = 'iframe';
					output[1] = match[1];
					return output;
				}

				// regExp = /clipfish\.de\/(?:embed_image\/\?vid=|[a-z\/\-]+)([0-9]{2,7})/i;

				console.log('nothing matched');
				return 'nothing matched';
			};

			$('#arve-url').bind('keyup mouseup change',function() {

				var provider_and_id = getid( $(this).val() );
				if ( provider_and_id != 'nothing matched' ) {
					$('#arve-provider').val( provider_and_id[0] );
					$('#arve-id').val( provider_and_id[1] );
				}
			});

			$('#arve-url, #arve-provider, #arve-id, #arve-maxwidth, #arve-mode, #arve-align, #arve-autoplay').bind('keyup mouseup change',function() {

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