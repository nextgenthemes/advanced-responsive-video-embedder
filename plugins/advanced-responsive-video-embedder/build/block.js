!function(e){var t={};function n(r){if(t[r])return t[r].exports;var l=t[r]={i:r,l:!1,exports:{}};return e[r].call(l.exports,l,l.exports,n),l.l=!0,l.exports}n.m=e,n.c=t,n.d=function(e,t,r){n.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:r})},n.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},n.t=function(e,t){if(1&t&&(e=n(e)),8&t)return e;if(4&t&&"object"==typeof e&&e&&e.__esModule)return e;var r=Object.create(null);if(n.r(r),Object.defineProperty(r,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var l in e)n.d(r,l,function(t){return e[t]}.bind(null,l));return r},n.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return n.d(t,"a",t),t},n.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},n.p="",n(n.s=9)}([function(e,t){e.exports=window.wp.element},function(e,t){e.exports=window.wp.components},function(e,t){e.exports=window.wp.i18n},function(e,t){e.exports=window.wp.blockEditor},,function(e,t){e.exports=window.wp.serverSideRender},,,,function(e,t,n){"use strict";n.r(t);var r=n(2),l=n(5),a=n.n(l),o=n(0),c=n(3),i=n(1);const s=window.ARVEsettings,u=window.wp,b=new DOMParser;function d(e){const t=[];return Object.entries(e).forEach(([e,n])=>{const r={label:n,value:e};t.push(r)}),t}function p(e){if("string"!=typeof e.description)return"";if("string"==typeof e.descriptionlinktext){const t=e.description.split(e.descriptionlinktext);return Object(o.createElement)("span",null,Object(o.createElement)("span",null,t[0]),Object(o.createElement)("a",{href:e.descriptionlink},e.descriptionlinktext),",",Object(o.createElement)("span",null,t[1]))}return e.description}function m(e,t){const n=function e(t,n){return n?e(n,t%n):t}(e,t);return e/n+":"+t/n}u.blocks.registerBlockType("nextgenthemes/arve-block",{title:"Video Embed (ARVE)",description:"You can disable help texts on the ARVE settings page to clean up the UI",icon:"video-alt3",category:"embed",supports:{AlignWide:!0,align:["left","right","center","wide","full"]},edit:e=>{const t=function(e){const t=[],n={},l=Object(o.createElement)("p",null,Object(r.__)("To edit the featured image, you need permission to upload media."));let a=!1;Object.values(s).forEach(e=>{n[e.tag]=[]}),Object.entries(s).forEach(([t,s])=>{let u=e.attributes[t],h="";switch(s.type){case"boolean":"sandbox"===t&&void 0===u&&(u=!0),n[s.tag].push(Object(o.createElement)(i.ToggleControl,{key:t,label:s.label,help:p(s),checked:!!u,onChange:n=>e.setAttributes({[t]:n})}));break;case"select":n[s.tag].push(Object(o.createElement)(i.SelectControl,{key:t,value:u,label:s.label,help:p(s),options:d(s.options),onChange:n=>e.setAttributes({[t]:n})}));break;case"string":n[s.tag].push(Object(o.createElement)(i.TextControl,{key:t,label:s.label,placeholder:s.placeholder,help:p(s),value:u,onChange:n=>(function(e,t,n){if("url"===e){const e=b.parseFromString(t,"text/html").querySelector("iframe");if(e&&e.getAttribute("src")){t=e.src;const r=e.width,l=e.height;r&&l&&n.setAttributes({aspect_ratio:m(r,l)})}}}(t,n,e),e.setAttributes({[t]:n}))}));break;case"attachment_old":h=e.attributes[t+"_url"],n[s.tag].push(Object(o.createElement)("div",null,Object(o.createElement)(c.MediaUploadCheck,null,Object(o.createElement)(c.MediaUpload,{onSelect:n=>e.setAttributes({[t]:n.id.toString(),[t+"_url"]:n.url}),allowedTypes:"image",render:({open:e})=>Object(o.createElement)(i.Button,{className:"components-button--arve-thumbnail",onClick:e,"aria-label":Object(r.__)("Edit or update the image")},!!h&&Object(o.createElement)("div",null,Object(o.createElement)("img",{src:h,alt:Object(r.__)("Selected Thumbnail")})),Object(r.__)("Edit or update the image"))})),!!u&&Object(o.createElement)(i.Button,{onClick:()=>e.setAttributes({[t]:"",[t+"_url"]:""})},Object(r.__)("Remove Custom Thumbnail")),Object(o.createElement)(i.TextControl,{label:s.label,placeholder:s.placeholder,help:p(s),value:u,onChange:n=>e.setAttributes({[t]:n})})));break;case"attachment":h=e.attributes[t+"_url"],n[s.tag].push(Object(o.createElement)("div",{className:"editor-post-featured-image"},Object(o.createElement)(c.MediaUploadCheck,{fallback:l},Object(o.createElement)(c.MediaUpload,{title:Object(r.__)("Thumbnail"),onSelect:n=>(a=n,e.setAttributes({[t]:n.id.toString(),[t+"_url"]:n.url})),unstableFeaturedImageFlow:!0,allowedTypes:"Image",modalClass:"editor-post-featured-image__media-modal",render:({open:e})=>Object(o.createElement)("div",{className:"editor-post-featured-image__container"},Object(o.createElement)(i.Button,{className:u?"editor-post-featured-image__preview":"editor-post-featured-image__toggle",onClick:e,"aria-describedby":u?`editor-post-featured-image-${u}-describedby`:""},!!u&&!!h&&Object(o.createElement)(i.ResponsiveWrapper,{naturalWidth:640,naturalHeight:380},Object(o.createElement)("img",{src:h,alt:""})),!u&&Object(r.__)("Set Thumbnail")),Object(o.createElement)(i.DropZone,null)),value:u})),!!u&&!!h&&Object(o.createElement)(c.MediaUploadCheck,null,Object(o.createElement)(c.MediaUpload,{title:Object(r.__)("Thumbnail"),onSelect:n=>(a=n,e.setAttributes({[t]:n.id.toString(),[t+"_url"]:n.url})),unstableFeaturedImageFlow:!0,allowedTypes:"image",modalClass:"editor-post-featured-image__media-modal",render:({open:e})=>Object(o.createElement)(i.Button,{onClick:e,isSecondary:!0},Object(r.__)("Replace Thumbnail"))})),!!u&&Object(o.createElement)(c.MediaUploadCheck,null,Object(o.createElement)(i.Button,{onClick:()=>e.setAttributes({[t]:"",[t+"_url"]:""}),isLink:!0,isDestructive:!0},Object(r.__)("Remove Thumbnail"))),Object(o.createElement)(i.TextControl,{label:s.label,placeholder:s.placeholder,help:p(s),value:u,onChange:n=>e.setAttributes({[t]:n})})))}});let u=!0;return Object.keys(n).forEach(e=>{var r;t.push(Object(o.createElement)(i.PanelBody,{key:e,title:(r=e,r.charAt(0).toUpperCase()+r.slice(1)),initialOpen:u},n[e])),u=!1}),t}(e);return[Object(o.createElement)(a.a,{key:"ssr",block:"nextgenthemes/arve-block",attributes:e.attributes}),Object(o.createElement)(c.InspectorControls,{key:"insp"},t)]},save:()=>null})}]);