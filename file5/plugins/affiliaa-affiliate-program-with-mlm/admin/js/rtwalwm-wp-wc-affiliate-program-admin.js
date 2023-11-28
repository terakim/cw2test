(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
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

		 $(document).ready(function(){

			// $(".rtw_close_popup").on("click",function(){
			// 	$(".rtw_popup").hide();
			// });
	
			 $('.rtwalwm-extra-features-wrap > ul >li').on('click', function(){
				 $('.rtwalwm-extra-features-wrap > ul >li').removeClass('active');
				 $(this).addClass('active');
				 $('.rtwalwm-extra-table-wrapper > table').removeClass('rtwalwm-show');
				 $('.rtwalwm-extra-table-wrapper > table').addClass('rtwalwm-hide-table');
				 $('#'+$(this).attr('data-target')).removeClass('rtwalwm-hide-table');
				 $('#'+$(this).attr('data-target')).addClass('rtwalwm-show');
			 });

			 $(document).on('click','.rtwalwm-manual-referral', function(){
				 $('.rtwalwm-popup-wrapper').addClass('show');
			 });
			 $(document).on('click','.rtwalwm_manual_add_message', function(){
				 $('.rtwalwm-popup-wrapper').removeClass('show');
			 });
			 $(document).on('click','.rtwalwm_add_notification', function(){
				$('.rtwalwm-popup-wrapper').addClass('show');
			});
			

			 $('#rtwalwm-manual-aff-id').select2();
		 });


	




		 $(document).ready(function(){

			$('.rtwalwm-extra-features-wrap > ul >li').on('click', function(){
				$('.rtwalwm-extra-features-wrap > ul >li').removeClass('active');
				$(this).addClass('active');
				$('.rtwalwm-extra-table-wrapper > table').removeClass('rtwalwm-show');
				$('.rtwalwm-extra-table-wrapper > table').addClass('rtwalwm-hide-table');
				$('#'+$(this).attr('data-target')).removeClass('rtwalwm-hide-table');
				$('#'+$(this).attr('data-target')).addClass('rtwalwm-show');
			});

			$(document).on('click','.rtwalwm-button-reset', function(){
				$('.rtwalwm-popup-wrapper').removeClass('show');
			});
			$('#rtwalwm-manual-aff-id').select2();
		});

		
        $(document).on( 'change', '.rtwwwap_select2_mlm', function(){

        	if( $(document).find( '.rtwwwap_select2_mlm' ).val() == 0 ){
        		$(document).find( '#rtwwwap_mlm_child' ).attr( 'max', 2 );
        		$(document).find( '#rtwwwap_mlm_child' ).closest('tr').show();
        		$(document).find( '#rtwwwap_mlm_child' ).removeAttr( 'disabled', 'disabled' );
        		$(document).find( '#rtwwwap_mlm_child' ).val( 2 );
        	}
        	else if( $(document).find( '.rtwwwap_select2_mlm' ).val() == 1 ){
        		$(document).find( '#rtwwwap_mlm_child' ).removeAttr( 'max' );
        		$(document).find( '#rtwwwap_mlm_child' ).closest('tr').show();
        		$(document).find( '#rtwwwap_mlm_child' ).removeAttr( 'disabled', 'disabled' );
			}
			else if( $(document).find( '.rtwwwap_select2_mlm' ).val() == 3 ){
        		$(document).find( '#rtwwwap_mlm_child' ).removeAttr( 'max' );
        		$(document).find( '#rtwwwap_mlm_child' ).closest('tr').show();
        		$(document).find( '#rtwwwap_mlm_child' ).removeAttr( 'disabled', 'disabled' );
			}
			
        	else if( $(document).find( '.rtwwwap_select2_mlm' ).val() == 2 ){
        		$(document).find( '#rtwwwap_mlm_child' ).closest('tr').hide();
        		$(document).find( '#rtwwwap_mlm_child' ).attr( 'disabled', 'disabled' );
			}
			
        });

		$(document).find( ".rtwwwap_select2_mlm" ).select2({ width : '30%' });
		$(document).find( ".rtwwwap_select2_mlm_default_comm" ).select2({ width : '60%' });
		$(document).find( ".rtwwwap_select2_mlm_level_comm_type" ).select2({ width : '60%' });



        var rtwwwap_mlm_depth_reset = $(document).find( '.rtwwwap_mlm_depth' ).data( 'rtwwwap_depth' );
        $(document).find( '.rtwwwap_mlm_depth' ).val( rtwwwap_mlm_depth_reset );

        $(document).on( 'keydown', '.rtwwwap_mlm_depth', function(e){
        	e.preventDefault();
        });

        $(document).on( 'change', '.rtwwwap_mlm_depth', function(){
        	var rtwwwap_new_depth = $(this).val();
        	var rtwwwap_old_depth = $(this).data( 'rtwwwap_depth' );

        	if( rtwwwap_new_depth > rtwwwap_old_depth && rtwwwap_new_depth <= 3  )
        	{
        		$(this).data( 'rtwwwap_depth', rtwwwap_new_depth );
        		var rtwwwap_rowCount = $( '.rtwwwap_tbody tr' ).length;
        		var rtwwwap_cloned 	= $(document).find( '.rtwwwap_add_new_row_hidden' ).clone();
        		$( rtwwwap_cloned ).insertAfter( $( document ).find( '.rtwwwap_tbody tr:last-child' ).last() );
        		$(document).find( '.rtwwwap_tbody tr:last-child' ).removeClass( 'rtwwwap_add_new_row_hidden' );
        		$(document).find( '.rtwwwap_tbody tr:last-child' ).find( '.rtwwwap_select2_mlm_level_comm_type_hidden' ).removeClass( 'rtwwwap_select2_mlm_level_comm_type_hidden' ).addClass( 'rtwwwap_select2_mlm_level_comm_type' );

        		var rtwwwap_new_row_td_name = 'rtwwwap_mlm_opt[mlm_levels][ '+rtwwwap_rowCount+' ][mlm_level_id]';
        		var rtwwwap_new_row_select2_name = 'rtwwwap_mlm_opt[mlm_levels][ '+rtwwwap_rowCount+' ][mlm_level_comm_type]';
        		var rtwwwap_new_row_comm_name 	= 'rtwwwap_mlm_opt[mlm_levels][ '+rtwwwap_rowCount+' ][mlm_level_comm_amount]';

        		$(document).find( '.rtwwwap_tbody tr:last-child' ).find( 'td:eq(0)' ).attr( 'name', rtwwwap_new_row_td_name );
        		$(document).find( '.rtwwwap_tbody tr:last-child' ).find( 'td:eq(0)' ).html( rtwwwap_rowCount );

        		$(document).find( '.rtwwwap_tbody tr:last-child' ).find( 'td:eq(1) > .rtwwwap_select2_mlm_level_comm_type' ).attr( 'name', rtwwwap_new_row_select2_name );

        		$(document).find( '.rtwwwap_tbody tr:last-child' ).find( 'td:eq(2) > .rtwwwap_mlm_level_comm_amount' ).attr( 'name', rtwwwap_new_row_comm_name );
        		var rtwwwap_default_comm_val 	= $(document).find( '.rtwwwap_mlm_default_comm_amount' ).val();
        		var rtwwwap_default_comm_type 	= $(document).find( '.rtwwwap_select2_mlm_default_comm' ).val();

        		$(document).find( '.rtwwwap_tbody tr:last-child' ).find( 'td:eq(2) > .rtwwwap_mlm_level_comm_amount' ).val( rtwwwap_default_comm_val );
        		$(document).find( '.rtwwwap_tbody tr:last-child' ).find( 'td:eq(1) > .rtwwwap_select2_mlm_level_comm_type' ).val( rtwwwap_default_comm_type );
        		$(document).find( '.rtwwwap_tbody tr:last-child' ).find( '.rtwwwap_select2_mlm_level_comm_type' ).select2({ width : '60%' });

        		$(document).find( '.rtwwwap_tbody tr:last-child' ).show();
        	}
        	else if( rtwwwap_old_depth > rtwwwap_new_depth )
        	{
        		$(this).data( 'rtwwwap_depth', rtwwwap_new_depth );
        		$(document).find( '.rtwwwap_tbody tr:last-child' ).remove();
        	}
        });


		$(document).on('click','.rtwwwap-faq-heading' ,function(){
	 		
			if ($(this).next('.rtwwwap-faq-desc').is(':hidden')){
				$('.rtwwwap-faq-heading').removeClass('active');
				$('.rtwwwap-faq-desc').slideUp("3000");
				$(this).addClass('active');
				$(this).next('.rtwwwap-faq-desc').slideToggle("3000");
			}
			else{
				$('.rtwwwap-faq-heading').removeClass('active');
				$('.rtwwwap-faq-desc').slideUp("3000");
			}

		});

		$(document).on('click','#rtwwwap_read_more_btn' ,function(){
	 		
			$('#rtwwwap-faq-more-content').slideDown("3000");
			$(this).parent().slideUp("3000");
		});






		// License
		$(document).find('.rtwalwm_notice_error').addClass('rtwalwm_hide');
		var rules = {
		    rtwalwm_purchase_code 	: { required: true }
		};

		var messages = {
		    rtwalwm_purchase_code 	: { required: 'Required' }
		};

		$(document).find( "#rtwalwm_verify" ).validate({
		    rules: rules,
		    messages: messages
		});

	
		

	 	$(document).find( ".rtwalwm_select2" ).select2();
	 	$(document).find( ".rtwalwm_select2_all" ).select2({ width : '40%' });
	 	$(document).find( ".rtwalwm_select2_level" ).select2({ width : '60%' });
	 	$(document).find( ".rtwalwm_select2_level_criteria" ).select2({ width : '60%' });
	 	$(document).find( ".rtwalwm_select2_page" ).select2({ width : '20%' });
	 	$(document).find( ".rtwalwm_select2_curr" ).select2({ width : '30%' });
	 	$(document).find( ".rtwalwm_select2_sharing_bonus_time_limit" ).select2({ width : '40%' });

	 	$(document).on( 'hover', '.select2-selection__rendered', function(){
	 		$(this).removeAttr( "title" );
	 	});

		//button css start
		$(document).find( '#rtwalwm_buttonPicker' ).iris({
			defaultColor  : true,
			clear         : function() {},
			hide          : true,
			palettes      : true,
			width         : 400,
			change        : function( event, ui ) {
				$(document).find( "#rtwalwm_buttonPicker" ).css( 'background', ui.color.toString());
				$(document).find( "#rtwalwm_buttonPicker" ).css( 'color', ui.color.toString());
				$(this).siblings( '.rtwalwm_button_color' ).html( ui.color.toString());
			}
		});

		var rtwalwm_saved_button_color = $(document).find( '#rtwalwm_buttonPicker' ).val();
		if( rtwalwm_saved_button_color != '' ){
			$(document).find( '#rtwalwm_buttonPicker' ).iris( 'color', rtwalwm_saved_button_color );
		}
		else{
			$(document).find( '#rtwalwm_buttonPicker' ).iris( 'color', '#DADAF2' );
		}
        //button css end

        //form bg css start
        $(document).find( '#rtwalwm_bgPicker' ).iris({
        	defaultColor  : true,
        	clear         : function() {},
        	hide          : true,
        	palettes      : true,
        	width         : 400,
        	change        : function( event, ui ) {
        		$(document).find( "#rtwalwm_bgPicker" ).css( 'background', ui.color.toString());
        		$(document).find( "#rtwalwm_bgPicker" ).css( 'color', ui.color.toString());
        		$(this).siblings( '.rtwalwm_bg_color' ).html( ui.color.toString());
        	}
        });

        var rtwalwm_saved_bg_color = $(document).find( '#rtwalwm_bgPicker' ).val();
        if( rtwalwm_saved_bg_color != '' ){
        	$(document).find( '#rtwalwm_bgPicker' ).iris( 'color', rtwalwm_saved_bg_color );
        }
        else{
        	$(document).find( '#rtwalwm_bgPicker' ).iris( 'color', '#DADAF2' );
        }
        //form bg css end

        //main bg css start
        $(document).find( '#rtwalwm_mainbgPicker' ).iris({
        	defaultColor  : true,
        	clear         : function() {},
        	hide          : true,
        	palettes      : true,
        	width         : 400,
        	change        : function( event, ui ) {
        		$(document).find( "#rtwalwm_mainbgPicker" ).css( 'background', ui.color.toString());
        		$(document).find( "#rtwalwm_mainbgPicker" ).css( 'color', ui.color.toString());
        		$(this).siblings( '.rtwalwm_mainbg_color' ).html( ui.color.toString());
        	}
        });

        var rtwalwm_saved_bg_color = $(document).find( '#rtwalwm_mainbgPicker' ).val();
        if( rtwalwm_saved_bg_color != '' ){
        	$(document).find( '#rtwalwm_mainbgPicker' ).iris( 'color', rtwalwm_saved_bg_color );
        }
        else{
        	$(document).find( '#rtwalwm_mainbgPicker' ).iris( 'color', '#DADAF2' );
        }
        //main bg css end

        //header css start
        $(document).find( '#rtwalwm_headPicker' ).iris({
        	defaultColor  : true,
        	clear         : function() {},
        	hide          : true,
        	palettes      : true,
        	width         : 400,
        	change        : function( event, ui ) {
        		$(document).find( "#rtwalwm_headPicker" ).css( 'background', ui.color.toString());
        		$(document).find( "#rtwalwm_headPicker" ).css( 'color', ui.color.toString());
        		$(this).siblings( '.rtwalwm_head_color' ).html( ui.color.toString());
        	}
        });

        var rtwalwm_saved_bg_color = $(document).find( '#rtwalwm_headPicker' ).val();
        if( rtwalwm_saved_bg_color != '' ){
        	$(document).find( '#rtwalwm_headPicker' ).iris( 'color', rtwalwm_saved_bg_color );
        }
        else{
        	$(document).find( '#rtwalwm_headPicker' ).iris( 'color', '#DADAF2' );
        }
        //header css end

		//show hide color picker on click start
		$(document).on( 'click', '#rtwalwm_bgPicker, #rtwalwm_buttonPicker, #rtwalwm_mainbgPicker, #rtwalwm_headPicker', function (event) {
			$(this).iris('hide');
			$(this).iris('show');
			return false;
		});

		$(document).on( 'click', 'body', function (e) {
			if ( !$(e.target).is( "#rtwalwm_bgPicker, #rtwalwm_buttonPicker, #rtwalwm_mainbgPicker, #rtwalwm_headPicker" ) )
			{
				if( $(document).find( '#rtwalwm_bgPicker' ).siblings( '.iris-picker' ).css( 'display' ) == 'block' || $(document).find( '#rtwalwm_buttonPicker' ).siblings( '.iris-picker' ).css( 'display' ) == 'block' || $(document).find( '#rtwalwm_mainbgPicker' ).siblings( '.iris-picker' ).css( 'display' ) == 'block' || $(document).find( '#rtwalwm_headPicker' ).siblings( '.iris-picker' ).css( 'display' ) == 'block' )
				{
					$( '#rtwalwm_bgPicker, #rtwalwm_buttonPicker, #rtwalwm_mainbgPicker, #rtwalwm_headPicker' ).iris( 'hide' );
					return false;
				}
			}
		});
    
        $(document).find( '.rtwalwm_payout_table' ).DataTable({
        	responsive: false,
        	"order" : [],
        	"columnDefs": [
        	{ orderable: false, targets: [0] },
        	{ "width": "5%", "targets": 0 },
        	{ "width": "10%", "targets": 1 },
        	{ "width": "15%", "targets": 2 },
        	{ "width": "15%", "targets": 3 },
        	{ "width": "10%", "targets": 4 },
        	{ "width": "20%", "targets": 5 },
        	{ "width": "25%", "targets": 6 }
        	],
		});
	
		
	        if( $(document).find( '.rtwalwm_referral_table' ).length != 0 ){
				var rtwalwm_referrals_table_length = $(document).find( '.rtwalwm_referral_table > thead > tr' )[0].cells.length;
	
				if( rtwalwm_referrals_table_length == 8 ){
					$(document).find( '.rtwalwm_referral_table').DataTable({
						responsive: false,
						"order" : [],
						"columnDefs": [
						{ orderable: false, targets: [0,7] },
						{ "width": "5%", "targets": 0 },
						{ "width": "10%", "targets": 1 },
						{ "width": "10%", "targets": 2 },
						{ "width": "10%", "targets": 3 },
						{ "width": "10%", "targets": 4 },
						{ "width": "20%", "targets": 5 },
						{ "width": "20%", "targets": 6 },
						{ "width": "15%", "targets": 7 },
						],
						});
				
				}
				else if( rtwalwm_referrals_table_length == 9 ){
					$(document).find( '.rtwalwm_referral_table').DataTable({
						responsive: false,
						"order" : [],
						"columnDefs": [
						{ orderable: false, targets: [0,8] },
						{ "width": "5%", "targets": 0 },
						{ "width": "10%", "targets": 1 },
						{ "width": "10%", "targets": 2 },
						{ "width": "10%", "targets": 3 },
						{ "width": "10%", "targets": 4 },
						{ "width": "10%", "targets": 5 },
						{ "width": "20%", "targets": 6 },
						{ "width": "20%", "targets": 7 },
						{ "width": "15%", "targets": 8 },
						],
						});
				}
			}
	
		
			

        if( $(document).find( '.rtwalwm_affiliates_table' ).length != 0 ){
        	var rtwalwm_affiliate_table_length = $(document).find( '.rtwalwm_affiliates_table > thead > tr' )[0].cells.length;

        	if( rtwalwm_affiliate_table_length == 6 ){
        		$(document).find( '.rtwalwm_affiliates_table' ).DataTable({
        			responsive: false,
        			"order" : [],
        			"columnDefs": [
        			{ orderable: false, targets: [ 0, 5 ] },
        			{ "width": "5%", "targets": 0 },
        			{ "width": "10%", "targets": 1 },
        			{ "width": "20%", "targets": 2 },
        			{ "width": "20%", "targets": 3 },
        			{ "width": "25%", "targets": 4 },
        			{ "width": "20%", "targets": 5 }
        			]
        		});
        	}
        	else if( rtwalwm_affiliate_table_length == 7 ){
        		$(document).find( '.rtwalwm_affiliates_table' ).DataTable({
        			responsive: false,
        			"order" : [],
        			"columnDefs": [
        			{ orderable: false, targets: [ 0, 6 ] },
        			{ "width": "5%", "targets": 0 },
        			{ "width": "5%", "targets": 1 },
        			{ "width": "20%", "targets": 2 },
        			{ "width": "20%", "targets": 3 },
        			{ "width": "20%", "targets": 4 },
        			{ "width": "20%", "targets": 5 },
        			{ "width": "10%", "targets": 6 }
        			]
        		});
        	}
        }

        $(document).find( '.rtwalwm_levels_table' ).DataTable({
        	responsive: false,
        	rowReorder: true,
        	"columnDefs": [
        	{ orderable: false, targets: [ 1, 2, 3, 4, 5 ] },
        	{ "width": "10%", "targets": 0 },
        	{ "width": "10%", "targets": 1 },
        	{ "width": "25%", "targets": 2 },
        	{ "width": "15%", "targets": 3 },
        	{ "width": "25%", "targets": 4 },
        	{ "width": "15%", "targets": 5 }
        	]
        });

        $(document).on( 'change', '#rtwalwm_affiliate', function(){
        	var rtwalwm_user_id 	= $(this).parent().data( 'rtwalwm-num' );
        	var rtwalwm_value 		= $(this).prop( 'checked' );

        	var rtwalwm_data = {
        		action 					: 'rtwalwm_change_affiliate',
        		rtwalwm_user_id 		: rtwalwm_user_id,
        		rtwalwm_value 			: (rtwalwm_value) ? 1 : 0,
        		rtwalwm_security_check	: rtwalwm_global_params.rtwalwm_nonce
        	};

        	$.blockUI({ message: '' });
        	if( rtwalwm_value ){
        		$(this).closest('tr').find( '.rtwalwm_aff_level_hidden' ).show();
        	}
        	else{
        		$(this).closest('tr').find( '.rtwalwm_aff_level_hidden' ).hide();
        	}
        	$.ajax({
        		url 		: rtwalwm_global_params.rtwalwm_ajaxurl,
        		type 		: "POST",
        		data 		: rtwalwm_data,
        		dataType 	: 'json',
        		success 	: function(response)
        		{
        			if( response.rtwalwm_status ){
        				var html = '<div id="message" class="updated notice is-dismissible rtwalwm_affiliate_notice"><p>'+response.rtwalwm_message+'.</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';

        				if( $(document).find( '.rtwalwm_affiliate_notice' ).length ){
        					$(document).find( '.rtwalwm_affiliate_notice p' ).text( response.rtwalwm_message );
        				}
        				else{
        					$(document).find( '.wp-header-end' ).after( html );
        				}

        				$('html, body').animate({
        					scrollTop: $("body").offset().top
        				}, 500, "linear", function(){
        				});
        			}
        			else{

        			}
        			$.unblockUI();
        		}
        	});
        });

        $(document).on( 'click', '.rtwalwm_affiliate_notice', function(){
        	$(this).remove();
        });

        $(document).on( 'keypress', '.rtwalwm_perc_commission_box, .rtwalwm_fix_commission_box', function (e) {
        	if( e.which != 8 && e.which != 0 && ( e.which < 48 || e.which > 57 ) ){
        		alert( rtwalwm_global_params.rtwalwm_digit );
        		return false;
        	}
        });

        $(document).on( 'blur', ".rtwalwm_perc_commission_box", function (e) {

			var $rtwalwm_this 			= $(this);
        	var rtwalwm_check_if_same 	= $(this).prop( "defaultValue" );

        	if( rtwalwm_check_if_same != $(this).val() ){
        		var rtwalwm_post_id 	= $(this).data( 'rtwalwm-num' );
        		var rtwalwm_value 		= $(this).val();
				var rtwalwm_type 		= 'perc_comm';
		

        		var rtwalwm_data = {
        			action 					: 'rtwalwm_change_prod_commission',
        			rtwalwm_post_id 		: rtwalwm_post_id,
        			rtwalwm_value 			: rtwalwm_value,
        			rtwalwm_type 			: rtwalwm_type,
        			rtwalwm_security_check	: rtwalwm_global_params.rtwalwm_nonce
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
        					$rtwalwm_this.prop( 'defaultValue', $rtwalwm_this.val() );
        				}
        				alert( response.rtwalwm_message );
        				$.unblockUI();
        			}
        		});
        	}
        });

        $(document).on( 'blur', ".rtwalwm_fix_commission_box", function (e) {
        	var $rtwalwm_this 			= $(this);
        	var rtwalwm_check_if_same 	= $(this).prop( "defaultValue" );

        	if( rtwalwm_check_if_same != $(this).val() ){
        		var rtwalwm_post_id 	= $(this).data( 'rtwalwm-num' );
        		var rtwalwm_value 		= $(this).val();
        		var rtwalwm_type 		= 'fix_comm';

        		var rtwalwm_data = {
        			action 					: 'rtwalwm_change_prod_commission',
        			rtwalwm_post_id 		: rtwalwm_post_id,
        			rtwalwm_value 			: rtwalwm_value,
        			rtwalwm_type 			: rtwalwm_type,
        			rtwalwm_security_check	: rtwalwm_global_params.rtwalwm_nonce
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
        					$rtwalwm_this.prop( 'defaultValue', $rtwalwm_this.val() );
        				}
        				alert( response.rtwalwm_message );
        				$.unblockUI();
        			}
        		});
        	}
        });

       


        $(document).on( 'click', ".rtwalwm_coupons", function (e){
        	if( $(this).val() == 1 ){
        		$(document).find( '#rtwalwm_min_amount' ).show();
        	}
        
        });

        $(document).on( 'click', '.rtwalwm_referrals_check_all', function(){
        	if( $(this).is( ':checked' ) ){
        		$(document).find( '.rtwalwm_referral_table > tbody  > tr' ).each( function()
        		{
        			if( $(this).find( 'input:checkbox' ).length == 1 ){
        				$(this).find( 'input:checkbox' ).prop( 'checked', true );
        			}
        		});
        	}
        	else{
        		$(document).find( '.rtwalwm_referral_table > tbody > tr' ).each( function()
        		{
        			if( $(this).find( 'input:checkbox' ).length == 1 ){
        				$(this).find( 'input:checkbox' ).prop( 'checked', false );
        			}
        		});
        	}
        });

        $(document).on( 'click', ".rtwalwm_approve", function () {
        	if( confirm( rtwalwm_global_params.rtwalwm_approval_sure ) ){
        		var $this 					= $(this);
				var rtwalwm_referral_ids 	= [];
				

				rtwalwm_referral_ids.push( $(this).closest( 'tr' ).data( 'referral_id' ) );
		

        		var data = {
        			action 					: 'rtwalwm_approve',
        			rtwalwm_security_check	: rtwalwm_global_params.rtwalwm_nonce,
        			rtwalwm_referral_ids	: rtwalwm_referral_ids,
        		};

        		$.blockUI({ message: '' });
        		$.ajax({
        			url 		: rtwalwm_global_params.rtwalwm_ajaxurl,
        			type 		: "POST",
        			data 		: data,
        			dataType 	: 'json',
        			success 	: function(response)
        			{
        				if( response.rtwalwm_status ){
        					$this.closest( 'td' ).find( '.rtwalwm_reject' ).remove();
        					$this.html( response.rtwalwm_message );
        					$this.removeClass( 'rtwalwm_approve' ).addClass( 'rtwalwm_approved' );
        					$this.closest('tr').find( '.rtwalwm-checkbox' ).remove();
        				}
        				else{
        					alert( response.rtwalwm_message );
        				}
        				$.unblockUI();
        			}
        		});
        	}
        });

        $(document).on( 'click', ".rtwalwm_approve_all_referrals", function () {
        	if( confirm( rtwalwm_global_params.rtwalwm_approval_sure_all ) ){
        		var rtwalwm_referral_ids 	= [];
        		var rtwalwm_all_checked 	= 0;
        		var rtwalwm_already_approved_capped = 0;
        		$(document).find( '.rtwalwm_referral_table > tbody > tr' ).each( function()
        		{
        			if( $(this).find( 'input:checkbox' ).length == 1 ){
        				if( $(this).find( 'input:checkbox' ).is( ':checked' ) ){
        					rtwalwm_all_checked++;
        					if( $(this).find( '.rtwalwm_approve' ).length == 0 ){
        						rtwalwm_already_approved_capped++;
        					}
        					else{
        						rtwalwm_referral_ids.push( $(this).data( 'referral_id' ) );
        					}
        				}
        			}
        		});

        		if( rtwalwm_all_checked == rtwalwm_already_approved_capped ){
        			alert( 'rtwalwm_global_params.rtwalwm_nothing_marked' );
        			return;
        		}

        		if( rtwalwm_referral_ids.length > 0 ){
        			var data = {
        				action 					: 'rtwalwm_approve',
        				rtwalwm_security_check	: rtwalwm_global_params.rtwalwm_nonce,
        				rtwalwm_referral_ids	: rtwalwm_referral_ids,
        			};

        			$.blockUI({ message: '' });
        			$.ajax({
        				url 		: rtwalwm_global_params.rtwalwm_ajaxurl,
        				type 		: "POST",
        				data 		: data,
        				dataType 	: 'json',
        				success 	: function(response)
        				{
        					alert( response.rtwalwm_message );
        					window.location.reload();
        					$.unblockUI();
        				}
        			});
        		}
        		else{
        			alert( rtwalwm_global_params.rtwalwm_nothing_marked );
        		}
        	}
		});
		
		
	
	

		$(document).on( 'click', ".rtwalwm_reject", function () {
        	if( confirm( rtwalwm_global_params.rtwalwm_reject_sure ) ){
        		var $this 					= $(this);
        		var rtwalwm_referral_ids 	= [];

        		rtwalwm_referral_ids.push( $(this).closest( 'tr' ).data( 'referral_id' ) );

        		var data = {
        			action 					: 'rtwalwm_reject',
        			rtwalwm_security_check	: rtwalwm_global_params.rtwalwm_nonce,
        			rtwalwm_referral_ids	: rtwalwm_referral_ids,
        		};

        		$.blockUI({ message: '' });
        		$.ajax({
        			url 		: rtwalwm_global_params.rtwalwm_ajaxurl,
        			type 		: "POST",
        			data 		: data,
        			dataType 	: 'json',
        			success 	: function(response)
        			{
        				if( response.rtwalwm_status ){
        					$this.closest( 'td' ).html( '<span class="rtwalwm_rejected">'+response.rtwalwm_message+'</span>' );
        				}
        				else {
        					alert( response.rtwalwm_message );
        				}
        				$.unblockUI();
        			}
        		});
        	}
        });

	
        $(document).on( 'click', ".rtwalwm_reject_all_referrals", function () {
        	if( confirm( rtwalwm_global_params.rtwalwm_reject_sure_all ) ){
        		var rtwalwm_referral_ids = [];
        		$(document).find( '.rtwalwm_referral_table > tbody > tr' ).each( function()
        		{
        			if( $(this).find( 'input:checkbox' ).length == 1 ){
        				if( $(this).find( 'input:checkbox' ).is( ':checked' ) ){
        					rtwalwm_referral_ids.push( $(this).data( 'referral_id' ) );
        				}
        			}
        		});

        		if( rtwalwm_referral_ids.length > 0 ){
        			var data = {
        				action 					: 'rtwalwm_reject',
        				rtwalwm_security_check	: rtwalwm_global_params.rtwalwm_nonce,
        				rtwalwm_referral_ids	: rtwalwm_referral_ids,
        			};

        			$.blockUI({ message: '' });
        			$.ajax({
        				url 		: rtwalwm_global_params.rtwalwm_ajaxurl,
        				type 		: "POST",
        				data 		: data,
        				dataType 	: 'json',
        				success 	: function(response)
        				{
        					alert( response.rtwalwm_message );
        					window.location.reload();
        					$.unblockUI();
        				}
        			});
        		}
        		else{
        			alert( rtwalwm_global_params.rtwalwm_nothing_marked );
        		}
        	}
        });

    
       


        $(document).on( 'click', '.rtwalwm_affiliate_check_all', function(){
        	if( $(this).is( ':checked' ) ){
        		$(document).find( '.rtwalwm_affiliates_table > tbody  > tr' ).each( function()
        		{
        			if( $(this).find( 'input:checkbox' ).length == 1 ){
        				$(this).find( 'input:checkbox' ).prop( 'checked', true );
        			}
        		});
        	}
        	else{
        		$(document).find( '.rtwalwm_affiliates_table > tbody > tr' ).each( function()
        		{
        			if( $(this).find( 'input:checkbox' ).length == 1 ){
        				$(this).find( 'input:checkbox' ).prop( 'checked', false );
        			}
        		});
        	}
        });

        $(document).on( 'click', ".rtwalwm_aff_approve", function (e) {
        	var $this = $(this);
        	var rtwalwm_referral_ids = [];

        	rtwalwm_referral_ids.push( $(this).closest( 'tr' ).data( 'referral_id' ) );

        	var data = {
        		action 					: 'rtwalwm_aff_approve',
        		rtwalwm_security_check	: rtwalwm_global_params.rtwalwm_nonce,
        		rtwalwm_referral_ids	: rtwalwm_referral_ids,
        	};

        	$.blockUI({ message: '' });
        	$.ajax({
        		url 		: rtwalwm_global_params.rtwalwm_ajaxurl,
        		type 		: "POST",
        		data 		: data,
        		dataType 	: 'json',
        		success 	: function(response)
        		{
        			if( response.rtwalwm_status ){
        				alert( response.rtwalwm_message );
        				$this.closest( 'tr' ).find( '.rtwalwm-checkbox' ).remove();
        				$this.removeClass( 'rtwalwm_aff_approve' ).addClass( 'rtwalwm_aff_approved' );
        			}
        			else {
        				alert( response.rtwalwm_message );
        			}
        			$.unblockUI();
        		}
        	});
        });

        $(document).on( 'click', ".rtwalwm_approve_all_affiliate", function () {
        	var rtwalwm_referral_ids = [];
        	$(document).find( '.rtwalwm_affiliates_table > tbody > tr' ).each( function()
        	{
        		if( $(this).find( 'input:checkbox' ).length == 1 ){
        			if( $(this).find( 'input:checkbox' ).is( ':checked' ) ){
        				rtwalwm_referral_ids.push( $(this).data( 'referral_id' ) );
        			}
        		}
        	});

        	if( rtwalwm_referral_ids.length > 0 ){
        		var data = {
        			action 					: 'rtwalwm_aff_approve',
        			rtwalwm_security_check	: rtwalwm_global_params.rtwalwm_nonce,
        			rtwalwm_referral_ids	: rtwalwm_referral_ids,
        		};

        		$.blockUI({ message: '' });
        		$.ajax({
        			url 		: rtwalwm_global_params.rtwalwm_ajaxurl,
        			type 		: "POST",
        			data 		: data,
        			dataType 	: 'json',
        			success 	: function(response)
        			{
        				if( response.rtwalwm_status ){
        					$(document).find( '.rtwalwm_affiliates_table > tbody  > tr' ).each( function()
        					{
        						if( $.inArray( $(this).data( 'referral_id' ), response.rtwalwm_approved_ids ) ){
        							if( $(this).find( 'input:checkbox' ).length == 1 ){
        								$(this).find( '.rtwalwm-checkbox' ).remove();
        								$(this).find( '.rtwalwm-add-link > span' ).removeClass( 'rtwalwm_aff_approve' ).addClass( 'rtwalwm_aff_approved' );
        							}
        						}
        					});
        				}
        				alert( response.rtwalwm_message );
        				$.unblockUI();
        			}
        		});
        	}
        	else{
        		alert( rtwalwm_global_params.rtwalwm_nothing_marked );
        	}
        });

        $(document).on( 'change', '.rtwalwm_paypal_live_radio', function(){
        	if( $(this).is(':checked') ){
        		$(this).closest( 'div' ).find( '#rtwalwm_paypal_live_id, #rtwalwm_paypal_live_secret' ).attr( 'required', 'required' );
        	}
        	else{
        		$(this).closest( 'div' ).find( '#rtwalwm_paypal_live_id, #rtwalwm_paypal_live_secret' ).attr( 'required', 'required' );
        	}
        });

        $(document).on( 'change', '.rtwalwm_paypal_sandbox_radio', function(){
        	if( $(this).is(':checked') ){
        		$(this).closest( 'div' ).find( '#rtwalwm_paypal_sandbox_id, #rtwalwm_paypal_sandbox_secret' ).attr( 'required', 'required' );
        	}
        	else{
        		$(this).closest( 'div' ).find( '#rtwalwm_paypal_sandbox_id, #rtwalwm_paypal_sandbox_secret' ).attr( 'required', 'required' );
        	}
        });

        $(document).on( 'click', ".rtwalwm_override_show_hide", function (e){
        	if( $(this).val() == 1 ){
        		$(document).find( '.rtwalwm_override' ).show();
        	}
        	else if( $(this).val() == 0 ){
        		$(document).find( '.rtwalwm_override' ).hide();
        	}
        });

        $(document).on( 'click', ".rtwalwm_show_hide_prod_comm", function (e){
        	if( $(this).val() == 1 ){
        		$(document).find( '.rtwalwm_prod_comm' ).show();
        	}
        	else if( $(this).val() == 2 ){
        		$(document).find( '.rtwalwm_prod_comm' ).hide();
        	}
        });

        $(document).on( 'change', ".rtwalwm_affiliate_level_select", function (e){
        	var rtwalwm_user_id 	= $(this).data( 'rtwalwm-num' );
        	var rtwalwm_value 		= $(this).val();

        	var rtwalwm_data = {
        		action 					: 'rtwalwm_change_affiliate_level',
        		rtwalwm_user_id 		: rtwalwm_user_id,
        		rtwalwm_value 			: rtwalwm_value,
        		rtwalwm_security_check	: rtwalwm_global_params.rtwalwm_nonce
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
        				var html = '<div id="message" class="notice notice-success is-dismissible rtwalwm_affiliate_notice"><p>'+response.rtwalwm_message+'.</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';

        				if( $(document).find( '.rtwalwm_affiliate_notice' ).length ){
        					$(document).find( '.rtwalwm_affiliate_notice p' ).text( response.rtwalwm_message );
        				}
        				else{
        					$(document).find( '.wp-header-end' ).after( html );
        				}

        				$('html, body').animate({
        					scrollTop: $("body").offset().top
        				}, 500, "linear", function(){
        				});
        			}
        			else{
        				var html = '<div id="message" class="notice notice-error is-dismissible rtwalwm_affiliate_notice"><p>'+response.rtwalwm_message+'.</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';

        				if( $(document).find( '.rtwalwm_affiliate_notice' ).length ){
        					$(document).find( '.rtwalwm_affiliate_notice p' ).text( response.rtwalwm_message );
        				}
        				else{
        					$(document).find( '.wp-header-end' ).after( html );
        				}

        				$('html, body').animate({
        					scrollTop: $("body").offset().top
        				}, 500, "linear", function(){
        				});
        			}
        			$.unblockUI();
        		}
        	});
        });

        $(document).on( 'change', ".rtwalwm_select2_level_criteria", function (e){
        	if( $(this).val() == 0 ){
        		$(document).find( '.rtwalwm_level_criteria_amount' ).val( '0' );
        		$(document).find( '.rtwalwm_level_criteria_amount' ).attr( 'disabled', 'disabled' );
        	}
        	else{
        		$(document).find( '.rtwalwm_level_criteria_amount' ).removeAttr( 'disabled' );
        	}
        });

       

        $(document).on( 'click', '.rtwalwm_referral_delete', function(){
        	var $this = $(this);
        	var rtwalwm_referral_id = $(this).closest('tr').data( 'referral_id' );

        	var data = {
        		action 					: 'rtwalwm_referral_delete',
        		rtwalwm_security_check	: rtwalwm_global_params.rtwalwm_nonce,
        		rtwalwm_referral_id		: rtwalwm_referral_id,
        	};

        	$.blockUI({ message: '' });
        	$.ajax({
        		url 		: rtwalwm_global_params.rtwalwm_ajaxurl,
        		type 		: "POST",
        		data 		: data,
        		dataType 	: 'json',
        		success 	: function(response)
        		{
        			if( response.rtwalwm_status ){
        				alert( response.rtwalwm_message );
        				$this.closest( 'tr' ).remove();
        			}
        			else {
        				alert( response.rtwalwm_message );
        			}
        			$.unblockUI();
        		}
        	});
        });



  

        if( $(document).find( '.rtwalwm_edit_user_level' ).length != 0 ){
        	var rtwalwm_aff = $(document).find( '.rtwalwm_edit_user_affiliate' ).val();
        	if( $(document).find( '.rtwalwm_edit_user_affiliate' ).prop( 'checked' ) ){
        		$(document).find( '.rtwalwm_edit_user_level' ).show()
        	}
        }

        // $(document).on( 'click', '.rtwalwm_add_user_affiliate', function() {
        // 	if( $(this).prop( 'checked' ) ){
        // 		$(document).find( '.rtwalwm_new_user_level' ).show();
        // 	}
        // 	else{
        // 		$(document).find( '.rtwalwm_new_user_level' ).hide();
        // 	}
        // });

        $(document).on( 'click', '.rtwalwm_edit_user_affiliate', function() {
        	if( $(this).prop( 'checked' ) ){
        		$(document).find( '.rtwalwm_edit_user_level' ).show();
        	}
        	else{
        		$(document).find( '.rtwalwm_edit_user_level' ).hide();
        	}
        });

    
      

       
    

        $(document).on( 'change', '.rtwalwm_select2_sharing_bonus_time_limit', function(){
        	if( $(this).val() == 0 ){
        		$(document).find( '.sharing_bonus_amount_limit' ).attr( 'disabled', 'disabled' );
        	}
        	else{
        		$(document).find( '.sharing_bonus_amount_limit' ).removeAttr( 'disabled' );
        	}
        });

        $(document).on('click', '.rtwalwm-form-custom-field-clone', function(){
        	var clone_id = $('.rtwalwm_clone_counter').val();
        	var updated_clone_id = parseInt(clone_id)+parseInt(1)
        	$('.rtwalwm_clone_counter').val(parseInt(updated_clone_id));
        	var rtwalwm_cloned = $(".rtwalwm-input_type-inner-wrapper").clone();
        	rtwalwm_cloned.html(function(i, Html){
        		$(Html+":contains("+clone_id+")").each(function(){
        			Html = Html.replace(0,updated_clone_id)
        		});
        		return  Html
        	});
                rtwalwm_cloned.find("input:text").val("");
                rtwalwm_cloned.find('select').each(function(index, item) {
                     $(item).val("");

                });
        	rtwalwm_cloned.find(".rtwalwm-custom-input-options-span").remove();
        	$(rtwalwm_cloned).addClass('rtwalwm-input_type-inner-wrapper'+updated_clone_id).removeClass('rtwalwm-input_type-inner-wrapper');
        	rtwalwm_cloned.appendTo('.rtwalwm-input_type-wrapper');
        });

        $(document).on( 'change', '.rtwalwm-custom-input_type', function(){
        	var current_clone_id = $(this).data('current_count');
        	var selected_val = $(this).children("option:selected").val();
	        if(selected_val == 'checkbox' || selected_val == 'radio' || selected_val == 'select'){
        		if($(this).parents('.rtwalwm-input_type-inner').find(".rtwalwm-custom-options").length == 0){
	        		$(this).parents('.rtwalwm-input_type-inner').append('<span class="rtwalwm-custom-input-options-span">\
	        			<label for="rtwalwm-custom-label-class">Options</label>\
	        			<input type="text" name="rtwalwm_reg_temp_opt[custom-input]['+current_clone_id+'][custom-input-options]" class="rtwalwm-custom-options">\
	        			</span>');
	        	}
        	}else{
        		$(this).parents('.rtwalwm-input_type-inner').find(".rtwalwm-custom-input-options-span").remove();
        	}
		});


		$(document).on( 'click', ".rtwalwm_add_custom_banner", function () {

			$(".rtwalwm_add_custom_banner_wrapper").addClass('show');
		})
		$(document).on( 'click', "#rtwalwm_cancle_custom_banner", function () {

			$(".rtwalwm_add_custom_banner_wrapper").removeClass('show');
		})

		var file_frame;
		var attachment;
		var wp_media_post_id ; 
		var set_to_post_id = $(".rtwalwm_custom_banner_image").attr("data-id");
		$(document).on('click','.rtwalwm_custom_banner_image', function( event ){
			
			if ( file_frame ) {
				file_frame.uploader.uploader.param( 'post_id', set_to_post_id );
				file_frame.open();
				return;
			} else {
				wp.media.model.settings.post.id = set_to_post_id;
			}
			
			file_frame = wp.media.frames.file_frame = wp.media({
				title: 'Select a image to upload',
				button: {
					text: 'Upload',
				},
				multiple: false	
			});
			
		
			file_frame.on( 'select', function() {
				attachment = file_frame.state().get('selection').first().toJSON();
				// Do something with attachment.id and/or attachment.url here
				if(attachment)
				{
				$(".rtwalwm_image_width_detail").css('display','block');
				$(".rtwalwm_image_height_detail").css('display','block');

				}
				$( '#rtwalwm-image-preview' ).attr( 'src', attachment.url ).css( 'width', '200px','height','300px' );
				$( '#rtwalwm-image_attachment_id' ).val( attachment.id );	
				$("#rtwalwm_image_width").html(attachment.width) ;
				$("#rtwalwm_image_height").html(attachment.height) ;


				// Restore the main post ID
				wp.media.model.settings.post.id = wp_media_post_id;
			});
				file_frame.open();
			});
		
		$( 'a.add_media' ).on( 'click', function() {
			wp.media.model.settings.post.id = wp_media_post_id;
		});

		$(document).on('click','#rtwalwm_save_custom_banner',function(){
			
			var rtwalwm_image_id = $(document).find('#rtwalwm-image_attachment_id').val();
			var rtwalwm_target_link = $(document).find( '.rtwalwm_custom_banner_url_detail' ).val();

			var rtwalwm_select_option_val = $(".rtwalwm_select_image_size").val();
			var rtwalwm_array_select_option = rtwalwm_select_option_val.split("x"); 
			var rtwalwm_image_dimention_width = rtwalwm_array_select_option[0]; 
			var rtwalwm_image_dimention_height = rtwalwm_array_select_option[1];

			var rtwwwwap_selected_image_width = $("#rtwalwm_image_width").html();
			var rtwwwwap_selected_image_height = $("#rtwalwm_image_height").html(); 
	
			if( rtwalwm_image_id && rtwalwm_target_link && ((rtwalwm_image_dimention_width == rtwwwwap_selected_image_width) && (rtwalwm_image_dimention_height == rtwwwwap_selected_image_height) ) ){
        		var data = {
        			action 					: 'rtwalwm_custom_banner',
        			rtwalwm_security_check	: rtwalwm_global_params.rtwalwm_nonce,
        			rtwalwm_image_id		: rtwalwm_image_id,
					rtwalwm_target_link		: rtwalwm_target_link,
					rtwalwm_image_dimention_width : rtwalwm_image_dimention_width,
					rtwalwm_image_dimention_height : rtwalwm_image_dimention_height

        		};

        		$.blockUI({ message: '' });
        		$.ajax({
        			url 		: rtwalwm_global_params.rtwalwm_ajaxurl,
        			type 		: "POST",
        			data 		: data,
        			dataType 	: 'json',
        			success 	: function(response)
					{	if(response.rtwalwm_status == true)
						{
							$(".rtwalwm_add_custom_banner_wrapper").hide();
							alert( response.rtwalwm_message );

							window.location.reload();
						}
						else{
							alert( response.rtwalwm_message );
						}
						$.unblockUI();
					
					}
				});
			
			}
			else{
					if(rtwalwm_image_id == ''){
						alert(rtwalwm_global_params.rtwalwm_image_id);
					}
					else if((rtwalwm_image_dimention_width != rtwwwwap_selected_image_width) || (rtwalwm_image_dimention_height != rtwwwwap_selected_image_height))
					{
						alert(rtwalwm_global_params.rtwalwm_image_parameter_not_match);
					}	
					else if(rtwalwm_target_link == '')
					{
						alert(rtwalwm_global_params.rtwalwm_target_link);
					}	
			
			}
			
		});

		$(document).on("click",".rtwalwm_custom_banner_delete", function(){

				var rtwalwm_image_id = $(this).data('image_id');
				var rtwalwm_target_link = $(this).data('target_link');
				
	
				var data = {
        			action 					: 'rtwalwm_delete_banner',
        			rtwalwm_security_check	: rtwalwm_global_params.rtwalwm_nonce,
        			rtwalwm_image_id		: rtwalwm_image_id,
        			rtwalwm_target_link		: rtwalwm_target_link
        		};

        		$.blockUI({ message: '' });
        		$.ajax({
        			url 		: rtwalwm_global_params.rtwalwm_ajaxurl,
        			type 		: "POST",
        			data 		: data,
        			dataType 	: 'json',
        			success 	: function(response)
					{	if(response.rtwalwm_status == true)
						{
							alert( response.rtwalwm_message );
							window.location.reload();
						}
						else{
							alert( response.rtwalwm_message );
						}
						$.unblockUI();
					}
				});

		});
		
		$(document).on('click','#rtwalwm_cancle_custom_banner', function(){
			$('.rtwalwm_add_custom_banner_wrapper').removeClass('show');
			$('#rtwalwm-image-preview').attr('src',"");
			$('.rtwalwm_select_image_size').val("0");
			$(".rtwalwm_image_width_detail").css('display','none');
			$(".rtwalwm_image_height_detail").css('display','none');
		});


// 		$(document).on('click','#rtwwwap_save_notification',function()
// 		{
// 			var rtwwwap_not_title = $('#rtwwwap_notification_title_inpt').val();
// 			var rtwwwap_no_text = $('.rtwwwap_notification_textarea').val();
		
// 			if($(this).data('key'))
// 			{
// 				var rtwwwap_key = $(this).data('key');
// 			}
// 			else{
// 				var rtwwwap_key = "";
// 			}
			
// 			var data = {
// 				action 					: 'rtwwwap_save_notification',
// 				rtwwwap_security_check	: rtwwwap_global_params.rtwwwap_nonce,
// 				rtwwwap_not_title		: rtwwwap_not_title,
// 				rtwwwap_no_text			: rtwwwap_no_text,
// 				rtwwwap_key				: rtwwwap_key
// 			};

// 			$.blockUI({ message: '' });
// 			$.ajax({
// 				url 		: rtwwwap_global_params.rtwwwap_ajaxurl,
// 				type 		: "POST",
// 				data 		: data,
// 				dataType 	: 'json',
// 				success 	: function(response)
// 				{	
// 					if(response.rtwwwap_status)
// 					{
// 						alert(response.rtwwwap_message);
// 						$('.rtwwwap-notification-wrapper').removeClass('show');

// 						var rtwwwap_final_array = response.rtwwwap_array;
						

// 						if(rtwwwap_final_array)
// 						{
							
// 							var html = "";
// 							Object.entries(rtwwwap_final_array).forEach(function(row)
// 							{
// 								var key = row[0];
// 								var title = row[1].title;
// 								var content = row[1].content;
// 								print_r($key);
// 								print_r($title);
// 								html += "<tr><td>"+title+"</td><td><span><i class='fa fa-eye rtwwwap_view_edit_icon' data-key="+key+" aria-hidden='true' data-noti_title="+title+" data-noti_content="+content+"></i></span></td><td><i class='far fa-trash-alt rtwwwap_delete rtwwwap_view_delete_icon' data-key="+ (key) +"></i></td></tr>";
// 							});
// 						}
// 						$(".rtwwwap_noti_main").html(" ");
// 						$(".rtwwwap_noti_main").append(html);
// 						$('#rtwwwap_notification_title_inpt').val('');
// 						$('.rtwwwap_notification_textarea').val('');
						
// 						$(document).find( '.rtwwwap_cancle_custom_banner' ).trigger( 'click' );

// 					}
// 					else{
// 						alert(response.rtwwwap_message);

// 					}
// 					$.unblockUI();
				
// 				}
// 			});
// 		});

// 		$("#rtwwwap_cancle_add_notification").on('click',function(){
// 			$(".rtwwwap-notification-wrapper").removeClass('show');
// 			$('#rtwwwap_save_notification').removeAttr("data-key");
// 			$('#rtwwwap_save_notification').val("Save");

// 		});

// 		$(document).on('click',".rtwwwap_view_edit_icon",function(){
// 			var rtwwwap_noti_title = $(this).data('noti_title');
// 			var rtwwwap_noti_content = $(this).data('noti_content');
// 			var rtwwwap_key = $(this).data('key');

// 			$('#rtwwwap_save_notification').attr("data-key",rtwwwap_key);
//      		$('#rtwwwap_save_notification').val("Update");
// 			 $('#rtwwwap_notification_title_inpt').val(rtwwwap_noti_title);
// 			 $('.rtwwwap_notification_textarea').val(rtwwwap_noti_content);
// 			 $('.rtwwwap-notification-wrapper').addClass('show');
// 		});

// 		$(document).find( '.rtwwwap_notification_table').DataTable({
//         	responsive: true,
//         	"order" : [],
//         	"columnDefs": [
//         	{ orderable: false, targets: [0] },
//         	{ "width": "33.33%", "targets": 0 },
//         	{ "width": "33.33%", "targets": 1 },
//         	{ "width": "33.33%", "targets": 2 }
//         	],
// 		});

// 		$(document).on('click','.rtwwwap_delete', function(){
// 			var rtwwwap_key = $(this).data('key');
// 			var This = $(this).closest('tr');
// 			var data = {
// 				action 					: 'rtwwwap_delete_noti',
// 				rtwwwap_security_check	: rtwwwap_global_params.rtwwwap_nonce,
// 				rtwwwap_key				: rtwwwap_key
// 			};

// 			$.blockUI({ message: '' });
// 			$.ajax({
// 				url 		: rtwwwap_global_params.rtwwwap_ajaxurl,
// 				type 		: "POST",
// 				data 		: data,
// 				dataType 	: 'json',
// 				success 	: function(response)
// 				{	
// 					if(response.rtwwwap_status)
// 					{
// 						This.remove();
// 						alert(response.rtwwwap_message);
// 					}
// 					else{
// 						alert(response.rtwwwap_message);
// 					}
// 					$.unblockUI();
				
// 				}
// 			});

// 		});

// 	});
// })( jQuery );

		$(document).on('click','#rtwalwm_save_notification', function(){
			var rtwalwm_not_title = $('#rtwalwm_notification_title_inpt').val();
			var rtwalwm_no_text = $('.rtwalwm_notification_textarea').val();
			
			if($(this).data('key') > -1)
			{
				var rtwalwm_key = $(this).data('key');
			}
			else{
				var rtwalwm_key = "";
			}
		
			var data = {
				action 					: 'rtwalwm_save_notification',
				rtwalwm_security_check	: rtwalwm_global_params.rtwalwm_nonce,
				rtwalwm_not_title		: rtwalwm_not_title,
				rtwalwm_no_text			: rtwalwm_no_text,
				rtwalwm_key				: rtwalwm_key
			};

			$.blockUI({ message: '' });
			$.ajax({
			url 		: rtwwwap_global_params.rtwwwap_ajaxurl,
			type 		: "POST",
			data 		: data,
			dataType 	: 'json',
			success 	: function(response)
			{	
				if(response.rtwwwap_status)
				{
					alert(response.rtwwwap_message);
					$('.rtwwwap-notification-wrapper').removeClass('show');

					var rtwwwap_final_array = response.rtwwwap_array;
					

					if(rtwwwap_final_array)
					{
						
						var html = "";
						Object.entries(rtwwwap_final_array).forEach(function(row)
						{
							var key = row[0];
							var title = row[1].title;
							var content = row[1].content;
							print_r($key);
							print_r($title);
							html += "<tr><td>"+title+"</td><td><span><i class='fa fa-eye rtwwwap_view_edit_icon' data-key="+key+" aria-hidden='true' data-noti_title="+title+" data-noti_content="+content+"></i></span></td><td><i class='far fa-trash-alt rtwwwap_delete rtwwwap_view_delete_icon' data-key="+ (key) +"></i></td></tr>";
						});
					}
					$(".rtwwwap_noti_main").html(" ");
					$(".rtwwwap_noti_main").append(html);
					$('#rtwwwap_notification_title_inpt').val('');
					$('.rtwwwap_notification_textarea').val('');
					
					$(document).find( '.rtwwwap_cancle_custom_banner' ).trigger( 'click' );

				}
				else{
					alert(response.rtwwwap_message);

				}
				$.unblockUI();
			
			}
		});

	});
	$(document).on('click','.rtwalwm_customize_email', function(){
		alert(" This Feature is Available in our pro version plugin ");
	});
});
})( jQuery );

// $(document).on('click','#publish', function(){
// 	var select_affiliate = $('#rtwalwm_select_affiliate').val();
// 	console.log(select_affiliate);
// 	var data = {
// 		action 					: 'rtwalwm_save_coupon',
// 		rtwalwm_security_check	: rtwalwm_global_params.rtwalwm_nonce,
// 		rtwalwm_select_affiliate: rtwalwm_select_affiliate
// 	};

// 	$.blockUI({ message: '' });
// 	$.ajax({
// 		url 		: rtwalwm_global_params.rtwalwm_ajaxurl,
// 		type 		: "POST",
// 		data 		: data,
// 		dataType 	: 'json',
// 		success 	: function(response)
// 		{	
// 			alert(response.rtwalwm_select_affiliate);
// 			$.unblockUI();
// 		}
// 	});
// })(jQuery);
