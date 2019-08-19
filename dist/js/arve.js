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

eval("var qs = document.querySelector.bind(document);\nvar qsa = document.querySelectorAll.bind(document);\n\nfunction removeUnwantedStuff() {\n  qsa('.arve p, .arve .video-wrap, .arve .fluid-width-video-wrapper, .arve .fluid-vids').forEach(function (el) {\n    var parent = el.parentNode; // move all children out of the element\n\n    while (el.firstChild) {\n      parent.insertBefore(el.firstChild, el);\n    } // remove the empty element\n\n\n    parent.removeChild(el);\n  });\n  qsa('.arve br').forEach(function (el) {\n    el.remove();\n  });\n  qsa('.arve-iframe, .arve-video').forEach(function (el) {\n    el.removeAttribute('width');\n    el.removeAttribute('height');\n    el.removeAttribute('style');\n  });\n  qsa('.wp-block-embed').forEach(function (el) {\n    if (el.querySelector('.arve')) {\n      var $WRAPPER = el.querySelector('.wp-block-embed__wrapper');\n      el.classList.remove(['wp-embed-aspect-16-9', 'wp-has-aspect-ratio']);\n\n      if ($WRAPPER) {\n        $WRAPPER.contents().unwrap();\n      }\n    }\n  });\n}\n\nfunction globalID() {\n  if (qs('html[id=\"arve\"]')) {\n    return;\n  }\n\n  if (null === qs('html[id]')) {\n    qs('html').setAttribute('id', 'arve');\n  } else if (null === qs('body[id]')) {\n    document.body.setAttribute('id', 'arve');\n  } else {\n    var $WRAP = document.createElement('div');\n    $WRAP.setAttribute('id', 'arve');\n\n    while (document.body.firstChild) {\n      $WRAP.append(document.body.firstChild);\n    }\n\n    document.body.append($WRAP);\n  }\n}\n\nremoveUnwantedStuff();\nglobalID();\ndocument.addEventListener('DOMContentLoaded', function () {\n  removeUnwantedStuff();\n  globalID();\n});//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9yZXNvdXJjZXMvanMvYXJ2ZS5qcz9kNzFlIl0sIm5hbWVzIjpbInFzIiwiZG9jdW1lbnQiLCJxdWVyeVNlbGVjdG9yIiwiYmluZCIsInFzYSIsInF1ZXJ5U2VsZWN0b3JBbGwiLCJyZW1vdmVVbndhbnRlZFN0dWZmIiwiZm9yRWFjaCIsImVsIiwicGFyZW50IiwicGFyZW50Tm9kZSIsImZpcnN0Q2hpbGQiLCJpbnNlcnRCZWZvcmUiLCJyZW1vdmVDaGlsZCIsInJlbW92ZSIsInJlbW92ZUF0dHJpYnV0ZSIsIiRXUkFQUEVSIiwiY2xhc3NMaXN0IiwiY29udGVudHMiLCJ1bndyYXAiLCJnbG9iYWxJRCIsInNldEF0dHJpYnV0ZSIsImJvZHkiLCIkV1JBUCIsImNyZWF0ZUVsZW1lbnQiLCJhcHBlbmQiLCJhZGRFdmVudExpc3RlbmVyIl0sIm1hcHBpbmdzIjoiQUFBQSxJQUFNQSxFQUFFLEdBQUlDLFFBQVEsQ0FBQ0MsYUFBVCxDQUF1QkMsSUFBdkIsQ0FBNkJGLFFBQTdCLENBQVo7QUFDQSxJQUFNRyxHQUFHLEdBQUdILFFBQVEsQ0FBQ0ksZ0JBQVQsQ0FBMEJGLElBQTFCLENBQWdDRixRQUFoQyxDQUFaOztBQUVBLFNBQVNLLG1CQUFULEdBQStCO0FBQzlCRixLQUFHLENBQUUsaUZBQUYsQ0FBSCxDQUF5RkcsT0FBekYsQ0FBa0csVUFBRUMsRUFBRixFQUFVO0FBQzNHLFFBQU1DLE1BQU0sR0FBR0QsRUFBRSxDQUFDRSxVQUFsQixDQUQyRyxDQUczRzs7QUFDQSxXQUFRRixFQUFFLENBQUNHLFVBQVgsRUFBd0I7QUFDdkJGLFlBQU0sQ0FBQ0csWUFBUCxDQUFxQkosRUFBRSxDQUFDRyxVQUF4QixFQUFvQ0gsRUFBcEM7QUFDQSxLQU4wRyxDQVEzRzs7O0FBQ0FDLFVBQU0sQ0FBQ0ksV0FBUCxDQUFvQkwsRUFBcEI7QUFDQSxHQVZEO0FBWUFKLEtBQUcsQ0FBRSxVQUFGLENBQUgsQ0FBa0JHLE9BQWxCLENBQTJCLFVBQUVDLEVBQUYsRUFBVTtBQUNwQ0EsTUFBRSxDQUFDTSxNQUFIO0FBQ0EsR0FGRDtBQUlBVixLQUFHLENBQUUsMkJBQUYsQ0FBSCxDQUFtQ0csT0FBbkMsQ0FBNEMsVUFBRUMsRUFBRixFQUFVO0FBQ3JEQSxNQUFFLENBQUNPLGVBQUgsQ0FBb0IsT0FBcEI7QUFDQVAsTUFBRSxDQUFDTyxlQUFILENBQW9CLFFBQXBCO0FBQ0FQLE1BQUUsQ0FBQ08sZUFBSCxDQUFvQixPQUFwQjtBQUNBLEdBSkQ7QUFNQVgsS0FBRyxDQUFFLGlCQUFGLENBQUgsQ0FBeUJHLE9BQXpCLENBQWtDLFVBQUVDLEVBQUYsRUFBVTtBQUMzQyxRQUFLQSxFQUFFLENBQUNOLGFBQUgsQ0FBa0IsT0FBbEIsQ0FBTCxFQUFtQztBQUNsQyxVQUFNYyxRQUFRLEdBQUdSLEVBQUUsQ0FBQ04sYUFBSCxDQUFrQiwwQkFBbEIsQ0FBakI7QUFDQU0sUUFBRSxDQUFDUyxTQUFILENBQWFILE1BQWIsQ0FBcUIsQ0FBRSxzQkFBRixFQUEwQixxQkFBMUIsQ0FBckI7O0FBRUEsVUFBS0UsUUFBTCxFQUFnQjtBQUNmQSxnQkFBUSxDQUFDRSxRQUFULEdBQW9CQyxNQUFwQjtBQUNBO0FBQ0Q7QUFDRCxHQVREO0FBVUE7O0FBRUQsU0FBU0MsUUFBVCxHQUFvQjtBQUNuQixNQUFLcEIsRUFBRSxDQUFFLGlCQUFGLENBQVAsRUFBK0I7QUFDOUI7QUFDQTs7QUFFRCxNQUFLLFNBQVNBLEVBQUUsQ0FBRSxVQUFGLENBQWhCLEVBQWlDO0FBQ2hDQSxNQUFFLENBQUUsTUFBRixDQUFGLENBQWFxQixZQUFiLENBQTJCLElBQTNCLEVBQWlDLE1BQWpDO0FBQ0EsR0FGRCxNQUVPLElBQUssU0FBU3JCLEVBQUUsQ0FBRSxVQUFGLENBQWhCLEVBQWlDO0FBQ3ZDQyxZQUFRLENBQUNxQixJQUFULENBQWNELFlBQWQsQ0FBNEIsSUFBNUIsRUFBa0MsTUFBbEM7QUFDQSxHQUZNLE1BRUE7QUFDTixRQUFNRSxLQUFLLEdBQUd0QixRQUFRLENBQUN1QixhQUFULENBQXdCLEtBQXhCLENBQWQ7QUFDQUQsU0FBSyxDQUFDRixZQUFOLENBQW9CLElBQXBCLEVBQTBCLE1BQTFCOztBQUNBLFdBQVFwQixRQUFRLENBQUNxQixJQUFULENBQWNYLFVBQXRCLEVBQW1DO0FBQ2xDWSxXQUFLLENBQUNFLE1BQU4sQ0FBY3hCLFFBQVEsQ0FBQ3FCLElBQVQsQ0FBY1gsVUFBNUI7QUFDQTs7QUFDRFYsWUFBUSxDQUFDcUIsSUFBVCxDQUFjRyxNQUFkLENBQXNCRixLQUF0QjtBQUNBO0FBQ0Q7O0FBRURqQixtQkFBbUI7QUFDbkJjLFFBQVE7QUFFUm5CLFFBQVEsQ0FBQ3lCLGdCQUFULENBQTJCLGtCQUEzQixFQUErQyxZQUFNO0FBQ3BEcEIscUJBQW1CO0FBQ25CYyxVQUFRO0FBQ1IsQ0FIRCIsImZpbGUiOiIuL3Jlc291cmNlcy9qcy9hcnZlLmpzLmpzIiwic291cmNlc0NvbnRlbnQiOlsiY29uc3QgcXMgID0gZG9jdW1lbnQucXVlcnlTZWxlY3Rvci5iaW5kKCBkb2N1bWVudCApO1xuY29uc3QgcXNhID0gZG9jdW1lbnQucXVlcnlTZWxlY3RvckFsbC5iaW5kKCBkb2N1bWVudCApO1xuXG5mdW5jdGlvbiByZW1vdmVVbndhbnRlZFN0dWZmKCkge1xuXHRxc2EoICcuYXJ2ZSBwLCAuYXJ2ZSAudmlkZW8td3JhcCwgLmFydmUgLmZsdWlkLXdpZHRoLXZpZGVvLXdyYXBwZXIsIC5hcnZlIC5mbHVpZC12aWRzJyApLmZvckVhY2goICggZWwgKSA9PiB7XG5cdFx0Y29uc3QgcGFyZW50ID0gZWwucGFyZW50Tm9kZTtcblxuXHRcdC8vIG1vdmUgYWxsIGNoaWxkcmVuIG91dCBvZiB0aGUgZWxlbWVudFxuXHRcdHdoaWxlICggZWwuZmlyc3RDaGlsZCApIHtcblx0XHRcdHBhcmVudC5pbnNlcnRCZWZvcmUoIGVsLmZpcnN0Q2hpbGQsIGVsICk7XG5cdFx0fVxuXG5cdFx0Ly8gcmVtb3ZlIHRoZSBlbXB0eSBlbGVtZW50XG5cdFx0cGFyZW50LnJlbW92ZUNoaWxkKCBlbCApO1xuXHR9ICk7XG5cblx0cXNhKCAnLmFydmUgYnInICkuZm9yRWFjaCggKCBlbCApID0+IHtcblx0XHRlbC5yZW1vdmUoKTtcblx0fSApO1xuXG5cdHFzYSggJy5hcnZlLWlmcmFtZSwgLmFydmUtdmlkZW8nICkuZm9yRWFjaCggKCBlbCApID0+IHtcblx0XHRlbC5yZW1vdmVBdHRyaWJ1dGUoICd3aWR0aCcgKTtcblx0XHRlbC5yZW1vdmVBdHRyaWJ1dGUoICdoZWlnaHQnICk7XG5cdFx0ZWwucmVtb3ZlQXR0cmlidXRlKCAnc3R5bGUnICk7XG5cdH0gKTtcblxuXHRxc2EoICcud3AtYmxvY2stZW1iZWQnICkuZm9yRWFjaCggKCBlbCApID0+IHtcblx0XHRpZiAoIGVsLnF1ZXJ5U2VsZWN0b3IoICcuYXJ2ZScgKSApIHtcblx0XHRcdGNvbnN0ICRXUkFQUEVSID0gZWwucXVlcnlTZWxlY3RvciggJy53cC1ibG9jay1lbWJlZF9fd3JhcHBlcicgKTtcblx0XHRcdGVsLmNsYXNzTGlzdC5yZW1vdmUoIFsgJ3dwLWVtYmVkLWFzcGVjdC0xNi05JywgJ3dwLWhhcy1hc3BlY3QtcmF0aW8nIF0gKTtcblxuXHRcdFx0aWYgKCAkV1JBUFBFUiApIHtcblx0XHRcdFx0JFdSQVBQRVIuY29udGVudHMoKS51bndyYXAoKTtcblx0XHRcdH1cblx0XHR9XG5cdH0gKTtcbn1cblxuZnVuY3Rpb24gZ2xvYmFsSUQoKSB7XG5cdGlmICggcXMoICdodG1sW2lkPVwiYXJ2ZVwiXScgKSApIHtcblx0XHRyZXR1cm47XG5cdH1cblxuXHRpZiAoIG51bGwgPT09IHFzKCAnaHRtbFtpZF0nICkgKSB7XG5cdFx0cXMoICdodG1sJyApLnNldEF0dHJpYnV0ZSggJ2lkJywgJ2FydmUnICk7XG5cdH0gZWxzZSBpZiAoIG51bGwgPT09IHFzKCAnYm9keVtpZF0nICkgKSB7XG5cdFx0ZG9jdW1lbnQuYm9keS5zZXRBdHRyaWJ1dGUoICdpZCcsICdhcnZlJyApO1xuXHR9IGVsc2Uge1xuXHRcdGNvbnN0ICRXUkFQID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCggJ2RpdicgKTtcblx0XHQkV1JBUC5zZXRBdHRyaWJ1dGUoICdpZCcsICdhcnZlJyApO1xuXHRcdHdoaWxlICggZG9jdW1lbnQuYm9keS5maXJzdENoaWxkICkge1xuXHRcdFx0JFdSQVAuYXBwZW5kKCBkb2N1bWVudC5ib2R5LmZpcnN0Q2hpbGQgKTtcblx0XHR9XG5cdFx0ZG9jdW1lbnQuYm9keS5hcHBlbmQoICRXUkFQICk7XG5cdH1cbn1cblxucmVtb3ZlVW53YW50ZWRTdHVmZigpO1xuZ2xvYmFsSUQoKTtcblxuZG9jdW1lbnQuYWRkRXZlbnRMaXN0ZW5lciggJ0RPTUNvbnRlbnRMb2FkZWQnLCAoKSA9PiB7XG5cdHJlbW92ZVVud2FudGVkU3R1ZmYoKTtcblx0Z2xvYmFsSUQoKTtcbn0gKTtcbiJdLCJzb3VyY2VSb290IjoiIn0=\n//# sourceURL=webpack-internal:///./resources/js/arve.js\n");

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