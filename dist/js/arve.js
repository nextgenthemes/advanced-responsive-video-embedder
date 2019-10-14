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
/******/ 	__webpack_require__.p = "/";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 0);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/common/scss/settings.scss":
/*!*********************************************!*\
  !*** ./resources/common/scss/settings.scss ***!
  \*********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("// removed by extract-text-webpack-plugin//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9yZXNvdXJjZXMvY29tbW9uL3Njc3Mvc2V0dGluZ3Muc2Nzcz83ZjUyIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiJBQUFBIiwiZmlsZSI6Ii4vcmVzb3VyY2VzL2NvbW1vbi9zY3NzL3NldHRpbmdzLnNjc3MuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvLyByZW1vdmVkIGJ5IGV4dHJhY3QtdGV4dC13ZWJwYWNrLXBsdWdpbiJdLCJzb3VyY2VSb290IjoiIn0=\n//# sourceURL=webpack-internal:///./resources/common/scss/settings.scss\n");

/***/ }),

/***/ "./resources/js/arve.js":
/*!******************************!*\
  !*** ./resources/js/arve.js ***!
  \******************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("var qsa = document.querySelectorAll.bind(document);\n\nfunction unwrap(el) {\n  // get the element's parent node\n  var parent = el.parentNode; // move all children out of the element\n\n  while (el.firstChild) {\n    parent.insertBefore(el.firstChild, el);\n  } // remove the empty element\n\n\n  parent.removeChild(el);\n}\n\nfunction removeUnwantedStuff() {\n  qsa('.arve p, .arve .video-wrap, .arve .fluid-width-video-wrapper, .arve .fluid-vids').forEach(function (el) {\n    unwrap(el);\n  });\n  qsa('.arve br').forEach(function (el) {\n    el.remove();\n  });\n  qsa('.arve-iframe, .arve-video').forEach(function (el) {\n    el.removeAttribute('width');\n    el.removeAttribute('height');\n    el.removeAttribute('style');\n  });\n  qsa('.wp-block-embed').forEach(function (el) {\n    if (el.querySelector('.arve')) {\n      el.classList.remove(['wp-embed-aspect-16-9', 'wp-has-aspect-ratio']);\n      var $WRAPPER = el.querySelector('.wp-block-embed__wrapper');\n\n      if ($WRAPPER) {\n        unwrap($WRAPPER);\n      }\n    }\n  });\n}\n\nfunction globalID() {\n  // Usually the id should be already there added with php using the language_attributes filter\n  if ('global' === document.documentElement.id) {\n    return;\n  }\n\n  if (!document.documentElement.id) {\n    document.documentElement.id = 'global';\n  } else if (!document.body.id) {\n    document.body.id = 'global';\n  }\n}\n\nremoveUnwantedStuff();\nglobalID();\ndocument.addEventListener('DOMContentLoaded', function () {\n  removeUnwantedStuff();\n  globalID();\n});//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9yZXNvdXJjZXMvanMvYXJ2ZS5qcz9kNzFlIl0sIm5hbWVzIjpbInFzYSIsImRvY3VtZW50IiwicXVlcnlTZWxlY3RvckFsbCIsImJpbmQiLCJ1bndyYXAiLCJlbCIsInBhcmVudCIsInBhcmVudE5vZGUiLCJmaXJzdENoaWxkIiwiaW5zZXJ0QmVmb3JlIiwicmVtb3ZlQ2hpbGQiLCJyZW1vdmVVbndhbnRlZFN0dWZmIiwiZm9yRWFjaCIsInJlbW92ZSIsInJlbW92ZUF0dHJpYnV0ZSIsInF1ZXJ5U2VsZWN0b3IiLCJjbGFzc0xpc3QiLCIkV1JBUFBFUiIsImdsb2JhbElEIiwiZG9jdW1lbnRFbGVtZW50IiwiaWQiLCJib2R5IiwiYWRkRXZlbnRMaXN0ZW5lciJdLCJtYXBwaW5ncyI6IkFBQUEsSUFBTUEsR0FBRyxHQUFHQyxRQUFRLENBQUNDLGdCQUFULENBQTBCQyxJQUExQixDQUFnQ0YsUUFBaEMsQ0FBWjs7QUFFQSxTQUFTRyxNQUFULENBQWlCQyxFQUFqQixFQUFzQjtBQUNyQjtBQUNBLE1BQU1DLE1BQU0sR0FBR0QsRUFBRSxDQUFDRSxVQUFsQixDQUZxQixDQUdyQjs7QUFDQSxTQUFRRixFQUFFLENBQUNHLFVBQVgsRUFBd0I7QUFDdkJGLFVBQU0sQ0FBQ0csWUFBUCxDQUFxQkosRUFBRSxDQUFDRyxVQUF4QixFQUFvQ0gsRUFBcEM7QUFDQSxHQU5vQixDQU9yQjs7O0FBQ0FDLFFBQU0sQ0FBQ0ksV0FBUCxDQUFvQkwsRUFBcEI7QUFDQTs7QUFFRCxTQUFTTSxtQkFBVCxHQUErQjtBQUM5QlgsS0FBRyxDQUFFLGlGQUFGLENBQUgsQ0FBeUZZLE9BQXpGLENBQWtHLFVBQUVQLEVBQUYsRUFBVTtBQUMzR0QsVUFBTSxDQUFFQyxFQUFGLENBQU47QUFDQSxHQUZEO0FBSUFMLEtBQUcsQ0FBRSxVQUFGLENBQUgsQ0FBa0JZLE9BQWxCLENBQTJCLFVBQUVQLEVBQUYsRUFBVTtBQUNwQ0EsTUFBRSxDQUFDUSxNQUFIO0FBQ0EsR0FGRDtBQUlBYixLQUFHLENBQUUsMkJBQUYsQ0FBSCxDQUFtQ1ksT0FBbkMsQ0FBNEMsVUFBRVAsRUFBRixFQUFVO0FBQ3JEQSxNQUFFLENBQUNTLGVBQUgsQ0FBb0IsT0FBcEI7QUFDQVQsTUFBRSxDQUFDUyxlQUFILENBQW9CLFFBQXBCO0FBQ0FULE1BQUUsQ0FBQ1MsZUFBSCxDQUFvQixPQUFwQjtBQUNBLEdBSkQ7QUFNQWQsS0FBRyxDQUFFLGlCQUFGLENBQUgsQ0FBeUJZLE9BQXpCLENBQWtDLFVBQUVQLEVBQUYsRUFBVTtBQUMzQyxRQUFLQSxFQUFFLENBQUNVLGFBQUgsQ0FBa0IsT0FBbEIsQ0FBTCxFQUFtQztBQUNsQ1YsUUFBRSxDQUFDVyxTQUFILENBQWFILE1BQWIsQ0FBcUIsQ0FBRSxzQkFBRixFQUEwQixxQkFBMUIsQ0FBckI7QUFFQSxVQUFNSSxRQUFRLEdBQUdaLEVBQUUsQ0FBQ1UsYUFBSCxDQUFrQiwwQkFBbEIsQ0FBakI7O0FBRUEsVUFBS0UsUUFBTCxFQUFnQjtBQUNmYixjQUFNLENBQUVhLFFBQUYsQ0FBTjtBQUNBO0FBQ0Q7QUFDRCxHQVZEO0FBV0E7O0FBRUQsU0FBU0MsUUFBVCxHQUFvQjtBQUVuQjtBQUNBLE1BQUssYUFBYWpCLFFBQVEsQ0FBQ2tCLGVBQVQsQ0FBeUJDLEVBQTNDLEVBQWdEO0FBQy9DO0FBQ0E7O0FBRUQsTUFBSyxDQUFFbkIsUUFBUSxDQUFDa0IsZUFBVCxDQUF5QkMsRUFBaEMsRUFBcUM7QUFDcENuQixZQUFRLENBQUNrQixlQUFULENBQXlCQyxFQUF6QixHQUE4QixRQUE5QjtBQUNBLEdBRkQsTUFFTyxJQUFLLENBQUVuQixRQUFRLENBQUNvQixJQUFULENBQWNELEVBQXJCLEVBQTBCO0FBQ2hDbkIsWUFBUSxDQUFDb0IsSUFBVCxDQUFjRCxFQUFkLEdBQW1CLFFBQW5CO0FBQ0E7QUFDRDs7QUFFRFQsbUJBQW1CO0FBQ25CTyxRQUFRO0FBRVJqQixRQUFRLENBQUNxQixnQkFBVCxDQUEyQixrQkFBM0IsRUFBK0MsWUFBTTtBQUNwRFgscUJBQW1CO0FBQ25CTyxVQUFRO0FBQ1IsQ0FIRCIsImZpbGUiOiIuL3Jlc291cmNlcy9qcy9hcnZlLmpzLmpzIiwic291cmNlc0NvbnRlbnQiOlsiY29uc3QgcXNhID0gZG9jdW1lbnQucXVlcnlTZWxlY3RvckFsbC5iaW5kKCBkb2N1bWVudCApO1xuXG5mdW5jdGlvbiB1bndyYXAoIGVsICkge1xuXHQvLyBnZXQgdGhlIGVsZW1lbnQncyBwYXJlbnQgbm9kZVxuXHRjb25zdCBwYXJlbnQgPSBlbC5wYXJlbnROb2RlO1xuXHQvLyBtb3ZlIGFsbCBjaGlsZHJlbiBvdXQgb2YgdGhlIGVsZW1lbnRcblx0d2hpbGUgKCBlbC5maXJzdENoaWxkICkge1xuXHRcdHBhcmVudC5pbnNlcnRCZWZvcmUoIGVsLmZpcnN0Q2hpbGQsIGVsICk7XG5cdH1cblx0Ly8gcmVtb3ZlIHRoZSBlbXB0eSBlbGVtZW50XG5cdHBhcmVudC5yZW1vdmVDaGlsZCggZWwgKTtcbn1cblxuZnVuY3Rpb24gcmVtb3ZlVW53YW50ZWRTdHVmZigpIHtcblx0cXNhKCAnLmFydmUgcCwgLmFydmUgLnZpZGVvLXdyYXAsIC5hcnZlIC5mbHVpZC13aWR0aC12aWRlby13cmFwcGVyLCAuYXJ2ZSAuZmx1aWQtdmlkcycgKS5mb3JFYWNoKCAoIGVsICkgPT4ge1xuXHRcdHVud3JhcCggZWwgKTtcblx0fSApO1xuXG5cdHFzYSggJy5hcnZlIGJyJyApLmZvckVhY2goICggZWwgKSA9PiB7XG5cdFx0ZWwucmVtb3ZlKCk7XG5cdH0gKTtcblxuXHRxc2EoICcuYXJ2ZS1pZnJhbWUsIC5hcnZlLXZpZGVvJyApLmZvckVhY2goICggZWwgKSA9PiB7XG5cdFx0ZWwucmVtb3ZlQXR0cmlidXRlKCAnd2lkdGgnICk7XG5cdFx0ZWwucmVtb3ZlQXR0cmlidXRlKCAnaGVpZ2h0JyApO1xuXHRcdGVsLnJlbW92ZUF0dHJpYnV0ZSggJ3N0eWxlJyApO1xuXHR9ICk7XG5cblx0cXNhKCAnLndwLWJsb2NrLWVtYmVkJyApLmZvckVhY2goICggZWwgKSA9PiB7XG5cdFx0aWYgKCBlbC5xdWVyeVNlbGVjdG9yKCAnLmFydmUnICkgKSB7XG5cdFx0XHRlbC5jbGFzc0xpc3QucmVtb3ZlKCBbICd3cC1lbWJlZC1hc3BlY3QtMTYtOScsICd3cC1oYXMtYXNwZWN0LXJhdGlvJyBdICk7XG5cblx0XHRcdGNvbnN0ICRXUkFQUEVSID0gZWwucXVlcnlTZWxlY3RvciggJy53cC1ibG9jay1lbWJlZF9fd3JhcHBlcicgKTtcblxuXHRcdFx0aWYgKCAkV1JBUFBFUiApIHtcblx0XHRcdFx0dW53cmFwKCAkV1JBUFBFUiApO1xuXHRcdFx0fVxuXHRcdH1cblx0fSApO1xufVxuXG5mdW5jdGlvbiBnbG9iYWxJRCgpIHtcblxuXHQvLyBVc3VhbGx5IHRoZSBpZCBzaG91bGQgYmUgYWxyZWFkeSB0aGVyZSBhZGRlZCB3aXRoIHBocCB1c2luZyB0aGUgbGFuZ3VhZ2VfYXR0cmlidXRlcyBmaWx0ZXJcblx0aWYgKCAnZ2xvYmFsJyA9PT0gZG9jdW1lbnQuZG9jdW1lbnRFbGVtZW50LmlkICkge1xuXHRcdHJldHVybjtcblx0fVxuXG5cdGlmICggISBkb2N1bWVudC5kb2N1bWVudEVsZW1lbnQuaWQgKSB7XG5cdFx0ZG9jdW1lbnQuZG9jdW1lbnRFbGVtZW50LmlkID0gJ2dsb2JhbCc7XG5cdH0gZWxzZSBpZiAoICEgZG9jdW1lbnQuYm9keS5pZCApIHtcblx0XHRkb2N1bWVudC5ib2R5LmlkID0gJ2dsb2JhbCc7XG5cdH1cbn1cblxucmVtb3ZlVW53YW50ZWRTdHVmZigpO1xuZ2xvYmFsSUQoKTtcblxuZG9jdW1lbnQuYWRkRXZlbnRMaXN0ZW5lciggJ0RPTUNvbnRlbnRMb2FkZWQnLCAoKSA9PiB7XG5cdHJlbW92ZVVud2FudGVkU3R1ZmYoKTtcblx0Z2xvYmFsSUQoKTtcbn0gKTtcbiJdLCJzb3VyY2VSb290IjoiIn0=\n//# sourceURL=webpack-internal:///./resources/js/arve.js\n");

/***/ }),

/***/ "./resources/scss/arve-admin.scss":
/*!****************************************!*\
  !*** ./resources/scss/arve-admin.scss ***!
  \****************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("// removed by extract-text-webpack-plugin//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9yZXNvdXJjZXMvc2Nzcy9hcnZlLWFkbWluLnNjc3M/NjhmMiJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiQUFBQSIsImZpbGUiOiIuL3Jlc291cmNlcy9zY3NzL2FydmUtYWRtaW4uc2Nzcy5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8vIHJlbW92ZWQgYnkgZXh0cmFjdC10ZXh0LXdlYnBhY2stcGx1Z2luIl0sInNvdXJjZVJvb3QiOiIifQ==\n//# sourceURL=webpack-internal:///./resources/scss/arve-admin.scss\n");

/***/ }),

/***/ "./resources/scss/arve.scss":
/*!**********************************!*\
  !*** ./resources/scss/arve.scss ***!
  \**********************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("// removed by extract-text-webpack-plugin//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9yZXNvdXJjZXMvc2Nzcy9hcnZlLnNjc3M/MDVmOSJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiQUFBQSIsImZpbGUiOiIuL3Jlc291cmNlcy9zY3NzL2FydmUuc2Nzcy5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8vIHJlbW92ZWQgYnkgZXh0cmFjdC10ZXh0LXdlYnBhY2stcGx1Z2luIl0sInNvdXJjZVJvb3QiOiIifQ==\n//# sourceURL=webpack-internal:///./resources/scss/arve.scss\n");

/***/ }),

/***/ 0:
/*!**************************************************************************************************************************************!*\
  !*** multi ./resources/js/arve.js ./resources/scss/arve.scss ./resources/scss/arve-admin.scss ./resources/common/scss/settings.scss ***!
  \**************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(/*! /uncrypted/websites/symbiosistheme.com/web/app/plugins/advanced-responsive-video-embedder/resources/js/arve.js */"./resources/js/arve.js");
__webpack_require__(/*! /uncrypted/websites/symbiosistheme.com/web/app/plugins/advanced-responsive-video-embedder/resources/scss/arve.scss */"./resources/scss/arve.scss");
__webpack_require__(/*! /uncrypted/websites/symbiosistheme.com/web/app/plugins/advanced-responsive-video-embedder/resources/scss/arve-admin.scss */"./resources/scss/arve-admin.scss");
module.exports = __webpack_require__(/*! /uncrypted/websites/symbiosistheme.com/web/app/plugins/advanced-responsive-video-embedder/resources/common/scss/settings.scss */"./resources/common/scss/settings.scss");


/***/ })

/******/ });