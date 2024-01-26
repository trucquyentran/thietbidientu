<?php

namespace WPCSN;

defined( 'ABSPATH' ) || exit;

class ajaxProcessing {
	public $error;

	function __construct() {
		add_filter( 'WPCSN/save_form', [ $this, 'save_form' ], 10, 2 );
		add_filter( 'WPCSN/fill/products', [ $this, 'fill_products' ], 10, 2 );
		add_filter( 'WPCSN/get_content', [ $this, 'get_content' ], 10, 2 );
	}

	function get_content( $response = [], $data = [] ) {
		include WPCSN_DIR . '/core/notification.php';
		$notification = notification::get_instance( $data );
		$response     = $notification->get_content();

		return $response;
	}

	function save_form( $response = [], $data = [] ) {
		$response = [
			'status' => 'error',
			'alert'  => '',
		];

		$data = ! is_array( $data['data'] ) ? ajax::parse_request( $data['data'] ) : $data['data'];
		include WPCSN_DIR . '/core/options.php';
		$options = options::get_instance();

		if ( $options->save_form( $data ) !== false ) {
			$response['status'] = 'success';
			$response['alert']  = esc_html__( 'Save Successful', 'wpc-smart-notification' );
		} else {
			$response['status'] = 'success';
			$response['alert']  = esc_html__( 'No change', 'wpc-smart-notification' );
		}

		return $response;
	}

	function fill_products( $response = [], $data = [] ) {
		global $wpdb;
		$q        = '';
		$page     = ! empty( $data['page'] ) ? max( 1, absint( $data['page'] ) ) : 1;
		$limit    = ! empty( $data['limit'] ) ? max( 1, absint( $data['limit'] ) ) : 10;
		$offset   = ( $page - 1 ) * $limit;
		$selected = array_filter( is_array( $data['selected'] ) ? $data['selected'] : [ $data['selected'] ], function ( $item ) {
			return is_numeric( $item ) && $item > 0 ? true : false;
		} );

		if ( wp_doing_ajax() ) {
			$q = $wpdb->prepare( 'AND `post_title` LIKE %s', '%' . $wpdb->esc_like( $data['q'] ) . '%' );
			$q .= ! empty( $selected ) ? sprintf( ' AND ID NOT IN ("%s")', implode( '","', $selected ) ) : '';
		} elseif ( ! empty( $selected ) ) {
			$q = sprintf( 'AND ID IN ("%s")', implode( '","', $selected ) );
		}

		$query = sprintf( 'SELECT SQL_CALC_FOUND_ROWS ID AS id, post_title AS text FROM %s WHERE post_type = "product" AND `post_status`="publish" %s LIMIT %d OFFSET %d',
			$wpdb->posts, $q, $limit, $offset );

		$data['items']       = ! empty( $q ) ? $wpdb->get_results( $query, ARRAY_A ) : [];
		$data['total_items'] = $wpdb->get_var( 'SELECT FOUND_ROWS()' );

		return $data;
	}

	public static function get_instance() {
		static $single;

		if ( empty( $single ) ) {
			$single = new self();
		}

		return $single;
	}
}

ajaxProcessing::get_instance();
