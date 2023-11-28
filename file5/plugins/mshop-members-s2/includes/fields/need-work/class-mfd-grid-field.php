<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class MFD_Grid_Field extends MFD_Field{

    public function output( $element ) {
	    $class   = array();
	    $class[] = ! empty( $element['required'] ) && 'yes' == $element['required'] ? 'required' : '';
	    $class[] = ! empty( $element['inline'] ) && 'yes' == $element['inline'] ? 'inline' : '';
	    ?>
	    <div class="field">
		    <div class="ui padded grid">
			    <?php
			    foreach ( $element['items'] as $item ) {
				    echo '<div class="' . ( ! empty( $item['class'] ) ? $item['class'] : 'eight wide column' ) . '">';
				    mfd_output_element( $item );
				    echo '</div>';
			    }
			    ?>
		    </div>
	    </div>
	    <?php
    }

}