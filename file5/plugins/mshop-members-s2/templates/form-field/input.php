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

$type = mfd_get( $element, 'type', 'text' );

if ( 'yes' == msm_get( $element, 'emailVerification' ) ) {
	$classes .= ' msm-email-verification-wrapper';
} else if ( 'yes' == msm_get( $element, 'checkDuplicate' ) ) {
	$classes .= ' msm-check-duplicate-wrapper';
}
?>
<div class="<?php echo $classes; ?>" style="<?php echo mfd_get_conditional_style( $conditional_classes ); ?>" data-element_name="<?php echo mfd_get( $element, 'name' ); ?>" data-form_slug="<?php echo $form->get_slug(); ?>">
	<?php mfd_output_title( $element ); ?>
	<?php if ( 'yes' == msm_get( $element, 'emailVerification' ) || 'yes' == msm_get( $element, 'checkDuplicate' ) ) : ?>
        <div class="two fields email-wrapper">
            <div class="twelve wide column field">
                <input type="<?php echo $type; ?>"
                       name="<?php echo mfd_get( $element, 'name' ); ?>"
					<?php echo 'yes' == mfd_get( $element, 'multiple' ) ? 'multiple' : ''; ?>
					<?php echo 'yes' == mfd_get( $element, 'readonly' ) ? 'readonly' : ''; ?>
                       value="<?php echo ! empty( $value ) ? $value : ''; ?>"
                       maxlength="<?php echo mfd_get( $element, 'maxlength' ); ?>"
                       placeholder="<?php echo mfd_get( $element, 'placeHolder' ); ?>"/>
            </div>
            <div class="four wide column field">
				<?php if ( 'yes' == msm_get( $element, 'emailVerification' ) ) : ?>
                    <input type="button" class="ui black button fluid send-cert-number" value="<?php _e( "인증번호 요청", "mshop-members-s2" ); ?>">
                    <input type="hidden" name="<?php echo mfd_get( $element, 'name' ); ?>_check_duplicate" value=""/>
				<?php elseif ( 'yes' == msm_get( $element, 'checkDuplicate' ) ) : ?>
                    <input type="button" class="ui black button fluid check-duplicate" value="<?php _e( "중복확인", "mshop-members-s2" ); ?>">
                    <input type="hidden" name="<?php echo mfd_get( $element, 'name' ); ?>_check_duplicate" value=""/>
				<?php endif; ?>
            </div>
        </div>
		<?php if ( 'yes' == msm_get( $element, 'emailVerification' ) ) : ?>
            <div class="two fields verification-wrapper hidden">
                <div class="twelve wide column field">
                    <input type="text" name="certification_number" value="" placeholder="<?php _e( '이메일로 전송된 인증번호를 입력해주세요', 'mshop-members-s2' ); ?>"/>
                </div>
                <div class="four wide column field">
                    <div class="ui button fluid email-verification"><?php _e( '인증하기', 'mshop-members-s2' ); ?></div>
                    <input type="hidden" name="<?php echo mfd_get( $element, 'name' ); ?>_certification_number" value=""/>
                </div>
            </div>
		<?php endif; ?>
	<?php else: ?>
        <input type="<?php echo $type; ?>"
               name="<?php echo mfd_get( $element, 'name' ); ?>"
			<?php echo 'yes' == mfd_get( $element, 'multiple' ) ? 'multiple' : ''; ?>
			<?php echo 'yes' == mfd_get( $element, 'readonly' ) ? 'readonly' : ''; ?>
               value="<?php echo ! empty( $value ) ? $value : ''; ?>"
               maxlength="<?php echo mfd_get( $element, 'maxlength' ); ?>"
               placeholder="<?php echo mfd_get( $element, 'placeHolder' ); ?>"/>
	<?php endif; ?>
</div>