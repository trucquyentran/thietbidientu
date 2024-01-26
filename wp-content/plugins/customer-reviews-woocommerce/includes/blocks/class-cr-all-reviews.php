<?php

if (! defined('ABSPATH')) {
	exit;
}

if (! class_exists('CR_All_Reviews')) :

	class CR_All_Reviews
	{

		/**
		* @var array holds the current shorcode attributes
		*/
		private $shortcode_atts;
		private $shop_page_id;
		private $ivrating = 'ivrating';
		private $crsearch = 'crsearch';
		private $search = '';

		public function __construct() {
			$this->register_shortcode();
			$this->shop_page_id = wc_get_page_id( 'shop' );
			add_action( 'wp_enqueue_scripts', array( $this, 'cr_style_1' ) );
			add_action( 'wp_ajax_cr_show_more_all_reviews', array( $this, 'show_more_reviews' ) );
			add_action( 'wp_ajax_nopriv_cr_show_more_all_reviews', array( $this, 'show_more_reviews' ) );
			add_action( 'wp_ajax_cr_submit_review', array( $this, 'submit_review' ) );
			add_action( 'wp_ajax_nopriv_cr_submit_review', array( $this, 'submit_review' ) );
		}

		public function register_shortcode() {
			add_shortcode( 'cusrev_all_reviews', array( $this, 'render_all_reviews_shortcode' ) );
		}

		private function fill_attributes( $attributes ) {
			$defaults = array(
				'sort' => 'desc',
				'sort_by' => 'date',
				'per_page' => 10,
				'number' => -1,
				'show_summary_bar' => 'true',
				'show_pictures' => 'false',
				'show_products' => 'true',
				'categories' => [],
				'products' => [],
				'shop_reviews' => 'true',
				'number_shop_reviews' => -1,
				'inactive_products' => 'false',
				'show_replies' => 'false',
				'product_tags' => [],
				'show_more' => 5,
				'min_chars' => 0,
				'avatars' => 'initials',
				'users' => 'all',
				'add_review' => false
			);

			if ( isset( $attributes['categories'] ) && !is_array( $attributes['categories'] ) ) {
				$categories = str_replace( ' ', '', $attributes['categories'] );
				$categories = explode( ',', $categories );
				$categories = array_filter( $categories, 'is_numeric' );
				$categories = array_map( 'intval', $categories );

				$attributes['categories'] = $categories;
			}

			if ( isset( $attributes['products'] ) ) {
				if ( ! is_array( $attributes['products'] ) ) {
					$products = str_replace( ' ', '', $attributes['products'] );
					$products = explode( ',', $products );
					$products = array_filter( $products, 'is_numeric' );
					$products = array_map( 'intval', $products );

					$attributes['products'] = $products;
				}
			} else {
				$attributes['products'] = array();
			}

			if( ! empty( $attributes['product_tags'] ) && !is_array( $attributes['product_tags'] ) ) {
				$attributes['product_tags'] = array_filter( array_map( 'trim', explode( ',', $attributes['product_tags'] ) ) );
				$tagged_products = CR_Reviews_Slider::cr_products_by_tags( $attributes['product_tags'] );
				$attributes['products'] = array_merge( $attributes['products'], $tagged_products );
			}

			$this->shortcode_atts = shortcode_atts( $defaults, $attributes );
			$this->shortcode_atts['show_summary_bar'] = $this->shortcode_atts['show_summary_bar'] === 'true' ? true : false;
			$this->shortcode_atts['show_pictures'] = $this->shortcode_atts['show_pictures'] === 'true' ? true : false;
			$this->shortcode_atts['show_products'] = $this->shortcode_atts['show_products'] === 'true' ? true : false;
			$this->shortcode_atts['shop_reviews'] = $this->shortcode_atts['shop_reviews'] === 'true' ? true : false;
			$this->shortcode_atts['inactive_products'] = $this->shortcode_atts['inactive_products'] === 'true' ? true : false;
			$this->shortcode_atts['show_replies'] = $this->shortcode_atts['show_replies'] === 'true' ? true : false;
			$this->shortcode_atts['sort'] = strtolower( $this->shortcode_atts['sort'] );
			$this->shortcode_atts['sort_by'] = strtolower( $this->shortcode_atts['sort_by'] );
			$this->shortcode_atts['show_more'] = absint( $this->shortcode_atts['show_more'] );
			if( !empty( $this->shortcode_atts['show_more'] ) ) {
				$this->shortcode_atts['per_page'] = $this->shortcode_atts['show_more'];
			}
			$this->shortcode_atts['min_chars'] = intval( $this->shortcode_atts['min_chars'] );
			if( $this->shortcode_atts['min_chars'] < 0 ) {
				$this->shortcode_atts['min_chars'] = 0;
			}
			if(
				$this->shortcode_atts['avatars'] !== 'standard' &&
				$this->shortcode_atts['avatars'] !== 'hidden'
			) {
				$this->shortcode_atts['avatars'] = 'initials';
			}
			$this->shortcode_atts['users'] = strtolower( $this->shortcode_atts['users'] );
			if( 'current' !== $this->shortcode_atts['users'] ) {
				$this->shortcode_atts['users'] = 'all';
			}
			$this->shortcode_atts['add_review'] = $this->shortcode_atts['add_review'] === 'true' ? true : false;
		}

		public function render_all_reviews_shortcode( $attributes ) {
			if ( ! is_array( $attributes ) ) {
				// if the shortcode is used without parameters, $attributes will be an empty string
				$attributes = array();
			}
			$this->fill_attributes( $attributes );
			return $this->display_reviews();
		}

		public function get_reviews() {

			$comments = array();

			$number = $this->shortcode_atts['number'] == -1 ? null : intval( $this->shortcode_atts['number'] );
			if( 0 < $number || null === $number ) {
				$args = array(
					'number'      => $number,
					'status'      => 'approve',
					'post_type'   => 'product',
					'orderby'     => 'comment_date_gmt',
					'order'       => $this->shortcode_atts['sort'],
					'post__in'    => $this->shortcode_atts['products'],
					'type__not_in' => 'cr_qna'
				);
				// filter by the current user if 'users' parameter was provided in the shortcode
				if ( 'current' === $this->shortcode_atts['users'] ) {
					$current_user = get_current_user_id();
					if ( 0 < $current_user ) {
						$args['user_id'] = $current_user;
					}
				}
				//
				if( $this->shortcode_atts['sort_by'] === 'helpful' ) {
					$args['meta_query'] = array(
						array(
							'relation' => 'OR',
							array(
								'key' => 'ivole_review_votes',
								'type' => 'NUMERIC',
								'compare' => 'NOT EXISTS'
							),
							array(
								'key' => 'ivole_review_votes',
								'type' => 'NUMERIC',
								'compare' => 'EXISTS'
							)
						)
					);

					$args['orderby'] = array(
						'meta_value_num',
						'comment_date_gmt'
					);
				}

				// search
				$args['search'] = $this->search;

				if( !$this->shortcode_atts['inactive_products'] ) {
					$args['post_status'] = 'publish';
				}

				$filtered_by_rating = false;
				if( get_query_var( $this->ivrating ) ) {
					$rating = intval( get_query_var( $this->ivrating ) );
					if( $rating > 0 && $rating <= 5 ) {
						$args['meta_query']['relation'] = 'AND';
						$args['meta_query'][] = array(
							'key' => 'rating',
							'value'   => $rating,
							'compare' => '=',
							'type'    => 'numeric'
						);
						$filtered_by_rating = true;
					}
				}
				// if display of replies is disabled and there is no filter by rating,
				// apply an additional condition to show only comments with rating meta fields only
				if( !$this->shortcode_atts['show_replies'] && !$filtered_by_rating ) {
					$args['meta_query']['relation'] = 'AND';
					$args['meta_query'][] = array(
						'key' => 'rating',
						'compare' => 'EXISTS',
						'type' => 'numeric'
					);
				}

				// Query needs to be modified if min_chars constraints are set
				if ( ! empty( $this->shortcode_atts['min_chars'] ) ) {
					add_filter( 'comments_clauses', array( $this, 'min_chars_comments_clauses' ) );
				}
				// Query needs to be modified if category constraints are set
				if ( ! empty( $this->shortcode_atts['categories'] ) ) {
					add_filter( 'comments_clauses', array( $this, 'modify_comments_clauses' ) );
				}

				// check if there are any featured product reviews
				$args_f = $args;
				$args_f['meta_query'][] = array(
					'key' => 'ivole_featured',
					'compare' => '>',
					'value' => '0',
					'type' => 'NUMERIC'
				);
				$featured_reviews = get_comments( $args_f );
				if( 0 < count( $featured_reviews ) ) {
					$featured_reviews = array_map( function( $fr ) {
							$fr->comment_karma = 1;
							return $fr;
						},
						$featured_reviews
					);
					$args['comment__not_in'] = array_map( function( $fr ) { return $fr->comment_ID; }, $featured_reviews );
				}

				// get product reviews
				$comments = get_comments( $args );

				remove_filter( 'comments_clauses', array( $this, 'modify_comments_clauses' ) );
				remove_filter( 'comments_clauses', array( $this, 'min_chars_comments_clauses' ) );

				if( 0 < count( $featured_reviews ) ) {
					$comments = array_merge( $featured_reviews, $comments );
				}

				//highlight search results for products
				if( !empty( $this->search ) ) {
					$highlight = $this->search;
					$comments = array_map( function( $item ) use( $highlight ) {
						$item->comment_content = preg_replace( '/(' . $highlight . ')(?![^<>]*\/>)/iu', '<span class="cr-search-highlight">\0</span>', $item->comment_content );
						return $item;
					}, $comments );
				}
			}

			if( true === $this->shortcode_atts['shop_reviews'] ) {
				$number_sr = $this->shortcode_atts['number_shop_reviews'] == -1 ? null : intval( $this->shortcode_atts['number_shop_reviews'] );
				if( 0 < $number_sr || null === $number_sr ) {
					if( $this->shop_page_id > 0 ) {
						$args = array(
							'number'      => $number_sr,
							'status'      => 'approve',
							'post_status' => 'publish',
							'post_id'     => $this->shop_page_id,
							'search'	  => $this->search,
							'orderby'     => 'comment_date_gmt',
							'order'       => $this->shortcode_atts['sort'],
							'type__not_in' => 'cr_qna'
						);
						// filter by the current user if 'users' parameter was provided in the shortcode
						if ( 'current' === $this->shortcode_atts['users'] ) {
							$current_user = get_current_user_id();
							if ( 0 < $current_user ) {
								$args['user_id'] = $current_user;
							}
						}
						//
						if( !$this->shortcode_atts['show_replies'] ) {
							$args['meta_key'] = 'rating';
						}
						if( get_query_var( $this->ivrating ) ) {
							$rating = intval( get_query_var( $this->ivrating ) );
							if( $rating > 0 && $rating <= 5 ) {
								$args['meta_query'][] = array(
									'key' => 'rating',
									'value'   => $rating,
									'compare' => '=',
									'type'    => 'numeric'
								);
							}
						}
						// Query needs to be modified if min_chars constraints are set
						if ( ! empty( $this->shortcode_atts['min_chars'] ) ) {
							add_filter( 'comments_clauses', array( $this, 'min_chars_comments_clauses' ) );
						}

						// check if there are any featured shop reviews
						$args_sf = $args;
						$args_sf['meta_query'][] = array(
							'key' => 'ivole_featured',
							'compare' => '>',
							'value' => '0',
							'type' => 'NUMERIC'
						);
						$featured_s_reviews = get_comments( $args_sf );
						if( 0 < count( $featured_s_reviews ) ) {
							$featured_s_reviews = array_map( function( $fr ) {
									$fr->comment_karma = 1;
									return $fr;
								},
								$featured_s_reviews
							);
							$args['comment__not_in'] = array_map( function( $fr ) { return $fr->comment_ID; }, $featured_s_reviews );
						}

						// get shop reviews
						$comments_sr = get_comments($args);

						remove_filter( 'comments_clauses', array( $this, 'min_chars_comments_clauses' ) );

						if( 0 < count( $featured_s_reviews ) ) {
							$comments_sr = array_merge( $featured_s_reviews, $comments_sr );
						}

						//highlight search results for shop reviews
						if( !empty( $this->search ) ) {
							$highlight = $this->search;
							$comments_sr = array_map( function( $item ) use( $highlight ) {
								$item->comment_content = preg_replace( '/(' . $highlight . ')(?![^<>]*\/>)/iu', '<span class="cr-search-highlight">\0</span>', $item->comment_content );
								return $item;
							}, $comments_sr );
						}
						if( is_array( $comments ) && is_array( $comments_sr ) ) {
							$comments = array_merge( $comments, $comments_sr );
							// sorting by helpfulness rating
							if( 'helpful' === $this->shortcode_atts['sort_by'] ) {
								usort( $comments, array( $this, 'sort_by_helpful' ) );
							} else {
								// sorting by date
								usort( $comments, array( $this, 'sort_by_date' ) );
							}
						}
					}
				}
			}

			//include review replies after application of filters
			if( ( get_query_var( $this->ivrating ) || get_query_var( $this->crsearch ) ) && $this->shortcode_atts['show_replies'] ) {
				$comments = $this->include_review_replies( $comments );
			}

			return $comments;
		}

		public function display_reviews() {
			global $paged;

			if ( get_query_var( 'paged' ) ) {
				$paged = get_query_var( 'paged' );
			} elseif ( get_query_var( 'page' ) ) {
				$paged = get_query_var( 'page' );
			} else { $paged = 1; }
			$page = $paged ? $paged : 1;

			$per_page = $this->shortcode_atts['per_page'];

			if( 0  == $per_page ) {
				$per_page = 10;
			}

			if( get_query_var( $this->crsearch ) ) {
				$search_val = strval( get_query_var( $this->crsearch ) );
				if( 0 < mb_strlen( $search_val ) ) {
					$this->search = $search_val;
				}
			}

			$rating = 0;
			$all_rating_comments = 0;

			if ( 0 == $this->shortcode_atts['show_more'] ) {
				$shortcode_classes = 'cr-all-reviews-shortcode';
				if ( get_query_var( $this->ivrating ) ) {
					$rating = intval( get_query_var( $this->ivrating ) );
					if ( $rating > 0 && $rating <= 5 ) {
						$all_rating_comments = $this->count_ratings( 0 );
					} else {
						$rating = 0;
					}
				}
			} else {
				$shortcode_classes = 'cr-all-reviews-shortcode cr-all-reviews-no-pagination';
			}

			$base_url = preg_replace( '~(\?|&)crsearch=[^&]*~', '$1', get_pagenum_link() );

			$return = '<div id="cr_all_reviews_shortcode" class="' .
				$shortcode_classes .
				'" data-attributes="' . wc_esc_json( wp_json_encode( $this->shortcode_atts ) ) . '" data-baseurl="' .
				esc_attr( $base_url ) . '">';

			// add credits
			if ('yes' !== get_option('ivole_reviews_nobranding', 'yes')) {
				$return .= '<div class="cr-credits-div">';
				$return .= '<span>Powered by</span><a href="https://wordpress.org/plugins/customer-reviews-woocommerce/" target="_blank" alt="Customer Reviews for WooCommerce" title="Customer Reviews for WooCommerce"><img src="' . plugins_url( '/img/logo-vs.svg', dirname( dirname( __FILE__ ) ) ) . '"></a>';
				$return .= '</div>';
			}

			// add review form
			if ( $this->shortcode_atts['add_review'] ) {
				$return .= $this->show_add_review_form();
			}

			// show summary bar
			if ( $this->shortcode_atts['show_summary_bar'] || $this->shortcode_atts['add_review'] ) {
				$return .= $this->show_summary_table();
				$return .= CR_Ajax_Reviews::get_search_field();
			}

			$comments = $this->get_reviews();

			$top_comments_count = array_reduce( $comments, function( $carry, $item ) {
				if( property_exists( $item, 'comment_parent' ) && 0 == $item->comment_parent ) {
					$carry++;
				}
				return $carry;
			}, 0 );

			// show count of reviews
			$return .= $this->show_count_row( $top_comments_count, $page, $per_page, 0 == $this->shortcode_atts['show_more'], $rating, $all_rating_comments );

			if( 0 >= count( $comments ) ) {
				$return .= '<p class="cr-search-no-reviews">' . esc_html__('Sorry, no reviews match your current selections', 'customer-reviews-woocommerce') . '</p>';
				$return .= '</div>';
				return $return;
			}

			$hide_avatars = 'hidden' === $this->shortcode_atts['avatars'] ? true : false;

			$return .= '<ol class="commentlist">';
			if( 'initials' === $this->shortcode_atts['avatars'] ) {
				add_filter( 'get_avatar', array( 'CR_Reviews_Grid', 'cr_get_avatar' ), 10, 5 );
			}
			$return .= wp_list_comments( apply_filters('ivole_product_review_list_args', array(
				'callback' => array( 'CR_Reviews', 'callback_comments' ),
				'page'  => $page,
				'per_page' => $per_page,
				'reverse_top_level' => false,
				'echo' => false,
				'cr_show_products' => $this->shortcode_atts['show_products'],
				'cr_hide_avatars' => $hide_avatars
			)), $comments );
			if( 'initials' === $this->shortcode_atts['avatars'] ) {
				remove_filter( 'get_avatar', array( 'CR_Reviews_Grid', 'cr_get_avatar' ) );
			}
			$return .= '</ol>';

			if ( $this->shortcode_atts['show_more'] == 0 ) {
				$big = 999999999; // need an unlikely integer
				$pages = ceil( $top_comments_count / $per_page );
				$args = array(
					'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
					'format' => '?paged=%#%',
					'total' => $pages,
					'current' => $page,
					'show_all' => false,
					'end_size' => 1,
					'mid_size' => 2,
					'prev_next' => true,
					'prev_text' => __('&laquo;'),
					'next_text' => __('&raquo;'),
					'type' => 'plain'
				);

				// echo the pagination
				$return .= '<div class="cr-all-reviews-pagination">';
				$return .= paginate_links($args);
				$return .= '</div>';
			} else {
				if( $this->shortcode_atts['show_more'] < $top_comments_count ) {
					$return .= '<button id="cr-show-more-all-reviews" class="cr-show-more-button" type="button" data-page="1">';
					$return .=  sprintf( __( 'Show more reviews (%d)', 'customer-reviews-woocommerce' ), $top_comments_count - $this->shortcode_atts['show_more'] );
					$return .= '</button>';
				}
			}
			$return .= '<span class="cr-show-more-review-spinner" style="display:none;"></span>';
			$return .= '<p class="cr-search-no-reviews" style="display:none">' . esc_html__( 'Sorry, no reviews match your current selections', 'customer-reviews-woocommerce' ) . '</p>';

			$return .= '</div>';

			return $return;
		}

		public function show_more_reviews() {
			$attributes = array();
			$rating = 0;
			$all = 0;
			if( isset( $_POST['attributes'] ) && is_array( $_POST['attributes'] ) ) {
				$attributes = $_POST['attributes'];
			}
			//search
			if( !empty( trim( $_POST['search'] ) ) ) {
				$this->search = sanitize_text_field( trim( $_POST['search'] ) );
			}
			$this->fill_attributes($attributes);

			// sort
			if( isset( $_POST['sort'] ) ) {
				if( 'helpful' === $_POST['sort'] ) {
					$this->shortcode_atts['sort_by'] = 'helpful';
				} else {
					$this->shortcode_atts['sort_by'] = 'date';
				}
			}

			if( isset( $_POST['rating'] ) ) {
				$rating = intval( $_POST['rating'] );
				if( 0 < $rating && 5 >= $rating ) {
					set_query_var( $this->ivrating, $rating );
					$all = $this->count_ratings(0);
				} else {
					$rating = 0;
				}
			}

			$page = intval( $_POST['page'] ) + 1;
			$html = "";
			$comments = $this->get_reviews();

			$hide_avatars = 'hidden' === $this->shortcode_atts['avatars'] ? true : false;

			if( 'initials' === $this->shortcode_atts['avatars'] ) {
				add_filter( 'get_avatar', array( 'CR_Reviews_Grid', 'cr_get_avatar' ), 10, 5 );
			}
			$html .= wp_list_comments( apply_filters( 'ivole_product_review_list_args', array(
				'callback' => array( 'CR_Reviews', 'callback_comments' ),
				'page'  => $page,
				'per_page' => $this->shortcode_atts['show_more'],
				'reverse_top_level' => false,
				'echo' => false,
				'cr_show_products' => $this->shortcode_atts['show_products'],
				'cr_hide_avatars' => $hide_avatars
			) ), $comments );
			if( 'initials' === $this->shortcode_atts['avatars'] ) {
				remove_filter( 'get_avatar', array( 'CR_Reviews_Grid', 'cr_get_avatar' ) );
			}

			$top_comments_count = array_reduce( $comments, function( $carry, $item ) {
				if( property_exists( $item, 'comment_parent' ) && 0 == $item->comment_parent ) {
					$carry++;
				}
				return $carry;
			}, 0 );

			$count_pages = ceil( $top_comments_count / $this->shortcode_atts['show_more'] );
			$last_page = false;
			if( $count_pages <= $page ) {
				$last_page = true;
			}

			wp_send_json( array(
				'page' => $page,
				'html' => $html,
				'last_page' => $last_page,
				'show_more_label' => sprintf( __( 'Show more reviews (%d)', 'customer-reviews-woocommerce' ), $top_comments_count - $page * $this->shortcode_atts['show_more'] ),
				'count_row' => self::get_count_wording( $top_comments_count, $page, $this->shortcode_atts['show_more'], 0, $rating, $all )
			) );
		}

		private function sort_by_helpful( $a, $b ) {
			if( $a->comment_karma === $b->comment_karma ) {
				// sort by helpful if both reviews are featured (the same karma)
				$a_meta = get_comment_meta( $a->comment_ID, 'ivole_review_votes', true );
				$b_meta = get_comment_meta( $b->comment_ID, 'ivole_review_votes', true );

				$a_meta = $a_meta ? $a_meta : 0;
				$b_meta = $b_meta ? $b_meta : 0;

				if( $a_meta === $b_meta ) {
					// sort by dates if helpful votes are the same
					if( 'asc' === $this->shortcode_atts['sort'] ) {
						return strtotime( $a->comment_date_gmt ) - strtotime( $b->comment_date_gmt );
					} else {
						return strtotime( $b->comment_date_gmt ) - strtotime( $a->comment_date_gmt );
					}
				}

				if( $this->shortcode_atts['sort'] === 'asc' ) {
					return $a_meta - $b_meta;
				} else {
					return $b_meta - $a_meta;
				}
			}
			return $b->comment_karma - $a->comment_karma;
		}

		private function sort_by_date( $a, $b ) {
			if( $a->comment_karma === $b->comment_karma ) {
				if( 'asc' === $this->shortcode_atts['sort'] ) {
					return strtotime( $a->comment_date_gmt ) - strtotime( $b->comment_date_gmt );
				} else {
					return strtotime( $b->comment_date_gmt ) - strtotime( $a->comment_date_gmt );
				}
			}
			return $b->comment_karma - $a->comment_karma;
		}

		private function enqueue_wc_script( $handle, $path = '', $deps = array( 'jquery' ), $version = WC_VERSION, $in_footer = true ) {
			if ( ! wp_script_is( $handle, 'registered' ) ) {
				wp_register_script( $handle, $path, $deps, $version, $in_footer );
			}
			if( ! wp_script_is( $handle ) ) {
				wp_enqueue_script( $handle );
			}
		}

		private function enqueue_wc_style( $handle, $path = '', $deps = array(), $version = WC_VERSION, $media = 'all', $has_rtl = false ) {
			if ( ! wp_style_is( $handle, 'registered' ) ) {
				wp_register_style( $handle, $path, $deps, $version, $media );
			}
			if( ! wp_style_is( $handle ) ) {
				wp_enqueue_style( $handle );
			}
		}

		public function cr_style_1()
		{
			if( is_singular() && !is_product() ) {
				$assets_version = Ivole::CR_VERSION;
				$disable_lightbox = 'yes' === get_option( 'ivole_disable_lightbox', 'no' ) ? true : false;
				// Load gallery scripts on product pages only if supported.
				if ( 'yes' === get_option( 'ivole_attach_image', 'no' ) || 'yes' === get_option( 'ivole_form_attach_media', 'no' ) ) {
					if ( !$disable_lightbox ) {
						$this->enqueue_wc_script( 'photoswipe-ui-default' );
						$this->enqueue_wc_style( 'photoswipe-default-skin' );
						add_action( 'wp_footer', array( $this, 'woocommerce_photoswipe' ) );
					}
				}

				wp_register_style( 'ivole-frontend-css', plugins_url( '/css/frontend.css', dirname( dirname( __FILE__ ) ) ), array(), $assets_version, 'all' );
				wp_register_script( 'cr-frontend-js', plugins_url( '/js/frontend.js', dirname( dirname( __FILE__) ) ), array(), $assets_version, true );
				wp_register_script( 'cr-colcade', plugins_url( '/js/colcade.js', dirname( dirname( __FILE__) ) ), array(), $assets_version, true );
				wp_enqueue_style( 'ivole-frontend-css' );
				wp_localize_script(
					'cr-frontend-js',
					'cr_ajax_object',
					array(
						'ajax_url' => admin_url( 'admin-ajax.php' ),
						'ivole_disable_lightbox' => ( $disable_lightbox ? 1 : 0 )
					)
				);
				wp_enqueue_script( 'cr-frontend-js' );
			}
		}

		private function count_ratings( $rating ) {
			$number = $this->shortcode_atts['number'] == -1 ? null : intval( $this->shortcode_atts['number'] );
			if( 0 < $number || null === $number ) {
				$args = array(
					'number'      => $number,
					'post_type'   => 'product' ,
					'status' => 'approve',
					'parent' => 0,
					'count' => true,
					'post__in' => $this->shortcode_atts['products'],
					'type__not_in' => 'cr_qna'
				);
				// filter by the current user if 'users' parameter was provided in the shortcode
				if ( 'current' === $this->shortcode_atts['users'] ) {
					$current_user = get_current_user_id();
					if ( 0 < $current_user ) {
						$args['user_id'] = $current_user;
					}
				}
				//
				if( !$this->shortcode_atts['inactive_products'] ) {
					$args['post_status'] = 'publish';
				}
				if ($rating > 0) {
					$args['meta_query'][] = array(
						'key' => 'rating',
						'value'   => $rating,
						'compare' => '=',
						'type'    => 'numeric'
					);
				}
				// Query needs to be modified if min_chars constraints are set
				if ( ! empty( $this->shortcode_atts['min_chars'] ) ) {
					add_filter( 'comments_clauses', array( $this, 'min_chars_comments_clauses' ) );
				}
				// Query needs to be modified if category constraints are set
				if ( ! empty( $this->shortcode_atts['categories'] ) ) {
					add_filter( 'comments_clauses', array( $this, 'modify_comments_clauses' ) );
				}
				$count = get_comments($args);
				remove_filter( 'comments_clauses', array( $this, 'modify_comments_clauses' ) );
				remove_filter( 'comments_clauses', array( $this, 'min_chars_comments_clauses' ) );
			} else {
				$count = 0;
			}

			if( true === $this->shortcode_atts['shop_reviews'] ) {
				$number_sr = $this->shortcode_atts['number_shop_reviews'] == -1 ? null : $this->shortcode_atts['number_shop_reviews'];
				if( $this->shop_page_id > 0 ) {
					$args = array(
						'number'      => $number_sr,
						'status'      => 'approve',
						'post_status' => 'publish',
						'post_id'     => $this->shop_page_id,
						'meta_key'    => 'rating',
						'count'       => true,
						'type__not_in' => 'cr_qna'
					);
					// filter by the current user if 'users' parameter was provided in the shortcode
					if ( 'current' === $this->shortcode_atts['users'] ) {
						$current_user = get_current_user_id();
						if ( 0 < $current_user ) {
							$args['user_id'] = $current_user;
						}
					}
					//
					if ($rating > 0) {
						$args['meta_query'][] = array(
							'key' => 'rating',
							'value'   => $rating,
							'compare' => '=',
							'type'    => 'numeric'
						);
					}
					// Query needs to be modified if min_chars constraints are set
					if ( ! empty( $this->shortcode_atts['min_chars'] ) ) {
						add_filter( 'comments_clauses', array( $this, 'min_chars_comments_clauses' ) );
					}
					$count_sr = get_comments($args);
					remove_filter( 'comments_clauses', array( $this, 'min_chars_comments_clauses' ) );

					$count = $count + $count_sr;
				}
			}

			return $count;
		}

		public function show_summary_table() {
			$all = $this->count_ratings(0);
			if ($all > 0) {
				$five = (float)$this->count_ratings(5);
				$five_percent = floor($five / $all * 100);
				$five_rounding = $five / $all * 100 - $five_percent;
				$four = (float)$this->count_ratings(4);
				$four_percent = floor($four / $all * 100);
				$four_rounding = $four / $all * 100 - $four_percent;
				$three = (float)$this->count_ratings(3);
				$three_percent = floor($three / $all * 100);
				$three_rounding = $three / $all * 100 - $three_percent;
				$two = (float)$this->count_ratings(2);
				$two_percent = floor($two / $all * 100);
				$two_rounding = $two / $all * 100 - $two_percent;
				$one = (float)$this->count_ratings(1);
				$one_percent = floor($one / $all * 100);
				$one_rounding = $one / $all * 100 - $one_percent;
				$hundred = $five_percent + $four_percent + $three_percent + $two_percent + $one_percent;
				if( $hundred < 100 ) {
					$to_distribute = 100 - $hundred;
					$roundings = array( '5' => $five_rounding, '4' => $four_rounding, '3' => $three_rounding, '2' => $two_rounding, '1' => $one_rounding );
					arsort($roundings);
					$roundings = array_filter( $roundings, function( $value ) {
						return $value > 0;
					} );
					while( $to_distribute > 0 && count( $roundings ) > 0 ) {
						foreach( $roundings as $key => $value ) {
							if( $to_distribute > 0 ) {
								switch( $key ) {
									case 5:
									$five_percent++;
									break;
									case 4:
									$four_percent++;
									break;
									case 3:
									$three_percent++;
									break;
									case 2:
									$two_percent++;
									break;
									case 1:
									$one_percent++;
									break;
									default:
									break;
								}
								$to_distribute--;
							} else {
								break;
							}
						}
					}
				}
				$average = ( 5 * $five + 4 * $four + 3 * $three + 2 * $two + 1 * $one ) / $all;
				$summary_box_classes = 'cr-summaryBox-wrap';
				if ( $this->shortcode_atts['add_review'] ) {
					$summary_box_classes .= ' cr-summaryBox-add-review';
				}
				$output = '';
				$output .= '<div class="' . $summary_box_classes . '">';
				if ( $this->shortcode_atts['add_review'] ) {
					$output .= '<div class="cr-summary-separator-side"></div>';
				}
				$output .= '<div class="cr-overall-rating-wrap">';
				$output .= '<div class="cr-average-rating"><span>' . number_format_i18n( $average, 1 ) . '</span></div>';
				$output .= '<div class="cr-average-rating-stars"><div class="crstar-rating"><span style="width:'.($average / 5 * 100).'%;"></span></div></div>';
				$output .= '<div class="cr-total-rating-count">' . sprintf( _n( 'Based on %s review', 'Based on %s reviews', $all, 'customer-reviews-woocommerce' ), number_format_i18n( $all ) ) . '</div>';
				$output .= '</div>';
				$output .= '<div class="cr-summary-separator"><div class="cr-summary-separator-int"></div></div>';
				if( 0 < $this->shortcode_atts['show_more'] ) {
					$output .= '<div class="ivole-summaryBox cr-all-reviews-ajax">';
				} else {
					$output .= '<div class="ivole-summaryBox">';
				}
				$output .= '<table id="ivole-histogramTable">';
				$output .= '<tbody>';
				$output .= '<tr class="ivole-histogramRow">';
				// five
				if( $five > 0 ) {
					$output .= '<td class="ivole-histogramCell1"><a class="cr-histogram-a" data-rating="5" href="' . esc_url( add_query_arg( $this->ivrating, 5, get_permalink() ) ) . '" title="' . __( '5 star', 'customer-reviews-woocommerce' ) . '">' . __( '5 star', 'customer-reviews-woocommerce' ) . '</a></td>';
					$output .= '<td class="ivole-histogramCell2"><a class="cr-histogram-a" data-rating="5" href="' . esc_url( add_query_arg( $this->ivrating, 5, get_permalink() ) ) . '"><div class="ivole-meter"><div class="ivole-meter-bar" style="width: ' . $five_percent . '%">' . $five_percent . '</div></div></a></td>';
					$output .= '<td class="ivole-histogramCell3"><a class="cr-histogram-a" data-rating="5" href="' . esc_url( add_query_arg( $this->ivrating, 5, get_permalink() ) ) . '">' . (string)$five_percent . '%</a></td>';
				} else {
					$output .= '<td class="ivole-histogramCell1">' . __('5 star', 'customer-reviews-woocommerce') . '</td>';
					$output .= '<td class="ivole-histogramCell2"><div class="ivole-meter"><div class="ivole-meter-bar" style="width: ' . $five_percent . '%"></div></div></td>';
					$output .= '<td class="ivole-histogramCell3">' . (string)$five_percent . '%</td>';
				}

				$output .= '</tr>';
				$output .= '<tr class="ivole-histogramRow">';
				// four
				if( $four > 0 ) {
					$output .= '<td class="ivole-histogramCell1"><a class="cr-histogram-a" data-rating="4" href="' . esc_url( add_query_arg( $this->ivrating, 4, get_permalink() ) ) . '" title="' . __( '4 star', 'customer-reviews-woocommerce' ) . '">' . __( '4 star', 'customer-reviews-woocommerce' ) . '</a></td>';
					$output .= '<td class="ivole-histogramCell2"><a class="cr-histogram-a" data-rating="4" href="' . esc_url( add_query_arg( $this->ivrating, 4, get_permalink() ) ) . '"><div class="ivole-meter"><div class="ivole-meter-bar" style="width: ' . $four_percent . '%">' . $four_percent . '</div></div></a></td>';
					$output .= '<td class="ivole-histogramCell3"><a class="cr-histogram-a" data-rating="4" href="' . esc_url( add_query_arg( $this->ivrating, 4, get_permalink() ) ) . '">' . (string)$four_percent . '%</a></td>';
				} else {
					$output .= '<td class="ivole-histogramCell1">' . __('4 star', 'customer-reviews-woocommerce') . '</td>';
					$output .= '<td class="ivole-histogramCell2"><div class="ivole-meter"><div class="ivole-meter-bar" style="width: ' . $four_percent . '%"></div></div></td>';
					$output .= '<td class="ivole-histogramCell3">' . (string)$four_percent . '%</td>';
				}

				$output .= '</tr>';
				$output .= '<tr class="ivole-histogramRow">';
				// three
				if( $three > 0 ) {
					$output .= '<td class="ivole-histogramCell1"><a class="cr-histogram-a" data-rating="3" href="' . esc_url( add_query_arg( $this->ivrating, 3, get_permalink() ) ) . '" title="' . __( '3 star', 'customer-reviews-woocommerce' ) . '">' . __( '3 star', 'customer-reviews-woocommerce' ) . '</a></td>';
					$output .= '<td class="ivole-histogramCell2"><a class="cr-histogram-a" data-rating="3" href="' . esc_url( add_query_arg( $this->ivrating, 3, get_permalink() ) ) . '"><div class="ivole-meter"><div class="ivole-meter-bar" style="width: ' . $three_percent . '%">' . $three_percent . '</div></div></a></td>';
					$output .= '<td class="ivole-histogramCell3"><a class="cr-histogram-a" data-rating="3" href="' . esc_url( add_query_arg( $this->ivrating, 3, get_permalink() ) ) . '">' . (string)$three_percent . '%</a></td>';
				} else {
					$output .= '<td class="ivole-histogramCell1">' . __('3 star', 'customer-reviews-woocommerce') . '</td>';
					$output .= '<td class="ivole-histogramCell2"><div class="ivole-meter"><div class="ivole-meter-bar" style="width: ' . $three_percent . '%"></div></div></td>';
					$output .= '<td class="ivole-histogramCell3">' . (string)$three_percent . '%</td>';
				}

				$output .= '</tr>';
				$output .= '<tr class="ivole-histogramRow">';
				// two
				if( $two > 0 ) {
					$output .= '<td class="ivole-histogramCell1"><a class="cr-histogram-a" data-rating="2" href="' . esc_url( add_query_arg( $this->ivrating, 2, get_permalink() ) ) . '" title="' . __( '2 star', 'customer-reviews-woocommerce' ) . '">' . __( '2 star', 'customer-reviews-woocommerce' ) . '</a></td>';
					$output .= '<td class="ivole-histogramCell2"><a class="cr-histogram-a" data-rating="2" href="' . esc_url( add_query_arg( $this->ivrating, 2, get_permalink() ) ) . '"><div class="ivole-meter"><div class="ivole-meter-bar" style="width: ' . $two_percent . '%">' . $two_percent .'</div></div></a></td>';
					$output .= '<td class="ivole-histogramCell3"><a class="cr-histogram-a" data-rating="2" href="' . esc_url( add_query_arg( $this->ivrating, 2, get_permalink() ) ) . '">' . (string)$two_percent . '%</a></td>';
				} else {
					$output .= '<td class="ivole-histogramCell1">' . __('2 star', 'customer-reviews-woocommerce') . '</td>';
					$output .= '<td class="ivole-histogramCell2"><div class="ivole-meter"><div class="ivole-meter-bar" style="width: ' . $two_percent . '%"></div></div></td>';
					$output .= '<td class="ivole-histogramCell3">' . (string)$two_percent . '%</td>';
				}

				$output .= '</tr>';
				$output .= '<tr class="ivole-histogramRow">';
				// one
				if( $one > 0 ) {
					$output .= '<td class="ivole-histogramCell1"><a class="cr-histogram-a" data-rating="1" href="' . esc_url( add_query_arg( $this->ivrating, 1, get_permalink() ) ) . '" title="' . __( '1 star', 'customer-reviews-woocommerce' ) . '">' . __( '1 star', 'customer-reviews-woocommerce' ) . '</a></td>';
					$output .= '<td class="ivole-histogramCell2"><a class="cr-histogram-a" data-rating="1" href="' . esc_url( add_query_arg( $this->ivrating, 1, get_permalink() ) ) . '"><div class="ivole-meter"><div class="ivole-meter-bar" style="width: ' . $one_percent . '%">' . $one_percent . '</div></div></a></td>';
					$output .= '<td class="ivole-histogramCell3"><a class="cr-histogram-a" data-rating="1" href="' . esc_url( add_query_arg( $this->ivrating, 1, get_permalink() ) ) . '">' . (string)$one_percent . '%</a></td>';
				} else {
					$output .= '<td class="ivole-histogramCell1">' . __('1 star', 'customer-reviews-woocommerce') . '</td>';
					$output .= '<td class="ivole-histogramCell2"><div class="ivole-meter"><div class="ivole-meter-bar" style="width: ' . $one_percent . '%"></div></div></td>';
					$output .= '<td class="ivole-histogramCell3">' . (string)$one_percent . '%</td>';
				}

				$output .= '</tr>';
				$output .= '</tbody>';
				$output .= '</table>';
				$output .= '</div>';
				if ( $this->shortcode_atts['add_review'] ) {
					$output .= '<div class="cr-summary-separator"><div class="cr-summary-separator-int"></div></div>';
					$output .= '<div class="cr-add-review-wrap">';
					$output .= '<button class="cr-all-reviews-add-review" type="button">Add a review</button>';
					$output .= '</div>';
					$output .= '<div class="cr-summary-separator-side"></div>';
				}
				$output .= '</div>';
				return $output;
			}
		}

		/**
		* Modify the comments query to constrain results to the provided categories
		*/
		public function modify_comments_clauses( $clauses ) {
			global $wpdb;

			$terms = get_terms( array(
				'taxonomy' => 'product_cat',
				'include'  => $this->shortcode_atts['categories'],
				'fields'   => 'tt_ids'
			) );

			if ( is_array( $terms ) && count( $terms ) > 0 ) {
				$clauses['join'] .= " LEFT JOIN {$wpdb->term_relationships} ON {$wpdb->comments}.comment_post_ID = {$wpdb->term_relationships}.object_id";
				$clauses['where'] .= " AND {$wpdb->term_relationships}.term_taxonomy_id IN(" . implode( ',', $terms ) . ")";
			}

			return $clauses;
		}

		public function min_chars_comments_clauses( $clauses ) {
			global $wpdb;

			$clauses['where'] .= " AND CHAR_LENGTH({$wpdb->comments}.comment_content) >= " . $this->shortcode_atts['min_chars'];

			return $clauses;
		}

		private function include_review_replies( $comments ) {
			$comments_w_replies = array();
			foreach ( $comments as $comment ) {
				$comments_w_replies[]  = $comment;
				$args = array(
					'parent' => $comment->comment_ID,
					'format' => 'flat',
					'status' => 'approve',
					'orderby' => 'comment_date'
				);
				$comment_children = get_comments( $args );
				foreach ( $comment_children as $comment_child ) {
					$reply_already_exist = false;
					foreach( $comments as $comment_flat ) {
						if( $comment_flat->comment_ID === $comment_child->comment_ID ) {
							$reply_already_exist = true;
						}
					}
					if( !$reply_already_exist ) {
						$comments_w_replies[] = $comment_child;
					}
				}
			}
			return $comments_w_replies;
		}

		public function woocommerce_photoswipe() {
			wc_get_template( 'single-product/photoswipe.php' );
		}

		public function show_count_row( $count, $page, $per_page, $pagination, $rating, $all ) {
			$count_wording = self::get_count_wording( $count, $page, $per_page, $pagination, $rating, $all );
			$sort_helpful = 'helpful' === $this->shortcode_atts['sort_by'] ? true : false;

			$output = '<div class="cr-count-row">';
			$output .=  '<div class="cr-count-row-count">' . $count_wording . '</div>';
			if ( 0 < $this->shortcode_atts['show_more'] ) {
				$output .=  '<div class="cr-ajax-reviews-sort-div">';
				$output .=   '<select class="cr-ajax-reviews-sort" data-nonce="' . wp_create_nonce( 'cr_product_reviews_sort' ) . '">';
				$output .=    '<option value="recent"' . ( $sort_helpful ? '' : ' selected="selected"' ) . '>';
				$output .=     esc_html__( 'Most Recent', 'customer-reviews-woocommerce' );
				$output .=    '</option>';
				$output .=    '<option value="helpful"' . ( $sort_helpful ? ' selected="selected"' : '' ) . '>';
				$output .=     esc_html__( 'Most Helpful', 'customer-reviews-woocommerce' );
				$output .=    '</option>';
				$output .=   '</select>';
				$output .=  '</div>';
			}
			$output .= '</div>';
			return $output;
		}

		public static function get_count_wording( $count, $page, $per_page, $pagination, $rating, $all ) {
			// optional strings that need to be displayed when reviews are filtered by rating
			$rating_string = '';
			$all_reviews_string = '';
			if( $rating ) {
				$rating_string = sprintf(
					_n( '%d star', '%d stars', $rating, 'customer-reviews-woocommerce' ),
					$rating
				);
			}
			if( $all ) {
				$all_reviews_string = sprintf(
					_n( 'See all %d review', 'See all %d reviews', $all, 'customer-reviews-woocommerce' ),
					$all
				);
				$all_reviews_string =
					'<a class="cr-seeAllReviews" data-rating="0" href="' . esc_url( get_permalink() ) . '">' .
					esc_html( $all_reviews_string ) .
					'</a>';
			}
			//
			if( 0 < $count ) {
				if( $pagination ) {
					$from = ( $page - 1 ) * $per_page + 1;
				} else {
					$from = 1;
				}
				$to = $page * $per_page < $count ? $page * $per_page : $count;
				if( $rating_string ) {
					return sprintf(
						_n( '%d-%d of %d review (%s). %s', '%d-%d of %d reviews (%s). %s', $count, 'customer-reviews-woocommerce' ),
						$from,
						$to,
						$count,
						$rating_string,
						$all_reviews_string
					);
				} else {
					return sprintf(
						_n( '%d-%d of %d review', '%d-%d of %d reviews', $count, 'customer-reviews-woocommerce' ),
						$from,
						$to,
						$count
					);
				}
			} else {
				if( $rating_string ) {
					return sprintf (
						__( '0 of 0 reviews (%s). %s', 'customer-reviews-woocommerce' ),
						$rating_string,
						$all_reviews_string
					);
				} else {
					return __( '0 of 0 reviews', 'customer-reviews-woocommerce' );
				}
			}
		}

		private function show_add_review_form() {
			ob_start();
			wc_get_template(
				'cr-review-form.php',
				array(
					'cr_item_name' => Ivole_Email::get_blogname(),
					'cr_item_pic' => get_site_icon_url( 512, plugins_url( '/img/store.svg', dirname( dirname( __FILE__ ) ) ) )
				),
				'customer-reviews-woocommerce',
				dirname( dirname( dirname( __FILE__ ) ) ) . '/templates/'
			);
			return ob_get_clean();
		}

		public function submit_review() {
			$return = array(
				'code' => 2,
				'description' => __( 'Data validation error', 'customer-reviews-woocommerce' )
			);
			if (
				isset( $_POST['rating'] ) &&
				isset( $_POST['review'] ) &&
				isset( $_POST['name'] ) &&
				isset( $_POST['email'] )
			) {
				$shop_page_id = wc_get_page_id( 'shop' );
				if( 0 < $shop_page_id ) {
					// WPML compatibility
					if ( has_filter( 'wpml_object_id' ) ) {
						$shop_page_id = apply_filters( 'wpml_object_id', $shop_page_id, 'page', true );
					}
					//
					$rating = intval( $_POST['rating'] );
					$review = sanitize_textarea_field( trim( $_POST['review'] ) );
					$name = sanitize_text_field( trim( $_POST['name'] ) );
					$email = sanitize_email( trim( $_POST['email'] ) );
					//
					if (
						$rating &&
						$review &&
						$name &&
						is_email( $email )
					) {
						$user = get_user_by( 'email', $email );
						if( $user ) {
							$user = $user->ID;
						} else {
							$user = 0;
						}
						$commentdata = array(
							'comment_author' => $name,
							'comment_author_email' => $email,
							'comment_author_url' => '',
							'comment_content' => $review,
							'comment_type' => 'review',
							'comment_post_ID' => $shop_page_id,
							'user_id' => $user,
							'comment_meta' => array(
								'rating' => intval( $rating )
							)
						);
						add_filter( 'pre_comment_approved', array( 'CR_All_Reviews', 'is_review_approved' ), 10, 2 );
						$result = wp_new_comment( $commentdata, true );
						remove_filter( 'pre_comment_approved', array( 'CR_All_Reviews', 'is_review_approved' ), 10 );

						$error_description = __( 'Your review could not be added', 'customer-reviews-woocommerce' );
						$error_button = __( 'Try again', 'customer-reviews-woocommerce' );
						$success_description = __( 'Your review has been successfully added', 'customer-reviews-woocommerce' );
						$success_button = __( 'Continue', 'customer-reviews-woocommerce' );

						if (
							!$result ||
							is_wp_error( $result )
						) {
							if( is_wp_error( $result ) ) {
								$error_description = $result->get_error_message();
							}
							$return = array(
								'code' => 1,
								'description' => $error_description,
								'button' => $error_button
							);
						} else {
							$return = array(
								'code' => 0,
								'description' => $success_description,
								'button' => $success_button
							);
						}
					}
				}
			}
			wp_send_json( $return );
		}

		public static function is_review_approved( $approved, $commentdata ) {
			if ( current_user_can( 'manage_woocommerce' ) || current_user_can( 'manage_options' ) ) {
				$approved = 1;
			} else {
				$approved = 0;
			}
			return $approved;
		}

	}

endif;