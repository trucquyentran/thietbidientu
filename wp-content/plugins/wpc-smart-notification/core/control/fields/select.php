<?php
defined( 'ABSPATH' ) || exit;

if ( $value === false ) {
	$value = isset( $setting['std'] ) ? $setting['std'] : [];
} elseif ( ! is_array( $value ) ) {
	$value = [ $value ];
}

$options  = isset( $setting['options'] ) && is_array( $setting['options'] ) ? $setting['options'] : [];
$multiple = ! empty( $setting['multiple'] ) ? true : false;
$tags     = ! empty( $setting['tags'] ) ? true : false;
$source   = ! empty( $setting['source'] ) ? $setting['source'] : '';
$field    = ! empty( $setting['field'] ) ? $setting['field'] : '';