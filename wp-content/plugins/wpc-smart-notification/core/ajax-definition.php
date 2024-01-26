<?php

namespace WPCSN;

defined( 'ABSPATH' ) || exit;

class ajax {
	function __construct() {
		global $wpdb;

		if ( empty( $_POST['_nonce'] ) || ! wp_verify_nonce( $_POST['_nonce'], 'wpcsn' ) ) {
			$msg = 'Bad request!';
			goto response;
		}

		include WPCSN_DIR . '/core/ajax-processing.php';

		if ( ! empty( $_POST['form_data'] ) ) {
			$data = ! is_array( $_POST['form_data'] ) ? self::parse_request( sanitize_text_field( $_POST['form_data'] ) ) : (array) $_POST['form_data'];
		} else {
			$data = [];
		}

		if ( ! empty( $data['action'] ) ) {
			$response = apply_filters( 'WPCSN/' . $data['action'], [], $data );
		} else {
			$msg = 'Bad request!';
			goto response;
		}

		response:
		$wpdb->hide_errors();

		while ( ob_get_level() ) {
			@ob_end_clean();
		}

		header( 'Content-Type: application/json' );

		if ( ! empty( $msg ) ) {
			header( 'HTTP/1.1 400 Bad Request' );
			$response = [
				'status' => 400,
				'alert'  => $msg
			];
		}

		echo json_encode( $response );
		exit;
	}

	public static function parse_request( $string ) {
		if ( '' == $string ) {
			return false;
		} elseif ( is_array( $string ) ) {
			return $string;
		}

		$result = [];
		$pairs  = explode( '&', $string );

		foreach ( $pairs as $key => $pair ) {
			// use the original parse_str() on each element
			parse_str( $pair, $params );

			$k = key( $params );

			if ( ! isset( $result[ $k ] ) ) {
				$result += $params;
			} elseif ( ! is_array( $params[ $k ] ) ) {
				$result[ $k ] = $params[ $k ];
			} else {
				$result[ $k ] = self::array_merge_recursive_distinct( $result[ $k ], $params[ $k ] );
			}
		}

		return $result;
	}

	public static function array_merge_recursive_distinct( array $array1 = [], array $array2 = [] ) {
		$merged = [];
		$merged = $array1;

		foreach ( $array2 as $key => $value ) {
			if ( is_array( $value ) && isset( $merged[ $key ] ) && is_array( $merged[ $key ] ) ) {
				$merged[ $key ] = self::array_merge_recursive_distinct( $merged[ $key ], $value );
			} else if ( is_numeric( $key ) && isset( $merged[ $key ] ) ) {
				$merged[] = $value;
			} else {
				$merged[ $key ] = $value;
			}
		}

		return $merged;
	}
}
