<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$agreement = new MSM_Agreement( $agreement );

$terms = $agreement->content;
$terms = str_replace( __( '{쇼핑몰명}', 'mshop-members-s2' ), get_option( 'company_name' ), $terms );
$terms = str_replace( __( '{쇼핑몰URL}', 'mshop-members-s2' ), home_url(), $terms );


$required = 'yes' == $agreement->mandatory ? 'required' : '';

$classes = mfd_make_class( array (
	mfd_get( $element, 'class' ),
	mfd_get( $element, 'width', 'sixteen wide' ),
	$required,
	'field'
) );

?>

<div class="<?php echo $classes; ?>">
    <label><?php echo $agreement->title; ?></label>
    <textarea readOnly style="resize: none;"><?php echo strip_tags( $terms ); ?></textarea>

	<?php if ( ! isset( $element['show_checkbox'] ) || $element['show_checkbox'] != 'no' ) : ?>
        <div class="ui checkbox agreement-item" style="margin-top : 5px;">
            <input type="checkbox" name="<?php echo $agreement->slug; ?>"/>
            <label><?php echo $agreement->title . __( '에 동의합니다.</p>', 'mshop-members-s2' ); ?></label>
        </div>
	<?php endif; ?>
</div>