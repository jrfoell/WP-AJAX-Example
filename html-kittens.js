jQuery(document).ready(function($) {

	$('#submit').click(function() {

		var data = {
			action : 'wordup-html-request'
		};

		$.post(
			siteSettings.ajaxurl,
			data,   
			function(response) {
				$('#cat-house').html(response);			
			}
		);
	});	

});