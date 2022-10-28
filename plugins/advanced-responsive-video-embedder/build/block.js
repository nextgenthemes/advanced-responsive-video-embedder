!function(){"use strict";var e={};function t(){return t=Object.assign||function(e){for(var t=1;t<arguments.length;t++){var n=arguments[t];for(var r in n)Object.prototype.hasOwnProperty.call(n,r)&&(e[r]=n[r])}return e},t.apply(this,arguments)}e.n=function(t){var n=t&&t.__esModule?function(){return t.default}:function(){return t};return e.d(n,{a:n}),n},e.d=function(t,n){for(var r in n)e.o(n,r)&&!e.o(t,r)&&Object.defineProperty(t,r,{enumerable:!0,get:n[r]})},e.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)};var n=window.wp.element,r=JSON.parse('{"$schema":"https://schemas.wp.org/trunk/block.json","apiVersion":2,"name":"nextgenthemes/arve-block","title":"Video Embed (ARVE)","category":"embed","icon":"video-alt3","description":"Advanced Responsive Video Embedder","keywords":["embed","youtube","rumble","vimeo","odysee"],"version":"9.7.18","textdomain":"advanced-responsive-video-embedder","supports":{"align":["wide","full"]},"styles":[],"example":{"attributes":{"url":"https://www.youtube.com/watch?v=oe452WcY7fA","title":"Example Title"}},"editorScript":"arve-block","editorStyle":"arve","attributes":{"url":{"type":"string"},"title":{"type":"string"},"description":{"type":"string"},"upload_date":{"type":"string"},"mode":{"type":"string"},"thumbnail":{"type":"string"},"hide_title":{"type":"boolean"},"grow":{"type":"string"},"fullscreen":{"type":"string"},"play_icon_style":{"type":"string"},"hover_effect":{"type":"string"},"disable_links":{"type":"string"},"align":{"type":"string"},"arve_link":{"type":"string"},"duration":{"type":"string"},"autoplay":{"type":"string"},"lightbox_maxwidth":{"type":"integer"},"sticky":{"type":"string"},"sticky_on_mobile":{"type":"string"},"sticky_position":{"type":"string"},"aspect_ratio":{"type":"string"},"parameters":{"type":"string"},"controlslist":{"type":"string"},"controls":{"type":"string"},"loop":{"type":"boolean"},"muted":{"type":"boolean"},"volume":{"type":"integer"},"random_video_url":{"type":"string"},"random_video_urls":{"type":"string"},"sandbox":{"type":"string"},"thumbnail_url":{"type":"string"}}}'),i=window.wp.i18n,a=window.wp.serverSideRender,l=e.n(a),o=window.wp.blockEditor,s=window.wp.components,c=window.wp.blocks;const{name:d}=r,p=window.ARVEsettings,u=new DOMParser;function m(e){const t=[];return Object.entries(e).forEach((e=>{let[n,r]=e;const i={label:r,value:n};t.push(i)})),t}function b(e){if("string"!=typeof e.description)return"";if("string"==typeof e.descriptionlinktext){const t=e.description.split(e.descriptionlinktext);return(0,n.createElement)("span",null,(0,n.createElement)("span",null,t[0]),(0,n.createElement)("a",{href:e.descriptionlink},e.descriptionlinktext),",",(0,n.createElement)("span",null,t[1]))}return e.description}function g(e,t){const n=y(e,t);return e/n+":"+t/n}function y(e,t){return t?y(t,e%t):e}(0,c.registerBlockType)(d,{edit:function(e){const{attributes:{align:r},setAttributes:a}=e,c=(0,o.useBlockProps)(),d=function(e){const t=[],r={},a=(0,n.createElement)("p",null,(0,i.__)("To edit the featured image, you need permission to upload media."));let l=!1;Object.values(p).forEach((e=>{r[e.tag]=[]})),Object.entries(p).forEach((t=>{let[c,d]=t,p=e.attributes[c],y="";switch(d.type){case"boolean":"sandbox"===c&&void 0===p&&(p=!0),r[d.tag].push((0,n.createElement)(s.ToggleControl,{key:c,label:d.label,help:b(d),checked:!!p,onChange:t=>e.setAttributes({[c]:t})}));break;case"select":r[d.tag].push((0,n.createElement)(s.SelectControl,{key:c,value:p,label:d.label,help:b(d),options:m(d.options),onChange:t=>e.setAttributes({[c]:t})}));break;case"string":r[d.tag].push((0,n.createElement)(s.TextControl,{key:c,label:d.label,placeholder:d.placeholder,help:b(d),value:p,onChange:t=>(function(e,t,n){if("url"===e){const e=u.parseFromString(t,"text/html").querySelector("iframe");if(e&&e.getAttribute("src")){t=e.src;const r=e.width,i=e.height;r&&i&&n.setAttributes({aspect_ratio:g(r,i)})}}}(c,t,e),e.setAttributes({[c]:t}))}));break;case"attachment":y=e.attributes[c+"_url"],r[d.tag].push((0,n.createElement)(s.BaseControl,{className:"editor-post-featured-image",help:b(d),key:c},(0,n.createElement)(o.MediaUploadCheck,{fallback:a},(0,n.createElement)(o.MediaUpload,{title:(0,i.__)("Thumbnail"),onSelect:t=>(l=t,e.setAttributes({[c]:t.id.toString(),[c+"_url"]:t.url})),unstableFeaturedImageFlow:!0,allowedTypes:["image"],modalClass:"editor-post-featured-image__media-modal",render:e=>{let{open:t}=e;return(0,n.createElement)("div",{className:"editor-post-featured-image__container"},(0,n.createElement)(s.Button,{className:p?"editor-post-featured-image__preview":"editor-post-featured-image__toggle",onClick:t,"aria-label":p?(0,i.__)("Edit or update the image"):null,"aria-describedby":p?`editor-post-featured-image-${p}-describedby`:""},!!p&&!!y&&(0,n.createElement)("div",{style:{overflow:"hidden"}},(0,n.createElement)(s.ResponsiveWrapper,{naturalWidth:640,naturalHeight:360,isInline:!0},(0,n.createElement)("img",{src:y,alt:"ARVE Thumbnail",style:{width:"100%",height:"100%",objectFit:"cover"}}))),!p&&(0,i.__)("Set Thumbnail")),(0,n.createElement)(s.DropZone,null))},value:p})),!!p&&!!y&&(0,n.createElement)(o.MediaUploadCheck,null,(0,n.createElement)(o.MediaUpload,{title:(0,i.__)("Thumbnail"),onSelect:t=>(l=t,e.setAttributes({[c]:t.id.toString(),[c+"_url"]:t.url})),unstableFeaturedImageFlow:!0,allowedTypes:["image"],modalClass:"editor-post-featured-image__media-modal",render:e=>{let{open:t}=e;return(0,n.createElement)(s.Button,{onClick:t,isSecondary:!0},(0,i.__)("Replace Thumbnail"))}})),!!p&&(0,n.createElement)(o.MediaUploadCheck,null,(0,n.createElement)(s.Button,{onClick:()=>e.setAttributes({[c]:"",[c+"_url"]:""}),isLink:!0,isDestructive:!0},(0,i.__)("Remove Thumbnail")))))}}));let c=!0;return r.main.push((0,n.createElement)(s.BaseControl,{key:"info",help:(0,i.__)("You can disable the extensive help texts on the ARVE settings page to clean up this UI","advanced-responsive-video-embedder")},(0,n.createElement)(s.BaseControl.VisualLabel,null,(0,i.__)("Info","advanced-responsive-video-embedder")))),Object.keys(r).forEach((e=>{var i;t.push((0,n.createElement)(s.PanelBody,{key:e,title:(i=e,i.charAt(0).toUpperCase()+i.slice(1)),initialOpen:c},r[e])),c=!1})),t}(e);return[(0,n.createElement)("div",t({},c,{key:"block"}),(0,n.createElement)("div",{className:"arve-select-helper",style:{textAlign:"center",padding:".1em"}},(0,i.__)("Select ARVE block","advanced-responsive-video-embedder")),(0,n.createElement)(l(),{block:"nextgenthemes/arve-block",attributes:e.attributes})),(0,n.createElement)(o.InspectorControls,{key:"insp"},d)]}})}();