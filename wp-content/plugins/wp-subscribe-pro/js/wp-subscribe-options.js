/*
Plugin Name: WP Subscribe Pro
Plugin URI: http://mythemeshop.com/plugins/wp-subscribe-pro/
Description: WP Subscribe is a simple but powerful subscription plugin which supports MailChimp, Aweber and Feedburner.
Author: MyThemeShop
Author URI: http://mythemeshop.com/
*/

( function( $ ){
	$( document ).ready( function() {
		var $preview_buttons = $('.wp-subscribe-preview-popup');
		$('.wp-subscribe-color-select').wpColorPicker({
			change: _.throttle(function(event, ui) {
				$(this).trigger( 'colorchange', [ui.color.toString()] );
			})
		});
		$('#wp_subscribe_enable_popup').change(function() {
			if ($(this).is(':checked')) {
				$('#wp-subscribe-popup-options').slideDown();
				$('.ifpopup').show();
			} else {
				$('#wp-subscribe-popup-options').slideUp();
				$('.ifpopup').hide();
			}
		});
		$('.popup_content_field').change(function(e) {
			var $this = $(this);
			if ($this.val() == 'subscribe_form') {
				$('#wp-subscribe-form-options').show();
				$('#wp-subscribe-popup-posts-options').hide();
				$('#wp-subscribe-custom-html-field').hide();
			} else if ($this.val() == 'posts') {
				$('#wp-subscribe-popup-posts-options').show();
				$('#wp-subscribe-custom-html-field').hide();
				$('#wp-subscribe-form-options').hide();
			} else if ($this.val() == 'custom_html') {
				$('#wp-subscribe-form-options').hide();
				$('#wp-subscribe-popup-posts-options').hide();
				$('#wp-subscribe-custom-html-field').show();
			}
			var $tab = $('#popup-content-tab');
			$tab.addClass('nav-tab-active');
			setTimeout(function() { $tab.removeClass('nav-tab-active'); }, 200);
		});
		$('#wp_subscribe_regenerate_cookie').click(function(e) {
			e.preventDefault();
			$('#cookies-cleared').fadeIn();
			$('#cookiehash').val(new Date().getTime());
		});
		$('.wps-nav-tab-wrapper a').click(function(e) {
			e.preventDefault();
			var $this = $(this);
			$this.addClass('nav-tab-active').siblings().removeClass('nav-tab-active');
			$($this.data('rel')).show().siblings().hide();
		});
		function initPopup(removal_delay) {
			$preview_buttons.magnificPopup({
			  type:'inline',
			  midClick: true,
			  removalDelay: removal_delay, //delay removal by X to allow out-animation
			  callbacks: {
			    beforeOpen: function() {
			       this.st.mainClass = 'animated '+this.st.el.attr('data-animatein');
			    },
			    beforeClose: function() {
			    	var $wrap = this.wrap,
			    		$bg = $wrap.prev(),
			    		$mfp = $wrap.add($bg);

			    	$mfp.removeClass(this.st.el.attr('data-animatein')).addClass(this.st.el.attr('data-animateout'));
			    },
			  },
			});
		}
		initPopup(wps_opts.popup_removal_delay);

		$('#popup_animation_in').on('change', function() {
			$preview_buttons.attr('data-animatein', $(this).val());
		});
		$('#popup_animation_out').on('change', function() {
			var val = $(this).val();
			$preview_buttons.attr('data-animateout', val);
			if (val == 'hinge') {
				initPopup(2000);
			} else if (val == '0') {
				initPopup(0);
			} else {
				initPopup(800);
			}
		});
		$('#wp_subscribe_overlay_opacity').on('change', function() {
	    	var value = parseFloat($(this).val());
	    	if (value < 0) {
	    		value = 0;
	    		$(this).val('0');
	    	} else if (value > 1) {
	    		value = 1;
	    		$(this).val('1');
	    	}
	    	$("#wp-subscribe-opacity-slider").slider("value", value);
			popup_change_overlay_opacity(value);
	    });
	    $("#wp-subscribe-opacity-slider").slider({
		    range: "min",
		    value: $('#wp_subscribe_overlay_opacity').val(),
		    step: 0.01,
		    min: 0,
		    max: 1,
		    slide: function(event, ui) {
		        $("#wp_subscribe_overlay_opacity").val(ui.value);
				popup_change_overlay_opacity(ui.value);
		    }
		});
		$('#wp_subscribe_popup_width').on('change', function() {
	    	var value = parseFloat($(this).val());
	    	if (value < 0) {
	    		value = 0;
	    		$(this).val('0');
	    	} else if (value > 1200) {
	    		value = 1200;
	    		$(this).val('1200');
	    	}
	    	$("#wp-subscribe-popup-width-slider").slider("value", value);
			popup_change_width(value);
	    });
	    $("#wp-subscribe-popup-width-slider").slider({
		    range: "min",
		    value: $('#wp_subscribe_popup_width').val(),
		    step: 10,
		    min: 200,
		    max: 1200,
		    slide: function(event, ui) {
		        $("#wp_subscribe_popup_width").val(ui.value);
				popup_change_width(ui.value);
		    }
		});
		function popup_change_overlay_color(color) {
			$('#overlay-style-color').html('.mfp-bg {background: '+color+';}');
		}
		function popup_change_overlay_opacity(opacity) {
		    $('#overlay-style-opacity').html('.mfp-bg.mfp-ready {opacity: '+opacity+';}');
		}
		function popup_change_width(width) {
		    $('#popup-style-width').html('#wp_subscribe_popup {width: '+width+'px;}');
		    
		    var breakpoints = [300, 600, 900],
			$popup = $('#wp_subscribe_popup');
			$.each(breakpoints, function(index, breakpoint) {
				 if (width < breakpoint) {
				 	$popup.addClass('lt_'+breakpoint);
				 } else {
				 	$popup.removeClass('lt_'+breakpoint);
				 }
			});
	
		}
		$('#wp_subscribe_options_colors_popup_overlay_color').on('colorchange', function(event, color) {
			popup_change_overlay_color(color);
		});
		$('.services_dropdown').change(function() {
			var $this = $(this);
			$this.parent().next().find('.wp_subscribe_account_details_'+$this.val()).show().siblings().hide();
			$namefield = $this.parent().siblings('.wp_subscribe_include_name_wrapper');
			if ($this.val() == 'feedburner') {
				$namefield.hide();
				if ($this.closest('#wp-subscribe-single-options').length)
					$('._single_post_form_labels_name_placeholder-wrapper').hide()
				else
					$('._popup_form_labels_name_placeholder-wrapper').hide()
					
			} else {
				$namefield.show().find('input').trigger('change');
			}
		}).trigger('change');
		$('.wp_subscribe_include_name_wrapper input').change(function() {
			var $this = $(this);
			if ($this.is(':checked')) {
				if ($this.closest('#wp-subscribe-single-options').length)
					$('._single_post_form_labels_name_placeholder-wrapper').show()
				else
					$('._popup_form_labels_name_placeholder-wrapper').show()
			} else {
				if ($this.closest('#wp-subscribe-single-options').length)
					$('._single_post_form_labels_name_placeholder-wrapper').hide()
				else
					$('._popup_form_labels_name_placeholder-wrapper').hide()
			}
		}).trigger('change');

		$('#wp_subscribe_enable_single_post_form').change(function() {
			if ($(this).is(':checked')) {
				$('#wp-subscribe-single-options').slideDown();
			} else {
				$('#wp-subscribe-single-options').slideUp();
			}
		});

		// update preview content via AJAX
		$('.popup_content_field, .wps-popup-content-options input, .wp-editor-area').on('change colorchange', function() {
			$preview_buttons.addClass('disabled');
			var fields = $('#wp_subscribe_options_form').serialize();
			fields += '&action=preview_popup';
			$.ajax({
				url: ajaxurl,
				type: 'POST',
				dataType: 'html',
				data: fields,
			})
			.done(function(response) {
				$('#wp_subscribe_popup').html(response);
			})
			.fail(function() {

			})
			.always(function() {
				$preview_buttons.removeClass('disabled');
			});
		});

		$('#copy_options_popup_to_single').click(function(e) {
			e.preventDefault();
			$('#wp-subscribe-single-options').find('input').each(function(index, el) {
				var $input = $(this);
				var $mapped = $('#'+this.id.replace('single_post', 'popup'));
				if ($mapped.length && $mapped.prop('id') != this.id) {
					$input.val($mapped.val()).trigger('change');
				}
			});
			var service = $('#popup_form_service').val();
			$('#single_post_form_service option').each(function() {
				var $this = $(this);
				if ($this.attr('value') == service)
					$this.prop('selected', true);
				else
					$this.prop('selected', false);
			}).trigger('change');
		});

		$(document).on('click', '.wps-load-palette', function(e) {
			var $this = $(this),
				$palette = $this.closest('.single-palette');

			$palette.find('input.wps-palette-color').each(function(i, el) {
				$('#'+$(el).attr('name')).iris('color', $(el).val());
			});

			e.preventDefault();
		});
		$(document).on('click', '.wps-toggle-palettes', function(e) {
			$(this).closest('.wps-colors-loader').find('.wps-palettes').slideToggle();
			e.preventDefault();
		});
	} );
}( jQuery ) );