<?php

// Add function to widgets_init that'll load our widget.
add_action( 'widgets_init', 'wp_subscribe_register_widget' );

// Register widget.
function wp_subscribe_register_widget() {
    register_widget( 'wp_subscribe' );
}

// Widget class.
class wp_subscribe extends WP_Widget {


/*-----------------------------------------------------------------------------------*/
/*  Widget Setup
/*-----------------------------------------------------------------------------------*/
    
    function __construct() {
        
        add_action('wp_enqueue_scripts', array(&$this, 'register_scripts'));
        add_action('admin_enqueue_scripts', array(&$this, 'register_admin_scripts'));
        add_action('customize_controls_enqueue_scripts', array(&$this, 'register_admin_scripts'));

        /* Widget settings. */
        $widget_ops = array( 'classname' => 'wp_subscribe', 'description' => __('Displays subscription form, supports FeedBurner, MailChimp, GetResponse & AWeber.', 'wp-subscribe') );

        /* Widget control settings. */
        $control_ops = array( 'id_base' => 'wp_subscribe' );

        /* Create the widget. */
        parent::__construct( 'wp_subscribe', __('WP Subscribe Pro Widget', 'wp-subscribe'), $widget_ops, $control_ops );
    }

/*-----------------------------------------------------------------------------------*/
/*  Enqueue assets
/*-----------------------------------------------------------------------------------*/
    function register_scripts() { 
        // JS    
        wp_register_script('wp-subscribe', plugins_url('js/wp-subscribe-form.js', __FILE__), array('jquery'));     
        wp_localize_script( 'wp-subscribe', 'wp_subscribe', array('ajaxurl' => admin_url('admin-ajax.php')) );
        // CSS     
        wp_register_style('wp-subscribe', plugins_url('css/wp-subscribe-form.css', __FILE__));
    }  
    function register_admin_scripts($hook) {
        $screen = get_current_screen();
        $screen_id = $screen->id;
        $current_filter = current_filter();
        if ( 'widgets' === $screen_id || 'customize_controls_enqueue_scripts' === $current_filter ) {
            wp_enqueue_script('wp-color-picker');
            wp_enqueue_style('wp-color-picker');
            wp_register_script('wp-subscribe-admin', plugins_url('js/wp-subscribe-admin.js', __FILE__), array('jquery'));  
            wp_enqueue_script('wp-subscribe-admin');
            wp_enqueue_style( 'wp-subscribe-options', plugins_url('css/wp-subscribe-options.css', __FILE__) );
        }
    }
    

/*-----------------------------------------------------------------------------------*/
/*  Display Widget
/*-----------------------------------------------------------------------------------*/
    
    function widget( $args, $instance ) {
        global $wp_subscribe_forms;
        $wp_subscribe_forms++;
        extract( $args );
        $defaults = $this->get_defaults();
        $instance = wp_parse_args( (array) $instance, $defaults ); 
        wp_enqueue_style( 'wp-subscribe' );
        wp_enqueue_script( 'wp-subscribe' );

        /* Before widget (defined by themes). */
        echo $before_widget;
       
        global $wp;
        $current_url = add_query_arg( $wp->query_string, '', home_url( $wp->request ) );

        // has-name-field class
        $name_class = ' no-name-field';
        if (!empty($instance['include_name_field'])) $name_class = ' has-name-field';

        /* Display Widget */
        ?>
            
            <div class="wp-subscribe<?php echo $name_class; ?>" id="wp-subscribe" style="background: <?php echo $instance['background_color']; ?>;">
                <h4 class="title" style="color: <?php echo $instance['title_color']; ?>;"><?php echo $instance['title'];?></h4>
                <p class="text" style="color: <?php echo $instance['text_color']; ?>;"><?php echo $instance['text'];?></p>
                
                <?php if ($instance['service'] == 'feedburner') { ?>
                
                    <form action="http://feedburner.google.com/fb/a/mailverify" method="post" target="popupwindow" onsubmit="window.open('http://feedburner.google.com/fb/a/mailverify?uri=<?php echo $instance['feedburner_id']; ?>', 'popupwindow', 'scrollbars=yes,width=550,height=520');return true" _lpchecked="1" class="wp-subscribe-form wp-subscribe-feedburner" id="wp-subscribe-form-<?php echo $wp_subscribe_forms; ?>">
                        <input class="email-field" type="text" value="" placeholder="<?php echo $instance['email_placeholder']; ?>" name="email" style="background: <?php echo $instance['field_background_color']; ?>; color: <?php echo $instance['field_text_color']; ?>">
                        <input type="hidden" value="<?php echo $instance['feedburner_id']; ?>" name="uri"><input type="hidden" name="loc" value="en_US">
                        <input class="submit" name="submit" type="submit" value="<?php echo $instance['button_text']; ?>" style="background: <?php echo $instance['button_background_color']; ?>; color: <?php echo $instance['button_text_color']; ?>">
                    </form>
                
                <?php } elseif ($instance['service'] == 'mailchimp') { ?>
                    
                    <form action="<?php echo add_query_arg('mailchimp_signup', '1', $current_url); ?>" method="post" class="wp-subscribe-form wp-subscribe-mailchimp" id="wp-subscribe-form-<?php echo $wp_subscribe_forms; ?>">
                        <?php if (!empty($instance['include_name_field'])) { ?>
                        <input class="name-field" type="text" value="" placeholder="<?php echo $instance['name_placeholder']; ?>" name="mailchimp_name" style="background: <?php echo $instance['field_background_color']; ?>; color: <?php echo $instance['field_text_color']; ?>">
                        <?php } ?>
                        <input class="email-field" type="text" value="" placeholder="<?php echo $instance['email_placeholder']; ?>" name="mailchimp_email" style="background: <?php echo $instance['field_background_color']; ?>; color: <?php echo $instance['field_text_color']; ?>">
                        <input class="submit" name="submit" type="submit" value="<?php echo $instance['button_text']; ?>" style="background: <?php echo $instance['button_background_color']; ?>; color: <?php echo $instance['button_text_color']; ?>">
                        <input type="hidden" name="widget_id" value="<?php echo $this->id ?>" />
                        <?php if (!empty($instance['from_popup'])) { ?>
                            <input type="hidden" name="from_popup" value="1" />
                        <?php } elseif (!empty($instance['from_single'])) { ?>
                            <input type="hidden" name="from_single" value="1" />
                        <?php } ?>
                        <?php wp_nonce_field('wp-subscribe-mailchimp'); ?>
                    </form>

                            <p class="thanks" style="color: <?php echo $instance['text_color']; ?>; display: none;"><?php echo $instance['success_message']; ?></p>
                        
                            <p class="error" style="color: <?php echo $instance['text_color']; ?>; display: none;"><?php echo $instance['error_message']; ?></p>
                        <?php

                    } elseif ($instance['service'] == 'aweber') { ?>
                
                        <form method="post" action="http://www.aweber.com/scripts/addlead.pl" target="_blank" class="wp-subscribe-form wp-subscribe-aweber" id="wp-subscribe-form-<?php echo $wp_subscribe_forms; ?>">
                            <div style="display: none;">
                                <input type="hidden" name="listname" value="<?php echo $instance['aweber_list_id']; ?>" />
                                <!-- <input type="hidden" name="redirect" value="<?php echo add_query_arg('aweber_signedup', '1', $current_url); ?>" />
                                <input type="hidden" name="meta_redirect_onlist" value="<?php echo add_query_arg('aweber_signedup', '-1', $current_url); ?>" /> -->
                            </div>
                            <?php if (!empty($instance['include_name_field'])) { ?>
                            <input class="name-field" type="text" value="" placeholder="<?php echo $instance['name_placeholder']; ?>" name="name" style="background: <?php echo $instance['field_background_color']; ?>; color: <?php echo $instance['field_text_color']; ?>">
                            <?php } ?>
                            <input class="email-field" type="text" value="" placeholder="<?php echo $instance['email_placeholder']; ?>" name="email" style="background: <?php echo $instance['field_background_color']; ?>; color: <?php echo $instance['field_text_color']; ?>">
                            <input class="submit" name="submit" type="submit" value="<?php echo $instance['button_text']; ?>" style="background: <?php echo $instance['button_background_color']; ?>; color: <?php echo $instance['button_text_color']; ?>">
                        </form>
                        <p class="thanks" style="color: <?php echo $instance['text_color']; ?>; display: none;"><?php echo $instance['success_message']; ?></p>
                
                <?php } elseif ($instance['service'] == 'getresponse') { ?>
                    
                    <form action="<?php echo add_query_arg('getresponse_signup', '1', $current_url); ?>" method="post" class="wp-subscribe-form wp-subscribe-getresponse" id="wp-subscribe-form-<?php echo $wp_subscribe_forms; ?>">
                        <?php if (!empty($instance['include_name_field'])) { ?>
                        <input class="name-field" type="text" value="" placeholder="<?php echo $instance['name_placeholder']; ?>" name="getresponse_name" style="background: <?php echo $instance['field_background_color']; ?>; color: <?php echo $instance['field_text_color']; ?>">
                        <?php } ?>
                        <input class="email-field" type="text" value="" placeholder="<?php echo $instance['email_placeholder']; ?>" name="getresponse_email" style="background: <?php echo $instance['field_background_color']; ?>; color: <?php echo $instance['field_text_color']; ?>">
                        <input class="submit" name="submit" type="submit" value="<?php echo $instance['button_text']; ?>" style="background: <?php echo $instance['button_background_color']; ?>; color: <?php echo $instance['button_text_color']; ?>">
                        <input type="hidden" name="widget_id" value="<?php echo $this->id ?>" />
                        <?php if (!empty($instance['from_popup'])) { ?>
                            <input type="hidden" name="from_popup" value="1" />
                        <?php } elseif (!empty($instance['from_single'])) { ?>
                            <input type="hidden" name="from_single" value="1" />
                        <?php } ?>
                        <?php wp_nonce_field('wp-subscribe-getresponse'); ?>
                    </form>

                            <p class="thanks" style="color: <?php echo $instance['text_color']; ?>; display: none;"><?php echo $instance['success_message']; ?></p>
                        
                            <p class="error" style="color: <?php echo $instance['text_color']; ?>; display: none;"><?php echo $instance['error_message']; ?></p>
                        <?php

                    } ?>
                <div class="clear"></div>
                
                <p class="footer-text" style="color: <?php echo $instance['footer_text_color']; ?>;"><?php echo $instance['footer_text'];?></p>
            </div><!--subscribe_widget-->
        
        <?php

        /* After widget (defined by themes). */
        echo $after_widget;
    }


/*-----------------------------------------------------------------------------------*/
/*  Update Widget
/*-----------------------------------------------------------------------------------*/
    
    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;

        $instance = array_merge($instance, $new_instance);

        // Feedburner ID -- make sure the user didn't insert full url
        if (strpos($instance['feedburner_id'], 'http') === 0)
            $instance['feedburner_id'] = substr( $instance['feedburner_id'], strrpos( $instance['feedburner_id'], '/' )+1 );

        return $instance;
    }
    

/*-----------------------------------------------------------------------------------*/
/*  Widget Settings
/*-----------------------------------------------------------------------------------*/
     
    function form( $instance ) {
        $defaults = $this->get_defaults();
        $instance = wp_parse_args( (array) $instance, $defaults );
        ?>
        <div class="wp_subscribe_options_form">
        
        <!-- Hidden title field to prevent WP picking up Title Color field as widget title -->
        <input type="hidden" value="" id="<?php echo $this->get_field_id('title') ?>" name="<?php echo $this->get_field_name('title') ?>">
        
        <?php $this->output_select_field('service', __('Service:', 'wp-subscribe'), array('feedburner' => 'FeedBurner', 'mailchimp' => 'MailChimp', 'aweber' => 'AWeber', 'getresponse' => 'GetResponse'), $instance['service']); ?>
        
        <div class="clear"></div>
        
        <div class="wp_subscribe_account_details">
        <div class="wp_subscribe_account_details_feedburner" style="display: none;">
            <?php $this->output_text_field('feedburner_id', __('Feedburner ID', 'wp-subscribe'), $instance['feedburner_id']); ?>
        </div><!-- .wp_subscribe_account_details_mailchimp -->

        <div class="wp_subscribe_account_details_mailchimp" style="display: none;">
            <?php $this->output_text_field('mailchimp_api_key', __('Mailchimp API key', 'wp-subscribe'), $instance['mailchimp_api_key']); ?>
            <a href="http://kb.mailchimp.com/accounts/management/about-api-keys#Finding-or-generating-your-API-key" target="_blank"><?php _e('Find your API key', 'wp-subscribe'); ?></a>
            <?php $this->output_text_field('mailchimp_list_id', __('Mailchimp List ID', 'wp-subscribe'), $instance['mailchimp_list_id']); ?>
            <a href="http://kb.mailchimp.com/lists/managing-subscribers/find-your-list-id" target="_blank"><?php _e('Find your list ID', 'wp-subscribe'); ?></a>
            <p class="wp_subscribe_mailchimp_double_optin"><label for="<?php echo $this->get_field_id('mailchimp_double_optin'); ?>">
                <input type="hidden" name="<?php echo $this->get_field_name('mailchimp_double_optin'); ?>" value="0">
                <input id="<?php echo $this->get_field_id('mailchimp_double_optin'); ?>" type="checkbox" name="<?php echo $this->get_field_name('mailchimp_double_optin'); ?>" value="1" <?php checked($instance['mailchimp_double_optin']); ?>>
                <?php _e( 'Send double opt-in notification', 'wp-subscribe' ); ?>
            </label></p>
        </div><!-- .wp_subscribe_account_details_mailchimp -->

        <div class="wp_subscribe_account_details_aweber" style="display: none;">
            <?php $this->output_text_field('aweber_list_id', __('AWeber List ID', 'wp-subscribe'), $instance['aweber_list_id']); ?>
            <a href="https://help.aweber.com/entries/61177326-What-Is-The-Unique-List-ID-" target="_blank"><?php _e('Find your list ID', 'wp-subscribe'); ?></a>
        </div><!-- .wp_subscribe_account_details_aweber -->

        <div class="wp_subscribe_account_details_getresponse" style="display: none;">
            <?php $this->output_text_field('getresponse_api_key', __('GetResponse API key', 'wp-subscribe'), $instance['getresponse_api_key']); ?>
            <a href="http://support.getresponse.com/faq/where-i-find-api-key" target="_blank"><?php _e('Find your API key', 'wp-subscribe'); ?></a>
            <?php $this->output_text_field('getresponse_list_id', __('GetResponse campaign name', 'wp-subscribe'), $instance['getresponse_list_id']); ?>
            <a href="http://support.getresponse.com/faq/can-i-change-the-name-of-a-campaign" target="_blank"><?php _e('Find the campaign name', 'wp-subscribe'); ?></a>
        </div><!-- .wp_subscribe_account_details_getresponse -->
        </div><!-- .wp_subscribe_account_details -->

        <p class="wp_subscribe_include_name"><label for="<?php echo $this->get_field_id('include_name_field'); ?>">
            <input type="hidden" name="<?php echo $this->get_field_name('include_name_field'); ?>" value="0">
            <input id="<?php echo $this->get_field_id('include_name_field'); ?>" type="checkbox" class="include-name-field" name="<?php echo $this->get_field_name('include_name_field'); ?>" value="1" <?php checked($instance['include_name_field']); ?>>
            <?php _e( 'Include <strong>Name</strong> field.', 'wp-subscribe' ); ?>
        </label></p>

        <h4 class="wp_subscribe_labels_header"><a class="wp-subscribe-toggle" href="#" rel="wp_subscribe_labels"><?php _e('Labels', 'wp-subscribe'); ?></a></h4>
        <div class="wp_subscribe_labels" style="display: none;">
        <?php 
        $this->output_textarea_field('title', __('Title', 'wp-subscribe'), $instance['title']);
        $this->output_text_field('text', __('Text', 'wp-subscribe'), $instance['text']);
        $this->output_text_field('name_placeholder', __('Name Placeholder', 'wp-subscribe'), $instance['name_placeholder']);
        $this->output_text_field('email_placeholder', __('Email Placeholder', 'wp-subscribe'), $instance['email_placeholder']);
        $this->output_text_field('button_text', __('Button Text', 'wp-subscribe'), $instance['button_text']);
        $this->output_text_field('success_message', __('Success Message', 'wp-subscribe'), $instance['success_message']);
        $this->output_text_field('error_message', __('Error Message', 'wp-subscribe'), $instance['error_message']);
        $this->output_text_field('footer_text', __('Footer Text', 'wp-subscribe'), $instance['footer_text']);
        ?>
        </div><!-- .wp_subscribe_labels -->

        <h4 class="wp_subscribe_colors_header"><a class="wp-subscribe-toggle" href="#" rel="wp_subscribe_colors"><?php _e('Colors', 'wp-subscribe-widget'); ?></a></h4>
        <div class="wp_subscribe_colors" style="display: none;">

        <?php 
        
        $this->wp_subscribe_widget_color_palettes_select();

        $this->output_color_field('background_color', __('Background', 'wp-subscribe-widget'), $instance['background_color']);
        $this->output_color_field('title_color', __('Title', 'wp-subscribe-widget'), $instance['title_color']);
        $this->output_color_field('text_color', __('Text', 'wp-subscribe-widget'), $instance['text_color']);
        $this->output_color_field('field_text_color', __('Field Text', 'wp-subscribe-widget'), $instance['field_text_color']);
        $this->output_color_field('field_background_color', __('Field Background', 'wp-subscribe-widget'), $instance['field_background_color']);
        $this->output_color_field('button_text_color', __('Button Text', 'wp-subscribe-widget'), $instance['button_text_color']);
        $this->output_color_field('button_background_color', __('Button Background', 'wp-subscribe-widget'), $instance['button_background_color']);
        $this->output_color_field('footer_text_color', __('Footer Text', 'wp-subscribe-widget'), $instance['footer_text_color']);
        
        ?>
        </div><!-- .wp_subscribe_colors -->
        <style type="text/css">
            .wp_subscribe_options_form label {
                vertical-align: top;
            }
            .wp_subscribe_colors, .wp_subscribe_labels {
                
            }
            .wp_subscribe_options_form .wp-picker-container {
                position: absolute;
                right: 0;
            }
            .wp_subscribe_colors > div {
                position: relative;
                margin: 20px 0;
            }
            .wp_subscribe_colors label {
                display: inline-block;
                margin-top: 2px;
            }
            .wp_subscribe_options_form .wp-picker-container > a {
                margin-right: 0;
            }
        </style>
        </div><!-- .wp_subscribe_options_form -->

    <?php
    }


    public function output_text_field($setting_name, $setting_label, $setting_value) {
        ?>

        <p class="wp-subscribe-<?php echo $setting_name; ?>-field">
            <label for="<?php echo $this->get_field_id($setting_name) ?>">
                <?php echo $setting_label ?>
            </label>

            <input class="widefat" 
                   id="<?php echo $this->get_field_id($setting_name) ?>" 
                   name="<?php echo $this->get_field_name($setting_name) ?>" 
                   type="text" 
                   value="<?php echo esc_attr($setting_value) ?>" />
        </p>

        <?php
    }

    public function output_textarea_field($setting_name, $setting_label, $setting_value) {
        ?>

        <p class="wp-subscribe-<?php echo $setting_name; ?>-field">
            <label for="<?php echo $this->get_field_id($setting_name) ?>">
                <?php echo $setting_label ?>
            </label>

            <textarea class="widefat" id="<?php echo $this->get_field_id($setting_name) ?>" name="<?php echo $this->get_field_name($setting_name) ?>"><?php echo esc_attr($setting_value); ?></textarea>
        </p>

        <?php
    }

    public function output_select_field($setting_name, $setting_label, $setting_values, $selected) {
        ?>

        <p class="wp-subscribe-<?php echo $setting_name; ?>-field">
            <label for="<?php echo $this->get_field_id($setting_name) ?>">
                <?php echo $setting_label ?>
            </label>

            <select class="widefat" 
                    id="<?php echo $this->get_field_id($setting_name) ?>" 
                    name="<?php echo $this->get_field_name($setting_name) ?>">

                <?php foreach ($setting_values as $value => $label) : ?>

                    <option value="<?php echo $value; ?>" <?php selected( $selected, $value ); ?>>
                        <?php echo $label; ?>
                    </option>

                <?php endforeach ?>
            </select>
        </p>

        <?php
    }

    public function output_color_field($setting_name, $setting_label, $setting_value) {
        ?>

        <div class="wp-subscribe-<?php echo $setting_name; ?>-field">
            <label for="<?php echo $this->get_field_id($setting_name) ?>">
                <?php echo $setting_label ?>
            </label>

            <input class="widefat wp-subscribe-color-select" 
                   id="<?php echo $this->get_field_id($setting_name) ?>" 
                   name="<?php echo $this->get_field_name($setting_name) ?>" 
                   type="text" 
                   value="<?php echo $setting_value ?>" />
            
        </div>
        <?php
    }

    public function get_defaults() {
        return apply_filters('wp_subscribe_form_defaults', array(
            'service' => 'feedburner',
            'feedburner_id' => '',
            'mailchimp_api_key' => '',
            'mailchimp_list_id' => '',
            'mailchimp_double_optin' => 0,
            'aweber_list_id' => '',
            'getresponse_api_key' => '',
            'getresponse_list_id' => '',

            'include_name_field' => false,

            'title' => __('Get more stuff', 'wp-subscribe'),
            'text' => __('Subscribe to our mailing list and get interesting stuff and updates to your email inbox.', 'wp-subscribe'),
            'email_placeholder' => __('Enter your email here', 'wp-subscribe'),
            'name_placeholder' => __('Enter your name here', 'wp-subscribe'),
            'button_text' => __('Sign Up Now', 'wp-subscribe'),
            'success_message' => __('Thank you for subscribing.', 'wp-subscribe'),
            'error_message' => __('Something went wrong.', 'wp-subscribe'),
            'footer_text' => __('we respect your privacy and take protecting it seriously', 'wp-subscribe'),

            'background_color' => '#f47555',
            'title_color' => '#FFFFFF',
            'text_color' => '#FFFFFF',
            'field_text_color' => '#FFFFFF',
            'field_background_color' => '#d56144',
            'button_text_color' => '#f47555',
            'button_background_color' => '#FFFFFF',
            'footer_text_color' => '#FFFFFF'
        ));
    }
    public function get_current_sidebar($widget_id) {
     
        $active_sidebars = wp_get_sidebars_widgets();
        $current_sidebar = false;
         
        foreach($active_sidebars as $key => $sidebar)
            if(array_search($widget_id, $sidebar))
                $current_sidebar = $key;    
         
        return $current_sidebar;
    }
    public function wp_subscribe_widget_color_palettes_select() {
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
                    <input type="hidden" class="wps-palette-color" name="<?php echo $this->get_field_id($field.'_color'); ?>" value="<?php echo $color; ?>" />
                    <?php } ?>
                    <a href="#" class="button button-secondary wps-load-palette"><?php _e('Load colors', 'wp-subscribe'); ?></a>
                    </div>
                    <?php } ?>
                </div>
            </div>
            <?php 
        }
    }

}