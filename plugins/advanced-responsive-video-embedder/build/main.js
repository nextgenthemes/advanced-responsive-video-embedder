/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = "./plugins/advanced-responsive-video-embedder/src/main.ts");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./plugins/advanced-responsive-video-embedder/src/main.scss":
/*!******************************************************************!*\
  !*** ./plugins/advanced-responsive-video-embedder/src/main.scss ***!
  \******************************************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./plugins/advanced-responsive-video-embedder/src/main.ts":
/*!****************************************************************!*\
  !*** ./plugins/advanced-responsive-video-embedder/src/main.ts ***!
  \****************************************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _main_scss__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./main.scss */ "./plugins/advanced-responsive-video-embedder/src/main.scss");

const qsa = document.querySelectorAll.bind(document);
const jq = window.jQuery;
globalID();
removeUnwantedStuff();
document.addEventListener('DOMContentLoaded', () => {
    removeUnwantedStuff();
});
if (jq && typeof jq.fn.fitVids !== 'undefined') {
    jq(document).ready(() => {
        setTimeout(() => {
            removeUnwantedStuff();
        }, 1);
    });
}
function removeUnwantedStuff() {
    qsa('.arve p, .arve .video-wrap, .arve .fluid-width-video-wrapper, .arve .fluid-vids').forEach((el) => {
        unwrap(el);
    });
    qsa('.ast-oembed-container').forEach((el) => {
        if (el.querySelector('.arve')) {
            unwrap(el);
        }
    });
    qsa('.arve br').forEach((el) => {
        el.remove();
    });
    qsa('.arve-iframe, .arve-video').forEach((el) => {
        el.removeAttribute('width');
        el.removeAttribute('height');
        el.removeAttribute('style');
    });
    qsa('.wp-block-embed').forEach((el) => {
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
    if ('html' === document.documentElement.id) {
        return;
    }
    if (!document.documentElement.id) {
        document.documentElement.id = 'html';
    }
    else if (!document.body.id) {
        document.body.id = 'html';
    }
}
function unwrap(el) {
    const parent = el.parentNode;
    if (!parent) {
        return;
    }
    while (parent && el.firstChild) {
        parent.insertBefore(el.firstChild, el);
    }
    parent.removeChild(el);
}


/***/ })

/******/ });
//# sourceMappingURL=main.js.map