<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$conditional_classes = mfd_get_conditional_class( $element );

$classes = mfd_make_class( array(
	'yes' == mfd_get( $element, 'required' ) ? 'required' : '',
	'yes' == mfd_get( $element, 'inline' ) ? 'inline' : '',
	mfd_get( $element, 'class' ),
	mfd_get( $element, 'width', 'sixteen wide' ),
	'field',
	implode( ' ', $conditional_classes )
) );

?>

<div class="<?php echo $classes; ?>">
    <script type="text/template" id="tmpl-msm-sigungu-template">
        <div class="sigungu {{data.class}}" data-sido={{{data.sido}}} data-sigungu='{{{ data.sigungu }}}' data-postcodes="{{{ data.postcodes }}}">
            <span>{{{data.sigungu}}}</span>
        </div>
    </script>

    <script type="text/template" id="tmpl-msm-selected-region-template">
        <div class="region {{data.class}}" data-sido={{{data.sido}}} data-sigungu='{{{ data.sigungu }}}' data-postcodes="{{{ data.postcodes }}}">
            <span>{{{data.sido}}} > {{{data.sigungu}}}</span>
            <div class="delete">X</div>
        </div>
    </script>

	<?php mfd_output_title( $element ); ?>
    <div class="region-selector-wrapper">
        <div class="region-selector">
            <div class="sido-wrapper">
				<?php foreach ( MFD_Region_Selector_Field::get_regions() as $sido => $sigungu ) : ?>
                    <div class="sido" data-sido="<?php echo $sido; ?>"><span><?php echo $sido; ?></span></div>
				<?php endforeach; ?>
            </div>
            <div class="sigungu-wrapper">

            </div>
        </div>
        <div class="selected-region-wrapper">
        </div>
        <input type="hidden" name="<?php echo mfd_get( $element, 'name' ); ?>" value="<?php echo esc_attr( get_user_meta( get_current_user_id(),  mfd_get( $element, 'name' ), true ) ); ?>">
    </div>
</div>