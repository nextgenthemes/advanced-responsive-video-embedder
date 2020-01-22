!function(t){var e={};function n(r){if(e[r])return e[r].exports;var o=e[r]={i:r,l:!1,exports:{}};return t[r].call(o.exports,o,o.exports,n),o.l=!0,o.exports}n.m=t,n.c=e,n.d=function(t,e,r){n.o(t,e)||Object.defineProperty(t,e,{enumerable:!0,get:r})},n.r=function(t){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(t,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(t,"__esModule",{value:!0})},n.t=function(t,e){if(1&e&&(t=n(t)),8&e)return t;if(4&e&&"object"==typeof t&&t&&t.__esModule)return t;var r=Object.create(null);if(n.r(r),Object.defineProperty(r,"default",{enumerable:!0,value:t}),2&e&&"string"!=typeof t)for(var o in t)n.d(r,o,function(e){return t[e]}.bind(null,o));return r},n.n=function(t){var e=t&&t.__esModule?function(){return t.default}:function(){return t};return n.d(e,"a",e),e},n.o=function(t,e){return Object.prototype.hasOwnProperty.call(t,e)},n.p="/",n(n.s=1)}({1:function(t,e,n){t.exports=n("5fYu")},"5fYu":function(t,e){function n(t){return function(t){if(Array.isArray(t)){for(var e=0,n=new Array(t.length);e<t.length;e++)n[e]=t[e];return n}}(t)||function(t){if(Symbol.iterator in Object(t)||"[object Arguments]"===Object.prototype.toString.call(t))return Array.from(t)}(t)||function(){throw new TypeError("Invalid attempt to spread non-iterable instance")}()}function r(t,e,n){return e in t?Object.defineProperty(t,e,{value:n,enumerable:!0,configurable:!0,writable:!0}):t[e]=n,t}function o(t,e){return function(t){if(Array.isArray(t))return t}(t)||function(t,e){if(!(Symbol.iterator in Object(t)||"[object Arguments]"===Object.prototype.toString.call(t)))return;var n=[],r=!0,o=!1,i=void 0;try{for(var a,c=t[Symbol.iterator]();!(r=(a=c.next()).done)&&(n.push(a.value),!e||n.length!==e);r=!0);}catch(t){o=!0,i=t}finally{try{r||null==c.return||c.return()}finally{if(o)throw i}}return n}(t,e)||function(){throw new TypeError("Invalid attempt to destructure non-iterable instance")}()}var i=window.wp,a=window.wp.element.createElement,c=window.ARVEsettings;function u(t){var e=[],u={},s=new DOMParser;Object.values(c).forEach((function(t){u[t.tag]=[]})),Object.entries(c).forEach((function(e){var n,c,p=o(e,2),f=p[0],d=p[1],b=t.attributes[f],v={label:d.label,onChange:function(e){if("url"===f){var n=s.parseFromString(e,"text/html").querySelector("iframe");if(n&&n.hasAttribute("src")&&n.getAttribute("src")){e=n.src;var o=n.width,i=n.height;o&&i&&t.setAttributes({aspect_ratio:l(o,i)})}}t.setAttributes(r({},f,e))}};if("string"==typeof d.description&&(v.help=d.description,"string"==typeof d.descriptionlinktext)){var h=d.description.split(d.descriptionlinktext);v.help=a("span",null,a("span",{},h[0]),a("a",{href:d.descriptionlink},d.descriptionlinktext),a("span",{},h[1]))}switch(d.type){case"boolean":"sandbox"===f&&void 0===b&&(v.checked=!0),void 0!==b&&(v.checked=b),u[d.tag].push(a(i.components.ToggleControl,v));break;case"select":void 0!==b&&(v.selected=b,v.value=b),v.options=(n=d.options,c=[],Object.entries(n).forEach((function(t){var e=o(t,2),n=e[0],r=e[1];c.push({label:r,value:n})})),c),u[d.tag].push(a(i.components.SelectControl,v));break;case"string":void 0!==b&&(v.value=b),v.placeholder=d.placeholder,u[d.tag].push(a(i.components.TextControl,v));break;case"attachment":var g=t.attributes[f+"_url"];void 0===g&&(g=""),v.children=[a(i.editor.MediaUpload,{type:"image",onSelect:function(e){var n;return t.setAttributes((r(n={},f,e.id.toString()),r(n,f+"_url",e.url),n))},render:function(t){return a(i.components.Button,{className:"components-icon-button image-block-btn is-button is-default is-large",onClick:t.open},a("svg",{className:"dashicon dashicons-edit",width:"20",height:"20"},a("path",{d:"M2.25 1h15.5c.69 0 1.25.56 1.25 1.25v15.5c0 .69-.56 1.25-1.25 1.25H2.25C1.56 19 1 18.44 1 17.75V2.25C1 1.56 1.56 1 2.25 1zM17 17V3H3v14h14zM10 6c0-1.1-.9-2-2-2s-2 .9-2 2 .9 2 2 2 2-.9 2-2zm3 5s0-6 3-6v10c0 .55-.45 1-1 1H5c-.55 0-1-.45-1-1V8c2 0 3 4 3 4s1-3 3-3 3 2 3 2z"})),a("span",{}," Select image"))}}),a("img",{src:g,alt:"thumbnail"})],u[d.tag].push(a(i.components.BaseControl,v))}}));var p=!0;return Object.keys(u).forEach((function(t){e.push(a.apply(void 0,[i.components.PanelBody,{title:t,initialOpen:p}].concat(n(u[t])))),p=!1})),e}function l(t,e){var n=function t(e,n){if(!n)return e;return t(n,e%n)}(t,e);return t/n+":"+e/n}i.blocks.registerBlockType("nextgenthemes/arve-block",{title:"Video Embed (ARVE)",icon:"video-alt3",category:"embed",edit:function(t){var e=u(t);return[a(i.components.ServerSideRender,{block:"nextgenthemes/arve-block",attributes:t.attributes}),a.apply(void 0,[i.blockEditor.InspectorControls,{}].concat(n(e)))]},save:function(){return null}})}});
//# sourceMappingURL=gb-block.js.map