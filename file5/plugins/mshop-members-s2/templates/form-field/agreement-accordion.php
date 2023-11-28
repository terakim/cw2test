<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$agreement = new MSM_Agreement( $agreement );

$terms = $agreement->content;
$terms = str_replace( __( '{쇼핑몰명}', 'mshop-members-s2' ), get_option( 'company_name' ), $terms );
$terms = str_replace( __( '{쇼핑몰URL}', 'mshop-members-s2' ), home_url(), $terms );

$classes = mfd_make_class( array (
	mfd_get( $element, 'class' ),
	mfd_get( $element, 'width', 'sixteen wide' ),
	$agreement->slug,
	'ui styled fluid accordion field'
) );

$required = 'yes' == $agreement->mandatory ? 'required' : '';

?>

<div class="<?php echo $classes; ?>">
    <div class="field title <?php echo $required; ?> <?php echo $agreement->slug; ?>">
        <i class="dropdown icon"></i>
		<?php echo $agreement->title; ?>
		<?php if ( ! isset( $element['show_checkbox'] ) || $element['show_checkbox'] != 'no' ) : ?>
            <div class="ui checkbox agreement-item"
                 style="float: right; margin-top: 3px; height: 17px; margin-bottom: 0px;">
                <input type="checkbox" name="<?php echo $agreement->slug; ?>"/>
                <label><?php echo __( '동의합니다.</p>', 'mshop-members-s2' ); ?></label>
            </div>
		<?php endif; ?>
    </div>
    <div class="content <?php echo mfd_get( $element, 'class' ); ?> <?php echo $agreement->slug; ?>">
        <textarea readOnly style="resize: none;"><?php echo strip_tags( $terms ); ?></textarea>
    </div>
</div>