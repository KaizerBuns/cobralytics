//version3
var cbStart=Date.now(),cbReferer=document.referrer;if(void 0==_cb_source)var cbSource=window.location.hostname;else var cbSource=_cb_source;if("undefined"==typeof _cb_host)var cbConfigURL="//app.cobralytics.com/tracker/";else var cbConfigURL=_cb_host;cbConfigURL+="pixel.php",function(){function e(){var e=(window.location.pathname,window.location.search,1),c=(Date.now()-cbStart,new Date),o="s="+cbSource+"&v="+e+"&c=0&d=0&r="+encodeURIComponent(cbReferer)+"&res="+t+"&t="+c.getTime();n(o)}function n(e){var n=new Image(1,1);n.onload=function(){iterator=0},n.src=cbConfigURL+"?"+e}var c=screen.width||window.innerWidth||document.documentElement.clientWidth||document.body.clientWidth,o=screen.height||window.innerHeight||document.documentElement.clientHeight||document.body.clientHeight,t=c+"x"+o;e()}();