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

/***/ "./resources/js/arve-shortcode-ui.js":
/*!*******************************************!*\
  !*** ./resources/js/arve-shortcode-ui.js ***!
  \*******************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("var _ = window._;\nvar domParser = new DOMParser();\n\nfunction arveExtractURL(changed, collection, shortcode) {\n  function attrByName(name) {\n    return _.find(collection, function (viewModel) {\n      return name === viewModel.model.get('attr');\n    });\n  }\n\n  var val = changed.value;\n  var urlInput = null;\n  var arInput = null;\n  urlInput = attrByName('url').$el.find('input');\n  arInput = attrByName('aspect_ratio').$el.find('input');\n\n  if (typeof val === 'undefined') {\n    return;\n  } // <iframe src=\"https://example.com\" width=\"640\" height=\"360\"></iframe>\n\n\n  var $iframe = domParser.parseFromString(val, 'text/html').querySelector('iframe');\n\n  if ($iframe && $iframe.hasAttribute('src') && $iframe.getAttribute('src')) {\n    urlInput.val($iframe.src).trigger('input');\n    var w = $iframe.width;\n    var h = $iframe.height;\n\n    if (w && h) {\n      arInput.val(w + ':' + h).trigger('input');\n    }\n  }\n}\n\nwindow.wp.shortcake.hooks.addAction('arve.url', arveExtractURL);//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9yZXNvdXJjZXMvanMvYXJ2ZS1zaG9ydGNvZGUtdWkuanM/OGJjNSJdLCJuYW1lcyI6WyJfIiwid2luZG93IiwiZG9tUGFyc2VyIiwiRE9NUGFyc2VyIiwiYXJ2ZUV4dHJhY3RVUkwiLCJjaGFuZ2VkIiwiY29sbGVjdGlvbiIsInNob3J0Y29kZSIsImF0dHJCeU5hbWUiLCJuYW1lIiwiZmluZCIsInZpZXdNb2RlbCIsIm1vZGVsIiwiZ2V0IiwidmFsIiwidmFsdWUiLCJ1cmxJbnB1dCIsImFySW5wdXQiLCIkZWwiLCIkaWZyYW1lIiwicGFyc2VGcm9tU3RyaW5nIiwicXVlcnlTZWxlY3RvciIsImhhc0F0dHJpYnV0ZSIsImdldEF0dHJpYnV0ZSIsInNyYyIsInRyaWdnZXIiLCJ3Iiwid2lkdGgiLCJoIiwiaGVpZ2h0Iiwid3AiLCJzaG9ydGNha2UiLCJob29rcyIsImFkZEFjdGlvbiJdLCJtYXBwaW5ncyI6IkFBQUEsSUFBTUEsQ0FBQyxHQUFHQyxNQUFNLENBQUNELENBQWpCO0FBQ0EsSUFBTUUsU0FBUyxHQUFHLElBQUlDLFNBQUosRUFBbEI7O0FBRUEsU0FBU0MsY0FBVCxDQUF5QkMsT0FBekIsRUFBa0NDLFVBQWxDLEVBQThDQyxTQUE5QyxFQUEwRDtBQUV6RCxXQUFTQyxVQUFULENBQXFCQyxJQUFyQixFQUE0QjtBQUMzQixXQUFPVCxDQUFDLENBQUNVLElBQUYsQ0FDTkosVUFETSxFQUVOLFVBQVVLLFNBQVYsRUFBc0I7QUFDckIsYUFBT0YsSUFBSSxLQUFLRSxTQUFTLENBQUNDLEtBQVYsQ0FBZ0JDLEdBQWhCLENBQXFCLE1BQXJCLENBQWhCO0FBQ0EsS0FKSyxDQUFQO0FBTUE7O0FBRUQsTUFBTUMsR0FBRyxHQUFNVCxPQUFPLENBQUNVLEtBQXZCO0FBQ0EsTUFBSUMsUUFBUSxHQUFHLElBQWY7QUFDQSxNQUFJQyxPQUFPLEdBQUksSUFBZjtBQUNBRCxVQUFRLEdBQU9SLFVBQVUsQ0FBRSxLQUFGLENBQVYsQ0FBb0JVLEdBQXBCLENBQXdCUixJQUF4QixDQUE4QixPQUE5QixDQUFmO0FBQ0FPLFNBQU8sR0FBUVQsVUFBVSxDQUFFLGNBQUYsQ0FBVixDQUE2QlUsR0FBN0IsQ0FBaUNSLElBQWpDLENBQXVDLE9BQXZDLENBQWY7O0FBRUEsTUFBSyxPQUFPSSxHQUFQLEtBQWUsV0FBcEIsRUFBa0M7QUFDakM7QUFDQSxHQW5Cd0QsQ0FxQnpEOzs7QUFFQSxNQUFNSyxPQUFPLEdBQUdqQixTQUFTLENBQ3ZCa0IsZUFEYyxDQUNHTixHQURILEVBQ1EsV0FEUixFQUVkTyxhQUZjLENBRUMsUUFGRCxDQUFoQjs7QUFJQSxNQUFLRixPQUFPLElBQ1hBLE9BQU8sQ0FBQ0csWUFBUixDQUFzQixLQUF0QixDQURJLElBRUpILE9BQU8sQ0FBQ0ksWUFBUixDQUFzQixLQUF0QixDQUZELEVBR0U7QUFDRFAsWUFBUSxDQUFDRixHQUFULENBQWNLLE9BQU8sQ0FBQ0ssR0FBdEIsRUFBNEJDLE9BQTVCLENBQXFDLE9BQXJDO0FBRUEsUUFBTUMsQ0FBQyxHQUFHUCxPQUFPLENBQUNRLEtBQWxCO0FBQ0EsUUFBTUMsQ0FBQyxHQUFHVCxPQUFPLENBQUNVLE1BQWxCOztBQUVBLFFBQUtILENBQUMsSUFBSUUsQ0FBVixFQUFjO0FBQ2JYLGFBQU8sQ0FBQ0gsR0FBUixDQUFhWSxDQUFDLEdBQUcsR0FBSixHQUFVRSxDQUF2QixFQUEyQkgsT0FBM0IsQ0FBb0MsT0FBcEM7QUFDQTtBQUNEO0FBQ0Q7O0FBRUR4QixNQUFNLENBQUM2QixFQUFQLENBQVVDLFNBQVYsQ0FBb0JDLEtBQXBCLENBQTBCQyxTQUExQixDQUFxQyxVQUFyQyxFQUFpRDdCLGNBQWpEIiwiZmlsZSI6Ii4vcmVzb3VyY2VzL2pzL2FydmUtc2hvcnRjb2RlLXVpLmpzLmpzIiwic291cmNlc0NvbnRlbnQiOlsiY29uc3QgXyA9IHdpbmRvdy5fO1xuY29uc3QgZG9tUGFyc2VyID0gbmV3IERPTVBhcnNlcigpO1xuXG5mdW5jdGlvbiBhcnZlRXh0cmFjdFVSTCggY2hhbmdlZCwgY29sbGVjdGlvbiwgc2hvcnRjb2RlICkge1xuXG5cdGZ1bmN0aW9uIGF0dHJCeU5hbWUoIG5hbWUgKSB7XG5cdFx0cmV0dXJuIF8uZmluZChcblx0XHRcdGNvbGxlY3Rpb24sXG5cdFx0XHRmdW5jdGlvbiggdmlld01vZGVsICkge1xuXHRcdFx0XHRyZXR1cm4gbmFtZSA9PT0gdmlld01vZGVsLm1vZGVsLmdldCggJ2F0dHInICk7XG5cdFx0XHR9XG5cdFx0KTtcblx0fVxuXG5cdGNvbnN0IHZhbCAgICA9IGNoYW5nZWQudmFsdWU7XG5cdGxldCB1cmxJbnB1dCA9IG51bGw7XG5cdGxldCBhcklucHV0ICA9IG51bGw7XG5cdHVybElucHV0ICAgICA9IGF0dHJCeU5hbWUoICd1cmwnICkuJGVsLmZpbmQoICdpbnB1dCcgKTtcblx0YXJJbnB1dCAgICAgID0gYXR0ckJ5TmFtZSggJ2FzcGVjdF9yYXRpbycgKS4kZWwuZmluZCggJ2lucHV0JyApO1xuXG5cdGlmICggdHlwZW9mIHZhbCA9PT0gJ3VuZGVmaW5lZCcgKSB7XG5cdFx0cmV0dXJuO1xuXHR9XG5cblx0Ly8gPGlmcmFtZSBzcmM9XCJodHRwczovL2V4YW1wbGUuY29tXCIgd2lkdGg9XCI2NDBcIiBoZWlnaHQ9XCIzNjBcIj48L2lmcmFtZT5cblxuXHRjb25zdCAkaWZyYW1lID0gZG9tUGFyc2VyXG5cdFx0LnBhcnNlRnJvbVN0cmluZyggdmFsLCAndGV4dC9odG1sJyApXG5cdFx0LnF1ZXJ5U2VsZWN0b3IoICdpZnJhbWUnICk7XG5cblx0aWYgKCAkaWZyYW1lICYmXG5cdFx0JGlmcmFtZS5oYXNBdHRyaWJ1dGUoICdzcmMnICkgJiZcblx0XHQkaWZyYW1lLmdldEF0dHJpYnV0ZSggJ3NyYycgKVxuXHQpIHtcblx0XHR1cmxJbnB1dC52YWwoICRpZnJhbWUuc3JjICkudHJpZ2dlciggJ2lucHV0JyApO1xuXG5cdFx0Y29uc3QgdyA9ICRpZnJhbWUud2lkdGg7XG5cdFx0Y29uc3QgaCA9ICRpZnJhbWUuaGVpZ2h0O1xuXG5cdFx0aWYgKCB3ICYmIGggKSB7XG5cdFx0XHRhcklucHV0LnZhbCggdyArICc6JyArIGggKS50cmlnZ2VyKCAnaW5wdXQnICk7XG5cdFx0fVxuXHR9XG59XG5cbndpbmRvdy53cC5zaG9ydGNha2UuaG9va3MuYWRkQWN0aW9uKCAnYXJ2ZS51cmwnLCBhcnZlRXh0cmFjdFVSTCApO1xuIl0sInNvdXJjZVJvb3QiOiIifQ==\n//# sourceURL=webpack-internal:///./resources/js/arve-shortcode-ui.js\n");

/***/ }),

/***/ 4:
/*!*************************************************!*\
  !*** multi ./resources/js/arve-shortcode-ui.js ***!
  \*************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! /uncrypted/websites/symbiosistheme.com/web/app/plugins/advanced-responsive-video-embedder/resources/js/arve-shortcode-ui.js */"./resources/js/arve-shortcode-ui.js");


/***/ })

/******/ });