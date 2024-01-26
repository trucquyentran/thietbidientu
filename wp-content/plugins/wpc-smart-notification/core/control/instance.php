<?php

namespace WPCSN\formControl;

defined( 'ABSPATH' ) || exit;

class Instance {
	public $new_instance;
	public $old_instance;
	public $instance;
	public $field_id;
	public $field_name;
	public $class;
	public $ajax;
	public $settings;

	public static function initialization( $args ) {
		static $single;

		if ( ! isset( $single ) ) {
			$single = new self();
		}

		$single->set( $args );

		return $single->sanitize();
	}

	function set( $args = [] ) {
		$args = array_merge( [
			'name_prefix'  => '',
			'class'        => '',
			'field_id'     => '',
			'field_name'   => '',
			'ajax'         => '',
			'settings'     => [],
			'instance'     => [],
			'old_instance' => [],
			'new_instance' => [],
			'isWidget'     => false,
		], is_array( $args ) ? $args : [] );

		$this->name_prefix  = $args['name_prefix'];
		$this->class        = $args['class'];
		$this->field_id     = $this->explode_field_id( $args['field_id'] );
		$this->field_name   = $this->explode_field_name( $args['field_name'] );
		$this->ajax         = $args['ajax'];
		$this->settings     = $args['settings'];
		$this->instance     = $args['instance'];
		$this->old_instance = $args['old_instance'];
		$this->new_instance = $args['new_instance'];
		$this->isWidget     = $args['isWidget'];
	}

	public function sanitize() {
		if ( ! $this->isWidget ) {
			$this->new_instance = ! empty( $this->new_instance[ $this->name_prefix ] ) ? $this->new_instance[ $this->name_prefix ] : [];
		}

		if ( empty( $this->new_instance['_nonce'] ) || ! wp_verify_nonce( $this->new_instance['_nonce'], 'save_' . $this->name_prefix ) ) {
			return false;
		}

		if ( ! empty( $this->settings['type'] ) && $this->settings['type'] == 'sub_panel' ) {
			$this->settings = [ 'builder_data' => $this->settings ];
		}

		$this->parseInstance( [
			'settings'   => $this->settings,
			'field_name' => $this->field_name
		] );

		return $this->instance;
	}

	private function parseInstance( $args = [] ) {
		$args = (object) $args;

		if ( empty( $args->settings ) || ! is_array( $args->settings ) ) {
			return;
		}

		if ( ! empty( $args->settings['type'] ) ) {
			$setting    = $args->settings;
			$field_name = $args->field_name;
			$value      = $args->instance;

			if ( $setting['type'] == 'text' ) {
				$value = sanitize_text_field( $value );

				if ( $value === false ) {
					$value = isset( $setting['std'] ) ? $setting['std'] : '';
				}

				$this->set_instance( $field_name, $value );
			} elseif ( $setting['type'] == 'url' ) {
				$value = esc_url( $value );

				if ( $value === false ) {
					$value = isset( $setting['std'] ) ? $setting['std'] : '';
				}

				$this->set_instance( $field_name, $value );
			} elseif ( $setting['type'] == 'email' ) {
				$value = sanitize_email( $value );

				if ( $value === false ) {
					$value = isset( $setting['std'] ) ? $setting['std'] : '';
				}

				$this->set_instance( $field_name, $value );
			} elseif ( $setting['type'] == 'size' ) {
				$value = $this->esc_measurement( $value );

				if ( $value === false ) {
					$value = isset( $setting['std'] ) ? $setting['std'] : '';
				}

				$this->set_instance( $field_name, $value );
			} elseif ( $setting['type'] == 'color' ) {
				if ( $value === false ) {
					$value = isset( $setting['std'] ) ? $setting['std'] : '';
				}

				if ( ! preg_match( '/^(#[a-f0-9]{3}|#[a-f0-9]{6}|#[a-f0-9]{8}|rgb\(\s*\d{1,3}\s*,\s*\d{1,3}\s*,\s*\d{1,3}\s*\)|rgba\(\s*\d{1,3}\s*,\s*\d{1,3}\s*,\s*\d{1,3}\s*,\s*\d{1}\.*\d{0,2}\s*\)|hsl\(\s*\d{1,3}\s*\,\s*\d{1,3}\%\s*,\s*\d{1,3}\%\s*\)|hsla\(\s*\d{1,3}\s*\,\s*\d{1,3}\%\s*,\s*\d{0,3}\%\s*,\s*\d{1}\.*\d{0,2}\s*\))$/i', $value ) ) {
					$value = '';
				}

				$this->set_instance( $field_name, $value );
			} elseif ( $setting['type'] == 'image' ) {
				$value = absint( $value );

				if ( $value === false ) {
					$value = isset( $setting['std'] ) ? $setting['std'] : '';
				}

				$this->set_instance( $field_name, $value );
			} elseif ( $setting['type'] == 'number' ) {
				if ( $value === false ) {
					$value = isset( $setting['std'] ) ? $setting['std'] : '';
				}

				$depth = 0;

				if ( ! empty( $setting['step'] ) && $setting['step'] < 1 && $setting['step'] > 0 ) {
					$step = $setting['step'];

					while ( $step < 1 ) {
						$depth ++;
						$step *= 10;
					}
				}

				$value = round( $value, $depth );

				if ( isset( $setting['min'] ) && is_numeric( $setting['min'] ) ) {
					$value = max( $value, $setting['min'] );
				}

				if ( isset( $setting['max'] ) && is_numeric( $setting['max'] ) ) {
					$value = min( $value, $setting['max'] );
				}

				$this->set_instance( $field_name, $value );
			} elseif ( in_array( $setting['type'], [ 'select', 'checkbox', 'radio', 'sortable' ] ) ) {
				$options = isset( $setting['options'] ) ? array_keys( $setting['options'] ) : [];
				$std     = isset( $setting['std'] ) ? $setting['std'] : '';

				if ( ( $setting['type'] == 'select' && empty( $setting['multiple'] ) ) || in_array( $setting['type'], [ 'radio' ] ) ) {
					if ( ! empty( $setting['source'] ) || ! empty( $setting['tags'] ) ) {
						$value = $value !== false ? esc_attr( $value ) : $std;
					} else {
						$value = in_array( $value, $options ) ? $value : explode( ',', $std );
					}
				} else {
					$value  = $value !== false ? (array) $value : [];
					$_value = [];

					foreach ( $value as $val ) {
						if ( ! empty( $setting['source'] ) || ! empty( $setting['tags'] ) ) {
							$_value[] = esc_attr( $val );
						} else {
							if ( in_array( $val, $options ) ) {
								$_value[] = $val;
							}
						}
					}
					$value = $_value;
				}

				$this->set_instance( $field_name, $value );
			} elseif ( $setting['type'] == 'textarea' ) {
				$value = wp_kses( trim( wp_unslash( $value ) ), wp_kses_allowed_html( 'post' ) );

				if ( $value === false ) {
					$value = isset( $setting['std'] ) ? $setting['std'] : '';
				}

				$this->set_instance( $field_name, $value );
			} elseif ( in_array( $setting['type'], [ 'group', 'section' ] ) ) {
				if ( is_array( $setting['data'] ) ) {
					$_args             = (array) $args;
					$_args['settings'] = $setting['data'];
					$this->parseInstance( $_args );
				}
			} elseif ( $setting['type'] == 'multipleField' ) {
				if ( is_array( $setting['data'] ) ) {
					$setting['data'] = [ $setting ];

					foreach ( $setting['data'] as $s ) {
						$_args             = (array) $args;
						$_args['settings'] = $s['data'];
						$this->parseInstance( $_args );
					}
				}
			}

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
					'section'
				] ) ? true : false;

				if ( preg_match( '/\>/', $key, $m ) ) {
					$field = explode( '>', preg_replace( '/\s+/i', '', $key ) );
				} else {
					$field = [ $key ];
				}

				if ( ! in_array( $setting['type'], [ 'group', 'section' ] ) ) {
					$_args['field_name'] = array_merge( $_args['field_name'], $field );
				}

				$_args['instance'] = $this->get_instance( $_args['field_name'] );
				$hasAddMore        = ! empty( $setting['multiple'] ) && ! in_array( $setting['type'], [
					'section',
					'group',
					'select',
					'radio',
					'checkbox'
				] ) ? true : false;

				if ( $hasAddMore ) {
					$field_name = $_args['field_name'];

					if ( in_array( $setting['type'], [ 'multipleField', 'group', 'section' ] ) ) {
						$instance = isset( $_args['instance'][0] ) && is_array( $_args['instance'][0] ) ? $_args['instance'] : [ [] ];
					} else {
						$instance = isset( $_args['instance'][0] ) && ! is_array( $_args['instance'][0] ) ? $_args['instance'] : [ '' ];
					}

					$k = 0;

					foreach ( $instance as $_instance ) {
						$_args['field_name']   = $field_name;
						$_args['field_name'][] = $k;
						$_args['instance']     = $_instance;
						$k ++;

						$this->parseInstance( $_args );
					}

				} else {
					$this->parseInstance( $_args );
				}
			}
		}
	}

	private function explode_field_id( $str = '' ) {
		$name = explode( '-', $str );

		if ( empty( $name[0] ) ) {
			unset( $name[0] );
		}

		return array_diff( $name, [] );
	}

	private function implode_field_id( $array = [] ) {
		if ( empty( $array ) || ! is_array( $array ) ) {
			return;
		}

		return implode( '-', $array );
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

	private function implode_field_name( $array = [] ) {
		if ( empty( $array ) || ! is_array( $array ) ) {
			return;
		}

		$prefix = array_shift( $array );

		return count( $array ) > 0 ? sprintf( '%s[%s]', $prefix, implode( '][', $array ) ) : $prefix;
	}

	private function get_instance( $key = [], $instance = [] ) {
		if ( empty( $instance ) ) {
			$instance = $this->new_instance;
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

	private function esc_measurement( $val = '' ) {
		$val = trim( $val );

		if ( $val == '' || $val == 0 || $val == 'auto' ) {
			return $val;
		}

		preg_match( '/[0-9]+\.*[0-9]*(px|%|in|cm|mm|em|ex|pt|pc|rem|vw|vh|)$/', $val, $size );

		if ( ! empty( $size[0] ) ) {
			$size = ! empty( $size[1] ) ? $size[0] : sprintf( '%spx', $size[0] );
		} else {
			$size = 0;
		}

		return $size;
	}

	private function get_attachment_image_src( $image, $size = 'full' ) {
		if ( empty( $image ) ) {
			return false;
		} else if ( is_numeric( $image ) ) {
			return wp_get_attachment_image_src( $image, $size );
		} else if ( is_string( $image ) ) {
			preg_match( '/(.*?)\#([0-9]+)x([0-9]+)$/', $image, $matches );

			return ! empty( $matches ) ? $matches : false;
		}
	}
}
