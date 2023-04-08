!function(){var e={184:function(e,t){var n;!function(){"use strict";var r={}.hasOwnProperty;function i(){for(var e=[],t=0;t<arguments.length;t++){var n=arguments[t];if(n){var o=typeof n;if("string"===o||"number"===o)e.push(n);else if(Array.isArray(n)){if(n.length){var a=i.apply(null,n);a&&e.push(a)}}else if("object"===o){if(n.toString!==Object.prototype.toString&&!n.toString.toString().includes("[native code]")){e.push(n.toString());continue}for(var l in n)r.call(n,l)&&n[l]&&e.push(l)}}}return e.join(" ")}e.exports?(i.default=i,e.exports=i):void 0===(n=function(){return i}.apply(t,[]))||(e.exports=n)}()}},t={};function n(r){var i=t[r];if(void 0!==i)return i.exports;var o=t[r]={exports:{}};return e[r](o,o.exports,n),o.exports}n.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return n.d(t,{a:t}),t},n.d=function(e,t){for(var r in t)n.o(t,r)&&!n.o(e,r)&&Object.defineProperty(e,r,{enumerable:!0,get:t[r]})},n.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},function(){"use strict";function e(){return e=Object.assign||function(e){for(var t=1;t<arguments.length;t++){var n=arguments[t];for(var r in n)Object.prototype.hasOwnProperty.call(n,r)&&(e[r]=n[r])}return e},e.apply(this,arguments)}var t=window.wp.element,r=JSON.parse('{"$schema":"https://schemas.wp.org/trunk/block.json","apiVersion":2,"name":"nextgenthemes/arve-block","title":"Video Embed (ARVE)","category":"embed","icon":"video-alt3","description":"Advanced Responsive Video Embedder","keywords":["embed","youtube","rumble","vimeo","odysee"],"version":"9.9.10-alpha2","textdomain":"advanced-responsive-video-embedder","supports":{"align":["wide","full","left","right"],"className":true,"customClassName":true},"styles":[],"example":{"attributes":{"url":"https://www.youtube.com/watch?v=oe452WcY7fA","title":"Example Title"}},"editorScript":"arve-block","editorStyle":["arve","file:../build/block.css"],"attributes":{"url":{"type":"string"},"title":{"type":"string"},"description":{"type":"string"},"upload_date":{"type":"string"},"mode":{"type":"string"},"thumbnail":{"type":"string"},"hide_title":{"type":"boolean"},"grow":{"type":"string"},"fullscreen":{"type":"string"},"play_icon_style":{"type":"string"},"hover_effect":{"type":"string"},"disable_links":{"type":"string"},"align":{"type":"string"},"arve_link":{"type":"string"},"duration":{"type":"string"},"autoplay":{"type":"string"},"lightbox_maxwidth":{"type":"integer"},"sticky":{"type":"string"},"sticky_on_mobile":{"type":"string"},"sticky_position":{"type":"string"},"aspect_ratio":{"type":"string"},"parameters":{"type":"string"},"controlslist":{"type":"string"},"controls":{"type":"string"},"loop":{"type":"boolean"},"muted":{"type":"boolean"},"volume":{"type":"integer"},"random_video_url":{"type":"string"},"random_video_urls":{"type":"string"},"sandbox":{"type":"string"},"thumbnail_url":{"type":"string"}}}'),i=window.wp.i18n,o=window.wp.serverSideRender,a=n.n(o),l=window.wp.blockEditor,s=window.wp.components,c=window.wp.blocks,p=n(184),u=n.n(p);const{name:d}=r,{settings:m,options:g}=window.ArveBlockJsBefore;delete m.align.options.center;const y=new DOMParser;function h(e){const t=[];return Object.entries(e).forEach((e=>{let[n,r]=e;const i={label:r,value:n};t.push(i)})),t}function b(e){const n=[],r={},o=(0,t.createElement)("p",null,(0,i.__)("To edit the featured image, you need permission to upload media."));let a;Object.values(m).forEach((e=>{r[e.tag]=[]})),Object.entries(m).forEach((n=>{let[c,p]=n;const u=e.attributes[c];r[p.tag].push((0,t.createElement)(t.Fragment,null,"boolean"===p.type&&(0,t.createElement)(s.ToggleControl,{key:c,label:p.label,help:f(p),checked:!!u,onChange:t=>e.setAttributes({[c]:t})}),"select"===p.type&&(0,t.createElement)(s.SelectControl,{key:c,value:u,label:p.label,help:f(p),options:h(p.options),onChange:t=>e.setAttributes({[c]:t})}),"string"===p.type&&(0,t.createElement)(s.TextControl,{key:c,label:p.label,placeholder:p.placeholder,help:f(p),value:u,onChange:t=>(function(e,t,n){if("url"===e){const e=y.parseFromString(t,"text/html").querySelector("iframe");if(e&&e.getAttribute("src")){t=e.src;const r=e.width,i=e.height;r&&i&&n.setAttributes({aspect_ratio:v(r,i)})}}}(c,t,e),e.setAttributes({[c]:t}))}),"attachment"===p.type&&(0,t.createElement)(s.BaseControl,{key:c,className:"editor-post-featured-image",help:f(p)},(0,t.createElement)(l.MediaUploadCheck,{fallback:o},(0,t.createElement)(l.MediaUpload,{title:(0,i.__)("Thumbnail"),onSelect:t=>(a=t,e.setAttributes({[c]:t.id.toString(),[c+"_url"]:t.url})),allowedTypes:["image"],modalClass:"editor-post-featured-image__media-modal",render:e=>{let{open:n}=e;return((e,n,r)=>(0,t.createElement)("div",{className:"editor-post-featured-image__container"},(0,t.createElement)(s.Button,{className:n?"editor-post-featured-image__preview":"editor-post-featured-image__toggle",onClick:e,"aria-label":n?(0,i.__)("Edit or update the image"):null,"aria-describedby":n?`editor-post-featured-image-${n}-describedby`:""},!!n&&!!r&&(0,t.createElement)("div",{style:{overflow:"hidden"}},(0,t.createElement)(s.ResponsiveWrapper,{naturalWidth:640,naturalHeight:360,isInline:!0},(0,t.createElement)("img",{src:r,alt:"ARVE Thumbnail",style:{width:"100%",height:"100%",objectFit:"cover"}}))),!n&&(0,i.__)("Set Thumbnail")),(0,t.createElement)(s.DropZone,null)))(n,u,"")},value:u})),!!u&&!1,!!u&&(0,t.createElement)(l.MediaUploadCheck,null,(0,t.createElement)(s.Button,{onClick:()=>e.setAttributes({[c]:"",[c+"_url"]:""}),isLink:!0,isDestructive:!0},(0,i.__)("Remove Thumbnail"))))))}));let c=!0;return r.main.push((0,t.createElement)(s.BaseControl,{key:"info",help:(0,i.__)("You can disable the extensive help texts on the ARVE settings page to clean up this UI","advanced-responsive-video-embedder")},(0,t.createElement)(s.BaseControl.VisualLabel,null,(0,i.__)("Info","advanced-responsive-video-embedder")))),Object.keys(r).forEach((e=>{var i;n.push((0,t.createElement)(s.PanelBody,{key:e,title:(i=e,i.charAt(0).toUpperCase()+i.slice(1)),initialOpen:c},r[e])),c=!1})),n}function f(e){if("string"!=typeof e.description)return"";if("string"==typeof e.descriptionlinktext){const n=e.description.split(e.descriptionlinktext);return(0,t.createElement)(t.Fragment,null,n[0],(0,t.createElement)("a",{href:e.descriptionlink},e.descriptionlinktext),n[1])}return e.description}function v(e,t){const n=_(e,t);return e/n+":"+t/n}function _(e,t){return t?_(t,e%t):e}(0,c.registerBlockType)(d,{edit:function(n){const{attributes:{mode:r,align:i,maxwidth:o}}=n;let s=!0;const c={},p=JSON.parse(JSON.stringify(n.attributes));delete p.align,delete p.maxwidth,!o||"left"!==i&&"right"!==i?"left"!==i&&"right"!==i||(c.width="100%",c.maxWidth=g.align_maxwidth):(c.width="100%",c.maxWidth=o);const d=(0,l.useBlockProps)({style:c});return("normal"===r||!r&&"normal"===g.mode)&&(s=!1),(0,t.createElement)(t.Fragment,null,(0,t.createElement)("div",e({},d,{key:"block"}),(0,t.createElement)(a(),{className:u()({"arve-ssr":!0,"arve-ssr--pointer-events-none":!s}),block:"nextgenthemes/arve-block",attributes:p,skipBlockSupportAttributes:!0})),(0,t.createElement)(l.InspectorControls,{key:"insp"},b(n)))}})}()}();