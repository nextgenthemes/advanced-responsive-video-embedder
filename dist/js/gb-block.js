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

eval("function _toConsumableArray(arr) { return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _nonIterableSpread(); }\n\nfunction _nonIterableSpread() { throw new TypeError(\"Invalid attempt to spread non-iterable instance\"); }\n\nfunction _iterableToArray(iter) { if (Symbol.iterator in Object(iter) || Object.prototype.toString.call(iter) === \"[object Arguments]\") return Array.from(iter); }\n\nfunction _arrayWithoutHoles(arr) { if (Array.isArray(arr)) { for (var i = 0, arr2 = new Array(arr.length); i < arr.length; i++) { arr2[i] = arr[i]; } return arr2; } }\n\nfunction _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }\n\n// License: GPLv2+\n//console.log( ARVEsettings );\nvar wp = window.wp;\nvar el = window.wp.element.createElement;\n/*\n * Keypair to gutenberg component\n */\n\nfunction PrepareOptions(options) {\n  var gboptions = [];\n  Object.keys(options).forEach(function (key) {\n    gboptions.push({\n      label: options[key],\n      value: key\n    });\n  });\n  return gboptions;\n}\n\nfunction BuildControls(props) {\n  var controls = [];\n  Object.keys(window.ARVEsettings).forEach(function (key) {\n    var opt = window.ARVEsettings[key];\n    var cArgs = {\n      label: opt.label,\n      //help: opt.description,\n      onChange: function onChange(value) {\n        props.setAttributes(_defineProperty({}, key, value));\n      }\n    };\n\n    if ('bool+default' === opt.type) {\n      opt.type = 'select';\n    }\n\n    switch (opt.type) {\n      case 'boolean':\n        cArgs.onChange = function (value) {\n          props.setAttributes(_defineProperty({}, key, value));\n        };\n\n        controls.push(el(wp.components.CheckboxControl, cArgs));\n        break;\n\n      case 'select':\n        cArgs.options = PrepareOptions(opt.options);\n        cArgs.selected = props.attributes[key];\n        controls.push(el(wp.components.SelectControl, cArgs));\n        break;\n\n      case 'string':\n        cArgs.value = props.attributes[key];\n        controls.push(el(wp.components.TextControl, cArgs));\n        break;\n    }\n  });\n  return controls;\n}\n/*\n * Here's where we register the block in JavaScript.\n *\n * It's not yet possible to register a block entirely without JavaScript, but\n * that is something I'd love to see happen. This is a barebones example\n * of registering the block, and giving the basic ability to edit the block\n * attributes. (In this case, there's only one attribute, 'foo'.)\n */\n\n\nwp.blocks.registerBlockType('nextgenthemes/arve-block', {\n  title: 'Video Embed (ARVE)',\n  icon: 'video-alt3',\n  category: 'embed',\n\n  /*\n   * In most other blocks, you'd see an 'attributes' property being defined here.\n   * We've defined attributes in the PHP, that information is automatically sent\n   * to the block editor, so we don't need to redefine it here.\n   */\n  edit: function edit(props) {\n    var controls = BuildControls(props);\n    return [\n    /*\n     * The ServerSideRender element uses the REST API to automatically call\n     * php_block_render() in your PHP code whenever it needs to get an updated\n     * view of the block.\n     */\n    el(wp.components.ServerSideRender, {\n      block: 'nextgenthemes/arve-block',\n      attributes: props.attributes\n    }),\n    /*\n     * InspectorControls lets you add controls to the Block sidebar. In this case,\n     * we're adding a TextControl, which lets us edit the 'foo' attribute (which\n     * we defined in the PHP). The onChange property is a little bit of magic to tell\n     * the block editor to update the value of our 'foo' property, and to re-render\n     * the block.\n     */\n    el.apply(void 0, [wp.editor.InspectorControls, {}].concat(_toConsumableArray(controls)))];\n  },\n  // We're going to be rendering in PHP, so save() can just return null.\n  save: function save() {\n    return null;\n  }\n});//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9yZXNvdXJjZXMvanMvZ2ItYmxvY2suanM/ZTVmNiJdLCJuYW1lcyI6WyJ3cCIsIndpbmRvdyIsImVsIiwiZWxlbWVudCIsImNyZWF0ZUVsZW1lbnQiLCJQcmVwYXJlT3B0aW9ucyIsIm9wdGlvbnMiLCJnYm9wdGlvbnMiLCJPYmplY3QiLCJrZXlzIiwiZm9yRWFjaCIsImtleSIsInB1c2giLCJsYWJlbCIsInZhbHVlIiwiQnVpbGRDb250cm9scyIsInByb3BzIiwiY29udHJvbHMiLCJBUlZFc2V0dGluZ3MiLCJvcHQiLCJjQXJncyIsIm9uQ2hhbmdlIiwic2V0QXR0cmlidXRlcyIsInR5cGUiLCJjb21wb25lbnRzIiwiQ2hlY2tib3hDb250cm9sIiwic2VsZWN0ZWQiLCJhdHRyaWJ1dGVzIiwiU2VsZWN0Q29udHJvbCIsIlRleHRDb250cm9sIiwiYmxvY2tzIiwicmVnaXN0ZXJCbG9ja1R5cGUiLCJ0aXRsZSIsImljb24iLCJjYXRlZ29yeSIsImVkaXQiLCJTZXJ2ZXJTaWRlUmVuZGVyIiwiYmxvY2siLCJlZGl0b3IiLCJJbnNwZWN0b3JDb250cm9scyIsInNhdmUiXSwibWFwcGluZ3MiOiI7Ozs7Ozs7Ozs7QUFBQTtBQUNBO0FBQ0EsSUFBTUEsRUFBRSxHQUFHQyxNQUFNLENBQUNELEVBQWxCO0FBQ0EsSUFBTUUsRUFBRSxHQUFHRCxNQUFNLENBQUNELEVBQVAsQ0FBVUcsT0FBVixDQUFrQkMsYUFBN0I7QUFFQTs7OztBQUdBLFNBQVNDLGNBQVQsQ0FBeUJDLE9BQXpCLEVBQW1DO0FBQ2xDLE1BQU1DLFNBQVMsR0FBRyxFQUFsQjtBQUVBQyxRQUFNLENBQUNDLElBQVAsQ0FBYUgsT0FBYixFQUF1QkksT0FBdkIsQ0FBZ0MsVUFBRUMsR0FBRixFQUFXO0FBQzFDSixhQUFTLENBQUNLLElBQVYsQ0FBZ0I7QUFDZkMsV0FBSyxFQUFFUCxPQUFPLENBQUVLLEdBQUYsQ0FEQztBQUVmRyxXQUFLLEVBQUVIO0FBRlEsS0FBaEI7QUFJQSxHQUxEO0FBT0EsU0FBT0osU0FBUDtBQUNBOztBQUVELFNBQVNRLGFBQVQsQ0FBd0JDLEtBQXhCLEVBQWdDO0FBQy9CLE1BQU1DLFFBQVEsR0FBRyxFQUFqQjtBQUVBVCxRQUFNLENBQUNDLElBQVAsQ0FBYVIsTUFBTSxDQUFDaUIsWUFBcEIsRUFBbUNSLE9BQW5DLENBQTRDLFVBQUVDLEdBQUYsRUFBVztBQUN0RCxRQUFNUSxHQUFHLEdBQUdsQixNQUFNLENBQUNpQixZQUFQLENBQXFCUCxHQUFyQixDQUFaO0FBQ0EsUUFBTVMsS0FBSyxHQUFHO0FBQ2JQLFdBQUssRUFBRU0sR0FBRyxDQUFDTixLQURFO0FBR2I7QUFDQVEsY0FBUSxFQUFFLGtCQUFFUCxLQUFGLEVBQWE7QUFDdEJFLGFBQUssQ0FBQ00sYUFBTixxQkFBeUJYLEdBQXpCLEVBQWdDRyxLQUFoQztBQUNBO0FBTlksS0FBZDs7QUFTQSxRQUFLLG1CQUFtQkssR0FBRyxDQUFDSSxJQUE1QixFQUFtQztBQUNsQ0osU0FBRyxDQUFDSSxJQUFKLEdBQVcsUUFBWDtBQUNBOztBQUVELFlBQVNKLEdBQUcsQ0FBQ0ksSUFBYjtBQUNDLFdBQUssU0FBTDtBQUNDSCxhQUFLLENBQUNDLFFBQU4sR0FBaUIsVUFBRVAsS0FBRixFQUFhO0FBQzdCRSxlQUFLLENBQUNNLGFBQU4scUJBQXlCWCxHQUF6QixFQUFnQ0csS0FBaEM7QUFDQSxTQUZEOztBQUlBRyxnQkFBUSxDQUFDTCxJQUFULENBQWVWLEVBQUUsQ0FBRUYsRUFBRSxDQUFDd0IsVUFBSCxDQUFjQyxlQUFoQixFQUFpQ0wsS0FBakMsQ0FBakI7QUFDQTs7QUFFRCxXQUFLLFFBQUw7QUFDQ0EsYUFBSyxDQUFDZCxPQUFOLEdBQWdCRCxjQUFjLENBQUVjLEdBQUcsQ0FBQ2IsT0FBTixDQUE5QjtBQUNBYyxhQUFLLENBQUNNLFFBQU4sR0FBaUJWLEtBQUssQ0FBQ1csVUFBTixDQUFrQmhCLEdBQWxCLENBQWpCO0FBRUFNLGdCQUFRLENBQUNMLElBQVQsQ0FBZVYsRUFBRSxDQUFFRixFQUFFLENBQUN3QixVQUFILENBQWNJLGFBQWhCLEVBQStCUixLQUEvQixDQUFqQjtBQUNBOztBQUVELFdBQUssUUFBTDtBQUNDQSxhQUFLLENBQUNOLEtBQU4sR0FBY0UsS0FBSyxDQUFDVyxVQUFOLENBQWtCaEIsR0FBbEIsQ0FBZDtBQUVBTSxnQkFBUSxDQUFDTCxJQUFULENBQWVWLEVBQUUsQ0FBRUYsRUFBRSxDQUFDd0IsVUFBSCxDQUFjSyxXQUFoQixFQUE2QlQsS0FBN0IsQ0FBakI7QUFDQTtBQXBCRjtBQXNCQSxHQXJDRDtBQXVDQSxTQUFPSCxRQUFQO0FBQ0E7QUFFRDs7Ozs7Ozs7OztBQVFBakIsRUFBRSxDQUFDOEIsTUFBSCxDQUFVQyxpQkFBVixDQUE2QiwwQkFBN0IsRUFBeUQ7QUFDeERDLE9BQUssRUFBRSxvQkFEaUQ7QUFFeERDLE1BQUksRUFBRSxZQUZrRDtBQUd4REMsVUFBUSxFQUFFLE9BSDhDOztBQUt4RDs7Ozs7QUFNQUMsTUFBSSxFQUFFLGNBQUVuQixLQUFGLEVBQWE7QUFDbEIsUUFBTUMsUUFBUSxHQUFHRixhQUFhLENBQUVDLEtBQUYsQ0FBOUI7QUFFQSxXQUFPO0FBRU47Ozs7O0FBS0FkLE1BQUUsQ0FBRUYsRUFBRSxDQUFDd0IsVUFBSCxDQUFjWSxnQkFBaEIsRUFBa0M7QUFDbkNDLFdBQUssRUFBRSwwQkFENEI7QUFFbkNWLGdCQUFVLEVBQUVYLEtBQUssQ0FBQ1c7QUFGaUIsS0FBbEMsQ0FQSTtBQVlOOzs7Ozs7O0FBT0F6QixNQUFFLE1BQUYsVUFBSUYsRUFBRSxDQUFDc0MsTUFBSCxDQUFVQyxpQkFBZCxFQUFpQyxFQUFqQyw0QkFBd0N0QixRQUF4QyxHQW5CTSxDQUFQO0FBcUJBLEdBbkN1RDtBQXFDeEQ7QUFDQXVCLE1BQUksRUFBRSxnQkFBTTtBQUNYLFdBQU8sSUFBUDtBQUNBO0FBeEN1RCxDQUF6RCIsImZpbGUiOiIuL3Jlc291cmNlcy9qcy9nYi1ibG9jay5qcy5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8vIExpY2Vuc2U6IEdQTHYyK1xuLy9jb25zb2xlLmxvZyggQVJWRXNldHRpbmdzICk7XG5jb25zdCB3cCA9IHdpbmRvdy53cDtcbmNvbnN0IGVsID0gd2luZG93LndwLmVsZW1lbnQuY3JlYXRlRWxlbWVudDtcblxuLypcbiAqIEtleXBhaXIgdG8gZ3V0ZW5iZXJnIGNvbXBvbmVudFxuICovXG5mdW5jdGlvbiBQcmVwYXJlT3B0aW9ucyggb3B0aW9ucyApIHtcblx0Y29uc3QgZ2JvcHRpb25zID0gW107XG5cblx0T2JqZWN0LmtleXMoIG9wdGlvbnMgKS5mb3JFYWNoKCAoIGtleSApID0+IHtcblx0XHRnYm9wdGlvbnMucHVzaCgge1xuXHRcdFx0bGFiZWw6IG9wdGlvbnNbIGtleSBdLFxuXHRcdFx0dmFsdWU6IGtleSxcblx0XHR9ICk7XG5cdH0gKTtcblxuXHRyZXR1cm4gZ2JvcHRpb25zO1xufVxuXG5mdW5jdGlvbiBCdWlsZENvbnRyb2xzKCBwcm9wcyApIHtcblx0Y29uc3QgY29udHJvbHMgPSBbXTtcblxuXHRPYmplY3Qua2V5cyggd2luZG93LkFSVkVzZXR0aW5ncyApLmZvckVhY2goICgga2V5ICkgPT4ge1xuXHRcdGNvbnN0IG9wdCA9IHdpbmRvdy5BUlZFc2V0dGluZ3NbIGtleSBdO1xuXHRcdGNvbnN0IGNBcmdzID0ge1xuXHRcdFx0bGFiZWw6IG9wdC5sYWJlbCxcblxuXHRcdFx0Ly9oZWxwOiBvcHQuZGVzY3JpcHRpb24sXG5cdFx0XHRvbkNoYW5nZTogKCB2YWx1ZSApID0+IHtcblx0XHRcdFx0cHJvcHMuc2V0QXR0cmlidXRlcyggeyBbIGtleSBdOiB2YWx1ZSB9ICk7XG5cdFx0XHR9LFxuXHRcdH07XG5cblx0XHRpZiAoICdib29sK2RlZmF1bHQnID09PSBvcHQudHlwZSApIHtcblx0XHRcdG9wdC50eXBlID0gJ3NlbGVjdCc7XG5cdFx0fVxuXG5cdFx0c3dpdGNoICggb3B0LnR5cGUgKSB7XG5cdFx0XHRjYXNlICdib29sZWFuJzpcblx0XHRcdFx0Y0FyZ3Mub25DaGFuZ2UgPSAoIHZhbHVlICkgPT4ge1xuXHRcdFx0XHRcdHByb3BzLnNldEF0dHJpYnV0ZXMoIHsgWyBrZXkgXTogdmFsdWUgfSApO1xuXHRcdFx0XHR9O1xuXG5cdFx0XHRcdGNvbnRyb2xzLnB1c2goIGVsKCB3cC5jb21wb25lbnRzLkNoZWNrYm94Q29udHJvbCwgY0FyZ3MgKSApO1xuXHRcdFx0XHRicmVhaztcblxuXHRcdFx0Y2FzZSAnc2VsZWN0Jzpcblx0XHRcdFx0Y0FyZ3Mub3B0aW9ucyA9IFByZXBhcmVPcHRpb25zKCBvcHQub3B0aW9ucyApO1xuXHRcdFx0XHRjQXJncy5zZWxlY3RlZCA9IHByb3BzLmF0dHJpYnV0ZXNbIGtleSBdO1xuXG5cdFx0XHRcdGNvbnRyb2xzLnB1c2goIGVsKCB3cC5jb21wb25lbnRzLlNlbGVjdENvbnRyb2wsIGNBcmdzICkgKTtcblx0XHRcdFx0YnJlYWs7XG5cblx0XHRcdGNhc2UgJ3N0cmluZyc6XG5cdFx0XHRcdGNBcmdzLnZhbHVlID0gcHJvcHMuYXR0cmlidXRlc1sga2V5IF07XG5cblx0XHRcdFx0Y29udHJvbHMucHVzaCggZWwoIHdwLmNvbXBvbmVudHMuVGV4dENvbnRyb2wsIGNBcmdzICkgKTtcblx0XHRcdFx0YnJlYWs7XG5cdFx0fVxuXHR9ICk7XG5cblx0cmV0dXJuIGNvbnRyb2xzO1xufVxuXG4vKlxuICogSGVyZSdzIHdoZXJlIHdlIHJlZ2lzdGVyIHRoZSBibG9jayBpbiBKYXZhU2NyaXB0LlxuICpcbiAqIEl0J3Mgbm90IHlldCBwb3NzaWJsZSB0byByZWdpc3RlciBhIGJsb2NrIGVudGlyZWx5IHdpdGhvdXQgSmF2YVNjcmlwdCwgYnV0XG4gKiB0aGF0IGlzIHNvbWV0aGluZyBJJ2QgbG92ZSB0byBzZWUgaGFwcGVuLiBUaGlzIGlzIGEgYmFyZWJvbmVzIGV4YW1wbGVcbiAqIG9mIHJlZ2lzdGVyaW5nIHRoZSBibG9jaywgYW5kIGdpdmluZyB0aGUgYmFzaWMgYWJpbGl0eSB0byBlZGl0IHRoZSBibG9ja1xuICogYXR0cmlidXRlcy4gKEluIHRoaXMgY2FzZSwgdGhlcmUncyBvbmx5IG9uZSBhdHRyaWJ1dGUsICdmb28nLilcbiAqL1xud3AuYmxvY2tzLnJlZ2lzdGVyQmxvY2tUeXBlKCAnbmV4dGdlbnRoZW1lcy9hcnZlLWJsb2NrJywge1xuXHR0aXRsZTogJ1ZpZGVvIEVtYmVkIChBUlZFKScsXG5cdGljb246ICd2aWRlby1hbHQzJyxcblx0Y2F0ZWdvcnk6ICdlbWJlZCcsXG5cblx0Lypcblx0ICogSW4gbW9zdCBvdGhlciBibG9ja3MsIHlvdSdkIHNlZSBhbiAnYXR0cmlidXRlcycgcHJvcGVydHkgYmVpbmcgZGVmaW5lZCBoZXJlLlxuXHQgKiBXZSd2ZSBkZWZpbmVkIGF0dHJpYnV0ZXMgaW4gdGhlIFBIUCwgdGhhdCBpbmZvcm1hdGlvbiBpcyBhdXRvbWF0aWNhbGx5IHNlbnRcblx0ICogdG8gdGhlIGJsb2NrIGVkaXRvciwgc28gd2UgZG9uJ3QgbmVlZCB0byByZWRlZmluZSBpdCBoZXJlLlxuXHQgKi9cblxuXHRlZGl0OiAoIHByb3BzICkgPT4ge1xuXHRcdGNvbnN0IGNvbnRyb2xzID0gQnVpbGRDb250cm9scyggcHJvcHMgKTtcblxuXHRcdHJldHVybiBbXG5cblx0XHRcdC8qXG5cdFx0XHQgKiBUaGUgU2VydmVyU2lkZVJlbmRlciBlbGVtZW50IHVzZXMgdGhlIFJFU1QgQVBJIHRvIGF1dG9tYXRpY2FsbHkgY2FsbFxuXHRcdFx0ICogcGhwX2Jsb2NrX3JlbmRlcigpIGluIHlvdXIgUEhQIGNvZGUgd2hlbmV2ZXIgaXQgbmVlZHMgdG8gZ2V0IGFuIHVwZGF0ZWRcblx0XHRcdCAqIHZpZXcgb2YgdGhlIGJsb2NrLlxuXHRcdFx0ICovXG5cdFx0XHRlbCggd3AuY29tcG9uZW50cy5TZXJ2ZXJTaWRlUmVuZGVyLCB7XG5cdFx0XHRcdGJsb2NrOiAnbmV4dGdlbnRoZW1lcy9hcnZlLWJsb2NrJyxcblx0XHRcdFx0YXR0cmlidXRlczogcHJvcHMuYXR0cmlidXRlcyxcblx0XHRcdH0gKSxcblxuXHRcdFx0Lypcblx0XHRcdCAqIEluc3BlY3RvckNvbnRyb2xzIGxldHMgeW91IGFkZCBjb250cm9scyB0byB0aGUgQmxvY2sgc2lkZWJhci4gSW4gdGhpcyBjYXNlLFxuXHRcdFx0ICogd2UncmUgYWRkaW5nIGEgVGV4dENvbnRyb2wsIHdoaWNoIGxldHMgdXMgZWRpdCB0aGUgJ2ZvbycgYXR0cmlidXRlICh3aGljaFxuXHRcdFx0ICogd2UgZGVmaW5lZCBpbiB0aGUgUEhQKS4gVGhlIG9uQ2hhbmdlIHByb3BlcnR5IGlzIGEgbGl0dGxlIGJpdCBvZiBtYWdpYyB0byB0ZWxsXG5cdFx0XHQgKiB0aGUgYmxvY2sgZWRpdG9yIHRvIHVwZGF0ZSB0aGUgdmFsdWUgb2Ygb3VyICdmb28nIHByb3BlcnR5LCBhbmQgdG8gcmUtcmVuZGVyXG5cdFx0XHQgKiB0aGUgYmxvY2suXG5cdFx0XHQgKi9cblx0XHRcdGVsKCB3cC5lZGl0b3IuSW5zcGVjdG9yQ29udHJvbHMsIHt9LCAuLi5jb250cm9scyApLFxuXHRcdF07XG5cdH0sXG5cblx0Ly8gV2UncmUgZ29pbmcgdG8gYmUgcmVuZGVyaW5nIGluIFBIUCwgc28gc2F2ZSgpIGNhbiBqdXN0IHJldHVybiBudWxsLlxuXHRzYXZlOiAoKSA9PiB7XG5cdFx0cmV0dXJuIG51bGw7XG5cdH0sXG59ICk7XG4iXSwic291cmNlUm9vdCI6IiJ9\n//# sourceURL=webpack-internal:///./resources/js/gb-block.js\n");

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