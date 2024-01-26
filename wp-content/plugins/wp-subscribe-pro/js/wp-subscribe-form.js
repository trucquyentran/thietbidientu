/*
Plugin Name: WP Subscribe Pro
Plugin URI: http://mythemeshop.com/plugins/wp-subscribe-pro/
Description: WP Subscribe is a simple but powerful subscription plugin which supports MailChimp, Aweber and Feedburner.
Author: MyThemeShop
Author URI: http://mythemeshop.com/
*/

jQuery(document).ready(function($) {

	// AJAX subscribe form
	// currently only for MailChimp & GetResponse
	$('.wp-subscribe-form').submit(function(e) {
		var $this = $(this),
			form_id = this.id;
		if ($this.hasClass('wp-subscribe-feedburner') || $this.hasClass('wp-subscribe-aweber')) {
			return true;
		}
		e.preventDefault();
		var $widget = $this.closest('.wp-subscribe').addClass('loading');

		// fix jquery sending placeholder value if field is empty
		$this.find('input[type=text]').val(function(index, value) {
			if (value == $(this).attr('placeholder'))
				return '';
			else
				return value;
		});

		var fields = $this.serialize();

		// Add "Anonymous" as name for GetResponse
		// because it is required
		var $name_field = $this.find('.name-field');
		if (!$name_field.length || $name_field.val() == '')
			fields += '&getresponse_name=Anonymous';

		fields += '&action=validate_subscribe';
		$.ajax({
			url: wp_subscribe.ajaxurl,
			type: 'POST',
			dataType: 'text',
			data: fields,
		})
		.done(function(data) {
			$widget.removeClass('loading');
			if (data == 'success') {
				$this.hide();
				$widget.find('.error').hide();
				$widget.find('.thanks').show();
			} else if (data == 'error') {
				$widget.find('.error').show(); // generic error
			} else {
				$widget.find('.error').show().html(data); // specific error
			}
		});
		
	});
});