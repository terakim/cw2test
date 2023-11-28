var mapObj;
var mapCenter;
var currentMarker;
var currentLabel;
var currentCircle;
var currentAddress;
var mapMode;
var mapInfo;
var markerItemZIndex	= 1000;
var arMarkerData		= [];
var arLabelData			= [];
var arCircleData			= [];
var oController			= {};
var oSearchData			= {};
var markerClustering;
var mapContainerID	= "";
var clusterZoom			= 11;
var labelZoom			= 14;
var focusZoom			= 14;
var loadZoom			= 10;
var currentZoom		= 10;
var maxTitleLength		= 30;
var webzinIndex			= -1;

var htmlMarker1;
var htmlMarker2;
var htmlMarker3;
var htmlMarker4;
var htmlMarker5;
var checkMarkerLoad	= true;
var checkFocusZoom	= 0;

var LabelOverlay = function(options) {
	var min_width		= '200px';
	var tempTitle			= options.title;
	if(tempTitle.length>maxTitleLength){
		tempTitle			= tempTitle.substr(0,maxTitleLength)+"..";
	}
    this._element = jQuery('<div style="position:absolute;left:0px;top:0;margin-left:-100px;width:'+min_width+';text-align:center;overflow:hidden;"><div style="margin-top:3px;display:inline-block;line-height:1.6 !important;border: 0px; font-size:12px; font-weight:bold;padding:1px 5px; color:#FFFFFF;background-color: #2f2e2c;opacity: .80;filter: alpha(opacity=80);-ms-filter:\'alpha(opacity=80)\';-khtml-opacity: .80;-moz-opacity: .80;-webkit-border-radius:10px;-moz-border-radius:10px;border-radius:10px;">' +
                        tempTitle +
                        '</div></div>')
    this.setPosition(options.position);
    this.setMap(options.map || null);
};

LabelOverlay.prototype = new naver.maps.OverlayView();
LabelOverlay.prototype.constructor = LabelOverlay;

LabelOverlay.prototype.setPosition = function(position) {
    this._position = position;
    this.draw();
};

LabelOverlay.prototype.getPosition = function() {
    return this._position;
};

LabelOverlay.prototype.onAdd = function() {
    var overlayLayer = this.getPanes().overlayLayer;

    this._element.appendTo(overlayLayer);
};

LabelOverlay.prototype.draw = function() {
    if (!this.getMap()) {
        return;
    }

    var projection = this.getProjection(),
        position = this.getPosition(),
        pixelPosition = projection.fromCoordToOffset(position);

    this._element.css('left', pixelPosition.x);
    this._element.css('top', pixelPosition.y);
};

LabelOverlay.prototype.onRemove = function() {
    var overlayLayer = this.getPanes().overlayLayer;
    this._element.remove();
    this._element.off();
};


function initMap(map,mode,id,options){	
	mapContainerID			= id;
	mapObj					= map;
	mapMode				= mode;
	if(typeof(options['cluster_zoom'])!=='undefined') clusterZoom		= options['cluster_zoom']+1;
	if(typeof(options['label_zoom'])!=='undefined') labelZoom		= options['label_zoom'];
	if(typeof(options['focus_zoom'])!=='undefined') focusZoom		= options['focus_zoom'];
	if(typeof(options['max_marker_label_length'])!=='undefined') maxTitleLength		= options['max_marker_label_length'];
	if(mb_options["device_type"]=="desktop"){
		naver.maps.Event.once(map, 'init_stylemap', function () {
			map.setOptions("zoomControl", true);
			map.setOptions("zoomControlOptions", {position: naver.maps.Position.TOP_LEFT});
			//map.setOptions("mapTypeControl", true); //지도 유형 컨트롤의 표시 여부
			//map.setOptions("mapTypeControlOptions", {position: naver.maps.Position.TOP_LEFT});
		});		
	}

	// 지도가 확대 또는 축소되면 마지막 파라미터로 넘어온 함수를 호출하도록 이벤트를 등록합니다
	naver.maps.Event.addListener(map, 'zoom_changed', function(mouseEvent) {		
		currentZoom		= mapObj.getZoom();
		//console.log(currentZoom);
		if(checkFocusZoom==0){
			checkMarkerLoad	= true;
		}
		setMapBoundsData('zoom');
		if(checkFocusZoom!=2){
			checkMarkerLoad	= true;
		}else{
			//스마트 포커스 모드일 경우 현재 지도 영역에 있는 마커만 표시하기
			setTimeout(function() {
				updateVisibleMarkers('zoom');
			}, 1000);
		}
		checkFocusZoom		= 0;
		if(mapInfo && mapInfo.getMap()){
			mapInfo.close();
		}
	});
	naver.maps.Event.addListener(map, 'dragend', function() {
		if(checkMarkerLoad){
			setMapBoundsData('dragend');
		}else{
			//스마트 포커스 모드일 경우 현재 지도 영역에 있는 마커만 표시하기
			updateVisibleMarkers('dragend');
		}
	});


	htmlMarker1 = {
        content: '<div style="cursor:pointer;width:40px;height:40px;line-height:42px;font-size:10px;color:white;text-align:center;font-weight:bold;background:url('+ mb_urls["skin"]+'images/cluster-marker-1.png);background-size:contain;"></div>',
        size: N.Size(40, 40),
        anchor: N.Point(20, 20)
    },
    htmlMarker2 = {
        content: '<div style="cursor:pointer;width:40px;height:40px;line-height:42px;font-size:10px;color:white;text-align:center;font-weight:bold;background:url('+ mb_urls["skin"]+'images/cluster-marker-2.png);background-size:contain;"></div>',
        size: N.Size(40, 40),
        anchor: N.Point(20, 20)
    },
    htmlMarker3 = {
        content: '<div style="cursor:pointer;width:40px;height:40px;line-height:42px;font-size:10px;color:white;text-align:center;font-weight:bold;background:url('+ mb_urls["skin"]+'images/cluster-marker-3.png);background-size:contain;"></div>',
        size: N.Size(40, 40),
        anchor: N.Point(20, 20)
    },
    htmlMarker4 = {
        content: '<div style="cursor:pointer;width:40px;height:40px;line-height:42px;font-size:10px;color:white;text-align:center;font-weight:bold;background:url('+ mb_urls["skin"]+'images/cluster-marker-4.png);background-size:contain;"></div>',
        size: N.Size(40, 40),
        anchor: N.Point(20, 20)
    },
    htmlMarker5 = {
        content: '<div style="cursor:pointer;width:40px;height:40px;line-height:42px;font-size:10px;color:white;text-align:center;font-weight:bold;background:url('+ mb_urls["skin"]+'images/cluster-marker-5.png);background-size:contain;"></div>',
        size: N.Size(40, 40),
        anchor: N.Point(20, 20)
    };
}
function updateVisibleMarkers(type) {
	if(arMarkerData!=undefined)
	{
		var bounds	= mapObj.getBounds();
		var i = 0;
		var markerCount	= arMarkerData.length;
		for (i = 0; i < markerCount; i++) {
			if(bounds.hasLatLng(arMarkerData[i].getPosition())) {
				if(arMarkerData[i]) arMarkerData[i].setMap(mapObj);
				if(arLabelData[i]) arLabelData[i].setMap(mapObj);
			}else{
				if(arMarkerData[i]) arMarkerData[i].setMap(null);
				if(arLabelData[i]) arLabelData[i].setMap(null);
			}
		}
	}
}
function addMapMarker(title,mode,category1){

	mapCenter		= mapObj.getCenter();
	var checkDrag		= false;
	var iconName		= "";
	var marker_icon		= "images/icon_map_marker1.png";
	/*
	if(category1=="카테고리1"){
		marker_icon	= "images/icon_map_marker2.png";
	}else if(category1=="카테고리2"){
		marker_icon	= "images/icon_map_marker3.png";
	}
	*/
	if(mode=="write"){
		iconName		= "icon_write.png";
		checkDrag		= true;
	}else if(mode=="view"){
		iconName		= "icon_view.png";
	}

	if(typeof(currentMarker)==='undefined'){
		if(mode!="write"){
			currentLabel = new LabelOverlay({
				position: mapCenter,
				title: title
			});
			currentLabel.setMap(mapObj);
		}

		currentMarker = new naver.maps.Marker({
			position: mapCenter,
			title:title,
			icon: {
				url: mb_urls["skin"]+marker_icon,
				size: new naver.maps.Size(28, 36),
				scaledSize: new naver.maps.Size(28, 36),
				origin: new naver.maps.Point(0, 0),
				anchor: new naver.maps.Point(14, 36)
			},
			draggable: checkDrag,
			map: mapObj
		});
		if(mode=="write") currentMarker.setDraggable(true);

		if(mode=="view"){			

		}else {
			currentMarker.setMap(mapObj);
		}

		//마커 dragend 이벤트 발생시 위치 좌표 설정
		if(checkDrag){
			naver.maps.Event.addListener(currentMarker, 'dragend', function(mouseEvent) {				
				setMapPositionData("drag");			
			});
		}
	}else{
		currentMarker.setAnimation(naver.maps.Animation.BOUNCE);
		currentMarker.setPosition(mapCenter);
		currentLabel.setPosition(mapCenter);
	}
}
function setMapBoundsData(type){
	if(mapObj && mapMode=="list"){
		var bounds		= mapObj.getBounds();		
		var bounds_ne	= bounds.getNE();
		var bounds_sw	= bounds.getSW();
		currentZoom		= mapObj.getZoom();
		loadZoom		= currentZoom;
		if(document.getElementById("mb_map_bounds_minx")) document.getElementById("mb_map_bounds_minx").value	= bounds_sw.lng();
		if(document.getElementById("mb_map_bounds_maxx")) document.getElementById("mb_map_bounds_maxx").value	= bounds_ne.lng();
		if(document.getElementById("mb_map_bounds_miny")) document.getElementById("mb_map_bounds_miny").value	= bounds_sw.lat();
		if(document.getElementById("mb_map_bounds_maxy")) document.getElementById("mb_map_bounds_maxy").value	= bounds_ne.lat();
		if(document.getElementById("mb_map_zoom")) document.getElementById("mb_map_zoom").value	= currentZoom;
		if(checkMarkerLoad){
			sendListTemplateData();		
			webzinIndex				= -1;
		}else checkLabelZoom();
	}
}
function setMapLayoutSize(){
    var size = new naver.maps.Size(jQuery(".mb-map-box").width(), jQuery(".mb-map-box").height()-jQuery(".icon-map-list-open").height());
	mapObj.setSize(size);
}
function setMapLatLngData(lat,lng){
	if(document.getElementById("mb_gps_latitude")) document.getElementById("mb_gps_latitude").value		= lat;
	if(document.getElementById("mb_gps_longitude")) document.getElementById("mb_gps_longitude").value	= lng;
	if(jQuery(".mb-gps-latitude").length>0) jQuery(".mb-gps-latitude").text( lat );
	if(jQuery(".mb-gps-longitude").length>0) jQuery(".mb-gps-longitude").text( lng );	
}

function setMapPositionData(mode){
	setMapLatLngData(currentMarker.getPosition()._lat,currentMarker.getPosition()._lng);
}

function addMarker(idx,obj){
	var map			= mapObj;
	var mkObj		= obj;
	var index			= parseInt(mkObj.index)-1;
	var marker_icon	= "images/icon_map_marker1.png";
	/*
	if(mkObj.category1=="카테고리1"){
		marker_icon	= "images/icon_map_marker2.png";
	}else if(mkObj.category1=="카테고리2"){
		marker_icon	= "images/icon_map_marker3.png";
	}
	*/

	var overlay = new LabelOverlay({
        position: new naver.maps.LatLng(mkObj.latitude, mkObj.longitude),
		title: mkObj.title
    });
	var zoom		= map.getZoom();
	if(zoom>=labelZoom){
	    if(zoom>=clusterZoom) overlay.setMap(map);			
	}
	arLabelData.push(overlay);

	var marker = new naver.maps.Marker({
			position: new naver.maps.LatLng(mkObj.latitude, mkObj.longitude),
			title: mkObj.title,
			icon: {
				url: mb_urls["skin"]+marker_icon,
				size: new naver.maps.Size(28, 36),
				scaledSize: new naver.maps.Size(28, 36),
				origin: new naver.maps.Point(0, 0),
				anchor: new naver.maps.Point(14, 36)
			},
			map: map
			//draggable: true
		});
		marker.img		= mkObj.img;
		marker.index	= index;
		marker.url		= mkObj.url;

	arMarkerData.push(marker);
	
	if(mapObj) {
		naver.maps.Event.addListener(marker, 'click', function(mouseEvent){
			setWebzineItemFocus(marker);
		});	
	}
}

function setMarkers(data,mode) {
	if(mapInfo && mapInfo.getMap()){
		mapInfo.close();
	}

	//지도 마커 추가하기 함수
	if(typeof(data)=="string") markers		= JSON.parse(data);
	else if(typeof(data)!=='undefined') markers		= data;	

	var map					= mapObj;
	if(mapObj) {	
		if(mode!="append") clearMarker();
	}

	if(markers.length>0){
		var i = 0;
		if(mapObj) {
			var latlng	= new naver.maps.LatLng(markers[0].latitude, markers[0].longitude);
			//setMapCenter(latlng);
		
			var nCount		= markers.length;		
			for(i = 0 ;i < nCount; i++) {
				var mItem = markers[i];
				addMarker(i,mItem);
			}
			markerClustering = new MarkerClustering({
				minClusterSize: 2,
				maxZoom: clusterZoom,
				map: map,
				markers: arMarkerData,
				disableClickZoom: false,
				gridSize: 120,
				icons: [htmlMarker1, htmlMarker2, htmlMarker3, htmlMarker4, htmlMarker5],
				indexGenerator: [10, 100, 200, 500, 1000],
				stylingFunction: function(clusterMarker, count) {
					jQuery(clusterMarker.getElement()).find('div').filter(":first-child").text(count);
				}
			});
		}
	}
	
	setTimeout(function() {
		jQuery('#'+mapContainerID).css("visibility","visible");
	}, 100);	
}
function showMapContainer(){
	setTimeout(function() {
		jQuery('#'+mapContainerID).css("visibility","visible");
	}, 100);
}
function checkLabelZoom(){
	var zoom		= mapObj.getZoom();
	if(zoom>=labelZoom){
		if(arLabelData!=undefined){		
			var i = 0;
			for (i = 0; i < arLabelData.length; i++) {
				if(arLabelData[i]) arLabelData[i].setMap(mapObj);
			}		
		}
	}
}
function setWebzineItemFocus(item){
	if((item.index+1)==webzinIndex){
		//openWindow(item.url,"","");
		if(mapInfo && mapInfo.getMap()){
			mapInfo.close();
		}else{
			mapInfo.open(mapObj, item);
			mapInfo.setPosition(new naver.maps.LatLng(mapInfo.getPosition()._lat+0.0002, mapInfo.getPosition()._lng));
		}
	}else{
		webzinIndex	= item.index+1;
		var obj		= jQuery("#mb_"+mb_options["board_name"]+"_tr_"+webzinIndex);	
		if(obj){
			if(mb_options["device_type"]=="mobile"){
				checkMapListOpen('open');
			}		
			jQuery(".mb-list-box tr").removeClass('mb-webzine-item-focus');
			obj.addClass('mb-webzine-item-focus');	
			var scrollTop		= obj.position().top+jQuery("#"+mb_options["board_name"]+"_form_board_search").height() + 10;
			if(scrollTop<0) scrollTop		= 0;
			jQuery('.mb-list-box').animate({scrollTop: scrollTop}, 400);
			setMarkerItemFocus('focus',webzinIndex);
		}
	}
}
function setMarkerItemFocus(type,index){	
	if(type=='enter' || type=='focus'){		
		checkMarkerLoad	= false;
		webzinIndex			= index;	
		var latlng		= arMarkerData[index-1].getPosition();		
		var zoom		= mapObj.getZoom();
		if(zoom<focusZoom){
			if((focusZoom-zoom)>2){
				checkFocusZoom	= 2;
			}else{
				checkFocusZoom	= 1;
			}
			mapObj.zoomBy((focusZoom-zoom),latlng,true);
			mapObj.setCenter(latlng);			
		}else{
			setMapCenter(latlng);	
			setTimeout(function() {
				updateVisibleMarkers('focus');
			}, 1000);
		}

		if(mapInfo && mapInfo.getMap()){
			mapInfo.close();
		}
		var mkObj	= arMarkerData[index-1];
		var infoMaxWidth	= 500;
		var addMapLat		= 0.0002;
		if(zoom>16){
			addMapLat		= 0;
		}
		if(mb_options["device_type"]=="mobile"){
			infoMaxWidth	= 250;
		}
		mapInfo = new naver.maps.InfoWindow({
			anchorSize: new naver.maps.Size(14, 8),
			borderWidth: 1,
			maxWidth: infoMaxWidth,
			borderColor: "#555",
			content: '<div class="mb-info-window" style="width:100%;"><a href="'+mkObj.url+'" target="_blank"><div style="max-width:'+infoMaxWidth+'px;padding:7px 14px 6px 12px;text-align:left;font-size:13px;line-height:1.4;z-index:9999;"><div style="font-size:13px;font-weight:600;padding:0 0 2px;">'+mkObj.title+'</div><div style="font-size:13px;">'+mb_languages["view_details"]+' <span style="font-size:11px;">&gt;</span></div></div></a></div>'
		});
		setTimeout(function() {
			mapInfo.open(mapObj, mkObj);
			mapInfo.setPosition(new naver.maps.LatLng(mapInfo.getPosition()._lat+addMapLat, mapInfo.getPosition()._lng));
		}, 500);

	}else if(type=='hover'){		
		if(webzinIndex!=index){
			markerItemZIndex++;
			arMarkerData[index-1].setZIndex(markerItemZIndex);
		}
		webzinIndex			= index;
		arMarkerData[index-1].setAnimation(naver.maps.Animation.BOUNCE);		
	}else if(type=='leave'){
		arMarkerData[index-1].setAnimation(null);
	}
}

function setMapCenter(latlng){
	if(typeof(latlng)==='undefined' || latlng==""){
		if(typeof(arMarkerData[0])!=='undefined'){
			latlng			= arMarkerData[0].getPosition();
		}
	}
	if(mapObj) mapObj.panTo(latlng);
}

function setMapCurrentMarker(latlng){
	if(mapMode=="write"){
		currentMarker.setMap(mapObj);		
		if(typeof(latlng)==='undefined' || latlng==""){
			currentMarker.setPosition(mapObj.getCenter());
		}else{
			currentMarker.setPosition(latlng);
		}		
	}
}

function clearMarker(){
	if(arMarkerData!=undefined){
		var markerCount	= arMarkerData.length;
		var i = 0;		
		for (i = 0; i < markerCount; i++) {
			if(arMarkerData[i]) arMarkerData[i].setMap(null);
			if(arLabelData[i]) arLabelData[i].setMap(null);
		}		
	}
	arMarkerData			= [];
	arLabelData				= [];
	if(markerClustering) markerClustering.setMap(null);
}

function searchMapAddress(){
	var address		= document.getElementById("mb_map_address").value;
	address			= address.replace(/\(.*\)/gi, '');	
	
	if(address=="" || address==" "){
		alert(mb_languages["ie_address"]);
	}else{
		naver.maps.Service.geocode({
			address: address
		}, function(status, response) {
			if (status === naver.maps.Service.Status.ERROR || response.result.items.length==0) {
				return alert(mb_languages["ie_find_location"]);
			}
			var item = response.result.items[0];
			if(jQuery("input[name=site_link1]").length>0 && typeof(item.address)!=='undefined') jQuery("input[name=site_link1]").val( item.address );
			if(jQuery("input[name=site_link2]").length>0 && typeof(item.addrdetail.sido)!=='undefined') jQuery("input[name=site_link2]").val( item.addrdetail.sido);
			if(jQuery(".mb-map-postcode-wrap").length>0) jQuery(".mb-map-postcode-wrap").hide();

			//addrType = item.isRoadAddress ? '[도로명 주소]' : '[지번 주소]',
			coords	= new naver.maps.Point(item.point.x, item.point.y);

			//setMapCenter(coords);
			if(mapObj) mapObj.setCenter(coords);
			setMapCurrentMarker();
			setMapPositionData("search");
		});
	}
}
function mb_setMapCurrentPosition(){	
	if(navigator.geolocation){
		showLoadingBox();
		navigator.geolocation.getCurrentPosition(
			function (position) {
				hideLoadingBox();
				lat			= position.coords.latitude; 
				lng			= position.coords.longitude;
				var latlng	= new naver.maps.LatLng(lat, lng);
				setMapCenter(latlng);
				setTimeout(function() {
					setMapBoundsData('current_position');
				}, 500);
			},
			function (error) {		
				hideLoadingBox();
				alert(mb_languages["ie_current_location"]);
			},{maximumAge:60000, timeout:12000, enableHighAccuracy: true}
		);  
	}	
}
function getPostcodeMap(obj,id) {
	if(typeof(id)==='undefined' || id=='') id = 'mb_kakao_postcode1';
	var element_wrap	= document.getElementById(id);
	if(element_wrap.style.display=="block") {
		element_wrap.style.display = 'none';
		return;
	}
	var currentScroll		= Math.max(document.body.scrollTop, document.documentElement.scrollTop);
	new daum.Postcode({
		oncomplete: function(data) {
			var fullAddr = ""; 
			var extraAddr = "";

			if(data.userSelectedType === "R"){
				fullAddr = data.roadAddress;
			}else{
				fullAddr = data.jibunAddress;
			}
			if(data.userSelectedType === "R"){
				if(data.bname !== ""){extraAddr += data.bname;}
				if(data.buildingName !== ""){extraAddr += (extraAddr !== "" ? ", " + data.buildingName : data.buildingName);}
				fullAddr += (extraAddr !== "" ? " ("+ extraAddr +")" : "");
			}
			jQuery(obj).val(fullAddr);
			jQuery(obj).focus();
			element_wrap.style.display = 'none';

			// 우편번호 찾기 화면이 보이기 이전으로 scroll 위치를 되돌린다.
			jQuery("html, body").scrollTop( currentScroll );
		},
		// 우편번호 찾기 화면 크기가 조정되었을때 실행할 코드를 작성하는 부분. iframe을 넣은 element의 높이값을 조정한다.
		onresize : function(size) {
			element_wrap.style.height = (size.height+30)+'px';
		},
		width : '100%',
		height : '100%'
	}).embed(element_wrap);
	// iframe을 넣은 element를 보이게 한다.
	element_wrap.style.display = 'block';
}