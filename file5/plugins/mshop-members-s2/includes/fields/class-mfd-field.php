<?php

class MFD_Field {

	public $property;

	public function __construct( $field = null ) {
		$this->property = mfd_get( $field, 'property' );
	}

	public function __isset( $key ) {
		return true;
	}
	public function __get( $key ) {
		if ( ! empty( $this->property ) && ! empty( $this->property[ $key ] ) ) {
			$this->$key = $this->property[ $key ];
		} else {
			$this->$key = '';
		}

		return $this->$key;
	}
	public function get_id() {
		return msm_get( $this->property, 'id' );
	}
	public function get_name() {
		return msm_get( $this->property, 'name' );
	}
	public function value( $params, $args = array() ) {
		return apply_filters( 'mfd_field_value', urldecode( mfd_get( $params, $this->name ) ), $this );
	}
	public function value_label( $params, $args = array() ) {
		return apply_filters( 'mfd_field_value_label', $this->value( $params, $args ), $this );
	}
	public function has_value_label() {
		return false;
	}
	function is_savable(){
		return apply_filters( 'mfd_field_is_savable', true, $this );
	}
    public function get_field( $all ) {

	    if ( ! $all && ! $this->is_savable() ) {
		    return array();
	    }
	    if ( empty( $this->items ) ) {
		    return array( $this );
	    } else {
		    $fields = array();
		    foreach ( $this->items as $field ) {
			    $fields = array_merge( $fields, mfd_get_field( $field, $all ) );
		    }

		    return array_filter( $fields );
	    }
    }
	public function update_meta( $id, $updator, $params, $args ) {
		$updator( $id, $this->name, $this->value( $params, $args ) );

		if ( $this->has_value_label() ) {
			$updator( $id, $this->name . '_label', $this->value_label( $params, $args ) );
		}
	}
	public function output( $element, $post, $form ){}
}
