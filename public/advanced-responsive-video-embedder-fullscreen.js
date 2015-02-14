window.onload = function() {

	"use_strict";

    var arve_iframe_btns = document.getElementsByClassName('arve-iframe-btn')

    for (var i=0; i < arve_iframe_btns.length; i++) {

    	arve_iframe_btns[i].onclick = function() {

			var id = this.getAttribute('data-arve-wrapper')
			
			var wrapper = document.getElementById('arve-wrapper-' + id)
			
			var iframe  = wrapper.querySelectorAll('iframe')
			
			if (screenfull.enabled) {
				screenfull.request(wrapper)
			}
			
			console.log(id)

			iframe.setAttribute('src', iframe.getAttribute('data-src'))
			iframe.className = 'arve-inner'
			this.parentNode.removeChild(this)
    	}
    }
}