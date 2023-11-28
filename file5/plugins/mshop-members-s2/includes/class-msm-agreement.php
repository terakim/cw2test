<?php

class MSM_Agreement {
    public $id = 0;
    public $post = null;
    public function __construct( $rule ) {
        if ( is_numeric( $rule ) ) {
            $this->id   = absint( $rule );
            $this->post = get_post( $this->id );
        } elseif ( $rule instanceof MSM_Agreement ) {
            $this->id   = absint( $rule->id );
            $this->post = $rule->post;
        } elseif ( isset( $rule->ID ) ) {
            $this->id   = absint( $rule->ID );
            $this->post = $rule;
        }
    }
    public function __isset( $key ) {
        return metadata_exists( 'post', $this->id, '_' . $key );
    }
    public function __get( $key ) {
        if ( 'slug' == $key ){
            $value = 'mshop_agreement_' . $this->id;
        }else if( 'title' == $key ){
            return $this->post->post_title;
        }else if( 'content' == $key ){
            return $this->post->post_content;
        }else{
            $value = get_post_meta( $this->id, '_' . $key, true );
        }

        if ( false !== $value ) {
            $this->$key = $value;
        }

        return $value;
    }
}
