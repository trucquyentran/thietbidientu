<?php
defined( 'ABSPATH' ) || exit;

if ( ! is_array( $options ) ) {
	return;
}

$action = '';
$sufix  = '';

if ( ! empty( $tags ) ) {
	$action = 'data-js="select2" data-tags="true"';

	if ( ! empty( $value ) ) {
		$value = is_array( (array) $value ) ? (array) $value : [ $value ];

		foreach ( $value as $item ) {
			$options[ $item ] = [
				'text' => $item
			];
		}
	}
} elseif ( ! empty( $source ) && ! empty( $field ) ) {
	$_options = [];
	$action   = sprintf( 'data-js="select2" data-source="%s" data-field="%s"', $source, $field );
	$options  = apply_filters( 'WPCSN/fill/' . $source, [], [
		'field'    => $field,
		'selected' => $value
	] );

	if ( isset( $options['items'] ) && is_array( $options['items'] ) ) {
		foreach ( $options['items'] as $option ) {
			$_options[ $option['id'] ] = [
				'text' => $option['text']
			];
		}

		$options = $_options;
	}

	unset( $_options );
} else {
	$options = $options;
}

if ( ! empty( $multiple ) ) {
	$sufix = '[]';
}

printf( '<select class="form-control select" id="%s" %s name="%s" %s %s>',
	$field_id, $tip, $field_name . $sufix, $action, ! empty( $multiple ) ? 'multiple' : ''
);

if ( is_array( $options ) ) {
	if ( ! is_array( $value ) ) {
		$value = [ $value ];
	}

	foreach ( $options as $key => $option ) {
		if ( empty( $option['text'] ) ) {
			continue;
		}

		$selected = ( in_array( $key, $value ) ) ? 'selected="selected"' : '';
		printf( '<option value="%s" %s>%s</option>', esc_attr( $key ), $selected, esc_html( $option['text'] ) );
	}
}

echo '</select>';