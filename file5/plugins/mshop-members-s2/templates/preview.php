<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

get_header(); ?>

	<div id="main-content" class="main-content">

		<div id="primary" class="content-area">
			<div id="content" class="site-content" role="main">

				<?php echo do_shortcode( "[mshop_form_designer slug='" . $_REQUEST['msm_preview'] . "' default=true]" ); ?>

			</div><!-- #content -->
		</div><!-- #primary -->
	</div><!-- #main-content -->

<?php

get_footer();

