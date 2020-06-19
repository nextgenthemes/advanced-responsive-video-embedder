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

/***/ "./src/common/ts/notice-ajax.ts":
/*!**************************************!*\
  !*** ./src/common/ts/notice-ajax.ts ***!
  \**************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
eval("\nexports.__esModule = true;\nvar closeBtn = document.querySelector('[data-nextgenthemes-notice-id] .notice-dismiss');\nif (closeBtn) {\n    closeBtn.addEventListener('click', dismiss, false);\n}\nfunction dismiss() {\n    var id = this.closest('[data-nextgenthemes-notice-id]').getAttribute('data-nextgenthemes-notice-id');\n    window.jQuery.ajax({\n        url: window.ajaxurl,\n        data: {\n            action: id,\n        },\n    });\n}\n//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9zcmMvY29tbW9uL3RzL25vdGljZS1hamF4LnRzP2Q2NDciXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6Ijs7QUFRQSxJQUFNLFFBQVEsR0FBRyxRQUFRLENBQUMsYUFBYSxDQUFDLGdEQUFnRCxDQUFDLENBQUM7QUFFMUYsSUFBSSxRQUFRLEVBQUU7SUFDYixRQUFRLENBQUMsZ0JBQWdCLENBQUMsT0FBTyxFQUFFLE9BQU8sRUFBRSxLQUFLLENBQUMsQ0FBQztDQUNuRDtBQUVELFNBQVMsT0FBTztJQUNmLElBQU0sRUFBRSxHQUFHLElBQUksQ0FBQyxPQUFPLENBQUMsZ0NBQWdDLENBQUMsQ0FBQyxZQUFZLENBQ3JFLDhCQUE4QixDQUM5QixDQUFDO0lBRUYsTUFBTSxDQUFDLE1BQU0sQ0FBQyxJQUFJLENBQUM7UUFDbEIsR0FBRyxFQUFFLE1BQU0sQ0FBQyxPQUFPO1FBQ25CLElBQUksRUFBRTtZQUNMLE1BQU0sRUFBRSxFQUFFO1NBQ1Y7S0FDRCxDQUFDLENBQUM7QUFpQkosQ0FBQyIsImZpbGUiOiIuL3NyYy9jb21tb24vdHMvbm90aWNlLWFqYXgudHMuanMiLCJzb3VyY2VzQ29udGVudCI6WyJleHBvcnQge307XG5kZWNsYXJlIGdsb2JhbCB7XG5cdGludGVyZmFjZSBXaW5kb3cge1xuXHRcdGpRdWVyeTtcblx0XHRhamF4dXJsO1xuXHR9XG59XG5cbmNvbnN0IGNsb3NlQnRuID0gZG9jdW1lbnQucXVlcnlTZWxlY3RvcignW2RhdGEtbmV4dGdlbnRoZW1lcy1ub3RpY2UtaWRdIC5ub3RpY2UtZGlzbWlzcycpO1xuXG5pZiAoY2xvc2VCdG4pIHtcblx0Y2xvc2VCdG4uYWRkRXZlbnRMaXN0ZW5lcignY2xpY2snLCBkaXNtaXNzLCBmYWxzZSk7XG59XG5cbmZ1bmN0aW9uIGRpc21pc3MoKSB7XG5cdGNvbnN0IGlkID0gdGhpcy5jbG9zZXN0KCdbZGF0YS1uZXh0Z2VudGhlbWVzLW5vdGljZS1pZF0nKS5nZXRBdHRyaWJ1dGUoXG5cdFx0J2RhdGEtbmV4dGdlbnRoZW1lcy1ub3RpY2UtaWQnXG5cdCk7XG5cblx0d2luZG93LmpRdWVyeS5hamF4KHtcblx0XHR1cmw6IHdpbmRvdy5hamF4dXJsLFxuXHRcdGRhdGE6IHtcblx0XHRcdGFjdGlvbjogaWQsXG5cdFx0fSxcblx0fSk7XG5cblx0Lypcblx0eGhyID0gbmV3IFhNTEh0dHBSZXF1ZXN0KCk7XG5cblx0eGhyLm9wZW4oICdQT1NUJywgYWpheHVybCApO1xuXHR4aHIuc2V0UmVxdWVzdEhlYWRlciggJ0NvbnRlbnQtVHlwZScsICdhcHBsaWNhdGlvbi94LXd3dy1mb3JtLXVybGVuY29kZWQnICk7XG5cdHhoci5vbmxvYWQgPSBmdW5jdGlvbigpIHtcblx0XHRpZiAoIHhoci5zdGF0dXMgPT09IDIwMCAmJiB4aHIucmVzcG9uc2VUZXh0ICE9PSBuZXdOYW1lICkge1xuXHRcdFx0YWxlcnQoJ1NvbWV0aGluZyB3ZW50IHdyb25nLiAgTmFtZSBpcyBub3cgJyArIHhoci5yZXNwb25zZVRleHQpO1xuXHRcdH1cblx0XHRlbHNlIGlmICh4aHIuc3RhdHVzICE9PSAyMDApIHtcblx0XHRcdGFsZXJ0KCdSZXF1ZXN0IGZhaWxlZC4gIFJldHVybmVkIHN0YXR1cyBvZiAnICsgeGhyLnN0YXR1cyk7XG5cdFx0fVxuXHR9O1xuXHR4aHIuc2VuZChlbmNvZGVVUkkoJ25hbWU9JyArIG5ld05hbWUpKTtcblx0Ki9cbn1cbiJdLCJzb3VyY2VSb290IjoiIn0=\n//# sourceURL=webpack-internal:///./src/common/ts/notice-ajax.ts\n");

/***/ }),

/***/ 6:
/*!********************************************!*\
  !*** multi ./src/common/ts/notice-ajax.ts ***!
  \********************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! /uncrypted/websites/symbiosistheme.com/web/app/plugins/advanced-responsive-video-embedder/src/common/ts/notice-ajax.ts */"./src/common/ts/notice-ajax.ts");


/***/ })

/******/ });