<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Google Map</title>
  <script src="https://opn-speridian.rightnowdemo.com/euf/assets/themes/311/js/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src='http://maps.google.com/maps/api/js?sensor=true&libraries=geometry,places'></script>

 <style>
      html, body, #map-canvas {
        height: 100%;
        margin: 0px;
        padding: 0px
      }
      .controls {
        margin-top: 16px;
        border: 1px solid transparent;
        border-radius: 2px 0 0 2px;
        box-sizing: border-box;
        -moz-box-sizing: border-box;
        height: 32px;
        outline: none;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
      }

      #pac_input {
        background-color: #fff;
        padding: 0 11px 0 13px;
        width: 400px;
        font-family: Roboto;
        font-size: 15px;
        font-weight: 300;
        text-overflow: ellipsis;
      }

      #pac_input:focus {
        border-color: #4d90fe;
        margin-left: -1px;
        padding-left: 14px;  /* Regular padding-left + 1. */
        width: 401px;
      }

      .pac-container {
        font-family: Roboto;
      }

      #type-selector {
        color: #fff;
        background-color: #4d90fe;
        padding: 5px 11px 0px 11px;
      }

      #type-selector label {
        font-family: Roboto;
        font-size: 13px;
        font-weight: 300;
      }
	  
	  #panel {
        position: absolute;
        bottom: 10px;
        left: 50%;
        margin-left: -300px;
        z-index: 5;
        background-color: #fff;
        padding: 5px;
        border: 1px solid #999;
		font-family: Roboto,Arial,sans-serif;
		font-size: 11px;
		font-weight: 400;
      }
	  
	 #side_bar {
        position: absolute;
        top: 30px;
        right: 5px;
        margin-left: -10px;
        z-index: 5;
        background-color: #fff;
        padding: 10px;
        border: 1px solid #999;
		font-family: Roboto,Arial,sans-serif;
		font-size: 11px;
		font-weight: 400;
		overflow-y:auto;
		height:83%;
		width:200px;
		display:none;
      }

	  
      #target {
        width: 345px;
      }
  
    </style>


</head>
<body>
<div id="map_canvas" style="width:100%;height:100%;"></div>
   <input id="pac_input" class="controls" type="text" placeholder="Search Box">
	
	<div id="panel">
	<table style="borders:none">
		<tr>
			<td><label for="addrValidation">Address Verification</label></td>
			<td><input id="addrValidation" type="text" readonly></td>
			<td><label for="PoliceDist">Police Precinct</label></td>
			<td><input id="PoliceDist" type="text" style = "width: 26px" readonly><br></td>
			<td><label for="CouncilDist">Council</label></td>
			<td><input id="CouncilDist" type="text" style = "width: 80px" readonly><br></td>
		
		</tr>
		<tr>
			<td><label for="Neighborhood">Neighborhood</label></td>
			<td><input id="Neighborhood" type="text" readonly></td>
			<td><label for="FireDist">Fire District</label></td>
			<td><input id="FireDist" type="text" style = "width: 26px" readonly></td>
			<td><label for="TrashPickup">Trash Pickup</label></td>
			<td><input id="TrashPickup" type="text" style = "width: 80px" readonly></td>
			</tr>
	</table>
    </div>

	<div id="side_bar">
	<table style="borders:none">
			<tr>
				<td><u><strong style="color:red;">Similar Service Requests</strong></u></td>
			</tr>
			<tr>
				<td><div id="side_bar_content"></div></td>
			</tr>
	</table></div>
	
    
<script>
var markers = [];
var marker;
var placeMarker; 
var map;
var searchBox;
var geocoder;
var addrJSON;
var incidentList;
var service;
var currentRefNum;
var addrLatLng;


/*var map = new google.maps.Map(document.getElementById('map_canvas'), {
		center: new google.maps.LatLng(36.8461478,-76.29059540000003),
		zoom: 12,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	  });
geocoder = new google.maps.Geocoder();*/
geocoder = new google.maps.Geocoder();
function initialize() {
	
   var defaultCenter = new google.maps.LatLng(41.5050966,-81.6936405);
  map = new google.maps.Map(document.getElementById("map_canvas"), {
    mapTypeId: google.maps.MapTypeId.ROADMAP,
	center: defaultCenter,
	zoom:15
  });
	

  // Create the search box and link it to the UI element.
  var input = /** @type {HTMLInputElement} */(
      document.getElementById("pac_input"));
  map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
  searchBox = new google.maps.places.SearchBox(
    /** @type {HTMLInputElement} */(input));
	
  service = new google.maps.places.PlacesService(map);
  
  
  //Set address to incident address if available
  if(window.external.Incident && window.external.Incident.GetCustomFieldByName("c$location") ){
	 document.getElementById("pac_input").value = window.external.Incident.GetCustomFieldByName("c$location");
	 google.maps.event.trigger(searchBox, 'places_changed');	
	
	currentRefNum = window.external.Incident.RefNum;
	addrLatLng =  new google.maps.LatLng(window.external.Incident.GetCustomFieldByName("c$latitude"), window.external.Incident.GetCustomFieldByName("c$longitude"));
	var request = {
		location:addrLatLng,
		radius: '50',
		query: window.external.Incident.GetCustomFieldByName("c$location")
	};
	service.textSearch(request,callback);
  }

  // Listen for the event fired when the user selects an item from the
  // pick list. Retrieve the matching places for that item.
  google.maps.event.addListener(searchBox, "places_changed", function() {
	clearOverlays();
	setPlaces(searchBox.getPlaces());
  });  // [END region_getplaces]

  // Bias the SearchBox results towards places that are within the bounds of the
  // current map"s viewport.
  google.maps.event.addListener(map, "bounds_changed", function() {
    var bounds = map.getBounds();
    searchBox.setBounds(bounds);
  });
  
  google.maps.event.addListener(map, "click", function(event) {
	clearOverlays();
	getMarkerAddress(event.latLng);
  });	
}

function setPlaces(places) {

  // For each place, get the icon, place name, and location.
  var bounds = new google.maps.LatLngBounds();
  for (var i = 0, place; place = places[i]; i++) {
      // Create a marker for each place.
	  setMarker(place);

	  if(i == 0){
		//console.log("Place_ID= "+ place.place_id + " Address = " + place.formatted_address);
		//getGISData(place.geometry.location,place.place_id);
		document.getElementById("addrValidation").value = "Valid";
			document.getElementById("PoliceDist").value = "8";
			document.getElementById("CouncilDist").value = "10";
			document.getElementById("FireDist").value = "5";
			document.getElementById("TrashPickup").value = "Tuesday";
			//document.getElementById("Neighborhood").value = "Queen St";
			populateWorkspace(place.geometry.location);
		getNearbyIncidents(place.geometry.location, bounds);
		
		if(place.address_components) {
			parseAddress(place.address_components);
		}
	    map.setZoom(17);
        map.setCenter(placeMarker.getPosition());
	  }
  }
} // setPlaces


//This function is used to position the marker at the point where the map was clicked. If a marker object already exists then the position of the marker is updated, else a new marker is created at the location. It then runs the getMarkerAddress() function.
function setMarker(place) {
	if (placeMarker) {
        //if marker already was created change position
        placeMarker.setPosition(place.geometry.location);
    } else {
        //create a marker
        placeMarker = new google.maps.Marker({
            position: place.geometry.location,
            map: map,
			title: place.name,
            draggable: false,
			animation: google.maps.Animation.DROP,
			zIndex:500
        });
		placeMarker.addListener('click', toggleBounce);

	  }	
	}
	
function toggleBounce() {
  if (placeMarker.getAnimation() !== null) {
    placeMarker.setAnimation(null);
  } else {
    placeMarker.setAnimation(google.maps.Animation.BOUNCE);
  }
}

function  markerImage(categoryId) {
		switch (Number(categoryId)) {
			case 294: // Potholes
				return "1F14F4";
			case 299: // Garbage collection
				return "35DADF";
			case 296: // Noise complaint
				return "DFDA35";
			case 295: // street light
				return "355ADF";
			case 303: // Abandoned vehicle
				return "5ADF35";
			case 304: // Illegal Parking
				return "DF35A1";
			case 305: // Lost and found
				return "C3B9BF";
			/*case 175: // General Suggestion/Complaint
				return "/euf/assets/images/maps/information.png";
			case 173: // Health Care Inquiry
				return "/euf/assets/images/maps/firstaid.png";
			case 177: // Heat Issues
				return "/euf/assets/images/maps/sauna.png";
			case 169: // Inspections & Appointments
				return "/euf/assets/images/maps/information.png";
			case 117: // Licenses, Permits & Certificates
				return "/euf/assets/images/maps/information.png";
			case 140: // Make a Payment
				return "/euf/assets/images/maps/information.png";
			case 162: // Taxi Issues
				return "/euf/assets/images/maps/taxi.png";
			case 163: // Noise Complaint
				return "/euf/assets/images/maps/audio.png";
			case 154: // Parking Rules & Information
				return "/euf/assets/images/maps/parking-meter-export.png";
			case 159: // Street Sign Repair
				return "/euf/assets/images/maps/stop.png";
			case 160: // Towing
				return "/euf/assets/images/maps/towtruck.png";
			case 164: // Water Issues
				return "/euf/assets/images/maps/drinkingwater.png";*/
			default:
				return "7DAFCB";
		}
	}	
	
//This function takes the latlng of the marker"s position and uses Google"s geocoder to convert this into an address. The address string is then passed to the element textbox in the HTML.
function getMarkerAddress(latlng){
	geocoder.geocode({"latLng": latlng}, function(results, status){
		if(status == google.maps.GeocoderStatus.OK){
			if(results[0]){
				//console.log("Address Geocoding: " + results[0].formatted_address);
				document.getElementById("pac_input").value = results[0].formatted_address;
				
				var request = {
					location:latlng,
					radius: '50',
					query: results[0].formatted_address
				};
				service.textSearch(request,callback);
				
				//google.maps.event.trigger(searchBox, 'places_changed');
				//getGISData(latlng,place.place_id);
				//parseAddress(results[0].address_components);
			}
			else{
			alert("No Results found");
			}
		}
		else{
		//alert("Geocoder failed due to: " + status);
		}
	});
}

//callback function for textSearchResults
function callback(results,status){
    if (status == google.maps.places.PlacesServiceStatus.OK) {
	 setPlaces(results);
    }
	else {
	//console.log("Text Search Error Code:" + status);
	}
	
}
//Removes the overlays from the map, but keeps them in the array
function clearOverlays() {
  if (markers) {
    for (i in markers) {
      markers[i].setMap(null);
    }
  }
}
function getParameter(theParameter) { 
  /*var params = window.location.search.substr(1).split('&');
 
  for (var i = 0; i < params.length; i++) {
    var p=params[i].split('=');
	if (p[0] == theParameter) {
	  return decodeURIComponent(p[1]);
	}
  }
  return false;*/
  return decodeURIComponent(window.external.Incident.Product);

}

function getNearbyIncidents(location, bounds){ 
  var loadURL = "related_incidents_cs.php";
  var pdt = getParameter("pdt_id");
  if (pdt){
	loadURL += "?ProductId="+pdt+"&i_id=" + window.external.Incident.ID;
  }
	 
  /*if(currentRefNum){
	loadURL += "/refNum/"+currentRefNum;
  }*/
  
  var getParms = "";
  $('#side_bar_content').empty();
  var sidebarContent = document.getElementById("side_bar_content");

  jQuery.support.cors = true;
	$.ajax({
		type: "GET",
		crossDomain: true,
		url: loadURL,
		cache: false,
		data: getParms,
        datatype: "html",
		success: function(data){	
			
			//var html = $(data).find("#incidents").get(0);			
			incidentList = $.parseJSON(data);
			//console.log("incidentlist: "+incidentList);
			var rad = 1609.34;
			//add markers to map
			$.each( incidentList, function(i,value){
				var latto = new google.maps.LatLng(parseFloat(value.lat),parseFloat(value.lng));
				if(value.street && google.maps.geometry.spherical.computeDistanceBetween(addrLatLng, latto) <= rad)
				{
					//var rand = Math.floor(Math.random() * (1000000 - 1) + 1);
					
				//$.getJSON('https://maps.googleapis.com/maps/api/geocode/json?sensor=false&address='+encodeURIComponent(value.street),function(dataaa) {
					
				/*geocoder.geocode( { 'address':value.street}, function(results, status) {	
					//var status = dataaa.status;
					if(status == google.maps.GeocoderStatus.OK)
					{
						//var results = dataaa.results;
					var latt = results[0].geometry.location.lat;
					var lng = results[0].geometry.location.lng;
					
					//var pinColor = markerImage(value.pdtId);
					//var pinImage = new google.maps.MarkerImage("http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|" + pinColor,new google.maps.Size(21, 34),new google.maps.Point(0,0),new google.maps.Point(10, 34)); 
					var nearbyLocation = new google.maps.LatLng(latt,lng);
					var nearbyMarker = new google.maps.Marker({
						position: nearbyLocation,
						map: map,
						icon:'http://opn-speridian.rightnowdemo.com/euf/assets/themes/311/images/maps/blue_pin.png',
						title: "Ref# "+value.referenceNumber
						});*/
				
				var param = value.street + "|" +value.referenceNumber;
				var content = '<div id="infoWindow" style="border-bottom:2px solid black;">';
				if (incidentList[incidentList .length - 1].parent == value.Id) {
					content += '<input type="radio" name="incident" value ="' + value.Id + '" checked><strong>Incident ID:</strong> ' + value.Id;
				} else {
					content += '<input type="radio" name="incident" value ="' + value.Id + '"><strong>Incident ID:</strong> ' + value.Id;
				}
				content +=   '<br><strong>Address:</strong> ' + value.street + 
							  '<br><strong>Reference #:</strong> '+ value.referenceNumber +
							  '<br><strong>Created:</strong> ' + value.created +
							  '<br><strong>Status:</strong> ' + value.status +							  
							  '<br><strong>Contact Name:</strong>' + value.last + ' ' + value.first +'<br><strong>Incident Type:</strong>'+ value.subject +'</div>'  ;	
							  
							  //'<br><a href="javascript:void(0)" onClick="linkRequest(\''+ param +'\')">Link Request</a>' +
				/*var infoWindow = new google.maps.InfoWindow({
					content: content,
					maxWidth: 200
				});
				google.maps.event.addListener(nearbyMarker,  'click',  function() {
					   infoWindow.open(map,  nearbyMarker);
				});
				google.maps.event.addListener(nearbyMarker,  'mouseover',  function() {
					   infoWindow.open(map,  nearbyMarker);
				});
				google.maps.event.addListener(nearbyMarker,  'mouseout',  function() {
					   infoWindow.close(map,  nearbyMarker);
				});				
				markers.push(nearbyMarker);*/
				var div = document.createElement('div');
				  div.innerHTML = content;
				  //div.style.cursor = 'pointer';
				  div.style.marginBottom = '5px'; 
				  
				/*google.maps.event.addDomListener(div, 'click', function() {
				google.maps.event.trigger(marker, 'click');
			   });
			   
			  google.maps.event.addDomListener(div, 'mouseover', function() {
				div.style.backgroundColor = '#eee';
			  });
			  
			  google.maps.event.addDomListener(div, 'mouseout', function() {
				div.style.backgroundColor = '#fff';
				});
				bounds.extend(nearbyLocation);*/

				//var sidebarEntry = createSidebarEntry(content);
				sidebarContent.appendChild(div);
				document.getElementById("side_bar").style.display = 'block';
				//}
				//});
				
				
				}
			});
			addEvent();
		},
		
		error: function( xhr, status, errorThrown ) {
			//alert( "Incident List call failed\n Error: " + errorThrown + "\n Status: " + status		);
			//console.log( "Error: " + errorThrown );
			//console.log( "Status: " + status );
			//console.dir( xhr );
		}
	}); 

} 
//getNearbyIncidents

function getAjax(loadURL) {
	$.ajax({
		type: "GET",
		crossDomain: true,
		url: loadURL,
		cache: false,
		data: "",
		success:function(response){
		},
		error:function(response){
			alert("error");
		}
	});
}
function addEvent() {
	var loadURL = "update_duplicate_incidents_cs.php";
	$("input:radio").bind("change",function(){
		var flag = false;
		if(this.checked) {
			flag = true;
		}
		loadURL += "?parentincidentid=" + $(this).val() + "&flag=" + flag +"&incidentid="+window.external.Incident.ID;
		if (window.confirm("Would you like to make this Ref No# " + $(this).val() + " as parent ?")) {
			getAjax(loadURL);
		}
	});
}


// Add incidents to sidebar
function createSidebarEntry(content) {
  var div = document.createElement('div');
  div.innerHTML = content;
  div.style.cursor = 'pointer';
  div.style.marginBottom = '5px'; 
 
  /*google.maps.event.addDomListener(div, 'click', function() {
    google.maps.event.trigger(marker, 'click');
  });
  google.maps.event.addDomListener(div, 'mouseover', function() {
    div.style.backgroundColor = '#eee';
  });
  google.maps.event.addDomListener(div, 'mouseout', function() {
    div.style.backgroundColor = '#fff';
  });*/
  return div;
} //createSidebarEntry


 //Get GIS Info for current address
function getGISData(location,placeID){ 
 var loadURL = "http://311demo.rightnowdemo.com/app/utils/maps/GISData/"+placeID;
  var getParms = "";

  jQuery.support.cors = true;
	$.ajax({
		type: "GET",
		crossDomain: true,
		url: loadURL,
		cache: false,
		data: getParms,
        datatype: "html",
		success: function(data){		
			var html = $(data).find("#places").get(0);			
			gisData = $.parseJSON(html.innerText);
			//console.log(gisData);
        	
			//Update GIS Fields
			document.getElementById("addrValidation").value = "Valid";
			document.getElementById("PoliceDist").value = gisData[0];
			document.getElementById("CouncilDist").value = gisData[0].councilDistrict;
			document.getElementById("FireDist").value = gisData[0].fireDistrict;
			document.getElementById("TrashPickup").value = gisData[0].TrashPickup;
			document.getElementById("Neighborhood").value = gisData[0].neighborhood;
		},
		
		error: function( xhr, status, errorThrown ) {
			//alert( "Incident List call failed\n Error: " + errorThrown + "\n Status: " + status		);
			//console.log( "Error: " + errorThrown );
			//console.log( "Status: " + status );
			//console.dir( xhr );
		}
	}); 
		
		//if(window.external.Incident) {
		var c = window.external.Incident;
		//console.log(c);
		c.SetCustomFieldByName("c$location", document.getElementById("pac_input").value);
		//alert("554");
		c.SetCustomFieldByName("c$latitude", location.lat().toString() );		
		c.SetCustomFieldByName("c$longitude", location.lng().toString() );	
		//c.SetCustomFieldByName("c$incident_city", "Calgary");
		//c.SetCustomFieldByName("c$incident_state_province", "AB");
		//}

	populateWorkspace(location);

	}	

/*function linkRequest(refNum) {
	//console.log("linkRequest: " + refNum);
	
	if(window.external.Incident) {
		var c = window.external.Incident;
		c.SetCustomFieldByName("c$duplicate_id", refNum.toString() );
		c.SetCustomFieldByName("c$location", document.getElementById("pac_input").value);
	}
}	*///linkRequest
	
	function linkRequest(param)
{
	var details = param.split('|');
	//alert(details[1]);
	
	geocoder.geocode( { 'address':details[0]}, function(results, status) {
		if(status == google.maps.GeocoderStatus.OK)
					{
					
						//var results = dataaa.results;
					var latt = results[0].geometry.location.lat;
					var lng = results[0].geometry.location.lng;
					var nearbyLocation = new google.maps.LatLng(latt,lng);
					
					var nearbyMarker = new google.maps.Marker({
						position: nearbyLocation,
						map: map,
						icon:'http://opn-speridian.rightnowdemo.com/euf/assets/themes/311/images/maps/blue_pin.png',
						title: "Ref# "+details[1]
						});
						var infoWindow = new google.maps.InfoWindow({
					content: results[0].formatted_address,
					maxWidth: 200
				});
				google.maps.event.addListener(nearbyMarker,  'click',  function() {
					   infoWindow.open(map,  nearbyMarker);
				});
				google.maps.event.addListener(nearbyMarker,  'mouseover',  function() {
					   infoWindow.open(map,  nearbyMarker);
				});
				google.maps.event.addListener(nearbyMarker,  'mouseout',  function() {
					   infoWindow.close(map,  nearbyMarker);
				});				
				markers.push(nearbyMarker);
					}
	});
}
 function parseAddress(addressComponent){
 		var arrAddress = addressComponent;
		var street='';
		var city='';
		var country='';
		var state='';
		var zip='';
		var streetNumber='';

		// iterate through address_component array
		$.each(arrAddress, function (i, address_component) {			
			if (address_component.types[0] == "street_number"){ 
 				streetNumber = address_component.long_name;
			}		
			if (address_component.types[0] == "route"){
				street = address_component.long_name;
			}
			if (address_component.types[0] == "locality"){
				city = address_component.long_name;
			}
			if (address_component.types[0] == "administrative_area_level_1"){
				state = address_component.long_name;
			}
			if (address_component.types[0] == "country"){ 
				country = address_component.long_name;
			}
			if (address_component.types[0] == "postal_code"){ 
				zip = address_component.long_name;
			}
			//return false; // break the loop   
		});
		//console.log("Address Street parse: " + streetNumber+ " " + street);	

		//if(window.external.Incident) {
			var c = window.external.Incident;
			
			//alert(streetNumber);
			//alert(street);
			c.SetCustomFieldByName("c$location", streetNumber + ' ' + street);
			//alert("652");
			c.SetCustomFieldByName("c$city", city);		
			c.SetCustomFieldByName("c$state_province", state );	
			c.SetCustomFieldByName("c$zip", zip);
			c.SetCustomFieldByName("c$address",city);
			

		//}
		
		/* set values on AAQ Form
		document.getElementsByName('Incident.CustomFields.c.incident_street_address')[0].value = streetNumber + ' ' + street;
		document.getElementsByName('Incident.CustomFields.c.incident_city')[0].value = city;
		document.getElementsByName('Incident.CustomFields.c.incident_state_province')[0].value = state;
		document.getElementsByName('Incident.CustomFields.c.incident_zip_postal_code')[0].value = zip;
		*/
 }
	
	
function populateWorkspace(location,addressComponent){
	
	//alert('populate');
	var street='';
		var city='';
		var country='';
		var state='';
		var zip='';
		var streetNumber='';
		var neighborhood = '';
		//alert(document.getElementById("pac_input").value);
	geocoder.geocode( { 'address':document.getElementById("pac_input").value}, function(results, status) {
		
		
      if (status == google.maps.GeocoderStatus.OK) {
		  
		  
		  //alert('ok');
        map.setCenter(results[0].geometry.location);
        
		//$.each(arrAddress, function (i, address_component) {	
         // for each(var item in results[0].address_components)
		 // {
			// alert(item[i].types[0] );
			 
		 // }

		
			if (results[0].address_components[0].types[0] == "street_number"){ 
			    //alert('geo1');
 				streetNumber = results[0].address_components[0].long_name;
			}		
			if (results[0].address_components[1].types[0] == "route"){
				//alert('geo2');
				street = results[0].address_components[1].long_name;
			}
			//alert(results[0].address_components[2].types[0]);
			if (results[0].address_components[2].types[0] == "neighborhood"){ 
			//alert('geo3');
				neighborhood = results[0].address_components[2].long_name;
			}
			if (results[0].address_components[3].types[0] == "locality"){
				//alert('geo4');
				city = results[0].address_components[3].long_name;
			}
			if (results[0].address_components[5].types[0] == "administrative_area_level_1"){
				//alert('geo5');
				state = results[0].address_components[5].long_name;
			}
			if (results[0].address_components[6].types[0] == "country"){ 
			//alert('geo6');
				country = results[0].address_components[6].long_name;
			}
			if (results[0].address_components[7].types[0] == "postal_code"){ 
			//alert('geo7');
				zip = results[0].address_components[7].long_name;
			}
			
			
			
			
			
			//if(window.external.Incident) {
				//alert('incident');
				
				
				var c = window.external.Incident;
				//alert("738");
				c.SetCustomFieldByName("c$location", document.getElementById("pac_input").value);
				c.SetCustomFieldByName("c$latitude", location.lat().toString());		
				c.SetCustomFieldByName("c$longitude", location.lng().toString() );
				c.SetCustomFieldByName("c$street_number", streetNumber);
				//c.SetCustomFieldByName("c$prefix1_txt", addrJSON.Prefix);
				c.SetCustomFieldByName("c$street_name", street);
				//c.SetCustomFieldByName("c$suffix1_txt", addrJSON.Suffix);
				//c.SetCustomFieldByName("c$st_type1_txt", addrJSON.StreetType);
				c.SetCustomFieldByName("c$city",city);
				c.SetCustomFieldByName("c$state_province",state);
				c.SetCustomFieldByName("c$zip",zip);
				//c.SetCustomFieldByName("c$police_district", addrJSON.PoliceDistt);
				c.SetCustomFieldByName("c$councildistrict",document.getElementById("CouncilDist").value);		
				c.SetCustomFieldByName("c$neighbourhood",neighborhood); 
			//}
			if(state)
			{
				document.getElementById("addrValidation").value = "Valid";
				//document.getElementById("PoliceDistt").value = addrJSON.PoliceDistt;
				//document.getElementById("CouncilDistt").value = addrJSON.CouncilDistt;
				document.getElementById("Neighborhood").value = neighborhood;
			}
			//return false; // break the loop   
		//});
      } else {
        //alert("Geocode was not successful for the following reason: " + status);
      }
    });
	
		
		
	
	
	//Update Google maps fields
	
		
	
	
}

google.maps.event.addDomListener(window, "load", initialize);


</script>

</body>

</html>