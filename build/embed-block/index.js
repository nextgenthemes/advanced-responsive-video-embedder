/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./src/block/components/ImageUpload.tsx"
/*!**********************************************!*\
  !*** ./src/block/components/ImageUpload.tsx ***!
  \**********************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var clsx__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! clsx */ "../../../../../node_modules/clsx/dist/clsx.mjs");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__);





const ImageUpload = ({
  className,
  sKey,
  val,
  url,
  help,
  setAttributes
}) => {
  const mediaUploadInstructions = /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("p", {
    children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('To edit the featured image, you need permission to upload media.')
  });
  const containerClasses = (0,clsx__WEBPACK_IMPORTED_MODULE_3__["default"])('editor-post-featured-image__container', className);
  const mediaUploadRender = open => {
    return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)("div", {
      className: containerClasses,
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
        className: !val ? 'editor-post-featured-image__toggle' : 'editor-post-featured-image__preview',
        onClick: open,
        "aria-label": !val ? undefined : (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Edit or update the image'),
        "aria-describedby": !val ? '' : `editor-post-featured-image-${val}-describedby`,
        children: val && url ? /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("div", {
          style: {
            width: '100%',
            overflow: 'hidden'
          },
          children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("img", {
            src: url,
            alt: "ARVE Thumbnail",
            style: {
              width: '100%',
              objectFit: 'cover',
              aspectRatio: '16/9'
            }
          })
        }) : /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("span", {
          children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Set Thumbnail')
        })
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.DropZone, {})]
    });
  };
  const handleSelect = media => {
    setAttributes({
      [sKey]: media.id.toString(),
      [`${sKey}_url`]: media.url || ''
    });
  };
  const handleRemove = () => {
    setAttributes({
      [sKey]: '',
      [`${sKey}_url`]: ''
    });
  };
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.BaseControl, {
    className: "editor-post-featured-image",
    help: help,
    children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__.MediaUploadCheck, {
      fallback: mediaUploadInstructions,
      children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__.MediaUpload, {
        title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Thumbnail'),
        onSelect: handleSelect,
        allowedTypes: ['image'],
        modalClass: "editor-post-featured-image__media-modal",
        render: ({
          open
        }) => mediaUploadRender(open),
        value: val
      })
    }), !!val && !!url && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__.MediaUploadCheck, {
      children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__.MediaUpload, {
        title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Thumbnail'),
        onSelect: handleSelect,
        allowedTypes: ['image'],
        modalClass: "editor-post-featured-image__media-modal",
        render: ({
          open
        }) => /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
          onClick: open,
          variant: "secondary",
          children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Replace Thumbnail')
        })
      })
    }, `${sKey}-MediaUploadCheck-2`), !!val && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__.MediaUploadCheck, {
      children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
        onClick: handleRemove,
        isDestructive: true,
        children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Remove Thumbnail')
      })
    }, `${sKey}-MediaUploadCheck-3`)]
  });
};
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (ImageUpload);

/***/ },

/***/ "./src/block/utils.ts"
/*!****************************!*\
  !*** ./src/block/utils.ts ***!
  \****************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   hasSameKeys: () => (/* binding */ hasSameKeys)
/* harmony export */ });
/**
 * Checks if an object has exactly the same set of keys as the provided array
 *
 * @template T - The type of the keys array
 * @param {Object} obj  - The object to check
 * @param {T}      keys - Array of keys to check against
 * @return {obj is Record<T[number], unknown>} - Type guard that indicates if obj has exactly these keys
 */
function hasSameKeys(obj, keys) {
  const objKeys = Object.keys(obj);
  return objKeys.length === keys.length && keys.every(key => objKeys.includes(key));
}

/***/ },

/***/ "./src/embed-block/components/UrlOrEmbedCode.tsx"
/*!*******************************************************!*\
  !*** ./src/embed-block/components/UrlOrEmbedCode.tsx ***!
  \*******************************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   UrlOrEmbedCode: () => (/* binding */ UrlOrEmbedCode),
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__);


function calculateAspectRatio(width, height) {
  const isPositiveIntegerString = str => /^[1-9]\d*$/.test(str);
  if (!isPositiveIntegerString(width) || !isPositiveIntegerString(height)) {
    return undefined;
  }
  const w = parseInt(width, 10);
  const h = parseInt(height, 10);
  const gcd = (a, b) => {
    return b === 0 ? a : gcd(b, a % b);
  };
  const divisor = gcd(w, h);
  return `${w / divisor}:${h / divisor}`;
}
function UrlOrEmbedCode({
  label,
  value,
  onChange,
  onAspectRatioChange,
  placeholder,
  help
}) {
  const handleChange = newValue => {
    const parser = new DOMParser();
    const iframe = parser.parseFromString(newValue, 'text/html').querySelector('iframe');
    if (iframe?.src) {
      const src = iframe.getAttribute('src') || '';
      onChange(src);
      if (iframe.width && iframe.height) {
        const ratio = calculateAspectRatio(iframe.width, iframe.height);
        if (ratio && ratio !== '16:9') {
          onAspectRatioChange(ratio);
        }
      }
      return;
    }
    onChange(newValue);
  };
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_0__.TextControl, {
    label: label,
    value: value,
    onChange: handleChange,
    placeholder: placeholder,
    help: help,
    type: "text"
  });
}
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (UrlOrEmbedCode);

/***/ },

/***/ "react/jsx-runtime"
/*!**********************************!*\
  !*** external "ReactJSXRuntime" ***!
  \**********************************/
(module) {

module.exports = window["ReactJSXRuntime"];

/***/ },

/***/ "@wordpress/api-fetch"
/*!**********************************!*\
  !*** external ["wp","apiFetch"] ***!
  \**********************************/
(module) {

module.exports = window["wp"]["apiFetch"];

/***/ },

/***/ "@wordpress/block-editor"
/*!*************************************!*\
  !*** external ["wp","blockEditor"] ***!
  \*************************************/
(module) {

module.exports = window["wp"]["blockEditor"];

/***/ },

/***/ "@wordpress/components"
/*!************************************!*\
  !*** external ["wp","components"] ***!
  \************************************/
(module) {

module.exports = window["wp"]["components"];

/***/ },

/***/ "@wordpress/compose"
/*!*********************************!*\
  !*** external ["wp","compose"] ***!
  \*********************************/
(module) {

module.exports = window["wp"]["compose"];

/***/ },

/***/ "@wordpress/data"
/*!******************************!*\
  !*** external ["wp","data"] ***!
  \******************************/
(module) {

module.exports = window["wp"]["data"];

/***/ },

/***/ "@wordpress/hooks"
/*!*******************************!*\
  !*** external ["wp","hooks"] ***!
  \*******************************/
(module) {

module.exports = window["wp"]["hooks"];

/***/ },

/***/ "@wordpress/i18n"
/*!******************************!*\
  !*** external ["wp","i18n"] ***!
  \******************************/
(module) {

module.exports = window["wp"]["i18n"];

/***/ },

/***/ "../../../../../node_modules/clsx/dist/clsx.mjs"
/*!******************************************************!*\
  !*** ../../../../../node_modules/clsx/dist/clsx.mjs ***!
  \******************************************************/
(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   clsx: () => (/* binding */ clsx),
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
function r(e){var t,f,n="";if("string"==typeof e||"number"==typeof e)n+=e;else if("object"==typeof e)if(Array.isArray(e)){var o=e.length;for(t=0;t<o;t++)e[t]&&(f=r(e[t]))&&(n&&(n+=" "),n+=f)}else for(f in e)e[f]&&(n&&(n+=" "),n+=f);return n}function clsx(){for(var e,t,f=0,n="",o=arguments.length;f<o;f++)(e=arguments[f])&&(t=r(e))&&(n&&(n+=" "),n+=t);return n}/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (clsx);

/***/ }

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		if (!(moduleId in __webpack_modules__)) {
/******/ 			delete __webpack_module_cache__[moduleId];
/******/ 			var e = new Error("Cannot find module '" + moduleId + "'");
/******/ 			e.code = 'MODULE_NOT_FOUND';
/******/ 			throw e;
/******/ 		}
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry needs to be wrapped in an IIFE because it needs to be isolated against other modules in the chunk.
(() => {
/*!***********************************!*\
  !*** ./src/embed-block/index.tsx ***!
  \***********************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/hooks */ "@wordpress/hooks");
/* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/compose */ "@wordpress/compose");
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_compose__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/api-fetch */ "@wordpress/api-fetch");
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _block_components_ImageUpload__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../block/components/ImageUpload */ "./src/block/components/ImageUpload.tsx");
/* harmony import */ var _components_UrlOrEmbedCode__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./components/UrlOrEmbedCode */ "./src/embed-block/components/UrlOrEmbedCode.tsx");
/* harmony import */ var _block_utils__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ../block/utils */ "./src/block/utils.ts");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_10___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_10__);
/* eslint-disable @wordpress/no-unsafe-wp-apis */











const {
  settingPageUrl,
  options,
  settings,
  gutenbergActive
} = window.ArveEmbedBlockExtData;
const {
  gutenberg_help: gutenbergHelp
} = options;
function createHelp(html) {
  if (!gutenbergHelp || !html) {
    return undefined;
  }
  if (!html.match(/<a/i)) {
    return html;
  }
  const doc = new DOMParser().parseFromString(html, 'text/html');
  const result = [];
  let key = 1;
  const walk = node => {
    if (node.nodeType === Node.TEXT_NODE) {
      const text = node.textContent;
      if (text !== null && text !== undefined) {
        result.push(text);
      }
    } else if (node.nodeType === Node.ELEMENT_NODE) {
      const el = node;
      if (el.tagName === 'A') {
        const a = el;
        const linkText = a.textContent || '';
        result.push(/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_10__.jsx)("a", {
          href: a.href,
          target: "_blank",
          rel: "noreferrer",
          children: linkText
        }, 'link-' + key));
        key++;
        return;
      }
      Array.from(el.childNodes).forEach(walk);
    }
  };
  walk(doc.body);
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_10__.jsx)(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_10__.Fragment, {
    children: result
  });
}
function prepareSelectOptions(opts) {
  return Object.entries(opts).map(([value, label]) => ({
    label,
    value
  }));
}
function shouldHide(settingKey, attributes) {
  if ('align' === settingKey) {
    return true;
  }
  const setting = settings[settingKey];
  if (!setting?.depends?.length) {
    return false;
  }
  const hide = !setting.depends.some(condition => {
    const [key, value] = Object.entries(condition)[0] || [];
    if (!attributes[key]) {
      return true;
    }
    return key !== undefined && attributes[key] === value;
  });
  return hide;
}
function buildControls({
  attributes,
  setAttributes
}) {
  const controls = [];
  const sectionControls = {};
  Object.values(settings).forEach(setting => {
    sectionControls[setting.category] = [];
  });
  Object.entries(settings).forEach(([sKey, setting]) => {
    const val = attributes[sKey];
    const url = attributes[`${sKey}_url`] || '';
    const tab = setting.category || 'no-category';
    if (shouldHide(sKey, attributes)) {
      return;
    }
    const settingOptions = setting.options || {};
    const boolWithDefaultKeys = ['', 'true', 'false'];
    if ((0,_block_utils__WEBPACK_IMPORTED_MODULE_9__.hasSameKeys)(settingOptions, boolWithDefaultKeys)) {
      sectionControls[tab].push(/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_10__.jsxs)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.__experimentalToggleGroupControl, {
        label: setting.label,
        value: val || '',
        isBlock: true,
        __next40pxDefaultSize: true,
        onChange: value => setAttributes({
          [sKey]: value
        }),
        help: createHelp(setting.description),
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_10__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.__experimentalToggleGroupControlOption, {
          value: "",
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('Default', 'advanced-responsive-video-embedder')
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_10__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.__experimentalToggleGroupControlOption, {
          value: "true",
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('True', 'advanced-responsive-video-embedder')
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_10__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.__experimentalToggleGroupControlOption, {
          value: "false",
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('False', 'advanced-responsive-video-embedder')
        })]
      }, sKey));
    } else if ('url' === sKey) {
      sectionControls[tab].push(/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_10__.jsx)(_components_UrlOrEmbedCode__WEBPACK_IMPORTED_MODULE_8__["default"], {
        label: setting.label,
        value: val || '',
        onChange: value => setAttributes({
          [sKey]: value
        }),
        onAspectRatioChange: ratio => setAttributes({
          aspect_ratio: ratio
        }),
        placeholder: setting.placeholder,
        help: createHelp(setting.description)
      }, sKey));
    } else if (setting.ui === 'image_upload') {
      sectionControls[tab].push(/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_10__.jsx)(_block_components_ImageUpload__WEBPACK_IMPORTED_MODULE_7__["default"], {
        sKey: sKey,
        className: `arve-ctl-${setting.tab}`,
        val: val || undefined,
        url: url,
        help: createHelp(setting.description),
        setAttributes: setAttributes
      }, sKey));
    } else if (setting.ui_element === 'select') {
      const selectOptions = prepareSelectOptions(setting.options);
      sectionControls[tab].push(/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_10__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.SelectControl, {
        className: `arve-ctl-${setting.tab}`,
        label: setting.label,
        value: val,
        options: selectOptions,
        onChange: value => setAttributes({
          [sKey]: value
        }),
        help: createHelp(setting.description)
      }, sKey));
    } else if (setting.ui_element_type === 'checkbox') {
      sectionControls[tab].push(/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_10__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.ToggleControl, {
        className: `arve-ctl-${setting.tab}`,
        label: setting.label,
        checked: Boolean(val),
        onChange: value => setAttributes({
          [sKey]: value
        }),
        help: createHelp(setting.description)
      }, sKey));
    } else {
      sectionControls[tab].push(/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_10__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.TextControl, {
        className: `arve-ctl-${setting.tab}`,
        label: setting.label,
        type: setting.ui_element_type,
        value: val || '',
        placeholder: setting.placeholder,
        onChange: value => setAttributes({
          [sKey]: value
        }),
        help: createHelp(setting.description)
      }, sKey));
    }
  });
  if (gutenbergHelp || gutenbergActive) {
    sectionControls.main.push(/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_10__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.BaseControl, {
      help: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_10__.jsxs)(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_10__.Fragment, {
        children: [gutenbergHelp && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_10__.jsxs)(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_10__.Fragment, {
          children: [(0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('Remember changing the defaults is possible on the', 'advanced-responsive-video-embedder'), ' ', /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_10__.jsx)("a", {
            href: settingPageUrl,
            target: "_blank",
            rel: "noreferrer",
            children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('Settings page', 'advanced-responsive-video-embedder')
          }), '. ', (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('You can also disable the extensive help texts there to clean up this UI.', 'advanced-responsive-video-embedder')]
        }), gutenbergActive && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_10__.jsxs)(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_10__.Fragment, {
          children: [' ', (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('Error 153 in YouTube embeds, is a known issue with the Gutenberg plugin active and effects only the editor and normal mode. Your Videos will work fine on the front-end. Lazyload is not effected.', 'advanced-responsive-video-embedder')]
        })]
      }),
      children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_10__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.BaseControl.VisualLabel, {
        children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('Info', 'advanced-responsive-video-embedder')
      })
    }, "info-panel"));
  }
  const categories = {
    main: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('Main', 'advanced-responsive-video-embedder'),
    lazyloadAndLightbox: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('Lazyload & Lightbox', 'advanced-responsive-video-embedder'),
    lightbox: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('Lightbox', 'advanced-responsive-video-embedder'),
    data: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('Data', 'advanced-responsive-video-embedder'),
    stickyVideos: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('Sticky Videos', 'advanced-responsive-video-embedder'),
    functional: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('Functional', 'advanced-responsive-video-embedder'),
    privacy: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('Privacy', 'advanced-responsive-video-embedder'),
    misc: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('Misc', 'advanced-responsive-video-embedder')
  };
  Object.entries(sectionControls).forEach(([tab, tabControls]) => {
    if (tabControls.length > 0) {
      controls.push(/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_10__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.PanelBody, {
        title: categories[tab] ?? tab,
        initialOpen: 'main' === tab,
        children: tabControls
      }, tab));
    }
  });
  return controls;
}

/**
 * Registry mapping embed URLs to their ARVE settings.
 * Kept in sync whenever the block editor data changes.
 */
const arveRegistry = {};
let prevArveRegistry = null;
let syncTimeout;
function syncArveRegistry() {
  clearTimeout(syncTimeout);
  syncTimeout = setTimeout(() => {
    try {
      const blocks = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_4__.select)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__.store).getBlocks();
      const embeds = blocks.filter(b => b.name === 'core/embed' && b.attributes.url);
      const next = {};
      for (const block of embeds) {
        if (typeof block?.attributes?.url === 'string' && block.attributes.url.trim()) {
          next[block.attributes.url] = block.attributes.arve || {};
        }
      }
      Object.assign(arveRegistry, next);
      const currentUrls = new Set(embeds.map(b => b.attributes.url));
      for (const url of Object.keys(arveRegistry)) {
        if (!currentUrls.has(url)) {
          delete arveRegistry[url];
        }
      }
      if (prevArveRegistry !== null) {
        for (const [url, currArve] of Object.entries(next)) {
          const prevArve = prevArveRegistry[url] || {};
          if (JSON.stringify(currArve) !== JSON.stringify(prevArve)) {
            try {
              (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_4__.dispatch)('core').invalidateResolution('getEmbedPreview', [url]);
            } catch (e) {
              // Core data store may not be available.
            }
            break;
          }
        }
      }
      prevArveRegistry = JSON.parse(JSON.stringify(next));
    } catch (e) {
      // Block editor store not yet available.
    }
  }, 100);
}
(0,_wordpress_data__WEBPACK_IMPORTED_MODULE_4__.subscribe)(syncArveRegistry, _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__.store);
_wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_5___default().use((proxyOptions, next) => {
  if (proxyOptions.path?.startsWith('/oembed/1.0/proxy')) {
    const params = new URLSearchParams(proxyOptions.path.split('?')[1] || '');
    const url = params.get('url');
    if (url && arveRegistry[url]) {
      params.set('arve', JSON.stringify(arveRegistry[url]));
      proxyOptions.path = proxyOptions.path.split('?')[0] + '?' + params.toString();
    }
  }
  return next(proxyOptions);
});
(0,_wordpress_hooks__WEBPACK_IMPORTED_MODULE_0__.addFilter)('blocks.registerBlockType', 'embed-block-extension/attribute', (regSettings, name) => {
  if (name !== 'core/embed') {
    return regSettings;
  }
  regSettings.attributes = {
    ...regSettings.attributes,
    arve: {
      type: 'object',
      default: {}
    }
  };
  return regSettings;
});
const withArveControls = (0,_wordpress_compose__WEBPACK_IMPORTED_MODULE_1__.createHigherOrderComponent)(BlockEdit => {
  return props => {
    if (props.name !== 'core/embed') {
      return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_10__.jsx)(BlockEdit, {
        ...props
      });
    }
    const {
      attributes,
      setAttributes
    } = props;
    const arve = attributes.arve || {};
    const setArveAttributes = attrs => {
      const topLevel = {};
      const arveLevel = {};
      for (const [key, value] of Object.entries(attrs)) {
        if (key === 'url') {
          topLevel[key] = value;
        } else {
          arveLevel[key] = value;
        }
      }
      const updates = {};
      if (Object.keys(arveLevel).length > 0) {
        updates.arve = {
          ...arve,
          ...arveLevel
        };
      }
      Object.assign(updates, topLevel);
      setAttributes(updates);
    };
    return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_10__.jsxs)(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_10__.Fragment, {
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_10__.jsx)(BlockEdit, {
        ...props
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_10__.jsx)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__.InspectorControls, {
        children: buildControls({
          attributes: {
            ...arve
          },
          setAttributes: setArveAttributes
        })
      })]
    });
  };
}, 'withArveControls');
(0,_wordpress_hooks__WEBPACK_IMPORTED_MODULE_0__.addFilter)('editor.BlockEdit', 'embed-block-extension/controls', withArveControls);
})();

/******/ })()
;
//# sourceMappingURL=index.js.map