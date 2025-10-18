/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./src/admin.scss":
/*!************************!*\
  !*** ./src/admin.scss ***!
  \************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./src/main.scss":
/*!***********************!*\
  !*** ./src/main.scss ***!
  \***********************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./src/main.ts":
/*!*********************!*\
  !*** ./src/main.ts ***!
  \*********************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   domReady: () => (/* binding */ domReady),
/* harmony export */   globalID: () => (/* binding */ globalID)
/* harmony export */ });
/* harmony import */ var _main_scss__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./main.scss */ "./src/main.scss");

const d = document;
const qsa = d.querySelectorAll.bind(d);
const jq = window.jQuery;
globalID();
domReady(() => {
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

/***/ }),

/***/ "./src/shortcode-dialog.scss":
/*!***********************************!*\
  !*** ./src/shortcode-dialog.scss ***!
  \***********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

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
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
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
// This entry needs to be wrapped in an IIFE because it needs to be isolated against other modules in the chunk.
(() => {
/*!**********************!*\
  !*** ./src/admin.ts ***!
  \**********************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _admin_scss__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./admin.scss */ "./src/admin.scss");
/* harmony import */ var _shortcode_dialog_scss__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./shortcode-dialog.scss */ "./src/shortcode-dialog.scss");
/* harmony import */ var _main__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./main */ "./src/main.ts");




const d = document;
const qs = d.querySelector.bind(d);
(0,_main__WEBPACK_IMPORTED_MODULE_2__.globalID)();
setEditorCanvasID();
function setEditorCanvasID() {
  // Taken from https://github.com/WordPress/gutenberg/blob/3317ba195da0149d0bae221dc3516cd76f536c5d/packages/react-native-bridge/common/gutenberg-web-single-block/editor-behavior-overrides.js#L126
  // The editor-canvas iframe relies upon `srcdoc`, which does not trigger a
  // `load` event. Thus, we must poll for the iframe to be ready.
  let attemptsToApplyID = 0;
  const interval = setInterval(() => {
    attemptsToApplyID++;
    const canvasIframe = qs('iframe[name="editor-canvas"]');
    const canvasBody = canvasIframe?.contentDocument?.body;
    if (canvasBody) {
      clearInterval(interval);
      canvasBody.setAttribute('id', 'html');
    }

    // Safeguard against an infinite loop.
    if (attemptsToApplyID > 100) {
      clearInterval(interval);
    }
  }, 300);
}
d.addEventListener('click', event => {
  const target = event?.target;
  if (target instanceof HTMLElement && target.matches('.notice-dismiss')) {
    event.preventDefault();
    const parent = target.parentNode;
    const noticeId = parent?.getAttribute('id');
    if (!parent?.matches('.notice.is-dismissible') || !noticeId) {
      return;
    }
    fetch(window.ajaxurl, {
      method: 'POST',
      credentials: 'same-origin',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded; charset=utf-8'
      },
      body: 'action=dnh_dismiss_notice&id=' + noticeId
    });
  }
});
})();

/******/ })()
;
//# sourceMappingURL=admin.js.map