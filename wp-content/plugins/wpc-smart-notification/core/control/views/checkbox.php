<?php
defined( 'ABSPATH' ) || exit;

if ( ! is_array( $options ) || ! is_array( $value ) ) {
	return;
}

foreach ( $options as $key => $option ) {
	echo '<label class="checkbox-container"><input class="checkbox" id="', $field_id, '" name="', $field_name, '[]', '" type="checkbox" value="', $key, '"', in_array( $key, $value ) ? 'checked' : '', '><span class="checkmark"></span>
	<span for="', $field_id, '">', $option['text'], '</span></label>';
}