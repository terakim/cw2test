<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$classes = mfd_make_class( array (
	mfd_get( $element, 'class' ),
	mfd_get( $element, 'width', 'sixteen wide' ),
	'field',
	implode( ' ', mfd_get_conditional_class( $element ) )
) );

$settings = array(
		'media_buttons' => 'yes' == mfd_get( $element, 'media_buttons'),
		'drag_drop_upload' => 'yes' == mfd_get( $element, 'drag_drop_upload'),
		'tinymce' => array(
				'themes' => "inlite",
				'body_class' => 'my_class'
		)
);

?>

<div class="<?php echo $classes; ?>">
	<script>
		jQuery('body').on('msm-before-submit', function(){
			var editor = tinymce.get( '<?php echo $element['name']; ?>' );

			if( editor ) {
				editor.save();
			}
		});
	</script>
	<?php mfd_output_title( $element ); ?>
	<?php wp_editor( htmlspecialchars_decode( $value), $element['name'], $settings ); ?>
</div>

