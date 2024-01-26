<?php
/*
Plugin Name: WPC Frequently Bought Together for WooCommerce
Plugin URI: https://wpclever.net/
Description: Increase your sales with personalized product recommendations.
Version: 5.3.0
Author: WPClever
Author URI: https://wpclever.net
Text Domain: woo-bought-together
Domain Path: /languages/
Requires at least: 4.0
Tested up to: 6.2
WC requires at least: 3.0
WC tested up to: 7.5
*/

use Automattic\WooCommerce\Utilities\FeaturesUtil;

defined( 'ABSPATH' ) || exit;

! defined( 'WOOBT_VERSION' ) && define( 'WOOBT_VERSION', '5.3.0' );
! defined( 'WOOBT_FILE' ) && define( 'WOOBT_FILE', __FILE__ );
! defined( 'WOOBT_URI' ) && define( 'WOOBT_URI', plugin_dir_url( __FILE__ ) );
! defined( 'WOOBT_DIR' ) && define( 'WOOBT_DIR', plugin_dir_path( __FILE__ ) );
! defined( 'WOOBT_SUPPORT' ) && define( 'WOOBT_SUPPORT', 'https://wpclever.net/support??utm_source=support&utm_medium=woobt&utm_campaign=wporg' );
! defined( 'WOOBT_REVIEWS' ) && define( 'WOOBT_REVIEWS', 'https://wordpress.org/support/plugin/woo-bought-together/reviews/?filter=5' );
! defined( 'WOOBT_CHANGELOG' ) && define( 'WOOBT_CHANGELOG', 'https://wordpress.org/plugins/woo-bought-together/#developers' );
! defined( 'WOOBT_DISCUSSION' ) && define( 'WOOBT_DISCUSSION', 'https://wordpress.org/support/plugin/woo-bought-together' );
! defined( 'WPC_URI' ) && define( 'WPC_URI', WOOBT_URI );

include 'includes/wpc-dashboard.php';
include 'includes/wpc-menu.php';
include 'includes/wpc-kit.php';

if ( ! function_exists( 'woobt_init' ) ) {
	add_action( 'plugins_loaded', 'woobt_init', 11 );

	function woobt_init() {
		// Load textdomain
		load_plugin_textdomain( 'woo-bought-together', false, basename( __DIR__ ) . '/languages/' );

		if ( ! function_exists( 'WC' ) || ! version_compare( WC()->version, '3.0', '>=' ) ) {
			add_action( 'admin_notices', 'woobt_notice_wc' );

			return;
		}

		if ( ! class_exists( 'WPCleverWoobt' ) && class_exists( 'WC_Product' ) ) {
			class WPCleverWoobt {
				protected static $instance = null;
				protected static $image_size = 'woocommerce_thumbnail';
				protected static $localization = [];
				protected static $positions = [];
				protected static $settings = [];
				protected static $types = [ 'simple', 'woosb', 'bundle', 'subscription' ];

				public static function instance() {
					if ( is_null( self::$instance ) ) {
						self::$instance = new self();
					}

					return self::$instance;
				}

				function __construct() {
					// Get settings & localization
					self::$settings     = (array) get_option( 'woobt_settings', [] );
					self::$localization = (array) get_option( 'woobt_localization', [] );

					// Init
					add_action( 'init', [ $this, 'init' ] );

					// Add image to variation
					add_filter( 'woocommerce_available_variation', [ $this, 'available_variation' ], 10, 3 );
					add_filter( 'woovr_data_attributes', [ $this, 'woovr_data_attributes' ], 10, 2 );

					// Settings
					add_action( 'admin_init', [ $this, 'register_settings' ] );
					add_action( 'admin_menu', [ $this, 'admin_menu' ] );

					// Enqueue frontend scripts
					add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );

					// Enqueue backend scripts
					add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );

					// Backend AJAX
					add_action( 'wp_ajax_woobt_update_search_settings', [ $this, 'ajax_update_search_settings' ] );
					add_action( 'wp_ajax_woobt_get_search_results', [ $this, 'ajax_get_search_results' ] );
					add_action( 'wp_ajax_woobt_import_export', [ $this, 'ajax_import_export' ] );
					add_action( 'wp_ajax_woobt_import_export_save', [ $this, 'ajax_import_export_save' ] );

					// Shortcode
					add_shortcode( 'woobt', [ $this, 'shortcode' ] );
					add_shortcode( 'woobt_items', [ $this, 'shortcode' ] );

					// Product data tabs
					add_filter( 'woocommerce_product_data_tabs', [ $this, 'product_data_tabs' ] );

					// Product data panels
					add_action( 'woocommerce_product_data_panels', [ $this, 'product_data_panels' ] );
					add_action( 'woocommerce_process_product_meta', [ $this, 'process_product_meta' ] );

					// Product price
					add_filter( 'woocommerce_product_price_class', [ $this, 'product_price_class' ] );

					// Add to cart button & form
					add_action( 'woocommerce_before_add_to_cart_button', [ $this, 'add_to_cart_button' ] );

					// Show items in standard position
					add_action( 'woocommerce_before_add_to_cart_form', [ $this, 'show_items_before_atc' ] );
					add_action( 'woocommerce_after_add_to_cart_form', [ $this, 'show_items_after_atc' ] );
					add_action( 'woocommerce_single_product_summary', [ $this, 'show_items_below_title' ], 6 );
					add_action( 'woocommerce_single_product_summary', [ $this, 'show_items_below_price' ], 11 );
					add_action( 'woocommerce_single_product_summary', [ $this, 'show_items_below_excerpt' ], 21 );
					add_action( 'woocommerce_single_product_summary', [ $this, 'show_items_below_meta' ], 41 );
					add_action( 'woocommerce_after_single_product_summary', [ $this, 'show_items_below_summary' ], 9 );

					// Show items in custom position
					add_action( 'woobt_custom_position', [ $this, 'show_items_position' ] );

					// Add to cart
					add_filter( 'woocommerce_add_to_cart_sold_individually_found_in_cart', [
						$this,
						'found_in_cart'
					], 10, 2 );
					add_filter( 'woocommerce_add_to_cart_validation', [ $this, 'add_to_cart_validation' ], 10, 2 );
					add_action( 'woocommerce_add_to_cart', [ $this, 'add_to_cart' ], 10, 6 );
					add_filter( 'woocommerce_add_cart_item_data', [ $this, 'add_cart_item_data' ], 10, 2 );
					add_filter( 'woocommerce_get_cart_item_from_session', [
						$this,
						'get_cart_item_from_session'
					], 10, 2 );

					// Add all to cart
					add_action( 'wp_ajax_woobt_add_all_to_cart', [ $this, 'ajax_add_all_to_cart' ] );
					add_action( 'wp_ajax_nopriv_woobt_add_all_to_cart', [ $this, 'ajax_add_all_to_cart' ] );

					// Cart contents
					add_action( 'woocommerce_before_mini_cart_contents', [ $this, 'before_mini_cart_contents' ] );
					add_action( 'woocommerce_before_calculate_totals', [ $this, 'before_calculate_totals' ], 9999 );

					// Cart item
					add_filter( 'woocommerce_cart_item_name', [ $this, 'cart_item_name' ], 10, 2 );
					add_filter( 'woocommerce_cart_item_quantity', [ $this, 'cart_item_quantity' ], 10, 3 );
					add_action( 'woocommerce_cart_item_removed', [ $this, 'cart_item_removed' ], 10, 2 );

					// Order item
					add_action( 'woocommerce_checkout_create_order_line_item', [ $this, 'order_line_item' ], 10, 3 );
					add_filter( 'woocommerce_order_item_name', [ $this, 'cart_item_name' ], 10, 2 );

					// Admin order
					add_filter( 'woocommerce_hidden_order_itemmeta', [ $this, 'hidden_order_item_meta' ] );
					add_action( 'woocommerce_before_order_itemmeta', [ $this, 'before_order_item_meta' ], 10, 2 );

					// Order again
					add_filter( 'woocommerce_order_again_cart_item_data', [ $this, 'order_again_item_data' ], 10, 2 );
					add_action( 'woocommerce_cart_loaded_from_session', [ $this, 'cart_loaded_from_session' ] );

					// Undo remove
					add_action( 'woocommerce_cart_item_restored', [ $this, 'cart_item_restored' ], 10, 2 );

					// Add settings link
					add_filter( 'plugin_action_links', [ $this, 'action_links' ], 10, 2 );
					add_filter( 'plugin_row_meta', [ $this, 'row_meta' ], 10, 2 );

					// Admin
					add_filter( 'display_post_states', [ $this, 'display_post_states' ], 10, 2 );

					// Search filters
					if ( self::get_setting( 'search_sku', 'no' ) === 'yes' ) {
						add_filter( 'pre_get_posts', [ $this, 'search_sku' ], 99 );
					}

					if ( self::get_setting( 'search_exact', 'no' ) === 'yes' ) {
						add_action( 'pre_get_posts', [ $this, 'search_exact' ], 99 );
					}

					if ( self::get_setting( 'search_sentence', 'no' ) === 'yes' ) {
						add_action( 'pre_get_posts', [ $this, 'search_sentence' ], 99 );
					}

					// Admin product filter
					add_filter( 'woocommerce_products_admin_list_table_filters', [ $this, 'product_filter' ] );
					add_action( 'pre_get_posts', [ $this, 'apply_product_filter' ] );

					// WPML
					if ( function_exists( 'wpml_loaded' ) ) {
						add_filter( 'woobt_item_id', [ $this, 'wpml_item_id' ], 99 );
					}

					// WPC Smart Messages
					add_filter( 'wpcsm_locations', [ $this, 'wpcsm_locations' ] );

					// Export
					add_filter( 'woocommerce_product_export_meta_value', [ $this, 'export_process' ], 10, 3 );

					// Import
					add_filter( 'woocommerce_product_import_pre_insert_product_object', [
						$this,
						'import_process'
					], 10, 2 );

					// HPOS compatibility
					add_action( 'before_woocommerce_init', function () {
						if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
							FeaturesUtil::declare_compatibility( 'custom_order_tables', WOOBT_FILE );
						}
					} );
				}

				function init() {
					self::$types      = (array) apply_filters( 'woobt_product_types', self::$types );
					self::$image_size = apply_filters( 'woobt_image_size', self::$image_size );
					self::$positions  = apply_filters( 'woobt_positions', [
						'before'        => esc_html__( 'Above add to cart button', 'woo-bought-together' ),
						'after'         => esc_html__( 'Under add to cart button', 'woo-bought-together' ),
						'below_title'   => esc_html__( 'Under the title', 'woo-bought-together' ),
						'below_price'   => esc_html__( 'Under the price', 'woo-bought-together' ),
						'below_excerpt' => esc_html__( 'Under the excerpt', 'woo-bought-together' ),
						'below_meta'    => esc_html__( 'Under the meta', 'woo-bought-together' ),
						'below_summary' => esc_html__( 'Under summary', 'woo-bought-together' ),
						'none'          => esc_html__( 'None (hide it)', 'woo-bought-together' ),
					] );
				}

				function available_variation( $data, $variable, $variation ) {
					if ( $image_id = $variation->get_image_id() ) {
						$data['woobt_image'] = wp_get_attachment_image( $image_id, self::$image_size );
					}

					return $data;
				}

				function woovr_data_attributes( $attributes, $variation ) {
					if ( $image_id = $variation->get_image_id() ) {
						$attributes['woobt_image'] = wp_get_attachment_image( $image_id, self::$image_size );
					}

					return $attributes;
				}

				public static function get_settings() {
					return apply_filters( 'woobt_get_settings', self::$settings );
				}

				public static function get_setting( $name, $default = false ) {
					if ( ! empty( self::$settings ) && isset( self::$settings[ $name ] ) ) {
						$setting = self::$settings[ $name ];
					} else {
						$setting = get_option( '_woobt_' . $name, $default );
					}

					return apply_filters( 'woobt_get_setting', $setting, $name, $default );
				}

				function register_settings() {
					// settings
					register_setting( 'woobt_settings', 'woobt_settings' );

					// localization
					register_setting( 'woobt_localization', 'woobt_localization' );
				}

				function admin_menu() {
					add_submenu_page( 'wpclever', esc_html__( 'WPC Frequently Bought Together', 'woo-bought-together' ), esc_html__( 'Bought Together', 'woo-bought-together' ), 'manage_options', 'wpclever-woobt', [
						$this,
						'admin_menu_content'
					] );
				}

				function admin_menu_content() {
					add_thickbox();
					$active_tab = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'settings';
					?>
					<div class="wpclever_settings_page wrap">
						<h1 class="wpclever_settings_page_title"><?php echo esc_html__( 'WPC Frequently Bought Together', 'woo-bought-together' ) . ' ' . WOOBT_VERSION; ?></h1>
						<div class="wpclever_settings_page_desc about-text">
							<p>
								<?php printf( /* translators: %s is the stars */ esc_html__( 'Thank you for using our plugin! If you are satisfied, please reward it a full five-star %s rating.', 'woo-bought-together' ), '<span style="color:#ffb900">&#9733;&#9733;&#9733;&#9733;&#9733;</span>' ); ?>
								<br/>
								<a href="<?php echo esc_url( WOOBT_REVIEWS ); ?>" target="_blank"><?php esc_html_e( 'Reviews', 'woo-bought-together' ); ?></a> |
								<a href="<?php echo esc_url( WOOBT_CHANGELOG ); ?>" target="_blank"><?php esc_html_e( 'Changelog', 'woo-bought-together' ); ?></a> |
								<a href="<?php echo esc_url( WOOBT_DISCUSSION ); ?>" target="_blank"><?php esc_html_e( 'Discussion', 'woo-bought-together' ); ?></a>
							</p>
						</div>
						<?php if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] ) { ?>
							<div class="notice notice-success is-dismissible">
								<p><?php esc_html_e( 'Settings updated.', 'woo-bought-together' ); ?></p>
							</div>
						<?php } ?>
						<div class="wpclever_settings_page_nav">
							<h2 class="nav-tab-wrapper">
								<a href="<?php echo admin_url( 'admin.php?page=wpclever-woobt&tab=how' ); ?>" class="<?php echo esc_attr( $active_tab === 'how' ? 'nav-tab nav-tab-active' : 'nav-tab' ); ?>">
									<?php esc_html_e( 'How to use?', 'woo-bought-together' ); ?>
								</a>
								<a href="<?php echo admin_url( 'admin.php?page=wpclever-woobt&tab=settings' ); ?>" class="<?php echo esc_attr( $active_tab === 'settings' ? 'nav-tab nav-tab-active' : 'nav-tab' ); ?>">
									<?php esc_html_e( 'Settings', 'woo-bought-together' ); ?>
								</a>
								<a href="<?php echo admin_url( 'admin.php?page=wpclever-woobt&tab=localization' ); ?>" class="<?php echo esc_attr( $active_tab === 'localization' ? 'nav-tab nav-tab-active' : 'nav-tab' ); ?>">
									<?php esc_html_e( 'Localization', 'woo-bought-together' ); ?>
								</a>
								<a href="<?php echo admin_url( 'admin.php?page=wpclever-woobt&tab=premium' ); ?>" class="<?php echo esc_attr( $active_tab === 'premium' ? 'nav-tab nav-tab-active' : 'nav-tab' ); ?>" style="color: #c9356e">
									<?php esc_html_e( 'Premium Version', 'woo-bought-together' ); ?>
								</a>
								<a href="<?php echo admin_url( 'admin.php?page=wpclever-kit' ); ?>" class="nav-tab">
									<?php esc_html_e( 'Essential Kit', 'woo-bought-together' ); ?>
								</a>
							</h2>
						</div>
						<div class="wpclever_settings_page_content">
							<?php if ( $active_tab === 'how' ) { ?>
								<div class="wpclever_settings_page_content_text">
									<p>
										<?php esc_html_e( 'When adding/editing the product you can choose Bought Together tab then add some products with the new price.', 'woo-bought-together' ); ?>
									</p>
									<p>
										<img src="<?php echo esc_url( WOOBT_URI ); ?>assets/images/how-01.jpg"/>
									</p>
								</div>
							<?php } elseif ( $active_tab === 'settings' ) {
								$pricing               = self::get_setting( 'pricing', 'sale_price' );
								$default               = self::get_setting( 'default', [ 'default' ] );
								$layout                = self::get_setting( 'layout', 'default' );
								$atc_button            = self::get_setting( 'atc_button', 'main' );
								$show_this_item        = self::get_setting( 'show_this_item', 'yes' );
								$exclude_unpurchasable = self::get_setting( 'exclude_unpurchasable', 'no' );
								$show_thumb            = self::get_setting( 'show_thumb', 'yes' );
								$show_price            = self::get_setting( 'show_price', 'yes' );
								$show_description      = self::get_setting( 'show_description', 'no' );
								$plus_minus            = self::get_setting( 'plus_minus', 'no' );
								$variations_selector   = self::get_setting( 'variations_selector', 'default' );
								$link                  = self::get_setting( 'link', 'yes' );
								$change_image          = self::get_setting( 'change_image', 'yes' );
								$change_price          = self::get_setting( 'change_price', 'yes' );
								$counter               = self::get_setting( 'counter', 'individual' );
								$responsive            = self::get_setting( 'responsive', 'yes' );
								$cart_quantity         = self::get_setting( 'cart_quantity', 'yes' );
								?>
								<form method="post" action="options.php">
									<table class="form-table">
										<tr class="heading">
											<th colspan="2">
												<?php esc_html_e( 'General', 'woo-bought-together' ); ?>
											</th>
										</tr>
										<tr>
											<th><?php esc_html_e( 'Pricing method', 'woo-bought-together' ); ?></th>
											<td>
												<select name="woobt_settings[pricing]">
													<option value="sale_price" <?php selected( $pricing, 'sale_price' ); ?>><?php esc_html_e( 'from Sale price', 'woo-bought-together' ); ?></option>
													<option value="regular_price" <?php selected( $pricing, 'regular_price' ); ?>><?php esc_html_e( 'from Regular price ', 'woo-bought-together' ); ?></option>
												</select>
												<span class="description"><?php esc_html_e( 'Calculate prices from the sale price (default) or regular price of products.', 'woo-bought-together' ); ?></span>
											</td>
										</tr>
										<tr>
											<th><?php esc_html_e( 'Default products', 'woo-bought-together' ); ?></th>
											<td>
												<?php
												// backward compatibility before 5.1.1
												if ( ! is_array( $default ) ) {
													switch ( (string) $default ) {
														case 'upsells':
															$default = [ 'upsells' ];
															break;
														case 'related':
															$default = [ 'related' ];
															break;
														case 'related_upsells':
															$default = [ 'upsells', 'related' ];
															break;
														case 'none':
															$default = [];
															break;
														default:
															$default = [];
													}
												}
												?>
												<input type="hidden" name="woobt_settings[default][]" value="default" checked/>
												<label><input type="checkbox" name="woobt_settings[default][]" value="related" <?php echo esc_attr( in_array( 'related', $default ) ? 'checked' : '' ); ?>/> <?php esc_html_e( 'Related products', 'woo-bought-together' ); ?>
												</label><br/>
												<label><input type="checkbox" name="woobt_settings[default][]" value="upsells" <?php echo esc_attr( in_array( 'upsells', $default ) ? 'checked' : '' ); ?>/> <?php esc_html_e( 'Upsells products', 'woo-bought-together' ); ?>
												</label><br/>
												<label><input type="checkbox" name="woobt_settings[default][]" value="crosssells" <?php echo esc_attr( in_array( 'crosssells', $default ) ? 'checked' : '' ); ?>/> <?php esc_html_e( 'Cross-sells products', 'woo-bought-together' ); ?>
												</label><br/>
												<span class="description"><?php esc_html_e( 'Default products when don\'t specified any products.', 'woo-bought-together' ); ?></span>
												<span class="woobt_show_if_default_products"><?php esc_html_e( 'Limit', 'woo-bought-together' ); ?>
                                                    <input type="number" class="small-text" name="woobt_settings[default_limit]" value="<?php echo esc_attr( self::get_setting( 'default_limit' ) ); ?>"/> <?php esc_html_e( 'products.', 'woo-bought-together' ); ?>
                                                </span>
												<p><span class="description">You can use
													<a href="<?php echo esc_url( admin_url( 'plugin-install.php?tab=plugin-information&plugin=wpc-custom-related-products&TB_iframe=true&width=800&height=550' ) ); ?>" class="thickbox" title="WPC Custom Related Products">WPC Custom Related Products</a> or
														<a href="<?php echo esc_url( admin_url( 'plugin-install.php?tab=plugin-information&plugin=wpc-smart-linked-products&TB_iframe=true&width=800&height=550' ) ); ?>" class="thickbox" title="WPC Smart Linked Products">WPC Smart Linked Products</a> plugin to configure related/upsells/cross-sells in bulk with smart conditions.</span>
												</p>
											</td>
										</tr>
										<tr>
											<th><?php esc_html_e( 'Layout', 'woo-bought-together' ); ?></th>
											<td>
												<select name="woobt_settings[layout]">
													<option value="default" <?php selected( $layout, 'default' ); ?>><?php esc_html_e( 'List', 'woo-bought-together' ); ?></option>
													<option value="separate" <?php selected( $layout, 'separate' ); ?>><?php esc_html_e( 'Separate images', 'woo-bought-together' ); ?></option>
													<option value="grid-2" <?php selected( $layout, 'grid-2' ); ?>><?php esc_html_e( 'Grid - 2 columns', 'woo-bought-together' ); ?></option>
													<option value="grid-3" <?php selected( $layout, 'grid-3' ); ?>><?php esc_html_e( 'Grid - 3 columns', 'woo-bought-together' ); ?></option>
													<option value="grid-4" <?php selected( $layout, 'grid-4' ); ?>><?php esc_html_e( 'Grid - 4 columns', 'woo-bought-together' ); ?></option>
												</select>
											</td>
										</tr>
										<tr>
											<th><?php esc_html_e( 'Position', 'woo-bought-together' ); ?></th>
											<td>
												<?php
												$position = apply_filters( 'woobt_position', self::get_setting( 'position', apply_filters( 'woobt_default_position', 'before' ) ) );

												if ( is_array( self::$positions ) && ( count( self::$positions ) > 0 ) ) {
													echo '<select name="woobt_settings[position]">';

													foreach ( self::$positions as $k => $p ) {
														echo '<option value="' . esc_attr( $k ) . '" ' . ( $k === $position ? 'selected' : '' ) . '>' . esc_html( $p ) . '</option>';
													}

													echo '</select>';
												}
												?>
												<span class="description"><?php esc_html_e( 'Choose the position to show the products list. You also can use the shortcode [woobt] to show the list where you want.', 'woo-bought-together' ); ?></span>
											</td>
										</tr>
										<tr>
											<th><?php esc_html_e( 'Add to cart button', 'woo-bought-together' ); ?></th>
											<td>
												<select name="woobt_settings[atc_button]" class="woobt_atc_button">
													<option value="main" <?php selected( $atc_button, 'main' ); ?>><?php esc_html_e( 'Main product\'s button', 'woo-bought-together' ); ?></option>
													<option value="separate" <?php selected( $atc_button, 'separate' ); ?>><?php esc_html_e( 'Separate buttons', 'woo-bought-together' ); ?></option>
												</select>
											</td>
										</tr>
										<tr>
											<th><?php esc_html_e( 'Show "this item"', 'woo-bought-together' ); ?></th>
											<td>
												<select name="woobt_settings[show_this_item]" class="woobt_show_this_item">
													<option value="yes" <?php selected( $show_this_item, 'yes' ); ?>><?php esc_html_e( 'Yes', 'woo-bought-together' ); ?></option>
													<option value="no" <?php selected( $show_this_item, 'no' ); ?>><?php esc_html_e( 'No', 'woo-bought-together' ); ?></option>
												</select>
												<span class="description"><?php esc_html_e( '"This item" cannot be hidden if "Separate buttons" is in use for the Add to Cart button.', 'woo-bought-together' ); ?></span>
											</td>
										</tr>
										<tr>
											<th><?php esc_html_e( 'Exclude unpurchasable', 'woo-bought-together' ); ?></th>
											<td>
												<select name="woobt_settings[exclude_unpurchasable]">
													<option value="yes" <?php selected( $exclude_unpurchasable, 'yes' ); ?>><?php esc_html_e( 'Yes', 'woo-bought-together' ); ?></option>
													<option value="no" <?php selected( $exclude_unpurchasable, 'no' ); ?>><?php esc_html_e( 'No', 'woo-bought-together' ); ?></option>
												</select>
												<span class="description"><?php esc_html_e( 'Exclude unpurchasable products from the list.', 'woo-bought-together' ); ?></span>
											</td>
										</tr>
										<tr>
											<th><?php esc_html_e( 'Show thumbnail', 'woo-bought-together' ); ?></th>
											<td>
												<select name="woobt_settings[show_thumb]">
													<option value="yes" <?php selected( $show_thumb, 'yes' ); ?>><?php esc_html_e( 'Yes', 'woo-bought-together' ); ?></option>
													<option value="no" <?php selected( $show_thumb, 'no' ); ?>><?php esc_html_e( 'No', 'woo-bought-together' ); ?></option>
												</select>
											</td>
										</tr>
										<tr>
											<th><?php esc_html_e( 'Show price', 'woo-bought-together' ); ?></th>
											<td>
												<select name="woobt_settings[show_price]">
													<option value="yes" <?php selected( $show_price, 'yes' ); ?>><?php esc_html_e( 'Price', 'woo-bought-together' ); ?></option>
													<option value="total" <?php selected( $show_price, 'total' ); ?>><?php esc_html_e( 'Total', 'woo-bought-together' ); ?></option>
													<option value="no" <?php selected( $show_price, 'no' ); ?>><?php esc_html_e( 'No', 'woo-bought-together' ); ?></option>
												</select>
											</td>
										</tr>
										<tr>
											<th><?php esc_html_e( 'Show short description', 'woo-bought-together' ); ?></th>
											<td>
												<select name="woobt_settings[show_description]">
													<option value="yes" <?php selected( $show_description, 'yes' ); ?>><?php esc_html_e( 'Yes', 'woo-bought-together' ); ?></option>
													<option value="no" <?php selected( $show_description, 'no' ); ?>><?php esc_html_e( 'No', 'woo-bought-together' ); ?></option>
												</select>
											</td>
										</tr>
										<tr>
											<th><?php esc_html_e( 'Show plus/minus button', 'woo-bought-together' ); ?></th>
											<td>
												<select name="woobt_settings[plus_minus]">
													<option value="yes" <?php selected( $plus_minus, 'yes' ); ?>><?php esc_html_e( 'Yes', 'woo-bought-together' ); ?></option>
													<option value="no" <?php selected( $plus_minus, 'no' ); ?>><?php esc_html_e( 'No', 'woo-bought-together' ); ?></option>
												</select>
												<span class="description"><?php esc_html_e( 'Show the plus/minus button for the quantity input.', 'woo-bought-together' ); ?></span>
											</td>
										</tr>
										<tr>
											<th><?php esc_html_e( 'Variations selector', 'woo-bought-together' ); ?></th>
											<td>
												<select name="woobt_settings[variations_selector]">
													<option value="default" <?php selected( $variations_selector, 'default' ); ?>><?php esc_html_e( 'Default', 'woo-bought-together' ); ?></option>
													<option value="woovr" <?php selected( $variations_selector, 'woovr' ); ?>><?php esc_html_e( 'Use WPC Variations Radio Buttons', 'woo-bought-together' ); ?></option>
												</select> <span class="description">If you choose "Use WPC Variations Radio Buttons", please install <a href="<?php echo esc_url( admin_url( 'plugin-install.php?tab=plugin-information&plugin=wpc-variations-radio-buttons&TB_iframe=true&width=800&height=550' ) ); ?>" class="thickbox" title="WPC Variations Radio Buttons">WPC Variations Radio Buttons</a> to make it work.</span>
											</td>
										</tr>
										<tr>
											<th><?php esc_html_e( 'Link to individual product', 'woo-bought-together' ); ?></th>
											<td>
												<select name="woobt_settings[link]">
													<option value="yes" <?php selected( $link, 'yes' ); ?>><?php esc_html_e( 'Yes, open in the same tab', 'woo-bought-together' ); ?></option>
													<option value="yes_blank" <?php selected( $link, 'yes_blank' ); ?>><?php esc_html_e( 'Yes, open in the new tab', 'woo-bought-together' ); ?></option>
													<option value="yes_popup" <?php selected( $link, 'yes_popup' ); ?>><?php esc_html_e( 'Yes, open quick view popup', 'woo-bought-together' ); ?></option>
													<option value="no" <?php selected( $link, 'no' ); ?>><?php esc_html_e( 'No', 'woo-bought-together' ); ?></option>
												</select> <span class="description">If you choose "Open quick view popup", please install <a href="<?php echo esc_url( admin_url( 'plugin-install.php?tab=plugin-information&plugin=woo-smart-quick-view&TB_iframe=true&width=800&height=550' ) ); ?>" class="thickbox" title="WPC Smart Quick View">WPC Smart Quick View</a> to make it work.</span>
											</td>
										</tr>
										<tr>
											<th><?php esc_html_e( 'Change image', 'woo-bought-together' ); ?></th>
											<td>
												<select name="woobt_settings[change_image]">
													<option value="yes" <?php selected( $change_image, 'yes' ); ?>><?php esc_html_e( 'Yes', 'woo-bought-together' ); ?></option>
													<option value="no" <?php selected( $change_image, 'no' ); ?>><?php esc_html_e( 'No', 'woo-bought-together' ); ?></option>
												</select>
												<span class="description"><?php esc_html_e( 'Change the main product image when choosing the variation of variable products.', 'woo-bought-together' ); ?></span>
											</td>
										</tr>
										<tr>
											<th><?php esc_html_e( 'Change price', 'woo-bought-together' ); ?></th>
											<td>
												<select name="woobt_settings[change_price]" class="woobt_change_price">
													<option value="yes" <?php selected( $change_price, 'yes' ); ?>><?php esc_html_e( 'Yes', 'woo-bought-together' ); ?></option>
													<option value="yes_custom" <?php selected( $change_price, 'yes_custom' ); ?>><?php esc_html_e( 'Yes, custom selector', 'woo-bought-together' ); ?></option>
													<option value="no" <?php selected( $change_price, 'no' ); ?>><?php esc_html_e( 'No', 'woo-bought-together' ); ?></option>
												</select>
												<input type="text" name="woobt_settings[change_price_custom]" value="<?php echo self::get_setting( 'change_price_custom', '.summary > .price' ); ?>" placeholder=".summary > .price" class="woobt_change_price_custom"/>
												<span class="description"><?php esc_html_e( 'Change the main product price when choosing the variation or quantity of products. It uses JavaScript to change product price so it is very dependent on theme’s HTML. If it cannot find and update the product price, please contact us and we can help you find the right selector or adjust the JS file.', 'woo-bought-together' ); ?></span>
											</td>
										</tr>
										<tr>
											<th><?php esc_html_e( 'Counter', 'woo-bought-together' ); ?></th>
											<td>
												<select name="woobt_settings[counter]">
													<option value="individual" <?php selected( $counter, 'individual' ); ?>><?php esc_html_e( 'Count the individual products', 'woo-bought-together' ); ?></option>
													<option value="qty" <?php selected( $counter, 'qty' ); ?>><?php esc_html_e( 'Count the product quantities', 'woo-bought-together' ); ?></option>
													<option value="hide" <?php selected( $counter, 'hide' ); ?>><?php esc_html_e( 'Hide', 'woo-bought-together' ); ?></option>
												</select>
												<span class="description"><?php esc_html_e( 'Counter on the add to cart button.', 'woo-bought-together' ); ?></span>
											</td>
										</tr>
										<tr>
											<th><?php esc_html_e( 'Responsive', 'woo-bought-together' ); ?></th>
											<td>
												<select name="woobt_settings[responsive]">
													<option value="yes" <?php selected( $responsive, 'yes' ); ?>><?php esc_html_e( 'Yes', 'woo-bought-together' ); ?></option>
													<option value="no" <?php selected( $responsive, 'no' ); ?>><?php esc_html_e( 'No', 'woo-bought-together' ); ?></option>
												</select>
												<span class="description"><?php esc_html_e( 'Change the layout for small screen devices.', 'woo-bought-together' ); ?></span>
											</td>
										</tr>
										<tr class="heading">
											<th colspan="2">
												<?php esc_html_e( 'Cart & Checkout', 'woo-bought-together' ); ?>
											</th>
										</tr>
										<tr>
											<th><?php esc_html_e( 'Change quantity', 'woo-bought-together' ); ?></th>
											<td>
												<select name="woobt_settings[cart_quantity]">
													<option value="yes" <?php selected( $cart_quantity, 'yes' ); ?>><?php esc_html_e( 'Yes', 'woo-bought-together' ); ?></option>
													<option value="no" <?php selected( $cart_quantity, 'no' ); ?>><?php esc_html_e( 'No', 'woo-bought-together' ); ?></option>
												</select>
												<span class="description"><?php esc_html_e( 'Buyer can change the quantity of associated products or not?', 'woo-bought-together' ); ?></span>
											</td>
										</tr>
										<tr class="heading">
											<th colspan="2">
												<?php esc_html_e( 'Search', 'woo-bought-together' ); ?>
											</th>
										</tr>
										<?php self::search_settings(); ?>
										<tr class="heading">
											<th colspan="2"><?php esc_html_e( 'Suggestion', 'woo-bought-together' ); ?></th>
										</tr>
										<tr>
											<td colspan="2">
												To display custom engaging real-time messages on any wished positions, please install
												<a href="https://wordpress.org/plugins/wpc-smart-messages/" target="_blank">WPC Smart Messages for WooCommerce</a> plugin. It's free and available now on the WordPress repository.
											</td>
										</tr>
										<tr class="submit">
											<th colspan="2">
												<?php settings_fields( 'woobt_settings' ); ?><?php submit_button(); ?>
											</th>
										</tr>
									</table>
								</form>
							<?php } elseif ( $active_tab === 'localization' ) { ?>
								<form method="post" action="options.php">
									<table class="form-table">
										<tr class="heading">
											<th scope="row"><?php esc_html_e( 'General', 'woo-bought-together' ); ?></th>
											<td>
												<?php esc_html_e( 'Leave blank to use the default text and its equivalent translation in multiple languages.', 'woo-bought-together' ); ?>
											</td>
										</tr>
										<tr>
											<th><?php esc_html_e( 'This item', 'woo-bought-together' ); ?></th>
											<td>
												<input type="text" name="woobt_localization[this_item]" class="regular-text" value="<?php echo esc_attr( self::localization( 'this_item' ) ); ?>" placeholder="<?php esc_attr_e( 'This item:', 'woo-bought-together' ); ?>"/>
											</td>
										</tr>
										<tr>
											<th><?php esc_html_e( 'Choose an attribute', 'woo-bought-together' ); ?></th>
											<td>
												<input type="text" name="woobt_localization[choose]" class="regular-text" value="<?php echo esc_attr( self::localization( 'choose' ) ); ?>" placeholder="<?php esc_attr_e( 'Choose %s', 'woo-bought-together' ); ?>"/>
												<span class="description"><?php esc_html_e( 'Use %s to show the attribute name.', 'woo-bought-together' ); ?></span>
											</td>
										</tr>
										<tr>
											<th><?php esc_html_e( 'Clear', 'woo-bought-together' ); ?></th>
											<td>
												<input type="text" name="woobt_localization[clear]" class="regular-text" value="<?php echo esc_attr( self::localization( 'clear' ) ); ?>" placeholder="<?php esc_attr_e( 'Clear', 'woo-bought-together' ); ?>"/>
											</td>
										</tr>
										<tr>
											<th><?php esc_html_e( 'Additional price', 'woo-bought-together' ); ?></th>
											<td>
												<input type="text" name="woobt_localization[additional]" class="regular-text" value="<?php echo esc_attr( self::localization( 'additional' ) ); ?>" placeholder="<?php esc_attr_e( 'Additional price:', 'woo-bought-together' ); ?>"/>
											</td>
										</tr>
										<tr>
											<th><?php esc_html_e( 'Total price', 'woo-bought-together' ); ?></th>
											<td>
												<input type="text" name="woobt_localization[total]" class="regular-text" value="<?php echo esc_attr( self::localization( 'total' ) ); ?>" placeholder="<?php esc_attr_e( 'Total:', 'woo-bought-together' ); ?>"/>
											</td>
										</tr>
										<tr>
											<th><?php esc_html_e( 'Associated', 'woo-bought-together' ); ?></th>
											<td>
												<input type="text" name="woobt_localization[associated]" class="regular-text" value="<?php echo esc_attr( self::localization( 'associated' ) ); ?>" placeholder="<?php esc_attr_e( '(bought together %s)', 'woo-bought-together' ); ?>"/>
												<span class="description"><?php esc_html_e( 'The text behind associated products. Use "%s" for the main product name.', 'woo-bought-together' ); ?></span>
											</td>
										</tr>
										<tr>
											<th><?php esc_html_e( 'Add to cart', 'woo-bought-together' ); ?></th>
											<td>
												<input type="text" name="woobt_localization[add_to_cart]" class="regular-text" value="<?php echo esc_attr( self::localization( 'add_to_cart' ) ); ?>" placeholder="<?php esc_attr_e( 'Add to cart', 'woo-bought-together' ); ?>"/>
											</td>
										</tr>
										<tr>
											<th><?php esc_html_e( 'Add all to cart', 'woo-bought-together' ); ?></th>
											<td>
												<input type="text" name="woobt_localization[add_all_to_cart]" class="regular-text" value="<?php echo esc_attr( self::localization( 'add_all_to_cart' ) ); ?>" placeholder="<?php esc_attr_e( 'Add all to cart', 'woo-bought-together' ); ?>"/>
											</td>
										</tr>
										<tr>
											<th><?php esc_html_e( 'Default above text', 'woo-bought-together' ); ?></th>
											<td>
												<input type="text" name="woobt_localization[above_text]" class="large-text" value="<?php echo esc_attr( self::localization( 'above_text' ) ); ?>"/>
												<span class="description"><?php esc_html_e( 'The default text above products list. You can overwrite it for each product in product settings.', 'woo-bought-together' ); ?></span>
											</td>
										</tr>
										<tr>
											<th><?php esc_html_e( 'Default under text', 'woo-bought-together' ); ?></th>
											<td>
												<input type="text" name="woobt_localization[under_text]" class="large-text" value="<?php echo esc_attr( self::localization( 'under_text' ) ); ?>"/>
												<span class="description"><?php esc_html_e( 'The default text under products list. You can overwrite it for each product in product settings.', 'woo-bought-together' ); ?></span>
											</td>
										</tr>
										<tr class="heading">
											<th colspan="2">
												<?php esc_html_e( 'Alert', 'woo-bought-together' ); ?>
											</th>
										</tr>
										<tr>
											<th><?php esc_html_e( 'Require selection', 'woo-bought-together' ); ?></th>
											<td>
												<input type="text" name="woobt_localization[alert_selection]" class="large-text" value="<?php echo esc_attr( self::localization( 'alert_selection' ) ); ?>" placeholder="<?php esc_attr_e( 'Please select a purchasable variation for [name] before adding this product to the cart.', 'woo-bought-together' ); ?>"/>
											</td>
										</tr>
										<tr class="submit">
											<th colspan="2">
												<?php settings_fields( 'woobt_localization' ); ?><?php submit_button(); ?>
											</th>
										</tr>
									</table>
								</form>
							<?php } elseif ( $active_tab == 'premium' ) { ?>
								<div class="wpclever_settings_page_content_text">
									<p>Get the Premium Version just $29!
										<a href="https://wpclever.net/downloads/frequently-bought-together?utm_source=pro&utm_medium=woobt&utm_campaign=wporg" target="_blank">https://wpclever.net/downloads/frequently-bought-together</a>
									</p>
									<p><strong>Extra features for Premium Version:</strong></p>
									<ul style="margin-bottom: 0">
										<li>- Add a variable product or a specific variation of a product.</li>
										<li>- Insert heading/paragraph into products list.</li>
										<li>- Get the lifetime update & premium support.</li>
									</ul>
								</div>
							<?php } ?>
						</div>
					</div>
					<?php
				}

				function search_settings() {
					$search_sku      = self::get_setting( 'search_sku', 'no' );
					$search_id       = self::get_setting( 'search_id', 'no' );
					$search_exact    = self::get_setting( 'search_exact', 'no' );
					$search_sentence = self::get_setting( 'search_sentence', 'no' );
					$search_same     = self::get_setting( 'search_same', 'no' );
					?>
					<tr>
						<th><?php esc_html_e( 'Search limit', 'woo-bought-together' ); ?></th>
						<td>
							<input class="woobt_search_limit" type="number" min="1" max="500" name="woobt_settings[search_limit]" value="<?php echo self::get_setting( 'search_limit', 10 ); ?>"/>
						</td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'Search by SKU', 'woo-bought-together' ); ?></th>
						<td>
							<select name="woobt_settings[search_sku]" class="woobt_search_sku">
								<option value="yes" <?php selected( $search_sku, 'yes' ); ?>><?php esc_html_e( 'Yes', 'woo-bought-together' ); ?></option>
								<option value="no" <?php selected( $search_sku, 'no' ); ?>><?php esc_html_e( 'No', 'woo-bought-together' ); ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'Search by ID', 'woo-bought-together' ); ?></th>
						<td>
							<select name="woobt_settings[search_id]" class="woobt_search_id">
								<option value="yes" <?php selected( $search_id, 'yes' ); ?>><?php esc_html_e( 'Yes', 'woo-bought-together' ); ?></option>
								<option value="no" <?php selected( $search_id, 'no' ); ?>><?php esc_html_e( 'No', 'woo-bought-together' ); ?></option>
							</select>
							<span class="description"><?php esc_html_e( 'Search by ID when entering the numeric only.', 'woo-bought-together' ); ?></span>
						</td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'Search exact', 'woo-bought-together' ); ?></th>
						<td>
							<select name="woobt_settings[search_exact]" class="woobt_search_exact">
								<option value="yes" <?php selected( $search_exact, 'yes' ); ?>><?php esc_html_e( 'Yes', 'woo-bought-together' ); ?></option>
								<option value="no" <?php selected( $search_exact, 'no' ); ?>><?php esc_html_e( 'No', 'woo-bought-together' ); ?></option>
							</select>
							<span class="description"><?php esc_html_e( 'Match whole product title or content?', 'woo-bought-together' ); ?></span>
						</td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'Search sentence', 'woo-bought-together' ); ?></th>
						<td>
							<select name="woobt_settings[search_sentence]" class="woobt_search_sentence">
								<option value="yes" <?php selected( $search_sentence, 'yes' ); ?>><?php esc_html_e( 'Yes', 'woo-bought-together' ); ?></option>
								<option value="no" <?php selected( $search_sentence, 'no' ); ?>><?php esc_html_e( 'No', 'woo-bought-together' ); ?></option>
							</select>
							<span class="description"><?php esc_html_e( 'Do a phrase search?', 'woo-bought-together' ); ?></span>
						</td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'Accept same products', 'woo-bought-together' ); ?></th>
						<td>
							<select name="woobt_settings[search_same]" class="woobt_search_same">
								<option value="yes" <?php selected( $search_same, 'yes' ); ?>><?php esc_html_e( 'Yes', 'woo-bought-together' ); ?></option>
								<option value="no" <?php selected( $search_same, 'no' ); ?>><?php esc_html_e( 'No', 'woo-bought-together' ); ?></option>
							</select>
							<span class="description"><?php esc_html_e( 'If yes, a product can be added many times.', 'woo-bought-together' ); ?></span>
						</td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'Product types', 'woo-bought-together' ); ?></th>
						<td>
							<?php
							$search_types  = self::get_setting( 'search_types', [ 'all' ] );
							$product_types = wc_get_product_types();
							$product_types = array_merge( [ 'all' => esc_html__( 'All', 'woo-bought-together' ) ], $product_types );
							$key_pos       = array_search( 'variable', array_keys( $product_types ) );

							if ( $key_pos !== false ) {
								$key_pos ++;
								$second_array  = array_splice( $product_types, $key_pos );
								$product_types = array_merge( $product_types, [ 'variation' => esc_html__( ' → Variation', 'woo-bought-together' ) ], $second_array );
							}

							echo '<select name="woobt_settings[search_types][]" multiple style="width: 200px; height: 150px;" class="woobt_search_types">';

							foreach ( $product_types as $key => $name ) {
								echo '<option value="' . esc_attr( $key ) . '" ' . ( in_array( $key, $search_types, true ) ? 'selected' : '' ) . '>' . esc_html( $name ) . '</option>';
							}

							echo '</select>';
							?>
						</td>
					</tr>
					<?php
				}

				function enqueue_scripts() {
					wp_enqueue_style( 'woobt-frontend', WOOBT_URI . 'assets/css/frontend.css', [], WOOBT_VERSION );
					wp_enqueue_script( 'woobt-frontend', WOOBT_URI . 'assets/js/frontend.js', [ 'jquery' ], WOOBT_VERSION, true );
					wp_localize_script( 'woobt-frontend', 'woobt_vars', [
							'ajax_url'                 => admin_url( 'admin-ajax.php' ),
							'change_image'             => self::get_setting( 'change_image', 'yes' ),
							'change_price'             => self::get_setting( 'change_price', 'yes' ),
							'price_selector'           => self::get_setting( 'change_price_custom', '' ),
							'counter'                  => self::get_setting( 'counter', 'individual' ),
							'variation_selector'       => ( class_exists( 'WPClever_Woovr' ) && ( self::get_setting( 'variations_selector', 'default' ) === 'woovr' ) ) ? 'woovr' : 'default',
							'price_format'             => get_woocommerce_price_format(),
							'price_suffix'             => ( $suffix = get_option( 'woocommerce_price_display_suffix' ) ) && wc_tax_enabled() ? $suffix : '',
							'price_decimals'           => wc_get_price_decimals(),
							'price_thousand_separator' => wc_get_price_thousand_separator(),
							'price_decimal_separator'  => wc_get_price_decimal_separator(),
							'currency_symbol'          => get_woocommerce_currency_symbol(),
							'trim_zeros'               => apply_filters( 'woocommerce_price_trim_zeros', false ),
							'additional_price_text'    => self::localization( 'additional', esc_html__( 'Additional price:', 'woo-bought-together' ) ),
							'total_price_text'         => self::localization( 'total', esc_html__( 'Total:', 'woo-bought-together' ) ),
							'add_to_cart'              => self::get_setting( 'atc_button', 'main' ) === 'main' ? self::localization( 'add_to_cart', esc_html__( 'Add to cart', 'woo-bought-together' ) ) : self::localization( 'add_all_to_cart', esc_html__( 'Add all to cart', 'woo-bought-together' ) ),
							'alert_selection'          => self::localization( 'alert_selection', esc_html__( 'Please select a purchasable variation for [name] before adding this product to the cart.', 'woo-bought-together' ) ),
						]
					);
				}

				function admin_enqueue_scripts() {
					wp_enqueue_style( 'hint', WOOBT_URI . 'assets/css/hint.css' );
					wp_enqueue_style( 'woobt-backend', WOOBT_URI . 'assets/css/backend.css', [], WOOBT_VERSION );
					wp_enqueue_script( 'woobt-backend', WOOBT_URI . 'assets/js/backend.js', [
						'jquery',
						'jquery-ui-dialog',
						'jquery-ui-sortable'
					], WOOBT_VERSION, true );
				}

				function action_links( $links, $file ) {
					static $plugin;

					if ( ! isset( $plugin ) ) {
						$plugin = plugin_basename( __FILE__ );
					}

					if ( $plugin === $file ) {
						$settings             = '<a href="' . admin_url( 'admin.php?page=wpclever-woobt&tab=settings' ) . '">' . esc_html__( 'Settings', 'woo-bought-together' ) . '</a>';
						$links['wpc-premium'] = '<a href="' . admin_url( 'admin.php?page=wpclever-woobt&tab=premium' ) . '">' . esc_html__( 'Premium Version', 'woo-bought-together' ) . '</a>';
						array_unshift( $links, $settings );
					}

					return (array) $links;
				}

				function row_meta( $links, $file ) {
					static $plugin;

					if ( ! isset( $plugin ) ) {
						$plugin = plugin_basename( __FILE__ );
					}

					if ( $plugin === $file ) {
						$row_meta = [
							'support' => '<a href="' . esc_url( WOOBT_DISCUSSION ) . '" target="_blank">' . esc_html__( 'Community support', 'woo-bought-together' ) . '</a>',
						];

						return array_merge( $links, $row_meta );
					}

					return (array) $links;
				}

				function display_post_states( $states, $post ) {
					if ( 'product' == get_post_type( $post->ID ) ) {
						if ( $ids = self::get_ids( $post->ID, 'edit' ) ) {
							$items = self::get_items( $ids, $post->ID, 'edit' );
							$count = 0;

							if ( ! empty( $items ) ) {
								foreach ( $items as $item ) {
									if ( ! empty( $item['id'] ) ) {
										$count += 1;
									}
								}

								$states[] = apply_filters( 'woobt_post_states', '<span class="woobt-state">' . sprintf( /* translators: %s is the count */ esc_html__( 'Bought together (%s)', 'woo-bought-together' ), $count ) . '</span>', $count, $post->ID );
							}
						}
					}

					return $states;
				}

				function cart_item_removed( $cart_item_key, $cart ) {
					if ( isset( $cart->removed_cart_contents[ $cart_item_key ]['woobt_keys'] ) ) {
						$keys = $cart->removed_cart_contents[ $cart_item_key ]['woobt_keys'];

						foreach ( $keys as $key ) {
							unset( $cart->cart_contents[ $key ] );
						}
					}
				}

				function cart_item_name( $item_name, $item ) {
					if ( isset( $item['woobt_parent_id'] ) && ! empty( $item['woobt_parent_id'] ) ) {
						$associated_text = self::localization( 'associated', esc_html__( '(bought together %s)', 'woo-bought-together' ) );
						$parent_id       = apply_filters( 'woobt_item_id', $item['woobt_parent_id'] );

						if ( strpos( $item_name, '</a>' ) !== false ) {
							$name = sprintf( $associated_text, '<a href="' . get_permalink( $parent_id ) . '">' . get_the_title( $parent_id ) . '</a>' );
						} else {
							$name = sprintf( $associated_text, get_the_title( $parent_id ) );
						}

						$item_name .= ' <span class="woobt-item-name">' . apply_filters( 'woobt_item_name', $name, $item ) . '</span>';
					}

					return $item_name;
				}

				function cart_item_quantity( $quantity, $cart_item_key, $cart_item ) {
					// add qty as text - not input
					if ( isset( $cart_item['woobt_parent_id'] ) ) {
						if ( ( self::get_setting( 'cart_quantity', 'yes' ) === 'no' ) || ( isset( $cart_item['woobt_sync_qty'] ) && $cart_item['woobt_sync_qty'] ) ) {
							return $cart_item['quantity'];
						}
					}

					return $quantity;
				}

				function check_in_cart( $product_id ) {
					foreach ( WC()->cart->get_cart() as $cart_item ) {
						if ( $cart_item['product_id'] === $product_id ) {
							return true;
						}
					}

					return false;
				}

				function found_in_cart( $found_in_cart, $product_id ) {
					if ( apply_filters( 'woobt_sold_individually_found_in_cart', true ) && self::check_in_cart( $product_id ) ) {
						return true;
					}

					return $found_in_cart;
				}

				function add_to_cart_validation( $passed, $product_id ) {
					if ( ( get_post_meta( $product_id, 'woobt_separately', true ) !== 'on' ) && self::get_ids( $product_id, 'validate' ) ) {
						if ( isset( $_REQUEST['woobt_ids'] ) || isset( $_REQUEST['data']['woobt_ids'] ) ) {
							if ( isset( $_REQUEST['woobt_ids'] ) ) {
								$items = self::get_items( $_REQUEST['woobt_ids'], $product_id );
							} elseif ( isset( $_REQUEST['data']['woobt_ids'] ) ) {
								$items = self::get_items( $_REQUEST['data']['woobt_ids'], $product_id );
							}

							if ( ! empty( $items ) ) {
								foreach ( $items as $item ) {
									$item_product = wc_get_product( $item['id'] );

									if ( ! $item_product ) {
										wc_add_notice( esc_html__( 'One of the associated products is unavailable.', 'woo-bought-together' ), 'error' );
										wc_add_notice( esc_html__( 'You cannot add this product to the cart.', 'woo-bought-together' ), 'error' );

										return false;
									}

									if ( $item_product->is_type( 'variable' ) ) {
										wc_add_notice( sprintf( /* translators: %s is the product name */ esc_html__( '"%s" is un-purchasable.', 'woo-bought-together' ), esc_html( apply_filters( 'woobt_product_get_name', $item_product->get_name(), $item_product ) ) ), 'error' );
										wc_add_notice( esc_html__( 'You cannot add this product to the cart.', 'woo-bought-together' ), 'error' );

										return false;
									}

									if ( $item_product->is_sold_individually() && apply_filters( 'woobt_sold_individually_found_in_cart', true ) && self::check_in_cart( $item['id'] ) ) {
										wc_add_notice( sprintf( /* translators: %s is the product name */ esc_html__( 'You cannot add another "%s" to the cart.', 'woo-bought-together' ), esc_html( apply_filters( 'woobt_product_get_name', $item_product->get_name(), $item_product ) ) ), 'error' );
										wc_add_notice( esc_html__( 'You cannot add this product to the cart.', 'woo-bought-together' ), 'error' );

										return false;
									}

									if ( apply_filters( 'woobt_custom_qty', get_post_meta( $product_id, 'woobt_custom_qty', true ) === 'on', $product_id ) ) {
										if ( ( $limit_min = get_post_meta( $product_id, 'woobt_limit_each_min', true ) ) && ( $item['qty'] < (float) $limit_min ) ) {
											wc_add_notice( sprintf( /* translators: %s is the product name */ esc_html__( '"%s" does not reach the minimum quantity.', 'woo-bought-together' ), esc_html( apply_filters( 'woobt_product_get_name', $item_product->get_name(), $item_product ) ) ), 'error' );
											wc_add_notice( esc_html__( 'You cannot add this product to the cart.', 'woo-bought-together' ), 'error' );

											return false;
										}

										if ( ( $limit_max = get_post_meta( $product_id, 'woobt_limit_each_max', true ) ) && ( $item['qty'] > (float) $limit_max ) ) {
											wc_add_notice( sprintf( /* translators: %s is the product name */ esc_html__( '"%s" passes the maximum quantity.', 'woo-bought-together' ), esc_html( apply_filters( 'woobt_product_get_name', $item_product->get_name(), $item_product ) ) ), 'error' );
											wc_add_notice( esc_html__( 'You cannot add this product to the cart.', 'woo-bought-together' ), 'error' );

											return false;
										}
									}
								}
							}
						}
					}

					return $passed;
				}

				function add_cart_item_data( $cart_item_data, $product_id ) {
					if ( ( isset( $_REQUEST['woobt_ids'] ) || isset( $_REQUEST['data']['woobt_ids'] ) ) && ( get_post_meta( $product_id, 'woobt_separately', true ) !== 'on' ) && ( self::get_ids( $product_id, 'validate' ) || ( self::get_setting( 'default', 'none' ) !== 'none' ) ) ) {
						// make sure that is bought together product
						if ( isset( $_REQUEST['woobt_ids'] ) ) {
							$ids = self::clean_ids( $_REQUEST['woobt_ids'] );
						} elseif ( isset( $_REQUEST['data']['woobt_ids'] ) ) {
							$ids = self::clean_ids( $_REQUEST['data']['woobt_ids'] );
						}

						if ( ! empty( $ids ) ) {
							$cart_item_data['woobt_ids'] = $ids;
						}
					}

					return $cart_item_data;
				}

				function add_to_cart( $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data ) {
					if ( isset( $cart_item_data['_bundle_variation_id'] ) ) {
						// WC Product Bundles Variation
						$variation_product = wc_get_product( $cart_item_data['_bundle_variation_id'] );
						$product_id        = $variation_product->get_parent_id();
					}

					if ( ( isset( $_REQUEST['woobt_ids'] ) || isset( $_REQUEST['data']['woobt_ids'] ) ) && ( self::get_ids( $product_id, 'validate' ) || ( self::get_setting( 'default', 'none' ) !== 'none' ) ) ) {
						$ids = '';

						if ( isset( $_REQUEST['woobt_ids'] ) ) {
							$ids = self::clean_ids( $_REQUEST['woobt_ids'] );
							unset( $_REQUEST['woobt_ids'] );
						} elseif ( isset( $_REQUEST['data']['woobt_ids'] ) ) {
							$ids = self::clean_ids( $_REQUEST['data']['woobt_ids'] );
							unset( $_REQUEST['data']['woobt_ids'] );
						}

						if ( $items = self::get_items( $ids, $product_id ) ) {
							$custom_qty = apply_filters( 'woobt_custom_qty', get_post_meta( $product_id, 'woobt_custom_qty', true ) === 'on', $product_id );
							$sync_qty   = ! $custom_qty && apply_filters( 'woobt_sync_qty', get_post_meta( $product_id, 'woobt_sync_qty', true ) === 'on' );

							// add sync_qty for the main product
							if ( get_post_meta( $product_id, 'woobt_separately', true ) !== 'on' ) {
								WC()->cart->cart_contents[ $cart_item_key ]['woobt_ids']      = $ids;
								WC()->cart->cart_contents[ $cart_item_key ]['woobt_key']      = $cart_item_key;
								WC()->cart->cart_contents[ $cart_item_key ]['woobt_sync_qty'] = $sync_qty;
							}

							// add child products
							self::add_to_cart_items( $items, $cart_item_key, $product_id, $quantity );
						}
					}
				}

				function add_to_cart_items( $items, $cart_item_key, $product_id, $quantity ) {
					$custom_qty = apply_filters( 'woobt_custom_qty', get_post_meta( $product_id, 'woobt_custom_qty', true ) === 'on', $product_id );
					$sync_qty   = ! $custom_qty && apply_filters( 'woobt_sync_qty', get_post_meta( $product_id, 'woobt_sync_qty', true ) === 'on' );

					// add child products
					foreach ( $items as $item ) {
						$item_id           = $item['id'];
						$item_price        = apply_filters( 'woobt_item_price', $item['price'], $item_id, $product_id );
						$item_qty          = $item['qty'];
						$item_variation    = $item['attrs'];
						$item_variation_id = 0;
						$item_product      = wc_get_product( $item_id );

						if ( $item_product instanceof WC_Product_Variation ) {
							// ensure we don't add a variation to the cart directly by variation ID
							$item_variation_id = $item_id;
							$item_id           = $item_product->get_parent_id();

							if ( empty( $item_variation ) ) {
								$item_variation = $item_product->get_variation_attributes();
							}
						}

						if ( $item_product && $item_product->is_in_stock() && $item_product->is_purchasable() && ( 'trash' !== $item_product->get_status() ) ) {
							if ( get_post_meta( $product_id, 'woobt_separately', true ) !== 'on' ) {
								// add to cart
								$item_key = WC()->cart->add_to_cart( $item_id, $item_qty, $item_variation_id, $item_variation, [
									'woobt_parent_id'  => $product_id,
									'woobt_parent_key' => $cart_item_key,
									'woobt_qty'        => $item_qty,
									'woobt_sync_qty'   => $sync_qty,
									'woobt_price_item' => $item_price
								] );

								if ( $item_key ) {
									WC()->cart->cart_contents[ $item_key ]['woobt_key']         = $item_key;
									WC()->cart->cart_contents[ $cart_item_key ]['woobt_keys'][] = $item_key;
								}
							} else {
								if ( $sync_qty ) {
									WC()->cart->add_to_cart( $item_id, $item_qty * $quantity, $item_variation_id, $item_variation );
								} else {
									WC()->cart->add_to_cart( $item_id, $item_qty, $item_variation_id, $item_variation );
								}
							}
						}
					}
				}

				function ajax_add_all_to_cart() {
					if ( ! isset( $_POST['product_id'] ) ) {
						return;
					}

					$product_id     = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $_POST['product_id'] ) );
					$product        = wc_get_product( $product_id );
					$product_status = get_post_status( $product_id );
					$quantity       = empty( $_POST['quantity'] ) ? 1 : wc_stock_amount( wp_unslash( $_POST['quantity'] ) );
					$variation_id   = isset( $_POST['variation_id'] ) ? absint( $_POST['variation_id'] ) : 0;
					$variation      = isset( $_POST['variation'] ) ? (array) $_POST['variation'] : [];

					if ( $product && 'variation' === $product->get_type() ) {
						$variation_id = $product_id;
						$product_id   = $product->get_parent_id();

						if ( empty( $variation ) ) {
							$variation = $product->get_variation_attributes();
						}
					}

					$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity, $variation_id, $variation );

					if ( $passed_validation && false !== WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variation ) && 'publish' === $product_status ) {
						do_action( 'woocommerce_ajax_added_to_cart', $product_id );

						if ( 'yes' === get_option( 'woocommerce_cart_redirect_after_add' ) ) {
							wc_add_to_cart_message( [ $product_id => $quantity ], true );
						}

						WC_AJAX::get_refreshed_fragments();
					} else {
						$data = [
							'error'       => true,
							'product_url' => apply_filters( 'woocommerce_cart_redirect_after_error', get_permalink( $product_id ), $product_id ),
						];

						wp_send_json( $data );
					}

					wp_die();
				}

				function before_mini_cart_contents() {
					WC()->cart->calculate_totals();
				}

				function before_calculate_totals( $cart_object ) {
					if ( ! defined( 'DOING_AJAX' ) && is_admin() ) {
						// This is necessary for WC 3.0+
						return;
					}

					$cart_contents = $cart_object->cart_contents;
					$new_keys      = [];

					foreach ( $cart_contents as $cart_item_key => $cart_item ) {
						if ( ! empty( $cart_item['woobt_key'] ) ) {
							$new_keys[ $cart_item_key ] = $cart_item['woobt_key'];
						}
					}

					foreach ( $cart_contents as $cart_item_key => $cart_item ) {
						// associated products
						if ( isset( $cart_item['woobt_parent_id'], $cart_item['woobt_price_item'] ) && ( $cart_item['woobt_price_item'] !== '100%' ) && ( $cart_item['woobt_price_item'] !== '' ) ) {
							$pricing  = self::get_setting( 'pricing', 'sale_price' );
							$_product = wc_get_product( ! empty( $cart_item['variation_id'] ) ? $cart_item['variation_id'] : $cart_item['product_id'] );

							// calc new price
							if ( $pricing === 'sale_price' ) {
								// from sale price
								$item_new_price = self::new_price( $_product->get_price( 'edit' ), $cart_item['woobt_price_item'] );
							} else {
								// from regular price
								$item_new_price = self::new_price( $_product->get_regular_price( 'edit' ), $cart_item['woobt_price_item'] );
							}

							$cart_item['data']->set_price( $item_new_price );
						}

						// sync quantity
						if ( ! empty( $cart_item['woobt_parent_key'] ) && ! empty( $cart_item['woobt_qty'] ) && ! empty( $cart_item['woobt_sync_qty'] ) ) {
							$parent_key     = $cart_item['woobt_parent_key'];
							$parent_new_key = array_search( $parent_key, $new_keys );

							if ( isset( $cart_contents[ $parent_key ] ) ) {
								WC()->cart->cart_contents[ $cart_item_key ]['quantity'] = $cart_item['woobt_qty'] * $cart_contents[ $parent_key ]['quantity'];
							} elseif ( isset( $cart_contents[ $parent_new_key ] ) ) {
								WC()->cart->cart_contents[ $cart_item_key ]['quantity'] = $cart_item['woobt_qty'] * $cart_contents[ $parent_new_key ]['quantity'];
							}
						}

						// main product
						if ( ! empty( $cart_item['woobt_ids'] ) && ( $discount = get_post_meta( $cart_item['product_id'], 'woobt_discount', true ) ) && ( get_post_meta( $cart_item['product_id'], 'woobt_separately', true ) !== 'on' ) ) {
							if ( $cart_item['variation_id'] > 0 ) {
								$item_product = wc_get_product( $cart_item['variation_id'] );
							} else {
								$item_product = wc_get_product( $cart_item['product_id'] );
							}

							$ori_price = $item_product->get_price();

							// has associated products
							$has_associated = false;

							if ( isset( $cart_item['woobt_keys'] ) ) {
								foreach ( $cart_item['woobt_keys'] as $key ) {
									if ( isset( $cart_contents[ $key ] ) ) {
										$has_associated = true;
										break;
									}
								}
							}

							if ( $has_associated && ! empty( $discount ) ) {
								$discount_price = $ori_price * ( 100 - (float) $discount ) / 100;
								$cart_item['data']->set_price( $discount_price );
							}
						}
					}
				}

				function get_cart_item_from_session( $cart_item, $item_session_values ) {
					if ( isset( $item_session_values['woobt_ids'] ) && ! empty( $item_session_values['woobt_ids'] ) ) {
						$cart_item['woobt_ids']      = $item_session_values['woobt_ids'];
						$cart_item['woobt_sync_qty'] = $item_session_values['woobt_sync_qty'];
					}

					if ( isset( $item_session_values['woobt_parent_id'] ) ) {
						$cart_item['woobt_parent_id']  = $item_session_values['woobt_parent_id'];
						$cart_item['woobt_parent_key'] = $item_session_values['woobt_parent_key'];
						$cart_item['woobt_price_item'] = $item_session_values['woobt_price_item'];
						$cart_item['woobt_qty']        = $item_session_values['woobt_qty'];
						$cart_item['woobt_sync_qty']   = $item_session_values['woobt_sync_qty'];
					}

					return $cart_item;
				}

				function order_line_item( $item, $cart_item_key, $values ) {
					// add _ to hide
					if ( isset( $values['woobt_parent_id'] ) ) {
						$item->update_meta_data( '_woobt_parent_id', $values['woobt_parent_id'] );
					}

					if ( isset( $values['woobt_ids'] ) ) {
						$item->update_meta_data( '_woobt_ids', $values['woobt_ids'] );
					}
				}

				function hidden_order_item_meta( $hidden ) {
					return array_merge( $hidden, [
						'_woobt_parent_id',
						'_woobt_ids',
						'woobt_parent_id',
						'woobt_ids'
					] );
				}

				function before_order_item_meta( $item_id, $item ) {
					if ( $parent_id = $item->get_meta( '_woobt_parent_id' ) ) {
						echo sprintf( self::localization( 'associated', esc_html__( '(bought together %s)', 'woo-bought-together' ) ), get_the_title( $parent_id ) );
					}
				}

				function order_again_item_data( $data, $item ) {
					if ( $ids = $item->get_meta( '_woobt_ids' ) ) {
						$data['woobt_order_again'] = 'yes';
						$data['woobt_ids']         = $ids;
					}

					if ( $parent_id = $item->get_meta( '_woobt_parent_id' ) ) {
						$data['woobt_order_again'] = 'yes';
						$data['woobt_parent_id']   = $parent_id;
					}

					return $data;
				}

				function cart_loaded_from_session( $cart ) {
					foreach ( $cart->cart_contents as $cart_item_key => $cart_item ) {
						// remove associated products first
						if ( isset( $cart_item['woobt_order_again'], $cart_item['woobt_parent_id'] ) ) {
							$cart->remove_cart_item( $cart_item_key );
						}
					}

					foreach ( $cart->cart_contents as $cart_item_key => $cart_item ) {
						// add associated products again
						if ( isset( $cart_item['woobt_order_again'], $cart_item['woobt_ids'] ) ) {
							unset( $cart->cart_contents[ $cart_item_key ]['woobt_order_again'] );

							$product_id = $cart_item['product_id'];
							$custom_qty = apply_filters( 'woobt_custom_qty', get_post_meta( $product_id, 'woobt_custom_qty', true ) === 'on', $product_id );
							$sync_qty   = ! $custom_qty && apply_filters( 'woobt_sync_qty', get_post_meta( $product_id, 'woobt_sync_qty', true ) === 'on' );

							$cart->cart_contents[ $cart_item_key ]['woobt_key']      = $cart_item_key;
							$cart->cart_contents[ $cart_item_key ]['woobt_sync_qty'] = $sync_qty;

							if ( $items = self::get_items( $cart_item['woobt_ids'], $cart_item['product_id'] ) ) {
								self::add_to_cart_items( $items, $cart_item_key, $cart_item['product_id'], $cart_item['quantity'] );
							}
						}
					}
				}

				function cart_item_restored( $cart_item_key, $cart ) {
					if ( isset( $cart->cart_contents[ $cart_item_key ]['woobt_ids'] ) ) {
						// remove old keys
						unset( $cart->cart_contents[ $cart_item_key ]['woobt_keys'] );

						$ids        = $cart->cart_contents[ $cart_item_key ]['woobt_ids'];
						$product_id = $cart->cart_contents[ $cart_item_key ]['product_id'];
						$quantity   = $cart->cart_contents[ $cart_item_key ]['quantity'];

						if ( get_post_meta( $product_id, 'woobt_separately', true ) !== 'on' ) {
							if ( $items = self::get_items( $ids, $product_id ) ) {
								self::add_to_cart_items( $items, $cart_item_key, $product_id, $quantity );
							}
						}
					}
				}

				function ajax_update_search_settings() {
					$settings = (array) get_option( 'woobt_settings', [] );

					$settings['search_limit']    = (int) sanitize_text_field( $_POST['limit'] );
					$settings['search_sku']      = sanitize_text_field( $_POST['sku'] );
					$settings['search_id']       = sanitize_text_field( $_POST['id'] );
					$settings['search_exact']    = sanitize_text_field( $_POST['exact'] );
					$settings['search_sentence'] = sanitize_text_field( $_POST['sentence'] );
					$settings['search_same']     = sanitize_text_field( $_POST['same'] );
					$settings['search_types']    = array_map( 'sanitize_text_field', (array) $_POST['types'] );

					update_option( 'woobt_settings', $settings );
					wp_die();
				}

				function ajax_get_search_results() {
					$types         = self::get_setting( 'search_types', [ 'all' ] );
					$keyword       = esc_html( $_POST['woobt_keyword'] );
					$id            = absint( $_POST['woobt_id'] );
					$exclude_ids   = explode( ',', self::clean_ids( $_POST['woobt_ids'] ) );
					$exclude_ids[] = $id;

					if ( ( self::get_setting( 'search_id', 'no' ) === 'yes' ) && is_numeric( $keyword ) ) {
						// search by id
						$query_args = [
							'p'         => absint( $keyword ),
							'post_type' => 'product'
						];
					} else {
						$query_args = [
							'is_woobt'       => true,
							'post_type'      => 'product',
							'post_status'    => 'publish',
							's'              => $keyword,
							'posts_per_page' => self::get_setting( 'search_limit', 10 )
						];

						if ( ! empty( $types ) && ! in_array( 'all', $types, true ) ) {
							$product_types = $types;

							if ( in_array( 'variation', $types, true ) ) {
								$product_types[] = 'variable';
							}

							$query_args['tax_query'] = [
								[
									'taxonomy' => 'product_type',
									'field'    => 'slug',
									'terms'    => $product_types,
								],
							];
						}

						if ( self::get_setting( 'search_same', 'no' ) !== 'yes' ) {
							$query_args['post__not_in'] = $exclude_ids;
						}
					}

					$query = new WP_Query( $query_args );

					if ( $query->have_posts() ) {
						echo '<ul>';

						while ( $query->have_posts() ) {
							$query->the_post();
							$product = wc_get_product( get_the_ID() );

							if ( ! $product || ( 'trash' === $product->get_status() ) ) {
								continue;
							}

							if ( ! $product->is_type( 'variable' ) || in_array( 'variable', $types, true ) || in_array( 'all', $types, true ) ) {
								self::product_data_li( $product, '100%', 1, true );
							}

							if ( $product->is_type( 'variable' ) && ( empty( $types ) || in_array( 'all', $types, true ) || in_array( 'variation', $types, true ) ) ) {
								// show all children
								$children = $product->get_children();

								if ( is_array( $children ) && count( $children ) > 0 ) {
									foreach ( $children as $child ) {
										$product_child = wc_get_product( $child );

										if ( $product_child ) {
											self::product_data_li( $product_child, '100%', 1, true );
										}
									}
								}
							}
						}

						echo '</ul>';
						wp_reset_postdata();
					} else {
						echo '<ul><span>' . sprintf( /* translators: %s is the keyword */ esc_html__( 'No results found for "%s"', 'woo-bought-together' ), esc_html( $keyword ) ) . '</span></ul>';
					}

					wp_die();
				}

				function product_data_li( $product, $price = '100%', $qty = 1, $search = false ) {
					$key           = uniqid();
					$product_id    = $product->get_id();
					$product_sku   = $product->get_sku();
					$product_class = 'woobt-li-product woobt-item';
					$product_class .= ! $product->is_in_stock() ? ' out-of-stock' : '';
					$product_class .= ! in_array( $product->get_type(), self::$types, true ) ? ' disabled' : '';

					if ( class_exists( 'WPCleverWoopq' ) && ( get_option( '_woopq_decimal', 'no' ) === 'yes' ) ) {
						$step = '0.000001';
					} else {
						$step = '1';
						$qty  = (int) $qty;
					}

					if ( $search ) {
						$remove_btn = '<span class="woobt-remove hint--left" aria-label="' . esc_html__( 'Add', 'woo-bought-together' ) . '">+</span>';
					} else {
						$remove_btn = '<span class="woobt-remove hint--left" aria-label="' . esc_html__( 'Remove', 'woo-bought-together' ) . '">×</span>';
					}

					$hidden_input = '<input type="hidden" name="woobt_ids[' . $key . '][id]" value="' . $product_id . '"/><input type="hidden" name="woobt_ids[' . $key . '][sku]" value="' . $product_sku . '"/>';

					echo '<li class="' . esc_attr( trim( $product_class ) ) . '" data-id="' . $product->get_id() . '">' . $hidden_input . '<span class="woobt-move"></span><span class="price hint--right" aria-label="' . esc_html__( 'Set a new price using a number (eg. "49") or percentage (eg. "90%" of original price)', 'woo-bought-together' ) . '"><input type="text" name="woobt_ids[' . $key . '][price]" value="' . $price . '"/></span><span class="qty hint--right" aria-label="' . esc_html__( 'Default quantity', 'woo-bought-together' ) . '"><input type="number" name="woobt_ids[' . $key . '][qty]" value="' . esc_attr( $qty ) . '" step="' . esc_attr( $step ) . '"/></span><span class="img">' . $product->get_image( [
							30,
							30
						] ) . '</span><span class="data">' . ( $product->get_status() === 'private' ? '<span class="info">private</span> ' : '' ) . '<span class="name">' . strip_tags( $product->get_name() ) . '</span> <span class="info">' . $product->get_price_html() . '</span></span> <span class="type"><a href="' . get_edit_post_link( $product_id ) . '" target="_blank">' . $product->get_type() . '<br/>#' . $product->get_id() . '</a></span> ' . $remove_btn . '</li>';
				}

				function text_data_li( $data = [] ) {
					$key  = uniqid();
					$data = array_merge( [ 'type' => 'h1', 'text' => '' ], $data );
					$type = '<select name="woobt_ids[' . $key . '][type]"><option value="h1" ' . selected( $data['type'], 'h1', false ) . '>H1</option><option value="h2" ' . selected( $data['type'], 'h2', false ) . '>H2</option><option value="h3" ' . selected( $data['type'], 'h3', false ) . '>H3</option><option value="h4" ' . selected( $data['type'], 'h4', false ) . '>H4</option><option value="h5" ' . selected( $data['type'], 'h5', false ) . '>H5</option><option value="h6" ' . selected( $data['type'], 'h6', false ) . '>H6</option><option value="p" ' . selected( $data['type'], 'p', false ) . '>p</option><option value="span" ' . selected( $data['type'], 'span', false ) . '>span</option><option value="none" ' . selected( $data['type'], 'none', false ) . '>none</option></select>';

					echo '<li class="woobt-li-text"><span class="woobt-move"></span><span class="tag">' . $type . '</span><span class="data"><input type="text" name="woobt_ids[' . $key . '][text]" value="' . esc_attr( $data['text'] ) . '"/></span><span class="woobt-remove hint--left" aria-label="' . esc_html__( 'Remove', 'woo-bought-together' ) . '">×</span></li>';
				}

				function product_data_tabs( $tabs ) {
					$tabs['woobt'] = [
						'label'  => esc_html__( 'Bought Together', 'woo-bought-together' ),
						'target' => 'woobt_settings',
					];

					return $tabs;
				}

				function product_data_panels() {
					global $post;
					$post_id        = $post->ID;
					$selection      = get_post_meta( $post_id, 'woobt_selection', true ) ?: 'multiple';
					$layout         = get_post_meta( $post_id, 'woobt_layout', true ) ?: 'unset';
					$position       = get_post_meta( $post_id, 'woobt_position', true ) ?: 'unset';
					$atc_button     = get_post_meta( $post_id, 'woobt_atc_button', true ) ?: 'unset';
					$show_this_item = get_post_meta( $post_id, 'woobt_show_this_item', true ) ?: 'unset';
					?>
					<div id='woobt_settings' class='panel woocommerce_options_panel woobt_table'>
						<div id="woobt_search_settings" style="display: none" data-title="<?php esc_html_e( 'Search settings', 'woo-bought-together' ); ?>">
							<table>
								<?php self::search_settings(); ?>
								<tr>
									<th></th>
									<td>
										<button id="woobt_search_settings_update" class="button button-primary">
											<?php esc_html_e( 'Update Options', 'woo-bought-together' ); ?>
										</button>
									</td>
								</tr>
							</table>
						</div>
						<table>
							<tr>
								<th><?php esc_html_e( 'Search', 'woo-bought-together' ); ?> (<a href="<?php echo admin_url( 'admin.php?page=wpclever-woobt&tab=settings#search' ); ?>" id="woobt_search_settings_btn"><?php esc_html_e( 'settings', 'woo-bought-together' ); ?></a>)
								</th>
								<td>
									<div class="w100">
										<span class="loading" id="woobt_loading" style="display: none"><?php esc_html_e( 'searching...', 'woo-bought-together' ); ?></span>
										<input type="search" id="woobt_keyword" placeholder="<?php esc_attr_e( 'Type any keyword to search', 'woo-bought-together' ); ?>"/>
										<div id="woobt_results" class="woobt_results" style="display: none"></div>
									</div>
								</td>
							</tr>
							<tr class="woobt_tr_space">
								<th>
									<?php esc_html_e( 'Selected', 'woo-bought-together' ); ?>
									<div class="woobt_tools">
										<a href="#" class="woobt-import-export"><?php esc_html_e( 'import/export', 'woo-bought-together' ); ?></a>
									</div>
								</th>
								<td>
									<div class="w100">
										<?php echo '<div class="woobt_notice_default">' . sprintf( esc_html__( '* If don\'t choose any products, it can shows the default products %s.', 'woo-bought-together' ), '<a
                                                    href="' . admin_url( 'admin.php?page=wpclever-woobt&tab=settings' ) . '" target="_blank">' . esc_html__( 'here', 'woo-bought-together' ) . '</a>' ) . '</div>'; ?>
										<div id="woobt_selected" class="woobt_selected">
											<ul>
												<?php
												if ( $ids = self::get_ids( $post_id, 'edit' ) ) {
													if ( $items = self::get_items( $ids, $post_id, 'edit' ) ) {
														foreach ( $items as $item ) {
															if ( ! empty( $item['id'] ) ) {
																$item_id      = $item['id'];
																$item_price   = $item['price'];
																$item_qty     = $item['qty'];
																$item_product = wc_get_product( $item_id );

																if ( ! $item_product ) {
																	continue;
																}

																self::product_data_li( $item_product, $item_price, $item_qty, false );
															} else {
																// new version 5.0
																self::text_data_li( $item );
															}
														}
													}
												}
												?>
											</ul>
										</div>
									</div>
								</td>
							</tr>
							<tr class="woobt_tr_space">
								<th></th>
								<td>
									<a href="https://wpclever.net/downloads/frequently-bought-together?utm_source=pro&utm_medium=woobt&utm_campaign=wporg" target="_blank" class="woobt_add_txt" onclick="return confirm('This feature only available in Premium Version!\nBuy it now? Just $29')">
										<?php esc_html_e( '+ Add heading/paragraph', 'woo-bought-together' ); ?>
									</a>
								</td>
							</tr>
							<tr class="woobt_tr_space">
								<th><?php esc_html_e( 'Add separately', 'woo-bought-together' ); ?></th>
								<td>
									<input id="woobt_separately" name="woobt_separately" type="checkbox" <?php echo esc_attr( get_post_meta( $post_id, 'woobt_separately', true ) === 'on' ? 'checked' : '' ); ?>/>
									<span class="woocommerce-help-tip" data-tip="<?php esc_attr_e( 'If enabled, the associated products will be added as separate items and stay unaffected from the main product, their prices will change back to the original.', 'woo-bought-together' ); ?>"></span>
								</td>
							</tr>
							<tr class="woobt_tr_space">
								<th><?php esc_html_e( 'Selecting method', 'woo-bought-together' ); ?></th>
								<td>
									<select name="woobt_selection">
										<option value="multiple" <?php selected( $selection, 'multiple' ); ?>><?php esc_html_e( 'Multiple selection (default)', 'woo-bought-together' ); ?></option>
										<option value="single" <?php selected( $selection, 'single' ); ?>><?php esc_html_e( 'Single selection (choose 1 only)', 'woo-bought-together' ); ?></option>
									</select>
								</td>
							</tr>
							<tr class="woobt_tr_space">
								<th><?php esc_html_e( 'Discount', 'woo-bought-together' ); ?></th>
								<td>
									<input id="woobt_discount" name="woobt_discount" type="number" min="0" max="100" step="0.0001" style="width: 50px" value="<?php echo get_post_meta( $post_id, 'woobt_discount', true ); ?>"/>%
									<span class="woocommerce-help-tip" data-tip="Discount for the main product when buying at least one product in this list."></span>
								</td>
							</tr>
							<tr class="woobt_tr_space">
								<th><?php esc_html_e( 'Checked all', 'woo-bought-together' ); ?></th>
								<td>
									<input id="woobt_checked_all" name="woobt_checked_all" type="checkbox" <?php echo esc_attr( get_post_meta( $post_id, 'woobt_checked_all', true ) === 'on' ? 'checked' : '' ); ?>/>
									<label for="woobt_checked_all"><?php esc_html_e( 'Checked all by default.', 'woo-bought-together' ); ?></label>
								</td>
							</tr>
							<tr class="woobt_tr_space">
								<th><?php esc_html_e( 'Custom quantity', 'woo-bought-together' ); ?></th>
								<td>
									<input id="woobt_custom_qty" name="woobt_custom_qty" type="checkbox" <?php echo esc_attr( get_post_meta( $post_id, 'woobt_custom_qty', true ) === 'on' ? 'checked' : '' ); ?>/>
									<label for="woobt_custom_qty"><?php esc_html_e( 'Allow the customer can change the quantity of each product.', 'woo-bought-together' ); ?></label>
								</td>
							</tr>
							<tr class="woobt_tr_space woobt_tr_hide_if_custom_qty">
								<th><?php esc_html_e( 'Sync quantity', 'woo-bought-together' ); ?></th>
								<td>
									<input id="woobt_sync_qty" name="woobt_sync_qty" type="checkbox" <?php echo esc_attr( get_post_meta( $post_id, 'woobt_sync_qty', true ) === 'on' ? 'checked' : '' ); ?>/>
									<label for="woobt_sync_qty"><?php esc_html_e( 'Sync the quantity of the main product with associated products.', 'woo-bought-together' ); ?></label>
								</td>
							</tr>
							<tr class="woobt_tr_space woobt_tr_show_if_custom_qty">
								<th><?php esc_html_e( 'Limit each item', 'woo-bought-together' ); ?></th>
								<td>
									<input id="woobt_limit_each_min_default" name="woobt_limit_each_min_default" type="checkbox" <?php echo esc_attr( get_post_meta( $post_id, 'woobt_limit_each_min_default', true ) === 'on' ? 'checked' : '' ); ?>/>
									<label for="woobt_limit_each_min_default"><?php esc_html_e( 'Use default quantity as min', 'woo-bought-together' ); ?></label>
									<u>or</u> Min
									<input name="woobt_limit_each_min" type="number" min="0" value="<?php echo esc_attr( get_post_meta( $post_id, 'woobt_limit_each_min', true ) ?: '' ); ?>" style="width: 60px; float: none"/> Max
									<input name="woobt_limit_each_max" type="number" min="1" value="<?php echo esc_attr( get_post_meta( $post_id, 'woobt_limit_each_max', true ) ?: '' ); ?>" style="width: 60px; float: none"/>
								</td>
							</tr>
							<tr class="woobt_tr_space">
								<th><?php esc_html_e( 'Displaying', 'woo-bought-together' ); ?></th>
								<td>
									<a href="#" class="woobt_displaying"><?php esc_html_e( 'Overwrite the default displaying settings', 'woo-bought-together' ); ?></a>
								</td>
							</tr>
							<tr class="woobt_tr_space woobt_show_if_displaying">
								<th><?php esc_html_e( 'Layout', 'woo-bought-together' ); ?></th>
								<td>
									<select name="woobt_layout">
										<option value="unset" <?php selected( $layout, 'unset' ); ?>><?php esc_html_e( 'Unset (default setting)', 'woo-bought-together' ); ?></option>
										<option value="default" <?php selected( $layout, 'default' ); ?>><?php esc_html_e( 'List', 'woo-bought-together' ); ?></option>
										<option value="separate" <?php selected( $layout, 'separate' ); ?>><?php esc_html_e( 'Separate images', 'woo-bought-together' ); ?></option>
										<option value="grid-2" <?php selected( $layout, 'grid-2' ); ?>><?php esc_html_e( 'Grid - 2 columns', 'woo-bought-together' ); ?></option>
										<option value="grid-3" <?php selected( $layout, 'grid-3' ); ?>><?php esc_html_e( 'Grid - 3 columns', 'woo-bought-together' ); ?></option>
										<option value="grid-4" <?php selected( $layout, 'grid-4' ); ?>><?php esc_html_e( 'Grid - 4 columns', 'woo-bought-together' ); ?></option>
									</select>
								</td>
							</tr>
							<tr class="woobt_tr_space woobt_show_if_displaying">
								<th><?php esc_html_e( 'Position', 'woo-bought-together' ); ?></th>
								<td>
									<?php
									if ( is_array( self::$positions ) && ( count( self::$positions ) > 0 ) ) {
										echo '<select name="woobt_position">';

										echo '<option value="unset" ' . ( 'unset' === $position ? 'selected' : '' ) . '>' . esc_html__( 'Unset (default setting)', 'woo-bought-together' ) . '</option>';

										foreach ( self::$positions as $k => $p ) {
											echo '<option value="' . esc_attr( $k ) . '" ' . ( $k === $position ? 'selected' : '' ) . '>' . esc_html( $p ) . '</option>';
										}

										echo '</select>';
									}
									?>
									<span class="description"><?php esc_html_e( 'Choose the position to show the products list. You also can use the shortcode [woobt] to show the list where you want.', 'woo-bought-together' ); ?></span>
								</td>
							</tr>
							<tr class="woobt_tr_space woobt_show_if_displaying">
								<th><?php esc_html_e( 'Add to cart button', 'woo-bought-together' ); ?></th>
								<td>
									<select name="woobt_atc_button" class="woobt_atc_button">
										<option value="unset" <?php selected( $atc_button, 'unset' ); ?>><?php esc_html_e( 'Unset (default setting)', 'woo-bought-together' ); ?></option>
										<option value="main" <?php selected( $atc_button, 'main' ); ?>><?php esc_html_e( 'Main product\'s button', 'woo-bought-together' ); ?></option>
										<option value="separate" <?php selected( $atc_button, 'separate' ); ?>><?php esc_html_e( 'Separate buttons', 'woo-bought-together' ); ?></option>
									</select>
								</td>
							</tr>
							<tr class="woobt_tr_space woobt_show_if_displaying">
								<th><?php esc_html_e( 'Show "this item"', 'woo-bought-together' ); ?></th>
								<td>
									<select name="woobt_show_this_item" class="woobt_show_this_item">
										<option value="unset" <?php selected( $show_this_item, 'unset' ); ?>><?php esc_html_e( 'Unset (default setting)', 'woo-bought-together' ); ?></option>
										<option value="yes" <?php selected( $show_this_item, 'yes' ); ?>><?php esc_html_e( 'Yes', 'woo-bought-together' ); ?></option>
										<option value="no" <?php selected( $show_this_item, 'no' ); ?>><?php esc_html_e( 'No', 'woo-bought-together' ); ?></option>
									</select>
									<span class="description"><?php esc_html_e( '"This item" cannot be hidden if "Separate buttons" is in use for the Add to Cart button.', 'woo-bought-together' ); ?></span>
								</td>
							</tr>
							<tr class="woobt_tr_space woobt_show_if_displaying">
								<th><?php esc_html_e( 'Above text', 'woo-bought-together' ); ?></th>
								<td>
									<div class="w100">
										<textarea name="woobt_before_text" rows="1" style="width: 100%"><?php echo stripslashes( get_post_meta( $post_id, 'woobt_before_text', true ) ); ?></textarea>
									</div>
								</td>
							</tr>
							<tr class="woobt_tr_space woobt_show_if_displaying">
								<th><?php esc_html_e( 'Under text', 'woo-bought-together' ); ?></th>
								<td>
									<div class="w100">
										<textarea name="woobt_after_text" rows="1" style="width: 100%"><?php echo stripslashes( get_post_meta( $post_id, 'woobt_after_text', true ) ); ?></textarea>
									</div>
								</td>
							</tr>
						</table>
					</div>
					<?php
				}

				function process_product_meta( $post_id ) {
					if ( isset( $_POST['woobt_ids'] ) ) {
						update_post_meta( $post_id, 'woobt_ids', self::sanitize_array( $_POST['woobt_ids'] ) );
					} else {
						delete_post_meta( $post_id, 'woobt_ids' );
					}

					if ( ! empty( $_POST['woobt_discount'] ) ) {
						update_post_meta( $post_id, 'woobt_discount', sanitize_text_field( $_POST['woobt_discount'] ) );
					} else {
						delete_post_meta( $post_id, 'woobt_discount' );
					}

					if ( isset( $_POST['woobt_checked_all'] ) ) {
						update_post_meta( $post_id, 'woobt_checked_all', 'on' );
					} else {
						update_post_meta( $post_id, 'woobt_checked_all', 'off' );
					}

					if ( isset( $_POST['woobt_separately'] ) ) {
						update_post_meta( $post_id, 'woobt_separately', 'on' );
					} else {
						update_post_meta( $post_id, 'woobt_separately', 'off' );
					}

					if ( isset( $_POST['woobt_selection'] ) ) {
						update_post_meta( $post_id, 'woobt_selection', sanitize_text_field( $_POST['woobt_selection'] ) );
					}

					if ( isset( $_POST['woobt_custom_qty'] ) ) {
						update_post_meta( $post_id, 'woobt_custom_qty', 'on' );
					} else {
						update_post_meta( $post_id, 'woobt_custom_qty', 'off' );
					}

					if ( isset( $_POST['woobt_sync_qty'] ) ) {
						update_post_meta( $post_id, 'woobt_sync_qty', 'on' );
					} else {
						update_post_meta( $post_id, 'woobt_sync_qty', 'off' );
					}

					if ( isset( $_POST['woobt_limit_each_min_default'] ) ) {
						update_post_meta( $post_id, 'woobt_limit_each_min_default', 'on' );
					} else {
						update_post_meta( $post_id, 'woobt_limit_each_min_default', 'off' );
					}

					if ( isset( $_POST['woobt_limit_each_min'] ) ) {
						update_post_meta( $post_id, 'woobt_limit_each_min', sanitize_text_field( $_POST['woobt_limit_each_min'] ) );
					}

					if ( isset( $_POST['woobt_limit_each_max'] ) ) {
						update_post_meta( $post_id, 'woobt_limit_each_max', sanitize_text_field( $_POST['woobt_limit_each_max'] ) );
					}

					// overwrite displaying

					if ( isset( $_POST['woobt_layout'] ) ) {
						update_post_meta( $post_id, 'woobt_layout', sanitize_text_field( $_POST['woobt_layout'] ) );
					}

					if ( isset( $_POST['woobt_position'] ) ) {
						update_post_meta( $post_id, 'woobt_position', sanitize_text_field( $_POST['woobt_position'] ) );
					}

					if ( isset( $_POST['woobt_atc_button'] ) ) {
						update_post_meta( $post_id, 'woobt_atc_button', sanitize_text_field( $_POST['woobt_atc_button'] ) );
					}

					if ( isset( $_POST['woobt_show_this_item'] ) ) {
						update_post_meta( $post_id, 'woobt_show_this_item', sanitize_text_field( $_POST['woobt_show_this_item'] ) );
					}

					if ( ! empty( $_POST['woobt_before_text'] ) ) {
						update_post_meta( $post_id, 'woobt_before_text', addslashes( $_POST['woobt_before_text'] ) );
					} else {
						delete_post_meta( $post_id, 'woobt_before_text' );
					}

					if ( ! empty( $_POST['woobt_after_text'] ) ) {
						update_post_meta( $post_id, 'woobt_after_text', addslashes( $_POST['woobt_after_text'] ) );
					} else {
						delete_post_meta( $post_id, 'woobt_after_text' );
					}
				}

				function ajax_import_export() {
					$ids = isset( $_POST['ids'] ) ? $_POST['ids'] : [];

					echo '<textarea class="woobt_import_export_data" style="width: 100%; height: 200px">' . ( ! empty( $ids ) ? json_encode( $ids ) : '' ) . '</textarea>';
					echo '<div style="display: flex; align-items: center"><button class="button button-primary woobt-import-export-save">' . esc_html__( 'Update', 'woo-product-timer' ) . '</button>';
					echo '<span style="color: #ff4f3b; font-size: 10px; margin-left: 10px">' . esc_html__( '* All current selected products will be replaced after pressing Update!', 'woo-product-timer' ) . '</span>';
					echo '</div>';

					wp_die();
				}

				function ajax_import_export_save() {
					$ids = sanitize_textarea_field( trim( $_POST['ids'] ) );

					if ( ! empty( $ids ) ) {
						$items = [];
						$ids   = json_decode( stripcslashes( $ids ) );

						if ( $ids !== null ) {
							foreach ( $ids as $id ) {
								if ( preg_match( '/woobt_ids\[(.*?)\]\[(.*?)\]/', $id->name, $matches ) ) {
									$key   = $matches[1];
									$field = $matches[2];

									if ( ! empty( $key ) && ! empty( $field ) ) {
										$items[ $key ][ $field ] = $id->value;
									}
								}
							}
						}

						if ( ! empty( $items ) ) {
							foreach ( $items as $item ) {
								if ( ! empty( $item['id'] ) ) {
									$item_id      = $item['id'];
									$item_price   = $item['price'];
									$item_qty     = $item['qty'];
									$item_product = wc_get_product( $item_id );

									if ( ! $item_product ) {
										continue;
									}

									self::product_data_li( $item_product, $item_price, $item_qty, false );
								} else {
									// new version 5.0
									self::text_data_li( $item );
								}
							}
						}
					}

					wp_die();
				}

				function product_price_class( $class ) {
					global $product;

					return $class . ' woobt-price-' . $product->get_id();
				}

				function show_items_position( $pos = 'before' ) {
					global $product;

					if ( ! $product || ! is_a( $product, 'WC_Product' ) ) {
						return;
					}

					$_position = get_post_meta( $product->get_id(), 'woobt_position', true ) ?: 'unset';
					$position  = $_position !== 'unset' ? $_position : apply_filters( 'woobt_position', self::get_setting( 'position', apply_filters( 'woobt_default_position', 'before' ) ) );

					if ( $position === $pos ) {
						self::show_items();
					}
				}

				function show_items_before_atc() {
					self::show_items_position( 'before' );
				}

				function show_items_after_atc() {
					self::show_items_position( 'after' );
				}

				function show_items_below_title() {
					self::show_items_position( 'below_title' );
				}

				function show_items_below_price() {
					self::show_items_position( 'below_price' );
				}

				function show_items_below_excerpt() {
					self::show_items_position( 'below_excerpt' );
				}

				function show_items_below_meta() {
					self::show_items_position( 'below_meta' );
				}

				function show_items_below_summary() {
					self::show_items_position( 'below_summary' );
				}

				function add_to_cart_button() {
					global $product;

					if ( ! $product || ! is_a( $product, 'WC_Product' ) || $product->is_type( 'grouped' ) || $product->is_type( 'external' ) ) {
						return;
					}

					$product_id = $product->get_id();

					$_position   = get_post_meta( $product_id, 'woobt_position', true ) ?: 'unset';
					$_atc_button = get_post_meta( $product_id, 'woobt_atc_button', true ) ?: 'unset';
					$position    = $_position !== 'unset' ? $_position : apply_filters( 'woobt_position', self::get_setting( 'position', apply_filters( 'woobt_default_position', 'before' ) ) );
					$atc_button  = $_atc_button !== 'unset' ? $_atc_button : self::get_setting( 'atc_button', 'main' );

					if ( ( $atc_button === 'main' ) && ( $position !== 'none' ) ) {
						echo '<input name="woobt_ids" class="woobt-ids woobt-ids-' . esc_attr( $product->get_id() ) . '" data-id="' . esc_attr( $product->get_id() ) . '" type="hidden"/>';
					}
				}

				function has_variables( $items ) {
					foreach ( $items as $item ) {
						if ( is_array( $item ) && isset( $item['id'] ) ) {
							$item_id = $item['id'];
						} else {
							$item_id = absint( $item );
						}

						$item_product = wc_get_product( $item_id );

						if ( ! $item_product ) {
							continue;
						}

						if ( $item_product->is_type( 'variable' ) ) {
							return true;
						}
					}

					return false;
				}

				function shortcode( $attrs ) {
					$attrs = shortcode_atts( [ 'id' => null, 'custom_position' => true ], $attrs );

					ob_start();

					self::show_items( $attrs['id'], wc_string_to_bool( $attrs['custom_position'] ) );

					return ob_get_clean();
				}

				function show_items( $product = null, $is_custom_position = false ) {
					$product_id = 0;

					if ( ! $product ) {
						global $product;

						if ( $product ) {
							$product_id = $product->get_id();
						}
					} else {
						if ( is_a( $product, 'WC_Product' ) ) {
							$product_id = $product->get_id();
						}

						if ( is_numeric( $product ) ) {
							$product_id = absint( $product );
							$product    = wc_get_product( $product_id );
						}
					}

					if ( ! $product_id || ! $product || $product->is_type( 'grouped' ) || $product->is_type( 'external' ) ) {
						return;
					}

					wp_enqueue_script( 'wc-add-to-cart-variation' );

					$custom_qty      = apply_filters( 'woobt_custom_qty', get_post_meta( $product_id, 'woobt_custom_qty', true ) === 'on', $product_id );
					$sync_qty        = apply_filters( 'woobt_sync_qty', get_post_meta( $product_id, 'woobt_sync_qty', true ) === 'on', $product_id );
					$separately      = apply_filters( 'woobt_separately', get_post_meta( $product_id, 'woobt_separately', true ) === 'on', $product_id );
					$selection       = apply_filters( 'woobt_selection', get_post_meta( $product_id, 'woobt_selection', true ) ?: 'multiple', $product_id );
					$_position       = get_post_meta( $product_id, 'woobt_position', true ) ?: 'unset';
					$_layout         = get_post_meta( $product_id, 'woobt_layout', true ) ?: 'unset';
					$_atc_button     = get_post_meta( $product_id, 'woobt_atc_button', true ) ?: 'unset';
					$_show_this_item = get_post_meta( $product_id, 'woobt_show_this_item', true ) ?: 'unset';

					// settings
					$default            = apply_filters( 'woobt_default', self::get_setting( 'default', [ 'default' ] ) );
					$default_limit      = (int) apply_filters( 'woobt_default_limit', self::get_setting( 'default_limit', 0 ) );
					$pricing            = self::get_setting( 'pricing', 'sale_price' );
					$position           = $_position !== 'unset' ? $_position : apply_filters( 'woobt_position', self::get_setting( 'position', apply_filters( 'woobt_default_position', 'before' ) ) );
					$layout             = $_layout !== 'unset' ? $_layout : self::get_setting( 'layout', 'default' );
					$show_this_item     = $_show_this_item !== 'unset' ? $_show_this_item : self::get_setting( 'show_this_item', 'yes' );
					$atc_button         = $_atc_button !== 'unset' ? $_atc_button : self::get_setting( 'atc_button', 'main' );
					$is_separate_atc    = $atc_button === 'separate';
					$is_separate_layout = $layout === 'separate';

					$items = [];

					// backward compatibility before 5.1.1
					if ( ! is_array( $default ) ) {
						switch ( (string) $default ) {
							case 'upsells':
								$default = [ 'upsells' ];
								break;
							case 'related':
								$default = [ 'related' ];
								break;
							case 'related_upsells':
								$default = [ 'upsells', 'related' ];
								break;
							case 'none':
								$default = [];
								break;
							default:
								$default = [];
						}
					}

					if ( $ids = self::get_ids( $product_id ) ) {
						$items = self::get_items( $ids, $product_id );
					}

					if ( ! $items && is_array( $default ) && ! empty( $default ) ) {
						$items = [];

						if ( in_array( 'related', $default ) ) {
							$items = array_merge( $items, wc_get_related_products( $product_id ) );
						}

						if ( in_array( 'upsells', $default ) ) {
							$items = array_merge( $items, $product->get_upsell_ids() );
						}

						if ( in_array( 'crosssells', $default ) ) {
							$items = array_merge( $items, $product->get_cross_sell_ids() );
						}

						if ( $default_limit ) {
							$items = array_slice( $items, 0, $default_limit );
						}
					}

					// filter items before showing
					$items = apply_filters( 'woobt_show_items', $items, $product_id );

					if ( ! empty( $items ) ) {
						foreach ( $items as $key => $item ) {
							if ( is_array( $item ) ) {
								if ( ! empty( $item['id'] ) ) {
									$_item['id']    = $item['id'];
									$_item['price'] = $item['price'];
									$_item['qty']   = $item['qty'];
								} else {
									// heading/paragraph
									$_item = $item;
								}
							} else {
								// make it works with upsells & related
								$_item['id']    = absint( $item );
								$_item['price'] = '100%';
								$_item['qty']   = 1;
							}

							if ( ! empty( $_item['id'] ) ) {
								if ( $_item_product = wc_get_product( $_item['id'] ) ) {
									$_item['product'] = $_item_product;
								} else {
									unset( $items[ $key ] );
									continue;
								}
							}

							if ( ! empty( $_item['product'] ) && ( ! in_array( $_item['product']->get_type(), self::$types, true ) || ( ( self::get_setting( 'exclude_unpurchasable', 'no' ) === 'yes' ) && ( ! $_item['product']->is_purchasable() || ! $_item['product']->is_in_stock() ) ) ) ) {
								unset( $items[ $key ] );
								continue;
							}

							$items[ $key ] = $_item;
						}

						if ( ! empty( $items ) ) {
							$wrap_class = 'woobt-wrap woobt-layout-' . esc_attr( $layout ) . ' woobt-wrap-' . esc_attr( $product_id ) . ' ' . ( self::get_setting( 'responsive', 'yes' ) === 'yes' ? 'woobt-wrap-responsive' : '' );

							if ( $is_custom_position ) {
								$wrap_class .= ' woobt-wrap-custom-position';
							}

							if ( $is_separate_atc ) {
								$wrap_class .= ' woobt-wrap-separate-atc';
							}

							do_action( 'woobt_wrap_above', $product );

							echo '<div class="' . esc_attr( $wrap_class ) . '" data-id="' . esc_attr( $product_id ) . '" data-selection="' . esc_attr( $selection ) . '" data-position="' . esc_attr( $position ) . '" data-this-item="' . esc_attr( $show_this_item ) . '" data-layout="' . esc_attr( $layout ) . '" data-atc-button="' . esc_attr( $atc_button ) . '">';

							do_action( 'woobt_wrap_before', $product );

							if ( $before_text = apply_filters( 'woobt_before_text', get_post_meta( $product_id, 'woobt_before_text', true ) ?: self::localization( 'above_text' ), $product_id ) ) {
								do_action( 'woobt_before_text_above', $product );
								echo '<div class="woobt-before-text woobt-text">' . do_shortcode( stripslashes( $before_text ) ) . '</div>';
								do_action( 'woobt_before_text_below', $product );
							}

							if ( $is_separate_layout ) {
								do_action( 'woobt_images_above', $product );
								?>
								<div class="woobt-images">
									<?php
									do_action( 'woobt_images_before', $product );

									echo '<div class="woobt-image woobt-image-this woobt-image-order-0 woobt-image-' . esc_attr( $product_id ) . '">';
									do_action( 'woobt_product_thumb_before', $product, 0, 'separate' );
									echo '<span class="woobt-img woobt-img-order-0" data-img="' . esc_attr( htmlentities( $product->get_image( self::$image_size ) ) ) . '">' . $product->get_image( self::$image_size ) . '</span>';
									do_action( 'woobt_product_thumb_after', $product, 0, 'separate' );
									echo '</div>';

									$order = 1;

									foreach ( $items as $item ) {
										if ( ! empty( $item['id'] ) ) {
											$item_product = $item['product'];

											echo '<div class="woobt-image woobt-image-order-' . $order . ' woobt-image-' . esc_attr( $item['id'] ) . '">';

											do_action( 'woobt_product_thumb_before', $item_product, $order, 'separate' );

											if ( self::get_setting( 'link', 'yes' ) !== 'no' ) {
												echo '<a class="' . esc_attr( self::get_setting( 'link', 'yes' ) === 'yes_popup' ? 'woosq-link woobt-img woobt-img-order-' . $order : 'woobt-img woobt-img-order-' . $order ) . '" data-id="' . esc_attr( $item['id'] ) . '" data-context="woobt" href="' . $item_product->get_permalink() . '" data-img="' . esc_attr( htmlentities( $item_product->get_image( self::$image_size ) ) ) . '" ' . ( self::get_setting( 'link', 'yes' ) === 'yes_blank' ? 'target="_blank"' : '' ) . '>' . $item_product->get_image( self::$image_size ) . '</a>';
											} else {
												echo '<span class="' . esc_attr( 'woobt-img woobt-img-order-' . $order ) . '" data-img="' . esc_attr( htmlentities( $item_product->get_image( self::$image_size ) ) ) . '">' . $item_product->get_image( self::$image_size ) . '</span>';
											}

											do_action( 'woobt_product_thumb_after', $item_product, $order, 'separate' );

											echo '</div>';
											$order ++;
										}
									}

									do_action( 'woobt_images_after', $product );
									?>
								</div>
								<?php
								do_action( 'woobt_images_below', $product );
							}

							$sku            = $product->get_sku();
							$weight         = htmlentities( wc_format_weight( $product->get_weight() ) );
							$dimensions     = htmlentities( wc_format_dimensions( $product->get_dimensions( false ) ) );
							$price_html     = htmlentities( $product->get_price_html() );
							$products_class = apply_filters( 'woobt_products_class', 'woobt-products woobt-products-layout-' . $layout . ' woobt-products-' . $product_id, $product );

							do_action( 'woobt_products_above', $product );
							?>
							<div class="<?php echo esc_attr( $products_class ); ?>" data-show-price="<?php echo esc_attr( self::get_setting( 'show_price', 'yes' ) ); ?>" data-optional="<?php echo esc_attr( $custom_qty ? 'on' : 'off' ); ?>" data-sync-qty="<?php echo esc_attr( $sync_qty ? 'on' : 'off' ); ?>" data-variables="<?php echo esc_attr( self::has_variables( $items ) ? 'yes' : 'no' ); ?>" data-product-id="<?php echo esc_attr( $product->is_type( 'variable' ) ? '0' : $product_id ); ?>" data-product-type="<?php echo esc_attr( $product->get_type() ); ?>" data-product-price-suffix="<?php echo esc_attr( htmlentities( $product->get_price_suffix() ) ); ?>" data-product-price-html="<?php echo esc_attr( $price_html ); ?>" data-product-o_price-html="<?php echo esc_attr( $price_html ); ?>" data-pricing="<?php echo esc_attr( $pricing ); ?>" data-discount="<?php echo esc_attr( ! $separately && get_post_meta( $product_id, 'woobt_discount', true ) ? get_post_meta( $product_id, 'woobt_discount', true ) : '0' ); ?>" data-product-sku="<?php echo esc_attr( $sku ); ?>" data-product-o_sku="<?php echo esc_attr( $sku ); ?>" data-product-weight="<?php echo esc_attr( $weight ); ?>" data-product-o_weight="<?php echo esc_attr( $weight ); ?>" data-product-dimensions="<?php echo esc_attr( $dimensions ); ?>" data-product-o_dimensions="<?php echo esc_attr( $dimensions ); ?>">
								<?php
								do_action( 'woobt_products_before', $product );

								// this item
								echo self::product_this_output( $product, $show_this_item !== 'no', $is_custom_position, $is_separate_atc, $is_separate_layout );

								// other items
								$order = 1;

								$global_product = $product;

								foreach ( $items as $item ) {
									if ( ! empty( $item['id'] ) ) {
										echo self::product_output( $item, $product_id, $order );
										$order ++;
									} else {
										// heading/paragraph
										echo self::text_output( $item, $product_id );
									}
								}

								$product = $global_product;

								do_action( 'woobt_products_after', $product );
								?>
							</div><!-- /woobt-products -->
							<?php
							do_action( 'woobt_products_below', $product );

							do_action( 'woobt_extra_above', $product );

							echo '<div class="woobt-additional woobt-text"></div>';

							do_action( 'woobt_total_above', $product );

							echo '<div class="woobt-total woobt-text"></div>';

							do_action( 'woobt_alert_above', $product );

							echo '<div class="woobt-alert woobt-text"></div>';

							if ( $after_text = apply_filters( 'woobt_after_text', get_post_meta( $product_id, 'woobt_after_text', true ) ?: self::localization( 'under_text' ), $product_id ) ) {
								do_action( 'woobt_after_text_above', $product );
								echo '<div class="woobt-after-text woobt-text">' . do_shortcode( stripslashes( $after_text ) ) . '</div>';
								do_action( 'woobt_after_text_below', $product );
							}

							if ( $is_custom_position || $is_separate_atc ) {
								do_action( 'woobt_actions_above', $product );
								echo '<div class="woobt-actions">';
								do_action( 'woobt_actions_before', $product );
								echo '<div class="woobt-form">';
								echo '<input type="hidden" name="woobt_ids" class="woobt-ids woobt-ids-' . esc_attr( $product->get_id() ) . '" data-id="' . esc_attr( $product->get_id() ) . '"/>';
								echo '<input type="hidden" name="quantity" value="1"/>';
								echo '<input type="hidden" name="product_id" value="' . esc_attr( $product_id ) . '">';
								echo '<input type="hidden" name="variation_id" class="variation_id" value="0">';
								echo '<button type="submit" class="single_add_to_cart_button button alt">' . self::localization( 'add_all_to_cart', esc_html__( 'Add all to cart', 'woo-bought-together' ) ) . '</button>';
								echo '</div>';
								do_action( 'woobt_actions_after', $product );
								echo '</div><!-- /woobt-actions -->';
								do_action( 'woobt_actions_below', $product );
							}

							do_action( 'woobt_extra_below', $product );

							do_action( 'woobt_wrap_after', $product );

							echo '</div><!-- /woobt-wrap -->';

							do_action( 'woobt_wrap_below', $product );
						}
					}
				}

				function product_this_output( $product, $show_this_item = false, $is_custom_position = false, $is_separate_atc = false, $is_separate_layout = false ) {
					$hide_this          = ! $is_custom_position && ! $is_separate_atc && ! $show_this_item;
					$product_id         = $product->get_id();
					$this_item_quantity = apply_filters( 'woobt_this_item_quantity', false, $product );
					$product_name       = apply_filters( 'woobt_product_get_name', $product->get_name(), $product );
					$custom_qty         = apply_filters( 'woobt_custom_qty', get_post_meta( $product_id, 'woobt_custom_qty', true ) === 'on', $product_id );
					$separately         = apply_filters( 'woobt_separately', get_post_meta( $product_id, 'woobt_separately', true ) === 'on', $product_id );
					$plus_minus         = self::get_setting( 'plus_minus', 'no' ) === 'yes';

					ob_start();

					if ( $hide_this ) {
						?>
						<div class="woobt-product woobt-product-this woobt-hide-this" data-order="0" data-qty="1" data-id="<?php echo esc_attr( $product->is_type( 'variable' ) || ! $product->is_in_stock() ? 0 : $product_id ); ?>" data-pid="<?php echo esc_attr( $product_id ); ?>" data-name="<?php echo esc_attr( $product_name ); ?>" data-price="<?php echo esc_attr( apply_filters( 'woobt_item_data_price', wc_get_price_to_display( $product ), $product ) ); ?>" data-regular-price="<?php echo esc_attr( apply_filters( 'woobt_item_data_regular_price', wc_get_price_to_display( $product, [ 'price' => $product->get_regular_price() ] ), $product ) ); ?>">
							<div class="woobt-choose">
								<label for="woobt_checkbox_0"><?php echo esc_html( $product_name ); ?></label>
								<input id="woobt_checkbox_0" class="woobt-checkbox woobt-checkbox-this" type="checkbox" checked disabled/>
								<span class="checkmark"></span>
							</div>
						</div>
					<?php } else { ?>
						<div class="woobt-product woobt-product-this" data-order="0" data-qty="1" data-o_qty="1" data-id="<?php echo esc_attr( $product->is_type( 'variable' ) || ! $product->is_in_stock() ? 0 : $product_id ); ?>" data-pid="<?php echo esc_attr( $product_id ); ?>" data-name="<?php echo esc_attr( $product_name ); ?>" data-new-price="<?php echo esc_attr( ! $separately && ( $discount = get_post_meta( $product_id, 'woobt_discount', true ) ) ? ( 100 - (float) $discount ) . '%' : '100%' ); ?>" data-price-suffix="<?php echo esc_attr( htmlentities( $product->get_price_suffix() ) ); ?>" data-price="<?php echo esc_attr( apply_filters( 'woobt_item_data_price', wc_get_price_to_display( $product ), $product ) ); ?>" data-regular-price="<?php echo esc_attr( apply_filters( 'woobt_item_data_regular_price', wc_get_price_to_display( $product, [ 'price' => $product->get_regular_price() ] ), $product ) ); ?>">

							<?php do_action( 'woobt_product_before', $product ); ?>

							<div class="woobt-choose">
								<label for="woobt_checkbox_0"><?php echo esc_html( $product_name ); ?></label>
								<input id="woobt_checkbox_0" class="woobt-checkbox woobt-checkbox-this" type="checkbox" checked disabled/>
								<span class="checkmark"></span>
							</div>

							<?php if ( ! $is_separate_layout && ( self::get_setting( 'show_thumb', 'yes' ) !== 'no' ) ) {
								echo '<div class="woobt-thumb">';
								do_action( 'woobt_product_thumb_before', $product, 0, 'default' );
								echo '<span class="woobt-img woobt-img-order-0" data-img="' . esc_attr( htmlentities( $product->get_image( self::$image_size ) ) ) . '">' . $product->get_image( self::$image_size ) . '</span>';
								do_action( 'woobt_product_thumb_after', $product, 0, 'default' );
								echo '</div>';
							} ?>

							<div class="woobt-title">
                                <span class="woobt-title-inner">
                                    <?php echo '<span>' . self::localization( 'this_item', esc_html__( 'This item:', 'woo-bought-together' ) ) . '</span> <span>' . apply_filters( 'woobt_product_get_name', $product->get_name(), $product ) . '</span>'; ?>
                                </span>

								<?php if ( $is_separate_layout && ( self::get_setting( 'show_price', 'yes' ) !== 'no' ) ) { ?>
									<span class="woobt-price">
                                        <span class="woobt-price-new">
                                            <?php
                                            if ( ! $separately && ( $discount = get_post_meta( $product_id, 'woobt_discount', true ) ) ) {
	                                            $sale_price = $product->get_price() * ( 100 - (float) $discount ) / 100;
	                                            echo wc_format_sale_price( $product->get_price(), $sale_price ) . $product->get_price_suffix( $sale_price );
                                            } else {
	                                            echo $product->get_price_html();
                                            }
                                            ?>
                                        </span>
                                        <span class="woobt-price-ori">
                                            <?php echo $product->get_price_html(); ?>
                                        </span>
                                    </span>
								<?php } ?>

								<?php
								if ( ( $is_separate_atc || $is_custom_position ) && $product->is_type( 'variable' ) ) {
									if ( ( self::get_setting( 'variations_selector', 'default' ) === 'woovr' ) && class_exists( 'WPClever_Woovr' ) ) {
										echo '<div class="wpc_variations_form">';
										// use class name wpc_variations_form to prevent found_variation in woovr
										WPClever_Woovr::woovr_variations_form( $product, false, 'woobt' );
										echo '</div>';
									} else {
										$attributes           = $product->get_variation_attributes();
										$available_variations = $product->get_available_variations();

										if ( is_array( $attributes ) && ( count( $attributes ) > 0 ) ) {
											echo '<div class="variations_form" data-product_id="' . absint( $product_id ) . '" data-product_variations="' . htmlspecialchars( wp_json_encode( $available_variations ) ) . '">';
											echo '<div class="variations">';

											foreach ( $attributes as $attribute_name => $options ) { ?>
												<div class="variation">
													<div class="label">
														<?php echo wc_attribute_label( $attribute_name ); ?>
													</div>
													<div class="select">
														<?php
														$selected = isset( $_REQUEST[ 'attribute_' . sanitize_title( $attribute_name ) ] ) ? wc_clean( stripslashes( urldecode( $_REQUEST[ 'attribute_' . sanitize_title( $attribute_name ) ] ) ) ) : $product->get_variation_default_attribute( $attribute_name );
														wc_dropdown_variation_attribute_options( [
															'options'          => $options,
															'attribute'        => $attribute_name,
															'product'          => $product,
															'selected'         => $selected,
															'show_option_none' => sprintf( self::localization( 'choose', esc_html__( 'Choose %s', 'woo-bought-together' ) ), wc_attribute_label( $attribute_name ) )
														] );
														?>
													</div>
												</div>
											<?php }

											echo '<div class="reset">' . apply_filters( 'woocommerce_reset_variations_link', '<a class="reset_variations" href="#">' . self::localization( 'clear', esc_html__( 'Clear', 'woo-bought-together' ) ) . '</a>' ) . '</div>';
											echo '</div>';
											echo '</div>';

											if ( self::get_setting( 'show_description', 'no' ) === 'yes' ) {
												echo '<div class="woobt-variation-description"></div>';
											}
										}
									}
								}

								echo '<div class="woobt-availability">' . wc_get_stock_html( $product ) . '</div>';
								?>
							</div>

							<?php if ( ( $is_separate_atc || $is_custom_position || $this_item_quantity ) && $custom_qty ) {
								echo '<div class="' . esc_attr( ( $plus_minus ? 'woobt-quantity woobt-quantity-plus-minus' : 'woobt-quantity' ) ) . '">';

								if ( $plus_minus ) {
									echo '<div class="woobt-quantity-input">';
									echo '<div class="woobt-quantity-input-minus">-</div>';
								}

								woocommerce_quantity_input( [
									'input_name' => 'woobt_qty_0',
									'classes'    => [
										'input-text',
										'woobt-qty',
										'woobt-this-qty',
										'qty',
										'text'
									]
								], $product );

								if ( $plus_minus ) {
									echo '<div class="woobt-quantity-input-plus">+</div>';
									echo '</div>';
								}

								echo '</div>';
							}

							if ( ! $is_separate_layout && ( self::get_setting( 'show_price', 'yes' ) !== 'no' ) ) { ?>
								<div class="woobt-price">
									<div class="woobt-price-new">
										<?php
										if ( ! $separately && ( $discount = get_post_meta( $product_id, 'woobt_discount', true ) ) ) {
											$sale_price = $product->get_price() * ( 100 - (float) $discount ) / 100;
											echo wc_format_sale_price( $product->get_price(), $sale_price ) . $product->get_price_suffix( $sale_price );
										} else {
											echo $product->get_price_html();
										}
										?>
									</div>
									<div class="woobt-price-ori">
										<?php echo $product->get_price_html(); ?>
									</div>
								</div>
							<?php }

							do_action( 'woobt_product_after', $product );
							?>
						</div>
						<?php
					}

					return apply_filters( 'woobt_product_this_output', ob_get_clean(), $product, $is_custom_position );
				}

				function product_output( $item, $product_id = 0, $order = 1 ) {
					global $product;
					$product            = $item['product'];
					$item_id            = $item['id'];
					$item_price         = $item['price'];
					$item_qty           = $item['qty'];
					$item_qty_min       = 1;
					$item_qty_max       = 1000;
					$pricing            = self::get_setting( 'pricing', 'sale_price' );
					$custom_qty         = apply_filters( 'woobt_custom_qty', get_post_meta( $product_id, 'woobt_custom_qty', true ) === 'on', $product_id );
					$checked_all        = apply_filters( 'woobt_checked_all', get_post_meta( $product_id, 'woobt_checked_all', true ) === 'on', $product_id );
					$separately         = apply_filters( 'woobt_separately', get_post_meta( $product_id, 'woobt_separately', true ) === 'on', $product_id );
					$plus_minus         = self::get_setting( 'plus_minus', 'no' ) === 'yes';
					$layout             = self::get_setting( 'layout', 'default' );
					$is_separate_layout = $layout === 'separate';

					if ( $custom_qty ) {
						if ( get_post_meta( $product_id, 'woobt_limit_each_min_default', true ) === 'on' ) {
							$item_qty_min = $item_qty;
						} else {
							$item_qty_min = absint( get_post_meta( $product_id, 'woobt_limit_each_min', true ) ?: 0 );
						}

						$item_qty_max = absint( get_post_meta( $product_id, 'woobt_limit_each_max', true ) ?: 1000 );

						if ( $item_qty < $item_qty_min ) {
							$item_qty = $item_qty_min;
						}

						if ( $item_qty > $item_qty_max ) {
							$item_qty = $item_qty_max;
						}
					}

					$checked_individual = apply_filters( 'woobt_checked_individual', false, $item_id, $product_id );
					$item_price         = apply_filters( 'woobt_item_price', ! $separately ? $item_price : '100%', $item_id, $product_id );
					$item_name          = apply_filters( 'woobt_product_get_name', $product->get_name(), $product );

					ob_start();
					?>
					<div class="woobt-item-product woobt-product woobt-product-together" data-order="<?php echo esc_attr( $order ); ?>" data-id="<?php echo esc_attr( $product->is_type( 'variable' ) || ! $product->is_in_stock() ? 0 : $item_id ); ?>" data-pid="<?php echo esc_attr( $item_id ); ?>" data-name="<?php echo esc_attr( $item_name ); ?>" data-new-price="<?php echo esc_attr( $item_price ); ?>" data-price-suffix="<?php echo esc_attr( htmlentities( $product->get_price_suffix() ) ); ?>" data-price="<?php echo esc_attr( apply_filters( 'woobt_item_data_price', ( $pricing === 'sale_price' ) ? wc_get_price_to_display( $product ) : wc_get_price_to_display( $product, [ 'price' => $product->get_regular_price() ] ), $product ) ); ?>" data-regular-price="<?php echo esc_attr( apply_filters( 'woobt_item_data_regular_price', wc_get_price_to_display( $product, [ 'price' => $product->get_regular_price() ] ), $product ) ); ?>" data-qty="<?php echo esc_attr( $item_qty ); ?>" data-o_qty="<?php echo esc_attr( $item_qty ); ?>">

						<?php do_action( 'woobt_product_before', $product, $order ); ?>

						<div class="woobt-choose">
							<label for="<?php echo esc_attr( 'woobt_checkbox_' . $order ); ?>"><?php echo esc_html( $item_name ); ?></label>
							<input id="<?php echo esc_attr( 'woobt_checkbox_' . $order ); ?>" class="woobt-checkbox" type="checkbox" value="<?php echo esc_attr( $item_id ); ?>" <?php echo esc_attr( ! $product->is_in_stock() ? 'disabled' : '' ); ?> <?php echo esc_attr( $product->is_in_stock() && ( $checked_all || $checked_individual ) ? 'checked' : '' ); ?>/>
							<span class="checkmark"></span>
						</div>

						<?php if ( ! $is_separate_layout && ( self::get_setting( 'show_thumb', 'yes' ) !== 'no' ) ) {
							echo '<div class="woobt-thumb">';

							do_action( 'woobt_product_thumb_before', $product, $order, 'default' );

							if ( self::get_setting( 'link', 'yes' ) !== 'no' ) {
								echo '<a class="' . esc_attr( self::get_setting( 'link', 'yes' ) === 'yes_popup' ? 'woosq-link woobt-img woobt-img-order-' . $order : 'woobt-img woobt-img-order-' . $order ) . '" data-id="' . esc_attr( $item_id ) . '" data-context="woobt" href="' . $product->get_permalink() . '" data-img="' . esc_attr( htmlentities( $product->get_image( self::$image_size ) ) ) . '" ' . ( self::get_setting( 'link', 'yes' ) === 'yes_blank' ? 'target="_blank"' : '' ) . '>' . $product->get_image( self::$image_size ) . '</a>';
							} else {
								echo '<span class="woobt-img" data-img="' . esc_attr( htmlentities( $product->get_image( self::$image_size ) ) ) . '">' . $product->get_image( self::$image_size ) . '</span>';
							}

							do_action( 'woobt_product_thumb_after', $product, $order, 'default' );

							echo '</div>';
						} ?>

						<div class="woobt-title">
                            <span class="woobt-title-inner">
                                <?php
                                do_action( 'woobt_product_name_before', $product, $order );

                                if ( ! $custom_qty ) {
	                                $product_qty = '<span class="woobt-qty-num"><span class="woobt-qty">' . $item_qty . '</span> × </span>';
                                } else {
	                                $product_qty = '';
                                }

                                echo apply_filters( 'woobt_product_qty', $product_qty, $item_qty, $product );

                                if ( $product->is_in_stock() ) {
	                                $product_name = apply_filters( 'woobt_product_get_name', $product->get_name(), $product );
                                } else {
	                                $product_name = '<s>' . apply_filters( 'woobt_product_get_name', $product->get_name(), $product ) . '</s>';
                                }

                                if ( self::get_setting( 'link', 'yes' ) !== 'no' ) {
	                                $product_name = '<a ' . ( self::get_setting( 'link', 'yes' ) === 'yes_popup' ? 'class="woosq-link" data-id="' . $item_id . '" data-context="woobt"' : '' ) . ' href="' . $product->get_permalink() . '" ' . ( self::get_setting( 'link', 'yes' ) === 'yes_blank' ? 'target="_blank"' : '' ) . '>' . $product_name . '</a>';
                                } else {
	                                $product_name = '<span>' . $product_name . '</span>';
                                }

                                echo apply_filters( 'woobt_product_name', $product_name, $product );

                                do_action( 'woobt_product_name_after', $product, $order );
                                ?>
                            </span>

							<?php if ( $is_separate_layout && ( self::get_setting( 'show_price', 'yes' ) !== 'no' ) ) { ?>
								<span class="woobt-price">
                                    <?php do_action( 'woobt_product_price_before', $product, $order ); ?>
                                    <span class="woobt-price-new"></span>
                                    <span class="woobt-price-ori">
                                        <?php
                                        if ( ! $separately && ( $item_price !== '100%' ) ) {
	                                        if ( $product->is_type( 'variable' ) ) {
		                                        $item_ori_price_min = ( $pricing === 'sale_price' ) ? $product->get_variation_price( 'min', true ) : $product->get_variation_regular_price( 'min', true );
		                                        $item_ori_price_max = ( $pricing === 'sale_price' ) ? $product->get_variation_price( 'max', true ) : $product->get_variation_regular_price( 'max', true );
		                                        $item_new_price_min = self::new_price( $item_ori_price_min, $item_price );
		                                        $item_new_price_max = self::new_price( $item_ori_price_max, $item_price );

		                                        if ( $item_new_price_min < $item_new_price_max ) {
			                                        $product_price = wc_format_price_range( $item_new_price_min, $item_new_price_max );
		                                        } else {
			                                        $product_price = wc_format_sale_price( $item_ori_price_min, $item_new_price_min );
		                                        }
	                                        } else {
		                                        $item_ori_price = ( $pricing === 'sale_price' ) ? wc_get_price_to_display( $product, [ 'price' => $product->get_price() ] ) : wc_get_price_to_display( $product, [ 'price' => $product->get_regular_price() ] );
		                                        $item_new_price = self::new_price( $item_ori_price, $item_price );

		                                        if ( $item_new_price < $item_ori_price ) {
			                                        $product_price = wc_format_sale_price( $item_ori_price, $item_new_price );
		                                        } else {
			                                        $product_price = wc_price( $item_new_price );
		                                        }
	                                        }

	                                        $product_price .= $product->get_price_suffix();
                                        } else {
	                                        $product_price = $product->get_price_html();
                                        }

                                        echo apply_filters( 'woobt_product_price', $product_price, $product, $item );
                                        ?>
                                    </span>
                                    <?php do_action( 'woobt_product_price_after', $product, $order ); ?>
                                </span>
								<?php
							}

							if ( self::get_setting( 'show_description', 'no' ) === 'yes' ) {
								echo '<div class="woobt-description">' . apply_filters( 'woobt_product_short_description', $product->get_short_description(), $product ) . '</div>';
							}

							echo '<div class="woobt-availability">' . apply_filters( 'woobt_product_availability', wc_get_stock_html( $product ), $product ) . '</div>';
							?>
						</div>

						<?php if ( $custom_qty ) {
							echo '<div class="' . esc_attr( ( $plus_minus ? 'woobt-quantity woobt-quantity-plus-minus' : 'woobt-quantity' ) ) . '">';

							do_action( 'woobt_product_qty_before', $product, $order );

							if ( $plus_minus ) {
								echo '<div class="woobt-quantity-input">';
								echo '<div class="woobt-quantity-input-minus">-</div>';
							}

							woocommerce_quantity_input( [
								'classes'     => [ 'input-text', 'woobt-qty', 'qty', 'text' ],
								'input_value' => $item_qty,
								'min_value'   => $item_qty_min,
								'max_value'   => $item_qty_max,
								'input_name'  => 'woobt_qty_' . $order,
								'woobt_qty'   => [
									'input_value' => $item_qty,
									'min_value'   => $item_qty_min,
									'max_value'   => $item_qty_max
								]
								// compatible with WPC Product Quantity
							], $product );

							if ( $plus_minus ) {
								echo '<div class="woobt-quantity-input-plus">+</div>';
								echo '</div>';
							}

							do_action( 'woobt_product_qty_after', $product, $order );

							echo '</div>';
						}

						if ( ! $is_separate_layout && ( self::get_setting( 'show_price', 'yes' ) !== 'no' ) ) { ?>
							<div class="woobt-price">
								<?php do_action( 'woobt_product_price_before', $product, $order ); ?>
								<div class="woobt-price-new"></div>
								<div class="woobt-price-ori">
									<?php
									if ( ! $separately && ( $item_price !== '100%' ) ) {
										if ( $product->is_type( 'variable' ) ) {
											$item_ori_price_min = ( $pricing === 'sale_price' ) ? $product->get_variation_price( 'min', true ) : $product->get_variation_regular_price( 'min', true );
											$item_ori_price_max = ( $pricing === 'sale_price' ) ? $product->get_variation_price( 'max', true ) : $product->get_variation_regular_price( 'max', true );
											$item_new_price_min = self::new_price( $item_ori_price_min, $item_price );
											$item_new_price_max = self::new_price( $item_ori_price_max, $item_price );

											if ( $item_new_price_min < $item_new_price_max ) {
												$product_price = wc_format_price_range( $item_new_price_min, $item_new_price_max );
											} else {
												$product_price = wc_format_sale_price( $item_ori_price_min, $item_new_price_min );
											}
										} else {
											$item_ori_price = ( $pricing === 'sale_price' ) ? wc_get_price_to_display( $product, [ 'price' => $product->get_price() ] ) : wc_get_price_to_display( $product, [ 'price' => $product->get_regular_price() ] );
											$item_new_price = self::new_price( $item_ori_price, $item_price );

											if ( $item_new_price < $item_ori_price ) {
												$product_price = wc_format_sale_price( $item_ori_price, $item_new_price );
											} else {
												$product_price = wc_price( $item_new_price );
											}
										}

										$product_price .= $product->get_price_suffix();
									} else {
										$product_price = $product->get_price_html();
									}

									echo apply_filters( 'woobt_product_price', $product_price, $product, $item );
									?>
								</div>
								<?php do_action( 'woobt_product_price_after', $product, $order ); ?>
							</div>
						<?php }

						do_action( 'woobt_product_after', $product, $order );
						?>
					</div>
					<?php

					return apply_filters( 'woobt_product_output', ob_get_clean(), $item, $product_id, $order );
				}

				function text_output( $item, $product_id = 0 ) {
					ob_start();

					if ( ! empty( $item['text'] ) ) {
						$item_class = 'woobt-item-text';

						if ( ! empty( $item['type'] ) ) {
							$item_class .= ' woobt-item-text-type-' . $item['type'];
						}

						echo '<div class="' . esc_attr( apply_filters( 'woobt_item_text_class', $item_class, $item, $product_id ) ) . '">';

						if ( empty( $item['type'] ) || ( $item['type'] === 'none' ) ) {
							echo $item['text'];
						} else {
							echo '<' . $item['type'] . '>' . $item['text'] . '</' . $item['type'] . '>';
						}

						echo '</div>';
					}

					return apply_filters( 'woobt_text_output', ob_get_clean(), $item, $product_id );
				}

				function get_ids( $product_id, $context = 'display' ) {
					$ids = get_post_meta( $product_id, 'woobt_ids', true );

					return apply_filters( 'woobt_get_ids', $ids, $product_id, $context );
				}

				function get_ids_str( $product_id, $context = 'display' ) {
					$ids_arr = [];
					$ids     = self::get_ids( $product_id, $context );

					if ( is_array( $ids ) ) {
						// new version 5.0
						foreach ( $ids as $item ) {
							$item = array_merge( [ 'id' => 0, 'price' => '100%', 'qty' => 1 ], $item );

							if ( ! empty( $item['id'] ) ) {
								$ids_arr[] = $item['id'] . '/' . $item['price'] . '/' . $item['qty'];
							}
						}

						$ids_str = implode( ',', $ids_arr );
					} else {
						$ids_str = $ids;
					}

					return apply_filters( 'woobt_get_ids_str', $ids_str, $product_id, $context );
				}

				function get_items( $ids, $product_id = 0, $context = 'view' ) {
					$items = [];
					$ids   = self::clean_ids( $ids );

					if ( ! empty( $ids ) ) {
						if ( is_array( $ids ) ) {
							// new version 5.0
							foreach ( $ids as $item ) {
								$item = array_merge( [
									'id'    => 0,
									'sku'   => '',
									'price' => '100%',
									'qty'   => 0,
									'attrs' => []
								], $item );

								// check for variation
								if ( ( $parent_id = wp_get_post_parent_id( $item['id'] ) ) && ( $parent = wc_get_product( $parent_id ) ) ) {
									$parent_sku = $parent->get_sku();
								} else {
									$parent_sku = '';
								}

								if ( apply_filters( 'woobt_use_sku', false ) && ! empty( $item['sku'] ) && ( $item['sku'] !== $parent_sku ) && ( $new_id = wc_get_product_id_by_sku( $item['sku'] ) ) ) {
									// get product id by SKU for export/import
									$item['id'] = $new_id;
								}

								$items[] = $item;
							}
						} else {
							$_items = explode( ',', $ids );

							if ( is_array( $_items ) && count( $_items ) > 0 ) {
								foreach ( $_items as $_item ) {
									$_item_data    = explode( '/', $_item );
									$_item_id      = apply_filters( 'woobt_item_id', absint( $_item_data[0] ?: 0 ) );
									$_item_product = wc_get_product( $_item_id );

									if ( ! $_item_product || ( $_item_product->get_status() === 'trash' ) ) {
										continue;
									}

									if ( ( $context === 'view' ) && ( ( self::get_setting( 'exclude_unpurchasable', 'no' ) === 'yes' ) && ( ! $_item_product->is_purchasable() || ! $_item_product->is_in_stock() ) ) ) {
										continue;
									}

									$items[] = [
										'id'    => $_item_id,
										'price' => isset( $_item_data[1] ) ? self::format_price( $_item_data[1] ) : '100%',
										'qty'   => (float) ( isset( $_item_data[2] ) ? $_item_data[2] : 1 ),
										'attrs' => isset( $_item_data[3] ) ? (array) json_decode( rawurldecode( $_item_data[3] ) ) : []
									];
								}
							}
						}
					}

					$items = apply_filters( 'woobt_get_items', $items, $ids, $product_id, $context );

					if ( $items && is_array( $items ) && count( $items ) > 0 ) {
						return $items;
					}

					return false;
				}

				function search_sku( $query ) {
					if ( $query->is_search && isset( $query->query['is_woobt'] ) ) {
						global $wpdb;
						$sku = sanitize_text_field( $query->query['s'] );
						$ids = $wpdb->get_col( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_sku' AND meta_value = %s;", $sku ) );

						if ( ! $ids ) {
							return;
						}

						unset( $query->query['s'], $query->query_vars['s'] );
						$query->query['post__in'] = [];

						foreach ( $ids as $id ) {
							$post = get_post( $id );

							if ( $post->post_type === 'product_variation' ) {
								$query->query['post__in'][]      = $post->post_parent;
								$query->query_vars['post__in'][] = $post->post_parent;
							} else {
								$query->query_vars['post__in'][] = $post->ID;
							}
						}
					}
				}

				function search_exact( $query ) {
					if ( $query->is_search && isset( $query->query['is_woobt'] ) ) {
						$query->set( 'exact', true );
					}
				}

				function search_sentence( $query ) {
					if ( $query->is_search && isset( $query->query['is_woobt'] ) ) {
						$query->set( 'sentence', true );
					}
				}

				function sanitize_array( $arr ) {
					foreach ( (array) $arr as $k => $v ) {
						if ( is_array( $v ) ) {
							$arr[ $k ] = self::sanitize_array( $v );
						} else {
							$arr[ $k ] = sanitize_text_field( $v );
						}
					}

					return $arr;
				}

				public static function clean_ids( $ids ) {
					return apply_filters( 'woobt_clean_ids', $ids );
				}

				public static function format_price( $price ) {
					// format price to percent or number
					$price = preg_replace( '/[^.%0-9]/', '', $price );

					return apply_filters( 'woobt_format_price', $price );
				}

				public static function new_price( $old_price, $new_price ) {
					if ( strpos( $new_price, '%' ) !== false ) {
						$calc_price = ( (float) $new_price * $old_price ) / 100;
					} else {
						$calc_price = $new_price;
					}

					return apply_filters( 'woobt_new_price', $calc_price, $old_price );
				}

				function wpml_item_id( $id ) {
					return apply_filters( 'wpml_object_id', $id, 'product', true );
				}

				public static function localization( $key = '', $default = '' ) {
					$str = '';

					if ( ! empty( $key ) && ! empty( self::$localization[ $key ] ) ) {
						$str = self::$localization[ $key ];
					} elseif ( ! empty( $default ) ) {
						$str = $default;
					}

					return apply_filters( 'woobt_localization_' . $key, $str );
				}

				function product_filter( $filters ) {
					$filters['woobt'] = [ $this, 'product_filter_callback' ];

					return $filters;
				}

				function product_filter_callback() {
					$woobt  = isset( $_REQUEST['woobt'] ) ? wc_clean( wp_unslash( $_REQUEST['woobt'] ) ) : false;
					$output = '<select name="woobt"><option value="">' . esc_html__( 'Bought together', 'woo-bought-together' ) . '</option>';
					$output .= '<option value="yes" ' . selected( $woobt, 'yes', false ) . '>' . esc_html__( 'With associated products', 'woo-bought-together' ) . '</option>';
					$output .= '<option value="no" ' . selected( $woobt, 'no', false ) . '>' . esc_html__( 'Without associated products', 'woo-bought-together' ) . '</option>';
					$output .= '</select>';
					echo $output;
				}

				function apply_product_filter( $query ) {
					global $pagenow;

					if ( $query->is_admin && $pagenow == 'edit.php' && isset( $_GET['woobt'] ) && $_GET['woobt'] != '' && $_GET['post_type'] == 'product' ) {
						$meta_query = (array) $query->get( 'meta_query' );

						if ( $_GET['woobt'] === 'yes' ) {
							$meta_query[] = [
								'relation' => 'AND',
								[
									'key'     => 'woobt_ids',
									'compare' => 'EXISTS'
								],
								[
									'key'     => 'woobt_ids',
									'value'   => '',
									'compare' => '!='
								],
							];
						} else {
							$meta_query[] = [
								'relation' => 'OR',
								[
									'key'     => 'woobt_ids',
									'compare' => 'NOT EXISTS'
								],
								[
									'key'     => 'woobt_ids',
									'value'   => '',
									'compare' => '=='
								],
							];
						}

						$query->set( 'meta_query', $meta_query );
					}
				}

				function wpcsm_locations( $locations ) {
					$locations['WPC Frequently Bought Together'] = [
						'woobt_wrap_before'          => esc_html__( 'Before wrapper', 'woo-bought-together' ),
						'woobt_wrap_after'           => esc_html__( 'After wrapper', 'woo-bought-together' ),
						'woobt_products_before'      => esc_html__( 'Before products', 'woo-bought-together' ),
						'woobt_products_after'       => esc_html__( 'After products', 'woo-bought-together' ),
						'woobt_product_before'       => esc_html__( 'Before sub-product', 'woo-bought-together' ),
						'woobt_product_after'        => esc_html__( 'After sub-product', 'woo-bought-together' ),
						'woobt_product_thumb_before' => esc_html__( 'Before sub-product thumbnail', 'woo-bought-together' ),
						'woobt_product_thumb_after'  => esc_html__( 'After sub-product thumbnail', 'woo-bought-together' ),
						'woobt_product_name_before'  => esc_html__( 'Before sub-product name', 'woo-bought-together' ),
						'woobt_product_name_after'   => esc_html__( 'After sub-product name', 'woo-bought-together' ),
						'woobt_product_price_before' => esc_html__( 'Before sub-product price', 'woo-bought-together' ),
						'woobt_product_price_after'  => esc_html__( 'After sub-product price', 'woo-bought-together' ),
						'woobt_product_qty_before'   => esc_html__( 'Before sub-product quantity', 'woo-bought-together' ),
						'woobt_product_qty_after'    => esc_html__( 'After sub-product quantity', 'woo-bought-together' ),
					];

					return $locations;
				}

				function export_process( $value, $meta, $product ) {
					if ( $meta->key === 'woobt_ids' ) {
						$ids = get_post_meta( $product->get_id(), 'woobt_ids', true );

						if ( ! empty( $ids ) && is_array( $ids ) ) {
							return json_encode( $ids );
						}
					}

					return $value;
				}

				function import_process( $object, $data ) {
					if ( isset( $data['meta_data'] ) ) {
						foreach ( $data['meta_data'] as $meta ) {
							if ( $meta['key'] === 'woobt_ids' ) {
								$object->update_meta_data( 'woobt_ids', json_decode( $meta['value'], true ) );
								break;
							}
						}
					}

					return $object;
				}
			}

			return WPCleverWoobt::instance();
		}
	}
}

if ( ! function_exists( 'woobt_notice_wc' ) ) {
	function woobt_notice_wc() {
		?>
		<div class="error">
			<p><strong>WPC Frequently Bought Together</strong> requires WooCommerce version 3.0 or greater.</p>
		</div>
		<?php
	}
}
