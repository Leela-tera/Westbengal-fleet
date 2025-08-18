@extends('admin.layout.base')

@section('title', 'Map View ')

@section('content')
<div class="content-area py-1">
        <div class="container-fluid ">
        	<div class="box box-block bg-white"> 
        <form action="{{route('admin.tracklocations')}}" method="GET">
            <div class="row">
                <div class="col-xs-2">
                   <select class="form-control selectpicker" data-show-subtext="true" data-live-search="true" name="district_id" required>
                   	<option value="">Please Select District</option>
                    @foreach($districts as $district)
                    <option value="{{$district->id}}" @if(Request::get('district_id')) @if(@Request::get('district_id') == $district->id) selected @endif @endif>{{$district->name}} </option> 
                   @endforeach 
                  </select>
                </div>
				
				
				 <div class="col-xs-2">
                   <select class="form-control selectpicker" data-show-subtext="true" data-live-search="true" name="provider_id" required>
                   	<option value="">Please Select Person</option>
                    @foreach($providers as $provider)
                    <option value="{{$provider->id}}" @if(Request::get('provider_id')) @if(@Request::get('provider_id') == $provider->id) selected @endif @endif>{{$provider->first_name}} {{$provider->last_name}}</option> 
                   @endforeach 
                  </select>
                </div>
				
				<div class="form-group row col-md-3">
                            <label for="name" class="col-xs-4 col-form-label">Date From</label>
                            <div class="col-xs-8">
                                <input class="form-control" type="date" name="from_date" required placeholder="From Date">
                            </div>
                </div>
                            
                <div class="form-group row col-md-3">
                            <label for="email" class="col-xs-4 col-form-label">Date To</label>
                            <div class="col-xs-8">
                                <input class="form-control" type="date" required name="to_date" placeholder="To Date">
                            </div>
                  </div>
                
                <div class="col-xs-2">
                    <button type="submit" class="form-control btn btn-primary">Fetch</button>
                </div>  
            </div>
        </form>
        </div>

          <div class="box box-block bg-white">
		     @if((Request::get('from_date') != '') && (Request::get('to_date') != ''))
            <h5 class="mb-1" style="color:#0275d8;"> Attendance Map View From {{Request::get('from_date')}} To {{Request::get('to_date')}}</h5>
		     @else
			 <h5 class="mb-1" style="color:#0275d8;">Map View of All Locations and Users</h5>	 
			 @endif
            <div class="row">
            	 <input type="hidden" name="districtdata" value="{{$district_id}}" id="districtdata" />
				 <input type="hidden" name="fromdate" value="{{$from_date}}" id="fromdate" />
				 <input type="hidden" name="todate" value="{{$to_date}}" id="todate" />
				 <input type="hidden" name="providerid" value="{{$provider_id}}" id="providerid" />
				 
                <div class="col-xs-12">
                    <div id="map"></div>
                    <div id="legend"><h3>Note: </h3></div>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection

@section('styles')
<style type="text/css">
    #map {
        height: 100%;
        min-height: 500px;
    }
    
    #legend {
        font-family: Arial, sans-serif;
        background: rgba(255,255,255,0.8);
        padding: 10px;
        margin: 10px;
        border: 2px solid #f3f3f3;
    }

    #legend h3 {
        margin-top: 0;
        font-size: 16px;
        font-weight: bold;
        text-align: center;
    }

    #legend img {
        vertical-align: middle;
        margin-bottom: 5px;
    }
</style>
@endsection

@section('scripts')
<script>
    var map;
    var users;
    var districtdata = document.getElementById('districtdata').value;
	var fromdate = document.getElementById('fromdate').value;
	var todate = document.getElementById('todate').value;
	var providerid = document.getElementById('providerid').value;
    var providers;
    var ajaxMarkers = [];
    var googleMarkers = [];
    var mapIcons = {
        user: '{{ asset("asset/img/marker-user.png") }}',
        active: '{{ asset("asset/img/marker-user.png") }}',
        riding: '{{ asset("asset/img/marker-user.png") }}',
        offline: '{{ asset("asset/img/map-marker-red.png") }}',
        unactivated: '{{ asset("asset/img/marker-plus.png") }}',
        gpsactive: '{{ asset("asset/img/marker-home.png") }}',
        gpsoffline: '{{ asset("asset/img/marker-dhome.png") }}',


    }

    function initMap() {
        map = new google.maps.Map(document.getElementById('map'), {
            center: {lat: 20.8444, lng: 85.1511},
            zoom: 8,
            minZoom: 1
        });

        setInterval(ajaxMapData, 3000);

        var legend = document.getElementById('legend');
       

         var div = document.createElement('div');
        div.innerHTML = '<img src="' + mapIcons['gpsoffline'] + '"> ' + 'Gps offline';
        legend.appendChild(div);
        
        var div = document.createElement('div');
        div.innerHTML = '<img src="' + mapIcons['gpsactive'] + '"> ' + 'Gps online';
        legend.appendChild(div);

        map.controls[google.maps.ControlPosition.RIGHT_BOTTOM].push(legend);
        
        google.maps.Map.prototype.clearOverlays = function() {
            for (var i = 0; i < googleMarkers.length; i++ ) {
                googleMarkers[i].setMap(null);
            }
            googleMarkers.length = 0;
        }

    }

    function ajaxMapData() {
        map.clearOverlays();
        $.ajax({
            url: '/admin/alltrackmap/ajax?district_id='+ districtdata+'&provider_id='+providerid +'&from_date='+fromdate+'&to_date='+todate,
            dataType: "JSON",
            headers: {'X-CSRF-TOKEN': window.Laravel.csrfToken },
            type: "GET",
            success: function(data) {
                console.log('Ajax Response', data);
                ajaxMarkers = data;
            }
        });

        ajaxMarkers ? ajaxMarkers.forEach(addMarkerToMap) : '';
    }

    function addMarkerToMap(element, index) {
    	var baddress =element.gp_name;
        var utype =  "location";
        marker = new google.maps.Marker({
            position: {
                lat: element.latitude,
                lng: element.longitude
            },
            id: element.id,
            map: map,
            title: element.gpname + "\n" + baddress + "\n" +utype,
            icon : mapIcons[element.astatus],
        });

        googleMarkers.push(marker);

        google.maps.event.addListener(marker, 'click', function() {
            //window.location.href = '/admin/' + element.service ? 'provider' : 'user' + '/' +element.user_id;
            //window.location.href = '/admin/currentlocation/'+element.id;
        });
    }
</script>
<script>
filterSelection("all")
function filterSelection(c) {
  var x, i;
  x = document.getElementsByClassName("filterDiv");
  if (c == "all") c = "";
  for (i = 0; i < x.length; i++) {
    w3RemoveClass(x[i], "show");
    if (x[i].className.indexOf(c) > -1) w3AddClass(x[i], "show");
  }
}

function w3AddClass(element, name) {
  var i, arr1, arr2;
  arr1 = element.className.split(" ");
  arr2 = name.split(" ");
  for (i = 0; i < arr2.length; i++) {
    if (arr1.indexOf(arr2[i]) == -1) {element.className += " " + arr2[i];}
  }
}

function w3RemoveClass(element, name) {
  var i, arr1, arr2;
  arr1 = element.className.split(" ");
  arr2 = name.split(" ");
  for (i = 0; i < arr2.length; i++) {
    while (arr1.indexOf(arr2[i]) > -1) {
      arr1.splice(arr1.indexOf(arr2[i]), 1);     
    }
  }
  element.className = arr1.join(" ");
}

// Add active class to the current button (highlight it)
var btnContainer = document.getElementById("myBtnContainer");
var btns = btnContainer.getElementsByClassName("btn");
for (var i = 0; i < btns.length; i++) {
  btns[i].addEventListener("click", function(){
    var current = document.getElementsByClassName("gpsactive");
    current[0].className = current[0].className.replace(" gpsactive", "");
    this.className += " gpsactive";
  });
}
</script>
<script src="//maps.googleapis.com/maps/api/js?key={{ Setting::get('map_key') }}&libraries=places&callback=initMap" async defer></script>
@endsection