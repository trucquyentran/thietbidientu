<?php
defined( 'ABSPATH' ) || exit;

$value = esc_attr( $value );
?>
<label class="indicator"> <span style="background-color: <?php echo $value ?>"></span> </label>
<input id="<?php echo $field_id ?>" class="form-control" data-js="colorpicker" type="text" name="<?php echo $field_name ?>" placeholder="<?php echo $placeholder ?>" <?php echo $tip ?>
	value="<?php echo $value ?>"/>