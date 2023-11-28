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
	'field phone-field',
	mfd_get( $element, 'name' ),
	implode( ' ', $conditional_classes )
) );

if ( ! empty( $value ) ) {
	$numbers = explode( '-', $value );
	if ( count( $numbers ) != 3 ) {
		$value = preg_replace( '/([0-9]{3})([0-9]{4})([0-9]{4})/', '$1-$2-$3', $value );

		$numbers = explode( '-', $value );
	}

	$part1 = msm_get( $numbers, 0 );
	$part2 = msm_get( $numbers, 1 );
	$part3 = msm_get( $numbers, 2 );
} else {
	$part1 = '010';
	$part2 = '';
	$part3 = '';
}

$part1_lists = array( '010', '011', '016', '017', '019', '070' );


$require_certification = 'yes' == mfd_get( $element, 'certification' );

$hash = '';

if ( function_exists( 'is_account_page' ) && is_account_page() ) {
	if ( ! empty( $value ) ) {
		$expiration = apply_filters( 'msm_phone_certification_expiration', 10 * MINUTE_IN_SECONDS );
		$hash       = md5( preg_replace( '~\D~', '', $value ) );
		set_transient( 'msm_phone_certification_' . preg_replace( '~\D~', '', $value ), $hash, $expiration );
	}
}

?>
<style>
    <?php if ( 'yes' == mfd_get( $element, 'certification' ) ) : ?>
    .mfs_form form.ui.form .fields .phone-field .three.fields {
        flex-wrap: wrap;
    }

    div.field.phone-field.phone_number > .fields > div,
    div.field.phone-field.phone_number > .fields > div select {
        margin-bottom: 0 !important;
    }

    <?php endif; ?>
</style>
<div class="<?php echo $classes; ?>" data-element_name="<?php echo mfd_get( $element, 'name' ); ?>" data-form_slug="<?php echo $form->get_slug(); ?>" style="<?php echo mfd_get_conditional_style( $conditional_classes ); ?>">
	<?php mfd_output_title( $element ); ?>

	<?php if ( 'yes' == mfd_get( $element, 'temporary_password' ) ) : ?>
        <div class="fields">
            <div class="twelve wide column field">
                <input type="text" name="<?php echo mfd_get( $element, 'name' ); ?>_user_login" value="" placeholder="<?php _e( '아이디 또는 이메일을 입력해주세요.', 'mshop-members-s2' ); ?>"/>
            </div>
            <div class="four wide column field">
            </div>
        </div>
        <input type="hidden" name="<?php echo mfd_get( $element, 'name' ); ?>_temporary_password" value="yes"/>
	<?php endif; ?>

    <div class="three fields phone-number-wrapper">
        <div class="field">
            <select name="part1" class="ui dropdown phone-part">
				<?php foreach ( $part1_lists as $number ) : ?>
                    <option value="<?php echo $number; ?>" <?php echo( $number == $part1 ? 'selected' : '' ); ?>><?php echo $number; ?></option>
				<?php endforeach; ?>
            </select>
        </div>
        <div class="field">
            <input type="text" name="part2" class="phone-part" maxlength="4" value="<?php echo $part2; ?>"/>
        </div>
        <div class="field">
            <input type="text" name="part3" class="phone-part" maxlength="4" value="<?php echo $part3; ?>"/>
        </div>
		<?php if ( 'yes' == mfd_get( $element, 'certification' ) ) : ?>
            <div class="field phone-cerf-wrap">
                <input id="button" type="button" class="ui button fluid phone-certification" name="button" value="<?php _e( "인증번호 요청", "mshop-members-s2" ); ?>">
            </div>
		<?php endif; ?>

        <input type="hidden" name="<?php echo mfd_get( $element, 'name' ); ?>" value="<?php echo $value; ?>"/>
    </div>

	<?php if ( 'yes' == mfd_get( $element, 'certification' ) ) : ?>
        <div class="fields verification-wrapper hidden">
            <div class="twelve wide column field">
                <input type="text" name="certification_number" value="" placeholder="<?php _e( '휴대폰으로 전송된 인증번호를 입력해주세요', 'mshop-members-s2' ); ?>"/>
            </div>
            <div class="four wide column field">
                <div class="ui button fluid phone-verification"><?php _e( '인증하기', 'mshop-members-s2' ); ?></div>
            </div>
        </div>

		<?php if ( function_exists( 'is_account_page' ) && is_account_page() ) : ?>
            <input type="hidden" name="<?php echo mfd_get( $element, 'name' ); ?>_certification_number" value="<?php echo preg_replace( '~\D~', '', $value ); ?>"/>
		<?php else: ?>
            <input type="hidden" name="<?php echo mfd_get( $element, 'name' ); ?>_certification_number" value=""/>
		<?php endif; ?>
        <input type="hidden" name="<?php echo mfd_get( $element, 'name' ); ?>_find_login" value="<?php echo mfd_get( $element, 'find_login' ); ?>"/>
        <input type="hidden" name="<?php echo mfd_get( $element, 'name' ); ?>_allow_duplicate" value="<?php echo mfd_get( $element, 'allow_duplicate' ); ?>"/>
	<?php endif; ?>
</div>