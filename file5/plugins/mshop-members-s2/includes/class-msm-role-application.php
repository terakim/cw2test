<?php

class MSM_Role_Application {
	public $id = 0;
	public $post = null;
	public function __construct( $form ) {
		if ( is_numeric( $form ) ) {
			$this->id   = absint( $form );
			$this->post = get_post( $this->id );
		} elseif ( $form instanceof MSM_Form ) {
			$this->id   = absint( $form->id );
			$this->post = $form->post;
		} elseif ( isset( $form->ID ) ) {
			$this->id   = absint( $form->ID );
			$this->post = $form;
		}
	}
	public function __isset( $key ) {
		return metadata_exists( 'post', $this->id, '_' . $key );
	}
	public function __get( $key ) {
		$value = get_post_meta( $this->id, $key, true );

		if ( false !== $value ) {
			$this->$key = $value;
		}

		return $value;
	}
	public static function get_statuses() {
		return array(
			'mshop-apply'    => '요청',
			'mshop-approved' => '승인',
			'mshop-rejected' => '반려'
		);
	}

	public function approved() {
		$user_id      = $this->user_id;
		$current_role = $this->current_role;
		$request_role = $this->request_role;

		$user_role = mshop_members_get_user_role( $user_id );

		if ( $user_role == $current_role ) {
			if ( apply_filters( 'msm_process_approved', true, $this->id ) ) {
				$user = get_user_by( 'id', $user_id );
				$user->set_role( $request_role );
			}

			$this->update_status( 'mshop-approved' );
			update_user_meta( $user_id, 'role_application_status', 'mshop-approved' );
		}
	}

	public function rejected() {
		$user_id      = $this->user_id;
		$current_role = $this->current_role;
		$request_role = $this->request_role;

		$user_role = mshop_members_get_user_role( $user_id );

		if ( apply_filters( 'msm_process_rejected', true, $this->id ) && $user_role == $request_role ) {
			$user = get_user_by( 'id', $user_id );
			$user->set_role( $current_role );

			$this->update_status( 'mshop-rejected' );
			update_user_meta( $user_id, 'role_application_status', 'mshop-rejected' );
		} else {
			$this->update_status( 'mshop-rejected' );
			update_user_meta( $user_id, 'role_application_status', 'mshop-rejected' );
		}
	}

	public function update_status( $new_status ) {
		$this->post->post_status = $new_status;
		wp_update_post( $this->post );
		$timezone_format = _x( 'Y-m-d H:i:s', 'timezone date format' );
		update_post_meta( $this->id, 'processing_time', date_i18n( $timezone_format ) );
	}
}
