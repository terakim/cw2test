<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class MFD_Address_Field extends MFD_Field {

	function save_meta( $element = null ) {
		return true;
	}

	public function output( $element, $post, $form ) {
		$postcode = '';
		$address1 = '';
		$address2 = '';

		$classes = mfd_make_class( array (
			'yes' == mfd_get( $element, 'required' ) ? 'required' : '',
			mfd_get( $element, 'class' ),
			mfd_get( $element, 'width' ),
			'msaddr_widget field'
		) );

		if ( $post ) {
			if ( $post instanceof WP_Post ) {
				$postcode = get_post_meta( $post->ID, $this->id . '_postcode', true );
				$address1 = get_post_meta( $post->ID, $this->id . '_address_1', true );
				$address2 = get_post_meta( $post->ID, $this->id . '_address_2', true );
			} else if ( $post instanceof WP_User ) {
				$postcode = get_user_meta( $post->ID, $this->id . '_postcode', true );
				$address1 = get_user_meta( $post->ID, $this->id . '_address_1', true );
				$address2 = get_user_meta( $post->ID, $this->id . '_address_2', true );
			}
		}

		wp_enqueue_style( 'msaddr-widget', MSADDR()->plugin_url() . '/assets/css/msaddr-widget.css', array (), MSADDR()->version );
		wp_enqueue_script( 'msaddr-widget', MSADDR()->plugin_url() . '/assets/js/msaddr-widget.js', array ( 'jquery', 'underscore' ), MSADDR()->version );
		wp_localize_script( 'msaddr-widget', '_msaddr_widget', array (
			'primary_address_type' => get_option( 'msaddr_primary_address_type', 'road' ),
			'show_other_address'   => get_option( 'msaddr_show_other_address', 'no' )
		) );

		?>
        <div class="<?php echo $classes; ?>" data-id="<?php echo mfd_get( $element, 'id' ); ?>">
			<?php if ( ! empty( $element['title'] ) ) : ?>
                <label><?php _e( $element['title'] ); ?></label>
			<?php endif; ?>
            <div class="two fields">
                <div class="field">
                    <input type="text" name="<?php echo mfd_get( $element, 'id' ); ?>_postcode" data-validate="<?php echo mfd_get( $element, 'id' ); ?>" placeholder="우편번호" value="<?php echo $postcode; ?>" style="width:100px;" readonly/>
                    <input type="button" class="button" name="msm_address_search" value="<?php _e( $element['buttonText'] ); ?>">
                </div>
                <div class="field">
                </div>
            </div>
            <div class="two fields">
                <div class="field">
                    <input type="text" name="<?php echo mfd_get( $element, 'id' ); ?>_address_1" data-validate="<?php echo mfd_get( $element, 'id' ); ?>" placeholder="기본주소" value="<?php echo $address1; ?>" readonly/>
                </div>
                <div class="field">
					<?php if ( 'yes' == get_option( 'msaddr_required_field_address2', 'no' ) ) : ?>
                        <input type="text" name="<?php echo mfd_get( $element, 'id' ); ?>_address_2" data-validate="<?php echo mfd_get( $element, 'id' ); ?>" placeholder="상세주소" value="<?php echo $address2; ?>"/>
					<?php else: ?>
                        <input type="text" name="<?php echo mfd_get( $element, 'id' ); ?>_address_2" placeholder="상세주소" value="<?php echo $address2; ?>"/>
					<?php endif; ?>
                </div>
            </div>
            <div class="ui scrolling small modal form mshop_address_search <?php echo mfd_get( $element, 'id' ); ?>">
                <div class="header">
                    주소 검색
                </div>
                <div class="scrolling content">
                    <div class="search_wrapper">
                        <div class="field">
                            <input type="text" class="msaddr_keyword" name="<?php echo mfd_get( $element, 'id' ); ?>_search" placeholder=""/>
                        </div>
                        <div class="field result">
                            <table class="ui celled table unstackable mshop_address_result">
                                <thead>
                                <tr>
                                    <th class="postalcode">우펀번호</th>
                                    <th>주소</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                        <div class="field pagination">
                            <div class="ui right floated pagination menu">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="actions">
                    <div class="ui black deny button">취소</div>
                    <div class="ui positive right labeled icon button">
                        확인
                        <i class="checkmark icon"></i>
                    </div>
                </div>
            </div>
        </div>
		<?php
	}

	public function update_meta( $id, $updator, $params, $args ) {
		$postcode  = urldecode( mfd_get( $params, $this->id . '_postcode' ) );
		$address_1 = urldecode( mfd_get( $params, $this->id . '_address_1' ) );
		$address_2 = urldecode( mfd_get( $params, $this->id . '_address_2' ) );

		$updator( $id, $this->id . '_postcode', $postcode );
		$updator( $id, $this->id . '_address_1', $address_1 );
		$updator( $id, $this->id . '_address_2', $address_2 );
		$updator( $id, 'mshop_' . $this->id . '_address-postnum', $postcode );
		$updator( $id, 'mshop_' . $this->id . '_address-addr1', $address_1 );
		$updator( $id, 'mshop_' . $this->id . '_address-addr2', $address_2 );

		$updator( $id, $this->id, sprintf( '(%s) %s %s', $postcode, $address_1, $address_2 ) );
	}

}