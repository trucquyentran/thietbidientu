<?php
/*
Plugin Name: WPC Product Quantity for WooCommerce
Plugin URI: https://wpclever.net/
Description: WPC Product Quantity provides powerful controls for product quantity.
Version: 3.1.6
Author: WPClever
Author URI: https://wpclever.net
Text Domain: wpc-product-quantity
Domain Path: /languages/
Requires at least: 4.0
Tested up to: 6.2
WC requires at least: 3.0
WC tested up to: 7.5
*/

use Automattic\WooCommerce\Utilities\FeaturesUtil;

defined( 'ABSPATH' ) || exit;

! defined( 'WOOPQ_VERSION' ) && define( 'WOOPQ_VERSION', '3.1.6' );
! defined( 'WOOPQ_FILE' ) && define( 'WOOPQ_FILE', __FILE__ );
! defined( 'WOOPQ_URI' ) && define( 'WOOPQ_URI', plugin_dir_url( __FILE__ ) );
! defined( 'WOOPQ_DIR' ) && define( 'WOOPQ_DIR', plugin_dir_path( __FILE__ ) );
! defined( 'WOOPQ_REVIEWS' ) && define( 'WOOPQ_REVIEWS', 'https://wordpress.org/support/plugin/wpc-product-quantity/reviews/?filter=5' );
! defined( 'WOOPQ_CHANGELOG' ) && define( 'WOOPQ_CHANGELOG', 'https://wordpress.org/plugins/wpc-product-quantity/#developers' );
! defined( 'WOOPQ_DISCUSSION' ) && define( 'WOOPQ_DISCUSSION', 'https://wordpress.org/support/plugin/wpc-product-quantity' );
! defined( 'WPC_URI' ) && define( 'WPC_URI', WOOPQ_URI );

include 'includes/wpc-dashboard.php';
include 'includes/wpc-menu.php';
include 'includes/wpc-kit.php';

if ( ! function_exists( 'woopq_init' ) ) {
	add_action( 'plugins_loaded', 'woopq_init', 11 );

	function woopq_init() {
		// load text-domain
		load_plugin_textdomain( 'wpc-product-quantity', false, basename( __DIR__ ) . '/languages/' );

		if ( ! function_exists( 'WC' ) || ! version_compare( WC()->version, '3.0', '>=' ) ) {
			add_action( 'admin_notices', 'woopq_notice_wc' );

			return;
		}

		if ( ! class_exists( 'WPCleverWoopq' ) && class_exists( 'WC_Product' ) ) {
			class WPCleverWoopq {
				protected static $settings = [];
				protected static $instance = null;

				public static function instance() {
					if ( is_null( self::$instance ) ) {
						self::$instance = new self();
					}

					return self::$instance;
				}

				function __construct() {
					self::$settings = (array) get_option( 'woopq_settings', [] );

					// enqueue backend
					add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ], 99 );

					// enqueue frontend
					add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ], 99 );

					// settings page
					add_action( 'admin_init', [ $this, 'register_settings' ] );
					add_action( 'admin_menu', [ $this, 'admin_menu' ] );

					// settings link
					add_filter( 'plugin_action_links', [ $this, 'action_links' ], 10, 2 );
					add_filter( 'plugin_row_meta', [ $this, 'row_meta' ], 10, 2 );

					// args
					add_filter( 'woocommerce_quantity_input_args', [ $this, 'quantity_input_args' ], 99, 2 );
					add_filter( 'woocommerce_loop_add_to_cart_args', [ $this, 'loop_add_to_cart_args' ], 99, 2 );

					// default input
					add_filter( 'woocommerce_quantity_input_min', [ $this, 'quantity_input_min' ], 99, 2 );
					add_filter( 'woocommerce_quantity_input_max', [ $this, 'quantity_input_max' ], 99, 2 );
					add_filter( 'woocommerce_quantity_input_step', [ $this, 'quantity_input_step' ], 99, 2 );

					// admin input
					add_filter( 'woocommerce_quantity_input_min_admin', [ $this, 'quantity_input_min_admin' ], 99, 2 );
					add_filter( 'woocommerce_quantity_input_step_admin', [
						$this,
						'quantity_input_step_admin'
					], 99, 2 );

					// decimal
					if ( self::get_setting( 'decimal', 'no' ) === 'yes' ) {
						remove_filter( 'woocommerce_stock_amount', 'intval' );
						add_filter( 'woocommerce_stock_amount', 'floatval' );
					}

					// template
					add_filter( 'wc_get_template', [ $this, 'quantity_input_template' ], 99, 2 );

					// add to cart
					add_filter( 'woocommerce_add_to_cart_validation', [ $this, 'add_to_cart_validation' ], 99, 4 );

					// add to cart message
					if ( self::get_setting( 'decimal', 'no' ) === 'yes' ) {
						add_filter( 'wc_add_to_cart_message_html', [ $this, 'add_to_cart_message_html' ], 99, 3 );
					}

					// product settings
					add_filter( 'woocommerce_product_data_tabs', [ $this, 'product_data_tabs' ] );
					add_action( 'woocommerce_product_data_panels', [ $this, 'product_data_panels' ] );

					// variation settings
					add_action( 'woocommerce_product_after_variable_attributes', [
						$this,
						'variation_settings'
					], 99, 3 );

					// WPC Smart Messages
					add_filter( 'wpcsm_locations', [ $this, 'wpcsm_locations' ] );

					// HPOS compatibility
					add_action( 'before_woocommerce_init', function () {
						if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
							FeaturesUtil::declare_compatibility( 'custom_order_tables', WOOPQ_FILE );
						}
					} );
				}

				public static function get_settings() {
					return apply_filters( 'woopq_get_settings', self::$settings );
				}

				public static function get_setting( $name, $default = false ) {
					if ( ! empty( self::$settings ) && isset( self::$settings[ $name ] ) ) {
						$setting = self::$settings[ $name ];
					} else {
						$setting = get_option( '_woopq_' . $name, $default );
					}

					return apply_filters( 'woopq_get_setting', $setting, $name, $default );
				}

				function admin_enqueue_scripts() {
					wp_enqueue_style( 'woopq-backend', WOOPQ_URI . 'assets/css/backend.css', [], WOOPQ_VERSION );
					wp_enqueue_script( 'woopq-backend', WOOPQ_URI . 'assets/js/backend.js', [ 'jquery' ], WOOPQ_VERSION, true );
				}

				function enqueue_scripts() {
					wp_enqueue_style( 'woopq-frontend', WOOPQ_URI . 'assets/css/frontend.css', [], WOOPQ_VERSION );
					wp_enqueue_script( 'woopq-frontend', WOOPQ_URI . 'assets/js/frontend.js', [ 'jquery' ], WOOPQ_VERSION, true );
					wp_localize_script( 'woopq-frontend', 'woopq_vars', [
							'rounding'     => self::get_setting( 'rounding', 'down' ),
							'auto_correct' => self::get_setting( 'auto_correct', 'entering' ),
							'timeout'      => apply_filters( 'woopq_auto_correct_timeout', 1000 ),
						]
					);
				}

				function register_settings() {
					// settings
					register_setting( 'woopq_settings', 'woopq_settings' );
				}

				function admin_menu() {
					add_submenu_page( 'wpclever', esc_html__( 'WPC Product Quantity', 'wpc-product-quantity' ), esc_html__( 'Product Quantity', 'wpc-product-quantity' ), 'manage_options', 'wpclever-woopq', [
						$this,
						'admin_menu_content'
					] );
				}

				function admin_menu_content() {
					$active_tab = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'settings';
					?>
					<div class="wpclever_settings_page wrap">
						<h1 class="wpclever_settings_page_title"><?php echo esc_html__( 'WPC Product Quantity', 'wpc-product-quantity' ) . ' ' . WOOPQ_VERSION; ?></h1>
						<div class="wpclever_settings_page_desc about-text">
							<p>
								<?php printf( esc_html__( 'Thank you for using our plugin! If you are satisfied, please reward it a full five-star %s rating.', 'wpc-product-quantity' ), '<span style="color:#ffb900">&#9733;&#9733;&#9733;&#9733;&#9733;</span>' ); ?>
								<br/>
								<a href="<?php echo esc_url( WOOPQ_REVIEWS ); ?>" target="_blank"><?php esc_html_e( 'Reviews', 'wpc-product-quantity' ); ?></a> |
								<a href="<?php echo esc_url( WOOPQ_CHANGELOG ); ?>" target="_blank"><?php esc_html_e( 'Changelog', 'wpc-product-quantity' ); ?></a> |
								<a href="<?php echo esc_url( WOOPQ_DISCUSSION ); ?>" target="_blank"><?php esc_html_e( 'Discussion', 'wpc-product-quantity' ); ?></a>
							</p>
						</div>
						<?php if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] ) { ?>
							<div class="notice notice-success is-dismissible">
								<p><?php esc_html_e( 'Settings updated.', 'wpc-product-quantity' ); ?></p>
							</div>
						<?php } ?>
						<div class="wpclever_settings_page_nav">
							<h2 class="nav-tab-wrapper">
								<a href="<?php echo admin_url( 'admin.php?page=wpclever-woopq&tab=settings' ); ?>" class="<?php echo esc_attr( $active_tab === 'settings' ? 'nav-tab nav-tab-active' : 'nav-tab' ); ?>">
									<?php esc_html_e( 'Settings', 'wpc-product-quantity' ); ?>
								</a>
								<a href="<?php echo admin_url( 'admin.php?page=wpclever-woopq&tab=premium' ); ?>" class="<?php echo esc_attr( $active_tab === 'premium' ? 'nav-tab nav-tab-active' : 'nav-tab' ); ?>" style="color: #c9356e">
									<?php esc_html_e( 'Premium Version', 'wpc-product-quantity' ); ?>
								</a>
								<a href="<?php echo admin_url( 'admin.php?page=wpclever-kit' ); ?>" class="nav-tab">
									<?php esc_html_e( 'Essential Kit', 'wpc-product-quantity' ); ?>
								</a>
							</h2>
						</div>
						<div class="wpclever_settings_page_content">
							<?php if ( $active_tab === 'settings' ) {
								// update old settings
								if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] ) {
									if ( ! empty( self::$settings ) ) {
										foreach ( self::$settings as $k => $s ) {
											update_option( '_woopq_' . $k, $s );
										}
									}
								}

								if ( self::get_setting( 'decimal', 'no' ) === 'yes' ) {
									$step = '0.000001';
								} else {
									$step = '1';
								}

								$decimal      = self::get_setting( 'decimal', 'no' );
								$plus_minus   = self::get_setting( 'plus_minus', 'hide' );
								$auto_correct = self::get_setting( 'auto_correct', 'entering' );
								$rounding     = self::get_setting( 'rounding', 'down' );
								$type         = self::get_setting( 'type', 'default' );
								?>
								<form method="post" action="options.php">
									<table class="form-table woopq-table">
										<tr class="heading">
											<th colspan="2">
												<?php esc_html_e( 'General', 'wpc-product-quantity' ); ?>
											</th>
										</tr>
										<tr>
											<th>
												<?php esc_html_e( 'Decimal quantities', 'wpc-product-quantity' ); ?>
											</th>
											<td>
												<select name="woopq_settings[decimal]">
													<option value="no" <?php selected( $decimal, 'no' ); ?>><?php esc_html_e( 'No', 'wpc-product-quantity' ); ?></option>
													<option value="yes" <?php selected( $decimal, 'yes' ); ?>><?php esc_html_e( 'Yes', 'wpc-product-quantity' ); ?></option>
												</select>
												<span class="description"><?php esc_html_e( 'Press "Update Options" after enabling this option, then you can enter decimal quantities in min, max, step quantity options.', 'wpc-product-quantity' ); ?></span>
											</td>
										</tr>
										<tr>
											<th><?php esc_html_e( 'Plus/minus button', 'wpc-product-quantity' ); ?></th>
											<td>
												<select name="woopq_settings[plus_minus]">
													<option value="show" <?php selected( $plus_minus, 'show' ); ?>><?php esc_html_e( 'Show', 'wpc-product-quantity' ); ?></option>
													<option value="hide" <?php selected( $plus_minus, 'hide' ); ?>><?php esc_html_e( 'Hide', 'wpc-product-quantity' ); ?></option>
												</select>
												<span class="description"><?php esc_html_e( 'Show the plus/minus button for the input type to increase/decrease the quantity.', 'wpc-product-quantity' ); ?></span>
											</td>
										</tr>
										<tr>
											<th><?php esc_html_e( 'Auto-correct', 'wpc-product-quantity' ); ?></th>
											<td>
												<select name="woopq_settings[auto_correct]">
													<option value="entering" <?php selected( $auto_correct, 'entering' ); ?>><?php esc_html_e( 'While entering', 'wpc-product-quantity' ); ?></option>
													<option value="out_of_focus" <?php selected( $auto_correct, 'out_of_focus' ); ?>><?php esc_html_e( 'Out of focus', 'wpc-product-quantity' ); ?></option>
												</select>
												<span class="description"><?php esc_html_e( 'When the auto-correct functionality will be triggered: while entering the number or out of focus on the input (click outside).', 'wpc-product-quantity' ); ?></span>
											</td>
										</tr>
										<tr>
											<th><?php esc_html_e( 'Rounding values', 'wpc-product-quantity' ); ?></th>
											<td>
												<select name="woopq_settings[rounding]">
													<option value="down" <?php selected( $rounding, 'down' ); ?>><?php esc_html_e( 'Down', 'wpc-product-quantity' ); ?></option>
													<option value="up" <?php selected( $rounding, 'up' ); ?>><?php esc_html_e( 'Up', 'wpc-product-quantity' ); ?></option>
												</select>
												<span class="description"><?php esc_html_e( 'Round the quantity to the nearest bigger (up) or smaller (down) value when an invalid number is inputted.', 'wpc-product-quantity' ); ?></span>
											</td>
										</tr>
										<tr class="heading">
											<th colspan="2">
												<?php esc_html_e( 'Quantity', 'wpc-product-quantity' ); ?>
											</th>
										</tr>
										<tr>
											<th><?php esc_html_e( 'Type', 'wpc-product-quantity' ); ?></th>
											<td>
												<select name="woopq_settings[type]" class="woopq_type">
													<option value="default" <?php selected( $type, 'default' ); ?>><?php esc_html_e( 'Input (Default)', 'wpc-product-quantity' ); ?></option>
													<option value="select" <?php selected( $type, 'select' ); ?>><?php esc_html_e( 'Select', 'wpc-product-quantity' ); ?></option>
													<option value="radio" <?php selected( $type, 'radio' ); ?>><?php esc_html_e( 'Radio', 'wpc-product-quantity' ); ?></option>
												</select>
											</td>
										</tr>
										<tr class="woopq_show_if_type woopq_show_if_type_select woopq_show_if_type_radio">
											<th><?php esc_html_e( 'Values', 'wpc-product-quantity' ); ?></th>
											<td>
												<textarea name="woopq_settings[values]" rows="10" cols="50"><?php echo self::get_setting( 'values' ); ?></textarea>
												<p class="description"><?php esc_html_e( 'These values will be used for select/radio type. Enter each value in one line and can use the range e.g "10-20".', 'wpc-product-quantity' ); ?></p>
											</td>
										</tr>
										<tr class="woopq_show_if_type woopq_show_if_type_default">
											<th><?php esc_html_e( 'Minimum', 'wpc-product-quantity' ); ?></th>
											<td>
												<input type="number" name="woopq_settings[min]" min="0" step="<?php echo esc_attr( $step ); ?>" value="<?php echo self::get_setting( 'min' ); ?>"/>
												<span class="description"><?php esc_html_e( 'Leave blank to disable.', 'wpc-product-quantity' ); ?></span>
											</td>
										</tr>
										<tr class="woopq_show_if_type woopq_show_if_type_default">
											<th><?php esc_html_e( 'Step', 'wpc-product-quantity' ); ?></th>
											<td>
												<input type="number" name="woopq_settings[step]" min="0" step="<?php echo esc_attr( $step ); ?>" value="<?php echo self::get_setting( 'step' ); ?>"/>
												<span class="description"><?php esc_html_e( 'Leave blank to disable.', 'wpc-product-quantity' ); ?></span>
											</td>
										</tr>
										<tr class="woopq_show_if_type woopq_show_if_type_default">
											<th><?php esc_html_e( 'Maximum', 'wpc-product-quantity' ); ?></th>
											<td>
												<input type="number" name="woopq_settings[max]" min="0" step="<?php echo esc_attr( $step ); ?>" value="<?php echo self::get_setting( 'max' ); ?>"/>
												<span class="description"><?php esc_html_e( 'Leave blank to disable.', 'wpc-product-quantity' ); ?></span>
											</td>
										</tr>
										<tr>
											<th><?php esc_html_e( 'Default value', 'wpc-product-quantity' ); ?></th>
											<td>
												<input type="number" name="woopq_settings[value]" min="0" step="<?php echo esc_attr( $step ); ?>" value="<?php echo self::get_setting( 'value', 1 ); ?>"/>
												<span class="description"><?php esc_html_e( 'Leave blank to disable.', 'wpc-product-quantity' ); ?></span>
											</td>
										</tr>
										<tr class="heading">
											<th colspan="2"><?php esc_html_e( 'Suggestion', 'wpc-product-quantity' ); ?></th>
										</tr>
										<tr>
											<td colspan="2">
												To display custom engaging real-time messages on any wished positions, please install
												<a href="https://wordpress.org/plugins/wpc-smart-messages/" target="_blank">WPC Smart Messages for WooCommerce</a> plugin. It's free and available now on the WordPress repository.
											</td>
										</tr>
										<tr class="submit">
											<th colspan="2">
												<?php settings_fields( 'woopq_settings' ); ?><?php submit_button(); ?>
											</th>
										</tr>
									</table>
								</form>
							<?php } elseif ( $active_tab === 'premium' ) { ?>
								<div class="wpclever_settings_page_content_text">
									<p>
										Get the Premium Version just $29!
										<a href="https://wpclever.net/downloads/product-quantity?utm_source=pro&utm_medium=woopq&utm_campaign=wporg" target="_blank">https://wpclever.net/downloads/product-quantity</a>
									</p>
									<p><strong>Extra features for Premium Version:</strong></p>
									<ul style="margin-bottom: 0">
										<li>- Add quantity settings at product and variation basis.</li>
										<li>- Get the lifetime update & premium support.</li>
									</ul>
								</div>
							<?php } ?>
						</div>
					</div>
					<?php
				}

				function action_links( $links, $file ) {
					static $plugin;

					if ( ! isset( $plugin ) ) {
						$plugin = plugin_basename( __FILE__ );
					}

					if ( $plugin === $file ) {
						$settings             = '<a href="' . admin_url( 'admin.php?page=wpclever-woopq&tab=settings' ) . '">' . esc_html__( 'Settings', 'wpc-product-quantity' ) . '</a>';
						$links['wpc-premium'] = '<a href="' . admin_url( 'admin.php?page=wpclever-woopq&tab=premium' ) . '">' . esc_html__( 'Premium Version', 'wpc-product-quantity' ) . '</a>';
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
							'support' => '<a href="' . esc_url( WOOPQ_DISCUSSION ) . '" target="_blank">' . esc_html__( 'Community support', 'wpc-product-quantity' ) . '</a>',
						];

						return array_merge( $links, $row_meta );
					}

					return (array) $links;
				}

				function loop_add_to_cart_args( $args, $product ) {
					if ( $product ) {
						$woopq_value = self::get_value( $product );
						$woopq_min   = self::get_min( $product );

						if ( ! empty( $woopq_min ) && ( $woopq_value < $woopq_min ) ) {
							$args['quantity'] = $woopq_min;
						} else {
							$args['quantity'] = $woopq_value;
						}
					}

					return $args;
				}

				function quantity_input_args( $args, $product ) {
					if ( $product ) {
						$args['product_id'] = $product->get_id();
						$args['min_value']  = self::get_min( $product, $args['min_value'] );
						$args['max_value']  = self::get_max( $product, $args['max_value'] );
						$args['step']       = self::get_step( $product, $args['step'] );

						if ( substr( $args['input_name'], 0, 8 ) === 'quantity' ) {
							// check if isn't in the cart
							$args['input_value'] = self::get_value( $product, $args['input_value'] );
						}
					}

					return $args;
				}

				function quantity_input_min( $min, $product ) {
					if ( $product ) {
						return self::get_min( $product, $min );
					}

					return $min;
				}

				function quantity_input_max( $max, $product ) {
					if ( $product ) {
						return self::get_max( $product, $max );
					}

					return $max;
				}

				function quantity_input_step( $step, $product ) {
					if ( $product ) {
						return self::get_step( $product, $step );
					}

					return $step;
				}

				function quantity_input_min_admin( $min, $product ) {
					if ( ! apply_filters( 'woopq_ignore_admin_input', false, 'min' ) ) {
						if ( $product ) {
							return self::get_min( $product, $min );
						}
					} else {
						return '0';
					}

					return $min;
				}

				function quantity_input_max_admin( $max, $product ) {
					if ( ! apply_filters( 'woopq_ignore_admin_input', false, 'max' ) ) {
						if ( $product ) {
							return self::get_max( $product, $max );
						}
					}

					return $max;
				}

				function quantity_input_step_admin( $step, $product ) {
					if ( ! apply_filters( 'woopq_ignore_admin_input', false, 'step' ) ) {
						if ( $product ) {
							return self::get_step( $product, $step );
						}
					} else {
						return 'any';
					}

					return $step;
				}

				function get_quantity( $product, $is_variation = false ) {
					if ( is_numeric( $product ) ) {
						$product_id = $product;
						$product    = wc_get_product( $product_id );
					} else {
						$product_id = $product->get_id();
					}

					if ( $is_variation || $product->is_type( 'variation' ) ) {
						return apply_filters( 'woopq_quantity', get_post_meta( $product_id, '_woopq_quantity', true ) ?: 'parent', $product_id );
					}

					return apply_filters( 'woopq_quantity', get_post_meta( $product_id, '_woopq_quantity', true ) ?: 'default', $product_id );
				}

				function get_type( $product ) {
					if ( is_numeric( $product ) ) {
						$product_id = $product;
					} else {
						$product_id = $product->get_id();
					}

					$woopq_type = 'default';
					$quantity   = self::get_quantity( $product_id );

					switch ( $quantity ) {
						case 'disable':
							$woopq_type = 'hidden';

							break;
						case 'default':
							$woopq_type = self::get_setting( 'type' );

							break;
						case 'parent':
							$product = wc_get_product( $product_id );

							if ( $product->is_type( 'variation' ) && ( $parent_id = $product->get_parent_id() ) ) {
								return self::get_type( $parent_id );
							}

							break;
						default:
							$woopq_type = get_post_meta( $product_id, '_woopq_type', true ) ?: 'default';

							break;
					}

					return apply_filters( 'woopq_type', $woopq_type, $product_id );
				}

				function get_min( $product, $min = 0 ) {
					if ( is_numeric( $product ) ) {
						$product_id = $product;
						$product    = wc_get_product( $product_id );
					} else {
						$product_id = $product->get_id();
					}

					$woopq_min = $min;
					$quantity  = self::get_quantity( $product );

					switch ( $quantity ) {
						case 'disable':
							break;
						case 'default':
							if ( self::get_type( $product_id ) !== 'default' ) {
								$woopq_values = self::get_values( $product );

								if ( ! empty( $woopq_values ) ) {
									$woopq_min = min( array_column( $woopq_values, 'value' ) );
								}
							} else {
								$woopq_min = self::get_setting( 'min' );
							}

							break;
						case 'parent':
							if ( $product->is_type( 'variation' ) && ( $parent_id = $product->get_parent_id() ) ) {
								return self::get_min( wc_get_product( $parent_id ) );
							}

							break;
						default:
							if ( self::get_type( $product_id ) !== 'default' ) {
								$woopq_values = self::get_values( $product );

								if ( ! empty( $woopq_values ) ) {
									$woopq_min = min( array_column( $woopq_values, 'value' ) );
								}
							} else {
								$woopq_min = get_post_meta( $product_id, '_woopq_min', true );
							}

							break;
					}

					if ( ! is_numeric( $woopq_min ) ) {
						// leave blank to disable
						$woopq_min = $min;
					}

					$woopq_min = (float) $woopq_min;

					if ( self::get_setting( 'decimal', 'no' ) !== 'yes' ) {
						$woopq_min = ceil( $woopq_min );
					}

					return apply_filters( 'woopq_min', $woopq_min, $product_id, $product );
				}

				function get_max( $product, $max = 100000 ) {
					if ( is_numeric( $product ) ) {
						$product_id = $product;
						$product    = wc_get_product( $product_id );
					} else {
						$product_id = $product->get_id();
					}

					$woopq_max = $max;
					$quantity  = self::get_quantity( $product );
					$max_value = $product->get_max_purchase_quantity();

					switch ( $quantity ) {
						case 'disable':
							break;
						case 'default':
							if ( self::get_type( $product_id ) !== 'default' ) {
								$woopq_values = self::get_values( $product );

								if ( ! empty( $woopq_values ) ) {
									$woopq_max = max( array_column( $woopq_values, 'value' ) );
								}
							} else {
								$woopq_max = self::get_setting( 'max' );
							}

							break;
						case 'parent':
							if ( $product->is_type( 'variation' ) && ( $parent_id = $product->get_parent_id() ) ) {
								return self::get_max( wc_get_product( $parent_id ) );
							}

							break;
						default:
							if ( self::get_type( $product_id ) !== 'default' ) {
								$woopq_values = self::get_values( $product );

								if ( ! empty( $woopq_values ) ) {
									$woopq_max = max( array_column( $woopq_values, 'value' ) );
								}
							} else {
								$woopq_max = get_post_meta( $product_id, '_woopq_max', true );
							}

							break;
					}

					if ( ! is_numeric( $woopq_max ) ) {
						// leave blank to disable
						$woopq_max = $max;
					}

					$woopq_max = (float) $woopq_max;

					if ( ( $max_value > 0 ) && ( $woopq_max > $max_value ) ) {
						$woopq_max = $max_value;
					}

					if ( self::get_setting( 'decimal', 'no' ) !== 'yes' ) {
						$woopq_max = ceil( $woopq_max );
					}

					return apply_filters( 'woopq_max', $woopq_max, $product_id, $product );
				}

				function get_step( $product, $step = 1 ) {
					if ( is_numeric( $product ) ) {
						$product_id = $product;
						$product    = wc_get_product( $product_id );
					} else {
						$product_id = $product->get_id();
					}

					$woopq_step = $step;
					$quantity   = self::get_quantity( $product );

					switch ( $quantity ) {
						case 'disable':
							break;
						case 'default':
							$woopq_step = self::get_setting( 'step' );

							break;
						case 'parent':
							if ( $product->is_type( 'variation' ) && ( $parent_id = $product->get_parent_id() ) ) {
								return self::get_step( wc_get_product( $parent_id ) );
							}

							break;
						default:
							$woopq_step = get_post_meta( $product_id, '_woopq_step', true );

							break;
					}

					if ( ! is_numeric( $woopq_step ) ) {
						// leave blank to disable
						$woopq_step = $step;
					}

					$woopq_step = (float) $woopq_step;

					if ( self::get_setting( 'decimal', 'no' ) !== 'yes' ) {
						$woopq_step = ceil( $woopq_step );
					}

					return apply_filters( 'woopq_step', $woopq_step, $product_id, $product );
				}

				function get_value( $product, $value = 1 ) {
					if ( is_numeric( $product ) ) {
						$product_id = $product;
						$product    = wc_get_product( $product_id );
					} else {
						$product_id = $product->get_id();
					}

					$woopq_value = $value;
					$quantity    = self::get_quantity( $product );

					switch ( $quantity ) {
						case 'disable':
							break;
						case 'default':
							$woopq_value = self::get_setting( 'value' );

							break;
						case 'parent':
							if ( $product->is_type( 'variation' ) && ( $parent_id = $product->get_parent_id() ) ) {
								return self::get_value( wc_get_product( $parent_id ) );
							}

							break;
						default:
							$woopq_value = get_post_meta( $product_id, '_woopq_value', true );

							break;
					}

					if ( ! is_numeric( $woopq_value ) ) {
						// leave blank to disable
						$woopq_value = $value;
					}

					$woopq_value = (float) $woopq_value;

					if ( self::get_setting( 'decimal', 'no' ) !== 'yes' ) {
						$woopq_value = ceil( $woopq_value );
					}

					return apply_filters( 'woopq_value', $woopq_value, $product_id, $product );
				}

				function get_values( $product, $values = '' ) {
					if ( is_numeric( $product ) ) {
						$product_id = $product;
						$product    = wc_get_product( $product_id );
					} else {
						$product_id = $product->get_id();
					}

					$quantity = self::get_quantity( $product );

					switch ( $quantity ) {
						case 'default':
							$values = self::get_setting( 'values' );

							break;
						case 'parent':
							if ( $product->is_type( 'variation' ) && ( $parent_id = $product->get_parent_id() ) ) {
								return self::get_values( wc_get_product( $parent_id ) );
							}

							break;
						default:
							$values = get_post_meta( $product_id, '_woopq_values', true );

							break;
					}

					$woopq_values  = [];
					$woopq_decimal = self::get_setting( 'decimal', 'no' );
					$values_arr    = explode( "\n", $values );

					if ( count( $values_arr ) > 0 ) {
						foreach ( $values_arr as $item ) {
							$item_value = self::clean_value( $item );

							if ( strpos( $item_value, '-' ) ) {
								// quantity range e.g 1-10
								$item_value_arr = explode( '-', $item_value );

								for ( $i = (int) $item_value_arr[0]; $i <= (int) $item_value_arr[1]; $i ++ ) {
									$woopq_values[] = [ 'name' => $i, 'value' => $i ];
								}
							} elseif ( is_numeric( $item_value ) ) {
								if ( $woopq_decimal !== 'yes' ) {
									$woopq_values[] = [
										'name'  => esc_html( trim( $item ) ),
										'value' => (int) $item_value
									];
								} else {
									$woopq_values[] = [
										'name'  => esc_html( trim( $item ) ),
										'value' => (float) $item_value
									];
								}
							}
						}
					}

					if ( empty( $woopq_values ) ) {
						// default values
						$woopq_values = apply_filters( 'woopq_default_values', [
							[ 'name' => '1', 'value' => 1 ],
							[ 'name' => '2', 'value' => 2 ],
							[ 'name' => '3', 'value' => 3 ],
							[ 'name' => '4', 'value' => 4 ],
							[ 'name' => '5', 'value' => 5 ],
							[ 'name' => '6', 'value' => 6 ],
							[ 'name' => '7', 'value' => 7 ],
							[ 'name' => '8', 'value' => 8 ],
							[ 'name' => '9', 'value' => 9 ],
							[ 'name' => '10', 'value' => 10 ]
						] );
					} else {
						$woopq_values = array_intersect_key( $woopq_values, array_unique( array_map( 'serialize', $woopq_values ) ) );
					}

					return apply_filters( 'woopq_values', $woopq_values, $product_id, $product );
				}

				function quantity_input_template( $located, $template_name ) {
					if ( $template_name === 'global/quantity-input.php' ) {
						return WOOPQ_DIR . 'templates/quantity-input.php';
					}

					return $located;
				}

				function product_data_tabs( $tabs ) {
					$tabs['woopq'] = [
						'label'  => esc_html__( 'Quantity', 'wpc-product-quantity' ),
						'target' => 'woopq_settings',
					];

					return $tabs;
				}

				function product_data_panels() {
					global $post;
					$post_id = $post->ID;

					self::product_settings( $post_id );
				}

				function product_settings( $post_id, $is_variation = false ) {
					$woopq_step = '1';

					if ( self::get_setting( 'decimal', 'no' ) === 'yes' ) {
						$woopq_step = '0.000001';
					}

					$quantity = self::get_quantity( $post_id, $is_variation );
					$type     = self::get_type( $post_id );

					$name  = '';
					$id    = 'woopq_settings';
					$class = 'woopq_table woopq-table panel woocommerce_options_panel';

					if ( $is_variation ) {
						$name  = '_v[' . $post_id . ']';
						$id    = '';
						$class = 'woopq_table woopq-table';
					}
					?>
					<div id="<?php echo esc_attr( $id ); ?>" class="<?php echo esc_attr( $class ); ?>">
						<div class="woopq_tr">
							<div class="woopq_td"><?php esc_html_e( 'Quantity', 'wpc-product-quantity' ); ?></div>
							<div class="woopq_td">
								<div class="woopq_active">
									<input name="<?php echo esc_attr( '_woopq_quantity' . $name ); ?>" type="radio" class="woopq_active_input" value="default" <?php checked( $quantity, 'default' ); ?>/> <?php esc_html_e( 'Default', 'wpc-product-quantity' ); ?>
									(<a href="<?php echo admin_url( 'admin.php?page=wpclever-woopq&tab=settings' ); ?>" target="_blank"><?php esc_html_e( 'settings', 'wpc-product-quantity' ); ?></a>)
								</div>
								<?php if ( $is_variation ) { ?>
									<div class="woopq_active">
										<input name="<?php echo esc_attr( '_woopq_quantity' . $name ); ?>" type="radio" class="woopq_active_input" value="parent" <?php checked( $quantity, 'parent' ); ?>/> <?php esc_html_e( 'Parent', 'wpc-product-quantity' ); ?>
									</div>
								<?php } ?>
								<div class="woopq_active">
									<input name="<?php echo esc_attr( '_woopq_quantity' . $name ); ?>" type="radio" class="woopq_active_input" value="disable" <?php checked( $quantity, 'disable' ); ?>/> <?php esc_html_e( 'Disable', 'wpc-product-quantity' ); ?>
								</div>
								<div class="woopq_active">
									<input name="<?php echo esc_attr( '_woopq_quantity' . $name ); ?>" type="radio" class="woopq_active_input" value="overwrite" <?php checked( $quantity, 'overwrite' ); ?>/> <?php esc_html_e( 'Overwrite', 'wpc-product-quantity' ); ?>
								</div>
								<div style="color: #c9356e; padding-left: 0; padding-right: 0; margin-top: 10px">You only can use the
									<a href="<?php echo admin_url( 'admin.php?page=wpclever-woopq&tab=settings' ); ?>" target="_blank">default settings</a> for all products and variations.<br/>Quantity settings at a product or variation basis only available on the Premium Version.
									<a href="https://wpclever.net/downloads/product-quantity?utm_source=pro&utm_medium=woopq&utm_campaign=wporg" target="_blank">Click here</a> to buy, just $29!
								</div>
							</div>
						</div>
						<div class="woopq_show_if_overwrite">
							<div class="woopq_tr">
								<div class="woopq_td"><?php esc_html_e( 'Type', 'wpc-product-quantity' ); ?></div>
								<div class="woopq_td">
									<select name="<?php echo esc_attr( '_woopq_type' . $name ); ?>" class="woopq_type">
										<option value="default" <?php echo esc_attr( $type === 'default' ? 'selected' : '' ); ?>><?php esc_html_e( 'Input (Default)', 'wpc-product-quantity' ); ?></option>
										<option value="select" <?php echo esc_attr( $type === 'select' ? 'selected' : '' ); ?>><?php esc_html_e( 'Select', 'wpc-product-quantity' ); ?></option>
										<option value="radio" <?php echo esc_attr( $type === 'radio' ? 'selected' : '' ); ?>><?php esc_html_e( 'Radio', 'wpc-product-quantity' ); ?></option>
									</select>
								</div>
							</div>
							<div class="woopq_tr woopq_show_if_type woopq_show_if_type_select woopq_show_if_type_radio">
								<div class="woopq_td"><?php esc_html_e( 'Values', 'wpc-product-quantity' ); ?></div>
								<div class="woopq_td">
									<textarea name="<?php echo esc_attr( '_woopq_values' . $name ); ?>" rows="10" cols="50" style="float: none; width: 100%; height: 200px"><?php echo get_post_meta( $post_id, '_woopq_values', true ); ?></textarea>
									<p class="description" style="margin-left: 0"><?php esc_html_e( 'These values will be used for select/radio type. Enter each value in one line and can use the range e.g "10-20".', 'wpc-product-quantity' ); ?></p>
								</div>
							</div>
							<div class="woopq_tr woopq_show_if_type woopq_show_if_type_default">
								<div class="woopq_td"><?php esc_html_e( 'Minimum', 'wpc-product-quantity' ); ?></div>
								<div class="woopq_td">
									<input type="number" name="<?php echo esc_attr( '_woopq_min' . $name ); ?>" min="0" step="<?php echo esc_attr( $woopq_step ); ?>" style="width: 120px" value="<?php echo get_post_meta( $post_id, '_woopq_min', true ); ?>"/>
									<span class="description"><?php esc_html_e( 'Leave blank to disable.', 'wpc-product-quantity' ); ?></span>
								</div>
							</div>
							<div class="woopq_tr woopq_show_if_type woopq_show_if_type_default">
								<div class="woopq_td"><?php esc_html_e( 'Step', 'wpc-product-quantity' ); ?></div>
								<div class="woopq_td">
									<input type="number" name="<?php echo esc_attr( '_woopq_step' . $name ); ?>" min="0" step="<?php echo esc_attr( $woopq_step ); ?>" style="width: 120px" value="<?php echo get_post_meta( $post_id, '_woopq_step', true ); ?>"/>
									<span class="description"><?php esc_html_e( 'Leave blank to disable.', 'wpc-product-quantity' ); ?></span>
								</div>
							</div>
							<div class="woopq_tr woopq_show_if_type woopq_show_if_type_default">
								<div class="woopq_td"><?php esc_html_e( 'Maximum', 'wpc-product-quantity' ); ?></div>
								<div class="woopq_td">
									<input type="number" name="<?php echo esc_attr( '_woopq_max' . $name ); ?>" min="0" step="<?php echo esc_attr( $woopq_step ); ?>" style="width: 120px" value="<?php echo get_post_meta( $post_id, '_woopq_max', true ); ?>"/>
									<span class="description"><?php esc_html_e( 'Leave blank to disable.', 'wpc-product-quantity' ); ?></span>
								</div>
							</div>
							<div class="woopq_tr">
								<div class="woopq_td"><?php esc_html_e( 'Default value', 'wpc-product-quantity' ); ?></div>
								<div class="woopq_td">
									<input type="number" name="<?php echo esc_attr( '_woopq_value' . $name ); ?>" min="0" step="<?php echo esc_attr( $woopq_step ); ?>" style="width: 120px" value="<?php echo get_post_meta( $post_id, '_woopq_value', true ); ?>"/>
									<span class="description"><?php esc_html_e( 'Leave blank to disable.', 'wpc-product-quantity' ); ?></span>
								</div>
							</div>
						</div>
					</div>
					<?php
				}

				function add_to_cart_validation( $passed, $product_id, $qty, $variation_id = 0 ) {
					if ( $variation_id ) {
						$product_id = $variation_id;
					}

					if ( ( self::get_quantity( $product_id ) !== 'disable' ) && apply_filters( 'woopq_add_to_cart_validation', true, $product_id, $qty ) ) {
						// only validate when active quantity settings
						$product = wc_get_product( $product_id );
						$added   = self::qty_in_cart( $product_id );

						if ( self::get_type( $product_id ) === 'default' ) {
							// input
							$min  = self::get_min( $product );
							$step = self::get_step( $product );
							$max  = self::get_max( $product );

							if ( ( $min > 0 ) && ( $qty < $min ) && apply_filters( 'woopq_add_to_cart_validation_min', true, $product_id, $qty, $min ) ) {
								wc_add_notice( sprintf( esc_html__( 'You can\'t add less than %s &times; "%s" to the cart.', 'wpc-product-quantity' ), $min, esc_html( get_the_title( $product_id ) ) ), 'error' );

								return false;
							}

							if ( ( $max > 0 ) && ( $qty + $added ) > $max && apply_filters( 'woopq_add_to_cart_validation_max', true, $product_id, $qty, $max, $added ) ) {
								wc_add_notice( sprintf( esc_html__( 'You can\'t add more than %s &times; "%s" to the cart.', 'wpc-product-quantity' ), $max, esc_html( get_the_title( $product_id ) ) ), 'error' );

								return false;
							}

							if ( $step > 0 ) {
								$num = ( $qty - $min ) / $step;

								if ( ( filter_var( $num, FILTER_VALIDATE_INT ) === false ) && apply_filters( 'woopq_add_to_cart_validation_step', true, $product_id, $qty, $step, $min ) ) {
									wc_add_notice( sprintf( esc_html__( 'You can\'t add %s &times; "%s" to the cart.', 'wpc-product-quantity' ), $qty, esc_html( get_the_title( $product_id ) ) ), 'error' );

									return false;
								}
							}
						} else {
							// select or radio
							$values = self::get_values( $product );

							if ( ! empty( $values ) ) {
								if ( ( ! in_array( $qty, array_column( $values, 'value' ) ) || ! in_array( $qty + $added, array_column( $values, 'value' ) ) ) && apply_filters( 'woopq_add_to_cart_validation_values', true, $product_id, $qty, $added, $values ) ) {
									wc_add_notice( sprintf( esc_html__( 'You can\'t add %s &times; "%s" to the cart.', 'wpc-product-quantity' ), $qty, esc_html( get_the_title( $product_id ) ) ), 'error' );

									return false;
								}
							}
						}
					}

					return $passed;
				}

				function qty_in_cart( $product_id ) {
					$qty = 0;

					foreach ( WC()->cart->get_cart() as $cart_item ) {
						if ( $cart_item['product_id'] === $product_id ) {
							$qty += $cart_item['quantity'];
						}
					}

					return $qty;
				}

				function add_to_cart_message_html( $message, $products, $show_qty ) {
					$titles = [];
					$count  = 0;

					if ( ! is_array( $products ) ) {
						$products = [ $products => 1 ];
						$show_qty = false;
					}

					if ( ! $show_qty ) {
						$products = array_fill_keys( array_keys( $products ), 1 );
					}

					foreach ( $products as $product_id => $qty ) {
						/* translators: %s: product name */
						$titles[] = apply_filters( 'woocommerce_add_to_cart_qty_html', ( $qty > 1 ? (float) $qty . ' &times; ' : '' ), $product_id ) . apply_filters( 'woocommerce_add_to_cart_item_name_in_quotes', sprintf( _x( '&ldquo;%s&rdquo;', 'Item name in quotes', 'wpc-product-quantity' ), strip_tags( get_the_title( $product_id ) ) ), $product_id );
						$count    += $qty;
					}

					$titles = array_filter( $titles );
					/* translators: %s: product name */
					$added_text = sprintf( _n( '%s has been added to your cart.', '%s have been added to your cart.', $count, 'wpc-product-quantity' ), wc_format_list_of_items( $titles ) );

					if ( 'yes' === get_option( 'woocommerce_cart_redirect_after_add' ) ) {
						$return_to = apply_filters( 'woocommerce_continue_shopping_redirect', wc_get_raw_referer() ? wp_validate_redirect( wc_get_raw_referer(), false ) : wc_get_page_permalink( 'shop' ) );
						$message   = sprintf( '<a href="%s" tabindex="1" class="button wc-forward">%s</a> %s', esc_url( $return_to ), esc_html__( 'Continue shopping', 'wpc-product-quantity' ), esc_html( $added_text ) );
					} else {
						$message = sprintf( '<a href="%s" tabindex="1" class="button wc-forward">%s</a> %s', esc_url( wc_get_cart_url() ), esc_html__( 'View cart', 'wpc-product-quantity' ), esc_html( $added_text ) );
					}

					return $message;
				}

				function variation_settings( $loop, $variation_data, $variation ) {
					$variation_id = absint( $variation->ID );
					?>
					<div class="form-row form-row-full woopq-variation-settings">
						<label><?php esc_html_e( 'WPC Product Quantity', 'wpc-product-quantity' ); ?></label>
						<div class="woopq-variation-wrap woopq-variation-wrap-<?php echo esc_attr( $variation_id ); ?>">
							<?php self::product_settings( $variation_id, true ); ?>
						</div>
					</div>
					<?php
				}

				function clean_value( $str ) {
					return preg_replace( '/[^.\-0-9]/', '', $str );
				}

				function wpcsm_locations( $locations ) {
					$locations['WPC Product Quantity'] = [
						'woopq_before_wrap'           => esc_html__( 'Before wrapper', 'wpc-product-quantity' ),
						'woopq_after_wrap'            => esc_html__( 'After wrapper', 'wpc-product-quantity' ),
						'woopq_before_quantity_input' => esc_html__( 'Before quantity input', 'wpc-product-quantity' ),
						'woopq_after_quantity_input'  => esc_html__( 'After quantity input', 'wpc-product-quantity' ),
						'woopq_before_hidden_field'   => esc_html__( 'Before hidden field', 'wpc-product-quantity' ),
						'woopq_after_hidden_field'    => esc_html__( 'After hidden field', 'wpc-product-quantity' ),
						'woopq_before_select_field'   => esc_html__( 'Before select field', 'wpc-product-quantity' ),
						'woopq_after_select_field'    => esc_html__( 'After select field', 'wpc-product-quantity' ),
						'woopq_before_radio_field'    => esc_html__( 'Before radio field', 'wpc-product-quantity' ),
						'woopq_after_radio_field'     => esc_html__( 'After radio field', 'wpc-product-quantity' ),
						'woopq_before_input_field'    => esc_html__( 'Before input field', 'wpc-product-quantity' ),
						'woopq_after_input_field'     => esc_html__( 'After input field', 'wpc-product-quantity' ),
					];

					return $locations;
				}
			}

			return WPCleverWoopq::instance();
		}
	}
}

if ( ! function_exists( 'woopq_notice_wc' ) ) {
	function woopq_notice_wc() {
		?>
		<div class="error">
			<p><strong>WPC Product Quantity</strong> requires WooCommerce version 3.0 or greater.</p>
		</div>
		<?php
	}
}
