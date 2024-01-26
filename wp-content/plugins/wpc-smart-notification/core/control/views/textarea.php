<?php
defined( 'ABSPATH' ) || exit;
?>
<textarea class="form-control textarea" id="<?php echo $field_id ?>" placeholder="<?php echo $placeholder ?>" <?php echo $tip ?> name="<?php echo $field_name ?>" cols="20" rows="3"><?php echo esc_textarea( $value ); ?></textarea>
