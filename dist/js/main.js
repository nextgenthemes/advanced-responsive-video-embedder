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

/***/ "./src/common/scss/settings.scss":
/*!***************************************!*\
  !*** ./src/common/scss/settings.scss ***!
  \***************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("// removed by extract-text-webpack-plugin//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9zcmMvY29tbW9uL3Njc3Mvc2V0dGluZ3Muc2Nzcz85Y2E0Il0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiJBQUFBIiwiZmlsZSI6Ii4vc3JjL2NvbW1vbi9zY3NzL3NldHRpbmdzLnNjc3MuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvLyByZW1vdmVkIGJ5IGV4dHJhY3QtdGV4dC13ZWJwYWNrLXBsdWdpbiJdLCJzb3VyY2VSb290IjoiIn0=\n//# sourceURL=webpack-internal:///./src/common/scss/settings.scss\n");

/***/ }),

/***/ "./src/scss/admin.scss":
/*!*****************************!*\
  !*** ./src/scss/admin.scss ***!
  \*****************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("// removed by extract-text-webpack-plugin//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9zcmMvc2Nzcy9hZG1pbi5zY3NzPzU3ZDAiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IkFBQUEiLCJmaWxlIjoiLi9zcmMvc2Nzcy9hZG1pbi5zY3NzLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLy8gcmVtb3ZlZCBieSBleHRyYWN0LXRleHQtd2VicGFjay1wbHVnaW4iXSwic291cmNlUm9vdCI6IiJ9\n//# sourceURL=webpack-internal:///./src/scss/admin.scss\n");

/***/ }),

/***/ "./src/scss/main.scss":
/*!****************************!*\
  !*** ./src/scss/main.scss ***!
  \****************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("// removed by extract-text-webpack-plugin//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9zcmMvc2Nzcy9tYWluLnNjc3M/NzE1OSJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiQUFBQSIsImZpbGUiOiIuL3NyYy9zY3NzL21haW4uc2Nzcy5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8vIHJlbW92ZWQgYnkgZXh0cmFjdC10ZXh0LXdlYnBhY2stcGx1Z2luIl0sInNvdXJjZVJvb3QiOiIifQ==\n//# sourceURL=webpack-internal:///./src/scss/main.scss\n");

/***/ }),

/***/ "./src/ts/main.ts":
/*!************************!*\
  !*** ./src/ts/main.ts ***!
  \************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("var qsa = document.querySelectorAll.bind(document);\nremoveUnwantedStuff();\nglobalID();\ndocument.addEventListener('DOMContentLoaded', function () {\n    removeUnwantedStuff();\n    globalID();\n});\nfunction removeUnwantedStuff() {\n    qsa('.arve p, .arve .video-wrap, .arve .fluid-width-video-wrapper, .arve .fluid-vids').forEach(function (el) {\n        unwrap(el);\n    });\n    qsa('.arve br').forEach(function (el) {\n        el.remove();\n    });\n    qsa('.arve-iframe, .arve-video').forEach(function (el) {\n        el.removeAttribute('width');\n        el.removeAttribute('height');\n        el.removeAttribute('style');\n    });\n    qsa('.wp-block-embed').forEach(function (el) {\n        if (el.querySelector('.arve')) {\n            el.classList.remove('wp-embed-aspect-16-9', 'wp-has-aspect-ratio');\n            var wrapper = el.querySelector('.wp-block-embed__wrapper');\n            if (wrapper) {\n                unwrap(wrapper);\n            }\n        }\n    });\n}\nfunction globalID() {\n    if ('html' === document.documentElement.id) {\n        return;\n    }\n    if (!document.documentElement.id) {\n        document.documentElement.id = 'html';\n    }\n    else if (!document.body.id) {\n        document.body.id = 'html';\n    }\n}\nfunction unwrap(el) {\n    var parent = el.parentNode;\n    while (el.firstChild) {\n        parent.insertBefore(el.firstChild, el);\n    }\n    parent.removeChild(el);\n}\n//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9zcmMvdHMvbWFpbi50cz82NGY0Il0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiJBQUFBLElBQU0sR0FBRyxHQUFHLFFBQVEsQ0FBQyxnQkFBZ0IsQ0FBQyxJQUFJLENBQUMsUUFBUSxDQUFxQyxDQUFDO0FBRXpGLG1CQUFtQixFQUFFLENBQUM7QUFDdEIsUUFBUSxFQUFFLENBQUM7QUFFWCxRQUFRLENBQUMsZ0JBQWdCLENBQUMsa0JBQWtCLEVBQUU7SUFDN0MsbUJBQW1CLEVBQUUsQ0FBQztJQUN0QixRQUFRLEVBQUUsQ0FBQztBQUNaLENBQUMsQ0FBQyxDQUFDO0FBRUgsU0FBUyxtQkFBbUI7SUFDM0IsR0FBRyxDQUFDLGlGQUFpRixDQUFDLENBQUMsT0FBTyxDQUM3RixVQUFDLEVBQUU7UUFDRixNQUFNLENBQUMsRUFBRSxDQUFDLENBQUM7SUFDWixDQUFDLENBQ0QsQ0FBQztJQUVGLEdBQUcsQ0FBQyxVQUFVLENBQUMsQ0FBQyxPQUFPLENBQUMsVUFBQyxFQUFFO1FBQzFCLEVBQUUsQ0FBQyxNQUFNLEVBQUUsQ0FBQztJQUNiLENBQUMsQ0FBQyxDQUFDO0lBRUgsR0FBRyxDQUFDLDJCQUEyQixDQUFDLENBQUMsT0FBTyxDQUFDLFVBQUMsRUFBRTtRQUMzQyxFQUFFLENBQUMsZUFBZSxDQUFDLE9BQU8sQ0FBQyxDQUFDO1FBQzVCLEVBQUUsQ0FBQyxlQUFlLENBQUMsUUFBUSxDQUFDLENBQUM7UUFDN0IsRUFBRSxDQUFDLGVBQWUsQ0FBQyxPQUFPLENBQUMsQ0FBQztJQUM3QixDQUFDLENBQUMsQ0FBQztJQUVILEdBQUcsQ0FBQyxpQkFBaUIsQ0FBQyxDQUFDLE9BQU8sQ0FBQyxVQUFDLEVBQUU7UUFDakMsSUFBSSxFQUFFLENBQUMsYUFBYSxDQUFDLE9BQU8sQ0FBQyxFQUFFO1lBQzlCLEVBQUUsQ0FBQyxTQUFTLENBQUMsTUFBTSxDQUFDLHNCQUFzQixFQUFFLHFCQUFxQixDQUFDLENBQUM7WUFFbkUsSUFBTSxPQUFPLEdBQUcsRUFBRSxDQUFDLGFBQWEsQ0FBQywwQkFBMEIsQ0FBQyxDQUFDO1lBRTdELElBQUksT0FBTyxFQUFFO2dCQUNaLE1BQU0sQ0FBQyxPQUFPLENBQUMsQ0FBQzthQUNoQjtTQUNEO0lBQ0YsQ0FBQyxDQUFDLENBQUM7QUFDSixDQUFDO0FBRUQsU0FBUyxRQUFRO0lBRWhCLElBQUksTUFBTSxLQUFLLFFBQVEsQ0FBQyxlQUFlLENBQUMsRUFBRSxFQUFFO1FBQzNDLE9BQU87S0FDUDtJQUVELElBQUksQ0FBQyxRQUFRLENBQUMsZUFBZSxDQUFDLEVBQUUsRUFBRTtRQUNqQyxRQUFRLENBQUMsZUFBZSxDQUFDLEVBQUUsR0FBRyxNQUFNLENBQUM7S0FDckM7U0FBTSxJQUFJLENBQUMsUUFBUSxDQUFDLElBQUksQ0FBQyxFQUFFLEVBQUU7UUFDN0IsUUFBUSxDQUFDLElBQUksQ0FBQyxFQUFFLEdBQUcsTUFBTSxDQUFDO0tBQzFCO0FBQ0YsQ0FBQztBQUVELFNBQVMsTUFBTSxDQUFDLEVBQUU7SUFFakIsSUFBTSxNQUFNLEdBQUcsRUFBRSxDQUFDLFVBQVUsQ0FBQztJQUU3QixPQUFPLEVBQUUsQ0FBQyxVQUFVLEVBQUU7UUFDckIsTUFBTSxDQUFDLFlBQVksQ0FBQyxFQUFFLENBQUMsVUFBVSxFQUFFLEVBQUUsQ0FBQyxDQUFDO0tBQ3ZDO0lBRUQsTUFBTSxDQUFDLFdBQVcsQ0FBQyxFQUFFLENBQUMsQ0FBQztBQUN4QixDQUFDIiwiZmlsZSI6Ii4vc3JjL3RzL21haW4udHMuanMiLCJzb3VyY2VzQ29udGVudCI6WyJjb25zdCBxc2EgPSBkb2N1bWVudC5xdWVyeVNlbGVjdG9yQWxsLmJpbmQoZG9jdW1lbnQpIGFzIHR5cGVvZiBkb2N1bWVudC5xdWVyeVNlbGVjdG9yQWxsO1xuXG5yZW1vdmVVbndhbnRlZFN0dWZmKCk7XG5nbG9iYWxJRCgpO1xuXG5kb2N1bWVudC5hZGRFdmVudExpc3RlbmVyKCdET01Db250ZW50TG9hZGVkJywgKCkgPT4ge1xuXHRyZW1vdmVVbndhbnRlZFN0dWZmKCk7XG5cdGdsb2JhbElEKCk7XG59KTtcblxuZnVuY3Rpb24gcmVtb3ZlVW53YW50ZWRTdHVmZigpIHtcblx0cXNhKCcuYXJ2ZSBwLCAuYXJ2ZSAudmlkZW8td3JhcCwgLmFydmUgLmZsdWlkLXdpZHRoLXZpZGVvLXdyYXBwZXIsIC5hcnZlIC5mbHVpZC12aWRzJykuZm9yRWFjaChcblx0XHQoZWwpID0+IHtcblx0XHRcdHVud3JhcChlbCk7XG5cdFx0fVxuXHQpO1xuXG5cdHFzYSgnLmFydmUgYnInKS5mb3JFYWNoKChlbCkgPT4ge1xuXHRcdGVsLnJlbW92ZSgpO1xuXHR9KTtcblxuXHRxc2EoJy5hcnZlLWlmcmFtZSwgLmFydmUtdmlkZW8nKS5mb3JFYWNoKChlbCkgPT4ge1xuXHRcdGVsLnJlbW92ZUF0dHJpYnV0ZSgnd2lkdGgnKTtcblx0XHRlbC5yZW1vdmVBdHRyaWJ1dGUoJ2hlaWdodCcpO1xuXHRcdGVsLnJlbW92ZUF0dHJpYnV0ZSgnc3R5bGUnKTtcblx0fSk7XG5cblx0cXNhKCcud3AtYmxvY2stZW1iZWQnKS5mb3JFYWNoKChlbCkgPT4ge1xuXHRcdGlmIChlbC5xdWVyeVNlbGVjdG9yKCcuYXJ2ZScpKSB7XG5cdFx0XHRlbC5jbGFzc0xpc3QucmVtb3ZlKCd3cC1lbWJlZC1hc3BlY3QtMTYtOScsICd3cC1oYXMtYXNwZWN0LXJhdGlvJyk7XG5cblx0XHRcdGNvbnN0IHdyYXBwZXIgPSBlbC5xdWVyeVNlbGVjdG9yKCcud3AtYmxvY2stZW1iZWRfX3dyYXBwZXInKTtcblxuXHRcdFx0aWYgKHdyYXBwZXIpIHtcblx0XHRcdFx0dW53cmFwKHdyYXBwZXIpO1xuXHRcdFx0fVxuXHRcdH1cblx0fSk7XG59XG5cbmZ1bmN0aW9uIGdsb2JhbElEKCkge1xuXHQvLyBVc3VhbGx5IHRoZSBpZCBzaG91bGQgYmUgYWxyZWFkeSB0aGVyZSBhZGRlZCB3aXRoIHBocCB1c2luZyB0aGUgbGFuZ3VhZ2VfYXR0cmlidXRlcyBmaWx0ZXJcblx0aWYgKCdodG1sJyA9PT0gZG9jdW1lbnQuZG9jdW1lbnRFbGVtZW50LmlkKSB7XG5cdFx0cmV0dXJuO1xuXHR9XG5cblx0aWYgKCFkb2N1bWVudC5kb2N1bWVudEVsZW1lbnQuaWQpIHtcblx0XHRkb2N1bWVudC5kb2N1bWVudEVsZW1lbnQuaWQgPSAnaHRtbCc7XG5cdH0gZWxzZSBpZiAoIWRvY3VtZW50LmJvZHkuaWQpIHtcblx0XHRkb2N1bWVudC5ib2R5LmlkID0gJ2h0bWwnO1xuXHR9XG59XG5cbmZ1bmN0aW9uIHVud3JhcChlbCkge1xuXHQvLyBnZXQgdGhlIGVsZW1lbnQncyBwYXJlbnQgbm9kZVxuXHRjb25zdCBwYXJlbnQgPSBlbC5wYXJlbnROb2RlO1xuXHQvLyBtb3ZlIGFsbCBjaGlsZHJlbiBvdXQgb2YgdGhlIGVsZW1lbnRcblx0d2hpbGUgKGVsLmZpcnN0Q2hpbGQpIHtcblx0XHRwYXJlbnQuaW5zZXJ0QmVmb3JlKGVsLmZpcnN0Q2hpbGQsIGVsKTtcblx0fVxuXHQvLyByZW1vdmUgdGhlIGVtcHR5IGVsZW1lbnRcblx0cGFyZW50LnJlbW92ZUNoaWxkKGVsKTtcbn1cbiJdLCJzb3VyY2VSb290IjoiIn0=\n//# sourceURL=webpack-internal:///./src/ts/main.ts\n");

/***/ }),

/***/ 0:
/*!*********************************************************************************************************!*\
  !*** multi ./src/ts/main.ts ./src/scss/main.scss ./src/scss/admin.scss ./src/common/scss/settings.scss ***!
  \*********************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(/*! /uncrypted/websites/symbiosistheme.com/web/app/plugins/advanced-responsive-video-embedder/src/ts/main.ts */"./src/ts/main.ts");
__webpack_require__(/*! /uncrypted/websites/symbiosistheme.com/web/app/plugins/advanced-responsive-video-embedder/src/scss/main.scss */"./src/scss/main.scss");
__webpack_require__(/*! /uncrypted/websites/symbiosistheme.com/web/app/plugins/advanced-responsive-video-embedder/src/scss/admin.scss */"./src/scss/admin.scss");
module.exports = __webpack_require__(/*! /uncrypted/websites/symbiosistheme.com/web/app/plugins/advanced-responsive-video-embedder/src/common/scss/settings.scss */"./src/common/scss/settings.scss");


/***/ })

/******/ });