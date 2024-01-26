<?php
defined( 'ABSPATH' ) || exit;

if ( ! is_array( $options ) ) {
	return;
}

foreach ( $options as $key => $option ) {
	echo '<label class="checkbox-container"><input class="radio" id="', $field_id, '" name="', $field_name, '[]', '" type="radio" value="', $key, '"', $key == $value ? 'checked' : '', '/><span class="checkmark"></span>
	<span for="', $field_id, '">', $option['text'], '</span></label>';
}