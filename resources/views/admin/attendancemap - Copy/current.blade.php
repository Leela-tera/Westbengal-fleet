@extends('admin.layout.base')

@section('title', 'Map View ')

@section('content')

<div class="content-area py-1">
    <div class="container-fluid">
        	
        <div class="box box-block bg-white">
            <h5 class="mb-1">Current Location View</h5>
            <input type="hidden" name="latitude" id="latitude" value="{{$Providers->latitude}}"/>
            <input type="hidden" name="longitude" id="longitude" value="{{$Providers->longitude}}"/>
            <div class="row">
                <div class="col-xs-12">
                    <div id="map"></div>
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
    var lat = document.getElementById('latitude').value;
    var log = document.getElementById('longitude').value;
    function initMap() {
      var myLatlng = new google.maps.LatLng(lat, log);
      var myOptions = {
        zoom: 8,
        center: myLatlng,
        mapTypeId: google.maps.MapTypeId.ROADMAP
      }
     
      var map = new google.maps.Map(document.getElementById("map"), myOptions);

      var marker = new google.maps.Marker({position:myLatlng});
      marker.setMap(map);
    }

    function loadScript() {
      var script = document.createElement("script");
      script.type = "text/javascript";
      script.src = "//maps.googleapis.com/maps/api/js?key={{ Setting::get('map_key') }}&libraries=places&callback=initMap";
      document.body.appendChild(script);
    }

    window.onload = loadScript;

</script>

@endsection