/**
 * @author https://www.cosmosfarm.com/
 */

function kboard_franchise_map_initialize(name, address, location){
	var geocoder = new google.maps.Geocoder();
	var lat='';
	var lng='';
	var option = {};
	
	if(address){
		option = {'address':address};
	}
	else if(location){
		option = {'location':location};
	}
	
	geocoder.geocode(option,
		function(results, status){
			if(results){
				jQuery('#kboard-franchise-map-canvas').addClass('active');
				var location=results[0].geometry.location;
				lat=location.lat();
				lng=location.lng();
				var latlng = new google.maps.LatLng(lat, lng);
				var options = {
					zoom: 16,
					center: latlng,
					mapTypeControl: true,
					mapTypeId: google.maps.MapTypeId.ROADMAP
				};
				var map = new google.maps.Map(document.getElementById('kboard-franchise-map-canvas'), options);
				var marker = new google.maps.Marker({ 
					   position: latlng, 
					   map: map,
					   title: name
				});
			}
			else jQuery('#kboard-franchise-map-canvas').html('');
		}
	)
}