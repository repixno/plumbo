var XD=function(){var b,c,a=1,d,e=this;return{serialize:function(a){var b="",c=!0,d;for(d in a)c||(b+="&"),c=!1,b+=encodeURIComponent(d)+"="+encodeURIComponent(a[d]);return b},deserialize:function(a){for(var b={},a=a.split("&"),c=0;c<a.length;c++){var d=a[c].split("=");b[decodeURIComponent(d[0])]=decodeURIComponent(d[1])}return b},postMessage:function(b,c,d){if(c&&(d=d||parent))if(e.postMessage)d.postMessage(b,c.replace(/([^:]+:\/\/[^\/]+).*/,"$1"));else if(c)d.location=c.replace(/#.*$/,"")+"#"+ +new Date+
a++ +"&"+b},receiveMessage:function(a,f){if(e.postMessage)if(a&&(d=function(b){if("string"===typeof f&&b.origin!==f||"[object Function]"===Object.prototype.toString.call(f)&&!1===f(b.origin))return!1;a(b)}),e.addEventListener)e[a?"addEventListener":"removeEventListener"]("message",d,!1);else e[a?"attachEvent":"detachEvent"]("onmessage",d);else b&&clearInterval(b),b=null,a&&(b=setInterval(function(){var b=document.location.hash,d=/^#?\d+&/;b!==c&&d.test(b)&&(c=b,a({data:b.replace(d,"")}))},100))}}}(),
IFRAME_AT_PUBL_COM="";XD.receiveMessage(function(b){b=XD.deserialize(b.data);if("publ"==b.from)IFRAME_AT_PUBL_COM=b.url,XD.postMessage(XD.serialize({type:"connect"}),IFRAME_AT_PUBL_COM)},function(){return!0});PUBL=!1;
var workspace={w:1200,h:800,listeners:[],addListener:function(b){workspace.listeners.push(b)},getSize:function(){browser.supportHTML5?(workspace.w=window.innerWidth?window.innerWidth:workspace.w,workspace.h=window.innerHeight?window.innerHeight:workspace.h):(workspace.w=document.documentElement.clientWidth?document.documentElement.clientWidth:workspace.w,workspace.h=document.documentElement.clientHeight?document.documentElement.clientHeight:workspace.h)},callFn:function(){for(var b=0;b<workspace.listeners.length;b++)workspace.listeners[b].call(this)}},
blackBG={listeners:[],create:function(b){document.getElementById("blackBG")&&blackBG.remove();var b=b||!1,c=document.createElement("div");c.id="blackBG";c.style.width=workspace.w+"px";c.style.height=workspace.h+"px";c.style.position="fixed";c.style.top="0px";c.style.left="0px";c.style.display="block";c.style.zIndex="1000000";browser.supportHTML5?c.style.backgroundColor="rgba(0,0,0,0.7)":c.style.background='url("./style/bg_opacity.png")';browser.setEvent(c,"click",blackBG.remove);if(b)b=document.createElement("div"),
b.className="closeBG",c.appendChild(b);document.getElementsByTagName("body")[0].style.overflow="hidden";workspace.addListener(blackBG.resize);document.getElementById("bodyBg").parentNode.appendChild(c)},addListener:function(b){this.listeners.push(b)},checkListener:function(b){for(var c=!1,a=0;a<blackBG.listeners.length;a++)if(blackBG.listeners[a]==b){c=!0;break}return c},remListener:function(b){var c=blackBG.listeners;blackBG.listeners=[];for(var a=0;a<c.length;a++)c[a]!=b&&blackBG.addListener(c[a])},
remove:function(){document.getElementsByTagName("body")[0].style.overflow="visible";var b=document.getElementById("blackBG");b.parentNode.removeChild(b);for(b=0;b<blackBG.listeners.length;b++)blackBG.listeners[b].call(this);try{event.preventDefault(),event.stopPropagation()}catch(c){}},resize:function(){if(document.getElementById("blackBG")){var b=document.getElementById("blackBG");b.style.width=workspace.w+"px";b.style.height=workspace.h+"px"}}},notification={noFlash:!1,show:function(){if(-1!=window.location.hash.indexOf("noFlash")){notification.noFlash=
!0;blackBG.create();var b="",b=""!=PublicationTypeUrl?'<a id="pblType" href="'+PublicationTypeUrl+'" style="text-decoration: none; color: black;">'+LOCALS[PublicationTypeName.toUpperCase()]+"</a>":LOCALS[PublicationTypeName.toUpperCase()],c=document.getElementById("blackBG");c.innerHTML='<div id="flash-notification" style="width:600px; min-height:200px;  margin: auto; position: relative; top: 30%; padding: 18px; overflow: hidden; background-color: rgb(255, 255, 255); border: 1px solid rgb(200, 200, 200); border-radius: 7px; font-style: normal; font-variant: normal; font-weight: normal; font-size: 12px; line-height: 14px; font-family: Arial; position: relative; background-position: initial initial; background-repeat: initial initial; -webkit-box-shadow: 0px 3px 10px rgba(0,0,0,.8);-moz-box-shadow: 0px 3px 10px rgba(0,0,0,.8); box-shadow: 0px 3px 10px rgba(0,0,0,.8);"><a id="closeButton" href="javascript:void();" style="display:block;width:30px;height:30px;text-decoration:none;font:0/0 a;cursor:pointer;background:url(style/close.png);position:absolute;top:5px;right:5px;left:auto;bottom:auto;"></a><div style="display:block;cursor:pointer;min-width:50px;min-height:50px;display:block;float:left;text-decoration:none;margin:10px 28px 10px 10px; "><img id="noteImage" onload="notification.animated(this);"  style="-webkit-box-shadow: 0px 3px 10px rgba(0,0,0,.5);-moz-box-shadow: 0px 3px 10px rgba(0,0,0,.5); box-shadow: 0px 3px 10px rgba(0,0,0,.5);" src="'+
COVER_SRC+'"></div><div id="noteText" style="margin-left: 128px;"><h3 style="font-weight:bold;padding: 14px 10px 10px 10px; font-size:18px; margin-right: 20px;line-height: 22px;">'+TITLE+'</h3><div style="padding: 10px;">'+LOCALS.NOTIFICATION_TEXT.replace("{0}",b)+'</div><a id="getFlash" href="http://get.adobe.com/flashplayer/" style="display:block;text-decoration:none;margin:15px 10px 0 10px;" target="_blank"><img src="http://www.adobe.com/macromedia/style_guide/images/160x41_Get_Flash_Player.jpg" alt="" border="0" style="border:0;display:block;" width="160" height="41"></a></div></div>';
browser.supportHTML5?c.addEventListener("DOMContentLoaded",notification.eventFn(),!1):window.setTimeout(notification.eventFn,100)}},eventFn:function(){var b=document.getElementById("flash-notification");browser.setEvent(b,"click",function(a){a.preventDefault?a.preventDefault():a.returnValue=!1;a.stopPropagation?a.stopPropagation():a.cancelBubble=!0});browser.setEvent(b.childNodes[0],"click",function(a){blackBG.remove(a);a.preventDefault?a.preventDefault():a.returnValue=!1;a.stopPropagation?a.stopPropagation():
a.cancelBubble=!0});var c=function(a){window.location.replace(FULL_SRC);a.preventDefault?a.preventDefault():a.returnValue=!1;a.stopPropagation?a.stopPropagation():a.cancelBubble=!0};b.getElementsByTagName("img")&&browser.setEvent(b.getElementsByTagName("img")[0].parentNode,"click",c);b=function(a){window.open(PublicationTypeUrl);a.preventDefault?a.preventDefault():a.returnValue=!1;a.stopPropagation?a.stopPropagation():a.cancelBubble=!0};document.getElementById("pblType")&&setEvent(document.getElementById("pblType"),
"click",b);b=document.getElementById("getFlash");browser.setEvent(b.childNodes[0],"click",function(a){window.open("http://get.adobe.com/flashplayer/","","");a.preventDefault?a.preventDefault():a.returnValue=!1;a.stopPropagation?a.stopPropagation():a.cancelBubble=!0})},animated:function(b){b=b.parentNode.parentNode;b.childNodes[2].style.marginLeft=b.childNodes[1].offsetWidth+28+"px";b.childNodes[1].innerHTML='<iframe id="sprateIframe" style="-webkit-box-shadow: 0px 3px 10px rgba(0,0,0,.5);-moz-box-shadow: 0px 3px 10px rgba(0,0,0,.5); box-shadow: 0px 3px 10px rgba(0,0,0,.5);" frameborder="0"  width="'+
b.childNodes[1].offsetWidth+'px" height="'+(b.childNodes[1].offsetHeight-3)+'px"  title='+TITLE+' src="'+SPREAD_SRC+'" type="text/html" scrolling="no" marginwidth="0" marginheight="0"></iframe>'}},video={play:function(b,c){blackBG.create();var a=document.createElement("div");a.id="videoPlayer";a.style.position="relative";a.style.margin="5% auto";a.style.minHeight="200px";a.style.minWidth="300px";a.style.maxHeight="85%";a.style.maxWidth="80%";a.style.width="85%";a.style.height="85%";a.style.backgroundColor=
"rgba(0,0,0,1)";document.getElementById("blackBG").appendChild(a);var d=document.createElement("iframe");a.appendChild(d);d.className="youtube-player";d.type="text/html";d.width="100%";d.height="100%";d.frameBorder="0";d.style.display="inline-block";if("YouTube"==b)d.className="youtube-player",d.src="http://www.youtube.com/embed/"+c+"?autoplay=1&html5=1";else if("Vimeo"==b)d.src="http://player.vimeo.com/video/"+c+"?title=0&amp;byline=0&amp;portrait=0&amp;color=da4541"}};BASIC_SRC="";
function initLinks(){var b="png"==COVER_SRC.split(".")[COVER_SRC.split(".").length-1]?"p":"j";if(-1!=document.domain.indexOf("publ.com")){for(var c=document.URL.split("/"),a="",d=0;d<c.length;d++){"bookdata"==a.toLowerCase()&&(PUBL=c[d]);if("seo"==c[d].toLowerCase()||"basic-html"==c[d].toLowerCase())PUBL=a;a=c[d]}BASIC_SRC=decodeURIComponent("http://"+document.domain+"/BookData/"+PUBL+"/basic-html");FULL_SRC=decodeURIComponent("http://"+document.domain+"/"+PUBL);SPREAD_SRC=decodeURIComponent("http://"+
document.domain+"/e/"+PUBL+"?p=1&a=o&f="+b+"&mode=spread&link="+FULL_SRC)}else{var c=window.location.pathname.split("/"),e=FULL_SRC.split("/"),a=window.location.hostname,a=-1!=c[1].indexOf(":")?c[1]:"http://"+a+"/"+c[1];if(1<c.length)for(d=2;d<c.length-e.length;d++)a=a+"/"+c[d];FULL_SRC=decodeURIComponent(a+"/"+e[e.length-1]);if(1<c.length)for(d=c.length-e.length;d<c.length-1;d++)a=a+"/"+c[d];BASIC_SRC=decodeURIComponent(a);SPREAD_SRC=decodeURIComponent(SPREAD_SRC+"?p=1&a=o&f="+b+"&link="+FULL_SRC)}}
bgCorrection=function(){if(!browser.supportHTML5){var b=document.getElementById("bodyBg"),c=b.currentStyle||window.getComputedStyle(b,null);if("none"!=c.backgroundImage){var a=document.createElement("img");a.src=c.backgroundImage.substr(5,c.backgroundImage.length-7);a.style.position="fixed";a.style.top="0px";a.style.left="0px";a.style.width="100%";a.style.height="100%";b.appendChild(a);b.style.backgroundImage="none"}}};
function init(){initLinks();workspace.getSize();workspace.addListener(workspace.getSize);browser.supportHTML5?window.addEventListener("resize",workspace.callFn,!1):window.location.pathname.indexOf("index.html")&&window.attachEvent("onresize",workspace.callFn);var b=document.getElementsByTagName("head")[0],c=document.createElement("script");c.id="formPrinter";c.type="text/javascript";c.src=LOCALS_FOLDER+"/"+LANG+"/textlang.js";b.appendChild(c)}
function bindReady(b){function c(){a||(a=!0,setTimeout(b,50))}var a=!1;if(document.addEventListener)document.addEventListener("DOMContentLoaded",function(){c()},!1);else if(document.attachEvent){if(document.documentElement.doScroll&&window==window.top){var d=function(){if(!a&&document.body)try{document.documentElement.doScroll("left"),c()}catch(b){setTimeout(d,0)}};d()}document.attachEvent("onreadystatechange",function(){"complete"===document.readyState&&c()})}window.addEventListener?window.addEventListener("load",
c,!1):window.attachEvent?window.attachEvent("onload",c):window.onload=c}"undefined"!==typeof LOADED?init():bindReady(init);setEvent=function(b,c,a){browser.supportHTML5?b.addEventListener(c,a,!1):b.attachEvent("on"+c,a,!1)};
