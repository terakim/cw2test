/**
 * @author https://www.cosmosfarm.com/
 */

jQuery(document).ready(function(){
	kboard_calendar_layout();
});

jQuery(window).resize(function(){
	kboard_calendar_layout();
});

function kboard_calendar_layout(){
	jQuery('.kboard-cross-calendar-list').each(function(){
		var list = jQuery(this).width();
		var item_width = jQuery('.kboard-calendar-table td', this).width();
		jQuery('.kboard-calendar-table td', this).css({'height':item_width+'px'});
		
		if(list < 600){
			// mobile
			jQuery(this).removeClass('pc');
			jQuery(this).addClass('mobile');
			jQuery('.mobile .wide').css('display', 'none');
			jQuery('.mobile .short').css('display', 'block');
			
		}
		else{
			// pc
			jQuery(this).removeClass('mobile');
			jQuery(this).addClass('pc');
			jQuery('.pc .wide').css('display', 'block');
			jQuery('.pc .short').css('display', 'none');
		}
		
		jQuery(this).css({visibility:'visible'});
	});
}

function kboard_calendar_latest_template(year, month, type){
	jQuery.get(kboard_settings.site_url, {kboard_calendar_latest_template:'calendar', kboard_calendar_latest_board_id:jQuery('input[name=kboard_calendar_latest_board_id]').val(), kboard_calendar_latest_latestview_id:jQuery('input[name=kboard_calendar_latest_latestview_id]').val(), kboard_calendar_latest_board_url:jQuery('input[name=kboard_calendar_latest_board_url]').val(), kboard_calendar_latest_month:month, kboard_calendar_latest_year:year, kboard_calendar_latest_type:type}, function(data){
		jQuery('#kboard-cross-calendar-latest .kboard-cross-calendar-list').html(jQuery(data).find('.kboard-cross-calendar-list').html());
		kboard_calendar_layout();
    }, 'text');
}