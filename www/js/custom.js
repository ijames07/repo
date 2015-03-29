function refresh(timestamp) {
	console.log("http://localhost/nette/www/bookings/free?id=" + timestamp);
	var jqxhr = $.ajax({ 
					url: "http://localhost/nette/www/bookings/free?id=" + timestamp,
					cache: false
				})
		.done(function(msg) {
			console.log(msg);
	  	});
}