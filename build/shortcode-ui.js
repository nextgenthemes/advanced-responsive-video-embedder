/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	// The require scope
/******/ 	var __webpack_require__ = {};
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
/*!*****************************!*\
  !*** ./src/shortcode-ui.ts ***!
  \*****************************/
__webpack_require__.r(__webpack_exports__);

const _ = window._;
const domParser = new DOMParser();
function arveExtractURL(changed, collection, shortcode) {
  function attrByName(name) {
    return _.find(collection, function (viewModel) {
      return name === viewModel.model.get('attr');
    });
  }
  if (typeof changed.value === 'undefined') {
    return;
  }
  const urlInput = attrByName('url').$el.find('input');
  const arInput = attrByName('aspect_ratio').$el.find('input');

  // <iframe src="https://example.com" width="640" height="360"></iframe>

  const iframe = domParser.parseFromString(changed.value, 'text/html').querySelector('iframe');
  if (iframe && iframe.hasAttribute('src')) {
    urlInput.val(iframe.src).trigger('input');
    const w = iframe.width;
    const h = iframe.height;
    if (w && h) {
      arInput.val(w + ':' + h).trigger('input');
    }
  }
}
window.wp.shortcake.hooks.addAction('arve.url', arveExtractURL);
/******/ })()
;
//# sourceMappingURL=shortcode-ui.js.map