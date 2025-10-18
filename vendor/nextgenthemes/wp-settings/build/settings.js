import * as __WEBPACK_EXTERNAL_MODULE__wordpress_interactivity_8e89b257__ from "@wordpress/interactivity";
/******/ var __webpack_modules__ = ({

/***/ "./vendor/nextgenthemes/wp-settings/src/helpers.ts":
/*!*********************************************************!*\
  !*** ./vendor/nextgenthemes/wp-settings/src/helpers.ts ***!
  \*********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   aspectRatio: () => (/* binding */ aspectRatio),
/* harmony export */   debounce: () => (/* binding */ debounce)
/* harmony export */ });
function debounce(func, wait, immediate) {
  let timeout;
  return function (...args) {
    const context = this;
    const later = () => {
      timeout = undefined;
      if (!immediate) {
        func.apply(context, args);
      }
    };
    const callNow = immediate && !timeout;
    clearTimeout(timeout);
    timeout = window.setTimeout(later, wait);
    if (callNow) {
      func.apply(context, args);
    }
  };
}

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
    return `${w / arGCD}:${h / arGCD}`;
  }
  return `${width}:${height}`;
}

/**
 * Checks if the input string represents a positive integer.
 *
 * @param {string} str - the input string to be checked
 * @return {boolean} true if the input string represents a positive integer, false otherwise
 */
function isIntOverZero(str) {
  const n = Math.floor(Number(str));
  return n !== Infinity && String(n) === str && n > 0;
}

/**
 * Calculate the greatest common divisor of two numbers using the Euclidean algorithm.
 *
 * @param {number} a - the first number
 * @param {number} b - the second number
 * @return {number} the greatest common divisor of the two input numbers
 */
function gcd(a, b) {
  if (!b) {
    return a;
  }
  return gcd(b, a % b);
}

/***/ }),

/***/ "./vendor/nextgenthemes/wp-settings/src/settings.scss":
/*!************************************************************!*\
  !*** ./vendor/nextgenthemes/wp-settings/src/settings.scss ***!
  \************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "@wordpress/interactivity":
/*!*******************************************!*\
  !*** external "@wordpress/interactivity" ***!
  \*******************************************/
/***/ ((module) => {

module.exports = __WEBPACK_EXTERNAL_MODULE__wordpress_interactivity_8e89b257__;

/***/ })

/******/ });
/************************************************************************/
/******/ // The module cache
/******/ var __webpack_module_cache__ = {};
/******/ 
/******/ // The require function
/******/ function __webpack_require__(moduleId) {
/******/ 	// Check if module is in cache
/******/ 	var cachedModule = __webpack_module_cache__[moduleId];
/******/ 	if (cachedModule !== undefined) {
/******/ 		return cachedModule.exports;
/******/ 	}
/******/ 	// Create a new module (and put it into the cache)
/******/ 	var module = __webpack_module_cache__[moduleId] = {
/******/ 		// no module.id needed
/******/ 		// no module.loaded needed
/******/ 		exports: {}
/******/ 	};
/******/ 
/******/ 	// Execute the module function
/******/ 	__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 
/******/ 	// Return the exports of the module
/******/ 	return module.exports;
/******/ }
/******/ 
/************************************************************************/
/******/ /* webpack/runtime/define property getters */
/******/ (() => {
/******/ 	// define getter functions for harmony exports
/******/ 	__webpack_require__.d = (exports, definition) => {
/******/ 		for(var key in definition) {
/******/ 			if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 				Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 			}
/******/ 		}
/******/ 	};
/******/ })();
/******/ 
/******/ /* webpack/runtime/hasOwnProperty shorthand */
/******/ (() => {
/******/ 	__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ })();
/******/ 
/******/ /* webpack/runtime/make namespace object */
/******/ (() => {
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = (exports) => {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/ })();
/******/ 
/************************************************************************/
var __webpack_exports__ = {};
// This entry needs to be wrapped in an IIFE because it needs to be isolated against other modules in the chunk.
(() => {
/*!**********************************************************!*\
  !*** ./vendor/nextgenthemes/wp-settings/src/settings.ts ***!
  \**********************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _settings_scss__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./settings.scss */ "./vendor/nextgenthemes/wp-settings/src/settings.scss");
/* harmony import */ var _helpers__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./helpers */ "./vendor/nextgenthemes/wp-settings/src/helpers.ts");
/* harmony import */ var _wordpress_interactivity__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/interactivity */ "@wordpress/interactivity");



const domParser = new DOMParser();
const d = document;
const qs = d.querySelector.bind(d);
const dialog = qs('dialog[data-wp-interactive="nextgenthemes_arve_dialog"]');
setupInteractivityApi();
setBodyBackgroundColorAsCssVar();
function setBodyBackgroundColorAsCssVar() {
  const backgroundColor = window.getComputedStyle(d.body).backgroundColor;
  const wrap = qs('.wrap--nextgenthemes');
  if (wrap) {
    wrap.setAttribute('style', `--ngt-wp-body-bg: ${backgroundColor};`);
  }
}

// ACF
window.jQuery(document).on('click', '.arve-btn:not([data-editor="content"])', e => {
  e.preventDefault();
  const openBtn = qs('[data-wp-on--click="actions.openShortcodeDialog"][data-editor="content"]');
  const insertBtn = qs('[data-wp-on--click="actions.insertShortcode"]');
  if (!openBtn || !insertBtn || !dialog) {
    console.error('Open btn, insert btn od dialog not found'); // eslint-disable-line
    return;
  }
  openBtn.dispatchEvent(new Event('click'));
});
function setupInteractivityApi() {
  const namespace = qs('[data-wp-interactive^="nextgenthemes"]')?.dataset?.wpInteractive;
  if (!namespace) {
    // In ARVE this script will always be loaded but the config is only output when the media button is on the page
    return;
  }

  // eslint-disable-next-line @typescript-eslint/no-unused-vars
  const {
    state,
    actions,
    callbacks,
    helpers
  } = (0,_wordpress_interactivity__WEBPACK_IMPORTED_MODULE_2__.store)(namespace, {
    state: {
      isValidLicenseKey: () => {
        const context = (0,_wordpress_interactivity__WEBPACK_IMPORTED_MODULE_2__.getContext)();
        return 'valid' === state.options[context.option_key + '_status'];
      },
      is32charactersLong: () => {
        const context = (0,_wordpress_interactivity__WEBPACK_IMPORTED_MODULE_2__.getContext)();
        return state.options[context.option_key].length === 32;
      },
      get isActiveTab() {
        const context = (0,_wordpress_interactivity__WEBPACK_IMPORTED_MODULE_2__.getContext)();
        if (!context.activeTabs) {
          return true; // shortcode dialog has no sections
        }
        return true === context?.activeTabs[context.tab];
      }
    },
    actions: {
      toggleHelp: () => {
        state.help = !state.help;
      },
      openShortcodeDialog: event => {
        const editorId = event.target instanceof HTMLElement && event.target.dataset.editor;
        if (!dialog || !editorId) {
          console.error('Dialog or editorId not found'); // eslint-disable-line
          return;
        }
        dialog.dataset.editor = editorId;
        dialog.showModal();
      },
      insertShortcode: () => {
        const editorId = dialog?.dataset.editor;
        if (!editorId) {
          console.error('Editor ID not found'); // eslint-disable-line
        } else if ('content' === editorId) {
          window.wp.media.editor.insert(state.shortcode);
        } else {
          // Ensure TinyMCE is loaded and the editor exists
          if (typeof window.tinymce === 'undefined' || !window.tinymce.get(editorId)) {
            console.error('TinyMCE not initialized for field: ' + editorId); // eslint-disable-line
            return;
          }
          window.tinymce.get(editorId).insertContent(state.shortcode);
        }
        actions.closeShortcodeDialog();
      },
      closeShortcodeDialog: () => {
        if (dialog) {
          dialog.close();
        }
      },
      changeTab: () => {
        const context = (0,_wordpress_interactivity__WEBPACK_IMPORTED_MODULE_2__.getContext)();
        for (const key in context.activeTabs) {
          context.activeTabs[key] = false;
        }
        context.activeTabs[context.tab] = true;
      },
      inputChange: event => {
        const context = (0,_wordpress_interactivity__WEBPACK_IMPORTED_MODULE_2__.getContext)();
        const isInput = event?.target instanceof HTMLInputElement;
        const isSelect = event?.target instanceof HTMLSelectElement;
        if (!isInput && !isSelect) {
          throw new Error('event.target is not HTMLInputElement or HTMLSelectElement');
        }
        if ('arveUrl' in event.target.dataset) {
          helpers.extractFromEmbedCode(event.target.value);
        } else {
          state.options[context.option_key] = event.target.value;
        }
        if ('nextgenthemes_arve_dialog' !== namespace) {
          actions.saveOptions();
        }
      },
      checkboxChange: event => {
        const context = (0,_wordpress_interactivity__WEBPACK_IMPORTED_MODULE_2__.getContext)();
        state.options[context.option_key] = event.target.checked;
        if ('nextgenthemes_arve_dialog' !== namespace) {
          actions.saveOptions();
        }
      },
      selectImage: () => {
        if (dialog) {
          dialog.close();
        }
        const context = (0,_wordpress_interactivity__WEBPACK_IMPORTED_MODULE_2__.getContext)();
        const image = window.wp.media({
          title: 'Upload Image',
          multiple: false
        }).open().on('select', function () {
          // This will return the selected image from the Media Uploader, the result is an object
          const uploadedImage = image.state().get('selection').first();
          // We convert uploadedImage to a JSON object to make accessing it easier
          const attachmentID = uploadedImage.toJSON().id;
          state.options[context.option_key] = attachmentID;
          if (dialog) {
            dialog.showModal();
          }
        }).on('close', function () {
          if (dialog) {
            dialog.showModal();
          }
        });
      },
      deleteCaches: () => {
        const context = (0,_wordpress_interactivity__WEBPACK_IMPORTED_MODULE_2__.getContext)();
        actions.restCall('/delete-caches', {
          type: context.type,
          prefix: context.prefix,
          like: context.like,
          not_like: context.type,
          delete_option: context.delete_option
        });
      },
      // debounced version created later
      saveOptionsReal: () => {
        actions.restCall('/save', state.options);
      },
      restCall: (restRoute, body, refreshAfter = false) => {
        if (state.isSaving) {
          state.message = 'trying to save too fast';
          return;
        }
        const config = (0,_wordpress_interactivity__WEBPACK_IMPORTED_MODULE_2__.getConfig)();

        // set the state so that another save cannot happen while processing
        state.isSaving = true;
        state.message = 'Saving...';

        // Make a POST request to the REST API route that we registered in our PHP file
        fetch(config.restUrl + restRoute, {
          method: 'POST',
          body: JSON.stringify(body),
          headers: {
            'Content-Type': 'application/json',
            'X-WP-Nonce': config.nonce
          }
        }).then(response => {
          if (!response.ok) {
            // eslint-disable-next-line no-console
            console.log(response);
            throw new Error('Network response was not ok');
          }
          return response.json();
        }).then(message => {
          state.message = message;
          setTimeout(() => state.message = '', 666);
        }).catch(error => {
          state.message = error.message;
        }).finally(() => {
          state.isSaving = false;
          if (refreshAfter) {
            window.location.reload();
          }
        });
      },
      eddLicenseAction() {
        const context = (0,_wordpress_interactivity__WEBPACK_IMPORTED_MODULE_2__.getContext)();
        actions.restCall('/edd-license-action', {
          option_key: context.option_key,
          edd_store_url: context.edd_store_url,
          // EDD Store URL
          edd_action: context.edd_action,
          // edd api arg has same edd_ prefix
          item_id: context.edd_item_id,
          // edd api arg WITHOUT edd_ prefix
          license: state.options[context.option_key] // edd api arg WITHOUT edd_ prefix
        }, true);
      },
      resetOptionsSection() {
        const config = (0,_wordpress_interactivity__WEBPACK_IMPORTED_MODULE_2__.getConfig)();
        const context = (0,_wordpress_interactivity__WEBPACK_IMPORTED_MODULE_2__.getContext)();
        const sectionToReset = context.tab;
        Object.entries(config.defaultOptions).forEach(([section, options]) => {
          if ('all' === sectionToReset) {
            // reset all
            Object.entries(options).forEach(([key, value]) => {
              state.options[key] = value;
            });
          } else {
            Object.entries(options).forEach(([key, value]) => {
              if (section === sectionToReset) {
                state.options[key] = value;
              }
            });
          }
        });
        actions.saveOptionsReal();
      }
    },
    callbacks: {
      updateShortcode() {
        let out = '';
        for (const [key, value] of Object.entries(state.options)) {
          if ('credentialless' === key) {
            if (false === value) {
              out += `${key}="false" `;
            }
          } else if (true === value) {
            out += `${key}="true" `;
          } else if (value) {
            out += `${key}="${value}" `;
          }
        }
        state.shortcode = '[arve ' + out + '/]';
      }
      // updatePreview() {
      // 	const url = new URL( 'https://symbiosistheme.test/wp-json/arve/v1/shortcode' );
      // 	const params = new URLSearchParams();
      // 	const options = getContext< optionContext >().options;
      // 	const preview = document.getElementById( 'preview' );

      // 	if ( ! preview ) {
      // 		throw new Error( 'No preview element' );
      // 	}

      // 	for ( const [ key, value ] of Object.entries( options ) ) {
      // 		if ( true === value ) {
      // 			params.append( key, 'true' );
      // 		} else if ( value.length ) {
      // 			params.append( key, value );
      // 		}
      // 	}

      // 	url.search = params.toString();

      // 	fetch( url.href )
      // 		.then( ( response ) => response.json() )
      // 		.then( ( data ) => {
      // 			preview.innerHTML = data.html;
      // 		} )
      // 		.catch( () => {
      // 			//console.error( error );
      // 		} );
      // },
    },
    helpers: {
      debugJson: data => {
        state.debug = JSON.stringify(data, null, 2);
      },
      extractFromEmbedCode: url => {
        const iframe = domParser.parseFromString(url, 'text/html').querySelector('iframe');
        const srcAttr = iframe && iframe.getAttribute('src');
        if (srcAttr) {
          url = srcAttr;
          if (iframe.width && iframe.height) {
            const ratio = (0,_helpers__WEBPACK_IMPORTED_MODULE_1__.aspectRatio)(iframe.width, iframe.height);
            if ('16:9' !== ratio) {
              state.options.aspect_ratio = ratio;
            }
          }
        }
        state.options.url = url;
      }
    }
  });
  actions.saveOptions = (0,_helpers__WEBPACK_IMPORTED_MODULE_1__.debounce)(actions.saveOptionsReal, 1111);
}
})();


//# sourceMappingURL=settings.js.map