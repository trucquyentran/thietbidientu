/*
Plugin Name: WP Subscribe Pro
Plugin URI: http://mythemeshop.com/plugins/wp-subscribe-pro/
Description: WP Subscribe is a simple but powerful subscription plugin which supports MailChimp, Aweber and Feedburner.
Author: MyThemeShop
Author URI: http://mythemeshop.com/
*/

( function( $ ){

	// color picker
	function initColorPicker( widget ) {
		widget.find( '.wp-subscribe-color-select' ).wpColorPicker({});
		// and services dropdown
		widget.find('.wp-subscribe-service-field select').change(function(event) {
	        var $this = $(this);
	        widget.find('.wp_subscribe_account_details_'+$this.val()).show().siblings('div').hide();
	        widget.find('.wp_subscribe_account_details').slideDown();
	        if ($this.val() == 'feedburner') {
	        	widget.find('.wp_subscribe_include_name, .wp-subscribe-name_placeholder-field').hide();
	        } else {
	        	widget.find('.wp_subscribe_include_name').show().find('input').trigger('change');
	        }
	    }).trigger('change');
	    widget.find('.wp_subscribe_include_name input').change(function() {
	    	if ($(this).is(':checked')) {
	    		$('.wp-subscribe-name_placeholder-field').show();
	    	} else {
	    		$('.wp-subscribe-name_placeholder-field').hide();
	    	}
	    }).trigger('change');
	}

	function onFormUpdate( event, widget ) {
		initColorPicker( widget );
	}
	
	$( document ).on( 'widget-added widget-updated', onFormUpdate );

	$( document ).ready( function() {
		$( '#widgets-right .widget:has(.wp-subscribe-service-field select)' ).each( function () {
			initColorPicker( $( this ) );
		});
	} );

	// slideToggle
	$(document).on('click', function(e) {
	    var $this = jQuery(e.target);
	    var $widget = $this.closest('.wp_subscribe_options_form');
	    if ($widget.length) {
	        if ($this.is('.wp-subscribe-toggle')) {
	            e.preventDefault();
	            var $related = $widget.find('.'+$this.attr('rel'));
	            $related.slideToggle();
	        }
	    }
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
}( jQuery ) );