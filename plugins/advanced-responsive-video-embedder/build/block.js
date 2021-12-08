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
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_server_side_render__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/server-side-render */ "@wordpress/server-side-render");
/* harmony import */ var _wordpress_server_side_render__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_server_side_render__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__);





const settings = window.ARVEsettings;
const wp = window.wp;
const domParser = new DOMParser();
function PrepareSelectOptions(options) {
    const gboptions = [];
    Object.entries(options).forEach(([key, value]) => {
        const o = {
            label: value,
            value: key,
        };
        gboptions.push(o);
    });
    return gboptions;
}
function maybeSetAspectRatio(key, value, props) {
    if ('url' === key) {
        const iframe = domParser
            .parseFromString(value, 'text/html')
            .querySelector('iframe');
        if (iframe && iframe.getAttribute('src')) {
            value = iframe.src;
            const w = iframe.width;
            const h = iframe.height;
            if (w && h) {
                props.setAttributes({
                    aspect_ratio: aspectRatio(w, h),
                });
            }
        }
    }
}
function BuildControls(props) {
    const controls = [];
    const sectionControls = {};
    const mediaUploadInstructions = ((0,_wordpress_element__WEBPACK_IMPORTED_MODULE_2__.createElement)("p", null, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('To edit the featured image, you need permission to upload media.')));
    let selectedMedia = false;
    Object.values(settings).forEach((option) => {
        sectionControls[option.tag] = [];
    });
    Object.entries(settings).forEach(([key, option]) => {
        let val = props.attributes[key];
        let url = '';
        switch (option.type) {
            case 'boolean':
                if ('sandbox' === key && typeof val === 'undefined') {
                    val = true;
                }
                sectionControls[option.tag].push((0,_wordpress_element__WEBPACK_IMPORTED_MODULE_2__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__.ToggleControl, { key: key, label: option.label, help: createHelp(option), checked: !!val, onChange: (value) => {
                        return props.setAttributes({ [key]: value });
                    } }));
                break;
            case 'select':
                sectionControls[option.tag].push((0,_wordpress_element__WEBPACK_IMPORTED_MODULE_2__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__.SelectControl, { key: key, value: val, label: option.label, help: createHelp(option), options: PrepareSelectOptions(option.options), onChange: (value) => {
                        return props.setAttributes({ [key]: value });
                    } }));
                break;
            case 'string':
                sectionControls[option.tag].push((0,_wordpress_element__WEBPACK_IMPORTED_MODULE_2__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__.TextControl, { key: key, label: option.label, placeholder: option.placeholder, help: createHelp(option), value: val, onChange: (value) => {
                        maybeSetAspectRatio(key, value, props);
                        return props.setAttributes({ [key]: value });
                    } }));
                break;
            case 'attachment_old':
                url = props.attributes[key + '_url'];
                sectionControls[option.tag].push((0,_wordpress_element__WEBPACK_IMPORTED_MODULE_2__.createElement)("div", null,
                    (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_2__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__.MediaUploadCheck, null,
                        (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_2__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__.MediaUpload, { onSelect: (media) => {
                                return props.setAttributes({
                                    [key]: media.id.toString(),
                                    [key + '_url']: media.url,
                                });
                            }, allowedTypes: "image", render: ({ open }) => ((0,_wordpress_element__WEBPACK_IMPORTED_MODULE_2__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__.Button, { className: "components-button--arve-thumbnail", onClick: open, "aria-label": (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Edit or update the image') },
                                !!url && ((0,_wordpress_element__WEBPACK_IMPORTED_MODULE_2__.createElement)("div", null,
                                    (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_2__.createElement)("img", { src: url, alt: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Selected Thumbnail') }))),
                                (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Edit or update the image'))) })),
                    !!val && ((0,_wordpress_element__WEBPACK_IMPORTED_MODULE_2__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__.Button, { onClick: () => {
                            return props.setAttributes({
                                [key]: '',
                                [key + '_url']: '',
                            });
                        } }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Remove Custom Thumbnail'))),
                    (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_2__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__.TextControl, { label: option.label, placeholder: option.placeholder, help: createHelp(option), value: val, onChange: (value) => {
                            return props.setAttributes({ [key]: value });
                        } })));
                break;
            case 'attachment':
                url = props.attributes[key + '_url'];
                sectionControls[option.tag].push((0,_wordpress_element__WEBPACK_IMPORTED_MODULE_2__.createElement)("div", { className: "editor-post-featured-image" },
                    (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_2__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__.MediaUploadCheck, { fallback: mediaUploadInstructions },
                        (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_2__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__.MediaUpload, { title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Thumbnail'), onSelect: (media) => {
                                selectedMedia = media;
                                return props.setAttributes({
                                    [key]: media.id.toString(),
                                    [key + '_url']: media.url,
                                });
                            }, unstableFeaturedImageFlow: true, allowedTypes: "Image", modalClass: "editor-post-featured-image__media-modal", render: ({ open }) => ((0,_wordpress_element__WEBPACK_IMPORTED_MODULE_2__.createElement)("div", { className: "editor-post-featured-image__container" },
                                (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_2__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__.Button, { className: !val
                                        ? 'editor-post-featured-image__toggle'
                                        : 'editor-post-featured-image__preview', onClick: open, "aria-describedby": !val
                                        ? ''
                                        : `editor-post-featured-image-${val}-describedby` },
                                    !!val && !!url && ((0,_wordpress_element__WEBPACK_IMPORTED_MODULE_2__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__.ResponsiveWrapper, { naturalWidth: 640, naturalHeight: 380 },
                                        (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_2__.createElement)("img", { src: url, alt: "" }))),
                                    !val && (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Set Thumbnail')),
                                (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_2__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__.DropZone, null))), value: val })),
                    !!val && !!url && ((0,_wordpress_element__WEBPACK_IMPORTED_MODULE_2__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__.MediaUploadCheck, null,
                        (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_2__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__.MediaUpload, { title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Thumbnail'), onSelect: (media) => {
                                selectedMedia = media;
                                return props.setAttributes({
                                    [key]: media.id.toString(),
                                    [key + '_url']: media.url,
                                });
                            }, unstableFeaturedImageFlow: true, allowedTypes: "image", modalClass: "editor-post-featured-image__media-modal", render: ({ open }) => ((0,_wordpress_element__WEBPACK_IMPORTED_MODULE_2__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__.Button, { onClick: open, isSecondary: true }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Replace Thumbnail'))) }))),
                    !!val && ((0,_wordpress_element__WEBPACK_IMPORTED_MODULE_2__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__.MediaUploadCheck, null,
                        (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_2__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__.Button, { onClick: () => {
                                return props.setAttributes({
                                    [key]: '',
                                    [key + '_url']: '',
                                });
                            }, isLink: true, isDestructive: true }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Remove Thumbnail')))),
                    (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_2__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__.TextControl, { label: option.label, placeholder: option.placeholder, help: createHelp(option), value: val, onChange: (value) => {
                            return props.setAttributes({ [key]: value });
                        } })));
                break;
        }
    });
    let open = true;
    Object.keys(sectionControls).forEach((key) => {
        controls.push((0,_wordpress_element__WEBPACK_IMPORTED_MODULE_2__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__.PanelBody, { key: key, title: capitalizeFirstLetter(key), initialOpen: open }, sectionControls[key]));
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
        return ((0,_wordpress_element__WEBPACK_IMPORTED_MODULE_2__.createElement)("span", null,
            (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_2__.createElement)("span", null, textSplit[0]),
            (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_2__.createElement)("a", { href: option.descriptionlink }, option.descriptionlinktext),
            ",",
            (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_2__.createElement)("span", null, textSplit[1])));
    }
    return option.description;
}
function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}
wp.blocks.registerBlockType('nextgenthemes/arve-block', {
    title: 'Video Embed (ARVE)',
    description: 'You can disable help texts on the ARVE settings page to clean up the UI',
    icon: 'video-alt3',
    category: 'embed',
    supports: {
        AlignWide: true,
        align: ['left', 'right', 'center', 'wide', 'full'],
    },
    edit: (props) => {
        const controls = BuildControls(props);
        return [
            (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_2__.createElement)((_wordpress_server_side_render__WEBPACK_IMPORTED_MODULE_1___default()), { key: "ssr", block: "nextgenthemes/arve-block", attributes: props.attributes }),
            (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_2__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__.InspectorControls, { key: "insp" }, controls),
        ];
    },
    save: () => {
        return null;
    },
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

}();
/******/ })()
;
//# sourceMappingURL=block.js.map