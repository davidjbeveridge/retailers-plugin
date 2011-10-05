(function($){
	$(document).ready(function(docLoad){
		initMaps();
	});

	var newRetailerMapContainer = $('<div id="retailer-map-container" class="grid_11 alpha"></div>');
	var newRetailerMapSidebar = $('<div id="retailer-map-sidebar" class="grid_5 omega"></div>');
	var newRetailerSearch = $('<div id="retailer-search"><form id="retailer-form"><h3 id="retailer-map-title" class="align-left">Find A Retailer Near You</h3><input type="text" id="retailer-start-address" /><input type="submit" id="retailer-button" value="GO!" /></form></div>');
	var newRetailerMapNavigation = $('<div id="retailer-map-navigation"></div>')
	
	function initMaps(e){
		if ($('#retailer-map').length) {
			$('#retailer-map').append(newRetailerMapContainer);
			$('#retailer-map').append(newRetailerMapSidebar);
			$('#retailer-map-sidebar').append(newRetailerSearch);
			$('#retailer-map-sidebar').append(newRetailerMapNavigation);
			
			map = document.map = new GMap2(document.getElementById('retailer-map-container'));
			//map.setCenter(new GLatLng(37.4419, -122.1419), 13);
			map.setUIToDefault();
			$.getJSON(window.GHWRT_URL, {}, function(currentMap){
				plotPoints(currentMap);
				fillNavigation(currentMap);
			});
			
			$('#retailer-form').submit(function(e){
				e.preventDefault();
				if (!$('#retailer-start-address').val()) {
					return 0;
				}
				var input = {
					address: $('#retailer-start-address').val()
				};
				$.getJSON(window.GHWRT_URL, input, function(currentMap){
					plotPoints(currentMap);
					fillNavigation(currentMap);
				});
				return false;
			});
		}
		return false;
	}
	
	function plotPoints(m){
		map.setCenter(new GLatLng(m.center.lat, m.center.lon), 6);
		map.setUIToDefault();
		
		var baseIcon = new GIcon(G_DEFAULT_ICON);
		baseIcon.shadow = "http://www.google.com/mapfiles/shadow50.png";
		baseIcon.iconSize = new GSize(20, 34);
		baseIcon.shadowSize = new GSize(37, 34);
		baseIcon.iconAnchor = new GPoint(9, 34);
		baseIcon.infoWindowAnchor = new GPoint(9, 2);
		
		function createMarker(retailer, index, markerImage){
			// Create a lettered icon for this point using our icon class
			var letter = String.fromCharCode("A".charCodeAt(0) + index);
			var letteredIcon = new GIcon(baseIcon);
			letteredIcon.image = (markerImage) ? markerImage : "http://www.google.com/mapfiles/marker.png";
			
			// Set up our GMarkerOptions object
			markerOptions = {
				icon: letteredIcon
			};
			var point = new GLatLng(retailer.lat, retailer.lon);
			var marker = new GMarker(point, markerOptions);
			
			GEvent.addListener(marker, "click", function(){
				var infoHtml = "<div class=\"map-overlay\">";
				infoHtml += "<b>" + retailer.name + "</b><br />";
				infoHtml += retailer.address + "<br />";
				if (retailer.phone) {
					infoHtml += retailer.phone + "<br />";
				}
				if (retailer.email) {
					infoHtml += "<a href=\"mailto:" + retailer.email + "\">" + retailer.email + "</a><br />";
				}
				if (retailer.website) {
					infoHtml += "<a href=\"" + retailer.website + "\" target=\"_blank\">" + retailer.website + "</a><br />";
				}
				infoHtml += "</div>";
				marker.openInfoWindowHtml(infoHtml);
			});
			return marker;
		}
		
		r = m.retailers;
		for (i = 0; i < r.length; i++) {
			map.addOverlay(createMarker(r[i], i));
		}
		m.center.name = "You Are Here";
		map.addOverlay(createMarker(m.center, i, 'http://maps.google.com/mapfiles/arrow.png'));
		
	}
	
	function fillNavigation(m){
		$('#retailer-start-address').val(m.center.address);
		var list = $('<div class="retailers-list" style="overflow-x: hidden; overflow-y: auto; height: '+(600-$('#retailer-search').get(0).offsetHeight)+'px; clear: left;" />');
		//$(list).append($('<div class="retailer">Current Address:<br />'+m.center.address+'</div>'));
		r = m.retailers;
		for (i = 0; i < r.length; i++) {
			var item = '<div class="retailer" lat="' + r[i].lat + '" lon="' + r[i].lon + '" point="retailer-' + r[i].id + '">';
			item += '<h3 class="retailerName">' + r[i].name + '</h3>';
			item += '<div class="retailerAddress">' + r[i].address + '</div>';
			if (r[i].phone) {
				item += '<div class="retailerPhone">' + r[i].phone + '</div>';
			}
			if (r[i].email) {
				item += '<div><a class="retailerEmail" href="mailto:' + r[i].email + '">' + r[i].email + '</a><div>';
			}
			if (r[i].website) {
				item += '<div><a class="retailerWebsite" href="' + r[i].website + '" target="_blank">' + r[i].website + '</a></div>';
			}
			//item += '<a href="#" onclick="var directionsPanel = \'directions-'+r[i].id+'\'; $(\'#directionsPanel\').show(); var directions = new GDirections(map, document.getElementById(directionsPanel)); directions.load(\'from: '+m.center.address+' to: '+r[i].address+'\'); $(directionsPanel).prepend($(\'<a>hide</a>\')); $(this).hide(); return false;">Get Directions</a>';
			//item += '<div id="directions-'+r[i].id+'" style="padding: 10px; width: 90%; text-align: center;"></div>'
			item += '</div>';
			$(list).append($(item));
			$('h3', list).click(function(){
				var lon = $(this).parent().attr('lon');
				var lat = $(this).parent().attr('lat');
				map.panTo(new GLatLng(lat, lon))
			})
		}
		$('#retailer-map-navigation').html(list);
	}
})(jQuery);