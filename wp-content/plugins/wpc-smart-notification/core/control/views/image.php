<?php
defined( 'ABSPATH' ) || exit;

$src         = ! empty( $value ) ? wp_get_attachment_image_src( absint( $value ), 'full' )[0] : '';
$has_img     = ! empty( $src ) ? ' has-img' : '';
$placeholder = ! empty( $placeholder ) ? esc_html( $placeholder ) : esc_html__( 'Upload Thumbnail', 'wpc-smart-notification' );
$img         = ! empty( $src ) ? '<img src="' . $src . '">' : '';
?>
<div class="img-preview">
	<span class="src"><?php echo $src ?></span> <span class="button<?php echo $has_img ?>">
		<?php echo $img ?>
		<a href="#" class="upload"><?php echo $placeholder ?></a>
		<a href="#" class="remove"></a>
	</span>
</div>
<input type="hidden" class="img-id" id="<?php echo $field_id ?>" name="<?php echo $field_name ?>" value="<?php echo $value ?>"/>
