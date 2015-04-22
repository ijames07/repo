var toast=function(msg){
	if (typeof window.plugins  != 'undefined'
			&& typeof window.plugins.toast != 'undefined' 
			&& typeof window.plugins.toast.showShortBottom == 'function'
			&& typeof device !== 'undefined') {
		// phonegap toast message
		setTimeout(function () {
			if (device.platform != 'windows') {
				window.plugins.toast.showShortBottom(msg);
			} else {
				showDialog(text);
			}
		}, 100);
	} else {
		// default web message
		var $toast = $('<div class="ui-loader ui-overlay-shadow ui-body-e ui-corner-all"><h3>' + msg + '</h3></div>');

		$toast.css({
			display: 'block', 
			background: '#fff',
			opacity: 0.90, 
			position: 'fixed',
			padding: '7px',
			'text-align': 'center',
			width: '270px',
			left: ($(window).width() - 284) / 2,
			top: $(window).height() / 2 - 20
		});

		var removeToast = function(){
			$(this).remove();
		};

		$toast.click(removeToast);

		$toast.appendTo('body').delay(2000);
		$toast.fadeOut(400, removeToast);
	}
}