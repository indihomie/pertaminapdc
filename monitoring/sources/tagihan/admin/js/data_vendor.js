function unsetOption(idOption)
{
    field = document.getElementById('inp['+idOption+']');
	for(var i=field.options.length-1; i>=1; i--){
		field.remove(i);
		jQuery("#inp\\["+idOption+"\\]").trigger("chosen:updated");
	}
}

function getKota(kodePropinsi, getPar)
{
    unsetOption('kodeKota');

	jQuery.post("ajax.php?par[mode]=kta&par[kodePropinsi]="+ kodePropinsi + getPar+"").done(function(result)
    {
        data = jQuery.parseJSON(result);
        for(var i=0; i<data.length; i++)
        {
            jQuery("#inp\\[kodeKota\\]").append('<option value="'+data[i].kodeData+'">'+data[i].namaData+'</option>');
            jQuery("#inp\\[kodeKota\\]").trigger("chosen:updated");
        }
    });
}

function chk(getPar){
	nomorSupplier=document.getElementById("inp[nomorSupplier]").value;		
	var xmlHttp = getXMLHttp();		
	xmlHttp.onreadystatechange = function(){	
		if(xmlHttp.readyState == 4 && xmlHttp.status==200){			
			if(xmlHttp.responseText){					
				alert(xmlHttp.responseText);										
			}else{
				if(validation(document.form)){
					document.getElementById("form").submit();
				}
			}
		}
	}
	
	xmlHttp.open("GET", "ajax.php?par[mode]=cek&inp[nomorSupplier]=" + nomorSupplier + getPar, true);
	xmlHttp.send(null);
	return false;
}

function update(getPar){
	kodePropinsi = document.getElementById('inp[kodePropinsi]').value;
	kodeKota = document.getElementById('inp[kodeKota]').value;
	nomorSupplier = document.getElementById('inp[nomorSupplier]').value;
	namaSupplier = document.getElementById('inp[namaSupplier]').value;
	aliasSupplier = document.getElementById('inp[aliasSupplier]').value;
	alamatSupplier = document.getElementById('inp[alamatSupplier]').value;
	teleponSupplier = document.getElementById('inp[teleponSupplier]').value;
	instagramSupplier = document.getElementById('inp[instagramSupplier]').value;
	emailSupplier = document.getElementById('inp[emailSupplier]').value;
	webSupplier = document.getElementById('inp[webSupplier]').value;		
	logoSupplier = document.getElementById('logoSupplier').files[0];	
	statusSupplier = document.getElementById('true').checked == true ? 't' : 'f';
		
	siupIdentity = document.getElementById('inp[siupIdentity]').value;
	siupIdentity_file = document.getElementById('siupIdentity_file').files[0];
	tdpIdentity = document.getElementById('inp[tdpIdentity]').value;
	tdpIdentity_file = document.getElementById('tdpIdentity_file').files[0];
	idIdentity = document.getElementById('inp[idIdentity]').value;
	idIdentity_file = document.getElementById('idIdentity_file').files[0];
	npwpIdentity = document.getElementById('inp[npwpIdentity]').value;
	npwpIdentity_file = document.getElementById('npwpIdentity_file').files[0];
	alamatIdentity = document.getElementById('inp[alamatIdentity]').value;		
	
	var xmlHttp=new XMLHttpRequest();
    xmlHttp.onreadystatechange=function(){
        if (xmlHttp.readyState==4 && xmlHttp.status==200){
        	if(xmlHttp.responseText) alert(xmlHttp.responseText);
        }
    }
	
	xmlHttp.open("POST","ajax.php?par[mode]=update" + getPar,true);
    xmlHttp.setRequestHeader("Enctype", "multipart/form-data")
    var formData = new FormData();
	
	formData.append("inp[kodePropinsi]", kodePropinsi);	
	formData.append("inp[kodeKota]", kodeKota);
	formData.append("inp[nomorSupplier]", nomorSupplier);
	formData.append("inp[namaSupplier]", namaSupplier);
	formData.append("inp[aliasSupplier]", aliasSupplier);
	formData.append("inp[alamatSupplier]", alamatSupplier);	
	formData.append("inp[teleponSupplier]", teleponSupplier);
	formData.append("inp[instagramSupplier]", instagramSupplier);
	formData.append("inp[emailSupplier]", emailSupplier);
	formData.append("inp[webSupplier]", webSupplier);    
	formData.append("logoSupplier", logoSupplier);
	formData.append("inp[statusSupplier]", statusSupplier);
		
	formData.append("inp[siupIdentity]", siupIdentity);
	formData.append("siupIdentity_file", siupIdentity_file);
	formData.append("inp[tdpIdentity]", tdpIdentity);
	formData.append("tdpIdentity_file", tdpIdentity_file);
	formData.append("inp[idIdentity]", idIdentity);
	formData.append("idIdentity_file", idIdentity_file);
	formData.append("inp[npwpIdentity]", npwpIdentity);
	formData.append("npwpIdentity_file", npwpIdentity_file);
	formData.append("inp[alamatIdentity]", alamatIdentity);	
	
    xmlHttp.send(formData);		
	
}

/*function getKota(getPar){
	kodePropinsi = document.getElementById('inp[kodePropinsi]');
	kodeKota = document.getElementById('inp[kodeKota]');
	var xmlHttp = getXMLHttp();		
	xmlHttp.onreadystatechange = function(){	
		if(xmlHttp.readyState == 4 && xmlHttp.status==200){			
			for(var i=kodeKota.options.length-1; i>=0; i--){
				kodeKota.remove(i);
			}
			if(xmlHttp.responseText){
				var arr = xmlHttp.responseText.split("\n");						
				var opt = document.createElement("OPTION");
				opt.value = "";		
				opt.text = "";
				kodeKota.options.add(opt);
				for(var i=0; i<arr.length; i++){							
					var opt = document.createElement("OPTION");
					var val = arr[i].split("\t");
					opt.value = val[0];		 
					opt.text = val[1];
					if(opt.value) kodeKota.options.add(opt);
				}
			}
		}
	}
	xmlHttp.open("GET", "ajax.php?par[mode]=kta&par[kodePropinsi]="+ kodePropinsi.value + getPar, true);
	xmlHttp.send(null);
	return false;
}*/

function getKota2(getPar){
	kodePropinsi = document.getElementById('aSearch');
	kodeKota = document.getElementById('bSearch');
	var xmlHttp = getXMLHttp();		
	xmlHttp.onreadystatechange = function(){	
		if(xmlHttp.readyState == 4 && xmlHttp.status==200){			
			for(var i=kodeKota.options.length-1; i>=0; i--){
				kodeKota.remove(i);
			}
			if(xmlHttp.responseText){
				var arr = xmlHttp.responseText.split("\n");						
				var opt = document.createElement("OPTION");
				opt.value = "";		
				opt.text = "";
				kodeKota.options.add(opt);
				for(var i=0; i<arr.length; i++){							
					var opt = document.createElement("OPTION");
					var val = arr[i].split("\t");
					opt.value = val[0];		 
					opt.text = val[1];
					if(opt.value) kodeKota.options.add(opt);
				}
			}
		}
	}
	xmlHttp.open("GET", "ajax.php?par[mode]=kta&par[kodePropinsi]="+ kodePropinsi.value + getPar, true);
	xmlHttp.send(null);
	return false;
}
function getProd(getPar){
	kodeProduk = document.getElementById('inp[kodeProduk]');
	kodeKategori = document.getElementById('inp[kodeKategori]');
	var xmlHttp = getXMLHttp();		
	xmlHttp.onreadystatechange = function(){	
		if(xmlHttp.readyState == 4 && xmlHttp.status==200){			
			for(var i=kodeKategori.options.length-1; i>=0; i--){
				kodeKategori.remove(i);
			}
			if(xmlHttp.responseText){
				var arr = xmlHttp.responseText.split("\n");						
				var opt = document.createElement("OPTION");
				opt.value = "";		
				opt.text = "";
				kodeKategori.options.add(opt);
				for(var i=0; i<arr.length; i++){							
					var opt = document.createElement("OPTION");
					var val = arr[i].split("\t");
					opt.value = val[0];		 
					opt.text = val[1];
					if(opt.value) kodeKategori.options.add(opt);
				}
			}
		}
	}
	xmlHttp.open("GET", "ajax.php?par[mode]=subk&par[kodeProduk]="+ kodeProduk.value + getPar, true);
	xmlHttp.send(null);
	return false;
}

function getProduk(){	
	document.getElementById("inp[kodeKategori]").value = "";
	document.getElementById("inp[namaKategori]").value = "";
}

function setProduk(kodeProduk, kodeKategori, getPar){
	var xmlHttp = getXMLHttp();
	xmlHttp.onreadystatechange = function(){
		if(xmlHttp.readyState == 4 && xmlHttp.status==200){	
			response = xmlHttp.responseText;
			if(response){
				namaKategori = response;
				parent.document.getElementById("inp[kodeProduk]").value = kodeProduk;
				parent.document.getElementById("inp[kodeKategori]").value = kodeKategori;				
				parent.document.getElementById("inp[namaKategori]").value = namaKategori;
				closeBox();
			}
		}
	}
	
	xmlHttp.open("GET", "ajax.php?par[mode]=setProduk&par[kodeProduk]=" + kodeProduk + "&par[kodeKategori]=" + kodeKategori + getPar, true);
	xmlHttp.send(null);
	return false;
}
/*

// google maps
var geocoder;
var map;
var marker;

function initialize() {
	var lat = document.getElementById('inp[latitudeAddress]').value;
	var lng = document.getElementById('inp[longitudeAddress]').value;

	geocoder = new google.maps.Geocoder();
	var latLng = new google.maps.LatLng(lat, lng);
	var myMapParams = { zoom: 16, center: latLng, mapTypeId: google.maps.MapTypeId.ROADMAP };
	map = new google.maps.Map(document.getElementById('mapCanvas'), myMapParams);
	var myMarkerParams = { position: latLng, map: map, draggable: true };
	marker = new google.maps.Marker(myMarkerParams);

	updateMarkerPosition(latLng);
	geocodePosition(latLng);

	google.maps.event.addListener(marker, 'dragstart', 
	function() {
		updateMarkerAddress('Dragging...,Dragging...');
	});

	google.maps.event.addListener(marker, 'drag', 
	function() {              
		updateMarkerPosition(marker.getPosition());
	});

	google.maps.event.addListener(marker, 'dragend', 
	function() {                
		geocodePosition(marker.getPosition());
	});
}

function setGeocode(getPar) {
	var kodeKota = document.getElementById('inp[kodeKota]').value;
	var xmlHttp = getXMLHttp();	
	xmlHttp.onreadystatechange = function(){	
		if(xmlHttp.readyState == 4 && xmlHttp.status==200){									
			if(xmlHttp.responseText){
				var alamatAddress = document.getElementById('inp[alamatAddress]').value;
				var address = alamatAddress.concat(',', xmlHttp.responseText, ',', 'ID');
				geocoder.geocode({ 'address': address },
				function(results, status) {
					if (status == google.maps.GeocoderStatus.OK) {
						marker.setMap(null);
						map.setCenter(results[0].geometry.location);
						var myMarkerParams = { position: results[0].geometry.location, map: map, draggable: true };
						marker = new google.maps.Marker(myMarkerParams);
						
						updateMarkerPosition(results[0].geometry.location);
						geocodePosition(results[0].geometry.location);
						
						google.maps.event.addListener(marker, 'dragstart',
						function() {
							updateMarkerAddress('Dragging...,Dragging...');
						});

						google.maps.event.addListener(marker, 'drag',
						function() {
							updateMarkerPosition(marker.getPosition());
						});

						google.maps.event.addListener(marker, 'dragend',
						function() {
							geocodePosition(marker.getPosition());
						});
					} else {
						alert('error ' + status);
					}
				});
			}
		}
	}
	xmlHttp.open("GET", "ajax.php?par[mode]=geo&par[kodeKota]="+ kodeKota + getPar, true);
	xmlHttp.send(null);
	return false;
}

function geocodePosition(pos) {
	geocoder.geocode({ latLng: pos },
	function(results) {
		if (results && results.length > 0) {
			updateMarkerAddress(results[0].formatted_address);
		} else {
			updateMarkerAddress('-,-');
		}
	});
}        

function updateMarkerPosition(latLng) {
	document.getElementById('inp[latitudeAddress]').value = latLng.lat();
	document.getElementById('inp[longitudeAddress]').value = latLng.lng();
}

function updateMarkerAddress(str) {
	var arrStr = str.split(',');
	document.getElementById('inp[alamatAddress]').value = arrStr[0];
}*/
