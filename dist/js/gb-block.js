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

eval("function _toConsumableArray(arr) { return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _nonIterableSpread(); }\n\nfunction _nonIterableSpread() { throw new TypeError(\"Invalid attempt to spread non-iterable instance\"); }\n\nfunction _iterableToArray(iter) { if (Symbol.iterator in Object(iter) || Object.prototype.toString.call(iter) === \"[object Arguments]\") return Array.from(iter); }\n\nfunction _arrayWithoutHoles(arr) { if (Array.isArray(arr)) { for (var i = 0, arr2 = new Array(arr.length); i < arr.length; i++) { arr2[i] = arr[i]; } return arr2; } }\n\nfunction _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }\n\n/**\n * Copyright (c) 2019 Nicolas Jonas\n * License: GPL 3.0\n *\n * Based on: https://gist.github.com/pento/cf38fd73ce0f13fcf0f0ae7d6c4b685d\n * Copyright (c) 2019 Gary Pendergast\n * License: GPL 2.0+\n */\nvar wp = window.wp;\nvar el = window.wp.element.createElement;\nwp.data.dispatch('core/edit-post').hideBlockTypes(['core-embed/youtube', 'core-embed/vimeo', 'core-embed/dailymotion', 'core-embed/collegehumor', 'core-embed/ted']);\n/*\n * Keypair to gutenberg component\n */\n\nfunction PrepareOptions(options) {\n  var gboptions = [];\n  Object.keys(options).forEach(function (key) {\n    gboptions.push({\n      label: options[key],\n      value: key\n    });\n  });\n  return gboptions;\n}\n\nfunction BuildControls(props) {\n  var controls = [];\n  Object.keys(window.ARVEsettings).forEach(function (key) {\n    var opt = window.ARVEsettings[key];\n    var cArgs = {\n      label: opt.label,\n      //help: opt.description,\n      onChange: function onChange(value) {\n        if ('url' === key) {\n          var srcMatch = value.match(/<iframe[^>]+src=\"([^\\s\"]+)/);\n\n          if (srcMatch && srcMatch[1]) {\n            value = srcMatch[1];\n          }\n        }\n\n        props.setAttributes(_defineProperty({}, key, value));\n      }\n    };\n\n    if ('bool+default' === opt.type) {\n      opt.type = 'select';\n    }\n\n    switch (opt.type) {\n      case 'boolean':\n        cArgs.onChange = function (value) {\n          props.setAttributes(_defineProperty({}, key, value));\n        };\n\n        controls.push(el(wp.components.CheckboxControl, cArgs));\n        break;\n\n      case 'select':\n        cArgs.options = PrepareOptions(opt.options);\n        cArgs.selected = props.attributes[key];\n        controls.push(el(wp.components.SelectControl, cArgs));\n        break;\n\n      case 'string':\n        cArgs.value = props.attributes[key];\n        controls.push(el(wp.components.TextControl, cArgs));\n        break;\n    }\n  });\n  return controls;\n}\n/*\n * Here's where we register the block in JavaScript.\n *\n * It's not yet possible to register a block entirely without JavaScript, but\n * that is something I'd love to see happen. This is a barebones example\n * of registering the block, and giving the basic ability to edit the block\n * attributes. (In this case, there's only one attribute, 'foo'.)\n */\n\n\nwp.blocks.registerBlockType('nextgenthemes/arve-block', {\n  title: 'Video Embed (ARVE)',\n  icon: 'video-alt3',\n  category: 'embed',\n\n  /*\n   * In most other blocks, you'd see an 'attributes' property being defined here.\n   * We've defined attributes in the PHP, that information is automatically sent\n   * to the block editor, so we don't need to redefine it here.\n   */\n  edit: function edit(props) {\n    var controls = BuildControls(props);\n    return [\n    /*\n     * The ServerSideRender element uses the REST API to automatically call\n     * php_block_render() in your PHP code whenever it needs to get an updated\n     * view of the block.\n     */\n    el(wp.components.ServerSideRender, {\n      block: 'nextgenthemes/arve-block',\n      attributes: props.attributes\n    }),\n    /*\n     * InspectorControls lets you add controls to the Block sidebar. In this case,\n     * we're adding a TextControl, which lets us edit the 'foo' attribute (which\n     * we defined in the PHP). The onChange property is a little bit of magic to tell\n     * the block editor to update the value of our 'foo' property, and to re-render\n     * the block.\n     */\n    el.apply(void 0, [wp.editor.InspectorControls, {}].concat(_toConsumableArray(controls)))];\n  },\n  // We're going to be rendering in PHP, so save() can just return null.\n  save: function save() {\n    return null;\n  }\n});//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9yZXNvdXJjZXMvanMvZ2ItYmxvY2suanM/ZTVmNiJdLCJuYW1lcyI6WyJ3cCIsIndpbmRvdyIsImVsIiwiZWxlbWVudCIsImNyZWF0ZUVsZW1lbnQiLCJkYXRhIiwiZGlzcGF0Y2giLCJoaWRlQmxvY2tUeXBlcyIsIlByZXBhcmVPcHRpb25zIiwib3B0aW9ucyIsImdib3B0aW9ucyIsIk9iamVjdCIsImtleXMiLCJmb3JFYWNoIiwia2V5IiwicHVzaCIsImxhYmVsIiwidmFsdWUiLCJCdWlsZENvbnRyb2xzIiwicHJvcHMiLCJjb250cm9scyIsIkFSVkVzZXR0aW5ncyIsIm9wdCIsImNBcmdzIiwib25DaGFuZ2UiLCJzcmNNYXRjaCIsIm1hdGNoIiwic2V0QXR0cmlidXRlcyIsInR5cGUiLCJjb21wb25lbnRzIiwiQ2hlY2tib3hDb250cm9sIiwic2VsZWN0ZWQiLCJhdHRyaWJ1dGVzIiwiU2VsZWN0Q29udHJvbCIsIlRleHRDb250cm9sIiwiYmxvY2tzIiwicmVnaXN0ZXJCbG9ja1R5cGUiLCJ0aXRsZSIsImljb24iLCJjYXRlZ29yeSIsImVkaXQiLCJTZXJ2ZXJTaWRlUmVuZGVyIiwiYmxvY2siLCJlZGl0b3IiLCJJbnNwZWN0b3JDb250cm9scyIsInNhdmUiXSwibWFwcGluZ3MiOiI7Ozs7Ozs7Ozs7QUFBQTs7Ozs7Ozs7QUFTQSxJQUFNQSxFQUFFLEdBQUdDLE1BQU0sQ0FBQ0QsRUFBbEI7QUFDQSxJQUFNRSxFQUFFLEdBQUdELE1BQU0sQ0FBQ0QsRUFBUCxDQUFVRyxPQUFWLENBQWtCQyxhQUE3QjtBQUVBSixFQUFFLENBQUNLLElBQUgsQ0FBUUMsUUFBUixDQUFrQixnQkFBbEIsRUFBcUNDLGNBQXJDLENBQXFELENBQ3BELG9CQURvRCxFQUVwRCxrQkFGb0QsRUFHcEQsd0JBSG9ELEVBSXBELHlCQUpvRCxFQUtwRCxnQkFMb0QsQ0FBckQ7QUFRQTs7OztBQUdBLFNBQVNDLGNBQVQsQ0FBeUJDLE9BQXpCLEVBQW1DO0FBQ2xD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QUUsQ0FBQzZCLFVBQUgsQ0FBY1ksZ0JBQWhCLEVBQWtDO0FBQ25DQyxXQUFLLEVBQUUsMEJBRDRCO0FBRW5DVixnQkFBVSxFQUFFYixLQUFLLENBQUNhO0FBRmlCLEtBQWxDLENBUEk7QUFZTjs7Ozs7OztBQU9BOUIsTUFBRSxNQUFGLFVBQUlGLEVBQUUsQ0FBQzJDLE1BQUgsQ0FBVUMsaUJBQWQsRUFBaUMsRUFBakMsNEJBQXdDeEIsUUFBeEMsR0FuQk0sQ0FBUDtBQXFCQSxHQW5DdUQ7QUFxQ3hEO0FBQ0F5QixNQUFJLEVBQUUsZ0JBQU07QUFDWCxXQUFPLElBQVA7QUFDQTtBQXhDdUQsQ0FBekQiLCJmaWxlIjoiLi9yZXNvdXJjZXMvanMvZ2ItYmxvY2suanMuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKipcbiAqIENvcHlyaWdodCAoYykgMjAxOSBOaWNvbGFzIEpvbmFzXG4gKiBMaWNlbnNlOiBHUEwgMy4wXG4gKlxuICogQmFzZWQgb246IGh0dHBzOi8vZ2lzdC5naXRodWIuY29tL3BlbnRvL2NmMzhmZDczY2UwZjEzZmNmMGYwYWU3ZDZjNGI2ODVkXG4gKiBDb3B5cmlnaHQgKGMpIDIwMTkgR2FyeSBQZW5kZXJnYXN0XG4gKiBMaWNlbnNlOiBHUEwgMi4wK1xuICovXG5cbmNvbnN0IHdwID0gd2luZG93LndwO1xuY29uc3QgZWwgPSB3aW5kb3cud3AuZWxlbWVudC5jcmVhdGVFbGVtZW50O1xuXG53cC5kYXRhLmRpc3BhdGNoKCAnY29yZS9lZGl0LXBvc3QnICkuaGlkZUJsb2NrVHlwZXMoIFtcblx0J2NvcmUtZW1iZWQveW91dHViZScsXG5cdCdjb3JlLWVtYmVkL3ZpbWVvJyxcblx0J2NvcmUtZW1iZWQvZGFpbHltb3Rpb24nLFxuXHQnY29yZS1lbWJlZC9jb2xsZWdlaHVtb3InLFxuXHQnY29yZS1lbWJlZC90ZWQnLFxuXSApO1xuXG4vKlxuICogS2V5cGFpciB0byBndXRlbmJlcmcgY29tcG9uZW50XG4gKi9cbmZ1bmN0aW9uIFByZXBhcmVPcHRpb25zKCBvcHRpb25zICkge1xuXHRjb25zdCBnYm9wdGlvbnMgPSBbXTtcblxuXHRPYmplY3Qua2V5cyggb3B0aW9ucyApLmZvckVhY2goICgga2V5ICkgPT4ge1xuXHRcdGdib3B0aW9ucy5wdXNoKCB7XG5cdFx0XHRsYWJlbDogb3B0aW9uc1sga2V5IF0sXG5cdFx0XHR2YWx1ZToga2V5LFxuXHRcdH0gKTtcblx0fSApO1xuXG5cdHJldHVybiBnYm9wdGlvbnM7XG59XG5cbmZ1bmN0aW9uIEJ1aWxkQ29udHJvbHMoIHByb3BzICkge1xuXHRjb25zdCBjb250cm9scyA9IFtdO1xuXG5cdE9iamVjdC5rZXlzKCB3aW5kb3cuQVJWRXNldHRpbmdzICkuZm9yRWFjaCggKCBrZXkgKSA9PiB7XG5cdFx0Y29uc3Qgb3B0ID0gd2luZG93LkFSVkVzZXR0aW5nc1sga2V5IF07XG5cdFx0Y29uc3QgY0FyZ3MgPSB7XG5cdFx0XHRsYWJlbDogb3B0LmxhYmVsLFxuXG5cdFx0XHQvL2hlbHA6IG9wdC5kZXNjcmlwdGlvbixcblx0XHRcdG9uQ2hhbmdlOiAoIHZhbHVlICkgPT4ge1xuXG5cdFx0XHRcdGlmICggJ3VybCcgPT09IGtleSApIHtcblx0XHRcdFx0XHRjb25zdCBzcmNNYXRjaCA9IHZhbHVlLm1hdGNoKCAvPGlmcmFtZVtePl0rc3JjPVwiKFteXFxzXCJdKykvICk7XG5cblx0XHRcdFx0XHRpZiAoIHNyY01hdGNoICYmIHNyY01hdGNoWyAxIF0gKSB7XG5cblx0XHRcdFx0XHRcdHZhbHVlID0gc3JjTWF0Y2hbIDEgXTtcblx0XHRcdFx0XHR9XG5cdFx0XHRcdH1cblxuXHRcdFx0XHRwcm9wcy5zZXRBdHRyaWJ1dGVzKCB7IFsga2V5IF06IHZhbHVlIH0gKTtcblx0XHRcdH0sXG5cdFx0fTtcblxuXHRcdGlmICggJ2Jvb2wrZGVmYXVsdCcgPT09IG9wdC50eXBlICkge1xuXHRcdFx0b3B0LnR5cGUgPSAnc2VsZWN0Jztcblx0XHR9XG5cblx0XHRzd2l0Y2ggKCBvcHQudHlwZSApIHtcblx0XHRcdGNhc2UgJ2Jvb2xlYW4nOlxuXHRcdFx0XHRjQXJncy5vbkNoYW5nZSA9ICggdmFsdWUgKSA9PiB7XG5cdFx0XHRcdFx0cHJvcHMuc2V0QXR0cmlidXRlcyggeyBbIGtleSBdOiB2YWx1ZSB9ICk7XG5cdFx0XHRcdH07XG5cblx0XHRcdFx0Y29udHJvbHMucHVzaCggZWwoIHdwLmNvbXBvbmVudHMuQ2hlY2tib3hDb250cm9sLCBjQXJncyApICk7XG5cdFx0XHRcdGJyZWFrO1xuXG5cdFx0XHRjYXNlICdzZWxlY3QnOlxuXHRcdFx0XHRjQXJncy5vcHRpb25zID0gUHJlcGFyZU9wdGlvbnMoIG9wdC5vcHRpb25zICk7XG5cdFx0XHRcdGNBcmdzLnNlbGVjdGVkID0gcHJvcHMuYXR0cmlidXRlc1sga2V5IF07XG5cblx0XHRcdFx0Y29udHJvbHMucHVzaCggZWwoIHdwLmNvbXBvbmVudHMuU2VsZWN0Q29udHJvbCwgY0FyZ3MgKSApO1xuXHRcdFx0XHRicmVhaztcblxuXHRcdFx0Y2FzZSAnc3RyaW5nJzpcblx0XHRcdFx0Y0FyZ3MudmFsdWUgPSBwcm9wcy5hdHRyaWJ1dGVzWyBrZXkgXTtcblxuXHRcdFx0XHRjb250cm9scy5wdXNoKCBlbCggd3AuY29tcG9uZW50cy5UZXh0Q29udHJvbCwgY0FyZ3MgKSApO1xuXHRcdFx0XHRicmVhaztcblx0XHR9XG5cdH0gKTtcblxuXHRyZXR1cm4gY29udHJvbHM7XG59XG5cbi8qXG4gKiBIZXJlJ3Mgd2hlcmUgd2UgcmVnaXN0ZXIgdGhlIGJsb2NrIGluIEphdmFTY3JpcHQuXG4gKlxuICogSXQncyBub3QgeWV0IHBvc3NpYmxlIHRvIHJlZ2lzdGVyIGEgYmxvY2sgZW50aXJlbHkgd2l0aG91dCBKYXZhU2NyaXB0LCBidXRcbiAqIHRoYXQgaXMgc29tZXRoaW5nIEknZCBsb3ZlIHRvIHNlZSBoYXBwZW4uIFRoaXMgaXMgYSBiYXJlYm9uZXMgZXhhbXBsZVxuICogb2YgcmVnaXN0ZXJpbmcgdGhlIGJsb2NrLCBhbmQgZ2l2aW5nIHRoZSBiYXNpYyBhYmlsaXR5IHRvIGVkaXQgdGhlIGJsb2NrXG4gKiBhdHRyaWJ1dGVzLiAoSW4gdGhpcyBjYXNlLCB0aGVyZSdzIG9ubHkgb25lIGF0dHJpYnV0ZSwgJ2ZvbycuKVxuICovXG53cC5ibG9ja3MucmVnaXN0ZXJCbG9ja1R5cGUoICduZXh0Z2VudGhlbWVzL2FydmUtYmxvY2snLCB7XG5cdHRpdGxlOiAnVmlkZW8gRW1iZWQgKEFSVkUpJyxcblx0aWNvbjogJ3ZpZGVvLWFsdDMnLFxuXHRjYXRlZ29yeTogJ2VtYmVkJyxcblxuXHQvKlxuXHQgKiBJbiBtb3N0IG90aGVyIGJsb2NrcywgeW91J2Qgc2VlIGFuICdhdHRyaWJ1dGVzJyBwcm9wZXJ0eSBiZWluZyBkZWZpbmVkIGhlcmUuXG5cdCAqIFdlJ3ZlIGRlZmluZWQgYXR0cmlidXRlcyBpbiB0aGUgUEhQLCB0aGF0IGluZm9ybWF0aW9uIGlzIGF1dG9tYXRpY2FsbHkgc2VudFxuXHQgKiB0byB0aGUgYmxvY2sgZWRpdG9yLCBzbyB3ZSBkb24ndCBuZWVkIHRvIHJlZGVmaW5lIGl0IGhlcmUuXG5cdCAqL1xuXG5cdGVkaXQ6ICggcHJvcHMgKSA9PiB7XG5cdFx0Y29uc3QgY29udHJvbHMgPSBCdWlsZENvbnRyb2xzKCBwcm9wcyApO1xuXG5cdFx0cmV0dXJuIFtcblxuXHRcdFx0Lypcblx0XHRcdCAqIFRoZSBTZXJ2ZXJTaWRlUmVuZGVyIGVsZW1lbnQgdXNlcyB0aGUgUkVTVCBBUEkgdG8gYXV0b21hdGljYWxseSBjYWxsXG5cdFx0XHQgKiBwaHBfYmxvY2tfcmVuZGVyKCkgaW4geW91ciBQSFAgY29kZSB3aGVuZXZlciBpdCBuZWVkcyB0byBnZXQgYW4gdXBkYXRlZFxuXHRcdFx0ICogdmlldyBvZiB0aGUgYmxvY2suXG5cdFx0XHQgKi9cblx0XHRcdGVsKCB3cC5jb21wb25lbnRzLlNlcnZlclNpZGVSZW5kZXIsIHtcblx0XHRcdFx0YmxvY2s6ICduZXh0Z2VudGhlbWVzL2FydmUtYmxvY2snLFxuXHRcdFx0XHRhdHRyaWJ1dGVzOiBwcm9wcy5hdHRyaWJ1dGVzLFxuXHRcdFx0fSApLFxuXG5cdFx0XHQvKlxuXHRcdFx0ICogSW5zcGVjdG9yQ29udHJvbHMgbGV0cyB5b3UgYWRkIGNvbnRyb2xzIHRvIHRoZSBCbG9jayBzaWRlYmFyLiBJbiB0aGlzIGNhc2UsXG5cdFx0XHQgKiB3ZSdyZSBhZGRpbmcgYSBUZXh0Q29udHJvbCwgd2hpY2ggbGV0cyB1cyBlZGl0IHRoZSAnZm9vJyBhdHRyaWJ1dGUgKHdoaWNoXG5cdFx0XHQgKiB3ZSBkZWZpbmVkIGluIHRoZSBQSFApLiBUaGUgb25DaGFuZ2UgcHJvcGVydHkgaXMgYSBsaXR0bGUgYml0IG9mIG1hZ2ljIHRvIHRlbGxcblx0XHRcdCAqIHRoZSBibG9jayBlZGl0b3IgdG8gdXBkYXRlIHRoZSB2YWx1ZSBvZiBvdXIgJ2ZvbycgcHJvcGVydHksIGFuZCB0byByZS1yZW5kZXJcblx0XHRcdCAqIHRoZSBibG9jay5cblx0XHRcdCAqL1xuXHRcdFx0ZWwoIHdwLmVkaXRvci5JbnNwZWN0b3JDb250cm9scywge30sIC4uLmNvbnRyb2xzICksXG5cdFx0XTtcblx0fSxcblxuXHQvLyBXZSdyZSBnb2luZyB0byBiZSByZW5kZXJpbmcgaW4gUEhQLCBzbyBzYXZlKCkgY2FuIGp1c3QgcmV0dXJuIG51bGwuXG5cdHNhdmU6ICgpID0+IHtcblx0XHRyZXR1cm4gbnVsbDtcblx0fSxcbn0gKTtcbiJdLCJzb3VyY2VSb290IjoiIn0=\n//# sourceURL=webpack-internal:///./resources/js/gb-block.js\n");

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