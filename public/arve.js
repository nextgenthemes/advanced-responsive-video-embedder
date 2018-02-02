(function () {
	'use strict';

	var qs    = document.querySelector.bind(document);
	var qsa   = document.querySelectorAll.bind(document);
	var $body = document.body;

	if (window.NodeList && !NodeList.prototype.forEach) {
		NodeList.prototype.forEach = function (callback, thisArg) {
			thisArg = thisArg || window;
			for (var i = 0; i < this.length; i++) {
				callback.call(thisArg, this[i], i, this);
			}
		};
	}

	function unwrap( selector ) {
		qsa( selector ).forEach(
			function( el ) {
				el.outerHTML = el.innerHTML;
			}
		);
	}

	function remove_unwanted_stuff() {
		unwrap( '.arve-wrapper p' );
		unwrap( '.arve-wrapper .video-wrap' );
		unwrap( '.arve-wrapper .fluid-width-video-wrapper' );
		unwrap( '.arve-wrapper .fluid-vids' );

		qsa( '.arve-wrapper br' ).forEach(
			function( el ) {
				el.remove();
			}
		);

		qsa( '.arve-iframe, .arve-video' ).forEach(
			function( el ) {
				el.removeAttribute( 'style' );
			}
		);
	};

	function global_id() {

		if ( qs( 'html[id="arve"]' ) ) {
			return;
		}

		if ( ! qs( 'html[id]' ) ) {

			qs( 'html' ).addAttribute( 'id', 'arve' );

		} else if ( ! qs( 'body[id]' ) ) {

			$body.addAttribute( 'id', 'arve' );

		} else {

			$body.innerHTML = '<div id="arve">' + $body.innerHTML + '</div>';
		}
	}

	remove_unwanted_stuff();
	global_id();

	window.addEventListener( 'load', function() {
		remove_unwanted_stuff();
		global_id();
	} );

}());
