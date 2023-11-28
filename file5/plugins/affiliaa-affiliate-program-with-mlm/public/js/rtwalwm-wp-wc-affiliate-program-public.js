(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */
    $(function(){
        $(document).find( '.rtwalwm_select_cat' ).select2({ width: '50%' });
        $(document).find( '.rtwalwm_payment_method' ).select2({ width: '40%' });

        $(document).find( '#rtwalwm_coupons_table, #rtwalwm_referrals_table' ).DataTable({
            "pageLength": 5,
            "lengthMenu": [ [5, 10, 25, 50, -1], [5, 10, 25, 100, "All"] ],
            "searching" : false
        });

        $(document).find( '#rtwalwm_requests_table' ).DataTable({
            "pageLength": 5,
            "lengthMenu": [ [5, 10, 25, 50, -1], [5, 10, 25, 100, "All"] ],
            "searching" : false
        });

    	$(document).on( 'click', '#rtwalwm_affiliate_activate', function(){
			
    		var rtwalwm_user_id = $(this).data( 'rtwalwm_num' );

    		var rtwalwm_data = {
     			action 	               : 'rtwalwm_become_affiliate',
     			rtwalwm_user_id        : rtwalwm_user_id,
     			rtwalwm_security_check : rtwalwm_global_params.rtwalwm_nonce
     		};

            $.blockUI({ message: '' });
    		$.ajax({
     			url 		: rtwalwm_global_params.rtwalwm_ajaxurl,
     			type 		: "POST",
     			data 		: rtwalwm_data,
     			dataType 	: 'json',
     			success 	: function(response)
     			{
     				if( response.rtwalwm_status ){
     					alert( response.rtwalwm_message );
                        window.location.reload();
     				}
                    else{
                        alert( response.rtwalwm_message );
                        window.location.reload();
                    }
                    $.unblockUI();
     			}
     		});
     	});

     	$(document).on( 'click', '#rtwalwm_generate_button', function(){
        	var rtwalwm_url = $(document).find( '#rtwalwm_aff_link_input' ).val();

            if( rtwalwm_url != '' && rtwalwm_url.startsWith( rtwalwm_global_params.rtwalwm_home_url ) ){
    	    	var rtwalwm_aff_id        = $(this).data( 'rtwalwm_aff_id' );
    	    	var rtwalwm_aff_name      = $(this).data( 'rtwalwm_aff_name' );
    	    	var rtwalwm_generated_url = '';
                var rtwalwm_generated_url_share = '';

    	    	if( rtwalwm_url.indexOf( '?' ) > 0 ){
    	    		rtwalwm_generated_url        = rtwalwm_url+'&rtwalwm_aff='+rtwalwm_aff_name+'_'+rtwalwm_aff_id;
                    rtwalwm_generated_url_share  = rtwalwm_url+'&rtwalwm_aff='+rtwalwm_aff_name+'_'+rtwalwm_aff_id+'_share';
    	    	}
    	    	else
				{
    	    		rtwalwm_generated_url        = rtwalwm_url+'?rtwalwm_aff='+rtwalwm_aff_name+'_'+rtwalwm_aff_id;
                    rtwalwm_generated_url_share  = rtwalwm_url+'?rtwalwm_aff='+rtwalwm_aff_name+'_'+rtwalwm_aff_id+'_share';
    	    	}

    	    	$(document).find( '#rtwalwm_generated_link' ).text( rtwalwm_generated_url ).css({ 'visibility' : 'visible' });
    	    	$(document).find( '#rtwalwm_copy_to_clip, #rtwalwm_generate_qr, .rtwalwm_download_qr' ).css({ 'visibility' : 'visible' });

                $(document).find( '.rtwalwm_social_share' ).css( 'display', 'flex' );

                $(document).find( '#rtwalwm_qrcode_main' ).hide();
            }
            else{
                alert( rtwalwm_global_params.rtwalwm_enter_valid_url )
            }
        });

        $(document).on( 'click', '#rtwalwm_copy_to_clip', function(){
        	var $rtwalwm_temp = $("<input>");
    	  	$( "body" ).append( $rtwalwm_temp );
    	  	$rtwalwm_temp.val( $( '#rtwalwm_generated_link' ).text() ).select();
    	  	document.execCommand( "copy" );
      		$rtwalwm_temp.remove();

            $(document).find( '#rtwalwm_copy_tooltip_link' ).css( { 'visibility' : 'visible', 'opacity' : 1  } );
            setTimeout( function(){
                $(document).find( '#rtwalwm_copy_tooltip_link' ).css( { 'visibility' : 'hidden', 'opacity' : 0  } );
            }, 2000 );
        });

        $(document).on( 'click', '#rtwalwm_copy_to_clip_mlm', function(){
            var $rtwalwm_temp = $("<input>");
            $( "body" ).append( $rtwalwm_temp );
            $rtwalwm_temp.val( $( '#rtwalwm_aff_link_input' ).val() ).select();
            document.execCommand( "copy" );
            $rtwalwm_temp.remove();

            $(document).find( '#rtwalwm_copy_tooltip_link' ).css( { 'visibility' : 'visible', 'opacity' : 1  } );
            setTimeout( function(){
                $(document).find( '#rtwalwm_copy_tooltip_link' ).css( { 'visibility' : 'hidden', 'opacity' : 0  } );
            }, 2000 );
        });

        $(document).on( 'click', '#rtwalwm_search_button', function(){
        	var rtwalwm_prod_name  = $(document).find( '#rtwalwm_banner_prod_search' ).val();
        	var rtwalwm_cat_id 	   = $(document).find( '.rtwalwm_select_cat' ).val();

        	var rtwalwm_data = {
     			action                  : 'rtwalwm_search_prod',
     			rtwalwm_prod_name       : rtwalwm_prod_name,
     			rtwalwm_cat_id          : rtwalwm_cat_id,
     			rtwalwm_security_check  : rtwalwm_global_params.rtwalwm_nonce
     		};

            $.blockUI({ message: '' });
    		$.ajax({
     			url 		: rtwalwm_global_params.rtwalwm_ajaxurl,
     			type 		: "POST",
     			data 		: rtwalwm_data,
     			dataType 	: 'json',
     			success 	: function(response)
     			{
     				if( response.rtwalwm_products == '' ){
     					alert( response.rtwalwm_message );
     				}
     				else{
     					$(document).find( '#rtwalwm_search_main_container' ).html('');
     					$(document).find( '#rtwalwm_search_main_container' ).append( response.rtwalwm_products );
     				}
                    $.unblockUI();
     			}
     		});
		});
		$(document).on( 'click', '.rtwalwm_custom_banner_copy_html', function(){
            $(this).parent('.rtwalwm_custom_banner_product').find(".rtwalwm_banner_copy_text").fadeIn(800).delay(500).fadeOut(800);
    		var rtwalwm_html = '';
            var rtwalwm_image_url = $(this).data( 'image_id' );
            var rtwalwm_target_link = $(this).data( 'target_link' );
            var rtwalwm_image_width = $(this).data( 'image_width' );
            var rtwalwm_image_height = $(this).data( 'image_height' );
            rtwalwm_html += '<a hreff="'+rtwalwm_target_link+'" style="width:'+rtwalwm_image_width+';height:'+rtwalwm_image_height+'">' ;
            rtwalwm_html +=      '<img src="'+rtwalwm_image_url+'" style="height:100%; width:100%">';
            rtwalwm_html += '</a>';
            var $rtwalwm_temp = $( "<input>" );
            $( "body" ).append( $rtwalwm_temp );
            $rtwalwm_temp.val( rtwalwm_html ).select();
            document.execCommand( "copy" );
            $rtwalwm_temp.remove();
            
           
    	});
     
    });

  



})( jQuery );
