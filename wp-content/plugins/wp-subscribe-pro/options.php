<?php

// set default options
add_action('init', 'wp_subscribe_init');
function wp_subscribe_init() {
	$options = get_option('wp_subscribe_options');
    if (empty($options)) {
    	update_option( 'wp_subscribe_options', wp_subscribe_default_options() );
    }
}

function wp_subscribe_default_options() {
    $default_options = array(
    	'enable_popup' => 0,
    	'popup_content' => 'subscribe_form',
    	'popup_form_options' => array(
    		'service' => 'feedburner',
    		'include_name_field' => false,
            'feedburner_id' => '',
            'mailchimp_api_key' => '',
            'mailchimp_list_id' => '',
            'mailchimp_double_optin' => '',
            'getresponse_api_key' => '',
            'getresponse_list_id' => '',
            'aweber_list_id' => '',
        ),
        'popup_form_labels' => array(
            'title' => __('Get more stuff like this<br/> <span>in your inbox</span>', 'wp-subscribe'),
            'text' => __('Subscribe to our mailing list and get interesting stuff and updates to your email inbox.', 'wp-subscribe'),
            'name_placeholder' => __('Enter your name here', 'wp-subscribe'),
            'email_placeholder' => __('Enter your email here', 'wp-subscribe'),
            'button_text' => __('Sign Up Now', 'wp-subscribe'),
            'success_message' => __('Thank you for subscribing.', 'wp-subscribe'),
            'error_message' => __('Something went wrong.', 'wp-subscribe'),
            'footer_text' => __('We respect your privacy and take protecting it seriously', 'wp-subscribe'),
        ),
		'popup_form_colors' => array(
			'background_color' => '#f47555',
            'title_color' => '#FFFFFF',
            'text_color' => '#FFFFFF',
            'field_text_color' => '#FFFFFF',
            'field_background_color' => '#d56144',
            'button_text_color' => '#f47555',
            'button_background_color' => '#FFFFFF',
            'footer_text_color' => '#FFFFFF'
        ),
		'popup_custom_html' => '<div class="popup-content">'.__('Some text with padding.', 'wp-subscribe').'</div>',
		'popup_animation_in' => 'fadeIn',
		'popup_animation_out' => 'fadeOut',
		'popup_triggers' => array(
			'on_enter' => 0,
			'on_timeout' => '1',
			'timeout' => 15,
			'on_reach_bottom' => '1',
			'on_exit_intent' => '1'
		),
		'popup_show_on' => array(
			'front_page' => '1',
			'single' => '1',
			'archive' => '1',
			'search' => '1',
			'404_page' => '1'
		),
		'popup_width' => '600',
		'popup_overlay_color' => '#0b0b0b',
		'popup_overlay_opacity' => '0.7',
		'cookie_expiration' => 14,
		'cookie_hash' => time(),
		'enable_single_post_form' => 0,
		'popup_posts_labels' => array(
			'title' => __('Before you go', 'wp-subscribe'),
			'text' => __('You may also be interested in these posts:', 'wp-subscribe')
		),
		'popup_posts_colors' => array(
			'background_color' => '#f47555',
			'title_color' => '#ffffff',
			'text_color' => '#ffffff',
			'line_color' => '#ffffff',
		),
		'popup_posts_meta' => array(
			'category' => false,
			'excerpt' => true,
		),
		'single_post_form_location' => 'bottom',
		'single_post_form_options' => array(
    		'service' => 'feedburner',
    		'include_name_field' => false,
            'feedburner_id' => '',
            'mailchimp_api_key' => '',
            'mailchimp_list_id' => '',
            'getresponse_api_key' => '',
            'getresponse_list_id' => '',
            'aweber_list_id' => '',
        ),
        'single_post_form_labels' => array(
            'title' => __('Get more stuff like this', 'wp-subscribe'),
            'text' => __('Subscribe to our mailing list and get interesting stuff and updates to your email inbox.', 'wp-subscribe'),
            'name_placeholder' => __('Enter your name here', 'wp-subscribe'),
            'email_placeholder' => __('Enter your email here', 'wp-subscribe'),
            'button_text' => __('Sign Up Now', 'wp-subscribe'),
            'success_message' => __('Thank you for subscribing.', 'wp-subscribe'),
            'error_message' => __('Something went wrong.', 'wp-subscribe'),
            'footer_text' => __('We respect your privacy and take protecting it seriously', 'wp-subscribe'),
        ),
		'single_post_form_colors' => array(
			'background_color' => '#f47555',
            'title_color' => '#FFFFFF',
            'text_color' => '#FFFFFF',
            'field_text_color' => '#FFFFFF',
            'field_background_color' => '#d56144',
            'button_text_color' => '#f47555',
            'button_background_color' => '#FFFFFF',
            'footer_text_color' => '#FFFFFF'
        ),
	);
    // set defaults
    return $default_options;
}

// create custom plugin settings menu
add_action('admin_menu', 'wp_subscribe_create_menu');

function wp_subscribe_create_menu() {

	$hook = add_options_page('WP Subscribe', 'WP Subscribe Pro', 'administrator', __FILE__, 'wp_subscribe_settings_page');

	//call register settings function
	add_action( 'admin_init', 'wp_subscribe_register_settings' );

	// body class
	// create_function() requires PHP 5.2+
	add_action( "load-$hook", create_function('', 'add_filter( "admin_body_class", "wp_subscribe_admin_body_class" );') );
	add_action( "load-$hook", 'wp_subscribe_options_scripts' );
}
// body class
function wp_subscribe_admin_body_class( $classes ) {
	$classes .= 'wp-subscribe-admin-options';
	return $classes;
}
// enqueue css & js
function wp_subscribe_options_scripts() {
    $options = wp_parse_args( get_option( 'wp_subscribe_options' ), wp_subscribe_default_options() );
	wp_enqueue_style( 'wp-subscribe-options', plugins_url('css/wp-subscribe-options.css', __FILE__) );
	wp_subscribe_enqueue_popup_css();
	wp_subscribe_enqueue_popup_js();
	wp_enqueue_style('jquery-ui-smoothness',
                '//ajax.googleapis.com/ajax/libs/jqueryui/1.8.21/themes/smoothness/jquery-ui.css',
                false,
                null,
                false);
	/* jQuery UI Slider */
	wp_enqueue_script( 'jquery-ui-core' );
	wp_enqueue_script( 'jquery-ui-slider' );
	
	/* WP Color Picker / Iris */
	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_script( 'wp-color-picker' );

	wp_register_script( 'wp-subscribe-options', plugins_url('js/wp-subscribe-options.js', __FILE__), array('jquery', 'underscore') );
	$popup_removal_delay = 0;
	if ($options['popup_animation_out'] == '0') {
		$popup_removal_delay = 0;
	} else if ($options['popup_animation_out'] == 'hinge') {
		$popup_removal_delay = 2000;
	} else {
		$popup_removal_delay = 800;
	}
	wp_localize_script( 'wp-subscribe-options', 'wps_opts', array(
			'popup_removal_delay' => $popup_removal_delay
	));
	wp_enqueue_script( 'wp-subscribe-options');
}
function wp_subscribe_register_settings() {
	//register our settings
	register_setting( 'wp_subscribe-settings-group', 'wp_subscribe_options' );
}

function wp_subscribe_settings_page() {
    $options = wp_parse_args( get_option( 'wp_subscribe_options' ), wp_subscribe_default_options() );

    $popup_removal_delay = 0;
	if ($options['popup_animation_out'] == '0') {
		$popup_removal_delay = 0;
	} else if ($options['popup_animation_out'] == 'hinge') {
		$popup_removal_delay = 2000;
	} else {
		$popup_removal_delay = 800;
	}
?>
<div class="wrap wp-subscribe">
	<h2><?php _e('WP Subscribe Pro Settings', 'wp-subscribe'); ?></h2>

	<form method="post" action="options.php" id="wp_subscribe_options_form">
	    <?php settings_fields( 'wp_subscribe-settings-group' ); ?>
		
	    <h2 class="nav-tab-wrapper wps-nav-tab-wrapper">
	    	<a href="#" class="nav-tab nav-tab-active" data-rel=".wps-popup-options"><?php _e( 'Popup', 'wp-subscribe' ); ?></a> 
	    	<a href="#" class="nav-tab ifpopup" id="popup-content-tab" data-rel=".wps-popup-content-options"<?php echo !$options['enable_popup'] ? ' style="display: none;"' : ''; ?>><?php _e( 'Popup Content', 'wp-subscribe' ); ?></a> 
	    	<a href="#" class="nav-tab ifpopup" data-rel=".wps-popup-trigger-options"<?php echo !$options['enable_popup'] ? ' style="display: none;"' : ''; ?>><?php _e( 'Popup Triggers', 'wp-subscribe' ); ?></a> 
	    	<a href="#" class="nav-tab" data-rel=".wps-post-options"><?php _e( 'Single Posts', 'wp-subscribe' ); ?></a></h2>
	    <div class="wps-tabs-wrapper">
	    	
	    	<!-- Popup Tab -->

		    <div class="wps-popup-options">
				<h3 class="wp-subscribe-field enable-popup">
					<label for="wp_subscribe_enable_popup">
						<input type="hidden" name="wp_subscribe_options[enable_popup]" value="0">
						<input id="wp_subscribe_enable_popup" type="checkbox" name="wp_subscribe_options[enable_popup]" value="1" <?php checked($options['enable_popup']); ?>>
						<?php _e( 'Enable Popup', 'wp-subscribe' ); ?>
					</label>
				</h3>
				<p><?php _e( 'Enable site-wide popup that shows subscribe form, related posts, or custom HTML.', 'wp-subscribe' ); ?></p>
				<div id="wp-subscribe-popup-options"<?php echo !$options['enable_popup'] ? ' style="display: none;"' : ''; ?>>
					<div class="wp-subscribe-field">
						<label for="wp_subscribe_show_form">
							<input class="popup_content_field" type="radio" name="wp_subscribe_options[popup_content]" value="subscribe_form" id="wp_subscribe_show_form" <?php checked($options['popup_content'], 'subscribe_form'); ?>>
							<?php _e( 'Show subscribe form in popup', 'wp-subscribe' ); ?>
						</label><br />
						<label for="wp_subscribe_show_posts">
							<input class="popup_content_field" type="radio" name="wp_subscribe_options[popup_content]" value="posts" id="wp_subscribe_show_posts" <?php checked($options['popup_content'], 'posts'); ?>>
							<?php _e( 'Show related posts in popup', 'wp-subscribe' ); ?>
						</label><br />
						<label for="wp_subscribe_show_custom">
							<input class="popup_content_field" type="radio" name="wp_subscribe_options[popup_content]" value="custom_html" id="wp_subscribe_show_custom" <?php checked($options['popup_content'], 'custom_html'); ?>>
							<?php _e( 'Show custom HTML or shortcode in popup', 'wp-subscribe' ); ?>
						</label>
					</div>

					<div class="wp-subscribe-field">
						<label for="wp_subscribe_popup_width"><?php _e( 'Popup width', 'wp-subscribe' ); ?></label>
						<div id="wp-subscribe-popup-width-slider"></div>
						<input type="number" min="200" max="1200" step="10" name="wp_subscribe_options[popup_width]" id="wp_subscribe_popup_width" value="<?php echo $options['popup_width']; ?>" /><span class="width-px-label">px</span>
					</div>
					
					<div class="wp-subscribe-field">
						<h4><?php _e( 'Popup Animation', 'wp-subscribe' ); ?></h4>
						<p>
							<select name="wp_subscribe_options[popup_animation_in]" id="popup_animation_in">
								<option value="0"><?php _e( 'No Animation', 'wp-review' ) ?></option>
								<optgroup label="<?php _e('Attention Seekers', 'wp-subscribe'); ?>">
						          <option value="bounce" <?php selected($options['popup_animation_in'], 'bounce'); ?>><?php _e('bounce', 'wp-subscribe'); ?></option>>
						          <option value="flash" <?php selected($options['popup_animation_in'], 'flash'); ?>><?php _e('flash', 'wp-subscribe'); ?></option>>
						          <option value="pulse" <?php selected($options['popup_animation_in'], 'pulse'); ?>><?php _e('pulse', 'wp-subscribe'); ?></option>>
						          <option value="rubberBand" <?php selected($options['popup_animation_in'], 'rubberBand'); ?>><?php _e('rubberBand', 'wp-subscribe'); ?></option>>
						          <option value="shake" <?php selected($options['popup_animation_in'], 'shake'); ?>><?php _e('shake', 'wp-subscribe'); ?></option>>
						          <option value="swing" <?php selected($options['popup_animation_in'], 'swing'); ?>><?php _e('swing', 'wp-subscribe'); ?></option>>
						          <option value="tada" <?php selected($options['popup_animation_in'], 'tada'); ?>><?php _e('tada', 'wp-subscribe'); ?></option>>
						          <option value="wobble" <?php selected($options['popup_animation_in'], 'wobble'); ?>><?php _e('wobble', 'wp-subscribe'); ?></option>>
						        </optgroup>

						        <optgroup label="<?php _e('Bouncing Entrances', 'wp-subscribe'); ?>">
						          <option value="bounceIn" <?php selected($options['popup_animation_in'], 'bounceIn'); ?>><?php _e('bounceIn', 'wp-subscribe'); ?></option>>
						          <option value="bounceInDown" <?php selected($options['popup_animation_in'], 'bounceInDown'); ?>><?php _e('bounceInDown', 'wp-subscribe'); ?></option>>
						          <option value="bounceInLeft" <?php selected($options['popup_animation_in'], 'bounceInLeft'); ?>><?php _e('bounceInLeft', 'wp-subscribe'); ?></option>>
						          <option value="bounceInRight" <?php selected($options['popup_animation_in'], 'bounceInRight'); ?>><?php _e('bounceInRight', 'wp-subscribe'); ?></option>>
						          <option value="bounceInUp" <?php selected($options['popup_animation_in'], 'bounceInUp'); ?>><?php _e('bounceInUp', 'wp-subscribe'); ?></option>>
						        </optgroup>

						        <optgroup label="<?php _e('Fading Entrances', 'wp-subscribe'); ?>">
						          <option value="fadeIn" <?php selected($options['popup_animation_in'], 'fadeIn'); ?>><?php _e('fadeIn', 'wp-subscribe'); ?></option>>
						          <option value="fadeInDown" <?php selected($options['popup_animation_in'], 'fadeInDown'); ?>><?php _e('fadeInDown', 'wp-subscribe'); ?></option>>
						          <option value="fadeInDownBig" <?php selected($options['popup_animation_in'], 'fadeInDownBig'); ?>><?php _e('fadeInDownBig', 'wp-subscribe'); ?></option>>
						          <option value="fadeInLeft" <?php selected($options['popup_animation_in'], 'fadeInLeft'); ?>><?php _e('fadeInLeft', 'wp-subscribe'); ?></option>>
						          <option value="fadeInLeftBig" <?php selected($options['popup_animation_in'], 'fadeInLeftBig'); ?>><?php _e('fadeInLeftBig', 'wp-subscribe'); ?></option>>
						          <option value="fadeInRight" <?php selected($options['popup_animation_in'], 'fadeInRight'); ?>><?php _e('fadeInRight', 'wp-subscribe'); ?></option>>
						          <option value="fadeInRightBig" <?php selected($options['popup_animation_in'], 'fadeInRightBig'); ?>><?php _e('fadeInRightBig', 'wp-subscribe'); ?></option>>
						          <option value="fadeInUp" <?php selected($options['popup_animation_in'], 'fadeInUp'); ?>><?php _e('fadeInUp', 'wp-subscribe'); ?></option>>
						          <option value="fadeInUpBig" <?php selected($options['popup_animation_in'], 'fadeInUpBig'); ?>><?php _e('fadeInUpBig', 'wp-subscribe'); ?></option>>
						        </optgroup>

						        <optgroup label="<?php _e('Flippers', 'wp-subscribe'); ?>">
						          <option value="flipInX" <?php selected($options['popup_animation_in'], 'flipInX'); ?>><?php _e('flipInX', 'wp-subscribe'); ?></option>>
						          <option value="flipInY" <?php selected($options['popup_animation_in'], 'flipInY'); ?>><?php _e('flipInY', 'wp-subscribe'); ?></option>>
						        </optgroup>

						        <optgroup label="<?php _e('Lightspeed', 'wp-subscribe'); ?>">
						          <option value="lightSpeedIn" <?php selected($options['popup_animation_in'], 'lightSpeedIn'); ?>><?php _e('lightSpeedIn', 'wp-subscribe'); ?></option>>
						        </optgroup>

						        <optgroup label="<?php _e('Rotating Entrances', 'wp-subscribe'); ?>">
						          <option value="rotateIn" <?php selected($options['popup_animation_in'], 'rotateIn'); ?>><?php _e('rotateIn', 'wp-subscribe'); ?></option>>
						          <option value="rotateInDownLeft" <?php selected($options['popup_animation_in'], 'rotateInDownLeft'); ?>><?php _e('rotateInDownLeft', 'wp-subscribe'); ?></option>>
						          <option value="rotateInDownRight" <?php selected($options['popup_animation_in'], 'rotateInDownRight'); ?>><?php _e('rotateInDownRight', 'wp-subscribe'); ?></option>>
						          <option value="rotateInUpLeft" <?php selected($options['popup_animation_in'], 'rotateInUpLeft'); ?>><?php _e('rotateInUpLeft', 'wp-subscribe'); ?></option>>
						          <option value="rotateInUpRight" <?php selected($options['popup_animation_in'], 'rotateInUpRight'); ?>><?php _e('rotateInUpRight', 'wp-subscribe'); ?></option>>
						        </optgroup>

						        <optgroup label="<?php _e('Specials', 'wp-subscribe'); ?>">
						          <option value="rollIn" <?php selected($options['popup_animation_in'], 'rollIn'); ?>><?php _e('rollIn', 'wp-subscribe'); ?></option>>
						        </optgroup>

						        <optgroup label="<?php _e('Zoom Entrances', 'wp-subscribe'); ?>">
						          <option value="zoomIn" <?php selected($options['popup_animation_in'], 'zoomIn'); ?>><?php _e('zoomIn', 'wp-subscribe'); ?></option>>
						          <option value="zoomInDown" <?php selected($options['popup_animation_in'], 'zoomInDown'); ?>><?php _e('zoomInDown', 'wp-subscribe'); ?></option>>
						          <option value="zoomInLeft" <?php selected($options['popup_animation_in'], 'zoomInLeft'); ?>><?php _e('zoomInLeft', 'wp-subscribe'); ?></option>>
						          <option value="zoomInRight" <?php selected($options['popup_animation_in'], 'zoomInRight'); ?>><?php _e('zoomInRight', 'wp-subscribe'); ?></option>>
						          <option value="zoomInUp" <?php selected($options['popup_animation_in'], 'zoomInUp'); ?>><?php _e('zoomInUp', 'wp-subscribe'); ?></option>>
						        </optgroup>
							</select>


							<select name="wp_subscribe_options[popup_animation_out]" id="popup_animation_out">
								<option value="0"><?php _e( 'No Animation', 'wp-review' ) ?></option>

						        <optgroup label="<?php _e('Bouncing Exits', 'wp-subscribe'); ?>">
						          <option value="bounceOut" <?php selected($options['popup_animation_out'], 'bounceOut'); ?>><?php _e('bounceOut', 'wp-subscribe'); ?></option>>
						          <option value="bounceOutDown" <?php selected($options['popup_animation_out'], 'bounceOutDown'); ?>><?php _e('bounceOutDown', 'wp-subscribe'); ?></option>>
						          <option value="bounceOutLeft" <?php selected($options['popup_animation_out'], 'bounceOutLeft'); ?>><?php _e('bounceOutLeft', 'wp-subscribe'); ?></option>>
						          <option value="bounceOutRight" <?php selected($options['popup_animation_out'], 'bounceOutRight'); ?>><?php _e('bounceOutRight', 'wp-subscribe'); ?></option>>
						          <option value="bounceOutUp" <?php selected($options['popup_animation_out'], 'bounceOutUp'); ?>><?php _e('bounceOutUp', 'wp-subscribe'); ?></option>>
						        </optgroup>

						        <optgroup label="<?php _e('Fading Exits', 'wp-subscribe'); ?>">
						          <option value="fadeOut" <?php selected($options['popup_animation_out'], 'fadeOut'); ?>><?php _e('fadeOut', 'wp-subscribe'); ?></option>>
						          <option value="fadeOutDown" <?php selected($options['popup_animation_out'], 'fadeOutDown'); ?>><?php _e('fadeOutDown', 'wp-subscribe'); ?></option>>
						          <option value="fadeOutDownBig" <?php selected($options['popup_animation_out'], 'fadeOutDownBig'); ?>><?php _e('fadeOutDownBig', 'wp-subscribe'); ?></option>>
						          <option value="fadeOutLeft" <?php selected($options['popup_animation_out'], 'fadeOutLeft'); ?>><?php _e('fadeOutLeft', 'wp-subscribe'); ?></option>>
						          <option value="fadeOutLeftBig" <?php selected($options['popup_animation_out'], 'fadeOutLeftBig'); ?>><?php _e('fadeOutLeftBig', 'wp-subscribe'); ?></option>>
						          <option value="fadeOutRight" <?php selected($options['popup_animation_out'], 'fadeOutRight'); ?>><?php _e('fadeOutRight', 'wp-subscribe'); ?></option>>
						          <option value="fadeOutRightBig" <?php selected($options['popup_animation_out'], 'fadeOutRightBig'); ?>><?php _e('fadeOutRightBig', 'wp-subscribe'); ?></option>>
						          <option value="fadeOutUp" <?php selected($options['popup_animation_out'], 'fadeOutUp'); ?>><?php _e('fadeOutUp', 'wp-subscribe'); ?></option>>
						          <option value="fadeOutUpBig" <?php selected($options['popup_animation_out'], 'fadeOutUpBig'); ?>><?php _e('fadeOutUpBig', 'wp-subscribe'); ?></option>>
						        </optgroup>
						        <optgroup label="<?php _e('Flippers', 'wp-subscribe'); ?>">
						          <option value="flipOutX" <?php selected($options['popup_animation_out'], 'flipOutX'); ?>><?php _e('flipOutX', 'wp-subscribe'); ?></option>>
						          <option value="flipOutY" <?php selected($options['popup_animation_out'], 'flipOutY'); ?>><?php _e('flipOutY', 'wp-subscribe'); ?></option>>
						        </optgroup>
						        <optgroup label="<?php _e('Lightspeed', 'wp-subscribe'); ?>">
						          <option value="lightSpeedOut" <?php selected($options['popup_animation_out'], 'lightSpeedOut'); ?>><?php _e('lightSpeedOut', 'wp-subscribe'); ?></option>>
						        </optgroup>
						        <optgroup label="<?php _e('Rotating Exits', 'wp-subscribe'); ?>">
						          <option value="rotateOut" <?php selected($options['popup_animation_out'], 'rotateOut'); ?>><?php _e('rotateOut', 'wp-subscribe'); ?></option>>
						          <option value="rotateOutDownLeft" <?php selected($options['popup_animation_out'], 'rotateOutDownLeft'); ?>><?php _e('rotateOutDownLeft', 'wp-subscribe'); ?></option>>
						          <option value="rotateOutDownRight" <?php selected($options['popup_animation_out'], 'rotateOutDownRight'); ?>><?php _e('rotateOutDownRight', 'wp-subscribe'); ?></option>>
						          <option value="rotateOutUpLeft" <?php selected($options['popup_animation_out'], 'rotateOutUpLeft'); ?>><?php _e('rotateOutUpLeft', 'wp-subscribe'); ?></option>>
						          <option value="rotateOutUpRight" <?php selected($options['popup_animation_out'], 'rotateOutUpRight'); ?>><?php _e('rotateOutUpRight', 'wp-subscribe'); ?></option>>
						        </optgroup>
						        <optgroup label="<?php _e('Specials', 'wp-subscribe'); ?>">
						          <option value="hinge" <?php selected($options['popup_animation_out'], 'hinge'); ?>><?php _e('hinge', 'wp-subscribe'); ?></option>>
						          <option value="rollOut" <?php selected($options['popup_animation_out'], 'rollOut'); ?>><?php _e('rollOut', 'wp-subscribe'); ?></option>>
						        </optgroup>
						        <optgroup label="<?php _e('Zoom Exits', 'wp-subscribe'); ?>">
						          <option value="zoomOut" <?php selected($options['popup_animation_out'], 'zoomOut'); ?>><?php _e('zoomOut', 'wp-subscribe'); ?></option>>
						          <option value="zoomOutDown" <?php selected($options['popup_animation_out'], 'zoomOutDown'); ?>><?php _e('zoomOutDown', 'wp-subscribe'); ?></option>>
						          <option value="zoomOutLeft" <?php selected($options['popup_animation_out'], 'zoomOutLeft'); ?>><?php _e('zoomOutLeft', 'wp-subscribe'); ?></option>>
						          <option value="zoomOutRight" <?php selected($options['popup_animation_out'], 'zoomOutRight'); ?>><?php _e('zoomOutRight', 'wp-subscribe'); ?></option>>
						          <option value="zoomOutUp" <?php selected($options['popup_animation_out'], 'zoomOutUp'); ?>><?php _e('zoomOutUp', 'wp-subscribe'); ?></option>>
						        </optgroup>
							</select>
						</p>
					</div>

					<div class="wp-subscribe-field">
						<?php wp_subscribe_options_color_field('popup_overlay_color', __('Popup overlay color', 'wp-subscribe'), ''); ?>
					</div>
					<div class="wp-subscribe-field">
						<label for="wp_subscribe_overlay_opacity"><?php _e( 'Popup overlay opacity', 'wp-subscribe' ); ?></label>
						<div id="wp-subscribe-opacity-slider"></div>
						<input type="number" min="0" max="1" step="0.01" name="wp_subscribe_options[popup_overlay_opacity]" id="wp_subscribe_overlay_opacity" value="<?php echo $options['popup_overlay_opacity']; ?>" />
					</div>
				</div>
				<p class="submit">
			    <a href="#wp_subscribe_popup" class="button-secondary wp-subscribe-preview-popup ifpopup" data-animatein="<?php echo $options['popup_animation_in']; ?>" data-animateout="<?php echo $options['popup_animation_out']; ?>"<?php echo !$options['enable_popup'] ? ' style="display: none;"' : ''; ?>><?php _e('Preview Popup') ?></a>
			    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			    </p>
			</div>
	    	
	    	<!-- Popup Content Tab -->

	    	<div class="wps-popup-content-options" style="display: none;">

	    		<div class="wp-subscribe-field" id="wp-subscribe-form-options"<?php echo $options['popup_content'] != 'subscribe_form' ? ' style="display: none;"' : ''; ?>>
	    			<div class="wp-subscribe-field">
						<select name="wp_subscribe_options[popup_form_options][service]" id="popup_form_service" class="services_dropdown">
							<option value="feedburner" <?php selected( $options['popup_form_options']['service'], 'feedburner' ); ?>>FeedBurner</option>
							<option value="mailchimp" <?php selected( $options['popup_form_options']['service'], 'mailchimp' ); ?>>Mailchimp</option>
							<option value="getresponse" <?php selected( $options['popup_form_options']['service'], 'getresponse' ); ?>>GetResponse</option>
							<option value="aweber" <?php selected( $options['popup_form_options']['service'], 'aweber' ); ?>>AWeber</option>
						</select>
					</div>
					<div class="wp_subscribe_account_details">
       					<div class="wp_subscribe_account_details_feedburner" style="display: none;">
							<?php wp_subscribe_options_text_field('feedburner_id', __('Feedburner ID', 'wp-subscribe'), 'popup_form_options'); ?>
						</div><!-- .wp_subscribe_account_details_feedburner -->

			        	<div class="wp_subscribe_account_details_mailchimp" style="display: none;">
		        			<?php wp_subscribe_options_text_field('mailchimp_api_key', __('MailChimp API Key', 'wp-subscribe'), 'popup_form_options'); ?>
							<?php wp_subscribe_options_text_field('mailchimp_list_id', __('MailChimp List ID', 'wp-subscribe'), 'popup_form_options'); ?>
							<p class="clear"><a href="http://kb.mailchimp.com/accounts/management/about-api-keys#Finding-or-generating-your-API-key" target="_blank"><?php _e('Find your API key', 'wp-subscribe'); ?></a> | 
	            			<a href="http://kb.mailchimp.com/lists/managing-subscribers/find-your-list-id" target="_blank"><?php _e('Find your list ID', 'wp-subscribe'); ?></a></p>
							<p class="wp_subscribe_mailchimp_double_optin"><label for="popup_form_mailchimp_double_optin">
				                <input type="hidden" name="wp_subscribe_options[popup_form_options][mailchimp_double_optin]" value="0">
				                <input id="popup_form_mailchimp_double_optin" type="checkbox" name="wp_subscribe_options[popup_form_options][mailchimp_double_optin]" value="1" <?php checked($options['popup_form_options']['mailchimp_double_optin']); ?>>
				                <?php _e( 'Send double opt-in notification', 'wp-subscribe' ); ?>
				            </label></p>
						</div><!-- .wp_subscribe_account_details_mailchimp -->

        				<div class="wp_subscribe_account_details_aweber" style="display: none;">
							<?php wp_subscribe_options_text_field('aweber_list_id', __('AWeber List ID', 'wp-subscribe'), 'popup_form_options'); ?>
	           				<p class="clear"><a href="https://help.aweber.com/entries/61177326-What-Is-The-Unique-List-ID-" target="_blank"><?php _e('Find your list ID', 'wp-subscribe'); ?></a></p>
						</div><!-- .wp_subscribe_account_details_aweber -->

						<div class="wp_subscribe_account_details_getresponse" style="display: none;">
		        			<?php wp_subscribe_options_text_field('getresponse_api_key', __('GetResponse API Key', 'wp-subscribe'), 'popup_form_options'); ?>
							<?php wp_subscribe_options_text_field('getresponse_list_id', __('GetResponse Campaign Name', 'wp-subscribe'), 'popup_form_options'); ?>
							<p class="clear"><a href="http://support.getresponse.com/faq/where-i-find-api-key" target="_blank"><?php _e('Find your API key', 'wp-subscribe'); ?></a> | 
	            			<a href="http://support.getresponse.com/faq/can-i-change-the-name-of-a-campaign" target="_blank"><?php _e('Find the campaign name', 'wp-subscribe'); ?></a></p>
						</div><!-- .wp_subscribe_account_details_getresponse -->
    			    </div><!-- .wp_subscribe_account_details -->
	    			
	    			<div class="wp-subscribe-field wp_subscribe_include_name_wrapper" <?php echo $options['popup_form_options']['service'] == 'feedburner' ? ' style="display: none;"' : ''; ?>>
	    			    <label for="wp_subscribe_popup_form_include_name">
							<input type="hidden" name="wp_subscribe_options[popup_form_options][include_name_field]" value="0">
							<input id="wp_subscribe_popup_form_include_name" type="checkbox" name="wp_subscribe_options[popup_form_options][include_name_field]" value="1" <?php checked($options['popup_form_options']['include_name_field']); ?>>
							<?php _e( 'Include <strong>Name</strong> field', 'wp-subscribe' ); ?>
						</label>
					</div>

					<?php wp_subscribe_options_text_field('title', __('Title', 'wp-subscribe')); ?>
					<?php wp_subscribe_options_text_field('text', __('Text', 'wp-subscribe')); ?>
					<?php wp_subscribe_options_text_field('name_placeholder', __('Name Placeholder Text', 'wp-subscribe')); ?>
					<?php wp_subscribe_options_text_field('email_placeholder', __('Email Placeholder Text', 'wp-subscribe')); ?>
					<?php wp_subscribe_options_text_field('button_text', __('Button Text', 'wp-subscribe')); ?>
					<?php wp_subscribe_options_text_field('success_message', __('Success Message', 'wp-subscribe')); ?>
					<?php wp_subscribe_options_text_field('error_message', __('Error Message', 'wp-subscribe')); ?>
					<?php wp_subscribe_options_text_field('footer_text', __('Footer Text', 'wp-subscribe')); ?>
					<div class="wp-subscribe-content-colors">
						<?php wp_subscribe_color_palettes_select('wp_subscribe_options_colors_popup_form_colors'); ?>

						<?php wp_subscribe_options_color_field('background_color', __('Background color', 'wp-subscribe')); ?>
						<?php wp_subscribe_options_color_field('title_color', __('Title color', 'wp-subscribe')); ?>
						<?php wp_subscribe_options_color_field('text_color', __('Text color', 'wp-subscribe')); ?>
						<?php wp_subscribe_options_color_field('field_text_color', __('Field text color', 'wp-subscribe')); ?>
						<?php wp_subscribe_options_color_field('field_background_color', __('Field background color', 'wp-subscribe')); ?>
						<?php wp_subscribe_options_color_field('button_text_color', __('Button text color', 'wp-subscribe')); ?>
						<?php wp_subscribe_options_color_field('button_background_color', __('Button background color', 'wp-subscribe')); ?>
						<?php wp_subscribe_options_color_field('footer_text_color', __('Footer text color', 'wp-subscribe')); ?>
					</div>
				</div>

				<div class="wp-subscribe-field" id="wp-subscribe-custom-html-field"<?php echo $options['popup_content'] != 'custom_html' ? ' style="display: none;"' : ''; ?>>
					<?php wp_editor($options['popup_custom_html'], 'wp_subscribe_options[popup_custom_html]', array('textarea_rows'=> 8, 'tinymce' => false, 'media_buttons' => false, 'wpautop' => false, 'quicktags' => array('buttons' => 'strong,em,block,del,ins,img,ul,ol,li,code,close') )); ?>
				</div>

				<div class="wp-subscribe-field" id="wp-subscribe-popup-posts-options"<?php echo $options['popup_content'] != 'posts' ? ' style="display: none;"' : ''; ?>>
					<?php wp_subscribe_options_text_field('title', __('Title', 'wp-subscribe'), 'popup_posts_labels'); ?>
					<?php wp_subscribe_options_text_field('text', __('Text', 'wp-subscribe'), 'popup_posts_labels'); ?>
					<div class="wp-subscribe-content-colors">
						<?php wp_subscribe_options_color_field('background_color', __('Background color', 'wp-subscribe'), 'popup_posts_colors'); ?>
						<?php wp_subscribe_options_color_field('title_color', __('Title color', 'wp-subscribe'), 'popup_posts_colors'); ?>
						<?php wp_subscribe_options_color_field('text_color', __('Text color', 'wp-subscribe'), 'popup_posts_colors'); ?>
						<?php wp_subscribe_options_color_field('line_color', __('Line color', 'wp-subscribe'), 'popup_posts_colors'); ?>
					</div>
					<h4><?php _e( 'Post Meta', 'wp-subscribe' ); ?></h4>
					<label class="postmeta-label" for="meta_showcategory">
						<input type="hidden" name="wp_subscribe_options[popup_posts_meta][category]" value="0">
						<input type="checkbox" name="wp_subscribe_options[popup_posts_meta][category]" id="meta_showcategory" value="1" <?php checked($options['popup_posts_meta']['category']); ?>>
						<?php _e('Show post categories', 'wp-subscribe'); ?>
					</label>
					<label class="postmeta-label" for="meta_showexcerpt">
						<input type="hidden" name="wp_subscribe_options[popup_posts_meta][excerpt]" value="0">
						<input type="checkbox" name="wp_subscribe_options[popup_posts_meta][excerpt]" id="meta_showexcerpt" value="1" <?php checked($options['popup_posts_meta']['excerpt']); ?>>
						<?php _e('Show post excerpt', 'wp-subscribe'); ?>
					</label>
				</div>

			    <p class="submit">
			    <a href="#wp_subscribe_popup" class="button-secondary wp-subscribe-preview-popup" data-animatein="<?php echo $options['popup_animation_in']; ?>" data-animateout="<?php echo $options['popup_animation_out']; ?>"><?php _e('Preview Popup') ?></a>
			    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			    </p>
			</div>
	    	
	    	<!-- Popup Triggers Tab -->

			<div class="wps-popup-trigger-options" style="display: none;">
				<h4><?php _e( 'Popup pages', 'wp-subscribe' ); ?></h4>
				<div class="wp-subscribe-field">
					<label for="wp_subscribe_popup_show_on_front_page">
						<input type="hidden" name="wp_subscribe_options[popup_show_on][front_page]" value="0">
						<input type="checkbox" name="wp_subscribe_options[popup_show_on][front_page]" value="1" id="wp_subscribe_popup_show_on_front_page" <?php checked( $options['popup_show_on']['front_page'] ); ?>>
						<?php _e( 'Show popup on front page', 'wp-subscribe' ); ?>
					</label><br />
					<label for="wp_subscribe_popup_show_on_single">
						<input type="hidden" name="wp_subscribe_options[popup_show_on][single]" value="0">
						<input type="checkbox" name="wp_subscribe_options[popup_show_on][single]" value="1" id="wp_subscribe_popup_show_on_single" <?php checked( $options['popup_show_on']['single'] ); ?>>
						<?php _e( 'Show popup on single posts, pages, and other post types', 'wp-subscribe' ); ?>
					</label><br />
					<label for="wp_subscribe_popup_show_on_archive">
						<input type="hidden" name="wp_subscribe_options[popup_show_on][archive]" value="0">
						<input type="checkbox" name="wp_subscribe_options[popup_show_on][archive]" value="1" id="wp_subscribe_popup_show_on_archive" <?php checked( $options['popup_show_on']['archive'] ); ?>>
						<?php _e( 'Show popup on archive pages (posts by date, category, etc.)', 'wp-subscribe' ); ?>
					</label><br />
					<label for="wp_subscribe_popup_show_on_search">
						<input type="hidden" name="wp_subscribe_options[popup_show_on][search]" value="0">
						<input type="checkbox" name="wp_subscribe_options[popup_show_on][search]" value="1" id="wp_subscribe_popup_show_on_search" <?php checked( $options['popup_show_on']['search'] ); ?>>
						<?php _e( 'Show popup on search results', 'wp-subscribe' ); ?>
					</label><br />
					<label for="wp_subscribe_popup_show_on_404_page">
						<input type="hidden" name="wp_subscribe_options[popup_show_on][404_page]" value="0">
						<input type="checkbox" name="wp_subscribe_options[popup_show_on][404_page]" value="1" id="wp_subscribe_popup_show_on_404_page" <?php checked( $options['popup_show_on']['404_page'] ); ?>>
						<?php _e( 'Show popup on 404 Not Found page', 'wp-subscribe' ); ?>
					</label>
				</div>

				<h4><?php _e( 'Popup triggers', 'wp-subscribe' ); ?></h4>
				<p class="wp-subscribe-field">
					<label for="wp_subscribe_popup_trigger_enter">
						<input type="hidden" name="wp_subscribe_options[popup_triggers][on_enter]" value="0">
						<input type="checkbox" name="wp_subscribe_options[popup_triggers][on_enter]" id="wp_subscribe_popup_trigger_enter" value="1" <?php checked( $options['popup_triggers']['on_enter'] ); ?>>
						<?php _e( 'Show popup when visitor enters site', 'wp-subscribe' ); ?>
					</label><br />
					<label for="wp_subscribe_popup_trigger_timeout">
						<input type="hidden" name="wp_subscribe_options[popup_triggers][on_timeout]" value="0">
						<input type="checkbox" name="wp_subscribe_options[popup_triggers][on_timeout]" id="wp_subscribe_popup_trigger_timeout" value="1" <?php checked( $options['popup_triggers']['on_timeout'] ); ?>>
						<?php 
							$input_seconds = '<input type="number" min="1" max="120" step="1" class="small-text" name="wp_subscribe_options[popup_triggers][timeout]" value="'.$options['popup_triggers']['timeout'].'">';
							$label = sprintf( __( 'Show popup after %s seconds.', 'wp-subscribe' ), $input_seconds );
							echo $label;
						?>
					</label><br />
					<label for="wp_subscribe_popup_trigger_reach_bottom">
						<input type="hidden" name="wp_subscribe_options[popup_triggers][on_reach_bottom]" value="0">
						<input type="checkbox" name="wp_subscribe_options[popup_triggers][on_reach_bottom]" id="wp_subscribe_popup_trigger_reach_bottom" value="1" <?php checked( $options['popup_triggers']['on_reach_bottom'] ); ?>>
						<?php _e( 'Show popup when visitor reaches the end of the content (only on single posts &amp; pages)', 'wp-subscribe' ); ?>
					</label><br />
					<label for="wp_subscribe_popup_trigger_exit_intent">
						<input type="hidden" name="wp_subscribe_options[popup_triggers][on_exit_intent]" value="0">
						<input type="checkbox" name="wp_subscribe_options[popup_triggers][on_exit_intent]" id="wp_subscribe_popup_trigger_exit_intent" value="1" <?php checked( $options['popup_triggers']['on_exit_intent'] ); ?>>
						<?php _e( 'Show popup when visitor is about to leave (exit intent)', 'wp-subscribe' ); ?>
					</label>
				</p>
				<p class="description"><?php _e('Note: popup will only appear once for each visitor until the cookie expires', 'wp-subscribe'); ?></p>

				<p class="wp-subscribe-field">
					<label for="wp_subscribe_cookie_expiration"><?php _e('Cookie expiration:', 'wp-subscribe'); ?><br />
					<input type="number" min="0" max="365" step="1" class="small-text" name="wp_subscribe_options[cookie_expiration]" value="<?php echo $options['cookie_expiration']; ?>" id="wp_subscribe_cookie_expiration">
					<?php _e('days', 'wp-subscribe'); ?> <span class="description">(<?php _e('Set to 0 to create cookies that last only for one browser session.', 'wp-subscribe'); ?>)</span>
					</label>
				</p>

			    <p>
			    	<?php _e('Clear cookies for all visitors:') ?><br />
			    	<a href="#" class="button-secondary" id="wp_subscribe_regenerate_cookie" title="<?php esc_attr_e('Click this button to make the popup show for all visitors once, even for those who already saw it.', 'wp-subscribe'); ?>"><?php _e('Generate new cookies') ?></a>
					<input type="hidden" name="wp_subscribe_options[cookie_hash]" id="cookiehash" value="<?php echo $options['cookie_hash']; ?>">
					<span id="cookies-cleared"><i class="dashicons dashicons-yes"></i> <?php _e('Please save the options to apply changes.', 'wp-subscribe'); ?></span>
				</p>

			    <p class="submit">
			    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			    </p>
			</div>
	    	
	    	<!-- Single Posts Tab -->

			<div class="wps-post-options" style="display: none;">

				<h3 class="wp-subscribe-field">
					<label for="wp_subscribe_enable_single_post_form">
						<input type="hidden" name="wp_subscribe_options[enable_single_post_form]" value="0">
						<input id="wp_subscribe_enable_single_post_form" type="checkbox" name="wp_subscribe_options[enable_single_post_form]" value="1" <?php checked($options['enable_single_post_form']); ?>>
						<?php _e( 'Add Subscribe Form to Single Posts', 'wp-subscribe' ); ?>
					</label>
				</h3>
				<p><?php _e( 'Show subscribe form before, after, or inside the content on single posts and pages.', 'wp-subscribe' ); ?></p>

				<div id="wp-subscribe-single-options"<?php echo !$options['enable_single_post_form'] ? ' style="display: none;"' : ''; ?>>

					<p class="wp-subscribe-field">
						<label for="wp_subscribe_form_before_single">
							<input type="radio" name="wp_subscribe_options[single_post_form_location]" value="top" id="wp_subscribe_form_before_single" <?php checked($options['single_post_form_location'], 'top'); ?>>
							<?php _e( 'Before post content', 'wp-subscribe' ); ?>
						</label><br />
						<label for="wp_subscribe_form_after_single">
							<input type="radio" name="wp_subscribe_options[single_post_form_location]" value="bottom" id="wp_subscribe_form_after_single" <?php checked($options['single_post_form_location'], 'bottom'); ?>>
							<?php _e( 'After post content', 'wp-subscribe' ); ?>
						</label><br />
						<label for="wp_subscribe_form_custom_single">
							<input type="radio" name="wp_subscribe_options[single_post_form_location]" value="custom" id="wp_subscribe_form_custom_single" <?php checked($options['single_post_form_location'], 'custom'); ?>>
							<?php _e( 'Only shortcode', 'wp-subscribe' ); ?>
						</label>
					</p>
					<p class="wp-subscribe-field">
			    		<a href="#" id="copy_options_popup_to_single" class="button-secondary ifpopup"<?php echo $options['enable_popup'] ? '' : ' style="display: none;"'; ?>><?php _e('Copy popup form settings') ?></a>
					</p>
					<p class="wp-subscribe-field">
						<select name="wp_subscribe_options[single_post_form_options][service]" id="single_post_form_service" class="services_dropdown">
							<option value="feedburner" <?php selected( $options['single_post_form_options']['service'], 'feedburner' ); ?>>FeedBurner</option>
							<option value="mailchimp" <?php selected( $options['single_post_form_options']['service'], 'mailchimp' ); ?>>Mailchimp</option>
							<option value="getresponse" <?php selected( $options['single_post_form_options']['service'], 'getresponse' ); ?>>GetResponse</option>
							<option value="aweber" <?php selected( $options['single_post_form_options']['service'], 'aweber' ); ?>>AWeber</option>
						</select>
					</p>
					<div class="wp_subscribe_account_details">
	   					<div class="wp_subscribe_account_details_feedburner" style="display: none;">
							<?php wp_subscribe_options_text_field('feedburner_id', __('Feedburner ID', 'wp-subscribe'), 'single_post_form_options'); ?>
						</div><!-- .wp_subscribe_account_details_feedburner -->

			        	<div class="wp_subscribe_account_details_mailchimp" style="display: none;">
		        			<?php wp_subscribe_options_text_field('mailchimp_api_key', __('MailChimp API Key', 'wp-subscribe'), 'single_post_form_options'); ?>
							<?php wp_subscribe_options_text_field('mailchimp_list_id', __('MailChimp List ID', 'wp-subscribe'), 'single_post_form_options'); ?>
	            			<p class="clear"><a href="http://kb.mailchimp.com/accounts/management/about-api-keys#Finding-or-generating-your-API-key" target="_blank"><?php _e('Find your API key', 'wp-subscribe'); ?></a> | 
	            			<a href="http://kb.mailchimp.com/lists/managing-subscribers/find-your-list-id" target="_blank"><?php _e('Find your list ID', 'wp-subscribe'); ?></a></p>
							<p class="wp_subscribe_mailchimp_double_optin"><label for="single_post_form_mailchimp_double_optin">
				                <input type="hidden" name="wp_subscribe_options[single_post_form_options][mailchimp_double_optin]" value="0">
				                <input id="single_post_form_mailchimp_double_optin" type="checkbox" name="wp_subscribe_options[single_post_form_options][mailchimp_double_optin]" value="1" <?php checked($options['single_post_form_options']['mailchimp_double_optin']); ?>>
				                <?php _e( 'Send double opt-in notification', 'wp-subscribe' ); ?>
				            </label></p>
						</div><!-- .wp_subscribe_account_details_mailchimp -->

	    				<div class="wp_subscribe_account_details_aweber" style="display: none;">
							<?php wp_subscribe_options_text_field('aweber_list_id', __('AWeber List ID', 'wp-subscribe'), 'single_post_form_options'); ?>
	           				<p class="clear"><a href="https://help.aweber.com/entries/61177326-What-Is-The-Unique-List-ID-" target="_blank"><?php _e('Find your list ID', 'wp-subscribe'); ?></a></p>
						</div><!-- .wp_subscribe_account_details_aweber -->

						<div class="wp_subscribe_account_details_getresponse" style="display: none;">
		        			<?php wp_subscribe_options_text_field('getresponse_api_key', __('GetResponse API Key', 'wp-subscribe'), 'single_post_form_options'); ?>
							<?php wp_subscribe_options_text_field('getresponse_list_id', __('GetResponse Campaign Name', 'wp-subscribe'), 'single_post_form_options'); ?>
	            			<p class="clear"><a href="http://support.getresponse.com/faq/where-i-find-api-key" target="_blank"><?php _e('Find your API key', 'wp-subscribe'); ?></a> | 
	            			<a href="http://support.getresponse.com/faq/can-i-change-the-name-of-a-campaign" target="_blank"><?php _e('Find the campaign name', 'wp-subscribe'); ?></a></p>
						</div><!-- .wp_subscribe_account_details_getresponse -->
				    </div><!-- .wp_subscribe_account_details -->

				    <div class="wp-subscribe-field wp_subscribe_include_name_wrapper" <?php echo $options['single_post_form_options']['service'] == 'feedburner' ? ' style="display: none;"' : ''; ?>>
	    			    <label for="wp_subscribe_single_post_form_include_name">
							<input type="hidden" name="wp_subscribe_options[single_post_form_options][include_name_field]" value="0">
							<input id="wp_subscribe_single_post_form_include_name" type="checkbox" name="wp_subscribe_options[single_post_form_options][include_name_field]" value="1" <?php checked($options['single_post_form_options']['include_name_field']); ?>>
							<?php _e( 'Include <strong>Name</strong> field', 'wp-subscribe' ); ?>
						</label>
					</div>

					<div class="wp-subscribe-field" id="wp-subscribe-single-options">
						<?php wp_subscribe_options_text_field('title', __('Title', 'wp-subscribe'), 'single_post_form_labels'); ?>
						<?php wp_subscribe_options_text_field('text', __('Text', 'wp-subscribe'), 'single_post_form_labels'); ?>
						<?php wp_subscribe_options_text_field('name_placeholder', __('Name Placeholder Text', 'wp-subscribe'), 'single_post_form_labels'); ?>
						<?php wp_subscribe_options_text_field('email_placeholder', __('Email Placeholder Text', 'wp-subscribe'), 'single_post_form_labels'); ?>
						<?php wp_subscribe_options_text_field('button_text', __('Button Text', 'wp-subscribe'), 'single_post_form_labels'); ?>
						<?php wp_subscribe_options_text_field('success_message', __('Success Message', 'wp-subscribe'), 'single_post_form_labels'); ?>
						<?php wp_subscribe_options_text_field('error_message', __('Error Message', 'wp-subscribe'), 'single_post_form_labels'); ?>
						<?php wp_subscribe_options_text_field('footer_text', __('Footer Text', 'wp-subscribe'), 'single_post_form_labels'); ?>
						<div class="wp-subscribe-content-colors">
							<?php wp_subscribe_color_palettes_select('wp_subscribe_options_colors_single_post_form_colors'); ?>

							<?php wp_subscribe_options_color_field('background_color', __('Background color', 'wp-subscribe'), 'single_post_form_colors'); ?>
							<?php wp_subscribe_options_color_field('title_color', __('Title color', 'wp-subscribe'), 'single_post_form_colors'); ?>
							<?php wp_subscribe_options_color_field('text_color', __('Text color', 'wp-subscribe'), 'single_post_form_colors'); ?>
							<?php wp_subscribe_options_color_field('field_text_color', __('Field text color', 'wp-subscribe'), 'single_post_form_colors'); ?>
							<?php wp_subscribe_options_color_field('field_background_color', __('Field background color', 'wp-subscribe'), 'single_post_form_colors'); ?>
							<?php wp_subscribe_options_color_field('button_text_color', __('Button text color', 'wp-subscribe'), 'single_post_form_colors'); ?>
							<?php wp_subscribe_options_color_field('button_background_color', __('Button background color', 'wp-subscribe'), 'single_post_form_colors'); ?>
							<?php wp_subscribe_options_color_field('footer_text_color', __('Footer text color', 'wp-subscribe'), 'single_post_form_colors'); ?>
						</div>
					</div>

					<p><?php _e('You may also use the <code>[wp-subscribe]</code> shortcode in your posts &amp; pages.') ?></p>
				</div>
			    <p class="submit">
			    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			    </p>
			</div>
		</div><!-- .wps-tabs-wrapper -->
	</form>
	<?php wp_subscribe_popup_html(); ?>
	
</div>
<?php }
function wp_subscribe_options_color_field($field_id, $label, $group='popup_form_colors') {
    $options = wp_parse_args( get_option( 'wp_subscribe_options' ), wp_subscribe_default_options() );
	
	if ($group)
		$value = empty($options[$group][$field_id]) ? '' : $options[$group][$field_id];
	else
		$value = empty($options[$field_id]) ? '' : $options[$field_id];
	?>
	<div class="wp-subscribe-color-field">
        <label for="wp_subscribe_options_colors_<?php echo $field_id; ?>">
            <?php echo $label; ?>
        </label>

        <input class="wp-subscribe-color-select" 
               id="wp_subscribe_options_colors<?php echo $group ? '_'.$group : ''; ?>_<?php echo $field_id; ?>" 
               <?php if ($group) { ?>
               name="wp_subscribe_options[<?php echo $group; ?>][<?php echo $field_id; ?>]" 
               <?php } else { ?>
               name="wp_subscribe_options[<?php echo $field_id; ?>]" 
               <?php } ?>
               type="text" 
               value="<?php echo $value; ?>" />
    </div>
	<?php
}
function wp_subscribe_options_text_field($field_id, $label, $group='popup_form_labels') {
    $options = wp_parse_args( get_option( 'wp_subscribe_options' ), wp_subscribe_default_options() );

	if ($group)
		$value = empty($options[$group][$field_id]) ? '' : $options[$group][$field_id];
	else
		$value = empty($options[$field_id]) ? '' : $options[$field_id];
	?>
	<div class="wp-subscribe-label-field <?php echo $group ? '_'.$group : ''; ?>_<?php echo $field_id; ?>-wrapper">
				            <label for="wp_subscribe_options_labels_<?php echo $field_id; ?>">
				                <?php echo $label; ?>
				            </label>

				            <input id="wp_subscribe_options_labels<?php echo $group ? '_'.$group : ''; ?>_<?php echo $field_id; ?>" 
				            	   <?php if ($group) { ?>
				                   name="wp_subscribe_options[<?php echo $group; ?>][<?php echo $field_id; ?>]" 
				                   <?php } else { ?>
				                   name="wp_subscribe_options[<?php echo $field_id; ?>]" 
				                   <?php } ?>
				                   type="text" 
				                   value="<?php echo esc_attr($value); ?>" />
				        </div>
	<?php
}
// Add settings link on plugin page
function wp_subscribe_plugin_settings_link($links) {
	$dir = explode('/', WP_SUBSCRIBE_PRO_PLUGIN_BASE);
	$dir = $dir[0];
	$settings_link = '<a href="options-general.php?page='.$dir.'/options.php">'.__('Settings', 'wp-subscribe').'</a>'; 
	array_unshift($links, $settings_link); 
	return $links; 
}
add_filter('plugin_action_links_'.WP_SUBSCRIBE_PRO_PLUGIN_BASE, 'wp_subscribe_plugin_settings_link' );

// AJAX for getting shortcode content
function wp_subscribe_ajax_preview_popup() {
	$content = $_POST['wp_subscribe_options']['popup_content'];
	if ($content == 'custom_html') {
		echo do_shortcode(stripslashes($_POST['wp_subscribe_options']['popup_custom_html']));
	} elseif ($content == 'subscribe_form') {
		the_widget( 'wp_subscribe', array(
            'service' => 'mailchimp',
            'include_name_field' => $_POST['wp_subscribe_options']['popup_form_options']['include_name_field'],
            'feedburner_id' => '',
            'mailchimp_api_key' => '',
            'mailchimp_list_id' => '',
            'aweber_list_id' => '',
            'getresponse_api_key' => '',
            'getresponse_list_id' => '',

            'title' => $_POST['wp_subscribe_options']['popup_form_labels']['title'],
            'text' => $_POST['wp_subscribe_options']['popup_form_labels']['text'],
            'name_placeholder' => $_POST['wp_subscribe_options']['popup_form_labels']['name_placeholder'],
            'email_placeholder' => $_POST['wp_subscribe_options']['popup_form_labels']['email_placeholder'],
            'button_text' => $_POST['wp_subscribe_options']['popup_form_labels']['button_text'],
            'success_message' => $_POST['wp_subscribe_options']['popup_form_labels']['success_message'],
            'error_message' => $_POST['wp_subscribe_options']['popup_form_labels']['error_message'],
            'footer_text' => $_POST['wp_subscribe_options']['popup_form_labels']['footer_text'],

            'background_color' => $_POST['wp_subscribe_options']['popup_form_colors']['background_color'],
            'title_color' => $_POST['wp_subscribe_options']['popup_form_colors']['title_color'],
            'text_color' => $_POST['wp_subscribe_options']['popup_form_colors']['text_color'],
            'field_text_color' => $_POST['wp_subscribe_options']['popup_form_colors']['field_text_color'],
            'field_background_color' => $_POST['wp_subscribe_options']['popup_form_colors']['field_background_color'],
            'button_text_color' => $_POST['wp_subscribe_options']['popup_form_colors']['button_text_color'],
            'button_background_color' => $_POST['wp_subscribe_options']['popup_form_colors']['button_background_color'],
            'footer_text_color' => $_POST['wp_subscribe_options']['popup_form_colors']['footer_text_color']
        ), array() );
	} elseif ($content == 'posts') {
		echo wp_subscribe_get_related_posts($_POST['wp_subscribe_options']);
	}
	exit();
}
add_action( 'wp_ajax_preview_popup', 'wp_subscribe_ajax_preview_popup' );

function wp_subscribe_color_palettes_select($target) {
	$palettes = wp_subscribe_get_default_color_palettes();
	if (!empty($palettes)) {
		?>
		<div class="wps-colors-loader">
			<a href="#" class="wps-toggle-palettes"><?php _e('Load a predefined color set', 'wp-subscribe'); ?></a>
			<div class="wps-palettes">
				<?php foreach ($palettes as $i => $palette) { ?>
				<div class="single-palette">
				<table class="color-palette">
					<tbody>
						<tr>
							<?php foreach ($palette['colors'] as $color) { ?>
							<td style="background-color: <?php echo $color; ?>">&nbsp;</td>
							<?php } ?>
						</tr>
					</tbody>
				</table>
				<?php foreach ($palette['colors'] as $field => $color) { ?>
				<input type="hidden" class="wps-palette-color" name="<?php echo $target.'_'.$field.'_color'; ?>" value="<?php echo $color; ?>" />
				<?php } ?>
				<a href="#" class="button button-secondary wps-load-palette"><?php _e('Load colors', 'wp-subscribe'); ?></a>
				</div>
				<?php } ?>
			</div>
		</div>
		<?php 
	}
}

function wp_subscribe_get_default_color_palettes() {
	$default_palettes = array(
		'black_and_white' => array(
			'colors' => array(
				'background' => '#f5f5f5', 
		        'title' => '#2a2f2d', 
		        'text' => '#959494', 
		        'field_text' => '#999999', 
		        'field_background' => '#e7e7e7', 
		        'button_text' => '#2a2f2d', 
		        'button_background' => '#ffa054', 
		        'footer_text' => '#959494'
			)
		),
		'wp_subscribe_default' => array(
			'colors' => array(
				'background' => '#f47555', 
				'title' => '#FFFFFF', 
				'text' => '#FFFFFF', 
				'field_text' => '#FFFFFF', 
				'field_background' => '#d56144', 
				'button_text' => '#f47555', 
				'button_background' => '#FFFFFF', 
				'footer_text' => '#FFFFFF'
				)
			)
		);
	return apply_filters( 'wp_subscribe_form_color_palettes', $default_palettes );
}