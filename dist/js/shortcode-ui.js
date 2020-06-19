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
/******/ 	return __webpack_require__(__webpack_require__.s = 4);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/ts/shortcode-ui.ts":
/*!********************************!*\
  !*** ./src/ts/shortcode-ui.ts ***!
  \********************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
eval("\nexports.__esModule = true;\nvar _ = window._;\nvar domParser = new DOMParser();\nfunction arveExtractURL(changed, collection, shortcode) {\n    function attrByName(name) {\n        return _.find(collection, function (viewModel) {\n            return name === viewModel.model.get('attr');\n        });\n    }\n    var val = changed.value;\n    var urlInput = attrByName('url').$el.find('input');\n    var arInput = attrByName('aspect_ratio').$el.find('input');\n    if (typeof val === 'undefined') {\n        return;\n    }\n    var $iframe = domParser.parseFromString(val, 'text/html').querySelector('iframe');\n    if ($iframe && $iframe.hasAttribute('src')) {\n        urlInput.val($iframe.src).trigger('input');\n        var w = $iframe.width;\n        var h = $iframe.height;\n        if (w && h) {\n            arInput.val(w + ':' + h).trigger('input');\n        }\n    }\n}\nwindow.wp.shortcake.hooks.addAction('arve.url', arveExtractURL);\n//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9zcmMvdHMvc2hvcnRjb2RlLXVpLnRzPzkxZjkiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6Ijs7QUFPQSxJQUFNLENBQUMsR0FBRyxNQUFNLENBQUMsQ0FBQyxDQUFDO0FBQ25CLElBQU0sU0FBUyxHQUFHLElBQUksU0FBUyxFQUFFLENBQUM7QUFFbEMsU0FBUyxjQUFjLENBQUMsT0FBTyxFQUFFLFVBQVUsRUFBRSxTQUFTO0lBQ3JELFNBQVMsVUFBVSxDQUFDLElBQUk7UUFDdkIsT0FBTyxDQUFDLENBQUMsSUFBSSxDQUFDLFVBQVUsRUFBRSxVQUFVLFNBQVM7WUFDNUMsT0FBTyxJQUFJLEtBQUssU0FBUyxDQUFDLEtBQUssQ0FBQyxHQUFHLENBQUMsTUFBTSxDQUFDLENBQUM7UUFDN0MsQ0FBQyxDQUFDLENBQUM7SUFDSixDQUFDO0lBRUQsSUFBTSxHQUFHLEdBQUcsT0FBTyxDQUFDLEtBQUssQ0FBQztJQU0xQixJQUFNLFFBQVEsR0FBRyxVQUFVLENBQUMsS0FBSyxDQUFDLENBQUMsR0FBRyxDQUFDLElBQUksQ0FBQyxPQUFPLENBQUMsQ0FBQztJQUNyRCxJQUFNLE9BQU8sR0FBRyxVQUFVLENBQUMsY0FBYyxDQUFDLENBQUMsR0FBRyxDQUFDLElBQUksQ0FBQyxPQUFPLENBQUMsQ0FBQztJQUU3RCxJQUFJLE9BQU8sR0FBRyxLQUFLLFdBQVcsRUFBRTtRQUMvQixPQUFPO0tBQ1A7SUFJRCxJQUFNLE9BQU8sR0FBRyxTQUFTLENBQUMsZUFBZSxDQUFDLEdBQUcsRUFBRSxXQUFXLENBQUMsQ0FBQyxhQUFhLENBQUMsUUFBUSxDQUFDLENBQUM7SUFFcEYsSUFBSSxPQUFPLElBQUksT0FBTyxDQUFDLFlBQVksQ0FBQyxLQUFLLENBQUMsRUFBRTtRQUMzQyxRQUFRLENBQUMsR0FBRyxDQUFDLE9BQU8sQ0FBQyxHQUFHLENBQUMsQ0FBQyxPQUFPLENBQUMsT0FBTyxDQUFDLENBQUM7UUFFM0MsSUFBTSxDQUFDLEdBQUcsT0FBTyxDQUFDLEtBQUssQ0FBQztRQUN4QixJQUFNLENBQUMsR0FBRyxPQUFPLENBQUMsTUFBTSxDQUFDO1FBRXpCLElBQUksQ0FBQyxJQUFJLENBQUMsRUFBRTtZQUNYLE9BQU8sQ0FBQyxHQUFHLENBQUMsQ0FBQyxHQUFHLEdBQUcsR0FBRyxDQUFDLENBQUMsQ0FBQyxPQUFPLENBQUMsT0FBTyxDQUFDLENBQUM7U0FDMUM7S0FDRDtBQUNGLENBQUM7QUFFRCxNQUFNLENBQUMsRUFBRSxDQUFDLFNBQVMsQ0FBQyxLQUFLLENBQUMsU0FBUyxDQUFDLFVBQVUsRUFBRSxjQUFjLENBQUMsQ0FBQyIsImZpbGUiOiIuL3NyYy90cy9zaG9ydGNvZGUtdWkudHMuanMiLCJzb3VyY2VzQ29udGVudCI6WyJleHBvcnQge307XG5kZWNsYXJlIGdsb2JhbCB7XG5cdGludGVyZmFjZSBXaW5kb3cge1xuXHRcdF87XG5cdH1cbn1cblxuY29uc3QgXyA9IHdpbmRvdy5fO1xuY29uc3QgZG9tUGFyc2VyID0gbmV3IERPTVBhcnNlcigpO1xuXG5mdW5jdGlvbiBhcnZlRXh0cmFjdFVSTChjaGFuZ2VkLCBjb2xsZWN0aW9uLCBzaG9ydGNvZGUpIHtcblx0ZnVuY3Rpb24gYXR0ckJ5TmFtZShuYW1lKSB7XG5cdFx0cmV0dXJuIF8uZmluZChjb2xsZWN0aW9uLCBmdW5jdGlvbiAodmlld01vZGVsKSB7XG5cdFx0XHRyZXR1cm4gbmFtZSA9PT0gdmlld01vZGVsLm1vZGVsLmdldCgnYXR0cicpO1xuXHRcdH0pO1xuXHR9XG5cblx0Y29uc3QgdmFsID0gY2hhbmdlZC52YWx1ZTtcblx0Ly8gbGV0IHVybElucHV0ID0gbnVsbDtcblx0Ly8gbGV0IGFySW5wdXQgPSBudWxsO1xuXHQvLyB1cmxJbnB1dCA9IGF0dHJCeU5hbWUoJ3VybCcpLiRlbC5maW5kKCdpbnB1dCcpO1xuXHQvLyBhcklucHV0ID0gYXR0ckJ5TmFtZSgnYXNwZWN0X3JhdGlvJykuJGVsLmZpbmQoJ2lucHV0Jyk7XG5cblx0Y29uc3QgdXJsSW5wdXQgPSBhdHRyQnlOYW1lKCd1cmwnKS4kZWwuZmluZCgnaW5wdXQnKTtcblx0Y29uc3QgYXJJbnB1dCA9IGF0dHJCeU5hbWUoJ2FzcGVjdF9yYXRpbycpLiRlbC5maW5kKCdpbnB1dCcpO1xuXG5cdGlmICh0eXBlb2YgdmFsID09PSAndW5kZWZpbmVkJykge1xuXHRcdHJldHVybjtcblx0fVxuXG5cdC8vIDxpZnJhbWUgc3JjPVwiaHR0cHM6Ly9leGFtcGxlLmNvbVwiIHdpZHRoPVwiNjQwXCIgaGVpZ2h0PVwiMzYwXCI+PC9pZnJhbWU+XG5cblx0Y29uc3QgJGlmcmFtZSA9IGRvbVBhcnNlci5wYXJzZUZyb21TdHJpbmcodmFsLCAndGV4dC9odG1sJykucXVlcnlTZWxlY3RvcignaWZyYW1lJyk7XG5cblx0aWYgKCRpZnJhbWUgJiYgJGlmcmFtZS5oYXNBdHRyaWJ1dGUoJ3NyYycpKSB7XG5cdFx0dXJsSW5wdXQudmFsKCRpZnJhbWUuc3JjKS50cmlnZ2VyKCdpbnB1dCcpO1xuXG5cdFx0Y29uc3QgdyA9ICRpZnJhbWUud2lkdGg7XG5cdFx0Y29uc3QgaCA9ICRpZnJhbWUuaGVpZ2h0O1xuXG5cdFx0aWYgKHcgJiYgaCkge1xuXHRcdFx0YXJJbnB1dC52YWwodyArICc6JyArIGgpLnRyaWdnZXIoJ2lucHV0Jyk7XG5cdFx0fVxuXHR9XG59XG5cbndpbmRvdy53cC5zaG9ydGNha2UuaG9va3MuYWRkQWN0aW9uKCdhcnZlLnVybCcsIGFydmVFeHRyYWN0VVJMKTtcbiJdLCJzb3VyY2VSb290IjoiIn0=\n//# sourceURL=webpack-internal:///./src/ts/shortcode-ui.ts\n");

/***/ }),

/***/ 4:
/*!**************************************!*\
  !*** multi ./src/ts/shortcode-ui.ts ***!
  \**************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! /uncrypted/websites/symbiosistheme.com/web/app/plugins/advanced-responsive-video-embedder/src/ts/shortcode-ui.ts */"./src/ts/shortcode-ui.ts");


/***/ })

/******/ });