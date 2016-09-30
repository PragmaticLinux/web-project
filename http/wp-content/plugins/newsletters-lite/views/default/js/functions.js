(function($) {
	$.fn.newsletters_subscribe_form = function() {
		var $form = this, 
			$submit = $form.find(':submit'),
			$fields = $form.find('.newsletters-fieldholder :input'),
			$filefields = $form.find(':file'),
			$errorfields = $form.find('.has-error'),
			$errors = $form.find('.newsletters-field-error'),
			$wrapper = $form.parent(), 
			$loading = $form.find('.newsletters_loading_wrapper'),
			$progress = $form.find('.newsletters-progress'),
			$progressbar = $form.find('.newsletters-progress .progress-bar'),
			$progresspercent = $form.find('.newsletters-progress .sr-only');
		
		if ($form.hasClass('newsletters-subscribe-form-ajax')) {
			$form.on('submit', function() {
				$loading.show();
				if ($filefields.length > 0) {
					$progress.show();
				}
				$errors.slideUp();
				$errorfields.removeClass('has-error');
				$submit.prop('disabled', true);
				$fields.attr('readonly', true);
			});
		}
		
		$fields.on('focus click', function() {
			$(this).removeClass('newsletters_fielderror').nextAll('div.newsletters-field-error').slideUp();	
		});
		
		if ($.isFunction($.fn.select2)) {
			$form.find('select').select2();
		}
		
		$('.newsletters-management .newsletters-fieldholder, .entry-content .newsletters-fieldholder, .post-entry .newsletters-fieldholder, .entry .newsletters-fieldholder').addClass('col-md-6');
		
		if ($form.hasClass('newsletters-subscribe-form-ajax')) {			
			if ($.isFunction($.fn.ajaxForm)) {
				$form.ajaxForm({
					url: newsletters_ajaxurl + 'action=wpmlsubscribe',
					data: (function() {	
						var formvalues = $form.serialize();							
						return formvalues;
					})(),
					type: "POST",
					cache: false,
					beforeSend: function() {
				        var percentVal = '0%';
				        $progressbar.width(percentVal)
				        $progresspercent.html(percentVal);
				    },
				    uploadProgress: function(event, position, total, percentComplete) {
				        var percentVal = percentComplete + '%';
				        $progressbar.width(percentVal)
				        $progresspercent.html(percentVal);
				    },
					success: function(response) {				
						$wrapper.html(response);
						$wrapper.find('.newsletters-subscribe-form').newsletters_subscribe_form();
						var targetOffset = ($wrapper.offset().top - 50);
					    $('html,body').animate({scrollTop: targetOffset}, 500);
					},
					complete: function(xhr) {
						var percentVal = '100%';
				        $progressbar.width(percentVal)
				        $progresspercent.html(percentVal);
					}
				});
			}
		}
		
		$form.trigger('newsletters_subscribe_form');
		return $form;
	}
	
	$(function() {
		$('.newsletters-subscribe-form').each( function() {
			$(this).newsletters_subscribe_form();
		});
	});
})(jQuery);