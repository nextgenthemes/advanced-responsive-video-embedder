!function(e){var t={};function r(n){if(t[n])return t[n].exports;var o=t[n]={i:n,l:!1,exports:{}};return e[n].call(o.exports,o,o.exports,r),o.l=!0,o.exports}r.m=e,r.c=t,r.d=function(e,t,n){r.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:n})},r.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},r.t=function(e,t){if(1&t&&(e=r(e)),8&t)return e;if(4&t&&"object"==typeof e&&e&&e.__esModule)return e;var n=Object.create(null);if(r.r(n),Object.defineProperty(n,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var o in e)r.d(n,o,function(t){return e[t]}.bind(null,o));return n},r.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return r.d(t,"a",t),t},r.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},r.p="/",r(r.s=0)}({0:function(e,t,r){r("1x4Z"),r("KGB7"),r("Kz3+"),e.exports=r("aQ6y")},"1x4Z":function(e,t){function r(e){return function(e){if(Array.isArray(e))return n(e)}(e)||function(e){if("undefined"!=typeof Symbol&&Symbol.iterator in Object(e))return Array.from(e)}(e)||function(e,t){if(!e)return;if("string"==typeof e)return n(e,t);var r=Object.prototype.toString.call(e).slice(8,-1);"Object"===r&&e.constructor&&(r=e.constructor.name);if("Map"===r||"Set"===r)return Array.from(r);if("Arguments"===r||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(r))return n(e,t)}(e)||function(){throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}()}function n(e,t){(null==t||t>e.length)&&(t=e.length);for(var r=0,n=new Array(t);r<t;r++)n[r]=e[r];return n}var o=document.querySelectorAll.bind(document);function i(e){var t=e.target,r=t.closest('.arve[mode="normal"]');t&&t.matches("iframe")&&r&&(a("arve--clicked","arve--clicked"),r.classList.add("arve--clicked"))}function a(){var e=arguments;o(arguments[0]).forEach((function(t){var n;e[0]="ngt-tmp",(n=t.classList).remove.apply(n,r(e))}))}function c(e){for(var t=e.parentNode;e.firstChild;)t.insertBefore(e.firstChild,e);t.removeChild(e)}function u(){o(".arve p, .arve .video-wrap, .arve .fluid-width-video-wrapper, .arve .fluid-vids").forEach((function(e){c(e)})),o(".arve br").forEach((function(e){e.remove()})),o(".arve-iframe, .arve-video").forEach((function(e){e.removeAttribute("width"),e.removeAttribute("height"),e.removeAttribute("style")})),o(".wp-block-embed").forEach((function(e){if(e.querySelector(".arve")){e.classList.remove(["wp-embed-aspect-16-9","wp-has-aspect-ratio"]);var t=e.querySelector(".wp-block-embed__wrapper");t&&c(t)}}))}function d(){"global"!==document.documentElement.id&&(document.documentElement.id?document.body.id||(document.body.id="global"):document.documentElement.id="global")}document.body.addEventListener("mouseover",i,!1),document.body.addEventListener("touchend",i,!1),document.body.addEventListener("play",(function(e){var t=e.target,r=t.closest(".arve");t&&t.matches("video")&&r&&(a("arve--clicked","arve--clicked"),r.classList.add("arve--clicked"))}),!0),u(),d(),document.addEventListener("DOMContentLoaded",(function(){u(),d()}))},KGB7:function(e,t){},"Kz3+":function(e,t){},aQ6y:function(e,t){}});
//# sourceMappingURL=arve.js.map