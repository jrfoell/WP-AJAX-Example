jQuery(document).ready(function($) {

	$('#activate').click(function() {

		var data = {
			action : 'wordup-json-request',
			user_id : adminSettings.user_id
		};

		$.post(
			ajaxurl,
			data,   
			function(response) {
				var result = $.parseJSON( response );
				if ( result.active ) {
					$('#active-status').css('background-color', 'green').html('Active');
				} else {
					$('#active-status').css('background-color', 'red').html('Inactive');
				}
			}
		);
	});	

});