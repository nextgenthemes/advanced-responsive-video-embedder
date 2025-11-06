/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "../../../../../node_modules/classnames/index.js":
/*!*******************************************************!*\
  !*** ../../../../../node_modules/classnames/index.js ***!
  \*******************************************************/
/***/ ((module, exports) => {

var __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
	Copyright (c) 2018 Jed Watson.
	Licensed under the MIT License (MIT), see
	http://jedwatson.github.io/classnames
*/
/* global define */

(function () {
	'use strict';

	var hasOwn = {}.hasOwnProperty;

	function classNames () {
		var classes = '';

		for (var i = 0; i < arguments.length; i++) {
			var arg = arguments[i];
			if (arg) {
				classes = appendClass(classes, parseValue(arg));
			}
		}

		return classes;
	}

	function parseValue (arg) {
		if (typeof arg === 'string' || typeof arg === 'number') {
			return arg;
		}

		if (typeof arg !== 'object') {
			return '';
		}

		if (Array.isArray(arg)) {
			return classNames.apply(null, arg);
		}

		if (arg.toString !== Object.prototype.toString && !arg.toString.toString().includes('[native code]')) {
			return arg.toString();
		}

		var classes = '';

		for (var key in arg) {
			if (hasOwn.call(arg, key) && arg[key]) {
				classes = appendClass(classes, key);
			}
		}

		return classes;
	}

	function appendClass (value, newClass) {
		if (!newClass) {
			return value;
		}
	
		if (value) {
			return value + ' ' + newClass;
		}
	
		return value + newClass;
	}

	if ( true && module.exports) {
		classNames.default = classNames;
		module.exports = classNames;
	} else if (true) {
		// register as 'classnames', consistent with npm package name
		!(__WEBPACK_AMD_DEFINE_ARRAY__ = [], __WEBPACK_AMD_DEFINE_RESULT__ = (function () {
			return classNames;
		}).apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__),
		__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
	} else // removed by dead control flow
{}
}());


/***/ }),

/***/ "./src/block.json":
/*!************************!*\
  !*** ./src/block.json ***!
  \************************/
/***/ ((module) => {

"use strict";
module.exports = /*#__PURE__*/JSON.parse('{"$schema":"https://schemas.wp.org/trunk/block.json","apiVersion":2,"name":"nextgenthemes/arve-block","title":"Video Embed (ARVE)","category":"embed","icon":"video-alt3","description":"Advanced Responsive Video Embedder","keywords":["embed","youtube","rumble","vimeo","odysee"],"version":"10.7.1","textdomain":"advanced-responsive-video-embedder","supports":{"align":["wide","full","left","right"],"className":true,"customClassName":true},"example":{"attributes":{"url":"https://www.youtube.com/watch?v=oe452WcY7fA","title":"Example ARVE Video"}},"editorScript":"arve-block","editorStyle":["arve-block","arve","arve-pro","arve-sticky-videos","arve-random-video"],"viewScript":["arve","arve-pro","arve-sticky-videos","arve-random-video"],"viewStyle":["arve","arve-pro","arve-sticky-videos","arve-random-video"],"attributes":{"url":{"type":"string"},"loop":{"type":"boolean"},"muted":{"type":"boolean"},"controls":{"type":"string"},"title":{"type":"string"},"description":{"type":"string"},"upload_date":{"type":"string"},"thumbnail":{"type":"string"},"align":{"type":"string"},"arve_link":{"type":"string"},"duration":{"type":"string"},"autoplay":{"type":"string"},"aspect_ratio":{"type":"string"},"parameters":{"type":"string"},"volume":{"type":"integer"},"encrypted_media":{"type":"boolean"},"credentialless":{"type":"boolean"},"lightbox_aspect_ratio":{"type":"string"},"controlslist":{"type":"string"},"random_video_url":{"type":"string"},"random_video_urls":{"type":"string"},"mode":{"type":"string"},"lazyload_style":{"type":"string"},"hide_title":{"type":"string"},"grow":{"type":"string"},"fullscreen":{"type":"string"},"play_icon_style":{"type":"string"},"hover_effect":{"type":"string"},"disable_links":{"type":"string"},"lightbox_maxwidth":{"type":"integer"},"invidious":{"type":"string"},"sticky":{"type":"string"},"sticky_on_mobile":{"type":"string"},"sticky_position":{"type":"string"},"thumbnail_url":{"type":"string"}}}');

/***/ }),

/***/ "./src/editor.scss":
/*!*************************!*\
  !*** ./src/editor.scss ***!
  \*************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "@wordpress/block-editor":
/*!*************************************!*\
  !*** external ["wp","blockEditor"] ***!
  \*************************************/
/***/ ((module) => {

"use strict";
module.exports = window["wp"]["blockEditor"];

/***/ }),

/***/ "@wordpress/blocks":
/*!********************************!*\
  !*** external ["wp","blocks"] ***!
  \********************************/
/***/ ((module) => {

"use strict";
module.exports = window["wp"]["blocks"];

/***/ }),

/***/ "@wordpress/components":
/*!************************************!*\
  !*** external ["wp","components"] ***!
  \************************************/
/***/ ((module) => {

"use strict";
module.exports = window["wp"]["components"];

/***/ }),

/***/ "@wordpress/element":
/*!*********************************!*\
  !*** external ["wp","element"] ***!
  \*********************************/
/***/ ((module) => {

"use strict";
module.exports = window["wp"]["element"];

/***/ }),

/***/ "@wordpress/i18n":
/*!******************************!*\
  !*** external ["wp","i18n"] ***!
  \******************************/
/***/ ((module) => {

"use strict";
module.exports = window["wp"]["i18n"];

/***/ }),

/***/ "@wordpress/server-side-render":
/*!******************************************!*\
  !*** external ["wp","serverSideRender"] ***!
  \******************************************/
/***/ ((module) => {

"use strict";
module.exports = window["wp"]["serverSideRender"];

/***/ }),

/***/ "react":
/*!************************!*\
  !*** external "React" ***!
  \************************/
/***/ ((module) => {

"use strict";
module.exports = window["React"];

/***/ }),

/***/ "react/jsx-runtime":
/*!**********************************!*\
  !*** external "ReactJSXRuntime" ***!
  \**********************************/
/***/ ((module) => {

"use strict";
module.exports = window["ReactJSXRuntime"];

/***/ })

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
// This entry needs to be wrapped in an IIFE because it needs to be in strict mode.
(() => {
"use strict";
/*!***********************!*\
  !*** ./src/block.tsx ***!
  \***********************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _block_json__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./block.json */ "./src/block.json");
/* harmony import */ var _editor_scss__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./editor.scss */ "./src/editor.scss");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_server_side_render__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/server-side-render */ "@wordpress/server-side-render");
/* harmony import */ var _wordpress_server_side_render__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_server_side_render__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/blocks */ "@wordpress/blocks");
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! classnames */ "../../../../../node_modules/classnames/index.js");
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(classnames__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__);
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_9__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_10___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_10__);
/**
 * Copyright 2019-2024 Nicolas Jonas
 * License: GPL 3.0
 *
 * Based on: https://gist.github.com/pento/cf38fd73ce0f13fcf0f0ae7d6c4b685d
 * Copyright 2019 Gary Pendergast
 * License: GPL 2.0+
 */












const {
  name
} = _block_json__WEBPACK_IMPORTED_MODULE_0__;
const {
  settings,
  options
} = window.ArveBlockJsBefore;
delete settings?.align?.options?.center;
const domParser = new DOMParser();

/**
 * Keypair to Gutenberg component
 * @param selectOptions
 */
function PrepareSelectOptions(selectOptions) {
  if (!selectOptions) {
    throw new Error('no options');
  }
  return Object.entries(selectOptions).map(([key, value]) => ({
    label: value,
    value: key
  }));
}
function changeTextControl(key, value, props) {
  if ('url' === key) {
    const iframe = domParser.parseFromString(value, 'text/html').querySelector('iframe');
    if (iframe && iframe.getAttribute('src')) {
      props.setAttributes({
        [key]: iframe.getAttribute('src')
      });
      if (iframe.width && iframe.height) {
        const ratio = aspectRatio(iframe.width, iframe.height);
        if ('16:9' !== ratio) {
          props.setAttributes({
            aspect_ratio: ratio
          });
        }
      }
      return;
    }
  }
  props.setAttributes({
    [key]: value
  });
}
const mediaUploadRender = (open, val, url) => {
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_9__.jsxs)("div", {
    className: "editor-post-featured-image__container",
    children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_9__.jsxs)(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__.Button, {
      className: !val ? 'editor-post-featured-image__toggle' : 'editor-post-featured-image__preview',
      onClick: open,
      "aria-label": !val ? null : (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Edit or update the image'),
      "aria-describedby": !val ? '' : `editor-post-featured-image-${val}-describedby`,
      children: [!!val && !!url && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_9__.jsx)("div", {
        style: {
          overflow: 'hidden'
        },
        children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_9__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__.ResponsiveWrapper, {
          naturalWidth: 640,
          naturalHeight: 360,
          isInline: true,
          children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_9__.jsx)("img", {
            src: url,
            alt: "ARVE Thumbnail",
            style: {
              width: '100%',
              height: '100%',
              objectFit: 'cover'
            }
          })
        })
      }), !val && (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Set Thumbnail')]
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_9__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__.DropZone, {})]
  });
};
function buildControls(props) {
  const controls = [];
  const sectionControls = {};
  const mediaUploadInstructions = /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_9__.jsx)("p", {
    children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('To edit the featured image, you need permission to upload media.')
  });
  let selectedMedia;
  Object.values(settings).forEach(option => {
    sectionControls[option.tab] = [];
  });
  Object.entries(settings).forEach(([key, option]) => {
    const val = props.attributes[key];
    const url = '';
    sectionControls[option.tab].push(/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_9__.jsxs)(_wordpress_element__WEBPACK_IMPORTED_MODULE_8__.Fragment, {
      children: ['select' === option.ui_element && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_9__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__.SelectControl, {
        value: val,
        label: option.label,
        help: createHelp(option?.description),
        options: PrepareSelectOptions(option.options),
        onChange: value => {
          return props.setAttributes({
            [key]: '' === value ? undefined : value
          });
        }
      }), 'checkbox' === option.ui_element_type && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_9__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__.ToggleControl, {
        label: option.label,
        help: createHelp(option?.description),
        checked: !!val,
        onChange: value => {
          return props.setAttributes({
            [key]: value
          });
        }
      }, key), ['text', 'number'].includes(option.ui_element_type) && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_9__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__.TextControl, {
        label: option.label,
        placeholder: option.placeholder,
        help: createHelp(option?.description),
        value: val,
        onChange: value => {
          changeTextControl(key, value, props);
        }
      }), 'image_upload' === option.ui && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_9__.jsxs)(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__.BaseControl, {
        className: "editor-post-featured-image",
        help: createHelp(option?.description),
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_9__.jsx)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__.MediaUploadCheck, {
          fallback: mediaUploadInstructions,
          children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_9__.jsx)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__.MediaUpload, {
            title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Thumbnail'),
            onSelect: media => {
              selectedMedia = media;
              return props.setAttributes({
                [key]: media.id.toString(),
                [key + '_url']: media.url
              });
            },
            allowedTypes: ['image'],
            modalClass: "editor-post-featured-image__media-modal",
            render: ({
              open
            }) => {
              return mediaUploadRender(open, val, url);
            },
            value: val
          })
        }), !!val && !!url && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_9__.jsx)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__.MediaUploadCheck, {
          children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_9__.jsx)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__.MediaUpload, {
            title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Thumbnail'),
            onSelect: media => {
              selectedMedia = media;
              return props.setAttributes({
                [key]: media.id.toString(),
                [key + '_url']: media.url
              });
            },
            allowedTypes: ['image'],
            modalClass: "editor-post-featured-image__media-modal",
            render: ({
              open
            }) => /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_9__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__.Button, {
              onClick: open,
              variant: "secondary",
              children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Replace Thumbnail')
            })
          })
        }, key + '-MediaUploadCheck-2'), !!val && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_9__.jsx)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__.MediaUploadCheck, {
          children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_9__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__.Button, {
            onClick: () => {
              return props.setAttributes({
                [key]: '',
                [key + '_url']: ''
              });
            },
            isDestructive: true,
            children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Remove Thumbnail')
          })
        }, key + '-MediaUploadCheck-3')]
      })]
    }, key + '-fragment'));
  });
  sectionControls.main.push(/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_9__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__.BaseControl, {
    help: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('You can disable the extensive help texts on the ARVE settings page to clean up this UI', 'advanced-responsive-video-embedder'),
    children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_9__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__.BaseControl.VisualLabel, {
      children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Info', 'advanced-responsive-video-embedder')
    })
  }, 'info'));
  Object.keys(sectionControls).forEach(key => {
    controls.push(/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_9__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__.PanelBody, {
      title: capitalizeFirstLetter(key),
      initialOpen: true,
      children: sectionControls[key]
    }, key));
  });
  return controls;

  // Object.keys( sectionControls ).forEach( ( key ) => {
  // 	controls.push( sectionControls[ key ] );
  // 	open = false;
  // } );

  // return (
  // 	<PanelBody key="arve" title="ARVE" initialOpen={ true }>
  // 		{ controls }
  // 	</PanelBody>
  // );
}
function createHelp(description) {
  if (!description) {
    return '';
  }
  const doc = domParser.parseFromString(description, 'text/html');
  const link = doc.querySelector('a');
  if (link) {
    const href = link.getAttribute('href') || '';
    const linkText = link.textContent || '';
    description = doc.body.textContent || '';
    const textSplit = description.split(linkText);
    if (textSplit.length !== 2) {
      throw new Error('textSplit.length must be 2');
    }
    return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_9__.jsxs)(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_9__.Fragment, {
      children: [textSplit[0], /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_9__.jsx)("a", {
        href: href,
        children: linkText
      }), textSplit[1]]
    });
  }
  return description;
}
function capitalizeFirstLetter(str) {
  return str.charAt(0).toUpperCase() + str.slice(1);
}
function Edit(props) {
  const {
    attributes: {
      mode,
      align,
      maxwidth
    }
  } = props;
  let pointerEvents = true;
  const style = {};
  const attrCopy = JSON.parse(JSON.stringify(props.attributes));
  delete attrCopy.align;
  delete attrCopy.maxwidth;
  if (maxwidth && ('left' === align || 'right' === align)) {
    style.width = '100%';
    style.maxWidth = maxwidth;
  } else if ('left' === align || 'right' === align) {
    style.width = '100%';
    style.maxWidth = options.align_maxwidth;
  }
  const blockProps = (0,_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__.useBlockProps)({
    style
  });
  if ('normal' === mode || !mode && 'normal' === options.mode) {
    pointerEvents = false;
  }
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_9__.jsxs)(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_9__.Fragment, {
    children: [/*#__PURE__*/(0,react__WEBPACK_IMPORTED_MODULE_10__.createElement)("div", {
      ...blockProps,
      key: "block"
    }, /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_9__.jsx)((_wordpress_server_side_render__WEBPACK_IMPORTED_MODULE_3___default()), {
      className: classnames__WEBPACK_IMPORTED_MODULE_7___default()({
        'arve-ssr': true,
        'arve-ssr--pointer-events-none': !pointerEvents
      }),
      block: "nextgenthemes/arve-block",
      attributes: attrCopy,
      skipBlockSupportAttributes: true
    })), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_9__.jsx)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__.InspectorControls, {
      children: buildControls(props)
    }, "insp")]
  });
}

// @ts-ignore
(0,_wordpress_blocks__WEBPACK_IMPORTED_MODULE_6__.registerBlockType)(name, {
  edit: Edit
});

/**
 * Calculate aspect ratio based on width and height.
 *
 * @param {string} width  - The width value
 * @param {string} height - The height value
 * @return {string} The aspect ratio in the format 'width:height'
 */
function aspectRatio(width, height) {
  if (isIntOverZero(width) && isIntOverZero(height)) {
    const w = parseInt(width);
    const h = parseInt(height);
    const arGCD = gcd(w, h);
    return w / arGCD + ':' + h / arGCD;
  }
  return width + ':' + height;
}

/**
 * Check if the input string is a positive integer.
 *
 * @param {string} str - The input string to be checked.
 * @return {boolean} Whether the input string is a positive integer or not.
 */
function isIntOverZero(str) {
  const n = Math.floor(Number(str));
  return n !== Infinity && String(n) === str && n > 0;
}

/**
 * Calculates the greatest common divisor of two numbers using the Euclidean algorithm.
 *
 * @param {number} a - the first number
 * @param {number} b - the second number
 * @return {number} the greatest common divisor of the two numbers
 */
function gcd(a, b) {
  if (!b) {
    return a;
  }
  return gcd(b, a % b);
}

/*
TODO when the sanitizer API
function sanitizeHelpHTML( option: OptionProps ) {
	if ( typeof option.description !== 'string' ) {
		return '';
	}

	const div = document.createElement( 'div' );

	if ( 'Sanitizer' in window ) {
		const sanitizer = new window.Sanitizer( {
			allowElements: [ 'a' ],
			allowAttributes: {
				target: [ 'a' ],
				href: [ 'a' ],
			},
		} );

		// @ts-ignore
		div.setHTML( option.description, { sanitizer } );

		return <span dangerouslySetInnerHTML={ { __html: div.innerHTML } } />;
	}

	return stripHTML( option.description );
}

function stripHTML( html ) {
	const doc = new DOMParser().parseFromString( html, 'text/html' );
	return doc.body.textContent || '';
}
*/
})();

/******/ })()
;
//# sourceMappingURL=block.js.map