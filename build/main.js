/******/ var __webpack_modules__ = ({

/***/ "./src/main.scss"
/*!***********************!*\
  !*** ./src/main.scss ***!
  \***********************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }

/******/ });
/************************************************************************/
/******/ // The module cache
/******/ var __webpack_module_cache__ = {};
/******/ 
/******/ // The require function
/******/ function __webpack_require__(moduleId) {
/******/ 	// Check if module is in cache
/******/ 	var cachedModule = __webpack_module_cache__[moduleId];
/******/ 	if (cachedModule !== undefined) {
/******/ 		return cachedModule.exports;
/******/ 	}
/******/ 	// Create a new module (and put it into the cache)
/******/ 	var module = __webpack_module_cache__[moduleId] = {
/******/ 		// no module.id needed
/******/ 		// no module.loaded needed
/******/ 		exports: {}
/******/ 	};
/******/ 
/******/ 	// Execute the module function
/******/ 	if (!(moduleId in __webpack_modules__)) {
/******/ 		delete __webpack_module_cache__[moduleId];
/******/ 		var e = new Error("Cannot find module '" + moduleId + "'");
/******/ 		e.code = 'MODULE_NOT_FOUND';
/******/ 		throw e;
/******/ 	}
/******/ 	__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 
/******/ 	// Return the exports of the module
/******/ 	return module.exports;
/******/ }
/******/ 
/************************************************************************/
/******/ /* webpack/runtime/define property getters */
/******/ (() => {
/******/ 	// define getter functions for harmony exports
/******/ 	__webpack_require__.d = (exports, definition) => {
/******/ 		for(var key in definition) {
/******/ 			if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 				Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 			}
/******/ 		}
/******/ 	};
/******/ })();
/******/ 
/******/ /* webpack/runtime/hasOwnProperty shorthand */
/******/ (() => {
/******/ 	__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ })();
/******/ 
/******/ /* webpack/runtime/make namespace object */
/******/ (() => {
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = (exports) => {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/ })();
/******/ 
/************************************************************************/
var __webpack_exports__ = {};
// This entry needs to be wrapped in an IIFE because it needs to be isolated against other modules in the chunk.
(() => {
/*!*********************!*\
  !*** ./src/main.ts ***!
  \*********************/
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   domReady: () => (/* binding */ domReady)
/* harmony export */ });
/* harmony import */ var _main_scss__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./main.scss */ "./src/main.scss");

const d = document;
const qsa = d.querySelectorAll.bind(d);
const jq = window.jQuery;
const isSafari = /^((?!chrome|android).)*safari/i.test(navigator.userAgent);
globalID();
domReady(() => {
  removeUnwantedStuff();
  addSafariVideoWorkaround();
});

// Mitigation for outdated versions of fitvids
if (jq && typeof jq.fn.fitVids !== 'undefined') {
  jq(d).ready(() => {
    setTimeout(() => {
      removeUnwantedStuff();
    }, 1);
  });
}

// Add workaround for videos without posters, so Safari shows a frame of the video as the poster
function addSafariVideoWorkaround() {
  if (!isSafari) {
    return;
  }
  document.querySelectorAll('video').forEach(video => {
    if (!video.poster) {
      // Add #t=0.001 workaround for videos without posters
      const src = video.src || video.querySelector('source')?.src;
      if (src && !src.includes('#t=')) {
        video.src = src + '#t=0.001';
      }
    }
  });
}
function removeUnwantedStuff() {
  qsa('.arve p:not(.arve-error p), .arve .video-wrap, .arve .fluid-width-video-wrapper, .arve .fluid-vids').forEach(el => {
    unwrap(el);
  });

  // Astor + Hueman Child theme fixes
  qsa('.ast-oembed-container, .video-container').forEach(el => {
    if (el.querySelector('.arve')) {
      unwrap(el);
    }
  });
  qsa('.arve-iframe, .arve-video').forEach(el => {
    el.removeAttribute('width');
    el.removeAttribute('height');
    el.removeAttribute('style');
  });
}
function globalID() {
  // Usually the id should be already there added with php using the language_attributes filter
  if ('html' === d.documentElement.id) {
    return;
  }
  if (!d.documentElement.id) {
    d.documentElement.id = 'html';
  } else if (!d.body.id) {
    d.body.id = 'html';
  }
}
function unwrap(el) {
  // Type guard for parentNode to ensure it exists and is a Node
  const parent = el.parentNode;
  if (!(parent instanceof Node)) {
    throw new Error('Element has no parent node');
  }

  // Move all children to parent
  while (el.firstChild) {
    parent.insertBefore(el.firstChild, el);
  }

  // Remove the empty element
  parent.removeChild(el);
}
function domReady(callback) {
  if (typeof d === 'undefined') {
    return;
  }
  if (d.readyState === 'complete' ||
  // DOMContentLoaded + Images/Styles/etc loaded, so we call directly.
  d.readyState === 'interactive' // DOMContentLoaded fires at this point, so we call directly.
  ) {
    return void callback();
  }

  // DOMContentLoaded has not fired yet, delay callback until then.
  d.addEventListener('DOMContentLoaded', callback);
}
})();

const __webpack_exports__domReady = __webpack_exports__.domReady;
export { __webpack_exports__domReady as domReady };

//# sourceMappingURL=main.js.map