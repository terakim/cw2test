<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class MFD_Section_Field extends MFD_Field{

    function save_meta() {
        return false;
    }

    public function output( $element ) {
        if( false ) {
            ?>
            <h2><?php _e( $element['title'] ); ?></h2>
            <?php
        }else{
            ?>
            <h3 class="ui dividing header"><?php _e( $element['title'] ); ?></h3>
            <?php
        }
    }

}