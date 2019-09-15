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

eval("function _toConsumableArray(arr) { return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _nonIterableSpread(); }\n\nfunction _nonIterableSpread() { throw new TypeError(\"Invalid attempt to spread non-iterable instance\"); }\n\nfunction _iterableToArray(iter) { if (Symbol.iterator in Object(iter) || Object.prototype.toString.call(iter) === \"[object Arguments]\") return Array.from(iter); }\n\nfunction _arrayWithoutHoles(arr) { if (Array.isArray(arr)) { for (var i = 0, arr2 = new Array(arr.length); i < arr.length; i++) { arr2[i] = arr[i]; } return arr2; } }\n\nfunction _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }\n\n/**\n * Copyright (c) 2019 Nicolas Jonas\n * License: GPL 3.0\n *\n * Based on: https://gist.github.com/pento/cf38fd73ce0f13fcf0f0ae7d6c4b685d\n * Copyright (c) 2019 Gary Pendergast\n * License: GPL 2.0+\n */\nvar wp = window.wp;\nvar el = window.wp.element.createElement;\nwp.data.dispatch('core/edit-post').hideBlockTypes(['core-embed/youtube', 'core-embed/vimeo', 'core-embed/dailymotion', 'core-embed/collegehumor', 'core-embed/ted']);\n/*\n * Keypair to gutenberg component\n */\n\nfunction PrepareSelectOptions(options) {\n  var gboptions = [];\n  Object.keys(options).forEach(function (key) {\n    gboptions.push({\n      label: options[key],\n      value: key\n    });\n  });\n  return gboptions;\n}\n\nfunction BuildControls(props) {\n  var controls = [];\n  var domParser = new DOMParser();\n  Object.keys(window.ARVEsettings).forEach(function (key) {\n    var option = window.ARVEsettings[key];\n    var attrVal = props.attributes[key];\n    var ctrlArgs = {\n      label: option.label,\n      help: option.description,\n      onChange: function onChange(value) {\n        if ('url' === key) {\n          var $iframe = domParser.parseFromString(value, 'text/html').querySelector('iframe');\n\n          if ($iframe && $iframe.hasAttribute('src') && $iframe.getAttribute('src')) {\n            value = $iframe.src;\n            var w = $iframe.width;\n            var h = $iframe.height;\n\n            if (w && h) {\n              props.setAttributes({\n                aspect_ratio: aspectRatio(w, h)\n              });\n            }\n          }\n        }\n\n        props.setAttributes(_defineProperty({}, key, value));\n      }\n    };\n\n    if ('bool+default' === option.type) {\n      option.type = 'select';\n    }\n\n    switch (option.type) {\n      case 'boolean':\n        if (typeof attrVal !== 'undefined') {\n          ctrlArgs.checked = attrVal;\n        }\n\n        controls.push(el(wp.components.ToggleControl, ctrlArgs));\n        break;\n\n      case 'select':\n        if (typeof attrVal !== 'undefined') {\n          ctrlArgs.selected = attrVal;\n          ctrlArgs.value = attrVal;\n        }\n\n        ctrlArgs.options = PrepareSelectOptions(option.options);\n        controls.push(el(wp.components.SelectControl, ctrlArgs));\n        break;\n\n      case 'string':\n        if (typeof attrVal !== 'undefined') {\n          ctrlArgs.value = attrVal;\n        }\n\n        controls.push(el(wp.components.TextControl, ctrlArgs));\n        break;\n    }\n  });\n  return controls;\n}\n/*\n * Here's where we register the block in JavaScript.\n *\n * It's not yet possible to register a block entirely without JavaScript, but\n * that is something I'd love to see happen. This is a barebones example\n * of registering the block, and giving the basic ability to edit the block\n * attributes. (In this case, there's only one attribute, 'foo'.)\n */\n\n\nwp.blocks.registerBlockType('nextgenthemes/arve-block', {\n  title: 'Video Embed (ARVE)',\n  icon: 'video-alt3',\n  category: 'embed',\n\n  /*\n   * In most other blocks, you'd see an 'attributes' property being defined here.\n   * We've defined attributes in the PHP, that information is automatically sent\n   * to the block editor, so we don't need to redefine it here.\n   */\n  edit: function edit(props) {\n    var controls = BuildControls(props);\n    return [\n    /*\n     * The ServerSideRender element uses the REST API to automatically call\n     * php_block_render() in your PHP code whenever it needs to get an updated\n     * view of the block.\n     */\n    el(wp.components.ServerSideRender, {\n      block: 'nextgenthemes/arve-block',\n      attributes: props.attributes\n    }),\n    /*\n     * InspectorControls lets you add controls to the Block sidebar. In this case,\n     * we're adding a TextControl, which lets us edit the 'foo' attribute (which\n     * we defined in the PHP). The onChange property is a little bit of magic to tell\n     * the block editor to update the value of our 'foo' property, and to re-render\n     * the block.\n     */\n    el.apply(void 0, [wp.blockEditor.InspectorControls, {}].concat(_toConsumableArray(controls)))];\n  },\n  // We're going to be rendering in PHP, so save() can just return null.\n  save: function save() {\n    return null;\n  }\n});\n\nfunction aspectRatio(w, h) {\n  var arGCD = gcd(w, h);\n  return w / arGCD + ':' + h / arGCD;\n}\n\nfunction gcd(a, b) {\n  if (!b) {\n    return a;\n  }\n\n  return gcd(b, a % b);\n}//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9yZXNvdXJjZXMvanMvZ2ItYmxvY2suanM/ZTVmNiJdLCJuYW1lcyI6WyJ3cCIsIndpbmRvdyIsImVsIiwiZWxlbWVudCIsImNyZWF0ZUVsZW1lbnQiLCJkYXRhIiwiZGlzcGF0Y2giLCJoaWRlQmxvY2tUeXBlcyIsIlByZXBhcmVTZWxlY3RPcHRpb25zIiwib3B0aW9ucyIsImdib3B0aW9ucyIsIk9iamVjdCIsImtleXMiLCJmb3JFYWNoIiwia2V5IiwicHVzaCIsImxhYmVsIiwidmFsdWUiLCJCdWlsZENvbnRyb2xzIiwicHJvcHMiLCJjb250cm9scyIsImRvbVBhcnNlciIsIkRPTVBhcnNlciIsIkFSVkVzZXR0aW5ncyIsIm9wdGlvbiIsImF0dHJWYWwiLCJhdHRyaWJ1dGVzIiwiY3RybEFyZ3MiLCJoZWxwIiwiZGVzY3JpcHRpb24iLCJvbkNoYW5nZSIsIiRpZnJhbWUiLCJwYXJzZUZyb21TdHJpbmciLCJxdWVyeVNlbGVjdG9yIiwiaGFzQXR0cmlidXRlIiwiZ2V0QXR0cmlidXRlIiwic3JjIiwidyIsIndpZHRoIiwiaCIsImhlaWdodCIsInNldEF0dHJpYnV0ZXMiLCJhc3BlY3RfcmF0aW8iLCJhc3BlY3RSYXRpbyIsInR5cGUiLCJjaGVja2VkIiwiY29tcG9uZW50cyIsIlRvZ2dsZUNvbnRyb2wiLCJzZWxlY3RlZCIsIlNlbGVjdENvbnRyb2wiLCJUZXh0Q29udHJvbCIsImJsb2NrcyIsInJlZ2lzdGVyQmxvY2tUeXBlIiwidGl0bGUiLCJpY29uIiwiY2F0ZWdvcnkiLCJlZGl0IiwiU2VydmVyU2lkZVJlbmRlciIsImJsb2NrIiwiYmxvY2tFZGl0b3IiLCJJbnNwZWN0b3JDb250cm9scyIsInNhdmUiLCJhckdDRCIsImdjZCIsImEiLCJiIl0sIm1hcHBpbmdzIjoiOzs7Ozs7Ozs7O0FBQUE7Ozs7Ozs7O0FBU0EsSUFBTUEsRUFBRSxHQUFHQyxNQUFNLENBQUNELEVBQWxCO0FBQ0EsSUFBTUUsRUFBRSxHQUFHRCxNQUFNLENB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QyxRQUE3QyxHQW5CTSxDQUFQO0FBcUJBLEdBbkN1RDtBQXFDeEQ7QUFDQXlDLE1BQUksRUFBRSxnQkFBTTtBQUNYLFdBQU8sSUFBUDtBQUNBO0FBeEN1RCxDQUF6RDs7QUEyQ0EsU0FBU2xCLFdBQVQsQ0FBc0JOLENBQXRCLEVBQXlCRSxDQUF6QixFQUE2QjtBQUU1QixNQUFNdUIsS0FBSyxHQUFHQyxHQUFHLENBQUUxQixDQUFGLEVBQUtFLENBQUwsQ0FBakI7QUFFQSxTQUFTRixDQUFDLEdBQUd5QixLQUFOLEdBQWdCLEdBQWhCLEdBQXlCdkIsQ0FBQyxHQUFHdUIsS0FBcEM7QUFDQTs7QUFFRCxTQUFTQyxHQUFULENBQWNDLENBQWQsRUFBaUJDLENBQWpCLEVBQXFCO0FBRXBCLE1BQUssQ0FBRUEsQ0FBUCxFQUFXO0FBQ1YsV0FBT0QsQ0FBUDtBQUNBOztBQUVELFNBQU9ELEdBQUcsQ0FBRUUsQ0FBRixFQUFLRCxDQUFDLEdBQUdDLENBQVQsQ0FBVjtBQUNBIiwiZmlsZSI6Ii4vcmVzb3VyY2VzL2pzL2diLWJsb2NrLmpzLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyoqXG4gKiBDb3B5cmlnaHQgKGMpIDIwMTkgTmljb2xhcyBKb25hc1xuICogTGljZW5zZTogR1BMIDMuMFxuICpcbiAqIEJhc2VkIG9uOiBodHRwczovL2dpc3QuZ2l0aHViLmNvbS9wZW50by9jZjM4ZmQ3M2NlMGYxM2ZjZjBmMGFlN2Q2YzRiNjg1ZFxuICogQ29weXJpZ2h0IChjKSAyMDE5IEdhcnkgUGVuZGVyZ2FzdFxuICogTGljZW5zZTogR1BMIDIuMCtcbiAqL1xuXG5jb25zdCB3cCA9IHdpbmRvdy53cDtcbmNvbnN0IGVsID0gd2luZG93LndwLmVsZW1lbnQuY3JlYXRlRWxlbWVudDtcblxud3AuZGF0YS5kaXNwYXRjaCggJ2NvcmUvZWRpdC1wb3N0JyApLmhpZGVCbG9ja1R5cGVzKCBbXG5cdCdjb3JlLWVtYmVkL3lvdXR1YmUnLFxuXHQnY29yZS1lbWJlZC92aW1lbycsXG5cdCdjb3JlLWVtYmVkL2RhaWx5bW90aW9uJyxcblx0J2NvcmUtZW1iZWQvY29sbGVnZWh1bW9yJyxcblx0J2NvcmUtZW1iZWQvdGVkJyxcbl0gKTtcblxuLypcbiAqIEtleXBhaXIgdG8gZ3V0ZW5iZXJnIGNvbXBvbmVudFxuICovXG5mdW5jdGlvbiBQcmVwYXJlU2VsZWN0T3B0aW9ucyggb3B0aW9ucyApIHtcblx0Y29uc3QgZ2JvcHRpb25zID0gW107XG5cblx0T2JqZWN0LmtleXMoIG9wdGlvbnMgKS5mb3JFYWNoKCAoIGtleSApID0+IHtcblx0XHRnYm9wdGlvbnMucHVzaCgge1xuXHRcdFx0bGFiZWw6IG9wdGlvbnNbIGtleSBdLFxuXHRcdFx0dmFsdWU6IGtleSxcblx0XHR9ICk7XG5cdH0gKTtcblxuXHRyZXR1cm4gZ2JvcHRpb25zO1xufVxuXG5mdW5jdGlvbiBCdWlsZENvbnRyb2xzKCBwcm9wcyApIHtcblxuXHRjb25zdCBjb250cm9scyAgPSBbXTtcblx0Y29uc3QgZG9tUGFyc2VyID0gbmV3IERPTVBhcnNlcigpO1xuXG5cdE9iamVjdC5rZXlzKCB3aW5kb3cuQVJWRXNldHRpbmdzICkuZm9yRWFjaCggKCBrZXkgKSA9PiB7XG5cdFx0Y29uc3Qgb3B0aW9uICAgPSB3aW5kb3cuQVJWRXNldHRpbmdzWyBrZXkgXTtcblx0XHRjb25zdCBhdHRyVmFsICA9IHByb3BzLmF0dHJpYnV0ZXNbIGtleSBdO1xuXHRcdGNvbnN0IGN0cmxBcmdzID0ge1xuXHRcdFx0bGFiZWw6IG9wdGlvbi5sYWJlbCxcblx0XHRcdGhlbHA6IG9wdGlvbi5kZXNjcmlwdGlvbixcblx0XHRcdG9uQ2hhbmdlOiAoIHZhbHVlICkgPT4ge1xuXHRcdFx0XHRpZiAoICd1cmwnID09PSBrZXkgKSB7XG5cdFx0XHRcdFx0Y29uc3QgJGlmcmFtZSA9IGRvbVBhcnNlci5wYXJzZUZyb21TdHJpbmcoIHZhbHVlLCAndGV4dC9odG1sJyApLnF1ZXJ5U2VsZWN0b3IoICdpZnJhbWUnICk7XG5cdFx0XHRcdFx0aWYgKCAkaWZyYW1lICYmXG5cdFx0XHRcdFx0XHQkaWZyYW1lLmhhc0F0dHJpYnV0ZSggJ3NyYycgKSAmJlxuXHRcdFx0XHRcdFx0JGlmcmFtZS5nZXRBdHRyaWJ1dGUoICdzcmMnIClcblx0XHRcdFx0XHQpIHtcblx0XHRcdFx0XHRcdHZhbHVlICAgPSAkaWZyYW1lLnNyYztcblx0XHRcdFx0XHRcdGNvbnN0IHcgPSAkaWZyYW1lLndpZHRoO1xuXHRcdFx0XHRcdFx0Y29uc3QgaCA9ICRpZnJhbWUuaGVpZ2h0O1xuXHRcdFx0XHRcdFx0aWYgKCB3ICYmIGggKSB7XG5cdFx0XHRcdFx0XHRcdHByb3BzLnNldEF0dHJpYnV0ZXMoIHsgYXNwZWN0X3JhdGlvOiBhc3BlY3RSYXRpbyggdywgaCApIH0gKTtcblx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHR9XG5cdFx0XHRcdH1cblx0XHRcdFx0cHJvcHMuc2V0QXR0cmlidXRlcyggeyBbIGtleSBdOiB2YWx1ZSB9ICk7XG5cdFx0XHR9LFxuXHRcdH07XG5cblx0XHRpZiAoICdib29sK2RlZmF1bHQnID09PSBvcHRpb24udHlwZSApIHtcblx0XHRcdG9wdGlvbi50eXBlID0gJ3NlbGVjdCc7XG5cdFx0fVxuXG5cdFx0c3dpdGNoICggb3B0aW9uLnR5cGUgKSB7XG5cdFx0XHRjYXNlICdib29sZWFuJzpcblx0XHRcdFx0aWYgKCB0eXBlb2YgYXR0clZhbCAhPT0gJ3VuZGVmaW5lZCcgKSB7XG5cdFx0XHRcdFx0Y3RybEFyZ3MuY2hlY2tlZCA9IGF0dHJWYWw7XG5cdFx0XHRcdH1cblx0XHRcdFx0Y29udHJvbHMucHVzaCggZWwoIHdwLmNvbXBvbmVudHMuVG9nZ2xlQ29udHJvbCwgY3RybEFyZ3MgKSApO1xuXHRcdFx0XHRicmVhaztcblx0XHRcdGNhc2UgJ3NlbGVjdCc6XG5cdFx0XHRcdGlmICggdHlwZW9mIGF0dHJWYWwgIT09ICd1bmRlZmluZWQnICkge1xuXHRcdFx0XHRcdGN0cmxBcmdzLnNlbGVjdGVkID0gYXR0clZhbDtcblx0XHRcdFx0XHRjdHJsQXJncy52YWx1ZSA9IGF0dHJWYWw7XG5cdFx0XHRcdH1cblx0XHRcdFx0Y3RybEFyZ3Mub3B0aW9ucyA9IFByZXBhcmVTZWxlY3RPcHRpb25zKCBvcHRpb24ub3B0aW9ucyApO1xuXHRcdFx0XHRjb250cm9scy5wdXNoKCBlbCggd3AuY29tcG9uZW50cy5TZWxlY3RDb250cm9sLCBjdHJsQXJncyApICk7XG5cdFx0XHRcdGJyZWFrO1xuXHRcdFx0Y2FzZSAnc3RyaW5nJzpcblx0XHRcdFx0aWYgKCB0eXBlb2YgYXR0clZhbCAhPT0gJ3VuZGVmaW5lZCcgKSB7XG5cdFx0XHRcdFx0Y3RybEFyZ3MudmFsdWUgPSBhdHRyVmFsO1xuXHRcdFx0XHR9XG5cdFx0XHRcdGNvbnRyb2xzLnB1c2goIGVsKCB3cC5jb21wb25lbnRzLlRleHRDb250cm9sLCBjdHJsQXJncyApICk7XG5cdFx0XHRcdGJyZWFrO1xuXHRcdH1cblx0fSApO1xuXG5cdHJldHVybiBjb250cm9scztcbn1cblxuLypcbiAqIEhlcmUncyB3aGVyZSB3ZSByZWdpc3RlciB0aGUgYmxvY2sgaW4gSmF2YVNjcmlwdC5cbiAqXG4gKiBJdCdzIG5vdCB5ZXQgcG9zc2libGUgdG8gcmVnaXN0ZXIgYSBibG9jayBlbnRpcmVseSB3aXRob3V0IEphdmFTY3JpcHQsIGJ1dFxuICogdGhhdCBpcyBzb21ldGhpbmcgSSdkIGxvdmUgdG8gc2VlIGhhcHBlbi4gVGhpcyBpcyBhIGJhcmVib25lcyBleGFtcGxlXG4gKiBvZiByZWdpc3RlcmluZyB0aGUgYmxvY2ssIGFuZCBnaXZpbmcgdGhlIGJhc2ljIGFiaWxpdHkgdG8gZWRpdCB0aGUgYmxvY2tcbiAqIGF0dHJpYnV0ZXMuIChJbiB0aGlzIGNhc2UsIHRoZXJlJ3Mgb25seSBvbmUgYXR0cmlidXRlLCAnZm9vJy4pXG4gKi9cbndwLmJsb2Nrcy5yZWdpc3RlckJsb2NrVHlwZSggJ25leHRnZW50aGVtZXMvYXJ2ZS1ibG9jaycsIHtcblx0dGl0bGU6ICdWaWRlbyBFbWJlZCAoQVJWRSknLFxuXHRpY29uOiAndmlkZW8tYWx0MycsXG5cdGNhdGVnb3J5OiAnZW1iZWQnLFxuXG5cdC8qXG5cdCAqIEluIG1vc3Qgb3RoZXIgYmxvY2tzLCB5b3UnZCBzZWUgYW4gJ2F0dHJpYnV0ZXMnIHByb3BlcnR5IGJlaW5nIGRlZmluZWQgaGVyZS5cblx0ICogV2UndmUgZGVmaW5lZCBhdHRyaWJ1dGVzIGluIHRoZSBQSFAsIHRoYXQgaW5mb3JtYXRpb24gaXMgYXV0b21hdGljYWxseSBzZW50XG5cdCAqIHRvIHRoZSBibG9jayBlZGl0b3IsIHNvIHdlIGRvbid0IG5lZWQgdG8gcmVkZWZpbmUgaXQgaGVyZS5cblx0ICovXG5cblx0ZWRpdDogKCBwcm9wcyApID0+IHtcblx0XHRjb25zdCBjb250cm9scyA9IEJ1aWxkQ29udHJvbHMoIHByb3BzICk7XG5cblx0XHRyZXR1cm4gW1xuXG5cdFx0XHQvKlxuXHRcdFx0ICogVGhlIFNlcnZlclNpZGVSZW5kZXIgZWxlbWVudCB1c2VzIHRoZSBSRVNUIEFQSSB0byBhdXRvbWF0aWNhbGx5IGNhbGxcblx0XHRcdCAqIHBocF9ibG9ja19yZW5kZXIoKSBpbiB5b3VyIFBIUCBjb2RlIHdoZW5ldmVyIGl0IG5lZWRzIHRvIGdldCBhbiB1cGRhdGVkXG5cdFx0XHQgKiB2aWV3IG9mIHRoZSBibG9jay5cblx0XHRcdCAqL1xuXHRcdFx0ZWwoIHdwLmNvbXBvbmVudHMuU2VydmVyU2lkZVJlbmRlciwge1xuXHRcdFx0XHRibG9jazogJ25leHRnZW50aGVtZXMvYXJ2ZS1ibG9jaycsXG5cdFx0XHRcdGF0dHJpYnV0ZXM6IHByb3BzLmF0dHJpYnV0ZXMsXG5cdFx0XHR9ICksXG5cblx0XHRcdC8qXG5cdFx0XHQgKiBJbnNwZWN0b3JDb250cm9scyBsZXRzIHlvdSBhZGQgY29udHJvbHMgdG8gdGhlIEJsb2NrIHNpZGViYXIuIEluIHRoaXMgY2FzZSxcblx0XHRcdCAqIHdlJ3JlIGFkZGluZyBhIFRleHRDb250cm9sLCB3aGljaCBsZXRzIHVzIGVkaXQgdGhlICdmb28nIGF0dHJpYnV0ZSAod2hpY2hcblx0XHRcdCAqIHdlIGRlZmluZWQgaW4gdGhlIFBIUCkuIFRoZSBvbkNoYW5nZSBwcm9wZXJ0eSBpcyBhIGxpdHRsZSBiaXQgb2YgbWFnaWMgdG8gdGVsbFxuXHRcdFx0ICogdGhlIGJsb2NrIGVkaXRvciB0byB1cGRhdGUgdGhlIHZhbHVlIG9mIG91ciAnZm9vJyBwcm9wZXJ0eSwgYW5kIHRvIHJlLXJlbmRlclxuXHRcdFx0ICogdGhlIGJsb2NrLlxuXHRcdFx0ICovXG5cdFx0XHRlbCggd3AuYmxvY2tFZGl0b3IuSW5zcGVjdG9yQ29udHJvbHMsIHt9LCAuLi5jb250cm9scyApLFxuXHRcdF07XG5cdH0sXG5cblx0Ly8gV2UncmUgZ29pbmcgdG8gYmUgcmVuZGVyaW5nIGluIFBIUCwgc28gc2F2ZSgpIGNhbiBqdXN0IHJldHVybiBudWxsLlxuXHRzYXZlOiAoKSA9PiB7XG5cdFx0cmV0dXJuIG51bGw7XG5cdH0sXG59ICk7XG5cbmZ1bmN0aW9uIGFzcGVjdFJhdGlvKCB3LCBoICkge1xuXG5cdGNvbnN0IGFyR0NEID0gZ2NkKCB3LCBoICk7XG5cblx0cmV0dXJuICggdyAvIGFyR0NEICkgKyAnOicgKyAgKCBoIC8gYXJHQ0QgKTtcbn1cblxuZnVuY3Rpb24gZ2NkKCBhLCBiICkge1xuXG5cdGlmICggISBiICkge1xuXHRcdHJldHVybiBhO1xuXHR9XG5cblx0cmV0dXJuIGdjZCggYiwgYSAlIGIgKTtcbn1cbiJdLCJzb3VyY2VSb290IjoiIn0=\n//# sourceURL=webpack-internal:///./resources/js/gb-block.js\n");

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