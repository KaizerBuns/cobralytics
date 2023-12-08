var cbStart = Date.now();
var cbReferer = document.referrer;

if(_cb_source == undefined) {
    var cbSource = window.location.hostname;
} else {
    var cbSource = _cb_source;
}

if(typeof _cb_host == 'undefined') { //legacy support
	var cbConfigURL = '//app.cobralytics.com/tracker/';
} else {
	var cbConfigURL = _cb_host;
}

cbConfigURL+= 'pixel.php';
(function(){  
	function createVisitorRequest () {  
	    var url = window.location.pathname;
	    var qs = window.location.search;
	    var page = url + qs;
	    var hits = 1;
	    var duration = Date.now() - cbStart;

	    var timeDate = new Date();
	    var request = 's='+cbSource+'&v='+hits+'&c=0&d=0&r='+encodeURIComponent(cbReferer)+'&res='+cbRes+'&t='+timeDate.getTime();
	    sendRequest(request);
	}

    function sendRequest(request) {
        var image = new Image(1, 1);
        image.onload = function () {
            iterator = 0;
        };
        image.src = cbConfigURL + '?' + request;
    }

    function isDefined(property) {
        var propertyType = typeof property;
        return propertyType !== 'undefined';
    }

    var w = screen.width || window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
    var h = screen.height || window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;
    var cbRes = w + 'x' + h;

    createVisitorRequest();

 }());