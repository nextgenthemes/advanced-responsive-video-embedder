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
/******/ 	return __webpack_require__(__webpack_require__.s = 3);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/ts/test-block.ts":
/*!******************************!*\
  !*** ./src/ts/test-block.ts ***!
  \******************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
eval("\nexports.__esModule = true;\nvar el = window.wp.element.createElement, registerBlockType = window.wp.blocks.registerBlockType, ServerSideRender = window.wp.components.ServerSideRender, TextControl = window.wp.components.TextControl, ToggleControl = window.wp.components.ToggleControl, InspectorControls = window.wp.editor.InspectorControls;\nregisterBlockType('nextgenthemes/arve-block', {\n    title: 'PHP Block',\n    icon: 'megaphone',\n    category: 'widgets',\n    edit: function (props) {\n        return [\n            el(ServerSideRender, {\n                block: 'nextgenthemes/arve-block',\n                attributes: props.attributes,\n            }),\n            el(InspectorControls, {}, el(TextControl, {\n                label: 'Foo',\n                value: props.attributes.foo,\n                onChange: function (value) {\n                    props.setAttributes({ foo: value });\n                },\n            }), el(ToggleControl, {\n                label: 'Toogle',\n                value: props.attributes.toggle,\n                onChange: function (value) {\n                    props.setAttributes({ toggle: value });\n                },\n            })),\n        ];\n    },\n    save: function () {\n        return null;\n    },\n});\n//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9zcmMvdHMvdGVzdC1ibG9jay50cz9kNTkzIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiI7O0FBUUEsSUFBTSxFQUFFLEdBQUcsTUFBTSxDQUFDLEVBQUUsQ0FBQyxPQUFPLENBQUMsYUFBYSxFQUN6QyxpQkFBaUIsR0FBRyxNQUFNLENBQUMsRUFBRSxDQUFDLE1BQU0sQ0FBQyxpQkFBaUIsRUFDdEQsZ0JBQWdCLEdBQUcsTUFBTSxDQUFDLEVBQUUsQ0FBQyxVQUFVLENBQUMsZ0JBQWdCLEVBQ3hELFdBQVcsR0FBRyxNQUFNLENBQUMsRUFBRSxDQUFDLFVBQVUsQ0FBQyxXQUFXLEVBQzlDLGFBQWEsR0FBRyxNQUFNLENBQUMsRUFBRSxDQUFDLFVBQVUsQ0FBQyxhQUFhLEVBQ2xELGlCQUFpQixHQUFHLE1BQU0sQ0FBQyxFQUFFLENBQUMsTUFBTSxDQUFDLGlCQUFpQixDQUFDO0FBVXhELGlCQUFpQixDQUFDLDBCQUEwQixFQUFFO0lBQzdDLEtBQUssRUFBRSxXQUFXO0lBQ2xCLElBQUksRUFBRSxXQUFXO0lBQ2pCLFFBQVEsRUFBRSxTQUFTO0lBUW5CLElBQUksRUFBRSxVQUFDLEtBQUs7UUFDWCxPQUFPO1lBTU4sRUFBRSxDQUFDLGdCQUFnQixFQUFFO2dCQUNwQixLQUFLLEVBQUUsMEJBQTBCO2dCQUNqQyxVQUFVLEVBQUUsS0FBSyxDQUFDLFVBQVU7YUFDNUIsQ0FBQztZQVFGLEVBQUUsQ0FDRCxpQkFBaUIsRUFDakIsRUFBRSxFQUNGLEVBQUUsQ0FBQyxXQUFXLEVBQUU7Z0JBQ2YsS0FBSyxFQUFFLEtBQUs7Z0JBQ1osS0FBSyxFQUFFLEtBQUssQ0FBQyxVQUFVLENBQUMsR0FBRztnQkFDM0IsUUFBUSxFQUFFLFVBQUMsS0FBSztvQkFDZixLQUFLLENBQUMsYUFBYSxDQUFDLEVBQUUsR0FBRyxFQUFFLEtBQUssRUFBRSxDQUFDLENBQUM7Z0JBQ3JDLENBQUM7YUFDRCxDQUFDLEVBQ0YsRUFBRSxDQUFDLGFBQWEsRUFBRTtnQkFDakIsS0FBSyxFQUFFLFFBQVE7Z0JBQ2YsS0FBSyxFQUFFLEtBQUssQ0FBQyxVQUFVLENBQUMsTUFBTTtnQkFDOUIsUUFBUSxFQUFFLFVBQUMsS0FBSztvQkFDZixLQUFLLENBQUMsYUFBYSxDQUFDLEVBQUUsTUFBTSxFQUFFLEtBQUssRUFBRSxDQUFDLENBQUM7Z0JBQ3hDLENBQUM7YUFDRCxDQUFDLENBQ0Y7U0FDRCxDQUFDO0lBQ0gsQ0FBQztJQUdELElBQUksRUFBRTtRQUNMLE9BQU8sSUFBSSxDQUFDO0lBQ2IsQ0FBQztDQUNELENBQUMsQ0FBQyIsImZpbGUiOiIuL3NyYy90cy90ZXN0LWJsb2NrLnRzLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLy8gTGljZW5zZTogR1BMdjIrXG5leHBvcnQge307XG5kZWNsYXJlIGdsb2JhbCB7XG5cdGludGVyZmFjZSBXaW5kb3cge1xuXHRcdHdwO1xuXHR9XG59XG5cbmNvbnN0IGVsID0gd2luZG93LndwLmVsZW1lbnQuY3JlYXRlRWxlbWVudCxcblx0cmVnaXN0ZXJCbG9ja1R5cGUgPSB3aW5kb3cud3AuYmxvY2tzLnJlZ2lzdGVyQmxvY2tUeXBlLFxuXHRTZXJ2ZXJTaWRlUmVuZGVyID0gd2luZG93LndwLmNvbXBvbmVudHMuU2VydmVyU2lkZVJlbmRlcixcblx0VGV4dENvbnRyb2wgPSB3aW5kb3cud3AuY29tcG9uZW50cy5UZXh0Q29udHJvbCxcblx0VG9nZ2xlQ29udHJvbCA9IHdpbmRvdy53cC5jb21wb25lbnRzLlRvZ2dsZUNvbnRyb2wsXG5cdEluc3BlY3RvckNvbnRyb2xzID0gd2luZG93LndwLmVkaXRvci5JbnNwZWN0b3JDb250cm9scztcblxuLypcbiAqIEhlcmUncyB3aGVyZSB3ZSByZWdpc3RlciB0aGUgYmxvY2sgaW4gSmF2YVNjcmlwdC5cbiAqXG4gKiBJdCdzIG5vdCB5ZXQgcG9zc2libGUgdG8gcmVnaXN0ZXIgYSBibG9jayBlbnRpcmVseSB3aXRob3V0IEphdmFTY3JpcHQsIGJ1dFxuICogdGhhdCBpcyBzb21ldGhpbmcgSSdkIGxvdmUgdG8gc2VlIGhhcHBlbi4gVGhpcyBpcyBhIGJhcmVib25lcyBleGFtcGxlXG4gKiBvZiByZWdpc3RlcmluZyB0aGUgYmxvY2ssIGFuZCBnaXZpbmcgdGhlIGJhc2ljIGFiaWxpdHkgdG8gZWRpdCB0aGUgYmxvY2tcbiAqIGF0dHJpYnV0ZXMuIChJbiB0aGlzIGNhc2UsIHRoZXJlJ3Mgb25seSBvbmUgYXR0cmlidXRlLCAnZm9vJy4pXG4gKi9cbnJlZ2lzdGVyQmxvY2tUeXBlKCduZXh0Z2VudGhlbWVzL2FydmUtYmxvY2snLCB7XG5cdHRpdGxlOiAnUEhQIEJsb2NrJyxcblx0aWNvbjogJ21lZ2FwaG9uZScsXG5cdGNhdGVnb3J5OiAnd2lkZ2V0cycsXG5cblx0Lypcblx0ICogSW4gbW9zdCBvdGhlciBibG9ja3MsIHlvdSdkIHNlZSBhbiAnYXR0cmlidXRlcycgcHJvcGVydHkgYmVpbmcgZGVmaW5lZCBoZXJlLlxuXHQgKiBXZSd2ZSBkZWZpbmVkIGF0dHJpYnV0ZXMgaW4gdGhlIFBIUCwgdGhhdCBpbmZvcm1hdGlvbiBpcyBhdXRvbWF0aWNhbGx5IHNlbnRcblx0ICogdG8gdGhlIGJsb2NrIGVkaXRvciwgc28gd2UgZG9uJ3QgbmVlZCB0byByZWRlZmluZSBpdCBoZXJlLlxuXHQgKi9cblxuXHRlZGl0OiAocHJvcHMpID0+IHtcblx0XHRyZXR1cm4gW1xuXHRcdFx0Lypcblx0XHRcdCAqIFRoZSBTZXJ2ZXJTaWRlUmVuZGVyIGVsZW1lbnQgdXNlcyB0aGUgUkVTVCBBUEkgdG8gYXV0b21hdGljYWxseSBjYWxsXG5cdFx0XHQgKiBwaHBfYmxvY2tfcmVuZGVyKCkgaW4geW91ciBQSFAgY29kZSB3aGVuZXZlciBpdCBuZWVkcyB0byBnZXQgYW4gdXBkYXRlZFxuXHRcdFx0ICogdmlldyBvZiB0aGUgYmxvY2suXG5cdFx0XHQgKi9cblx0XHRcdGVsKFNlcnZlclNpZGVSZW5kZXIsIHtcblx0XHRcdFx0YmxvY2s6ICduZXh0Z2VudGhlbWVzL2FydmUtYmxvY2snLFxuXHRcdFx0XHRhdHRyaWJ1dGVzOiBwcm9wcy5hdHRyaWJ1dGVzLFxuXHRcdFx0fSksXG5cdFx0XHQvKlxuXHRcdFx0ICogSW5zcGVjdG9yQ29udHJvbHMgbGV0cyB5b3UgYWRkIGNvbnRyb2xzIHRvIHRoZSBCbG9jayBzaWRlYmFyLiBJbiB0aGlzIGNhc2UsXG5cdFx0XHQgKiB3ZSdyZSBhZGRpbmcgYSBUZXh0Q29udHJvbCwgd2hpY2ggbGV0cyB1cyBlZGl0IHRoZSAnZm9vJyBhdHRyaWJ1dGUgKHdoaWNoXG5cdFx0XHQgKiB3ZSBkZWZpbmVkIGluIHRoZSBQSFApLiBUaGUgb25DaGFuZ2UgcHJvcGVydHkgaXMgYSBsaXR0bGUgYml0IG9mIG1hZ2ljIHRvIHRlbGxcblx0XHRcdCAqIHRoZSBibG9jayBlZGl0b3IgdG8gdXBkYXRlIHRoZSB2YWx1ZSBvZiBvdXIgJ2ZvbycgcHJvcGVydHksIGFuZCB0byByZS1yZW5kZXJcblx0XHRcdCAqIHRoZSBibG9jay5cblx0XHRcdCAqL1xuXHRcdFx0ZWwoXG5cdFx0XHRcdEluc3BlY3RvckNvbnRyb2xzLFxuXHRcdFx0XHR7fSxcblx0XHRcdFx0ZWwoVGV4dENvbnRyb2wsIHtcblx0XHRcdFx0XHRsYWJlbDogJ0ZvbycsXG5cdFx0XHRcdFx0dmFsdWU6IHByb3BzLmF0dHJpYnV0ZXMuZm9vLFxuXHRcdFx0XHRcdG9uQ2hhbmdlOiAodmFsdWUpID0+IHtcblx0XHRcdFx0XHRcdHByb3BzLnNldEF0dHJpYnV0ZXMoeyBmb286IHZhbHVlIH0pO1xuXHRcdFx0XHRcdH0sXG5cdFx0XHRcdH0pLFxuXHRcdFx0XHRlbChUb2dnbGVDb250cm9sLCB7XG5cdFx0XHRcdFx0bGFiZWw6ICdUb29nbGUnLFxuXHRcdFx0XHRcdHZhbHVlOiBwcm9wcy5hdHRyaWJ1dGVzLnRvZ2dsZSxcblx0XHRcdFx0XHRvbkNoYW5nZTogKHZhbHVlKSA9PiB7XG5cdFx0XHRcdFx0XHRwcm9wcy5zZXRBdHRyaWJ1dGVzKHsgdG9nZ2xlOiB2YWx1ZSB9KTtcblx0XHRcdFx0XHR9LFxuXHRcdFx0XHR9KVxuXHRcdFx0KSxcblx0XHRdO1xuXHR9LFxuXG5cdC8vIFdlJ3JlIGdvaW5nIHRvIGJlIHJlbmRlcmluZyBpbiBQSFAsIHNvIHNhdmUoKSBjYW4ganVzdCByZXR1cm4gbnVsbC5cblx0c2F2ZTogKCkgPT4ge1xuXHRcdHJldHVybiBudWxsO1xuXHR9LFxufSk7XG4iXSwic291cmNlUm9vdCI6IiJ9\n//# sourceURL=webpack-internal:///./src/ts/test-block.ts\n");

/***/ }),

/***/ 3:
/*!************************************!*\
  !*** multi ./src/ts/test-block.ts ***!
  \************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! /uncrypted/websites/symbiosistheme.com/web/app/plugins/advanced-responsive-video-embedder/src/ts/test-block.ts */"./src/ts/test-block.ts");


/***/ })

/******/ });