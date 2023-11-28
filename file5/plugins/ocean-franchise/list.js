/**
 * @author https://www.cosmosfarm.com/
 */

jQuery(document).ready(function(){
	var kboard_map_image = jQuery('#kboard-ocean-franchise-list .kboard-map-v2 img').attr('src');
	
	jQuery('#kboard-ocean-franchise-list .kboard-map-v2 .map-area').each(function(){
		var map = jQuery(this).data('map');
		jQuery(this).mouseover(function(){
			jQuery('#kboard-ocean-franchise-list .kboard-map-v2 img').attr('src', map);
		});
	});
	
	if(kboard_map_image){
		jQuery('#kboard-ocean-franchise-list .kboard-map-v2').mouseleave(function(){
			jQuery('#kboard-ocean-franchise-list .kboard-map-v2 img').attr('src', kboard_map_image);
		});
	}
});