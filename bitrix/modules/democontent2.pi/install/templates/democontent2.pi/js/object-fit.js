!function(){"use strict";if("undefined"!=typeof window){var t=window.navigator.userAgent.match(/Edge\/(\d{2})\./),n=!!t&&16<=parseInt(t[1],10);if("objectFit"in document.documentElement.style!=0&&!n)return window.objectFitPolyfill=function(){return!1};var s=function(t,e,i){var n,o,l,a,d;if((i=i.split(" ")).length<2&&(i[1]=i[0]),"x"===t)n=i[0],o=i[1],l="left",a="right",d=e.clientWidth;else{if("y"!==t)return;n=i[1],o=i[0],l="top",a="bottom",d=e.clientHeight}return n===l||o===l?void(e.style[l]="0"):n===a||o===a?void(e.style[a]="0"):"center"===n||"50%"===n?(e.style[l]="50%",void(e.style["margin-"+l]=d/-2+"px")):0<=n.indexOf("%")?void((n=parseInt(n))<50?(e.style[l]=n+"%",e.style["margin-"+l]=d*(n/-100)+"px"):(n=100-n,e.style[a]=n+"%",e.style["margin-"+a]=d*(n/-100)+"px")):void(e.style[l]=n)},o=function(t){var e=t.dataset?t.dataset.objectFit:t.getAttribute("data-object-fit"),i=t.dataset?t.dataset.objectPosition:t.getAttribute("data-object-position");e=e||"cover",i=i||"50% 50%";var n,o,l,a,d,r=t.parentNode;n=r,l=(o=window.getComputedStyle(n,null)).getPropertyValue("position"),a=o.getPropertyValue("overflow"),d=o.getPropertyValue("display"),l&&"static"!==l||(n.style.position="relative"),"hidden"!==a&&(n.style.overflow="hidden"),d&&"inline"!==d||(n.style.display="block"),0===n.clientHeight&&(n.style.height="100%"),-1===n.className.indexOf("object-fit-polyfill")&&(n.className=n.className+" object-fit-polyfill"),function(t){var e=window.getComputedStyle(t,null),i={"max-width":"none","max-height":"none","min-width":"0px","min-height":"0px",top:"auto",right:"auto",bottom:"auto",left:"auto","margin-top":"0px","margin-right":"0px","margin-bottom":"0px","margin-left":"0px"};for(var n in i)e.getPropertyValue(n)!==i[n]&&(t.style[n]=i[n])}(t),t.style.position="absolute",t.style.height="100%",t.style.width="auto","scale-down"===e&&(t.style.height="auto",t.clientWidth<r.clientWidth&&t.clientHeight<r.clientHeight?(s("x",t,i),s("y",t,i)):(e="contain",t.style.height="100%")),"none"===e?(t.style.width="auto",t.style.height="auto",s("x",t,i),s("y",t,i)):"cover"===e&&t.clientWidth>r.clientWidth||"contain"===e&&t.clientWidth<r.clientWidth?(t.style.top="0",t.style.marginTop="0",s("x",t,i)):"scale-down"!==e&&(t.style.width="100%",t.style.height="auto",t.style.left="0",t.style.marginLeft="0",s("y",t,i))},e=function(t){if(void 0===t)t=document.querySelectorAll("[data-object-fit]");else if(t&&t.nodeName)t=[t];else{if("object"!=typeof t||!t.length||!t[0].nodeName)return!1;t=t}for(var e=0;e<t.length;e++)if(t[e].nodeName){var i=t[e].nodeName.toLowerCase();"img"!==i||n?"video"===i&&(0<t[e].readyState?o(t[e]):t[e].addEventListener("loadedmetadata",function(){o(this)})):t[e].complete?o(t[e]):t[e].addEventListener("load",function(){o(this)})}return!0};document.addEventListener("DOMContentLoaded",function(){e()}),window.addEventListener("resize",function(){e()}),window.objectFitPolyfill=e}}();