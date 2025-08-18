@extends('admin.layout.base')

@section('title', 'Map View ')

@section('content')

<div class="content-area py-1">
    <div class="container-fluid">
        	<div class="box box-block bg-white"> 
        <form action="{{route('admin.map.index')}}" method="GET">
            <div class="row">
                <div class="col-xs-4">
                   <select class="form-control selectpicker" data-show-subtext="true" data-live-search="true" name="district_id" required>
                   	<option value="">Please Select District</option>
                    @foreach($districts as $district)
                    <option value="{{$district->id}}" @if(Request::get('district_id')) @if(@Request::get('district_id') == $district->id) selected @endif @endif>{{$district->name}} </option> 
                   @endforeach 
                  </select>
                </div>
                
                <div class="col-xs-2">
                    <button type="submit" class="form-control btn btn-primary">Fetch</button>
                </div>  
            </div>
        </form>
        </div>
       
        <div class="box box-block bg-white">
            <h5 class="mb-1">Tracking View</h5>
            <div class="row">
            	 <input type="hidden" name="districtdata" value="{{$district_id}}" id="districtdata" />
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
    var providers;
    var ajaxMarkers = [];
    var googleMarkers = [];
    var mapIcons = {
        user: '{{ asset("asset/img/marker-user.png") }}',
        active: '{{ asset("asset/img/marker-user.png") }}',
        riding: '{{ asset("asset/img/marker-user.png") }}',
        offline: '{{ asset("asset/img/map-marker-red.png") }}',
        unactivated: '{{ asset("asset/img/marker-plus.png") }}'
    }

    function initMap() {
        map = new google.maps.Map(document.getElementById('map'), {
            center: {lat: 20.8444, lng: 85.1511},
            zoom: 8,
            minZoom: 1
        });

        setInterval(ajaxMapData, 3000);

        var legend = document.getElementById('legend');
       


        // var div = document.createElement('div');
        // div.innerHTML = '<img src="' + mapIcons['user'] + '"> ' + 'User';
        // legend.appendChild(div);

        var div = document.createElement('div');
        div.innerHTML = '<img src="' + mapIcons['offline'] + '"> ' + 'offline';
        legend.appendChild(div);
        
        var div = document.createElement('div');
        div.innerHTML = '<img src="' + mapIcons['active'] + '"> ' + 'online';
        legend.appendChild(div);
        
        //var div = document.createElement('div');
       // div.innerHTML = '<img src="' + mapIcons['unactivated'] + '"> ' + 'Unactivated';
        //legend.appendChild(div);

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
            url: '/admin/map/ajax?district_id='+ districtdata,
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
    	var baddress = element.service.address?element.service.address:(element.latitude + "," + element.longitude);
        marker = new google.maps.Marker({
            position: {
                lat: element.latitude,
                lng: element.longitude
            },
            id: element.id,
            map: map,
            title: element.first_name + " " +element.last_name + "\n" + baddress,
            icon : mapIcons[element.service ? element.service.status : element.status],
        });

        googleMarkers.push(marker);

        google.maps.event.addListener(marker, 'click', function() {
            //window.location.href = '/admin/' + element.service ? 'provider' : 'user' + '/' +element.user_id;
            window.location.href = '/admin/currentlocation/'+element.id;
        });
    }
</script>
<script src="//maps.googleapis.com/maps/api/js?key={{ Setting::get('map_key') }}&libraries=places&callback=initMap" async defer></script>
@endsection