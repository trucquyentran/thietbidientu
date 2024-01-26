<?php
defined( 'ABSPATH' ) || exit;

$src         = ! empty( $value ) ? wp_get_attachment_image_src( absint( $value ), 'full' )[0] : '';
$has_img     = ! empty( $src ) ? ' has-img' : '';
$placeholder = ! empty( $placeholder ) ? esc_html( $placeholder ) : esc_html__( 'Upload Thumbnail', 'wpc-smart-notification' );
$img         = ! empty( $src ) ? '<img src="' . $src . '">' : '';