<?php
defined( 'ABSPATH' ) || exit;

if ( ! is_array( $options ) ) {
	return;
}

printf( '<ul id="%s" %s class="sortable">', $field_id, $tip );

if ( is_array( $options ) ) {
	if ( ! is_array( $value ) ) {
		$value = [ $value ];
	}

	$option_key = array_keys( $options );

	while ( ! empty( $options ) ) {
		if ( ! empty( $value ) ) {
			$key = array_shift( $value );
		} else {
			$key = array_shift( $option_key );
		}

		if ( empty( $key ) ) {
			continue;
		}

		printf( '<li class="ui-state-default">
			<input type="hidden" name="%s[]" value="%s"/>
			<i class="ui-icon ui-icon-arrowthick-2-n-s"></i>
			<span class="text">%s</span>
			</li>',
			$field_name,
			esc_attr( $key ),
			esc_html( $options[ $key ]['text'] )
		);

		unset( $options[ $key ] );
		$option_key = array_keys( $options );
	}
}

echo '</ul>';