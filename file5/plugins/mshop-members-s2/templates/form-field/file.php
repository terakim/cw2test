<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

wp_enqueue_script( 'msm-file-upload', MSM()->plugin_url() . '/assets/js/file-upload.js', array(), MSM_VERSION );
wp_enqueue_style( 'msm-file-upload', MSM()->plugin_url() . '/assets/css/file-upload.css', array(), MSM_VERSION );

$conditional_classes = mfd_get_conditional_class( $element );

$classes = mfd_make_class( array(
	'yes' == mfd_get( $element, 'required' ) ? 'required' : '',
	'yes' == mfd_get( $element, 'inline' ) ? 'inline' : '',
	mfd_get( $element, 'class' ),
	mfd_get( $element, 'width', 'sixteen wide' ),
	'field',
	implode( ' ', $conditional_classes )
) );

$filename = array();
$images   = array();

if ( is_array( $value ) ) {
	$filename = array_map( function ( $file ) {
		return urldecode( basename( $file['filename'] ) );
	}, $value );

    $images = array_map( function ( $image ) {
        return $image['image'];
    }, $value );
}
?>

<script>
    jQuery( document ).ready( function ( $ ) {
        $( 'input[name="<?php echo mfd_get( $element, 'name' ); ?>"]' ).customFile();
    } );
</script>

<div class="<?php echo $classes; ?>" style="<?php echo mfd_get_conditional_style( $conditional_classes ); ?>">
	<?php mfd_output_title( $element ); ?>

    <input style="display: none;"
           type="<?php echo mfd_get( $element, 'type', 'text' ); ?>"
           id="<?php echo mfd_get( $element, 'name' ); ?>"
           name="<?php echo mfd_get( $element, 'name' ); ?>"
		<?php echo 'yes' == mfd_get( $element, 'multiple' ) ? 'multiple' : ''; ?>
           value="<?php echo 1 < count( $filename ) ? sprintf( __( '%s 파일 외 %s개', 'mshop-members-s2' ), current( $filename ), count( $filename ) - 1 ) : current( $filename ); ?>"
           placeholder="<?php echo mfd_get( $element, 'placeHolder' ); ?>"/>
    <input type="hidden" name="_<?php echo mfd_get( $element, 'name' ); ?>" value="<?php echo esc_attr( json_encode( $value ) ); ?>">

    <?php
    if ( 'file' == mfd_get( $element, 'type', 'text' ) ) :

        $file_label = get_user_meta( get_current_user_id(), mfd_get( $element, 'name' ) . '_label' );

        if ( ! empty( $file_label ) && is_array( $file_label ) ) {
            $files = implode( ', ', $file_label );

            if ( ! empty( $files ) ) {
                echo sprintf( __( '<label class="file-download">파일 다운로드 : %s</label>', 'mshop-members-s2' ), implode( ', ', explode( '<br>', $files ) ) );
            }
        }
    ?>
        <ul class="image-preview" name="<?php echo mfd_get( $element, 'name' ); ?>">
            <?php echo implode( '', $images ); ?>
        </ul>
    <?php endif; ?>
</div>

