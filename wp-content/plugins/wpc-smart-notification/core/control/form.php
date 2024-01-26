<?php

namespace WPCSN\formControl;

defined( 'ABSPATH' ) || exit;

class FromBuilding {
	public $path;
	public $instance;
	public $class;
	public $field_id;
	public $field_name;
	public $ajax;
	public $settings;
	public $number;

	public static function initialization( $args ) {
		static $single;

		if ( ! isset( $single ) ) {
			$single = new self();
		}

		$single->set( $args );

		return $single->building();
	}

	function set( $args = [] ) {
		$args = array_merge( [
			'name_prefix' => '',
			'class'       => '',
			'field_id'    => '',
			'field_name'  => '',
			'ajax'        => '',
			'settings'    => [],
			'instance'    => [],
			'number'      => '',
			'isWidget'    => false,
		], is_array( $args ) ? $args : [] );

		$this->path        = dirname( __FILE__ );
		$this->name_prefix = $args['name_prefix'];
		$this->class       = $args['class'];
		$this->field_id    = $this->explode_field_id( $args['field_id'] );
		$this->field_name  = $this->explode_field_name( $args['field_name'] );
		$this->ajax        = $args['ajax'];
		$this->settings    = $args['settings'];
		$this->number      = $args['number'];
		$this->isWidget    = ! empty( $args['isWidget'] ) ? true : false;

		if ( ! $this->isWidget ) {
			array_unshift( $this->field_id, $this->name_prefix );
			array_unshift( $this->field_name, $this->name_prefix );
		}

		$this->set_instance( $this->field_name, $args['instance'] );
	}

	private function explode_field_name( $str = '' ) {
		$name = array_map( function ( $item ) {
			return rtrim( $item, ']' );
		}, explode( '[', $str ) );

		if ( empty( $name[0] ) ) {
			unset( $name[0] );
		}

		if ( end( $name ) == '' ) {
			array_pop( $name );
		}

		return $name;
	}

	private function explode_field_id( $str = '' ) {
		$name = explode( '-', $str );

		if ( empty( $name[0] ) ) {
			unset( $name[0] );
		}

		if ( end( $name ) == '' ) {
			array_pop( $name );
		}

		return $name;
	}

	private function implode_field_id( $array = [] ) {
		if ( empty( $array ) || ! is_array( $array ) ) {
			return;
		}

		return implode( '-', $array );
	}

	private function implode_field_name( $array = [] ) {
		if ( empty( $array ) || ! is_array( $array ) ) {
			return;
		}

		$prefix = array_shift( $array );

		return count( $array ) > 0 ? sprintf( '%s[%s]', $prefix, implode( '][', $array ) ) : $prefix;
	}

	private function unique_id( $prefix = '' ) {
		static $id_counter = 0;

		if ( function_exists( 'wp_unique_id' ) ) {
			return wp_unique_id( $prefix );
		}

		return $prefix . (string) ++ $id_counter;
	}

	private function get_instance( $key = [], $instance = [] ) {
		if ( empty( $instance ) ) {
			$instance = $this->instance;
		}

		foreach ( $key as $k ) {
			if ( ! isset( $instance[ $k ] ) ) {
				return false;
			}

			$instance = $instance[ $k ];
		}

		return $instance;
	}

	private function set_instance( $key = [], $value = false, &$instance = false ) {
		if ( $instance === false ) {
			$instance = &$this->instance;
		}

		$k = array_shift( $key );

		if ( ! empty( $key ) ) {
			if ( ! isset( $instance[ $k ] ) ) {
				$instance[ $k ] = [];
			}

			$this->set_instance( $key, $value, $instance[ $k ] );
		} else {
			$instance[ $k ] = $value;
		}
	}

	public function building() {
		$unique_id = $this->isWidget ? $this->implode_field_id( $this->field_id ) : $this->unique_id( $this->name_prefix . '-' );

		echo '<div id="', $unique_id, '" class="', $this->name_prefix, '-form-content', ! $this->isWidget ? ' attach' : '', '">
		<input type="hidden" name="', $this->implode_field_name( $this->field_name ), '[_nonce]" value="', wp_create_nonce( 'save_' . $this->name_prefix ), '">';

		if ( ! empty( $this->settings['type'] ) && $this->settings['type'] == 'sub_panel' ) {
			$this->settings = [ 'builder_data' => $this->settings ];
		}

		unset( $this->settings['type'] );
		unset( $this->settings['_nonce'] );

		$this->form( [
			'settings'   => $this->settings,
			'field_id'   => $this->field_id,
			'field_name' => $this->field_name
		] );

		if ( $this->isWidget && $this->number != '__i__' ) {
			echo '<script type="text/javascript">document.dispatchEvent( new CustomEvent("widget-', $this->name_prefix, '-loaded", "', $unique_id, '") )</script>';
		}

		echo '</div>';
	}

	private function form( $args = [] ) {
		$args = (object) $args;

		if ( empty( $args->settings ) || ! is_array( $args->settings ) ) {
			return;
		}

		if ( ! empty( $args->settings['type'] ) ) {
			$setting = $args->settings;
			unset( $args->settings );

			$field_id    = $this->implode_field_id( $args->field_id );
			$field_name  = $this->implode_field_name( $args->field_name );
			$tip         = isset( $setting['tip'] ) ? sprintf( 'title="%s"', esc_html( $setting['tip'] ) ) : '';
			$placeholder = isset( $setting['placeholder'] ) ? esc_html( $setting['placeholder'] ) : '';
			$value       = $args->instance;

			echo '<div class="item-inner">';

			if ( $setting['type'] == 'multipleField' ) {
				$setting['data'] = [ $setting ];

				foreach ( $setting['data'] as $s ) {
					if ( isset( $s['data'] ) && is_array( $s['data'] ) ) {
						$value = isset( $args->instance ) && is_array( $args->instance ) ? $args->instance : [ [] ];
						$this->form( [
							'settings'   => $s['data'],
							'instance'   => $value,
							'field_id'   => $args->field_id,
							'field_name' => $args->field_name,
						] );
					}
				}

			} elseif ( in_array( $setting['type'], [ 'group', 'section', 'tabItem', 'menuItem' ] ) ) {
				if ( isset( $setting['data'] ) && is_array( $setting['data'] ) ) {
					$value = isset( $args->instance ) && is_array( $args->instance ) ? $args->instance : [];

					foreach ( $setting['data'] as $k => $s ) {
						echo '<div class="item-', $setting['type'], '">';
						$this->form( [
							'settings'   => [ $k => $s ],
							'instance'   => $value,
							'field_id'   => $args->field_id,
							'field_name' => $args->field_name,
						] );
						echo '</div>';
					}
				}
			}

			$file_field = $this->path . '/fields/' . $setting['type'] . '.php';

			if ( file_exists( $file_field ) ) {
				include $file_field;
			} elseif ( ! in_array( $setting['type'], [ 'multipleField', 'group', 'section' ] ) ) {
				include $this->path . '/fields/default.php';
			}

			$file_view = $this->path . '/views/' . $setting['type'] . '.php';

			if ( file_exists( $file_view ) ) {
				include $file_view;
			} elseif ( ! in_array( $setting['type'], [ 'multipleField', 'group', 'section' ] ) ) {
				include $this->path . '/views/default.php';
			}

			if ( ! empty( $setting['desc'] ) ) {
				echo '<small><em>', esc_html( $setting['desc'] ), '</em></small>';
			}

			echo ! empty( $args->isLoop ) ? '<a href="#remove" class="btn btn-outline-danger remove">' . esc_html__( 'Remove', 'wpc-smart-notification' ) . '</a>' : '';
			echo '</div>';
		} else {
			$settings = $args->settings;

			foreach ( $args->settings as $key => $setting ) {
				if ( empty( $setting['type'] ) ) {
					continue;
				}

				$_args             = (array) $args;
				$_args['settings'] = $setting;
				$_args['isField']  = ! empty( $setting['type'] ) && ! in_array( $setting['type'], [
					'multipleField',
					'group',
					'section',
					'menuItem',
					'tabItem'
				] ) ? true : false;

				if ( preg_match( '/\>/', $key, $m ) ) {
					$field = explode( '>', preg_replace( '/\s+/i', '', $key ) );
				} else {
					$field = [ $key ];
				}

				if ( ! in_array( $setting['type'], [ 'group', 'section' ] ) ) {
					$_args['field_name'] = array_merge( $_args['field_name'], $field );
					$_args['field_id']   = array_merge( $_args['field_id'], $field );
				}

				$_args['instance'] = $this->get_instance( $_args['field_name'] );

				$hasAddMore = ! empty( $setting['multiple'] ) && ! in_array( $setting['type'], [
					'section',
					'group',
					'select',
					'radio',
					'checkbox'
				] ) ? true : false;

				$tag = $setting['type'] == 'section' ? 'details' : 'div';

				if ( ! empty( $setting['toggle']['field'] ) && ! empty( $setting['toggle']['value'] ) ) {
					$toggle = sprintf( ' data-toggle="%s" data-toggle-value="%s"',
						$setting['toggle']['field'], is_array( $setting['toggle']['value'] ) ? implode( ',', $setting['toggle']['value'] ) : $setting['toggle']['value']
					);
				} else {
					$toggle = '';
				}

				printf( '<%s class="widget-%s-%s%s item-wrapper field-%s" %s %s %s>',
					$tag, $this->name_prefix, $setting['type'],
					! empty( $setting['view'] ) ? ' ' . esc_html( $setting['view'] ) : '',
					implode( '-', $field ),
					$hasAddMore ? ' data-field-name=' . json_encode( $_args['field_name'] ) : '',
					! empty( $setting['style'] ) ? ' style="' . esc_html( $setting['style'] ) . '"' : '',
					$toggle
				);

				if ( empty( $args->inloop ) ) {
					printf( '<%1$s class="field-title">%2$s</%1$s>', $tag == 'div' ? 'span' : 'summary',
						esc_html( isset( $setting['label'] ) ? $setting['label'] : '' )
					);
				}

				echo ( ! empty( $setting['description'] ) && ( $setting['type'] === 'group' ) ) ? sprintf( '<small>%s</small>', esc_html( $setting['description'] ) ) : '';

				if ( $hasAddMore ) {
					$field_id   = $_args['field_id'];
					$field_name = $_args['field_name'];

					if ( in_array( $setting['type'], [ 'multipleField', 'group', 'section' ] ) ) {
						$instance = isset( $_args['instance'][0] ) && is_array( $_args['instance'][0] ) ? $_args['instance'] : [ [] ];
					} else {
						$instance = isset( $_args['instance'][0] ) && ! is_array( $_args['instance'][0] ) ? $_args['instance'] : [ '' ];
					}

					$k = 0;

					foreach ( $instance as $_instance ) {
						$_args['field_id']     = $field_id;
						$_args['field_id'][]   = $k;
						$_args['field_name']   = $field_name;
						$_args['field_name'][] = $k;
						$_args['instance']     = $_instance;
						$_args['isLoop']       = true;
						$k ++;
						$this->form( $_args );
					}
				} else {
					$this->form( $_args );
				}

				echo ( ! empty( $setting['description'] ) && ( $setting['type'] !== 'group' ) ) ? sprintf( '<small>%s</small>', esc_html( $setting['description'] ) ) : '';
				echo $hasAddMore ? '<a href="#add-more" class="btn btn-outline-success add-more">' . esc_html__( 'Add more', 'wpc-smart-notification' ) . '</a>' : '';
				printf( '</%s>', $tag );
			}
		}
	}
}
