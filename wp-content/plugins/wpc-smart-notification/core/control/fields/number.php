<?php
defined( 'ABSPATH' ) || exit;

if ( is_array( $value ) || $value === false ) {
	$value = isset( $setting['std'] ) ? $setting['std'] : '';
}

$step = isset( $setting['step'] ) ? $setting['step'] : 1;
$min  = isset( $setting['min'] ) ? $setting['min'] : 0;
$max  = isset( $setting['max'] ) ? $setting['max'] : 100;