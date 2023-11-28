<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly
?>

<div id="content" role="main" style="padding: 0px 5%;">
	<?php echo do_shortcode( "[mshop_form_designer slug='" . get_option( 'mshop_members_tac_form_for_guest' ) . "' default=true]" ); ?>
</div>
