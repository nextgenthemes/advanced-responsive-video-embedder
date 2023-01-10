/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./plugins/advanced-responsive-video-embedder/src/main.scss":
/*!******************************************************************!*\
  !*** ./plugins/advanced-responsive-video-embedder/src/main.scss ***!
  \******************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	!function() {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = function(exports) {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	}();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be isolated against other modules in the chunk.
!function() {
/*!****************************************************************!*\
  !*** ./plugins/advanced-responsive-video-embedder/src/main.ts ***!
  \****************************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _main_scss__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./main.scss */ "./plugins/advanced-responsive-video-embedder/src/main.scss");

const qsa = document.querySelectorAll.bind(document);
const jq = window.jQuery;
globalID();
removeUnwantedStuff();
document.addEventListener('DOMContentLoaded', () => {
  removeUnwantedStuff();
});

// Mitigation for outdated versions of fitvids
if (jq && typeof jq.fn.fitVids !== 'undefined') {
  jq(document).ready(() => {
    setTimeout(() => {
      removeUnwantedStuff();
    }, 1);
  });
}
function removeUnwantedStuff() {
  qsa('.arve p, .arve .video-wrap, .arve .fluid-width-video-wrapper, .arve .fluid-vids').forEach(el => {
    unwrap(el);
  });

  // Astor theme fix
  qsa('.ast-oembed-container').forEach(el => {
    if (el.querySelector('.arve')) {
      unwrap(el);
    }
  });
  qsa('.arve br').forEach(el => {
    el.remove();
  });
  qsa('.arve-iframe, .arve-video').forEach(el => {
    el.removeAttribute('width');
    el.removeAttribute('height');
    el.removeAttribute('style');
  });
  qsa('.wp-block-embed').forEach(el => {
    if (el.querySelector('.arve')) {
      el.classList.remove('wp-embed-aspect-16-9', 'wp-has-aspect-ratio');
      const wrapper = el.querySelector('.wp-block-embed__wrapper');
      if (wrapper) {
        unwrap(wrapper);
      }
    }
  });
}
function globalID() {
  // Usually the id should be already there added with php using the language_attributes filter
  if ('html' === document.documentElement.id) {
    return;
  }
  if (!document.documentElement.id) {
    document.documentElement.id = 'html';
  } else if (!document.body.id) {
    document.body.id = 'html';
  }
}
function unwrap(el) {
  const parent = el.parentNode;
  // make eslint STFU
  if (!parent) {
    return;
  }
  // move all children out of the element
  while (parent && el.firstChild) {
    parent.insertBefore(el.firstChild, el);
  }
  // remove the empty element
  parent.removeChild(el);
}
}();
/******/ })()
;
//# sourceMappingURL=main.js.map