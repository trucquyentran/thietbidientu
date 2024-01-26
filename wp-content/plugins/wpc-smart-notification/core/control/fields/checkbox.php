<?php
defined( 'ABSPATH' ) || exit;

if ( $value === false ) {
	$value = isset( $setting['std'] ) ? explode( ',', $setting['std'] ) : [ '' ];
} elseif ( ! is_array( $value ) ) {
	$value = [ $value ];
}

$options = isset( $setting['options'] ) && is_array( $setting['options'] ) ? $setting['options'] : [];