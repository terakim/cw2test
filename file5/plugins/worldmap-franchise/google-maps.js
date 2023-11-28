/**
 * @author https://www.cosmosfarm.com/
 */

function kboard_worldmap_franchise_init_map(){
	var current_category = jQuery('input[name="kboard_worldmap_franchise_current_category"]').length ? jQuery('input[name="kboard_worldmap_franchise_current_category"]').val() : '';
	var current_category2 = jQuery('input[name="kboard_worldmap_franchise_current_category2"]').length ? jQuery('input[name="kboard_worldmap_franchise_current_category2"]').val() : '';
	var document_lat = jQuery('input[name="kboard_worldmap_franchise_map_location_lat"]').length ? parseFloat(jQuery('input[name="kboard_worldmap_franchise_map_location_lat"]').val()) : '';
	var document_lng = jQuery('input[name="kboard_worldmap_franchise_map_location_lng"]').length ? parseFloat(jQuery('input[name="kboard_worldmap_franchise_map_location_lng"]').val()) : '';
	
	var map_div;
	var map;
	var map_option = {
		zoom : 7,
		mapTypeControl : true,
		mapTypeId : google.maps.MapTypeId.ROADMAP,
		scrollwheel : true,
		gestureHandling : 'greedy'
	};
	
	var marker;
	var markers = [];
	var markerCluster;
	
	if(document_lat && document_lng){
		map_div = jQuery('#kboard-worldmap-franchise-canvas')[0];
		jQuery('#kboard-worldmap-franchise-canvas').addClass('active');
		
		map_option.center = {lat: document_lat, lng: document_lng};
		map_option.zoom = 15;
	}
	else{
		map_div = jQuery('#kboard-worldmap-franchise-canvas')[0];
		jQuery('#kboard-worldmap-franchise-canvas').addClass('active');
	}
	
	map = new google.maps.Map(map_div, map_option);
	
	google.maps.event.addListenerOnce(map, 'idle', function(){
		if(document_lat && document_lng){
			kboard_worldmap_franchise_get_marker_list(current_category, current_category2, document_lat, document_lng, map, markers);
		}
		else{
			kboard_worldmap_franchise_get_current_location(current_category, current_category2, function(location){
				map.setCenter(location.latLng);
				map.setZoom(location.zoom);
				kboard_worldmap_franchise_get_marker_list(current_category, current_category2, document_lat, document_lng, map, markers);
			});
		}
		
		google.maps.event.addListener(map, 'zoom_changed', function(){
			kboard_worldmap_franchise_get_marker_list(current_category, current_category2, document_lat, document_lng, map, markers);
		});
    });
	
	google.maps.event.addListener(map, 'dragend', function(){
		if(map.getZoom() > 8 && map.getZoom() < 19){
			kboard_worldmap_franchise_get_marker_list(current_category, current_category2, document_lat, document_lng, map, markers);
		}
	});
}

function kboard_worldmap_franchise_get_marker_list(current_category, current_category2, document_lat, document_lng, map, markers){
	var lat = map.getCenter().lat();
	var lng = map.getCenter().lng();
	var bounds = map.getBounds();
	var south_east = bounds.getSouthWest();
    var south_east_lat = south_east.lat();
    var south_east_lng = south_east.lng();
	
	if(lat && lng){
		jQuery.get(worldmap_franchise.request_uri, {action:'kboard_worldmap_franchise_get_gps_list', board_id:kboard_current.board_id, category1:current_category, category2: current_category2, lat:lat, lng:lng, south_east_lat:south_east_lat, south_east_lng:south_east_lng, security:worldmap_franchise.security}, function(results){
			if(results){
				var list = [];
				var checker = [];
				
				for(var key in results){
					if(!results.hasOwnProperty(key)) continue;
					
					var lat = parseFloat(results[key].lat);
					var lng = parseFloat(results[key].lng);
	
					if(lat && lng && checker.indexOf(lat + '' + lng) == -1){
						list.push(results[key]);
						checker.push(lat + '' + lng);
					}
				}
				kboard_worldmap_franchise_add_marker(document_lat, document_lng, map, markers, list);
			}
		});
	}
}

function kboard_worldmap_franchise_get_current_location(current_category, current_category2, callback){
	var location;
	var default_location = jQuery('input[name="kboard_worldmap_franchise_default_location"]').length ? jQuery('input[name="kboard_worldmap_franchise_default_location"]').val() : '';
	var default_location_lat = '';
	var default_location_lng = '';
	var default_zoom = jQuery('input[name="kboard_worldmap_franchise_default_zoom"]').val() ? parseInt(jQuery('input[name="kboard_worldmap_franchise_default_zoom"]').val()) : 7;
	
	if(default_location){
		default_location = default_location.split(',');
		default_location_lat = parseFloat(jQuery.trim(default_location[0]));
		default_location_lng = parseFloat(jQuery.trim(default_location[1]));
	}
	
	if(navigator.geolocation && !current_category){
		navigator.geolocation.getCurrentPosition(function(position){
			location = {latLng: {lat: position.coords.latitude, lng: position.coords.longitude}, zoom: default_zoom};
			callback(location);
		}, function(error){
			if(error.code){
				location = {latLng: {lat: default_location_lat, lng: default_location_lng}, zoom: default_zoom};
				callback(location);
			}
		});
	}
	else{
		location = {latLng: {lat: default_location_lat, lng: default_location_lng}, zoom: default_zoom};
		callback(location);
	}
}

function kboard_worldmap_franchise_add_marker(document_lat, document_lng, map, markers, results){
	if(markers.length){
		for(var index in markers){
			if(!markers.hasOwnProperty(index)) continue;
			
			markers[index].setMap(null);
			markerCluster.removeMarker(markers[index]);
		}
		markers.length = 0;
	}
	
	if(results.length){
		var skin_path = jQuery('input[name="kboard_worldmap_franchise_skin_path"]').val();
		
		for(var key in results){
			if(!results.hasOwnProperty(key)) continue;
			
			var position = {lat:parseFloat(results[key].lat), lng:parseFloat(results[key].lng)};
			var marker = new google.maps.Marker({
				map: map,
				position: position,
				title: results[key].title,
				url: results[key].urls
			});
			
			if((results[key].lat == document_lat) && results[key].lng == document_lng){
				marker.setAnimation(google.maps.Animation.BOUNCE);
			}
			markers.push(marker);
			
			marker.addListener('click', function(){
				window.location.href = this.url;
			});
		}
		markerCluster = new MarkerClusterer(map, markers,{
			imagePath: skin_path + '/images/marker-clusterer/m',
			gridSize: 30
		});
	}
}