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

/***/ "./resources/js/arve.js":
/*!******************************!*\
  !*** ./resources/js/arve.js ***!
  \******************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("var qs = document.querySelector.bind(document);\nvar qsa = document.querySelectorAll.bind(document);\n\nfunction unwrap(el) {\n  // get the element's parent node\n  var parent = el.parentNode; // move all children out of the element\n\n  while (el.firstChild) {\n    parent.insertBefore(el.firstChild, el);\n  } // remove the empty element\n\n\n  parent.removeChild(el);\n}\n\nfunction removeUnwantedStuff() {\n  qsa('.arve p, .arve .video-wrap, .arve .fluid-width-video-wrapper, .arve .fluid-vids').forEach(function (el) {\n    unwrap(el);\n  });\n  qsa('.arve br').forEach(function (el) {\n    el.remove();\n  });\n  qsa('.arve-iframe, .arve-video').forEach(function (el) {\n    el.removeAttribute('width');\n    el.removeAttribute('height');\n    el.removeAttribute('style');\n  });\n  qsa('.wp-block-embed').forEach(function (el) {\n    if (el.querySelector('.arve')) {\n      var $WRAPPER = el.querySelector('.wp-block-embed__wrapper');\n      el.classList.remove(['wp-embed-aspect-16-9', 'wp-has-aspect-ratio']);\n\n      if ($WRAPPER) {\n        unwrap($WRAPPER);\n      }\n    }\n  });\n}\n\nfunction globalID() {\n  if (qs('html[id=\"arve\"]')) {\n    return;\n  }\n\n  if (null === qs('html[id]')) {\n    qs('html').setAttribute('id', 'arve');\n  } else if (null === qs('body[id]')) {\n    document.body.setAttribute('id', 'arve');\n  } else {\n    var $WRAP = document.createElement('div');\n    $WRAP.setAttribute('id', 'arve');\n\n    while (document.body.firstChild) {\n      $WRAP.append(document.body.firstChild);\n    }\n\n    document.body.append($WRAP);\n  }\n}\n\nremoveUnwantedStuff();\nglobalID();\ndocument.addEventListener('DOMContentLoaded', function () {\n  removeUnwantedStuff();\n  globalID();\n});//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9yZXNvdXJjZXMvanMvYXJ2ZS5qcz9kNzFlIl0sIm5hbWVzIjpbInFzIiwiZG9jdW1lbnQiLCJxdWVyeVNlbGVjdG9yIiwiYmluZCIsInFzYSIsInF1ZXJ5U2VsZWN0b3JBbGwiLCJ1bndyYXAiLCJlbCIsInBhcmVudCIsInBhcmVudE5vZGUiLCJmaXJzdENoaWxkIiwiaW5zZXJ0QmVmb3JlIiwicmVtb3ZlQ2hpbGQiLCJyZW1vdmVVbndhbnRlZFN0dWZmIiwiZm9yRWFjaCIsInJlbW92ZSIsInJlbW92ZUF0dHJpYnV0ZSIsIiRXUkFQUEVSIiwiY2xhc3NMaXN0IiwiZ2xvYmFsSUQiLCJzZXRBdHRyaWJ1dGUiLCJib2R5IiwiJFdSQVAiLCJjcmVhdGVFbGVtZW50IiwiYXBwZW5kIiwiYWRkRXZlbnRMaXN0ZW5lciJdLCJtYXBwaW5ncyI6IkFBQUEsSUFBTUEsRUFBRSxHQUFJQyxRQUFRLENBQUNDLGFBQVQsQ0FBdUJDLElBQXZCLENBQTZCRixRQUE3QixDQUFaO0FBQ0EsSUFBTUcsR0FBRyxHQUFHSCxRQUFRLENBQUNJLGdCQUFULENBQTBCRixJQUExQixDQUFnQ0YsUUFBaEMsQ0FBWjs7QUFFQSxTQUFTSyxNQUFULENBQWlCQyxFQUFqQixFQUFzQjtBQUNyQjtBQUNBLE1BQU1DLE1BQU0sR0FBR0QsRUFBRSxDQUFDRSxVQUFsQixDQUZxQixDQUdyQjs7QUFDQSxTQUFRRixFQUFFLENBQUNHLFVBQVgsRUFBd0I7QUFDdkJGLFVBQU0sQ0FBQ0csWUFBUCxDQUFxQkosRUFBRSxDQUFDRyxVQUF4QixFQUFvQ0gsRUFBcEM7QUFDQSxHQU5vQixDQU9yQjs7O0FBQ0FDLFFBQU0sQ0FBQ0ksV0FBUCxDQUFvQkwsRUFBcEI7QUFDQTs7QUFFRCxTQUFTTSxtQkFBVCxHQUErQjtBQUM5QlQsS0FBRyxDQUFFLGlGQUFGLENBQUgsQ0FBeUZVLE9BQXpGLENBQWtHLFVBQUVQLEVBQUYsRUFBVTtBQUMzR0QsVUFBTSxDQUFFQyxFQUFGLENBQU47QUFDQSxHQUZEO0FBSUFILEtBQUcsQ0FBRSxVQUFGLENBQUgsQ0FBa0JVLE9BQWxCLENBQTJCLFVBQUVQLEVBQUYsRUFBVTtBQUNwQ0EsTUFBRSxDQUFDUSxNQUFIO0FBQ0EsR0FGRDtBQUlBWCxLQUFHLENBQUUsMkJBQUYsQ0FBSCxDQUFtQ1UsT0FBbkMsQ0FBNEMsVUFBRVAsRUFBRixFQUFVO0FBQ3JEQSxNQUFFLENBQUNTLGVBQUgsQ0FBb0IsT0FBcEI7QUFDQVQsTUFBRSxDQUFDUyxlQUFILENBQW9CLFFBQXBCO0FBQ0FULE1BQUUsQ0FBQ1MsZUFBSCxDQUFvQixPQUFwQjtBQUNBLEdBSkQ7QUFNQVosS0FBRyxDQUFFLGlCQUFGLENBQUgsQ0FBeUJVLE9BQXpCLENBQWtDLFVBQUVQLEVBQUYsRUFBVTtBQUMzQyxRQUFLQSxFQUFFLENBQUNMLGFBQUgsQ0FBa0IsT0FBbEIsQ0FBTCxFQUFtQztBQUNsQyxVQUFNZSxRQUFRLEdBQUdWLEVBQUUsQ0FBQ0wsYUFBSCxDQUFrQiwwQkFBbEIsQ0FBakI7QUFDQUssUUFBRSxDQUFDVyxTQUFILENBQWFILE1BQWIsQ0FBcUIsQ0FBRSxzQkFBRixFQUEwQixxQkFBMUIsQ0FBckI7O0FBRUEsVUFBS0UsUUFBTCxFQUFnQjtBQUNmWCxjQUFNLENBQUVXLFFBQUYsQ0FBTjtBQUNBO0FBQ0Q7QUFDRCxHQVREO0FBVUE7O0FBRUQsU0FBU0UsUUFBVCxHQUFvQjtBQUNuQixNQUFLbkIsRUFBRSxDQUFFLGlCQUFGLENBQVAsRUFBK0I7QUFDOUI7QUFDQTs7QUFFRCxNQUFLLFNBQVNBLEVBQUUsQ0FBRSxVQUFGLENBQWhCLEVBQWlDO0FBQ2hDQSxNQUFFLENBQUUsTUFBRixDQUFGLENBQWFvQixZQUFiLENBQTJCLElBQTNCLEVBQWlDLE1BQWpDO0FBQ0EsR0FGRCxNQUVPLElBQUssU0FBU3BCLEVBQUUsQ0FBRSxVQUFGLENBQWhCLEVBQWlDO0FBQ3ZDQyxZQUFRLENBQUNvQixJQUFULENBQWNELFlBQWQsQ0FBNEIsSUFBNUIsRUFBa0MsTUFBbEM7QUFDQSxHQUZNLE1BRUE7QUFDTixRQUFNRSxLQUFLLEdBQUdyQixRQUFRLENBQUNzQixhQUFULENBQXdCLEtBQXhCLENBQWQ7QUFDQUQsU0FBSyxDQUFDRixZQUFOLENBQW9CLElBQXBCLEVBQTBCLE1BQTFCOztBQUNBLFdBQVFuQixRQUFRLENBQUNvQixJQUFULENBQWNYLFVBQXRCLEVBQW1DO0FBQ2xDWSxXQUFLLENBQUNFLE1BQU4sQ0FBY3ZCLFFBQVEsQ0FBQ29CLElBQVQsQ0FBY1gsVUFBNUI7QUFDQTs7QUFDRFQsWUFBUSxDQUFDb0IsSUFBVCxDQUFjRyxNQUFkLENBQXNCRixLQUF0QjtBQUNBO0FBQ0Q7O0FBRURULG1CQUFtQjtBQUNuQk0sUUFBUTtBQUVSbEIsUUFBUSxDQUFDd0IsZ0JBQVQsQ0FBMkIsa0JBQTNCLEVBQStDLFlBQU07QUFDcERaLHFCQUFtQjtBQUNuQk0sVUFBUTtBQUNSLENBSEQiLCJmaWxlIjoiLi9yZXNvdXJjZXMvanMvYXJ2ZS5qcy5qcyIsInNvdXJjZXNDb250ZW50IjpbImNvbnN0IHFzICA9IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IuYmluZCggZG9jdW1lbnQgKTtcbmNvbnN0IHFzYSA9IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3JBbGwuYmluZCggZG9jdW1lbnQgKTtcblxuZnVuY3Rpb24gdW53cmFwKCBlbCApIHtcblx0Ly8gZ2V0IHRoZSBlbGVtZW50J3MgcGFyZW50IG5vZGVcblx0Y29uc3QgcGFyZW50ID0gZWwucGFyZW50Tm9kZTtcblx0Ly8gbW92ZSBhbGwgY2hpbGRyZW4gb3V0IG9mIHRoZSBlbGVtZW50XG5cdHdoaWxlICggZWwuZmlyc3RDaGlsZCApIHtcblx0XHRwYXJlbnQuaW5zZXJ0QmVmb3JlKCBlbC5maXJzdENoaWxkLCBlbCApO1xuXHR9XG5cdC8vIHJlbW92ZSB0aGUgZW1wdHkgZWxlbWVudFxuXHRwYXJlbnQucmVtb3ZlQ2hpbGQoIGVsICk7XG59XG5cbmZ1bmN0aW9uIHJlbW92ZVVud2FudGVkU3R1ZmYoKSB7XG5cdHFzYSggJy5hcnZlIHAsIC5hcnZlIC52aWRlby13cmFwLCAuYXJ2ZSAuZmx1aWQtd2lkdGgtdmlkZW8td3JhcHBlciwgLmFydmUgLmZsdWlkLXZpZHMnICkuZm9yRWFjaCggKCBlbCApID0+IHtcblx0XHR1bndyYXAoIGVsICk7XG5cdH0gKTtcblxuXHRxc2EoICcuYXJ2ZSBicicgKS5mb3JFYWNoKCAoIGVsICkgPT4ge1xuXHRcdGVsLnJlbW92ZSgpO1xuXHR9ICk7XG5cblx0cXNhKCAnLmFydmUtaWZyYW1lLCAuYXJ2ZS12aWRlbycgKS5mb3JFYWNoKCAoIGVsICkgPT4ge1xuXHRcdGVsLnJlbW92ZUF0dHJpYnV0ZSggJ3dpZHRoJyApO1xuXHRcdGVsLnJlbW92ZUF0dHJpYnV0ZSggJ2hlaWdodCcgKTtcblx0XHRlbC5yZW1vdmVBdHRyaWJ1dGUoICdzdHlsZScgKTtcblx0fSApO1xuXG5cdHFzYSggJy53cC1ibG9jay1lbWJlZCcgKS5mb3JFYWNoKCAoIGVsICkgPT4ge1xuXHRcdGlmICggZWwucXVlcnlTZWxlY3RvciggJy5hcnZlJyApICkge1xuXHRcdFx0Y29uc3QgJFdSQVBQRVIgPSBlbC5xdWVyeVNlbGVjdG9yKCAnLndwLWJsb2NrLWVtYmVkX193cmFwcGVyJyApO1xuXHRcdFx0ZWwuY2xhc3NMaXN0LnJlbW92ZSggWyAnd3AtZW1iZWQtYXNwZWN0LTE2LTknLCAnd3AtaGFzLWFzcGVjdC1yYXRpbycgXSApO1xuXG5cdFx0XHRpZiAoICRXUkFQUEVSICkge1xuXHRcdFx0XHR1bndyYXAoICRXUkFQUEVSICk7XG5cdFx0XHR9XG5cdFx0fVxuXHR9ICk7XG59XG5cbmZ1bmN0aW9uIGdsb2JhbElEKCkge1xuXHRpZiAoIHFzKCAnaHRtbFtpZD1cImFydmVcIl0nICkgKSB7XG5cdFx0cmV0dXJuO1xuXHR9XG5cblx0aWYgKCBudWxsID09PSBxcyggJ2h0bWxbaWRdJyApICkge1xuXHRcdHFzKCAnaHRtbCcgKS5zZXRBdHRyaWJ1dGUoICdpZCcsICdhcnZlJyApO1xuXHR9IGVsc2UgaWYgKCBudWxsID09PSBxcyggJ2JvZHlbaWRdJyApICkge1xuXHRcdGRvY3VtZW50LmJvZHkuc2V0QXR0cmlidXRlKCAnaWQnLCAnYXJ2ZScgKTtcblx0fSBlbHNlIHtcblx0XHRjb25zdCAkV1JBUCA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoICdkaXYnICk7XG5cdFx0JFdSQVAuc2V0QXR0cmlidXRlKCAnaWQnLCAnYXJ2ZScgKTtcblx0XHR3aGlsZSAoIGRvY3VtZW50LmJvZHkuZmlyc3RDaGlsZCApIHtcblx0XHRcdCRXUkFQLmFwcGVuZCggZG9jdW1lbnQuYm9keS5maXJzdENoaWxkICk7XG5cdFx0fVxuXHRcdGRvY3VtZW50LmJvZHkuYXBwZW5kKCAkV1JBUCApO1xuXHR9XG59XG5cbnJlbW92ZVVud2FudGVkU3R1ZmYoKTtcbmdsb2JhbElEKCk7XG5cbmRvY3VtZW50LmFkZEV2ZW50TGlzdGVuZXIoICdET01Db250ZW50TG9hZGVkJywgKCkgPT4ge1xuXHRyZW1vdmVVbndhbnRlZFN0dWZmKCk7XG5cdGdsb2JhbElEKCk7XG59ICk7XG4iXSwic291cmNlUm9vdCI6IiJ9\n//# sourceURL=webpack-internal:///./resources/js/arve.js\n");

/***/ }),

/***/ "./resources/scss/arve-admin.scss":
/*!****************************************!*\
  !*** ./resources/scss/arve-admin.scss ***!
  \****************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("// removed by extract-text-webpack-plugin//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9yZXNvdXJjZXMvc2Nzcy9hcnZlLWFkbWluLnNjc3M/YWE3MCJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiQUFBQSIsImZpbGUiOiIuL3Jlc291cmNlcy9zY3NzL2FydmUtYWRtaW4uc2Nzcy5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8vIHJlbW92ZWQgYnkgZXh0cmFjdC10ZXh0LXdlYnBhY2stcGx1Z2luIl0sInNvdXJjZVJvb3QiOiIifQ==\n//# sourceURL=webpack-internal:///./resources/scss/arve-admin.scss\n");

/***/ }),

/***/ "./resources/scss/arve.scss":
/*!**********************************!*\
  !*** ./resources/scss/arve.scss ***!
  \**********************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("// removed by extract-text-webpack-plugin//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9yZXNvdXJjZXMvc2Nzcy9hcnZlLnNjc3M/Zjg0YiJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiQUFBQSIsImZpbGUiOiIuL3Jlc291cmNlcy9zY3NzL2FydmUuc2Nzcy5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8vIHJlbW92ZWQgYnkgZXh0cmFjdC10ZXh0LXdlYnBhY2stcGx1Z2luIl0sInNvdXJjZVJvb3QiOiIifQ==\n//# sourceURL=webpack-internal:///./resources/scss/arve.scss\n");

/***/ }),

/***/ 0:
/*!************************************************************************************************!*\
  !*** multi ./resources/js/arve.js ./resources/scss/arve.scss ./resources/scss/arve-admin.scss ***!
  \************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(/*! /uncrypted/websites/symbiosistheme.com/web/app/plugins/advanced-responsive-video-embedder/resources/js/arve.js */"./resources/js/arve.js");
__webpack_require__(/*! /uncrypted/websites/symbiosistheme.com/web/app/plugins/advanced-responsive-video-embedder/resources/scss/arve.scss */"./resources/scss/arve.scss");
module.exports = __webpack_require__(/*! /uncrypted/websites/symbiosistheme.com/web/app/plugins/advanced-responsive-video-embedder/resources/scss/arve-admin.scss */"./resources/scss/arve-admin.scss");


/***/ })

/******/ });