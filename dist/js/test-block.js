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
/******/ 	return __webpack_require__(__webpack_require__.s = 2);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/js/test-block.js":
/*!************************************!*\
  !*** ./resources/js/test-block.js ***!
  \************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("// License: GPLv2+\nvar el = wp.element.createElement,\n    registerBlockType = wp.blocks.registerBlockType,\n    ServerSideRender = wp.components.ServerSideRender,\n    TextControl = wp.components.TextControl,\n    ToggleControl = wp.components.ToggleControl,\n    InspectorControls = wp.editor.InspectorControls;\n/*\n * Here's where we register the block in JavaScript.\n *\n * It's not yet possible to register a block entirely without JavaScript, but\n * that is something I'd love to see happen. This is a barebones example\n * of registering the block, and giving the basic ability to edit the block\n * attributes. (In this case, there's only one attribute, 'foo'.)\n */\n\nregisterBlockType('nextgenthemes/arve-block', {\n  title: 'PHP Block',\n  icon: 'megaphone',\n  category: 'widgets',\n\n  /*\n   * In most other blocks, you'd see an 'attributes' property being defined here.\n   * We've defined attributes in the PHP, that information is automatically sent\n   * to the block editor, so we don't need to redefine it here.\n   */\n  edit: function edit(props) {\n    return [\n    /*\n     * The ServerSideRender element uses the REST API to automatically call\n     * php_block_render() in your PHP code whenever it needs to get an updated\n     * view of the block.\n     */\n    el(ServerSideRender, {\n      block: 'nextgenthemes/arve-block',\n      attributes: props.attributes\n    }),\n    /*\n     * InspectorControls lets you add controls to the Block sidebar. In this case,\n     * we're adding a TextControl, which lets us edit the 'foo' attribute (which\n     * we defined in the PHP). The onChange property is a little bit of magic to tell\n     * the block editor to update the value of our 'foo' property, and to re-render\n     * the block.\n     */\n    el(InspectorControls, {}, el(TextControl, {\n      label: 'Foo',\n      value: props.attributes.foo,\n      onChange: function onChange(value) {\n        props.setAttributes({\n          foo: value\n        });\n      }\n    }), el(ToggleControl, {\n      label: 'Toogle',\n      value: props.attributes.toggle,\n      onChange: function onChange(value) {\n        props.setAttributes({\n          toggle: value\n        });\n      }\n    }))];\n  },\n  // We're going to be rendering in PHP, so save() can just return null.\n  save: function save() {\n    return null;\n  }\n});//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9yZXNvdXJjZXMvanMvdGVzdC1ibG9jay5qcz83NzkzIl0sIm5hbWVzIjpbImVsIiwid3AiLCJlbGVtZW50IiwiY3JlYXRlRWxlbWVudCIsInJlZ2lzdGVyQmxvY2tUeXBlIiwiYmxvY2tzIiwiU2VydmVyU2lkZVJlbmRlciIsImNvbXBvbmVudHMiLCJUZXh0Q29udHJvbCIsIlRvZ2dsZUNvbnRyb2wiLCJJbnNwZWN0b3JDb250cm9scyIsImVkaXRvciIsInRpdGxlIiwiaWNvbiIsImNhdGVnb3J5IiwiZWRpdCIsInByb3BzIiwiYmxvY2siLCJhdHRyaWJ1dGVzIiwibGFiZWwiLCJ2YWx1ZSIsImZvbyIsIm9uQ2hhbmdlIiwic2V0QXR0cmlidXRlcyIsInRvZ2dsZSIsInNhdmUiXSwibWFwcGluZ3MiOiJBQUFBO0FBRUEsSUFBTUEsRUFBRSxHQUFHQyxFQUFFLENBQUNDLE9BQUgsQ0FBV0MsYUFBdEI7QUFBQSxJQUNDQyxpQkFBaUIsR0FBR0gsRUFBRSxDQUFDSSxNQUFILENBQVVELGlCQUQvQjtBQUFBLElBRUNFLGdCQUFnQixHQUFHTCxFQUFFLENBQUNNLFVBQUgsQ0FBY0QsZ0JBRmxDO0FBQUEsSUFHQ0UsV0FBVyxHQUFHUCxFQUFFLENBQUNNLFVBQUgsQ0FBY0MsV0FIN0I7QUFBQSxJQUlDQyxhQUFhLEdBQUdSLEVBQUUsQ0FBQ00sVUFBSCxDQUFjRSxhQUovQjtBQUFBLElBS0NDLGlCQUFpQixHQUFHVCxFQUFFLENBQUNVLE1BQUgsQ0FBVUQsaUJBTC9CO0FBT0E7Ozs7Ozs7OztBQVFBTixpQkFBaUIsQ0FBRSwwQkFBRixFQUE4QjtBQUM5Q1EsT0FBSyxFQUFFLFdBRHVDO0FBRTlDQyxNQUFJLEVBQUUsV0FGd0M7QUFHOUNDLFVBQVEsRUFBRSxTQUhvQzs7QUFLOUM7Ozs7O0FBTUFDLE1BQUksRUFBRSxjQUFFQyxLQUFGLEVBQWE7QUFDbEIsV0FBTztBQUNOOzs7OztBQUtBaEIsTUFBRSxDQUFFTSxnQkFBRixFQUFvQjtBQUNyQlcsV0FBSyxFQUFFLDBCQURjO0FBRXJCQyxnQkFBVSxFQUFFRixLQUFLLENBQUNFO0FBRkcsS0FBcEIsQ0FOSTtBQVVOOzs7Ozs7O0FBT0FsQixNQUFFLENBQUVVLGlCQUFGLEVBQXFCLEVBQXJCLEVBQ0RWLEVBQUUsQ0FDRFEsV0FEQyxFQUVEO0FBQ0NXLFdBQUssRUFBRSxLQURSO0FBRUNDLFdBQUssRUFBRUosS0FBSyxDQUFDRSxVQUFOLENBQWlCRyxHQUZ6QjtBQUdDQyxjQUFRLEVBQUUsa0JBQUVGLEtBQUYsRUFBYTtBQUN0QkosYUFBSyxDQUFDTyxhQUFOLENBQXFCO0FBQUVGLGFBQUcsRUFBRUQ7QUFBUCxTQUFyQjtBQUNBO0FBTEYsS0FGQyxDQURELEVBV0RwQixFQUFFLENBQ0RTLGFBREMsRUFFRDtBQUNDVSxXQUFLLEVBQUUsUUFEUjtBQUVDQyxXQUFLLEVBQUVKLEtBQUssQ0FBQ0UsVUFBTixDQUFpQk0sTUFGekI7QUFHQ0YsY0FBUSxFQUFFLGtCQUFFRixLQUFGLEVBQWE7QUFDdEJKLGFBQUssQ0FBQ08sYUFBTixDQUFxQjtBQUFFQyxnQkFBTSxFQUFFSjtBQUFWLFNBQXJCO0FBQ0E7QUFMRixLQUZDLENBWEQsQ0FqQkksQ0FBUDtBQXdDQSxHQXBENkM7QUFzRDlDO0FBQ0FLLE1BQUksRUFBRSxnQkFBTTtBQUNYLFdBQU8sSUFBUDtBQUNBO0FBekQ2QyxDQUE5QixDQUFqQiIsImZpbGUiOiIuL3Jlc291cmNlcy9qcy90ZXN0LWJsb2NrLmpzLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLy8gTGljZW5zZTogR1BMdjIrXG5cbmNvbnN0IGVsID0gd3AuZWxlbWVudC5jcmVhdGVFbGVtZW50LFxuXHRyZWdpc3RlckJsb2NrVHlwZSA9IHdwLmJsb2Nrcy5yZWdpc3RlckJsb2NrVHlwZSxcblx0U2VydmVyU2lkZVJlbmRlciA9IHdwLmNvbXBvbmVudHMuU2VydmVyU2lkZVJlbmRlcixcblx0VGV4dENvbnRyb2wgPSB3cC5jb21wb25lbnRzLlRleHRDb250cm9sLFxuXHRUb2dnbGVDb250cm9sID0gd3AuY29tcG9uZW50cy5Ub2dnbGVDb250cm9sLFxuXHRJbnNwZWN0b3JDb250cm9scyA9IHdwLmVkaXRvci5JbnNwZWN0b3JDb250cm9scztcblxuLypcbiAqIEhlcmUncyB3aGVyZSB3ZSByZWdpc3RlciB0aGUgYmxvY2sgaW4gSmF2YVNjcmlwdC5cbiAqXG4gKiBJdCdzIG5vdCB5ZXQgcG9zc2libGUgdG8gcmVnaXN0ZXIgYSBibG9jayBlbnRpcmVseSB3aXRob3V0IEphdmFTY3JpcHQsIGJ1dFxuICogdGhhdCBpcyBzb21ldGhpbmcgSSdkIGxvdmUgdG8gc2VlIGhhcHBlbi4gVGhpcyBpcyBhIGJhcmVib25lcyBleGFtcGxlXG4gKiBvZiByZWdpc3RlcmluZyB0aGUgYmxvY2ssIGFuZCBnaXZpbmcgdGhlIGJhc2ljIGFiaWxpdHkgdG8gZWRpdCB0aGUgYmxvY2tcbiAqIGF0dHJpYnV0ZXMuIChJbiB0aGlzIGNhc2UsIHRoZXJlJ3Mgb25seSBvbmUgYXR0cmlidXRlLCAnZm9vJy4pXG4gKi9cbnJlZ2lzdGVyQmxvY2tUeXBlKCAnbmV4dGdlbnRoZW1lcy9hcnZlLWJsb2NrJywge1xuXHR0aXRsZTogJ1BIUCBCbG9jaycsXG5cdGljb246ICdtZWdhcGhvbmUnLFxuXHRjYXRlZ29yeTogJ3dpZGdldHMnLFxuXG5cdC8qXG5cdCAqIEluIG1vc3Qgb3RoZXIgYmxvY2tzLCB5b3UnZCBzZWUgYW4gJ2F0dHJpYnV0ZXMnIHByb3BlcnR5IGJlaW5nIGRlZmluZWQgaGVyZS5cblx0ICogV2UndmUgZGVmaW5lZCBhdHRyaWJ1dGVzIGluIHRoZSBQSFAsIHRoYXQgaW5mb3JtYXRpb24gaXMgYXV0b21hdGljYWxseSBzZW50XG5cdCAqIHRvIHRoZSBibG9jayBlZGl0b3IsIHNvIHdlIGRvbid0IG5lZWQgdG8gcmVkZWZpbmUgaXQgaGVyZS5cblx0ICovXG5cblx0ZWRpdDogKCBwcm9wcyApID0+IHtcblx0XHRyZXR1cm4gW1xuXHRcdFx0Lypcblx0XHRcdCAqIFRoZSBTZXJ2ZXJTaWRlUmVuZGVyIGVsZW1lbnQgdXNlcyB0aGUgUkVTVCBBUEkgdG8gYXV0b21hdGljYWxseSBjYWxsXG5cdFx0XHQgKiBwaHBfYmxvY2tfcmVuZGVyKCkgaW4geW91ciBQSFAgY29kZSB3aGVuZXZlciBpdCBuZWVkcyB0byBnZXQgYW4gdXBkYXRlZFxuXHRcdFx0ICogdmlldyBvZiB0aGUgYmxvY2suXG5cdFx0XHQgKi9cblx0XHRcdGVsKCBTZXJ2ZXJTaWRlUmVuZGVyLCB7XG5cdFx0XHRcdGJsb2NrOiAnbmV4dGdlbnRoZW1lcy9hcnZlLWJsb2NrJyxcblx0XHRcdFx0YXR0cmlidXRlczogcHJvcHMuYXR0cmlidXRlcyxcblx0XHRcdH0gKSxcblx0XHRcdC8qXG5cdFx0XHQgKiBJbnNwZWN0b3JDb250cm9scyBsZXRzIHlvdSBhZGQgY29udHJvbHMgdG8gdGhlIEJsb2NrIHNpZGViYXIuIEluIHRoaXMgY2FzZSxcblx0XHRcdCAqIHdlJ3JlIGFkZGluZyBhIFRleHRDb250cm9sLCB3aGljaCBsZXRzIHVzIGVkaXQgdGhlICdmb28nIGF0dHJpYnV0ZSAod2hpY2hcblx0XHRcdCAqIHdlIGRlZmluZWQgaW4gdGhlIFBIUCkuIFRoZSBvbkNoYW5nZSBwcm9wZXJ0eSBpcyBhIGxpdHRsZSBiaXQgb2YgbWFnaWMgdG8gdGVsbFxuXHRcdFx0ICogdGhlIGJsb2NrIGVkaXRvciB0byB1cGRhdGUgdGhlIHZhbHVlIG9mIG91ciAnZm9vJyBwcm9wZXJ0eSwgYW5kIHRvIHJlLXJlbmRlclxuXHRcdFx0ICogdGhlIGJsb2NrLlxuXHRcdFx0ICovXG5cdFx0XHRlbCggSW5zcGVjdG9yQ29udHJvbHMsIHt9LFxuXHRcdFx0XHRlbChcblx0XHRcdFx0XHRUZXh0Q29udHJvbCxcblx0XHRcdFx0XHR7XG5cdFx0XHRcdFx0XHRsYWJlbDogJ0ZvbycsXG5cdFx0XHRcdFx0XHR2YWx1ZTogcHJvcHMuYXR0cmlidXRlcy5mb28sXG5cdFx0XHRcdFx0XHRvbkNoYW5nZTogKCB2YWx1ZSApID0+IHtcblx0XHRcdFx0XHRcdFx0cHJvcHMuc2V0QXR0cmlidXRlcyggeyBmb286IHZhbHVlIH0gKTtcblx0XHRcdFx0XHRcdH0sXG5cdFx0XHRcdFx0fVxuXHRcdFx0XHQpLFxuXHRcdFx0XHRlbChcblx0XHRcdFx0XHRUb2dnbGVDb250cm9sLFxuXHRcdFx0XHRcdHtcblx0XHRcdFx0XHRcdGxhYmVsOiAnVG9vZ2xlJyxcblx0XHRcdFx0XHRcdHZhbHVlOiBwcm9wcy5hdHRyaWJ1dGVzLnRvZ2dsZSxcblx0XHRcdFx0XHRcdG9uQ2hhbmdlOiAoIHZhbHVlICkgPT4ge1xuXHRcdFx0XHRcdFx0XHRwcm9wcy5zZXRBdHRyaWJ1dGVzKCB7IHRvZ2dsZTogdmFsdWUgfSApO1xuXHRcdFx0XHRcdFx0fSxcblx0XHRcdFx0XHR9XG5cdFx0XHRcdCksXG5cdFx0XHQpLFxuXHRcdF07XG5cdH0sXG5cblx0Ly8gV2UncmUgZ29pbmcgdG8gYmUgcmVuZGVyaW5nIGluIFBIUCwgc28gc2F2ZSgpIGNhbiBqdXN0IHJldHVybiBudWxsLlxuXHRzYXZlOiAoKSA9PiB7XG5cdFx0cmV0dXJuIG51bGw7XG5cdH0sXG59ICk7XG4iXSwic291cmNlUm9vdCI6IiJ9\n//# sourceURL=webpack-internal:///./resources/js/test-block.js\n");

/***/ }),

/***/ 2:
/*!******************************************!*\
  !*** multi ./resources/js/test-block.js ***!
  \******************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! /uncrypted/websites/symbiosistheme.com/web/app/plugins/advanced-responsive-video-embedder/resources/js/test-block.js */"./resources/js/test-block.js");


/***/ })

/******/ });