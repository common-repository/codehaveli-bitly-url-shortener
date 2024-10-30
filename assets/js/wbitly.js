(function($, window, undefined) {

	$(".copy_bitly").mouseout(function() {
		$('.wbitly_tooltiptext').html("Click to Copy");
	});

	$('body').on('click', '.copy_bitly', function(event) {
		event.preventDefault();
		$url = $(this).find('.copy_bitly_link').html();
		var $temp = $("<input>");
		$("body").append($temp);
		$temp.val($url).select();
		document.execCommand("copy");
		$temp.remove();
		$(this).find('.wbitly_tooltiptext').html("Copied: " + $url);
	});

	$('body').on('click', '.generate_bitly', function(event) {
		event.preventDefault();
		$wbitly_generate_button = $(this);
		let wbitly_post_id = $(this).attr('data-post_id');
		if (!wbitly_post_id) {
			$('.generate_bitly').addClass('generate_bitly_disable');
		}
		$.ajax({
			url: wbitlyJS.ajaxurl,
			data: {
				'action': 'generate_wbitly_url_via_ajax',
				'post_id': wbitly_post_id
			},
			method: 'POST',
			//Post method
			beforeSend: function() {
				$('.generate_bitly').addClass('generate_bitly_disable');
			},
			success: function(response) {
				var data = JSON.parse(response);
				if (data.status) {
					$main_container = $wbitly_generate_button.parent().parent();
					$main_container.html('').html(data.bitly_link_html)
				}
			},
			error: function(error) {
				$('.generate_bitly').removeClass('generate_bitly_disable');
			},
			complete: function() {
				$('.generate_bitly').removeClass('generate_bitly_disable');
			}
		})
	});
	
}(jQuery, window));