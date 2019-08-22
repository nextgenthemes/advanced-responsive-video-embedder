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
/******/ 	return __webpack_require__(__webpack_require__.s = 1);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/js/gb-block.js":
/*!**********************************!*\
  !*** ./resources/js/gb-block.js ***!
  \**********************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("function _toConsumableArray(arr) { return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _nonIterableSpread(); }\n\nfunction _nonIterableSpread() { throw new TypeError(\"Invalid attempt to spread non-iterable instance\"); }\n\nfunction _iterableToArray(iter) { if (Symbol.iterator in Object(iter) || Object.prototype.toString.call(iter) === \"[object Arguments]\") return Array.from(iter); }\n\nfunction _arrayWithoutHoles(arr) { if (Array.isArray(arr)) { for (var i = 0, arr2 = new Array(arr.length); i < arr.length; i++) { arr2[i] = arr[i]; } return arr2; } }\n\nfunction _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }\n\n// License: GPLv2+\n//console.log( ARVEsettings );\nvar wp = window.wp;\nvar el = window.wp.element.createElement;\nwp.data.dispatch('core/edit-post').hideBlockTypes(['core-embed/youtube', 'core-embed/vimeo', 'core-embed/dailymotion', 'core-embed/collegehumor', 'core-embed/ted']);\n/*\n * Keypair to gutenberg component\n */\n\nfunction PrepareOptions(options) {\n  var gboptions = [];\n  Object.keys(options).forEach(function (key) {\n    gboptions.push({\n      label: options[key],\n      value: key\n    });\n  });\n  return gboptions;\n}\n\nfunction BuildControls(props) {\n  var controls = [];\n  Object.keys(window.ARVEsettings).forEach(function (key) {\n    var opt = window.ARVEsettings[key];\n    var cArgs = {\n      label: opt.label,\n      //help: opt.description,\n      onChange: function onChange(value) {\n        props.setAttributes(_defineProperty({}, key, value));\n      }\n    };\n\n    if ('bool+default' === opt.type) {\n      opt.type = 'select';\n    }\n\n    switch (opt.type) {\n      case 'boolean':\n        cArgs.onChange = function (value) {\n          props.setAttributes(_defineProperty({}, key, value));\n        };\n\n        controls.push(el(wp.components.CheckboxControl, cArgs));\n        break;\n\n      case 'select':\n        cArgs.options = PrepareOptions(opt.options);\n        cArgs.selected = props.attributes[key];\n        controls.push(el(wp.components.SelectControl, cArgs));\n        break;\n\n      case 'string':\n        cArgs.value = props.attributes[key];\n        controls.push(el(wp.components.TextControl, cArgs));\n        break;\n    }\n  });\n  return controls;\n}\n/*\n * Here's where we register the block in JavaScript.\n *\n * It's not yet possible to register a block entirely without JavaScript, but\n * that is something I'd love to see happen. This is a barebones example\n * of registering the block, and giving the basic ability to edit the block\n * attributes. (In this case, there's only one attribute, 'foo'.)\n */\n\n\nwp.blocks.registerBlockType('nextgenthemes/arve-block', {\n  title: 'Video Embed (ARVE)',\n  icon: 'video-alt3',\n  category: 'embed',\n\n  /*\n   * In most other blocks, you'd see an 'attributes' property being defined here.\n   * We've defined attributes in the PHP, that information is automatically sent\n   * to the block editor, so we don't need to redefine it here.\n   */\n  edit: function edit(props) {\n    var controls = BuildControls(props);\n    return [\n    /*\n     * The ServerSideRender element uses the REST API to automatically call\n     * php_block_render() in your PHP code whenever it needs to get an updated\n     * view of the block.\n     */\n    el(wp.components.ServerSideRender, {\n      block: 'nextgenthemes/arve-block',\n      attributes: props.attributes\n    }),\n    /*\n     * InspectorControls lets you add controls to the Block sidebar. In this case,\n     * we're adding a TextControl, which lets us edit the 'foo' attribute (which\n     * we defined in the PHP). The onChange property is a little bit of magic to tell\n     * the block editor to update the value of our 'foo' property, and to re-render\n     * the block.\n     */\n    el.apply(void 0, [wp.editor.InspectorControls, {}].concat(_toConsumableArray(controls)))];\n  },\n  // We're going to be rendering in PHP, so save() can just return null.\n  save: function save() {\n    return null;\n  }\n});//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9yZXNvdXJjZXMvanMvZ2ItYmxvY2suanM/ZTVmNiJdLCJuYW1lcyI6WyJ3cCIsIndpbmRvdyIsImVsIiwiZWxlbWVudCIsImNyZWF0ZUVsZW1lbnQiLCJkYXRhIiwiZGlzcGF0Y2giLCJoaWRlQmxvY2tUeXBlcyIsIlByZXBhcmVPcHRpb25zIiwib3B0aW9ucyIsImdib3B0aW9ucyIsIk9iamVjdCIsImtleXMiLCJmb3JFYWNoIiwia2V5IiwicHVzaCIsImxhYmVsIiwidmFsdWUiLCJCdWlsZENvbnRyb2xzIiwicHJvcHMiLCJjb250cm9scyIsIkFSVkVzZXR0aW5ncyIsIm9wdCIsImNBcmdzIiwib25DaGFuZ2UiLCJzZXRBdHRyaWJ1dGVzIiwidHlwZSIsImNvbXBvbmVudHMiLCJDaGVja2JveENvbnRyb2wiLCJzZWxlY3RlZCIsImF0dHJpYnV0ZXMiLCJTZWxlY3RDb250cm9sIiwiVGV4dENvbnRyb2wiLCJibG9ja3MiLCJyZWdpc3RlckJsb2NrVHlwZSIsInRpdGxlIiwiaWNvbiIsImNhdGVnb3J5IiwiZWRpdCIsIlNlcnZlclNpZGVSZW5kZXIiLCJibG9jayIsImVkaXRvciIsIkluc3BlY3RvckNvbnRyb2xzIiwic2F2ZSJdLCJtYXBwaW5ncyI6Ijs7Ozs7Ozs7OztBQUFBO0FBQ0E7QUFDQSxJQUFNQSxFQUFFLEdBQUdDLE1BQU0sQ0FBQ0QsRUFBbEI7QUFDQSxJQUFNRSxFQUFFLEdBQUdELE1BQU0sQ0FBQ0QsRUFBUCxDQUFVRyxPQUFWLENBQWtCQyxhQUE3QjtBQUVBSixFQUFFLENBQUNLLElBQUgsQ0FBUUMsUUFBUixDQUFrQixnQkFBbEIsRUFBcUNDLGNBQXJDLENBQXFELENBQ3BELG9CQURvRCxFQUVwRCxrQkFGb0QsRUFHcEQsd0JBSG9ELEVBSXBELHlCQUpvRCxFQUtwRCxnQkFMb0QsQ0FBckQ7QUFRQTs7OztBQUdBLFNBQVNDLGNBQVQsQ0FBeUJDLE9BQXpCLEVBQW1DO0FBQ2xDLE1BQU1DLFNBQVMsR0FBRyxFQUFs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QXFCQSxHQW5DdUQ7QUFxQ3hEO0FBQ0F1QixNQUFJLEVBQUUsZ0JBQU07QUFDWCxXQUFPLElBQVA7QUFDQTtBQXhDdUQsQ0FBekQiLCJmaWxlIjoiLi9yZXNvdXJjZXMvanMvZ2ItYmxvY2suanMuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvLyBMaWNlbnNlOiBHUEx2Mitcbi8vY29uc29sZS5sb2coIEFSVkVzZXR0aW5ncyApO1xuY29uc3Qgd3AgPSB3aW5kb3cud3A7XG5jb25zdCBlbCA9IHdpbmRvdy53cC5lbGVtZW50LmNyZWF0ZUVsZW1lbnQ7XG5cbndwLmRhdGEuZGlzcGF0Y2goICdjb3JlL2VkaXQtcG9zdCcgKS5oaWRlQmxvY2tUeXBlcyggW1xuXHQnY29yZS1lbWJlZC95b3V0dWJlJyxcblx0J2NvcmUtZW1iZWQvdmltZW8nLFxuXHQnY29yZS1lbWJlZC9kYWlseW1vdGlvbicsXG5cdCdjb3JlLWVtYmVkL2NvbGxlZ2VodW1vcicsXG5cdCdjb3JlLWVtYmVkL3RlZCcsXG5dICk7XG5cbi8qXG4gKiBLZXlwYWlyIHRvIGd1dGVuYmVyZyBjb21wb25lbnRcbiAqL1xuZnVuY3Rpb24gUHJlcGFyZU9wdGlvbnMoIG9wdGlvbnMgKSB7XG5cdGNvbnN0IGdib3B0aW9ucyA9IFtdO1xuXG5cdE9iamVjdC5rZXlzKCBvcHRpb25zICkuZm9yRWFjaCggKCBrZXkgKSA9PiB7XG5cdFx0Z2JvcHRpb25zLnB1c2goIHtcblx0XHRcdGxhYmVsOiBvcHRpb25zWyBrZXkgXSxcblx0XHRcdHZhbHVlOiBrZXksXG5cdFx0fSApO1xuXHR9ICk7XG5cblx0cmV0dXJuIGdib3B0aW9ucztcbn1cblxuZnVuY3Rpb24gQnVpbGRDb250cm9scyggcHJvcHMgKSB7XG5cdGNvbnN0IGNvbnRyb2xzID0gW107XG5cblx0T2JqZWN0LmtleXMoIHdpbmRvdy5BUlZFc2V0dGluZ3MgKS5mb3JFYWNoKCAoIGtleSApID0+IHtcblx0XHRjb25zdCBvcHQgPSB3aW5kb3cuQVJWRXNldHRpbmdzWyBrZXkgXTtcblx0XHRjb25zdCBjQXJncyA9IHtcblx0XHRcdGxhYmVsOiBvcHQubGFiZWwsXG5cblx0XHRcdC8vaGVscDogb3B0LmRlc2NyaXB0aW9uLFxuXHRcdFx0b25DaGFuZ2U6ICggdmFsdWUgKSA9PiB7XG5cdFx0XHRcdHByb3BzLnNldEF0dHJpYnV0ZXMoIHsgWyBrZXkgXTogdmFsdWUgfSApO1xuXHRcdFx0fSxcblx0XHR9O1xuXG5cdFx0aWYgKCAnYm9vbCtkZWZhdWx0JyA9PT0gb3B0LnR5cGUgKSB7XG5cdFx0XHRvcHQudHlwZSA9ICdzZWxlY3QnO1xuXHRcdH1cblxuXHRcdHN3aXRjaCAoIG9wdC50eXBlICkge1xuXHRcdFx0Y2FzZSAnYm9vbGVhbic6XG5cdFx0XHRcdGNBcmdzLm9uQ2hhbmdlID0gKCB2YWx1ZSApID0+IHtcblx0XHRcdFx0XHRwcm9wcy5zZXRBdHRyaWJ1dGVzKCB7IFsga2V5IF06IHZhbHVlIH0gKTtcblx0XHRcdFx0fTtcblxuXHRcdFx0XHRjb250cm9scy5wdXNoKCBlbCggd3AuY29tcG9uZW50cy5DaGVja2JveENvbnRyb2wsIGNBcmdzICkgKTtcblx0XHRcdFx0YnJlYWs7XG5cblx0XHRcdGNhc2UgJ3NlbGVjdCc6XG5cdFx0XHRcdGNBcmdzLm9wdGlvbnMgPSBQcmVwYXJlT3B0aW9ucyggb3B0Lm9wdGlvbnMgKTtcblx0XHRcdFx0Y0FyZ3Muc2VsZWN0ZWQgPSBwcm9wcy5hdHRyaWJ1dGVzWyBrZXkgXTtcblxuXHRcdFx0XHRjb250cm9scy5wdXNoKCBlbCggd3AuY29tcG9uZW50cy5TZWxlY3RDb250cm9sLCBjQXJncyApICk7XG5cdFx0XHRcdGJyZWFrO1xuXG5cdFx0XHRjYXNlICdzdHJpbmcnOlxuXHRcdFx0XHRjQXJncy52YWx1ZSA9IHByb3BzLmF0dHJpYnV0ZXNbIGtleSBdO1xuXG5cdFx0XHRcdGNvbnRyb2xzLnB1c2goIGVsKCB3cC5jb21wb25lbnRzLlRleHRDb250cm9sLCBjQXJncyApICk7XG5cdFx0XHRcdGJyZWFrO1xuXHRcdH1cblx0fSApO1xuXG5cdHJldHVybiBjb250cm9scztcbn1cblxuLypcbiAqIEhlcmUncyB3aGVyZSB3ZSByZWdpc3RlciB0aGUgYmxvY2sgaW4gSmF2YVNjcmlwdC5cbiAqXG4gKiBJdCdzIG5vdCB5ZXQgcG9zc2libGUgdG8gcmVnaXN0ZXIgYSBibG9jayBlbnRpcmVseSB3aXRob3V0IEphdmFTY3JpcHQsIGJ1dFxuICogdGhhdCBpcyBzb21ldGhpbmcgSSdkIGxvdmUgdG8gc2VlIGhhcHBlbi4gVGhpcyBpcyBhIGJhcmVib25lcyBleGFtcGxlXG4gKiBvZiByZWdpc3RlcmluZyB0aGUgYmxvY2ssIGFuZCBnaXZpbmcgdGhlIGJhc2ljIGFiaWxpdHkgdG8gZWRpdCB0aGUgYmxvY2tcbiAqIGF0dHJpYnV0ZXMuIChJbiB0aGlzIGNhc2UsIHRoZXJlJ3Mgb25seSBvbmUgYXR0cmlidXRlLCAnZm9vJy4pXG4gKi9cbndwLmJsb2Nrcy5yZWdpc3RlckJsb2NrVHlwZSggJ25leHRnZW50aGVtZXMvYXJ2ZS1ibG9jaycsIHtcblx0dGl0bGU6ICdWaWRlbyBFbWJlZCAoQVJWRSknLFxuXHRpY29uOiAndmlkZW8tYWx0MycsXG5cdGNhdGVnb3J5OiAnZW1iZWQnLFxuXG5cdC8qXG5cdCAqIEluIG1vc3Qgb3RoZXIgYmxvY2tzLCB5b3UnZCBzZWUgYW4gJ2F0dHJpYnV0ZXMnIHByb3BlcnR5IGJlaW5nIGRlZmluZWQgaGVyZS5cblx0ICogV2UndmUgZGVmaW5lZCBhdHRyaWJ1dGVzIGluIHRoZSBQSFAsIHRoYXQgaW5mb3JtYXRpb24gaXMgYXV0b21hdGljYWxseSBzZW50XG5cdCAqIHRvIHRoZSBibG9jayBlZGl0b3IsIHNvIHdlIGRvbid0IG5lZWQgdG8gcmVkZWZpbmUgaXQgaGVyZS5cblx0ICovXG5cblx0ZWRpdDogKCBwcm9wcyApID0+IHtcblx0XHRjb25zdCBjb250cm9scyA9IEJ1aWxkQ29udHJvbHMoIHByb3BzICk7XG5cblx0XHRyZXR1cm4gW1xuXG5cdFx0XHQvKlxuXHRcdFx0ICogVGhlIFNlcnZlclNpZGVSZW5kZXIgZWxlbWVudCB1c2VzIHRoZSBSRVNUIEFQSSB0byBhdXRvbWF0aWNhbGx5IGNhbGxcblx0XHRcdCAqIHBocF9ibG9ja19yZW5kZXIoKSBpbiB5b3VyIFBIUCBjb2RlIHdoZW5ldmVyIGl0IG5lZWRzIHRvIGdldCBhbiB1cGRhdGVkXG5cdFx0XHQgKiB2aWV3IG9mIHRoZSBibG9jay5cblx0XHRcdCAqL1xuXHRcdFx0ZWwoIHdwLmNvbXBvbmVudHMuU2VydmVyU2lkZVJlbmRlciwge1xuXHRcdFx0XHRibG9jazogJ25leHRnZW50aGVtZXMvYXJ2ZS1ibG9jaycsXG5cdFx0XHRcdGF0dHJpYnV0ZXM6IHByb3BzLmF0dHJpYnV0ZXMsXG5cdFx0XHR9ICksXG5cblx0XHRcdC8qXG5cdFx0XHQgKiBJbnNwZWN0b3JDb250cm9scyBsZXRzIHlvdSBhZGQgY29udHJvbHMgdG8gdGhlIEJsb2NrIHNpZGViYXIuIEluIHRoaXMgY2FzZSxcblx0XHRcdCAqIHdlJ3JlIGFkZGluZyBhIFRleHRDb250cm9sLCB3aGljaCBsZXRzIHVzIGVkaXQgdGhlICdmb28nIGF0dHJpYnV0ZSAod2hpY2hcblx0XHRcdCAqIHdlIGRlZmluZWQgaW4gdGhlIFBIUCkuIFRoZSBvbkNoYW5nZSBwcm9wZXJ0eSBpcyBhIGxpdHRsZSBiaXQgb2YgbWFnaWMgdG8gdGVsbFxuXHRcdFx0ICogdGhlIGJsb2NrIGVkaXRvciB0byB1cGRhdGUgdGhlIHZhbHVlIG9mIG91ciAnZm9vJyBwcm9wZXJ0eSwgYW5kIHRvIHJlLXJlbmRlclxuXHRcdFx0ICogdGhlIGJsb2NrLlxuXHRcdFx0ICovXG5cdFx0XHRlbCggd3AuZWRpdG9yLkluc3BlY3RvckNvbnRyb2xzLCB7fSwgLi4uY29udHJvbHMgKSxcblx0XHRdO1xuXHR9LFxuXG5cdC8vIFdlJ3JlIGdvaW5nIHRvIGJlIHJlbmRlcmluZyBpbiBQSFAsIHNvIHNhdmUoKSBjYW4ganVzdCByZXR1cm4gbnVsbC5cblx0c2F2ZTogKCkgPT4ge1xuXHRcdHJldHVybiBudWxsO1xuXHR9LFxufSApO1xuIl0sInNvdXJjZVJvb3QiOiIifQ==\n//# sourceURL=webpack-internal:///./resources/js/gb-block.js\n");

/***/ }),

/***/ 1:
/*!****************************************!*\
  !*** multi ./resources/js/gb-block.js ***!
  \****************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! /uncrypted/websites/symbiosistheme.com/web/app/plugins/advanced-responsive-video-embedder/resources/js/gb-block.js */"./resources/js/gb-block.js");


/***/ })

/******/ });