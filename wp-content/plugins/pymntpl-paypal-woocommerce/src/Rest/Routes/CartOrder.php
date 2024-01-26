<?php

namespace PaymentPlugins\WooCommerce\PPCP\Rest\Routes;

use PaymentPlugins\WooCommerce\PPCP\Constants;

/**
 * Route that is called when the PayPal integration requests an order ID.
 */
class CartOrder extends AbstractCart {

	public function get_path() {
		return 'cart/order';
	}

	public function get_routes() {
		return [
			[
				'methods'  => \WP_REST_Server::CREATABLE,
				'callback' => [ $this, 'handle_request' ],
				'args'     => [
					'payment_method' => [
						'required' => true
					]
				]
			]
		];
	}

	public function handle_post_request( \WP_REST_Request $request ) {
		wc_maybe_define_constant( 'WOOCOMMERCE_CHECKOUT', true );
		$this->update_customer_data( WC()->customer, $request );
		$this->populate_post_data( $request );
		$this->calculate_totals();
		$order = $this->get_order_from_cart( $request );
		try {
			$result = $this->client->orders->create( $order );
			if ( is_wp_error( $result ) ) {
				throw new \Exception( $result->get_error_message() );
			}
			$this->cache->set( Constants::CAN_UPDATE_ORDER_DATA, true );
			$this->cache->set( Constants::PAYPAL_ORDER_ID, $result->id );

			return $result->id;
		} catch ( \Exception $e ) {
			$this->logger->error( sprintf( 'Error creating PayPal order. Msg:%s Params: %s', $e->getMessage(), print_r( $order->toArray(), true ) ) );
			throw new \Exception( $e->getMessage(), 400 );
		}
	}

	/**
	 * @param \WC_Customer     $customer
	 * @param \WP_REST_Request $request
	 */
	private function update_customer_data( $customer, $request ) {
		$customer->set_billing_email( isset( $request['billing_email'] ) ? $request['billing_email'] : null );
		$fields         = [ 'first_name', 'last_name', 'country', 'state', 'postcode', 'city', 'address_1', 'address_2' ];
		$billing_prefix = apply_filters( 'wc_ppcp_cart_order_billing_prefix', 'billing', $request );
		$props          = [];
		foreach ( $fields as $field ) {
			$key                     = "{$billing_prefix}_{$field}";
			$props["billing_$field"] = isset( $request[ $key ] ) ? wc_clean( wp_unslash( $request[ $key ] ) ) : null;
		}
		if ( wc_ship_to_billing_address_only() ) {
			$customer->set_props( $props );
		} else {
			$shipping_prefix = apply_filters( 'wc_ppcp_cart_order_shipping_prefix', isset( $request['ship_to_different_address'] ) ? 'shipping' : 'billing', $request );
			foreach ( $fields as $field ) {
				$key                      = "{$shipping_prefix}_{$field}";
				$props["shipping_$field"] = isset( $request[ $key ] ) ? wc_clean( wp_unslash( $request[ $key ] ) ) : null;
			}
			$customer->set_props( $props );
		}
	}

}