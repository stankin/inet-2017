(function($) {
	$(function() {

		var	$window = $(window),
			$body = $('body');

		$body.addClass('is-loading');

		$window.on('load', function() {
			$body.removeClass('is-loading');
		});

		$('.scrolly').scrolly();
	});

})(jQuery);