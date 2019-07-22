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

eval("(function () {\n  'use strict';\n\n  var qs = document.querySelector.bind(document);\n  var qsa = document.querySelectorAll.bind(document);\n\n  function removeUnwantedStuff() {\n    var _this = this;\n\n    qsa('.arve-wrapper p, .arve-wrapper .video-wrap, .arve-wrapper .fluid-width-video-wrapper, .arve-wrapper .fluid-vids').forEach(function (el) {\n      var parent = el.parentNode; // move all children out of the element\n\n      while (el.firstChild) {\n        parent.insertBefore(el.firstChild, el);\n      } // remove the empty element\n\n\n      parent.removeChild(el);\n    });\n    qsa('.arve-wrapper br').forEach(function (el) {\n      el.remove();\n    });\n    qsa('.arve-iframe, .arve-video').forEach(function (el) {\n      el.removeAttribute('width');\n      el.removeAttribute('height');\n      el.removeAttribute('style');\n    });\n    qsa('.wp-block-embed').forEach(function (el) {\n      if ($(_this).has('.arve-wrapper')) {\n        $(_this).removeClass('wp-embed-aspect-16-9 wp-has-aspect-ratio');\n\n        if ($(_this).has('.wp-block-embed__wrapper')) {\n          $(_this).find('.wp-block-embed__wrapper').contents().unwrap();\n        }\n      }\n    });\n  }\n\n  ;\n\n  function globalID() {\n    if (qs('html[id=\"arve\"]')) {\n      return;\n    }\n\n    if (null === qs('html[id]')) {\n      qs('html').setAttribute('id', 'arve');\n    } else if (null === qs('body[id]')) {\n      document.body.setAttribute('id', 'arve');\n    } else {\n      var wrapper = document.createElement('div');\n      wrapper.setAttribute('id', 'arve');\n\n      while (document.body.firstChild) {\n        wrapper.append(document.body.firstChild);\n      }\n\n      document.body.append(wrapper);\n    }\n  }\n\n  removeUnwantedStuff();\n  globalID();\n  document.addEventListener('DOMContentLoaded', function (event) {\n    removeUnwantedStuff();\n    globalID();\n  });\n})();//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9yZXNvdXJjZXMvanMvYXJ2ZS5qcz9kNzFlIl0sIm5hbWVzIjpbInFzIiwiZG9jdW1lbnQiLCJxdWVyeVNlbGVjdG9yIiwiYmluZCIsInFzYSIsInF1ZXJ5U2VsZWN0b3JBbGwiLCJyZW1vdmVVbndhbnRlZFN0dWZmIiwiZm9yRWFjaCIsImVsIiwicGFyZW50IiwicGFyZW50Tm9kZSIsImZpcnN0Q2hpbGQiLCJpbnNlcnRCZWZvcmUiLCJyZW1vdmVDaGlsZCIsInJlbW92ZSIsInJlbW92ZUF0dHJpYnV0ZSIsIiQiLCJoYXMiLCJyZW1vdmVDbGFzcyIsImZpbmQiLCJjb250ZW50cyIsInVud3JhcCIsImdsb2JhbElEIiwic2V0QXR0cmlidXRlIiwiYm9keSIsIndyYXBwZXIiLCJjcmVhdGVFbGVtZW50IiwiYXBwZW5kIiwiYWRkRXZlbnRMaXN0ZW5lciIsImV2ZW50Il0sIm1hcHBpbmdzIjoiQUFBRSxhQUFXO0FBQ1o7O0FBRUEsTUFBTUEsRUFBRSxHQUFJQyxRQUFRLENBQUNDLGFBQVQsQ0FBdUJDLElBQXZCLENBQTZCRixRQUE3QixDQUFaO0FBQ0EsTUFBTUcsR0FBRyxHQUFHSCxRQUFRLENBQUNJLGdCQUFULENBQTBCRixJQUExQixDQUFnQ0YsUUFBaEMsQ0FBWjs7QUFFQSxXQUFTSyxtQkFBVCxHQUErQjtBQUFBOztBQUU5QkYsT0FBRyxDQUFFLGlIQUFGLENBQUgsQ0FBeUhHLE9BQXpILENBQWtJLFVBQUFDLEVBQUUsRUFBSTtBQUN2SSxVQUFJQyxNQUFNLEdBQUdELEVBQUUsQ0FBQ0UsVUFBaEIsQ0FEdUksQ0FHdkk7O0FBQ0EsYUFBUUYsRUFBRSxDQUFDRyxVQUFYLEVBQXdCO0FBQ3ZCRixjQUFNLENBQUNHLFlBQVAsQ0FBcUJKLEVBQUUsQ0FBQ0csVUFBeEIsRUFBb0NILEVBQXBDO0FBQ0EsT0FOc0ksQ0FRdkk7OztBQUNBQyxZQUFNLENBQUNJLFdBQVAsQ0FBb0JMLEVBQXBCO0FBQ0EsS0FWRDtBQVlBSixPQUFHLENBQUUsa0JBQUYsQ0FBSCxDQUEwQkcsT0FBMUIsQ0FBbUMsVUFBQUMsRUFBRSxFQUFJO0FBQ3hDQSxRQUFFLENBQUNNLE1BQUg7QUFDQSxLQUZEO0FBSUFWLE9BQUcsQ0FBRSwyQkFBRixDQUFILENBQW1DRyxPQUFuQyxDQUE0QyxVQUFBQyxFQUFFLEVBQUk7QUFDakRBLFFBQUUsQ0FBQ08sZUFBSCxDQUFvQixPQUFwQjtBQUNBUCxRQUFFLENBQUNPLGVBQUgsQ0FBb0IsUUFBcEI7QUFDQVAsUUFBRSxDQUFDTyxlQUFILENBQW9CLE9BQXBCO0FBQ0EsS0FKRDtBQU1BWCxPQUFHLENBQUUsaUJBQUYsQ0FBSCxDQUF5QkcsT0FBekIsQ0FBa0MsVUFBQUMsRUFBRSxFQUFJO0FBRXZDLFVBQUtRLENBQUMsQ0FBRSxLQUFGLENBQUQsQ0FBVUMsR0FBVixDQUFlLGVBQWYsQ0FBTCxFQUF3QztBQUV2Q0QsU0FBQyxDQUFFLEtBQUYsQ0FBRCxDQUFVRSxXQUFWLENBQXVCLDBDQUF2Qjs7QUFFQSxZQUFLRixDQUFDLENBQUUsS0FBRixDQUFELENBQVVDLEdBQVYsQ0FBZSwwQkFBZixDQUFMLEVBQW1EO0FBQ2xERCxXQUFDLENBQUUsS0FBRixDQUFELENBQVVHLElBQVYsQ0FBZ0IsMEJBQWhCLEVBQTZDQyxRQUE3QyxHQUF3REMsTUFBeEQ7QUFDQTtBQUNEO0FBQ0QsS0FWRDtBQVdBOztBQUFBOztBQUVELFdBQVNDLFFBQVQsR0FBb0I7QUFFbkIsUUFBS3RCLEVBQUUsQ0FBRSxpQkFBRixDQUFQLEVBQStCO0FBQzlCO0FBQ0E7O0FBRUQsUUFBSyxTQUFTQSxFQUFFLENBQUUsVUFBRixDQUFoQixFQUFpQztBQUNoQ0EsUUFBRSxDQUFFLE1BQUYsQ0FBRixDQUFhdUIsWUFBYixDQUEyQixJQUEzQixFQUFpQyxNQUFqQztBQUNBLEtBRkQsTUFFTyxJQUFLLFNBQVN2QixFQUFFLENBQUUsVUFBRixDQUFoQixFQUFpQztBQUN2Q0MsY0FBUSxDQUFDdUIsSUFBVCxDQUFjRCxZQUFkLENBQTRCLElBQTVCLEVBQWtDLE1BQWxDO0FBQ0EsS0FGTSxNQUVBO0FBQ04sVUFBSUUsT0FBTyxHQUFHeEIsUUFBUSxDQUFDeUIsYUFBVCxDQUF3QixLQUF4QixDQUFkO0FBQ0FELGFBQU8sQ0FBQ0YsWUFBUixDQUFzQixJQUF0QixFQUE0QixNQUE1Qjs7QUFDQSxhQUFRdEIsUUFBUSxDQUFDdUIsSUFBVCxDQUFjYixVQUF0QixFQUFtQztBQUNsQ2MsZUFBTyxDQUFDRSxNQUFSLENBQWdCMUIsUUFBUSxDQUFDdUIsSUFBVCxDQUFjYixVQUE5QjtBQUNBOztBQUNEVixjQUFRLENBQUN1QixJQUFULENBQWNHLE1BQWQsQ0FBc0JGLE9BQXRCO0FBQ0E7QUFDRDs7QUFFRG5CLHFCQUFtQjtBQUNuQmdCLFVBQVE7QUFFUnJCLFVBQVEsQ0FBQzJCLGdCQUFULENBQTJCLGtCQUEzQixFQUErQyxVQUFVQyxLQUFWLEVBQWtCO0FBQ2hFdkIsdUJBQW1CO0FBQ25CZ0IsWUFBUTtBQUNSLEdBSEQ7QUFJQSxDQXRFQyxHQUFGIiwiZmlsZSI6Ii4vcmVzb3VyY2VzL2pzL2FydmUuanMuanMiLCJzb3VyY2VzQ29udGVudCI6WyIoIGZ1bmN0aW9uKCkge1xuXHQndXNlIHN0cmljdCc7XG5cblx0Y29uc3QgcXMgID0gZG9jdW1lbnQucXVlcnlTZWxlY3Rvci5iaW5kKCBkb2N1bWVudCApO1xuXHRjb25zdCBxc2EgPSBkb2N1bWVudC5xdWVyeVNlbGVjdG9yQWxsLmJpbmQoIGRvY3VtZW50ICk7XG5cblx0ZnVuY3Rpb24gcmVtb3ZlVW53YW50ZWRTdHVmZigpIHtcblxuXHRcdHFzYSggJy5hcnZlLXdyYXBwZXIgcCwgLmFydmUtd3JhcHBlciAudmlkZW8td3JhcCwgLmFydmUtd3JhcHBlciAuZmx1aWQtd2lkdGgtdmlkZW8td3JhcHBlciwgLmFydmUtd3JhcHBlciAuZmx1aWQtdmlkcycgKS5mb3JFYWNoKCBlbCA9PiB7XG5cdFx0XHRsZXQgcGFyZW50ID0gZWwucGFyZW50Tm9kZTtcblxuXHRcdFx0Ly8gbW92ZSBhbGwgY2hpbGRyZW4gb3V0IG9mIHRoZSBlbGVtZW50XG5cdFx0XHR3aGlsZSAoIGVsLmZpcnN0Q2hpbGQgKSB7XG5cdFx0XHRcdHBhcmVudC5pbnNlcnRCZWZvcmUoIGVsLmZpcnN0Q2hpbGQsIGVsICk7XG5cdFx0XHR9XG5cblx0XHRcdC8vIHJlbW92ZSB0aGUgZW1wdHkgZWxlbWVudFxuXHRcdFx0cGFyZW50LnJlbW92ZUNoaWxkKCBlbCApO1xuXHRcdH0pO1xuXG5cdFx0cXNhKCAnLmFydmUtd3JhcHBlciBicicgKS5mb3JFYWNoKCBlbCA9PiB7XG5cdFx0XHRlbC5yZW1vdmUoKTtcblx0XHR9KTtcblxuXHRcdHFzYSggJy5hcnZlLWlmcmFtZSwgLmFydmUtdmlkZW8nICkuZm9yRWFjaCggZWwgPT4ge1xuXHRcdFx0ZWwucmVtb3ZlQXR0cmlidXRlKCAnd2lkdGgnICk7XG5cdFx0XHRlbC5yZW1vdmVBdHRyaWJ1dGUoICdoZWlnaHQnICk7XG5cdFx0XHRlbC5yZW1vdmVBdHRyaWJ1dGUoICdzdHlsZScgKTtcblx0XHR9KTtcblxuXHRcdHFzYSggJy53cC1ibG9jay1lbWJlZCcgKS5mb3JFYWNoKCBlbCA9PiB7XG5cblx0XHRcdGlmICggJCggdGhpcyApLmhhcyggJy5hcnZlLXdyYXBwZXInICkgKSB7XG5cblx0XHRcdFx0JCggdGhpcyApLnJlbW92ZUNsYXNzKCAnd3AtZW1iZWQtYXNwZWN0LTE2LTkgd3AtaGFzLWFzcGVjdC1yYXRpbycgKTtcblxuXHRcdFx0XHRpZiAoICQoIHRoaXMgKS5oYXMoICcud3AtYmxvY2stZW1iZWRfX3dyYXBwZXInICkgKSB7XG5cdFx0XHRcdFx0JCggdGhpcyApLmZpbmQoICcud3AtYmxvY2stZW1iZWRfX3dyYXBwZXInICkuY29udGVudHMoKS51bndyYXAoKTtcblx0XHRcdFx0fVxuXHRcdFx0fVxuXHRcdH0pO1xuXHR9O1xuXG5cdGZ1bmN0aW9uIGdsb2JhbElEKCkge1xuXG5cdFx0aWYgKCBxcyggJ2h0bWxbaWQ9XCJhcnZlXCJdJyApICkge1xuXHRcdFx0cmV0dXJuO1xuXHRcdH1cblxuXHRcdGlmICggbnVsbCA9PT0gcXMoICdodG1sW2lkXScgKSApIHtcblx0XHRcdHFzKCAnaHRtbCcgKS5zZXRBdHRyaWJ1dGUoICdpZCcsICdhcnZlJyApO1xuXHRcdH0gZWxzZSBpZiAoIG51bGwgPT09IHFzKCAnYm9keVtpZF0nICkgKSB7XG5cdFx0XHRkb2N1bWVudC5ib2R5LnNldEF0dHJpYnV0ZSggJ2lkJywgJ2FydmUnICk7XG5cdFx0fSBlbHNlIHtcblx0XHRcdGxldCB3cmFwcGVyID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCggJ2RpdicgKTtcblx0XHRcdHdyYXBwZXIuc2V0QXR0cmlidXRlKCAnaWQnLCAnYXJ2ZScgKTtcblx0XHRcdHdoaWxlICggZG9jdW1lbnQuYm9keS5maXJzdENoaWxkICkge1xuXHRcdFx0XHR3cmFwcGVyLmFwcGVuZCggZG9jdW1lbnQuYm9keS5maXJzdENoaWxkICk7XG5cdFx0XHR9XG5cdFx0XHRkb2N1bWVudC5ib2R5LmFwcGVuZCggd3JhcHBlciApO1xuXHRcdH1cblx0fVxuXG5cdHJlbW92ZVVud2FudGVkU3R1ZmYoKTtcblx0Z2xvYmFsSUQoKTtcblxuXHRkb2N1bWVudC5hZGRFdmVudExpc3RlbmVyKCAnRE9NQ29udGVudExvYWRlZCcsIGZ1bmN0aW9uKCBldmVudCApIHtcblx0XHRyZW1vdmVVbndhbnRlZFN0dWZmKCk7XG5cdFx0Z2xvYmFsSUQoKTtcblx0fSk7XG59KCkgKTtcbiJdLCJzb3VyY2VSb290IjoiIn0=\n//# sourceURL=webpack-internal:///./resources/js/arve.js\n");

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