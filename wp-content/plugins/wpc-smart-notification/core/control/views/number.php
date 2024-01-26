<?php
defined( 'ABSPATH' ) || exit;
?>
<input class="form-control number" id="<?php echo $field_id ?>" placeholder="<?php echo $placeholder ?>" <?php echo $tip ?> name="<?php echo $field_name ?>" type="number" step="<?php echo floatval( $step ); ?>" min="<?php echo floatval( $min ); ?>" max="<?php echo floatval( $max ); ?>" value="<?php echo floatval( $value ); ?>"/>