console.log('Hello, world!');

for (counter = 0; counter <= 1000; counter++) {
	var page = require('webpage').create();
	page.onConsoleMessage = function(msg) {
		console.log(msg);
	};

	page.open("http://cobracmd.dev/templates/aceadmin/index.html", function(status) {
			if ( status === "success" ) {
				page.includeJs("https://code.jquery.com/jquery-2.1.3.min.js", function() {
				var hit = page.evaluate(function() {
			      return 1;
			    });
			    console.log("Hit " + hit);
			});
		}
	});

	console.log("Success " + counter);

	
}

