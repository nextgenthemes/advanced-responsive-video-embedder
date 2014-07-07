window.onload = function() {

	"use_strict";

    var arve_iframe_btns = document.getElementsByClassName( "arve-iframe-btn" );

    for ( var i=0; i < arve_iframe_btns.length; i++ ) {

    	arve_iframe_btns[i].onclick = function() {

			var target = document.getElementById( this.getAttribute( "data-target" ) );
			target.setAttribute( "src", target.getAttribute( "data-src" ) );
			target.className = "arve-inner";
			this.parentNode.removeChild(this);
    	};
    };
};