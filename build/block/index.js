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

/***/ "./src/block/components/UrlOrEmbedCode.tsx"
/*!*************************************************!*\
  !*** ./src/block/components/UrlOrEmbedCode.tsx ***!
  \*************************************************/
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
  // Check if the strings are valid positive integers (1 or greater)
  const isPositiveIntegerString = str => /^[1-9]\d*$/.test(str);
  if (!isPositiveIntegerString(width) || !isPositiveIntegerString(height)) {
    return undefined;
  }
  const w = parseInt(width, 10);
  const h = parseInt(height, 10);

  // Calculate the greatest common divisor
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
    // Try to parse as iframe HTML to extract src
    const parser = new DOMParser();
    const iframe = parser.parseFromString(newValue, 'text/html').querySelector('iframe');
    if (iframe?.src) {
      const src = iframe.getAttribute('src') || '';
      onChange(src);

      // If width and height are present, calculate aspect ratio
      if (iframe.width && iframe.height) {
        const ratio = calculateAspectRatio(iframe.width, iframe.height);
        if (ratio && ratio !== '16:9') {
          onAspectRatioChange(ratio);
        }
      }
      return;
    }

    // If not an iframe, just update the value
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

/***/ "./src/block/controls.tsx"
/*!********************************!*\
  !*** ./src/block/controls.tsx ***!
  \********************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   buildControls: () => (/* binding */ buildControls)
/* harmony export */ });
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _components_ImageUpload__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./components/ImageUpload */ "./src/block/components/ImageUpload.tsx");
/* harmony import */ var _components_UrlOrEmbedCode__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./components/UrlOrEmbedCode */ "./src/block/components/UrlOrEmbedCode.tsx");
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./utils */ "./src/block/utils.ts");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__);
/**
 * Copyright 2019-2025 Nicolas Jonas
 * License: GPL 3.0
 */


// createElement import removed as we're using JSX syntax




const {
  settingPageUrl,
  options,
  settings,
  gutenbergActive
} = window.ArveBlockJsBefore;
const {
  gutenberg_help: gutenbergHelp
} = options;
function createHelp(html) {
  if (!gutenbergHelp || !html) {
    return undefined;
  }

  // Quick check: if no <a> tags, return the html as a single string
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
        result.push(text); // Preserve all whitespace, no trim
      }
    } else if (node.nodeType === Node.ELEMENT_NODE) {
      const el = node;
      if (el.tagName === 'A') {
        const a = el;
        const linkText = a.textContent || '';
        result.push(/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)("a", {
          href: a.href,
          target: "_blank",
          rel: "noreferrer",
          children: linkText
        }, 'link-' + key));
        key++;
        return; // Don't process children since we handled the text
      }

      // Process child nodes for other elements (though assuming only text and <a>)
      Array.from(el.childNodes).forEach(walk);
    }
  };
  walk(doc.body);
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.Fragment, {
    children: result
  });
}

// Convert options to SelectControl format
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

  // If no dependencies, don't hide
  if (!setting?.depends?.length) {
    return false;
  }

  // Check if NONE of the dependency conditions are met
  const hide = !setting.depends.some(condition => {
    // Each condition is an object with a single key-value pair
    const [key, value] = Object.entries(condition)[0] || [];
    if (!attributes[key]) {
      return true; // If the is unset (default) show all settings, as advertisement
    }

    // If the attribute has the key and its value matches the condition, return true
    return key !== undefined && attributes[key] === value;
  });
  return hide;
}

// Main export: build controls for the block inspector
function buildControls({
  attributes,
  setAttributes
}) {
  const controls = [];
  const sectionControls = {};

  // Initialize section controls
  Object.values(settings).forEach(setting => {
    sectionControls[setting.category] = [];
  });

  // Add controls to sections
  Object.entries(settings).forEach(([sKey, setting]) => {
    const val = attributes[sKey];
    const url = attributes[`${sKey}_url`] || '';
    const tab = setting.category || 'no-category';
    if (shouldHide(sKey, attributes)) {
      return;
    }
    const settingOptions = setting.options || {};
    const boolWithDefaultKeys = ['', 'true', 'false']; // Use 'as const' for literal type inference

    if ((0,_utils__WEBPACK_IMPORTED_MODULE_4__.hasSameKeys)(settingOptions, boolWithDefaultKeys)) {
      sectionControls[tab].push(/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsxs)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.__experimentalToggleGroupControl, {
        label: setting.label,
        value: val || '',
        isBlock: true,
        __nextHasNoMarginBottom: true,
        __next40pxDefaultSize: true,
        onChange: value => setAttributes({
          [sKey]: value
        }),
        help: createHelp(setting.description),
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.__experimentalToggleGroupControlOption, {
          value: "",
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Default', 'advanced-responsive-video-embedder')
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.__experimentalToggleGroupControlOption, {
          value: "true",
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('True', 'advanced-responsive-video-embedder')
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.__experimentalToggleGroupControlOption, {
          value: "false",
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('False', 'advanced-responsive-video-embedder')
        })]
      }, sKey));
    } else if ('url' === sKey) {
      sectionControls[tab].push(/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(_components_UrlOrEmbedCode__WEBPACK_IMPORTED_MODULE_3__["default"], {
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
      sectionControls[tab].push(/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(_components_ImageUpload__WEBPACK_IMPORTED_MODULE_2__["default"], {
        sKey: sKey,
        className: `arve-ctl-${setting.tab}`,
        val: val || undefined,
        url: url,
        help: createHelp(setting.description),
        setAttributes: setAttributes
      }, sKey));
    } else if (setting.ui_element === 'select') {
      const selectOptions = prepareSelectOptions(setting.options);
      sectionControls[tab].push(/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.SelectControl, {
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
      sectionControls[tab].push(/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.ToggleControl, {
        className: `arve-ctl-${setting.tab}`,
        label: setting.label,
        checked: Boolean(val),
        onChange: value => setAttributes({
          [sKey]: value
        }),
        help: createHelp(setting.description)
      }, sKey));
    } else {
      sectionControls[tab].push(/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.TextControl, {
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
    // Add info panel to main section
    sectionControls.main.push(/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.BaseControl, {
      help: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsxs)(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.Fragment, {
        children: [gutenbergHelp && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsxs)(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.Fragment, {
          children: [(0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Remember changing the defaults is possible on the', 'advanced-responsive-video-embedder'), ' ', /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)("a", {
            href: settingPageUrl,
            target: "_blank",
            rel: "noreferrer",
            children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Settings page', 'advanced-responsive-video-embedder')
          }), '. ', (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('You can also disable the extensive help texts there to clean up this UI.', 'advanced-responsive-video-embedder')]
        }), gutenbergActive && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsxs)(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.Fragment, {
          children: [' ', (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Error 153 in YouTube embeds, is a known issue with the Gutenberg plugin active and effects only the editor and normal mode. Your Videos will work fine on the front-end. Lazyload is not effected.', 'advanced-responsive-video-embedder')]
        })]
      }),
      children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.BaseControl.VisualLabel, {
        children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Info', 'advanced-responsive-video-embedder')
      })
    }, "info-panel"));
  }
  const categories = {
    main: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Main', 'advanced-responsive-video-embedder'),
    lazyloadAndLightbox: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Lazyload & Lightbox', 'advanced-responsive-video-embedder'),
    lightbox: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Lightbox', 'advanced-responsive-video-embedder'),
    data: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Data', 'advanced-responsive-video-embedder'),
    stickyVideos: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Sticky Videos', 'advanced-responsive-video-embedder'),
    functional: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Functional', 'advanced-responsive-video-embedder'),
    privacy: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Privacy', 'advanced-responsive-video-embedder'),
    misc: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Misc', 'advanced-responsive-video-embedder')
  };

  // Convert section controls to panels
  Object.entries(sectionControls).forEach(([tab, tabControls]) => {
    if (tabControls.length > 0) {
      controls.push(/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.PanelBody, {
        title: categories[tab] ?? tab,
        initialOpen: 'main' === tab,
        children: tabControls
      }, tab));
    }
  });
  return controls;
}

/***/ },

/***/ "./src/block/edit.tsx"
/*!****************************!*\
  !*** ./src/block/edit.tsx ***!
  \****************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   Edit: () => (/* binding */ Edit)
/* harmony export */ });
/* harmony import */ var _editor_scss__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./editor.scss */ "./src/block/editor.scss");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_server_side_render__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/server-side-render */ "@wordpress/server-side-render");
/* harmony import */ var _wordpress_server_side_render__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_server_side_render__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var clsx__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! clsx */ "../../../../../node_modules/clsx/dist/clsx.mjs");
/* harmony import */ var _controls__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./controls */ "./src/block/controls.tsx");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_6__);
/**
 * Copyright 2019-2025 Nicolas Jonas
 * License: GPL 3.0
 */

// Import the editor styles







function Edit({
  attributes,
  setAttributes
}) {
  const {
    mode,
    align,
    maxwidth
  } = attributes;
  const {
    options
  } = window.ArveBlockJsBefore;
  let pointerEvents = true;
  const style = {};

  // Create a clean copy of attributes without block layout props
  const attrCopy = {
    ...attributes
  };
  delete attrCopy.align;
  delete attrCopy.maxwidth;

  // Handle alignment and max width styles
  if (maxwidth && ('left' === align || 'right' === align)) {
    style.width = '100%';
    style.maxWidth = maxwidth;
  } else if ('left' === align || 'right' === align) {
    style.width = '100%';
    style.maxWidth = options.align_maxwidth;
  }
  const blockProps = (0,_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__.useBlockProps)({
    style
  });
  if (mode === 'normal' || !mode && options.mode === 'normal') {
    pointerEvents = false;
  }
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsxs)(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.Fragment, {
    children: [/*#__PURE__*/(0,react__WEBPACK_IMPORTED_MODULE_6__.createElement)("div", {
      ...blockProps,
      key: "block"
    }, /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(_wordpress_server_side_render__WEBPACK_IMPORTED_MODULE_2__.ServerSideRender, {
      className: (0,clsx__WEBPACK_IMPORTED_MODULE_3__["default"])({
        'arve-ssr': true,
        'arve-ssr--pointer-events-none': !pointerEvents
      }),
      block: "nextgenthemes/arve-block",
      attributes: attrCopy,
      skipBlockSupportAttributes: true
    }, "ssr")), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__.InspectorControls, {
      children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.Fragment, {
        children: (0,_controls__WEBPACK_IMPORTED_MODULE_4__.buildControls)({
          attributes,
          setAttributes
        })
      })
    }, "insp")]
  });
}

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

/***/ "./src/block/editor.scss"
/*!*******************************!*\
  !*** ./src/block/editor.scss ***!
  \*******************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ },

/***/ "react"
/*!************************!*\
  !*** external "React" ***!
  \************************/
(module) {

module.exports = window["React"];

/***/ },

/***/ "react/jsx-runtime"
/*!**********************************!*\
  !*** external "ReactJSXRuntime" ***!
  \**********************************/
(module) {

module.exports = window["ReactJSXRuntime"];

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

/***/ "@wordpress/i18n"
/*!******************************!*\
  !*** external ["wp","i18n"] ***!
  \******************************/
(module) {

module.exports = window["wp"]["i18n"];

/***/ },

/***/ "@wordpress/server-side-render"
/*!******************************************!*\
  !*** external ["wp","serverSideRender"] ***!
  \******************************************/
(module) {

module.exports = window["wp"]["serverSideRender"];

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

/***/ },

/***/ "./src/block/block.json"
/*!******************************!*\
  !*** ./src/block/block.json ***!
  \******************************/
(module) {

module.exports = /*#__PURE__*/JSON.parse('{"$schema":"https://schemas.wp.org/trunk/block.json","apiVersion":2,"name":"nextgenthemes/arve-block","title":"Video Embed (ARVE)","category":"embed","icon":"video-alt3","description":"Advanced Responsive Video Embedder","keywords":["embed","youtube","rumble","vimeo","odysee"],"version":"10.8.6","textdomain":"advanced-responsive-video-embedder","supports":{"align":["wide","full","left","right"],"className":true,"customClassName":true},"example":{"attributes":{"url":"https://www.youtube.com/watch?v=oe452WcY7fA","title":"Example ARVE Video"}},"editorScript":"file:./index.js","editorStyle":["file:./index.css","arve","arve-pro","arve-sticky-videos","arve-random-video"],"viewScript":["arve","arve-pro","arve-sticky-videos","arve-random-video"],"viewScriptModule":["arve","arve-pro","arve-sticky-videos","arve-random-video"],"viewStyle":["arve","arve-pro","arve-sticky-videos","arve-random-video"],"attributes":{"url":{"type":"string"},"thumbnail":{"type":"string"},"mode":{"type":"string"},"grow":{"type":"string"},"lazyload_style":{"type":"string"},"hover_effect":{"type":"string"},"hide_title":{"type":"string"},"play_icon_style":{"type":"string"},"fullscreen":{"type":"string"},"lightbox_maxwidth":{"type":"integer"},"lightbox_aspect_ratio":{"type":"string"},"title":{"type":"string"},"description":{"type":"string"},"upload_date":{"type":"string"},"duration":{"type":"string"},"loop":{"type":"boolean"},"muted":{"type":"boolean"},"controls":{"type":"string"},"parameters":{"type":"string"},"controlslist":{"type":"string"},"autoplay":{"type":"string"},"disable_links":{"type":"string"},"credentialless":{"type":"boolean"},"invidious":{"type":"string"},"encrypted_media":{"type":"boolean"},"sticky":{"type":"string"},"sticky_on_mobile":{"type":"string"},"sticky_position":{"type":"string"},"volume":{"type":"integer"},"arve_link":{"type":"string"},"random_video_url":{"type":"string"},"random_video_urls":{"type":"string"},"align":{"type":"string"},"aspect_ratio":{"type":"string"},"thumbnail_url":{"type":"string"}}}');

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
/*!****************************!*\
  !*** ./src/block/index.js ***!
  \****************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _block_json__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./block.json */ "./src/block/block.json");
/* harmony import */ var _edit__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./edit */ "./src/block/edit.tsx");
/**
 * Copyright 2019-2025 Nicolas Jonas
 * License: GPL 3.0
 */



const {
  registerBlockType
} = window.wp.blocks;
registerBlockType(_block_json__WEBPACK_IMPORTED_MODULE_0__, {
  edit: _edit__WEBPACK_IMPORTED_MODULE_1__.Edit,
  // save() is intentionally omitted because we're using ServerSideRender
  // which handles the frontend rendering on the server
  save: () => null
});
})();

/******/ })()
;
//# sourceMappingURL=index.js.map