<?php

class MSM_Form {
	public $id = 0;
	public $post = null;
	protected $items = array();
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
		$value = get_post_meta( $this->id, '_' . $key, true );

		if ( in_array( $key, array( 'form_data' ) ) ) {
			$value = json_decode( $this->post->post_content, true );
		} elseif ( in_array( $key, array( 'form_data' ) ) ) {
			$value = json_decode( $value, true );
		}

		if ( false !== $value ) {
			$this->$key = $value;
		}

		return $value;
	}
	public function get_fields( $all = false ) {
		return mfd_get_form_fields( $this->form_data, $all );
	}

	public function has_field( $types ) {
		$fields = $this->get_fields( true );

		$fields = array_filter( $fields, function ( $field ) use ( $types ) {
			return in_array( get_class( $field ), $types );
		} );

		return ! empty( $fields );
	}

	public function get_field( $types ) {
		$fields = $this->get_fields( true );

		$fields = array_filter( $fields, function ( $field ) use ( $types ) {
			return in_array( get_class( $field ), $types );
		} );

		return $fields;
	}


	public function find_field( $id ) {
		$fields = $this->get_fields( true );

		$fields = array_filter( $fields, function ( $field ) use ( $id ) {
			return ! empty( $field->property['name'] ) && $id == $field->property['name'];
		} );

		return ! empty( $fields ) ? current( $fields ) : null;
	}

	public function get_submit_action() {
		return get_post_meta( $this->id, '_submit_action', true );
	}

	public function get_post_submit_action() {
		return get_post_meta( $this->id, '_msm_submit_actions', true );
	}

	public function get_slug() {
		return $this->post->post_name;
	}
}
