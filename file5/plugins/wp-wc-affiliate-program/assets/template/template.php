<?php

/**
 * 
 *
 * This is the template for Vendor store setup And vendor store page which can override any theme template
 *
 * 
 *
 * 
 */

$rtwwwap_user_id 		= get_current_user_id();


$rtwwwap_is_aff_approved 	= get_user_meta( get_current_user_id(), 'rtwwwap_aff_approved', true ) ;

$rtwwwap_is_affiliate 		= get_user_meta( $rtwwwap_user_id, 'rtwwwap_affiliate', true );


    if ($rtwwwap_is_aff_approved && $rtwwwap_is_affiliate) {
       
            ?>
           <!DOCTYPE html>
           <html lang="en">
               <head> 
                   <meta name="viewport" content="width=device-width, initial-scale=1.0">



                <?php wp_head(); ?></head>
           <body class="rtwwwap_template_body"><?php
           while (have_posts()) :
                the_post();
                the_content();
            endwhile;  
           ?></body>
           </html>
           <footer>
            <?php wp_footer();
           }
     else {      
                $rtwwwap_affiliate_page_id = get_option('rtwwwap_affiliate_page_id');
                $rtwwwap_redirect_link = get_permalink($rtwwwap_affiliate_page_id);
				              
                wp_redirect( $rtwwwap_redirect_link);

            } ?>
        </footer>
  