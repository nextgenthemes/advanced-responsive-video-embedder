(()=>{var e={84:(e,t)=>{var r;!function(){"use strict";var n={}.hasOwnProperty;function o(){for(var e="",t=0;t<arguments.length;t++){var r=arguments[t];r&&(e=a(e,i(r)))}return e}function i(e){if("string"==typeof e||"number"==typeof e)return e;if("object"!=typeof e)return"";if(Array.isArray(e))return o.apply(null,e);if(e.toString!==Object.prototype.toString&&!e.toString.toString().includes("[native code]"))return e.toString();var t="";for(var r in e)n.call(e,r)&&e[r]&&(t=a(t,r));return t}function a(e,t){return t?e?e+" "+t:e+t:e}e.exports?(o.default=o,e.exports=o):void 0===(r=function(){return o}.apply(t,[]))||(e.exports=r)}()}},t={};function r(n){var o=t[n];if(void 0!==o)return o.exports;var i=t[n]={exports:{}};return e[n](i,i.exports,r),i.exports}r.n=e=>{var t=e&&e.__esModule?()=>e.default:()=>e;return r.d(t,{a:t}),t},r.d=(e,t)=>{for(var n in t)r.o(t,n)&&!r.o(e,n)&&Object.defineProperty(e,n,{enumerable:!0,get:t[n]})},r.o=(e,t)=>Object.prototype.hasOwnProperty.call(e,t),(()=>{"use strict";const e=window.React,t=JSON.parse('{"name":"nextgenthemes/arve-block"}'),n=window.wp.i18n,o=window.wp.serverSideRender;var i=r.n(o);const a=window.wp.blockEditor,l=window.wp.components,s=window.wp.blocks;var c=r(84),u=r.n(c);const{name:d}=t,{settings:p,options:m}=window.ArveBlockJsBefore;delete p.align.options.center;const h=new DOMParser;function f(e){const t=[];return Object.entries(e).forEach((([e,r])=>{const n={label:r,value:e};t.push(n)})),t}function g(t){const r=[],o={},i=(0,e.createElement)("p",null,(0,n.__)("To edit the featured image, you need permission to upload media."));let s;Object.values(p).forEach((e=>{o[e.tag]=[]})),Object.entries(p).forEach((([r,c])=>{const u=t.attributes[r];o[c.tag].push((0,e.createElement)(e.Fragment,null,"boolean"===c.type&&(0,e.createElement)(l.ToggleControl,{key:r,label:c.label,help:b(c),checked:!!u,onChange:e=>t.setAttributes({[r]:e})}),"select"===c.type&&(0,e.createElement)(l.SelectControl,{key:r,value:u,label:c.label,help:b(c),options:f(c.options),onChange:e=>t.setAttributes({[r]:e})}),"string"===c.type&&(0,e.createElement)(l.TextControl,{key:r,label:c.label,placeholder:c.placeholder,help:b(c),value:u,onChange:e=>(function(e,t,r){if("url"===e){const e=h.parseFromString(t,"text/html").querySelector("iframe");if(e&&e.getAttribute("src")){t=e.src;const n=e.width,o=e.height;n&&o&&r.setAttributes({aspect_ratio:v(n,o)})}}}(r,e,t),t.setAttributes({[r]:e}))}),"attachment"===c.type&&(0,e.createElement)(l.BaseControl,{key:r,className:"editor-post-featured-image",help:b(c)},(0,e.createElement)(a.MediaUploadCheck,{fallback:i},(0,e.createElement)(a.MediaUpload,{title:(0,n.__)("Thumbnail"),onSelect:e=>(s=e,t.setAttributes({[r]:e.id.toString(),[r+"_url"]:e.url})),allowedTypes:["image"],modalClass:"editor-post-featured-image__media-modal",render:({open:t})=>((t,r,o)=>(0,e.createElement)("div",{className:"editor-post-featured-image__container"},(0,e.createElement)(l.Button,{className:r?"editor-post-featured-image__preview":"editor-post-featured-image__toggle",onClick:t,"aria-label":r?(0,n.__)("Edit or update the image"):null,"aria-describedby":r?`editor-post-featured-image-${r}-describedby`:""},!!r&&!!o&&(0,e.createElement)("div",{style:{overflow:"hidden"}},(0,e.createElement)(l.ResponsiveWrapper,{naturalWidth:640,naturalHeight:360,isInline:!0},(0,e.createElement)("img",{src:o,alt:"ARVE Thumbnail",style:{width:"100%",height:"100%",objectFit:"cover"}}))),!r&&(0,n.__)("Set Thumbnail")),(0,e.createElement)(l.DropZone,null)))(t,u,""),value:u})),!!u&&!1,!!u&&(0,e.createElement)(a.MediaUploadCheck,null,(0,e.createElement)(l.Button,{onClick:()=>t.setAttributes({[r]:"",[r+"_url"]:""}),isLink:!0,isDestructive:!0},(0,n.__)("Remove Thumbnail"))))))}));let c=!0;return o.main.push((0,e.createElement)(l.BaseControl,{key:"info",help:(0,n.__)("You can disable the extensive help texts on the ARVE settings page to clean up this UI","advanced-responsive-video-embedder")},(0,e.createElement)(l.BaseControl.VisualLabel,null,(0,n.__)("Info","advanced-responsive-video-embedder")))),Object.keys(o).forEach((t=>{var n;r.push((0,e.createElement)(l.PanelBody,{key:t,title:(n=t,n.charAt(0).toUpperCase()+n.slice(1)),initialOpen:c},o[t])),c=!1})),r}function b(t){if("string"!=typeof t.description)return"";if("string"==typeof t.descriptionlinktext){const r=t.description.split(t.descriptionlinktext);return(0,e.createElement)(e.Fragment,null,r[0],(0,e.createElement)("a",{href:t.descriptionlink},t.descriptionlinktext),r[1])}return t.description}function v(e,t){const r=y(e,t);return e/r+":"+t/r}function y(e,t){return t?y(t,e%t):e}(0,s.registerBlockType)(d,{edit:function(t){const{attributes:{mode:r,align:n,maxwidth:o}}=t;let l=!0;const s={},c=JSON.parse(JSON.stringify(t.attributes));delete c.align,delete c.maxwidth,!o||"left"!==n&&"right"!==n?"left"!==n&&"right"!==n||(s.width="100%",s.maxWidth=m.align_maxwidth):(s.width="100%",s.maxWidth=o);const d=(0,a.useBlockProps)({style:s});return("normal"===r||!r&&"normal"===m.mode)&&(l=!1),(0,e.createElement)(e.Fragment,null,(0,e.createElement)("div",{...d,key:"block"},(0,e.createElement)(i(),{className:u()({"arve-ssr":!0,"arve-ssr--pointer-events-none":!l}),block:"nextgenthemes/arve-block",attributes:c,skipBlockSupportAttributes:!0})),(0,e.createElement)(a.InspectorControls,{key:"insp"},g(t)))}})})()})();