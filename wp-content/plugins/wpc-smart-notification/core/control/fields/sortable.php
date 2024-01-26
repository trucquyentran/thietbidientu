<?php
defined( 'ABSPATH' ) || exit;

$options = isset( $setting['options'] ) && is_array( $setting['options'] ) ? $setting['options'] : [];

if ( $value === false ) {
	$value = isset( $setting['std'] ) ? $setting['std'] : array_keys( $options );
} elseif ( ! is_array( $value ) ) {
	$value = [ $value ];
}