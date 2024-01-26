<?php

$options = get_option('wp_subscribe_options');

if ($options['enable_popup']) {	
	add_action( 'wp_footer', 'wp_subscribe_popup' );
}

function wp_subscribe_popup() {
	$options = get_option('wp_subscribe_options');
	if (empty($_COOKIE['wps_cookie_'.$options['cookie_hash']]) && empty($_SESSION['wps_cookie_'.$options['cookie_hash']])) { // if cookie is not set
		
		// check if popup should be displayed on current page or not (global settings)
		if ((is_front_page() && ! $options['popup_show_on']['front_page'])
			|| (is_singular() && ! $options['popup_show_on']['single'])
			|| (is_archive() && ! $options['popup_show_on']['archive'])
			|| (is_search() && ! $options['popup_show_on']['search'])
			|| (is_404() && ! $options['popup_show_on']['404_page']))
				return;

		// check if popup is excluded on individual post
		if (is_singular()) {
			global $post;
			$disable_popup = get_post_meta( $post->ID, '_wp_subscribe_disable_popup', true );
			if ($disable_popup) {
				return;
			}
		}

		wp_subscribe_popup_html();
		wp_subscribe_popup_js();
		wp_subscribe_enqueue_popup_css();
		wp_subscribe_enqueue_popup_js();
	}
}

function wp_subscribe_popup_html($options = null) {
	if ($options == null)
		$options = get_option('wp_subscribe_options');

	// classes for responsive
	$breakpoints = array(300, 600, 900);
	$responsive_class = '';
	foreach ($breakpoints as $breakpoint) {
		if ($options['popup_width'] < $breakpoint ) {
			$responsive_class .= "lt_$breakpoint ";
		}
	}
	$responsive_class = trim($responsive_class);

	echo '<div id="wp_subscribe_popup" class="wp-subscribe-popup mfp-hide '.$responsive_class.'">';
	if ($options['popup_content'] == 'subscribe_form') {
		the_widget( 'wp_subscribe', array(
            'service' => $options['popup_form_options']['service'],
            'include_name_field' => $options['popup_form_options']['include_name_field'],
            'feedburner_id' => $options['popup_form_options']['feedburner_id'],
            'mailchimp_api_key' => $options['popup_form_options']['mailchimp_api_key'],
            'mailchimp_list_id' => $options['popup_form_options']['mailchimp_list_id'],
            'aweber_list_id' => $options['popup_form_options']['aweber_list_id'],

            'title' => $options['popup_form_labels']['title'],
            'text' => $options['popup_form_labels']['text'],
            'name_placeholder' => $options['popup_form_labels']['name_placeholder'],
            'email_placeholder' => $options['popup_form_labels']['email_placeholder'],
            'button_text' => $options['popup_form_labels']['button_text'],
            'success_message' => $options['popup_form_labels']['success_message'],
            'error_message' => $options['popup_form_labels']['error_message'],
            'footer_text' => $options['popup_form_labels']['footer_text'],

            'background_color' => $options['popup_form_colors']['background_color'],
            'title_color' => $options['popup_form_colors']['title_color'],
            'text_color' => $options['popup_form_colors']['text_color'],
            'field_text_color' => $options['popup_form_colors']['field_text_color'],
            'field_background_color' => $options['popup_form_colors']['field_background_color'],
            'button_text_color' => $options['popup_form_colors']['button_text_color'],
            'button_background_color' => $options['popup_form_colors']['button_background_color'],
            'footer_text_color' => $options['popup_form_colors']['footer_text_color'],
	        'from_popup' => '1'
        ), array('before_widget' => '<div class="wp-subscribe-popup-form-wrapper">') );
	} elseif ($options['popup_content'] == 'custom_html') {
		echo do_shortcode($options['popup_custom_html']);
	} elseif ($options['popup_content'] == 'posts') {
		echo wp_subscribe_get_related_posts();
	}
	echo '</div>';
	?>
	<style type="text/css" id="popup-style-width">#wp_subscribe_popup {width: <?php echo $options['popup_width']; ?>px;}</style>
	<style type="text/css" id="overlay-style-color">body > .mfp-bg {background: <?php echo $options['popup_overlay_color']; ?>;}</style>
	<style type="text/css" id="overlay-style-opacity">body > .mfp-bg.mfp-ready {opacity: <?php echo $options['popup_overlay_opacity']; ?>;}</style>

	<?php
}

function wp_subscribe_popup_js() {
	$options = get_option('wp_subscribe_options');
	$removal_delay = 800;
	if ($options['popup_animation_out'] == 'hinge') {
		$removal_delay = 2000;
	} elseif ($options['popup_animation_out'] == '0') {
		$removal_delay = 0;
	}
	?>
<script type="text/javascript">
var wps_disabled = false;
function wp_subscribe_popup() {
	if (!wps_disabled && !jQuery.cookie('wps_cookie_<?php echo $options['cookie_hash']; ?>')) {
		jQuery.magnificPopup.open({
			items: {
		  		src: '#wp_subscribe_popup',
		  		type: 'inline'
			},
			removalDelay: <?php echo $removal_delay; ?>,
			callbacks: {
			    beforeOpen: function() {
			       this.st.mainClass = 'animated <?php echo $options['popup_animation_in']; ?>';
			    },
			    beforeClose: function() {
			    	var $wrap = this.wrap,
			    		$bg = $wrap.prev(),
			    		$mfp = $wrap.add($bg);

			    	$mfp.removeClass('<?php echo $options['popup_animation_in']; ?>').addClass('<?php echo $options['popup_animation_out']; ?>');
			    },
			  },
		});
		jQuery.cookie('wps_cookie_<?php echo $options['cookie_hash']; ?>', '1', { path: '/'<?php if ($options['cookie_expiration']) { ?>, expires: <?php echo (int) $options['cookie_expiration'] ?><?php } ?>});
		wps_disabled = true;
	}
}
<?php if ($options['popup_triggers']['on_enter'] || $options['popup_triggers']['on_reach_bottom']) { ?>
jQuery(window).load(function() {
<?php if ($options['popup_triggers']['on_enter']) { ?>
	wp_subscribe_popup();
<?php } ?>
<?php if ((is_singular( 'post' ) || is_singular( 'page' )) && !empty($options['popup_triggers']['on_reach_bottom'])) { ?>
	if (jQuery('#wp-subscribe-content-bottom').length) {
		var content_bottom = Math.floor(jQuery('#wp-subscribe-content-bottom').offset().top);
		jQuery(window).scroll(function(event) {
			var viewport_bottom = jQuery(window).scrollTop() + jQuery(window).height();
			if (viewport_bottom >= content_bottom) wp_subscribe_popup();
		});
	}
<?php } ?>
});
<?php } ?>

jQuery(document).ready(function($) {
<?php if ($options['popup_triggers']['on_timeout']) { ?>
	setTimeout(wp_subscribe_popup, <?php echo 1000 * $options['popup_triggers']['timeout']; ?>);
<?php } ?>
<?php if ($options['popup_triggers']['on_exit_intent']) { ?>
	$(document).exitIntent(wp_subscribe_popup);
<?php } ?>
});
</script>
	<?php
}

// Add catcher element for "reach bottom" trigger
if ($options['enable_popup'] && !empty($options['popup_triggers']['on_reach_bottom'])) {
	function wp_subscribe_single_post_content_end($content) {
		$options = get_option('wp_subscribe_options');
		if ((is_singular( 'post' ) || is_singular( 'page' ))  && is_main_query() && in_the_loop()) {
			$content .= '<div id="wp-subscribe-content-bottom"></div>';
		}
		return $content;
	}
	add_filter( 'the_content', 'wp_subscribe_single_post_content_end' );
}

if ($options['enable_single_post_form']) {
	function wp_subscribe_single_post_form($content) {
		$options = get_option('wp_subscribe_options');
		if (is_singular('post') && is_main_query() && in_the_loop()) {

			global $post;
			$disabled = get_post_meta( $post->ID, '_wp_subscribe_disable_single', true );
			if ($disabled) return $content; // abort

			// Add before/after post content
			$position = $options['single_post_form_location'];
			$pos_class = '';
			if ($position == 'top') {
				$pos_class = ' wp-subscribe-before-content';
			} elseif ($position == 'bottom') {
				$pos_class = ' wp-subscribe-after-content';
			}
			
			$widget = wp_subscribe_get_the_widget( 'wp_subscribe', array(
	            'service' => $options['single_post_form_options']['service'],
            	'include_name_field' => $options['single_post_form_options']['include_name_field'],
	            'feedburner_id' => $options['single_post_form_options']['feedburner_id'],
	            'mailchimp_api_key' => $options['single_post_form_options']['mailchimp_api_key'],
	            'mailchimp_list_id' => $options['single_post_form_options']['mailchimp_list_id'],
	            'aweber_list_id' => $options['single_post_form_options']['aweber_list_id'],

	            'title' => $options['single_post_form_labels']['title'],
	            'text' => $options['single_post_form_labels']['text'],
            	'name_placeholder' => $options['single_post_form_labels']['name_placeholder'],
	            'email_placeholder' => $options['single_post_form_labels']['email_placeholder'],
	            'button_text' => $options['single_post_form_labels']['button_text'],
	            'success_message' => $options['single_post_form_labels']['success_message'],
	            'error_message' => $options['single_post_form_labels']['error_message'],
	            'footer_text' => $options['single_post_form_labels']['footer_text'],

	            'background_color' => $options['single_post_form_colors']['background_color'],
	            'title_color' => $options['single_post_form_colors']['title_color'],
	            'text_color' => $options['single_post_form_colors']['text_color'],
	            'field_text_color' => $options['single_post_form_colors']['field_text_color'],
	            'field_background_color' => $options['single_post_form_colors']['field_background_color'],
	            'button_text_color' => $options['single_post_form_colors']['button_text_color'],
	            'button_background_color' => $options['single_post_form_colors']['button_background_color'],
	            'footer_text_color' => $options['single_post_form_colors']['footer_text_color'],
	            'from_single' => '1'
	        ), array('before_widget' => '<div class="wp-subscribe-single'.$pos_class.'">') );
			if ($position == 'top') {
				$content = $widget.$content;
			} elseif ($position == 'bottom')  {
				$content = $content.$widget;
			}
		}
		return $content;
	}
	add_filter( 'the_content', 'wp_subscribe_single_post_form', 20 );
}

function wp_subscribe_enqueue_popup_css() {
	wp_enqueue_style( 'wp-subscribe', plugins_url('css/wp-subscribe-form.css', __FILE__) );
	wp_enqueue_style( 'wp-subscribe-popup', plugins_url('css/wp-subscribe-popup.css', __FILE__) );
}
function wp_subscribe_enqueue_popup_js() {
	wp_register_script('wp-subscribe', plugins_url('js/wp-subscribe-form.js', __FILE__), array('jquery'));     
	wp_localize_script( 'wp-subscribe', 'wp_subscribe', array('ajaxurl' => admin_url('admin-ajax.php')) );
	wp_enqueue_script( 'wp-subscribe' );
	wp_enqueue_script( 'magnific-popup', plugins_url('js/magnificpopup.js', __FILE__), array('jquery') );
	wp_enqueue_script( 'jquery-cookie', plugins_url('js/jquery.cookie.js', __FILE__), array('jquery') );
	wp_enqueue_script( 'exitIntent', plugins_url('js/jquery.exitIntent.js', __FILE__), array('jquery') );

}
function wp_subscribe_get_related_posts($options = null) {
	if ($options == null)
		$options = get_option('wp_subscribe_options');
	$html = '<div class="popup-related-posts-wrapper popup-content" style="background: '.$options['popup_posts_colors']['background_color'].';">';
	$html .= '<h3 style="color: '.$options['popup_posts_colors']['title_color'].'">'.$options['popup_posts_labels']['title'].'</h3>';
	$html .= '<p style="color: '.$options['popup_posts_colors']['text_color'].'">'.$options['popup_posts_labels']['text'].'</p>';
	$html .= '<div class="popup-related-posts" style="border-top-color: '.$options['popup_posts_colors']['line_color'].';">';
	$posts_html = '';
	global $post;
	if (is_singular('post')) {
		// get related posts by tags
		$tags = get_the_tags($post->ID);
		if (!empty($tags)) {
			$tag_ids = array();
			foreach($tags as $individual_tag) {
	        	$tag_ids[] = $individual_tag->term_id; 
	    	}
		    $args = array( 
		    	'tag__in' => $tag_ids, 
		        'post__not_in' => array($post->ID), 
		        'posts_per_page' => 3, 
		        'ignore_sticky_posts' => 1, 
		        'orderby' => 'rand',
		        'post_status' => 'publish'
		    );
		    $posts_query = new wp_query( $args ); if( !$posts_query->have_posts() ) {
		    	$posts_query = '';
		    }
		}
		// if there are no posts, get related posts by categories
		if (empty($posts_query)) {
			$categories = get_the_category($post->ID);
			if (!empty($categories)) {
				$category_ids = array(); 
                foreach($categories as $individual_category) 
                    $category_ids[] = $individual_category->term_id; 
                $args = array( 
                	'category__in' => $category_ids, 
                    'post__not_in' => array($post->ID), 
                    'posts_per_page' => 3,  
                    'ignore_sticky_posts' => 1, 
                    'orderby' => 'rand',
		        	'post_status' => 'publish'
                );
                $posts_query = new wp_query( $args ); if( !$posts_query->have_posts() ) {
		    		$posts_query = '';
		    	}
			}
		}
	}
	// still no posts or not singular: show random
	if (empty($posts_query)) {
		$args = array(
            'posts_per_page' => 3,  
            'ignore_sticky_posts' => 1, 
            'orderby' => 'rand',
        	'post_status' => 'publish'
        );
        $posts_query = new wp_query( $args );
	}
	if( $posts_query->have_posts() ) {
		while( $posts_query->have_posts() ) { $posts_query->the_post();
			global $post;
			$posts_html .= '<div class="popup-related-post">';
			if (has_post_thumbnail()) {
				$posts_html .= '<a href="'.get_the_permalink().'" title="'.get_the_title().'" rel="nofollow" class="popup-post-thumbnail">';
				    $posts_html .= get_the_post_thumbnail($post->ID, 'widgetfull', array('title' => ''));
	            $posts_html .= '</a>';
	        }
            if ($options['popup_posts_meta']['category']) {
            	$posts_html .= '<div class="popup-post-categories">';
				$categories = get_the_category();
				$i = 0;
				foreach($categories as $cat) {
					$i++; if ($i > 3) break; // show first 3 categories
					$posts_html .= '<a href="'.get_category_link( $cat->term_id ).'" title="' . esc_attr( sprintf( __( "View all posts in %s", 'wp-subscribe' ), $cat->name ) ) . '" style="color: '.$options['popup_posts_colors']['text_color'].'">'.$cat->cat_name.'</a> ';
				}
            	$posts_html .= '</div>';
            }
            $posts_html .= '<h4><a href="'.get_the_permalink().'" title="'.get_the_title().'" style="color: '.$options['popup_posts_colors']['text_color'].'">'.get_the_title().'</a></h4>';
			if ($options['popup_posts_meta']['excerpt']) {
				$excerpt = get_the_excerpt();
				$strlen = 'strlen';
				if (function_exists('mb_strlen'))
					$strlen = 'mb_strlen';
				if ($strlen($excerpt) > 80) {
					$substr = 'substr';
					if (function_exists('mb_substr'))
						$substr = 'mb_substr';
					$excerpt = $substr($excerpt, 0, 80).'&hellip;';
				}
				$posts_html .= '<p class="popup-post-excerpt" style="color: '.$options['popup_posts_colors']['text_color'].'">'.$excerpt.'</p>';
			}
			$posts_html .= '</div>';
		} // endwhile
	} // endif
	$html .= $posts_html;
	$html .= '</div>';
	$html .= '<div class="clear"></div>';
	$html .= '</div>';
	return $html;
}
function wp_subscribe_get_the_widget( $widget, $instance = '', $args = '' ){
	ob_start();
	the_widget($widget, $instance, $args);
	return ob_get_clean();
}

add_action( 'wp_ajax_validate_subscribe', 'wp_subscribe_ajax_validate_subscribe' );
add_action( 'wp_ajax_nopriv_validate_subscribe', 'wp_subscribe_ajax_validate_subscribe' );
function wp_subscribe_ajax_validate_subscribe() {
	if (!empty($_POST['mailchimp_email'])) {
		$signup = wp_subscribe_mailchimp_subscribe();
        if ($signup['success']) { 
        	echo 'success';
        } else {
            echo $signup['message'] ? $signup['message'] : 'error';
        }
	} elseif (!empty($_POST['getresponse_email'])) {
		$signup = wp_subscribe_getresponse_subscribe();

        if ($signup['success']) { 
        	echo 'success';
        } else {
            echo $signup['message'] ? $signup['message'] : 'error';
        }
	} else {
		echo 'error';
	}
	exit();
}

function wp_subscribe_mailchimp_subscribe() {
	$options = get_option('wp_subscribe_options');
    $ret = array(
        'success' => false,
        'message' => '',
    );

    $email = isset($_POST['mailchimp_email']) ? trim($_POST['mailchimp_email']) : '';
    $name = isset($_POST['mailchimp_name']) ? trim($_POST['mailchimp_name']) : '';
    $nonce = isset($_POST['_wpnonce']) ? trim($_POST['_wpnonce']) : '';
    $mc_api_key = null;
    $mc_list_id = null;
    $error_message = '';
    $widget_id = isset($_POST['widget_id']) ? trim($_POST['widget_id']) : '';

    if (($widget_settings = wp_subscribe_get_widget_settings($widget_id))) {
        $mc_api_key = isset($widget_settings['mailchimp_api_key']) ? $widget_settings['mailchimp_api_key'] : null;
        $mc_list_id = isset($widget_settings['mailchimp_list_id']) ? $widget_settings['mailchimp_list_id'] : null;
        $error_message = isset($widget_settings['error_message']) ? $widget_settings['error_message'] : '';
    	$double_optin = !empty($widget_settings['mailchimp_double_optin']) ? true : false;
    }

    if (isset($_POST['from_popup'])) {
    	$mc_api_key = $options['popup_form_options']['mailchimp_api_key'];
    	$mc_list_id = $options['popup_form_options']['mailchimp_list_id'];
    	$double_optin = !empty($options['popup_form_options']['mailchimp_double_optin']) ? true : false;
    	$widget_settings = true;
    }

    if (isset($_POST['from_single'])) {
    	$mc_api_key = $options['single_post_form_options']['mailchimp_api_key'];
    	$mc_list_id = $options['single_post_form_options']['mailchimp_list_id'];
    	$double_optin = !empty($options['single_post_form_options']['mailchimp_double_optin']) ? true : false;
    	$widget_settings = true;
    }

    if ($email &&
            $widget_settings &&
            $mc_api_key != null &&
            $mc_list_id != null &&
            wp_verify_nonce($nonce, 'wp-subscribe-mailchimp')) {

        try {
            $list = new Mailchimp_Lists(new Mailchimp($mc_api_key));
            $merge_vars = null;
            if ($name) {
            	$fname = $name;
            	$lname = '';
            	if ($space_pos = strpos($name, ' ')) {
            		$fname = substr($name, 0, $space_pos);
            		$lname = substr($name, $space_pos);
            	}
            	$merge_vars = array('FNAME' => $fname, 'LNAME' => $lname);
            }
            $resp = $list->subscribe($mc_list_id, array('email' => $email), $merge_vars, 'html', (bool) $double_optin, true);

            if ($resp) {
                $ret['success'] = true;
            }
        } catch (Exception $ex) {
            $ret['message'] = $ex->getMessage();
        }
    }
    return $ret;
}

function wp_subscribe_getresponse_subscribe() {
	$options = get_option('wp_subscribe_options');
    $ret = array(
        'success' => false,
        'message' => '',
    );

    $email = isset($_POST['getresponse_email']) ? trim($_POST['getresponse_email']) : '';
    $name = isset($_POST['getresponse_name']) ? trim($_POST['getresponse_name']) : '';
    $nonce = isset($_POST['_wpnonce']) ? trim($_POST['_wpnonce']) : '';
    $gr_api_key = null;
    $gr_list_id = null;
    $error_message = '';
    $widget_id = isset($_POST['widget_id']) ? trim($_POST['widget_id']) : '';

    if (($widget_settings = wp_subscribe_get_widget_settings($widget_id))) {
        $gr_api_key = isset($widget_settings['getresponse_api_key']) ? $widget_settings['getresponse_api_key'] : null;
        $gr_list_id = isset($widget_settings['getresponse_list_id']) ? $widget_settings['getresponse_list_id'] : null;
        $error_message = isset($widget_settings['error_message']) ? $widget_settings['error_message'] : '';
    }

    if (isset($_POST['from_popup'])) {
    	$gr_api_key = $options['popup_form_options']['getresponse_api_key'];
    	$gr_list_id = $options['popup_form_options']['getresponse_list_id'];
    	$widget_settings = true;
    }

    if (isset($_POST['from_single'])) {
    	$gr_api_key = $options['single_post_form_options']['getresponse_api_key'];
    	$gr_list_id = $options['single_post_form_options']['getresponse_list_id'];
    	$widget_settings = true;
    }

	if ($email &&
            $widget_settings &&
            $gr_api_key != null &&
            $gr_list_id != null &&
            wp_verify_nonce($nonce, 'wp-subscribe-getresponse')) {

	    $api = new GetResponse($gr_api_key);
	    $campaignName = $gr_list_id;
	    $subscriberName = $name;
	    $subscriberEmail = $email;
	 
	    $result =  $api->getCampaigns('EQUALS', $campaignName);
	    $campaigns = array_keys((array) $result);
	    $campaignId = array_pop($campaigns);
	 	
	 	$response = $api->addContact($campaignId, $subscriberName, $subscriberEmail);

		if (is_object($response) && empty($response->code)) {
			$ret['success'] = true;
		} else {
			if (is_object($response) && !empty($response->message)) {
				$ret['message'] = $response->message;
			}
		}

	    return $ret;
	}
}


function wp_subscribe_get_widget_settings($widget_id) {
    global $wp_registered_widgets;
    $ret = array();

    if (isset($wp_registered_widgets) && isset($wp_registered_widgets[$widget_id])) {
        $widget = $wp_registered_widgets[$widget_id];
        $option_data = get_option($widget['callback'][0]->option_name);

        if (isset($option_data[$widget['params'][0]['number']])) {
            $ret = $option_data[$widget['params'][0]['number']];
        }
    }

    return $ret;
}

// Add meta box to single editor
function wp_subscribe_metabox_insert() {
    $screens = get_post_types( array('public' => true) );
    foreach ($screens as $screen) {
        add_meta_box(
            'wp_subscribe_metabox',                  // id
            __('WP Subscribe Pro', 'wp-subscribe'),    // title
            'wp_subscribe_metabox_content',            // callback
            $screen,                                // post_type
            'side',                                 // context (normal, advanced, side)
            'default'                               // priority (high, core, default, low)
                                                    // callback args ($post passed by default)
        );
    }
}
add_action('add_meta_boxes', 'wp_subscribe_metabox_insert');

function wp_subscribe_metabox_content($post) {
    
    // Add an nonce field so we can check for it later.
    wp_nonce_field('wp_subscribe_metabox', 'wp_subscribe_metabox');
    
    /*
    * Use get_post_meta() to retrieve an existing value
    * from the database and use the value for the form.
    */
    $disable_popup = get_post_meta( $post->ID, '_wp_subscribe_disable_popup', true );
    $disable_single = get_post_meta( $post->ID, '_wp_subscribe_disable_single', true );

    $post_type = get_post_type( $post );
    ?>
    <p>
    	<label for="wp_subscribe_disable_popup">
    		<input type="hidden" name="wp_subscribe_disable_popup" value="0" />
    		<input type="checkbox" name="wp_subscribe_disable_popup" id="wp_subscribe_disable_popup" <?php checked($disable_popup); ?> value="1" />
    		<?php printf(__('Disable popup for this %s', 'wp-subscribe'), $post_type); ?>
    	</label><br />
    <?php if ($post_type == 'post') { ?>
    	<label for="wp_subscribe_disable_single">
    		<input type="hidden" name="wp_subscribe_disable_single" value="0" />
    		<input type="checkbox" name="wp_subscribe_disable_single" id="wp_subscribe_disable_single" <?php checked($disable_single); ?> value="1" />
    		<?php _e('Disable subscribe form before/after content', 'wp-subscribe'); ?>
    	</label><br />
    <?php } ?> 
    </p>
    <?php
}

function wp_subscribe_metabox_save( $post_id ) {
    
    // Check if our nonce is set.
    if ( ! isset( $_POST['wp_subscribe_metabox'] ) )
    return $post_id;
    
    $nonce = $_POST['wp_subscribe_metabox'];
    
    // Verify that the nonce is valid.
    if ( ! wp_verify_nonce( $nonce, 'wp_subscribe_metabox' ) )
      return $post_id;
    
    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
      return $post_id;
    
    // Check the user's permissions.
    if ( 'page' == $_POST['post_type'] ) {
    
    if ( ! current_user_can( 'edit_page', $post_id ) )
        return $post_id;
    
    } else {
    
    if ( ! current_user_can( 'edit_post', $post_id ) )
        return $post_id;
    }
    
    /* OK, its safe for us to save the data now. */
    
    if (isset($_POST['wp_subscribe_disable_popup'])) {
    	$val = (bool) $_POST['wp_subscribe_disable_popup'];
	    update_post_meta( $post_id, '_wp_subscribe_disable_popup', $val );
    }
    if (isset($_POST['wp_subscribe_disable_single'])) {
    	$val = (bool) $_POST['wp_subscribe_disable_single'];
	    update_post_meta( $post_id, '_wp_subscribe_disable_single', $val );
    }
}
add_action( 'save_post', 'wp_subscribe_metabox_save' );

// Shortcode support
add_shortcode( 'wp-subscribe', 'wp_subscribe_shortcode' );
function wp_subscribe_shortcode() {
	$options = get_option('wp_subscribe_options');
	if ($options['enable_single_post_form']) {
		$widget = wp_subscribe_get_the_widget( 'wp_subscribe', array(
	            'service' => $options['single_post_form_options']['service'],
            	'include_name_field' => $options['single_post_form_options']['include_name_field'],
	            'feedburner_id' => $options['single_post_form_options']['feedburner_id'],
	            'mailchimp_api_key' => $options['single_post_form_options']['mailchimp_api_key'],
	            'mailchimp_list_id' => $options['single_post_form_options']['mailchimp_list_id'],
	            'aweber_list_id' => $options['single_post_form_options']['aweber_list_id'],

	            'title' => $options['single_post_form_labels']['title'],
	            'text' => $options['single_post_form_labels']['text'],
            	'name_placeholder' => $options['single_post_form_labels']['name_placeholder'],
	            'email_placeholder' => $options['single_post_form_labels']['email_placeholder'],
	            'button_text' => $options['single_post_form_labels']['button_text'],
	            'success_message' => $options['single_post_form_labels']['success_message'],
	            'error_message' => $options['single_post_form_labels']['error_message'],
	            'footer_text' => $options['single_post_form_labels']['footer_text'],

	            'background_color' => $options['single_post_form_colors']['background_color'],
	            'title_color' => $options['single_post_form_colors']['title_color'],
	            'text_color' => $options['single_post_form_colors']['text_color'],
	            'field_text_color' => $options['single_post_form_colors']['field_text_color'],
	            'field_background_color' => $options['single_post_form_colors']['field_background_color'],
	            'button_text_color' => $options['single_post_form_colors']['button_text_color'],
	            'button_background_color' => $options['single_post_form_colors']['button_background_color'],
	            'footer_text_color' => $options['single_post_form_colors']['footer_text_color'],
	            'from_single' => '1'
	        ), array('before_widget' => '<div class="wp-subscribe-single wp-subscribe-shortcode">') );
	} else {
		$widget = '';
	}
	return $widget;
}
