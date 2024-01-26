<?php

namespace WPCSN;

defined( 'ABSPATH' ) || exit;

class notification {
	private $args;
	private $options;
	public $data;

	function __construct() {
		$this->options = get_option( 'wpcsn_opts' );
	}

	function get_content() {
		if ( empty( $this->options['data_sources'] ) ) {
			return [];
		}

		$response = [];

		foreach ( $this->options['data_sources'] as $source ) {
			$this->data[ $source ] = $this->$source();
		}

		if ( $this->options['order'] == 'default' ) {
			foreach ( $this->options['display_order'] as $source ) {
				if ( empty( $this->data[ $source ] ) ) {
					continue;
				}

				foreach ( $this->data[ $source ] as $value ) {
					$response[] = $value;
				}
			}
		} elseif ( $this->options['order'] == 'rand' ) {
			foreach ( $this->data as $data ) {
				foreach ( $data as $value ) {
					$response[] = $value;
				}
			}

			shuffle( $response );
		}

		return [
			'data'    => $response,
			'options' => $this->options['options']
		];
	}

	function manual() {
		$response = [];

		if ( ! empty( $this->options['manual'] ) ) {
			foreach ( $this->options['manual'] as $key => $value ) {
				$item              = [];
				$item['thumbnail'] = apply_filters( 'WPCSN/manual/item_thumbnail', sprintf( '<a href="%s" target="_blank">%s</a>', esc_url( $value['link'] ), wp_get_attachment_image( $value['thumbnail'], 'wpcsn-small' ) ) );
				$item['title']     = apply_filters( 'WPCSN/manual/item_title', sprintf( '<a href="%s" target="_blank">%s</a>', esc_url( $value['link'] ), $value['title'] ), $value );
				$item['content']   = apply_filters( 'WPCSN/manual/item_content', $value['content'], $value );
				$item['class']     = 'wpcsn-notification-item-manual';
				$response[]        = $item;
			}
		}

		return $response;
	}

	function html() {
		$response = [];

		return $response;
	}

	function cart() {
		$response = [];

		$item   = [];
		$target = ! empty( $this->options['cart']['link'] ) ? esc_attr( $this->options['cart']['link'] ) : 'same';
		$link   = '<a href="' . wc_get_cart_url() . '" class="' . esc_attr( $target === 'woofc' ? 'wpcsn-cart woofc-cart' : 'wpcsn-cart' ) . '" ' . ( $target === 'new' ? 'target="_blank"' : '' ) . '>%s</a>';

		if ( ! empty( $this->options['cart']['thumbnail'] ) ) {
			$item['thumbnail'] = apply_filters( 'WPCSN/cart/item_thumbnail', sprintf( $link, wp_get_attachment_image( $this->options['cart']['thumbnail'], 'wpcsn-small' ) ) );
		}

		$item['content'] = apply_filters( 'WPCSN/cart/item_content', sprintf( $link, sprintf( esc_html__( 'Your cart: %s', 'wpc-smart-notification' ), \WPCSN\initialization::get_cart() ) ) );
		$item['class']   = 'wpcsn-notification-item-cart';
		$response[]      = $item;

		return $response;
	}

	function new_orders() {
		$response = [];

		$number = ! empty( $this->options['new_orders']['number'] ) ? absint( $this->options['new_orders']['number'] ) : 10;
		$within = ! empty( $this->options['new_orders']['within'] ) ? absint( $this->options['new_orders']['within'] ) : 24;
		$time   = strtotime( 'today' ) - $within * HOUR_IN_SECONDS;

		$args = [
			'post_type'      => wc_get_order_types(),
			'post_status'    => array_keys( wc_get_order_statuses() ),
			'posts_per_page' => $number,
			'date_query'     => [
				'after' => date( 'Y-m-d H:i:s', $time ),
			],
		];

		$the_query = new \WP_Query( $args );

		if ( $the_query->have_posts() ) {
			while ( $the_query->have_posts() ) {
				$the_query->the_post();
				$order    = wc_get_order( get_the_ID() );
				$time     = $order->get_date_created()->format( 'U' );
				$time_ago = time() - $time;

				if ( $time_ago < HOUR_IN_SECONDS ) {
					$time_str = sprintf( _n( 'about %d minute ago.', 'about %d minutes ago.', round( $time_ago / MINUTE_IN_SECONDS ), 'wpc-smart-notification' ), round( $time_ago / MINUTE_IN_SECONDS ) );
				} elseif ( $time_ago < DAY_IN_SECONDS ) {
					$time_str = sprintf( _n( 'about %d hour ago.', 'about %d hours ago.', round( $time_ago / HOUR_IN_SECONDS ), 'wpc-smart-notification' ), round( $time_ago / HOUR_IN_SECONDS ) );
				} else {
					$time_str = sprintf( _n( 'about %d day ago.', 'about %d days ago.', round( $time_ago / DAY_IN_SECONDS ), 'wpc-smart-notification' ), round( $time_ago / DAY_IN_SECONDS ) );
				}

				$item = [];

				foreach ( $order->get_items() as $item_id => $_item ) {
					$product = $_item->get_product();

					if ( ! empty( $product->get_name() ) ) {
						$link = '<a href="%s" target="' . esc_attr( $this->options['options']['link'] === 'new' ? '_blank' : '_self' ) . '" ' . ( $this->options['options']['link'] === 'woosq' ? 'class="woosq-btn" data-id="' . $product->get_id() . '"' : '' ) . '>%s</a>';

						$item['thumbnail'] = sprintf( $link, $product->get_permalink(), $product->get_image( 'wpcsn-small' ) );
						$item['content']   = sprintf( $link, $product->get_permalink(), $product->get_name() );
						break;
					}
				}

				$_name    = $order->get_formatted_billing_full_name();
				$_address = $order->get_billing_city();

				if ( empty( $_address ) ) {
					$_address = $order->get_billing_country();
				}

				if ( empty( $_name ) || empty( $_address ) ) {
					continue;
				}

				$item['thumbnail'] = apply_filters( 'WPCSN/new_orders/item_thumbnail', $item['thumbnail'], $order );
				$item['title']     = apply_filters( 'WPCSN/new_orders/item_title', sprintf( esc_html__( '%s from %s purchased a', 'wpc-smart-notification' ), '<strong>' . $_name . '</strong>', '<strong>' . $_address . '</strong>' ), $_name, $_address, $order );
				$item['content']   = apply_filters( 'WPCSN/new_orders/item_content', $item['content'], $order );
				$item['time']      = apply_filters( 'WPCSN/new_orders/item_time', $time_str, $time, $order );
				$item['class']     = 'wpcsn-notification-item-new-order';

				$response[] = $item;
			}
		}

		// Reset Post Data
		wp_reset_postdata();

		return $response;
	}

	function virtual_orders() {
		$response = [];

		$number = ! empty( $this->options['virtual_orders']['number'] ) ? absint( $this->options['virtual_orders']['number'] ) : 10;
		$index  = 0;

		if ( empty( $this->options['virtual_orders']['name'] ) || empty( $this->options['virtual_orders']['address'] ) ) {
			return $response;
		}

		$data = [
			'name'     => $this->options['virtual_orders']['name'],
			'address'  => $this->options['virtual_orders']['address'],
			'products' => $this->options['virtual_orders']['products'],
			'within'   => $this->options['virtual_orders']['within']
		];

		if ( empty( $data['products'] ) ) {
			$args             = [
				'post_type'      => 'product',
				'post_status'    => 'publish',
				'posts_per_page' => $number,
			];
			$data['products'] = [];
			$the_query        = new \WP_Query( $args );

			if ( $the_query->have_posts() ) {
				while ( $the_query->have_posts() ) {
					$the_query->the_post();
					$data['products'][] = get_the_ID();
				}
			} else {
				return [];
			}
			// Reset Post Data
			wp_reset_postdata();
		}

		while ( $index < $number ) {
			$name       = $data['name'][ rand( 0, count( $data['name'] ) - 1 ) ];
			$address    = $data['address'][ rand( 0, count( $data['address'] ) - 1 ) ];
			$product_id = $data['products'][ rand( 0, count( $data['products'] ) - 1 ) ];
			$time_ago   = rand( 60, absint( $data['within'] ) );
			$product    = wc_get_product( $product_id );

			if ( empty( $product ) ) {
				continue;
			}

			if ( $time_ago < HOUR_IN_SECONDS ) {
				$time_str = sprintf( _n( 'about %d minute ago.', 'about %d minutes ago.', round( $time_ago / MINUTE_IN_SECONDS ), 'wpc-smart-notification' ), round( $time_ago / MINUTE_IN_SECONDS ) );
			} elseif ( $time_ago < DAY_IN_SECONDS ) {
				$time_str = sprintf( _n( 'about %d hour ago.', 'about %d hours ago.', round( $time_ago / HOUR_IN_SECONDS ), 'wpc-smart-notification' ), round( $time_ago / HOUR_IN_SECONDS ) );
			} else {
				$time_str = sprintf( _n( 'about %d day ago.', 'about %d days ago.', round( $time_ago / DAY_IN_SECONDS ), 'wpc-smart-notification' ), round( $time_ago / DAY_IN_SECONDS ) );
			}

			$link = '<a href="%s" target="' . esc_attr( $this->options['options']['link'] === 'new' ? '_blank' : '_self' ) . '" ' . ( $this->options['options']['link'] === 'woosq' ? 'class="woosq-btn" data-id="' . $product->get_id() . '"' : '' ) . '>%s</a>';

			$item              = [];
			$item['thumbnail'] = apply_filters( 'WPCSN/virtual_orders/item_thumbnail', sprintf( $link, $product->get_permalink(), $product->get_image( 'wpcsn-small' ) ) );
			$item['title']     = apply_filters( 'WPCSN/virtual_orders/item_title', sprintf( esc_html__( '%s from %s purchased a', 'wpc-smart-notification' ), '<strong>' . $name . '</strong>', '<strong>' . $address . '</strong>' ), $name, $address, $data );
			$item['content']   = apply_filters( 'WPCSN/virtual_orders/item_content', sprintf( $link, $product->get_permalink(), $product->get_name() ), $product, $data );
			$item['time']      = apply_filters( 'WPCSN/virtual_orders/item_time', $time_str, $product, $data );
			$item['class']     = 'wpcsn-notification-item-virtual-order';

			$response[] = $item;
			$index ++;
		}

		return $response;
	}

	function on_sale_products() {
		$response = [];

		$number      = ! empty( $this->options['on_sale_products']['number'] ) ? absint( $this->options['on_sale_products']['number'] ) : 10;
		$product_ids = wc_get_product_ids_on_sale();
		$product_ids = array_slice( $product_ids, 0, $number );

		foreach ( $product_ids as $product_id ) {
			$product = wc_get_product( $product_id );

			if ( empty( $product ) ) {
				continue;
			}

			$link = '<a href="%s" target="' . esc_attr( $this->options['options']['link'] === 'new' ? '_blank' : '_self' ) . '" ' . ( $this->options['options']['link'] === 'woosq' ? 'class="woosq-btn" data-id="' . $product->get_id() . '"' : '' ) . '>%s</a>';

			$item              = [];
			$item['thumbnail'] = apply_filters( 'WPCSN/on_sale_products/item_thumbnail', sprintf( $link, $product->get_permalink(), $product->get_image( 'wpcsn-small' ) ) );
			$item['title']     = apply_filters( 'WPCSN/on_sale_products/item_title', sprintf( esc_html__( '%s is on-sale. Hurry up!', 'wpc-smart-notification' ), sprintf( $link, $product->get_permalink(), $product->get_name() ) ), $product, $product_ids );
			$item['price']     = apply_filters( 'WPCSN/on_sale_products/item_price', $product->get_price_html(), $product, $product_ids );
			$item['class']     = 'wpcsn-notification-item-on-sale';

			$response[] = $item;
		}

		return $response;
	}

	function low_stock_products() {
		$response = [];

		$number              = ! empty( $this->options['low_stock_products']['number'] ) ? absint( $this->options['low_stock_products']['number'] ) : 10;
		$low_stock_threshold = ! empty( $this->options['low_stock_products']['low_stock_threshold'] ) ? absint( $this->options['low_stock_products']['low_stock_threshold'] ) : 10;

		$args = [
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'posts_per_page' => $number,
			'meta_query'     => [
				'relation' => 'AND',
				[
					'key'     => '_stock',
					'type'    => 'NUMERIC',
					'value'   => [ 1, $low_stock_threshold ],
					'compare' => 'BETWEEN'
				]
			],
		];

		$the_query = new \WP_Query( $args );

		if ( $the_query->have_posts() ) {
			while ( $the_query->have_posts() ) {
				$the_query->the_post();
				$product_id = get_the_ID();
				$product    = wc_get_product( $product_id );

				if ( empty( $product ) ) {
					continue;
				}

				$link = '<a href="%s" target="' . esc_attr( $this->options['options']['link'] === 'new' ? '_blank' : '_self' ) . '" ' . ( $this->options['options']['link'] === 'woosq' ? 'class="woosq-btn" data-id="' . $product->get_id() . '"' : '' ) . '>%s</a>';

				$item              = [];
				$item['thumbnail'] = apply_filters( 'WPCSN/low_stock_products/item_thumbnail', sprintf( $link, $product->get_permalink(), $product->get_image( 'wpcsn-small' ) ) );
				$item['title']     = apply_filters( 'WPCSN/low_stock_products/item_title', sprintf( esc_html__( '%s has %d left only. Don\'t miss it!', 'wpc-smart-notification' ), sprintf( $link, $product->get_permalink(), $product->get_name() ), $product->get_stock_quantity() ), $product );
				$item['price']     = apply_filters( 'WPCSN/low_stock_products/item_price', $product->get_price_html(), $product );
				$item['class']     = 'wpcsn-notification-item-low-stock';

				$response[] = $item;
			}
		}

		// Reset Post Data
		wp_reset_postdata();

		return $response;
	}

	function related_products() {
		$response = [];

		$number     = ! empty( $this->options['related_products']['number'] ) ? absint( $this->options['related_products']['number'] ) : 10;
		$product_id = ! empty( $this->args['ID'] ) ? absint( $this->args['ID'] ) : 0;
		$product    = wc_get_product( $product_id );

		if ( $product ) {
			$args = apply_filters(
				'woocommerce_top_rated_products_args',
				[
					'posts_per_page' => $number,
					'orderby'        => 'rand',
					'order'          => 'desc',
				]
			);

			$products            = wc_get_related_products( $product->get_id(), $args['posts_per_page'], $product->get_upsell_ids() );
			$related_product_ids = wc_products_array_orderby( $products, $args['orderby'], $args['order'] );
		} else {
			$args = apply_filters(
				'woocommerce_top_rated_products_args',
				[
					'posts_per_page' => $number,
					'no_found_rows'  => 1,
					'post_status'    => 'publish',
					'post_type'      => 'product',
					'meta_key'       => '_wc_average_rating',
					'orderby'        => 'meta_value_num',
					'order'          => 'DESC',
					'meta_query'     => WC()->query->get_meta_query(),
					'tax_query'      => WC()->query->get_tax_query(),
				]
			);

			$the_query           = new \WP_Query( $args );
			$related_product_ids = [];

			if ( $the_query->have_posts() ) {
				while ( $the_query->have_posts() ) {
					$the_query->the_post();
					$related_product_ids[] = get_the_ID();
				}
			}

			wp_reset_postdata();
		}

		foreach ( $related_product_ids as $product_id ) {
			$product = wc_get_product( $product_id );

			if ( empty( $product ) ) {
				continue;
			}

			$link = '<a href="%s" target="' . esc_attr( $this->options['options']['link'] === 'new' ? '_blank' : '_self' ) . '" ' . ( $this->options['options']['link'] === 'woosq' ? 'class="woosq-btn" data-id="' . $product->get_id() . '"' : '' ) . '>%s</a>';

			$item              = [];
			$item['thumbnail'] = apply_filters( 'WPCSN/related_products/item_thumbnail', sprintf( $link, $product->get_permalink(), $product->get_image( 'wpcsn-small' ) ) );
			$item['title']     = apply_filters( 'WPCSN/related_products/item_title', sprintf( esc_html__( 'You may also like %s', 'wpc-smart-notification' ), sprintf( $link, $product->get_permalink(), $product->get_name() ) ), $product );
			$item['price']     = apply_filters( 'WPCSN/related_products/item_price', $product->get_price_html(), $product );
			$item['class']     = 'wpcsn-notification-item-related';

			$response[] = $item;
		}

		return $response;
	}

	function viewing() {
		$response = [];

		$product_id = ! empty( $this->args['ID'] ) ? absint( $this->args['ID'] ) : 0;
		$product    = wc_get_product( $product_id );

		if ( $product ) {
			// is single product page
			$item    = [];
			$min     = ! empty( $this->options['viewing']['range']['min'] ) ? absint( $this->options['viewing']['range']['min'] ) : 10;
			$max     = ! empty( $this->options['viewing']['range']['max'] ) ? absint( $this->options['viewing']['range']['max'] ) : 100;
			$viewing = sprintf( esc_attr__( '%s people are viewing this product now.', 'wpc-smart-notification' ), rand( $min, $max ) );

			if ( ! empty( $this->options['viewing']['thumbnail'] ) ) {
				$item['thumbnail'] = apply_filters( 'WPCSN/viewing/item_thumbnail', wp_get_attachment_image( $this->options['viewing']['thumbnail'], 'wpcsn-small' ) );
			}

			$item['content'] = apply_filters( 'WPCSN/viewing/item_content', $viewing );
			$item['class']   = 'wpcsn-notification-item-viewing';
			$response[]      = $item;
		}

		return $response;
	}

	public static function get_instance( $args ) {
		static $single;

		if ( empty( $single ) ) {
			$single       = new self();
			$single->args = $args;
		}

		return $single;
	}
}
