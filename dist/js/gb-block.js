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

eval("function _toConsumableArray(arr) { return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _nonIterableSpread(); }\n\nfunction _nonIterableSpread() { throw new TypeError(\"Invalid attempt to spread non-iterable instance\"); }\n\nfunction _iterableToArray(iter) { if (Symbol.iterator in Object(iter) || Object.prototype.toString.call(iter) === \"[object Arguments]\") return Array.from(iter); }\n\nfunction _arrayWithoutHoles(arr) { if (Array.isArray(arr)) { for (var i = 0, arr2 = new Array(arr.length); i < arr.length; i++) { arr2[i] = arr[i]; } return arr2; } }\n\nfunction _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }\n\n/**\n * Copyright (c) 2019 Nicolas Jonas\n * License: GPL 3.0\n *\n * Based on: https://gist.github.com/pento/cf38fd73ce0f13fcf0f0ae7d6c4b685d\n * Copyright (c) 2019 Gary Pendergast\n * License: GPL 2.0+\n */\nvar wp = window.wp;\nvar el = window.wp.element.createElement;\nwp.data.dispatch('core/edit-post').hideBlockTypes(['core-embed/youtube', 'core-embed/vimeo', 'core-embed/dailymotion', 'core-embed/collegehumor', 'core-embed/ted']);\n/*\n * Keypair to gutenberg component\n */\n\nfunction PrepareSelectOptions(options) {\n  var gboptions = [];\n  Object.keys(options).forEach(function (key) {\n    gboptions.push({\n      label: options[key],\n      value: key\n    });\n  });\n  return gboptions;\n}\n\nfunction BuildControls(props) {\n  console.log('props', props);\n  var controls = [];\n  Object.keys(window.ARVEsettings).forEach(function (key) {\n    var option = window.ARVEsettings[key];\n    var cArgs = {\n      label: option.label,\n      //help: option.description,\n      onChange: function onChange(value) {\n        if ('url' === key) {\n          var parser = new DOMParser();\n          var $iframe = parser.parseFromString(value, 'text/html').querySelector('iframe');\n\n          if ($iframe.src) {\n            value = $iframe.src;\n            var w = $iframe.width;\n            var h = $iframe.height;\n\n            if (w && h) {\n              props.setAttributes({\n                aspect_ratio: aspectRatio(w, h)\n              });\n            }\n          }\n        }\n\n        props.setAttributes(_defineProperty({}, key, value));\n      }\n    };\n\n    if ('bool+default' === option.type) {\n      option.type = 'select';\n    }\n\n    switch (option.type) {\n      case 'TODOboolean':\n        if (typeof props.attributes[key] !== 'undefined') {\n          cArgs.selected = props.attributes[key];\n        }\n\n        cArgs.value = props.attributes[key];\n        controls.push(el(wp.components.ToggleControl, cArgs));\n        break;\n\n      case 'select':\n        cArgs.options = PrepareSelectOptions(option.options);\n\n        if (typeof props.attributes[key] !== 'undefined') {\n          cArgs.selected = props.attributes[key];\n        }\n\n        cArgs.value = props.attributes[key];\n        controls.push(el(wp.components.SelectControl, cArgs));\n        break;\n\n      case 'string':\n        cArgs.value = props.attributes[key];\n        controls.push(el(wp.components.TextControl, cArgs));\n        break;\n    }\n  });\n  return controls;\n}\n/*\n * Here's where we register the block in JavaScript.\n *\n * It's not yet possible to register a block entirely without JavaScript, but\n * that is something I'd love to see happen. This is a barebones example\n * of registering the block, and giving the basic ability to edit the block\n * attributes. (In this case, there's only one attribute, 'foo'.)\n */\n\n\nwp.blocks.registerBlockType('nextgenthemes/arve-block', {\n  title: 'Video Embed (ARVE)',\n  icon: 'video-alt3',\n  category: 'embed',\n\n  /*\n   * In most other blocks, you'd see an 'attributes' property being defined here.\n   * We've defined attributes in the PHP, that information is automatically sent\n   * to the block editor, so we don't need to redefine it here.\n   */\n  edit: function edit(props) {\n    var controls = BuildControls(props);\n    return [\n    /*\n     * The ServerSideRender element uses the REST API to automatically call\n     * php_block_render() in your PHP code whenever it needs to get an updated\n     * view of the block.\n     */\n    el(wp.components.ServerSideRender, {\n      block: 'nextgenthemes/arve-block',\n      attributes: props.attributes\n    }),\n    /*\n     * InspectorControls lets you add controls to the Block sidebar. In this case,\n     * we're adding a TextControl, which lets us edit the 'foo' attribute (which\n     * we defined in the PHP). The onChange property is a little bit of magic to tell\n     * the block editor to update the value of our 'foo' property, and to re-render\n     * the block.\n     */\n    el.apply(void 0, [wp.editor.InspectorControls, {}].concat(_toConsumableArray(controls)))];\n  },\n  // We're going to be rendering in PHP, so save() can just return null.\n  save: function save() {\n    return null;\n  }\n});\n\nfunction aspectRatio(w, h) {\n  var arGCD = gcd(w, h);\n  return w / arGCD + ':' + h / arGCD;\n}\n\nfunction gcd(a, b) {\n  if (!b) {\n    return a;\n  }\n\n  return gcd(b, a % b);\n}//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9yZXNvdXJjZXMvanMvZ2ItYmxvY2suanM/ZTVmNiJdLCJuYW1lcyI6WyJ3cCIsIndpbmRvdyIsImVsIiwiZWxlbWVudCIsImNyZWF0ZUVsZW1lbnQiLCJkYXRhIiwiZGlzcGF0Y2giLCJoaWRlQmxvY2tUeXBlcyIsIlByZXBhcmVTZWxlY3RPcHRpb25zIiwib3B0aW9ucyIsImdib3B0aW9ucyIsIk9iamVjdCIsImtleXMiLCJmb3JFYWNoIiwia2V5IiwicHVzaCIsImxhYmVsIiwidmFsdWUiLCJCdWlsZENvbnRyb2xzIiwicHJvcHMiLCJjb25zb2xlIiwibG9nIiwiY29udHJvbHMiLCJBUlZFc2V0dGluZ3MiLCJvcHRpb24iLCJjQXJncyIsIm9uQ2hhbmdlIiwicGFyc2VyIiwiRE9NUGFyc2VyIiwiJGlmcmFtZSIsInBhcnNlRnJvbVN0cmluZyIsInF1ZXJ5U2VsZWN0b3IiLCJzcmMiLCJ3Iiwid2lkdGgiLCJoIiwiaGVpZ2h0Iiwic2V0QXR0cmlidXRlcyIsImFzcGVjdF9yYXRpbyIsImFzcGVjdFJhdGlvIiwidHlwZSIsImF0dHJpYnV0ZXMiLCJzZWxlY3RlZCIsImNvbXBvbmVudHMiLCJUb2dnbGVDb250cm9sIiwiU2VsZWN0Q29udHJvbCIsIlRleHRDb250cm9sIiwiYmxvY2tzIiwicmVnaXN0ZXJCbG9ja1R5cGUiLCJ0aXRsZSIsImljb24iLCJjYXRlZ29yeSIsImVkaXQiLCJTZXJ2ZXJTaWRlUmVuZGVyIiwiYmxvY2siLCJlZGl0b3IiLCJJbnNwZWN0b3JDb250cm9scyIsInNhdmUiLCJhckdDRCIsImdjZCIsImEiLCJiIl0sIm1hcHBpbmdzIjoiOzs7Ozs7Ozs7O0FBQUE7Ozs7Ozs7O0FBU0EsSUFBTUEsRUFBRSxHQUFHQyxNQUFNLENBQUNELEVBQWxCO0FBQ0EsSUFBTUUsRUFBRSxHQUFHRCxNQUFNLENBQUNELEVBQVAsQ0FBVUcsT0FBVixDQUFrQkMsYUFBN0I7QUFFQUosRUFBRSxDQUFDSyxJQUFILENBQVFDLFFBQVIs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QWlDLEVBQWpDLDRCQUF3Q2xDLFFBQXhDLEdBbkJNLENBQVA7QUFxQkEsR0FuQ3VEO0FBcUN4RDtBQUNBbUMsTUFBSSxFQUFFLGdCQUFNO0FBQ1gsV0FBTyxJQUFQO0FBQ0E7QUF4Q3VELENBQXpEOztBQTJDQSxTQUFTbEIsV0FBVCxDQUFzQk4sQ0FBdEIsRUFBeUJFLENBQXpCLEVBQTZCO0FBRTVCLE1BQU11QixLQUFLLEdBQUdDLEdBQUcsQ0FBRTFCLENBQUYsRUFBS0UsQ0FBTCxDQUFqQjtBQUVBLFNBQVNGLENBQUMsR0FBR3lCLEtBQU4sR0FBZ0IsR0FBaEIsR0FBeUJ2QixDQUFDLEdBQUd1QixLQUFwQztBQUNBOztBQUVELFNBQVNDLEdBQVQsQ0FBY0MsQ0FBZCxFQUFpQkMsQ0FBakIsRUFBcUI7QUFFcEIsTUFBSyxDQUFFQSxDQUFQLEVBQVc7QUFDVixXQUFPRCxDQUFQO0FBQ0E7O0FBRUQsU0FBT0QsR0FBRyxDQUFFRSxDQUFGLEVBQUtELENBQUMsR0FBR0MsQ0FBVCxDQUFWO0FBQ0EiLCJmaWxlIjoiLi9yZXNvdXJjZXMvanMvZ2ItYmxvY2suanMuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKipcbiAqIENvcHlyaWdodCAoYykgMjAxOSBOaWNvbGFzIEpvbmFzXG4gKiBMaWNlbnNlOiBHUEwgMy4wXG4gKlxuICogQmFzZWQgb246IGh0dHBzOi8vZ2lzdC5naXRodWIuY29tL3BlbnRvL2NmMzhmZDczY2UwZjEzZmNmMGYwYWU3ZDZjNGI2ODVkXG4gKiBDb3B5cmlnaHQgKGMpIDIwMTkgR2FyeSBQZW5kZXJnYXN0XG4gKiBMaWNlbnNlOiBHUEwgMi4wK1xuICovXG5cbmNvbnN0IHdwID0gd2luZG93LndwO1xuY29uc3QgZWwgPSB3aW5kb3cud3AuZWxlbWVudC5jcmVhdGVFbGVtZW50O1xuXG53cC5kYXRhLmRpc3BhdGNoKCAnY29yZS9lZGl0LXBvc3QnICkuaGlkZUJsb2NrVHlwZXMoIFtcblx0J2NvcmUtZW1iZWQveW91dHViZScsXG5cdCdjb3JlLWVtYmVkL3ZpbWVvJyxcblx0J2NvcmUtZW1iZWQvZGFpbHltb3Rpb24nLFxuXHQnY29yZS1lbWJlZC9jb2xsZWdlaHVtb3InLFxuXHQnY29yZS1lbWJlZC90ZWQnLFxuXSApO1xuXG4vKlxuICogS2V5cGFpciB0byBndXRlbmJlcmcgY29tcG9uZW50XG4gKi9cbmZ1bmN0aW9uIFByZXBhcmVTZWxlY3RPcHRpb25zKCBvcHRpb25zICkge1xuXHRjb25zdCBnYm9wdGlvbnMgPSBbXTtcblxuXHRPYmplY3Qua2V5cyggb3B0aW9ucyApLmZvckVhY2goICgga2V5ICkgPT4ge1xuXHRcdGdib3B0aW9ucy5wdXNoKCB7XG5cdFx0XHRsYWJlbDogb3B0aW9uc1sga2V5IF0sXG5cdFx0XHR2YWx1ZToga2V5LFxuXHRcdH0gKTtcblx0fSApO1xuXG5cdHJldHVybiBnYm9wdGlvbnM7XG59XG5cbmZ1bmN0aW9uIEJ1aWxkQ29udHJvbHMoIHByb3BzICkge1xuXG5cdGNvbnNvbGUubG9nKCAncHJvcHMnLCBwcm9wcyApO1xuXG5cdGNvbnN0IGNvbnRyb2xzID0gW107XG5cblx0T2JqZWN0LmtleXMoIHdpbmRvdy5BUlZFc2V0dGluZ3MgKS5mb3JFYWNoKCAoIGtleSApID0+IHtcblx0XHRjb25zdCBvcHRpb24gPSB3aW5kb3cuQVJWRXNldHRpbmdzWyBrZXkgXTtcblx0XHRjb25zdCBjQXJncyAgPSB7XG5cdFx0XHRsYWJlbDogb3B0aW9uLmxhYmVsLFxuXHRcdFx0Ly9oZWxwOiBvcHRpb24uZGVzY3JpcHRpb24sXG5cdFx0XHRvbkNoYW5nZTogKCB2YWx1ZSApID0+IHtcblx0XHRcdFx0aWYgKCAndXJsJyA9PT0ga2V5ICkge1xuXHRcdFx0XHRcdGNvbnN0IHBhcnNlciAgPSBuZXcgRE9NUGFyc2VyKCk7XG5cdFx0XHRcdFx0Y29uc3QgJGlmcmFtZSA9IHBhcnNlci5wYXJzZUZyb21TdHJpbmcoIHZhbHVlLCAndGV4dC9odG1sJyApLnF1ZXJ5U2VsZWN0b3IoICdpZnJhbWUnICk7XG5cdFx0XHRcdFx0aWYgKCAkaWZyYW1lLnNyYyApIHtcblx0XHRcdFx0XHRcdHZhbHVlICAgPSAkaWZyYW1lLnNyYztcblx0XHRcdFx0XHRcdGNvbnN0IHcgPSAkaWZyYW1lLndpZHRoO1xuXHRcdFx0XHRcdFx0Y29uc3QgaCA9ICRpZnJhbWUuaGVpZ2h0O1xuXHRcdFx0XHRcdFx0aWYgKCB3ICYmIGggKSB7XG5cdFx0XHRcdFx0XHRcdHByb3BzLnNldEF0dHJpYnV0ZXMoIHsgYXNwZWN0X3JhdGlvOiBhc3BlY3RSYXRpbyggdywgaCApIH0gKTtcblx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHR9XG5cdFx0XHRcdH1cblx0XHRcdFx0cHJvcHMuc2V0QXR0cmlidXRlcyggeyBbIGtleSBdOiB2YWx1ZSB9ICk7XG5cdFx0XHR9LFxuXHRcdH07XG5cblx0XHRpZiAoICdib29sK2RlZmF1bHQnID09PSBvcHRpb24udHlwZSApIHtcblx0XHRcdG9wdGlvbi50eXBlID0gJ3NlbGVjdCc7XG5cdFx0fVxuXG5cdFx0c3dpdGNoICggb3B0aW9uLnR5cGUgKSB7XG5cdFx0XHRjYXNlICdUT0RPYm9vbGVhbic6XG5cdFx0XHRcdGlmICggdHlwZW9mIHByb3BzLmF0dHJpYnV0ZXNbIGtleSBdICE9PSAndW5kZWZpbmVkJyApIHtcblx0XHRcdFx0XHRjQXJncy5zZWxlY3RlZCA9IHByb3BzLmF0dHJpYnV0ZXNbIGtleSBdO1xuXHRcdFx0XHR9XG5cdFx0XHRcdGNBcmdzLnZhbHVlID0gcHJvcHMuYXR0cmlidXRlc1sga2V5IF07XG5cdFx0XHRcdGNvbnRyb2xzLnB1c2goIGVsKCB3cC5jb21wb25lbnRzLlRvZ2dsZUNvbnRyb2wsIGNBcmdzICkgKTtcblx0XHRcdFx0YnJlYWs7XG5cblx0XHRcdGNhc2UgJ3NlbGVjdCc6XG5cdFx0XHRcdGNBcmdzLm9wdGlvbnMgPSBQcmVwYXJlU2VsZWN0T3B0aW9ucyggb3B0aW9uLm9wdGlvbnMgKTtcblx0XHRcdFx0aWYgKCB0eXBlb2YgcHJvcHMuYXR0cmlidXRlc1sga2V5IF0gIT09ICd1bmRlZmluZWQnICkge1xuXHRcdFx0XHRcdGNBcmdzLnNlbGVjdGVkID0gcHJvcHMuYXR0cmlidXRlc1sga2V5IF07XG5cdFx0XHRcdH1cblx0XHRcdFx0Y0FyZ3MudmFsdWUgPSBwcm9wcy5hdHRyaWJ1dGVzWyBrZXkgXTtcblx0XHRcdFx0Y29udHJvbHMucHVzaCggZWwoIHdwLmNvbXBvbmVudHMuU2VsZWN0Q29udHJvbCwgY0FyZ3MgKSApO1xuXHRcdFx0XHRicmVhaztcblxuXHRcdFx0Y2FzZSAnc3RyaW5nJzpcblx0XHRcdFx0Y0FyZ3MudmFsdWUgPSBwcm9wcy5hdHRyaWJ1dGVzWyBrZXkgXTtcblx0XHRcdFx0Y29udHJvbHMucHVzaCggZWwoIHdwLmNvbXBvbmVudHMuVGV4dENvbnRyb2wsIGNBcmdzICkgKTtcblx0XHRcdFx0YnJlYWs7XG5cdFx0fVxuXHR9ICk7XG5cblx0cmV0dXJuIGNvbnRyb2xzO1xufVxuXG4vKlxuICogSGVyZSdzIHdoZXJlIHdlIHJlZ2lzdGVyIHRoZSBibG9jayBpbiBKYXZhU2NyaXB0LlxuICpcbiAqIEl0J3Mgbm90IHlldCBwb3NzaWJsZSB0byByZWdpc3RlciBhIGJsb2NrIGVudGlyZWx5IHdpdGhvdXQgSmF2YVNjcmlwdCwgYnV0XG4gKiB0aGF0IGlzIHNvbWV0aGluZyBJJ2QgbG92ZSB0byBzZWUgaGFwcGVuLiBUaGlzIGlzIGEgYmFyZWJvbmVzIGV4YW1wbGVcbiAqIG9mIHJlZ2lzdGVyaW5nIHRoZSBibG9jaywgYW5kIGdpdmluZyB0aGUgYmFzaWMgYWJpbGl0eSB0byBlZGl0IHRoZSBibG9ja1xuICogYXR0cmlidXRlcy4gKEluIHRoaXMgY2FzZSwgdGhlcmUncyBvbmx5IG9uZSBhdHRyaWJ1dGUsICdmb28nLilcbiAqL1xud3AuYmxvY2tzLnJlZ2lzdGVyQmxvY2tUeXBlKCAnbmV4dGdlbnRoZW1lcy9hcnZlLWJsb2NrJywge1xuXHR0aXRsZTogJ1ZpZGVvIEVtYmVkIChBUlZFKScsXG5cdGljb246ICd2aWRlby1hbHQzJyxcblx0Y2F0ZWdvcnk6ICdlbWJlZCcsXG5cblx0Lypcblx0ICogSW4gbW9zdCBvdGhlciBibG9ja3MsIHlvdSdkIHNlZSBhbiAnYXR0cmlidXRlcycgcHJvcGVydHkgYmVpbmcgZGVmaW5lZCBoZXJlLlxuXHQgKiBXZSd2ZSBkZWZpbmVkIGF0dHJpYnV0ZXMgaW4gdGhlIFBIUCwgdGhhdCBpbmZvcm1hdGlvbiBpcyBhdXRvbWF0aWNhbGx5IHNlbnRcblx0ICogdG8gdGhlIGJsb2NrIGVkaXRvciwgc28gd2UgZG9uJ3QgbmVlZCB0byByZWRlZmluZSBpdCBoZXJlLlxuXHQgKi9cblxuXHRlZGl0OiAoIHByb3BzICkgPT4ge1xuXHRcdGNvbnN0IGNvbnRyb2xzID0gQnVpbGRDb250cm9scyggcHJvcHMgKTtcblxuXHRcdHJldHVybiBbXG5cblx0XHRcdC8qXG5cdFx0XHQgKiBUaGUgU2VydmVyU2lkZVJlbmRlciBlbGVtZW50IHVzZXMgdGhlIFJFU1QgQVBJIHRvIGF1dG9tYXRpY2FsbHkgY2FsbFxuXHRcdFx0ICogcGhwX2Jsb2NrX3JlbmRlcigpIGluIHlvdXIgUEhQIGNvZGUgd2hlbmV2ZXIgaXQgbmVlZHMgdG8gZ2V0IGFuIHVwZGF0ZWRcblx0XHRcdCAqIHZpZXcgb2YgdGhlIGJsb2NrLlxuXHRcdFx0ICovXG5cdFx0XHRlbCggd3AuY29tcG9uZW50cy5TZXJ2ZXJTaWRlUmVuZGVyLCB7XG5cdFx0XHRcdGJsb2NrOiAnbmV4dGdlbnRoZW1lcy9hcnZlLWJsb2NrJyxcblx0XHRcdFx0YXR0cmlidXRlczogcHJvcHMuYXR0cmlidXRlcyxcblx0XHRcdH0gKSxcblxuXHRcdFx0Lypcblx0XHRcdCAqIEluc3BlY3RvckNvbnRyb2xzIGxldHMgeW91IGFkZCBjb250cm9scyB0byB0aGUgQmxvY2sgc2lkZWJhci4gSW4gdGhpcyBjYXNlLFxuXHRcdFx0ICogd2UncmUgYWRkaW5nIGEgVGV4dENvbnRyb2wsIHdoaWNoIGxldHMgdXMgZWRpdCB0aGUgJ2ZvbycgYXR0cmlidXRlICh3aGljaFxuXHRcdFx0ICogd2UgZGVmaW5lZCBpbiB0aGUgUEhQKS4gVGhlIG9uQ2hhbmdlIHByb3BlcnR5IGlzIGEgbGl0dGxlIGJpdCBvZiBtYWdpYyB0byB0ZWxsXG5cdFx0XHQgKiB0aGUgYmxvY2sgZWRpdG9yIHRvIHVwZGF0ZSB0aGUgdmFsdWUgb2Ygb3VyICdmb28nIHByb3BlcnR5LCBhbmQgdG8gcmUtcmVuZGVyXG5cdFx0XHQgKiB0aGUgYmxvY2suXG5cdFx0XHQgKi9cblx0XHRcdGVsKCB3cC5lZGl0b3IuSW5zcGVjdG9yQ29udHJvbHMsIHt9LCAuLi5jb250cm9scyApLFxuXHRcdF07XG5cdH0sXG5cblx0Ly8gV2UncmUgZ29pbmcgdG8gYmUgcmVuZGVyaW5nIGluIFBIUCwgc28gc2F2ZSgpIGNhbiBqdXN0IHJldHVybiBudWxsLlxuXHRzYXZlOiAoKSA9PiB7XG5cdFx0cmV0dXJuIG51bGw7XG5cdH0sXG59ICk7XG5cbmZ1bmN0aW9uIGFzcGVjdFJhdGlvKCB3LCBoICkge1xuXG5cdGNvbnN0IGFyR0NEID0gZ2NkKCB3LCBoICk7XG5cblx0cmV0dXJuICggdyAvIGFyR0NEICkgKyAnOicgKyAgKCBoIC8gYXJHQ0QgKTtcbn1cblxuZnVuY3Rpb24gZ2NkKCBhLCBiICkge1xuXG5cdGlmICggISBiICkge1xuXHRcdHJldHVybiBhO1xuXHR9XG5cblx0cmV0dXJuIGdjZCggYiwgYSAlIGIgKTtcbn1cbiJdLCJzb3VyY2VSb290IjoiIn0=\n//# sourceURL=webpack-internal:///./resources/js/gb-block.js\n");

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