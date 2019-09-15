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
/******/ 	return __webpack_require__(__webpack_require__.s = 6);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/common/js/notice-ajax.js":
/*!********************************************!*\
  !*** ./resources/common/js/notice-ajax.js ***!
  \********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("/* global jQuery, ajaxurl */\n(function ($) {\n  'use strict';\n\n  $(document).on('click', '[data-nextgenthemes-notice-id] .notice-dismiss', function () {\n    var id = $(this).closest('[data-nextgenthemes-notice-id]').attr('data-nextgenthemes-notice-id');\n    $.ajax({\n      url: ajaxurl,\n      data: {\n        action: id\n      }\n    });\n  });\n})(jQuery);//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9yZXNvdXJjZXMvY29tbW9uL2pzL25vdGljZS1hamF4LmpzP2IyOTYiXSwibmFtZXMiOlsiJCIsImRvY3VtZW50Iiwib24iLCJpZCIsImNsb3Nlc3QiLCJhdHRyIiwiYWpheCIsInVybCIsImFqYXh1cmwiLCJkYXRhIiwiYWN0aW9uIiwialF1ZXJ5Il0sIm1hcHBpbmdzIjoiQUFBQTtBQUNFLFdBQVVBLENBQVYsRUFBYztBQUNmOztBQUVBQSxHQUFDLENBQUVDLFFBQUYsQ0FBRCxDQUFjQyxFQUFkLENBQWtCLE9BQWxCLEVBQTJCLGdEQUEzQixFQUE2RSxZQUFXO0FBQ3ZGLFFBQU1DLEVBQUUsR0FBR0gsQ0FBQyxDQUFFLElBQUYsQ0FBRCxDQUFVSSxPQUFWLENBQW1CLGdDQUFuQixFQUFzREMsSUFBdEQsQ0FBNEQsOEJBQTVELENBQVg7QUFFQUwsS0FBQyxDQUFDTSxJQUFGLENBQVE7QUFDUEMsU0FBRyxFQUFFQyxPQURFO0FBRVBDLFVBQUksRUFBRTtBQUNMQyxjQUFNLEVBQUVQO0FBREg7QUFGQyxLQUFSO0FBTUEsR0FURDtBQVVBLENBYkMsRUFhQ1EsTUFiRCxDQUFGIiwiZmlsZSI6Ii4vcmVzb3VyY2VzL2NvbW1vbi9qcy9ub3RpY2UtYWpheC5qcy5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIGdsb2JhbCBqUXVlcnksIGFqYXh1cmwgKi9cbiggZnVuY3Rpb24oICQgKSB7XG5cdCd1c2Ugc3RyaWN0JztcblxuXHQkKCBkb2N1bWVudCApLm9uKCAnY2xpY2snLCAnW2RhdGEtbmV4dGdlbnRoZW1lcy1ub3RpY2UtaWRdIC5ub3RpY2UtZGlzbWlzcycsIGZ1bmN0aW9uKCkge1xuXHRcdGNvbnN0IGlkID0gJCggdGhpcyApLmNsb3Nlc3QoICdbZGF0YS1uZXh0Z2VudGhlbWVzLW5vdGljZS1pZF0nICkuYXR0ciggJ2RhdGEtbmV4dGdlbnRoZW1lcy1ub3RpY2UtaWQnICk7XG5cblx0XHQkLmFqYXgoIHtcblx0XHRcdHVybDogYWpheHVybCxcblx0XHRcdGRhdGE6IHtcblx0XHRcdFx0YWN0aW9uOiBpZCxcblx0XHRcdH0sXG5cdFx0fSApO1xuXHR9ICk7XG59KCBqUXVlcnkgKSApO1xuIl0sInNvdXJjZVJvb3QiOiIifQ==\n//# sourceURL=webpack-internal:///./resources/common/js/notice-ajax.js\n");

/***/ }),

/***/ 6:
/*!**************************************************!*\
  !*** multi ./resources/common/js/notice-ajax.js ***!
  \**************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! /uncrypted/websites/symbiosistheme.com/web/app/plugins/advanced-responsive-video-embedder/resources/common/js/notice-ajax.js */"./resources/common/js/notice-ajax.js");


/***/ })

/******/ });