/**
 * @author https://www.cosmosfarm.com/
 */

function kboard_discover_faq_toggle(obj){
	var parent = jQuery(obj).parent();
	var content = jQuery(parent).children('.kboard-list-description-wrap');
	var accordion_icon = jQuery(parent).find('.accordion');
	
	if(jQuery(parent).hasClass('active')){
		jQuery(content).slideUp('fast', function(){
			jQuery(parent).removeClass('active');
			jQuery(accordion_icon).html('<i class="fas fa-plus"></i>');
		});
	}
	else{
		jQuery(content).slideDown('fast');
		jQuery(parent).addClass('active');
		jQuery(accordion_icon).html('<i class="fas fa-minus"></i>');
	}
}