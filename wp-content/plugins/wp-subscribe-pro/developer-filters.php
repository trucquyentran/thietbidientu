<?php
/*
* Filters & Action hooks available for theme developers.
* 
*/


/*
* wp_subscribe_form_defaults
* 
* Set new default values for the subscribe widget setup form.
*/
add_filter( 'wp_subscribe_form_defaults', 'wp_subscribe_form_new_defaults' );
function wp_subscribe_form_new_defaults($defaults) {
	$defaults['title'] = __('Subscribe now!', 'mythemeshop');
	return $defaults; 
}

/*
* wp_subscribe_form_color_palettes
* 
* Add/edit/remove color sets available for all subscribe forms (widget, popup, single post)
*/
add_filter( 'wp_subscribe_form_color_palettes', 'wp_subscribe_add_color_set' );
function wp_subscribe_add_color_set($palettes) {
	$palettes['unique_id'] = array('colors' => array(
		'background' => '#FFFFFF', 
		'title' => '#000000', 
		'text' => '#444444', 
		'field_text' => '#FFFFFF', 
		'field_background' => '#1e73be', 
		'button_text' => '#FFFFFF', 
		'button_background' => '#16448e', 
		'footer_text' => '#16448e'
	));
	return $palettes;
}