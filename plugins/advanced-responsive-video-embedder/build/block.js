/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "@wordpress/block-editor":
/*!*************************************!*\
  !*** external ["wp","blockEditor"] ***!
  \*************************************/
/***/ (function(module) {

module.exports = window["wp"]["blockEditor"];

/***/ }),

/***/ "@wordpress/blocks":
/*!********************************!*\
  !*** external ["wp","blocks"] ***!
  \********************************/
/***/ (function(module) {

module.exports = window["wp"]["blocks"];

/***/ }),

/***/ "@wordpress/components":
/*!************************************!*\
  !*** external ["wp","components"] ***!
  \************************************/
/***/ (function(module) {

module.exports = window["wp"]["components"];

/***/ }),

/***/ "@wordpress/element":
/*!*********************************!*\
  !*** external ["wp","element"] ***!
  \*********************************/
/***/ (function(module) {

module.exports = window["wp"]["element"];

/***/ }),

/***/ "@wordpress/i18n":
/*!******************************!*\
  !*** external ["wp","i18n"] ***!
  \******************************/
/***/ (function(module) {

module.exports = window["wp"]["i18n"];

/***/ }),

/***/ "@wordpress/server-side-render":
/*!******************************************!*\
  !*** external ["wp","serverSideRender"] ***!
  \******************************************/
/***/ (function(module) {

module.exports = window["wp"]["serverSideRender"];

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/extends.js":
/*!************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/extends.js ***!
  \************************************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ _extends; }
/* harmony export */ });
function _extends() {
  _extends = Object.assign || function (target) {
    for (var i = 1; i < arguments.length; i++) {
      var source = arguments[i];

      for (var key in source) {
        if (Object.prototype.hasOwnProperty.call(source, key)) {
          target[key] = source[key];
        }
      }
    }

    return target;
  };

  return _extends.apply(this, arguments);
}

/***/ }),

/***/ "./plugins/advanced-responsive-video-embedder/src/block.json":
/*!*******************************************************************!*\
  !*** ./plugins/advanced-responsive-video-embedder/src/block.json ***!
  \*******************************************************************/
/***/ (function(module) {

module.exports = JSON.parse('{"$schema":"https://schemas.wp.org/trunk/block.json","apiVersion":2,"name":"nextgenthemes/arve-block","title":"Video Embed (ARVE)","category":"embed","icon":"video-alt3","description":"Advanced Responsive Video Embedder","keywords":["embed","youtube","rumble","vimeo","odysee"],"version":"9.9.2","textdomain":"advanced-responsive-video-embedder","supports":{"align":["wide","full"]},"styles":[],"example":{"attributes":{"url":"https://www.youtube.com/watch?v=oe452WcY7fA","title":"Example Title"}},"editorScript":"arve-block","editorStyle":"arve","attributes":{"url":{"type":"string"},"title":{"type":"string"},"description":{"type":"string"},"upload_date":{"type":"string"},"mode":{"type":"string"},"thumbnail":{"type":"string"},"hide_title":{"type":"boolean"},"grow":{"type":"string"},"fullscreen":{"type":"string"},"play_icon_style":{"type":"string"},"hover_effect":{"type":"string"},"disable_links":{"type":"string"},"align":{"type":"string"},"arve_link":{"type":"string"},"duration":{"type":"string"},"autoplay":{"type":"string"},"lightbox_maxwidth":{"type":"integer"},"sticky":{"type":"string"},"sticky_on_mobile":{"type":"string"},"sticky_position":{"type":"string"},"aspect_ratio":{"type":"string"},"parameters":{"type":"string"},"controlslist":{"type":"string"},"controls":{"type":"string"},"loop":{"type":"boolean"},"muted":{"type":"boolean"},"volume":{"type":"integer"},"random_video_url":{"type":"string"},"random_video_urls":{"type":"string"},"sandbox":{"type":"string"},"thumbnail_url":{"type":"string"}}}');

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
/******/ 	!function() {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = function(module) {
/******/ 			var getter = module && module.__esModule ?
/******/ 				function() { return module['default']; } :
/******/ 				function() { return module; };
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	!function() {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = function(exports, definition) {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	!function() {
/******/ 		__webpack_require__.o = function(obj, prop) { return Object.prototype.hasOwnProperty.call(obj, prop); }
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	!function() {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = function(exports) {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	}();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be isolated against other modules in the chunk.
!function() {
/*!******************************************************************!*\
  !*** ./plugins/advanced-responsive-video-embedder/src/block.tsx ***!
  \******************************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_helpers_extends__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/extends */ "./node_modules/@babel/runtime/helpers/esm/extends.js");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _block_json__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./block.json */ "./plugins/advanced-responsive-video-embedder/src/block.json");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_server_side_render__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/server-side-render */ "@wordpress/server-side-render");
/* harmony import */ var _wordpress_server_side_render__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_server_side_render__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @wordpress/blocks */ "@wordpress/blocks");
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_7__);


/**
 * Copyright 2019-2022 Nicolas Jonas
 * License: GPL 3.0
 *
 * Based on: https://gist.github.com/pento/cf38fd73ce0f13fcf0f0ae7d6c4b685d
 * Copyright 2019 Gary Pendergast
 * License: GPL 2.0+
 */



//import { createElement, Fragment } from '@wordpress/element';




const {
  name
} = _block_json__WEBPACK_IMPORTED_MODULE_2__;
const settings = window.ARVEsettings;
const domParser = new DOMParser();

/*
 * Keypair to gutenberg component
 */
function PrepareSelectOptions(options) {
  const gboptions = [];
  Object.entries(options).forEach(_ref => {
    let [key, value] = _ref;
    const o = {
      label: value,
      value: key
    };
    gboptions.push(o);
  });
  return gboptions;
}
function maybeSetAspectRatio(key, value, props) {
  if ('url' === key) {
    const iframe = domParser.parseFromString(value, 'text/html').querySelector('iframe');
    if (iframe && iframe.getAttribute('src')) {
      value = iframe.src;
      const w = iframe.width;
      const h = iframe.height;
      if (w && h) {
        props.setAttributes({
          aspect_ratio: aspectRatio(w, h)
        });
      }
    }
  }
}
function BuildControls(props) {
  const controls = [];
  const sectionControls = {};
  const mediaUploadInstructions = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.createElement)("p", null, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('To edit the featured image, you need permission to upload media.'));
  let selectedMedia = false;
  Object.values(settings).forEach(option => {
    sectionControls[option.tag] = [];
  });
  Object.entries(settings).forEach(_ref2 => {
    let [key, option] = _ref2;
    let val = props.attributes[key];
    let url = '';
    switch (option.type) {
      case 'boolean':
        if ('sandbox' === key && typeof val === 'undefined') {
          val = true;
        }
        sectionControls[option.tag].push((0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_6__.ToggleControl, {
          key: key,
          label: option.label,
          help: createHelp(option),
          checked: !!val,
          onChange: value => {
            return props.setAttributes({
              [key]: value
            });
          }
        }));
        break;
      case 'select':
        sectionControls[option.tag].push((0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_6__.SelectControl, {
          key: key,
          value: val,
          label: option.label,
          help: createHelp(option),
          options: PrepareSelectOptions(option.options),
          onChange: value => {
            return props.setAttributes({
              [key]: value
            });
          }
        }));
        break;
      case 'string':
        sectionControls[option.tag].push((0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_6__.TextControl, {
          key: key,
          label: option.label,
          placeholder: option.placeholder,
          help: createHelp(option),
          value: val,
          onChange: value => {
            maybeSetAspectRatio(key, value, props);
            return props.setAttributes({
              [key]: value
            });
          }
        }));
        break;
      case 'attachment':
        url = props.attributes[key + '_url'];
        sectionControls[option.tag].push((0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_6__.BaseControl, {
          className: "editor-post-featured-image",
          help: createHelp(option),
          key: key
        }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_5__.MediaUploadCheck, {
          fallback: mediaUploadInstructions
        }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_5__.MediaUpload, {
          title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Thumbnail'),
          onSelect: media => {
            selectedMedia = media;
            return props.setAttributes({
              [key]: media.id.toString(),
              [key + '_url']: media.url
            });
          },
          unstableFeaturedImageFlow: true,
          allowedTypes: ['image'],
          modalClass: "editor-post-featured-image__media-modal",
          render: _ref3 => {
            let {
              open
            } = _ref3;
            return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.createElement)("div", {
              className: "editor-post-featured-image__container"
            }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_6__.Button, {
              className: !val ? 'editor-post-featured-image__toggle' : 'editor-post-featured-image__preview',
              onClick: open,
              "aria-label": !val ? null : (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Edit or update the image'),
              "aria-describedby": !val ? '' : `editor-post-featured-image-${val}-describedby`
            }, !!val && !!url && (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.createElement)("div", {
              style: {
                overflow: 'hidden'
              }
            }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_6__.ResponsiveWrapper, {
              naturalWidth: 640,
              naturalHeight: 360,
              isInline: true
            }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.createElement)("img", {
              src: url,
              alt: "ARVE Thumbnail",
              style: {
                width: '100%',
                height: '100%',
                objectFit: 'cover'
              }
            }))), !val && (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Set Thumbnail')), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_6__.DropZone, null));
          },
          value: val
        })), !!val && !!url && (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_5__.MediaUploadCheck, null, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_5__.MediaUpload, {
          title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Thumbnail'),
          onSelect: media => {
            selectedMedia = media;
            return props.setAttributes({
              [key]: media.id.toString(),
              [key + '_url']: media.url
            });
          },
          unstableFeaturedImageFlow: true,
          allowedTypes: ['image'],
          modalClass: "editor-post-featured-image__media-modal",
          render: _ref4 => {
            let {
              open
            } = _ref4;
            return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_6__.Button, {
              onClick: open,
              isSecondary: true
            }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Replace Thumbnail'));
          }
        })), !!val && (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_5__.MediaUploadCheck, null, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_6__.Button, {
          onClick: () => {
            return props.setAttributes({
              [key]: '',
              [key + '_url']: ''
            });
          },
          isLink: true,
          isDestructive: true
        }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Remove Thumbnail')))));
        break;
    }
  });
  let open = true;
  sectionControls.main.push((0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_6__.BaseControl, {
    key: 'info',
    help: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('You can disable the extensive help texts on the ARVE settings page to clean up this UI', 'advanced-responsive-video-embedder')
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_6__.BaseControl.VisualLabel, null, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Info', 'advanced-responsive-video-embedder'))));
  Object.keys(sectionControls).forEach(key => {
    controls.push((0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_6__.PanelBody, {
      key: key,
      title: capitalizeFirstLetter(key),
      initialOpen: open
    }, sectionControls[key]));
    open = false;
  });
  return controls;
}
function createHelp(option) {
  if (typeof option.description !== 'string') {
    return '';
  }
  if (typeof option.descriptionlinktext === 'string') {
    const textSplit = option.description.split(option.descriptionlinktext);
    return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.createElement)("span", null, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.createElement)("span", null, textSplit[0]), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.createElement)("a", {
      href: option.descriptionlink
    }, option.descriptionlinktext), ",", (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.createElement)("span", null, textSplit[1]));
  }
  return option.description;
}
function capitalizeFirstLetter(string) {
  return string.charAt(0).toUpperCase() + string.slice(1);
}
function Edit(props) {
  const {
    attributes: {
      align
    },
    setAttributes
  } = props;
  const blockProps = (0,_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_5__.useBlockProps)();
  const controls = BuildControls(props);
  return [(0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.createElement)("div", (0,_babel_runtime_helpers_extends__WEBPACK_IMPORTED_MODULE_0__["default"])({}, blockProps, {
    key: "block"
  }), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.createElement)("div", {
    className: "arve-select-helper",
    style: {
      textAlign: 'center',
      padding: '.1em'
    }
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Select ARVE block', 'advanced-responsive-video-embedder')), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.createElement)((_wordpress_server_side_render__WEBPACK_IMPORTED_MODULE_4___default()), {
    block: "nextgenthemes/arve-block",
    attributes: props.attributes
  })), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_5__.InspectorControls, {
    key: "insp"
  }, controls)];
}
(0,_wordpress_blocks__WEBPACK_IMPORTED_MODULE_7__.registerBlockType)(name, {
  edit: Edit
});
function aspectRatio(w, h) {
  const arGCD = gcd(w, h);
  return w / arGCD + ':' + h / arGCD;
}
function gcd(a, b) {
  if (!b) {
    return a;
  }
  return gcd(b, a % b);
}

/*
wp.data.dispatch( 'core/edit-post' ).hideBlockTypes( [
	'core-embed/youtube',
	'core-embed/vimeo',
	'core-embed/dailymotion',
	'core-embed/collegehumor',
	'core-embed/ted',
] );
*/
}();
/******/ })()
;
//# sourceMappingURL=block.js.map