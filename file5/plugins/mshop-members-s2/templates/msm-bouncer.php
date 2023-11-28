<?php

define('WP_USE_THEMES', true);

do_action( 'template_redirect' );
wp_head();
get_header();
?>
<style>
	#content .row{
		margin: 0 auto;
	}
	.footer-widgets.footer .row{
		margin: 0 auto;
	}

	.msm_wsl_container{
		padding-top: 100px;
		max-width: 500px;
		margin: 0 auto;
	}

	.msm_wsl_container .ui.form input{
		border: 1px solid #e2e2e2;
	}

	.msm_wsl_container .ui.form input:focus{
		border: 1px solid #23e259;
	}

</style>
<div id="content" class="blog-wrapper blog-single page-wrapper">
	<div class="row align-center">
		<div class="large-10 col">
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<div class="article-inner">
					<div class="msm_wsl_container">
						<?php echo do_shortcode( "[mshop_form_designer slug='" . $form_id . "' default=true top_message='no' bottom_message='yes' error_popup='yes']" ); ?>
					</div>
				</div>
			</article><!-- #-<?php the_ID(); ?> -->
		</div>
	</div>
</div>


<?php
get_footer();

