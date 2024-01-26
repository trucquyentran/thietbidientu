<?php

namespace WPCSN;

defined( 'ABSPATH' ) || exit;

class initialization {
	function __construct() {
		add_action( 'init', [ $this, 'init' ] );
		add_action( 'admin_menu', [ $this, 'admin_menu' ], 20 );
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_script' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_style' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_script' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_style' ] );
		add_action( 'wp_ajax_wpcsn', [ $this, 'ajax' ] );
		add_action( 'wp_ajax_nopriv_wpcsn', [ $this, 'ajax' ] );
		add_action( 'wp_footer', [ $this, 'get_footer' ] );
		add_filter( 'woocommerce_add_to_cart_fragments', [ $this, 'cart_fragments' ] );
		add_filter( 'plugin_action_links', [ $this, 'action_links' ], 10, 2 );
		add_filter( 'plugin_row_meta', [ $this, 'row_meta' ], 10, 2 );

		// HPOS compatibility
		add_action( 'before_woocommerce_init', function () {
			if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
				\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', WPCSN_FILE );
			}
		} );
	}

	function init() {
		load_plugin_textdomain( 'wpc-smart-notification', false, WPCSN_DIR . '/languages/' );

		// add image sizes
		add_image_size( 'wpcsn-small', 80, 80, true );
	}

	function ajax() {
		include WPCSN_DIR . '/core/ajax-definition.php';
		new ajax;
		die;
	}

	function enqueue_style( $hook ) {
		wp_enqueue_style( 'wpcsn', WPCSN_URI . 'assets/css/frontend.css', false, WPCSN_VERSION );
	}

	function enqueue_script( $hook ) {
		global $post;

		$data = [
			'ID'      => isset( $post->ID ) && is_product() ? $post->ID : 0,
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'wpcsn' ),
		];

		wp_enqueue_script( 'wpcsn', WPCSN_URI . 'assets/js/frontend.js', [ 'jquery' ], WPCSN_VERSION, true );
		wp_localize_script( 'wpcsn', 'WPCSNOptions', $data );
	}

	function admin_enqueue_style( $hook ) {
		if ( empty( $_REQUEST['page'] ) || $_REQUEST['page'] != 'wpclever-wpcsn' ) {
			return;
		}

		wp_enqueue_style( 'noty', WPCSN_URI . 'assets/css/noty.css', false, WPCSN_VERSION );
		wp_enqueue_style( 'tipsy', WPCSN_URI . 'assets/css/tipsy.css', false, WPCSN_VERSION );
		wp_enqueue_style( 'select2', WPCSN_URI . 'assets/css/select2.min.css', false, WPCSN_VERSION );
		wp_enqueue_style( 'wpcsn', WPCSN_URI . 'assets/css/backend.css', false, WPCSN_VERSION );
	}

	function admin_enqueue_script( $hook ) {
		if ( empty( $_REQUEST['page'] ) || $_REQUEST['page'] != 'wpclever-wpcsn' ) {
			return;
		}

		wp_enqueue_media();
		wp_enqueue_script( 'noty', WPCSN_URI . 'assets/js/noty.min.js', [ 'jquery' ], WPCSN_VERSION, true );
		wp_enqueue_script( 'tipsy', WPCSN_URI . 'assets/js/tipsy.min.js', [ 'jquery' ], WPCSN_VERSION, true );
		wp_enqueue_script( 'select2', WPCSN_URI . 'assets/js/select2.min.js', [ 'jquery' ], WPCSN_VERSION, true );
		wp_enqueue_script( 'wpcsn', WPCSN_URI . 'assets/js/backend.js', [
			'jquery',
			'jquery-ui-sortable',
			'select2',
			'tipsy',
			'noty'
		], WPCSN_VERSION, true );
		$data = [
			'ajaxurl'  => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'wpcsn' ),
			'saveFail' => esc_html__( 'Can\'t save data', 'wpc-smart-notification' )
		];
		wp_localize_script( 'wpcsn', 'WPCSNOptions', $data );
	}

	function admin_menu() {
		add_submenu_page( 'wpclever', 'WPC Smart Notifications', 'Smart Notifications', 'manage_options', 'wpclever-wpcsn', [
			$this,
			'admin_menu_content'
		] );
	}

	function admin_menu_content() {
		include WPCSN_DIR . '/core/options.php';
		$options = options::get_instance();
		$options->get_html();
	}

	function get_footer() {
		if ( is_admin() ) {
			return;
		}

		$opts    = get_option( 'wpcsn_opts' );
		$disable = isset( $opts['options']['disable'] ) ? $opts['options']['disable'] : [];

		if ( ( in_array( 'cart', $disable ) && is_cart() ) || ( in_array( 'checkout', $disable ) && is_checkout() ) || ( in_array( 'mobile', $disable ) && wp_is_mobile() ) ) {
			return;
		}

		echo '<div class="wpcsn-notification"></div>';
	}

	function cart_fragments( $fragments ) {
		$fragments['.wpcsn-cart-data'] = self::get_cart();

		return $fragments;
	}

	public static function get_cart() {
		$cart_count    = WC()->cart->get_cart_contents_count();
		$cart_subtotal = WC()->cart->get_cart_subtotal();

		return '<span class="wpcsn-cart-data" data-count="' . esc_attr( $cart_count ) . '" data-subtotal="' . esc_attr( $cart_subtotal ) . '"> <span class="wpcsn-cart-subtotal">' . $cart_subtotal . '</span> <span class="wpcsn-cart-count">' . sprintf( _n( '(%s item)', '(%s items)', $cart_count, 'wpc-smart-notification' ), number_format_i18n( $cart_count ) ) . '</span></span>';
	}

	public static function get_instance() {
		global $wpcsn;

		if ( empty( $wpcsn ) ) {
			$wpcsn = new self();
		}

		return $wpcsn;
	}

	function action_links( $links, $file ) {
		static $plugin;

		if ( ! isset( $plugin ) ) {
			$plugin = plugin_basename( WPCSN_DIR . '/' . basename( WPCSN_FILE ) );
		}

		if ( $plugin === $file ) {
			$settings             = '<a href="' . admin_url( 'admin.php?page=wpclever-wpcsn&tab=settings' ) . '">' . esc_html__( 'Settings', 'wpc-smart-notification' ) . '</a>';
			$links['wpc-premium'] = '<a href="' . admin_url( 'admin.php?page=wpclever-wpcsn&tab=premium' ) . '" style="color: #c9356e">' . esc_html__( 'Premium Version', 'wpc-smart-notification' ) . '</a>';
			array_unshift( $links, $settings );
		}

		return (array) $links;
	}

	function row_meta( $links, $file ) {
		static $plugin;

		if ( ! isset( $plugin ) ) {
			$plugin = plugin_basename( WPCSN_DIR . '/' . basename( WPCSN_FILE ) );
		}

		if ( $plugin === $file ) {
			$row_meta = [
				'support' => '<a href="' . esc_url( WPCSN_DISCUSSION ) . '" target="_blank">' . esc_html__( 'Community support', 'wpc-smart-notification' ) . '</a>',
			];

			return array_merge( $links, $row_meta );
		}

		return (array) $links;
	}
}