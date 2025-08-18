@extends('admin.layout.base')

@section('title', 'Tracking View ')

@section('content')
 <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.10.0/css/bootstrap-select.min.css" />
 
@php
if($tracking)
{ 
    $latlngs_json = json_decode($tracking->latlng);
    $latlngs = array_slice($latlngs_json,1); 
}
@endphp
<div class="content-area py-1">
    <div class="container-fluid"> 
        <div class="box box-block bg-white"> 
        <form action="{{route('admin.tracking.provider')}}" method="GET">
            <div class="row">
                <div class="col-xs-4">
                   <select class="form-control selectpicker" data-show-subtext="true" data-live-search="true" name="provider_id" required>
                    @foreach($providers as $provider)
                    <option value="{{$provider->id}}" @if(Request::get('provider_id')) @if(@Request::get('provider_id') == $provider->id) selected @endif @endif >{{$provider->first_name}} {{$provider->last_name}}</option> 
                   @endforeach 
                  </select>
                </div>
                <div class="col-xs-4">
                    <input class="form-control" type="date" name="date_search" placeholder="DateTime" @if(Request::get('date_search')) value="{{@Request::get('date_search')}}" @endif required> 
                </div>
                <div class="col-xs-4">
                    <button type="submit" class="form-control btn btn-primary">Fetch</button>
                </div>  
            </div>
        </form>
        </div> 
        @if($tracking)
        <div class="box box-block bg-white"> 
            <div class="row">
                <div class="col-xs-4">
                    <h5 class="mb-1" style="text-align: center;">Provider Name : <span style="color: blueviolet;">{{$tracking->provider ? $tracking->provider->first_name :''}} {{$tracking->provider ? $tracking->provider->last_name :''}}</span></h5>
                </div>
                <div class="col-xs-4">
                   <h5 class="mb-1" style="text-align: center;">Date : <span style="color: blueviolet;">{{date('M d Y', strtotime($tracking->created_at))}}</span></h5>
                </div> 
                <div class="col-xs-4">
                   <h5 class="mb-1" style="text-align: center;">Total Distance : <span style="color: blueviolet;" id="total"></span></h5>
                </div>
            </div>
        </div>
        @else
            @if(Request::get('date_search') && Request::get('provider_id'))
            <div class="alert alert-success alert-dismissible">
              <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
              No result.
            </div>
            @endif
        @endif
        <div class="box box-block bg-white">
            <h5 class="mb-1">Tracking View</h5>
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
<script type="text/javascript" src="{{ asset('asset/js/route-map.js') }}"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ Setting::get('map_key') }}&libraries=places&callback=initMap" async defer></script> 
@if($tracking)    
    <!-- <script type="text/javascript" src="{{ asset('asset/js/route-map.js') }}"></script> -->
    <!-- <script src="https://maps.googleapis.com/maps/api/js?key={{ Setting::get('map_key') }}&libraries=places&callback=initMap" async defer></script> -->
     <script>
      function initMap() {
         var current_latitude = {{$tracking->s_latitude}};
        var current_longitude ={{$tracking->s_longitude}};
        
        var map = new google.maps.Map(document.getElementById('map'), {
          zoom: 4,
          center: {lat: current_latitude, lng: current_longitude}  // Australia.
        });

        var directionsService = new google.maps.DirectionsService;
        var directionsDisplay = new google.maps.DirectionsRenderer({
          draggable: false,
          map: map
          // panel: document.getElementById('right-panel')
        });

        directionsDisplay.addListener('directions_changed', function() {
          computeTotalDistance(directionsDisplay.getDirections());
        });

        displayRoute('<?php echo $tracking->s_latitude.','.$tracking->s_longitude ?>', '<?php echo $tracking->d_latitude.','.$tracking->d_longitude ?>', directionsService,
            directionsDisplay);
      }

      function displayRoute(origin, destination, service, display) {
        service.route({
          origin: origin,
          destination: destination,
          waypoints: [ <?php foreach($latlngs as $key => $value){ if($key<23) { if($key==0) { ?> <?php echo "{location: "."'$value->latitude, $value->longitude'"."}" ?> <?php }else{ ?> <?php echo ", {location: "."'$value->latitude, $value->longitude'"."}" ?> <?php }}} ?> ],
          travelMode: 'DRIVING',
          avoidTolls: true
        }, function(response, status) {
          if (status === 'OK') {
            display.setDirections(response);
          } else {
            alert('Could not display directions due to: ' + status);
          }
        });
      }

      function computeTotalDistance(result) {
        var total = 0;
        var myroute = result.routes[0];
        for (var i = 0; i < myroute.legs.length; i++) {
          total += myroute.legs[i].distance.value;
        }
        total = total / 1000;
        document.getElementById('total').innerHTML = total + ' km';
      }
    </script> 
    <script src="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js">
    </script>
    @else
    <script type="text/javascript" src="{{ asset('asset/js/route-map.js') }}"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ Setting::get('map_key') }}&libraries=places&callback=initMap" async defer></script>
     <script>
      function initMap() {
        var current_latitude = 21.7679;
        var current_longitude = 78.8718;
        
        var map = new google.maps.Map(document.getElementById('map'), {
          zoom: 4,
          center: {lat: current_latitude, lng: current_longitude}  // Australia.
        });

        var directionsService = new google.maps.DirectionsService;
        var directionsDisplay = new google.maps.DirectionsRenderer({
          draggable: false,
          map: map
          // panel: document.getElementById('right-panel')
        });

        directionsDisplay.addListener('directions_changed', function() {
          computeTotalDistance(directionsDisplay.getDirections());
        }); 
      }
    </script>
    @endif
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.10.0/js/bootstrap-select.min.js"></script>

@endsection


<!-- //Waypoint script only for 24 point  -->

 <!-- <script type="text/javascript" src="{{ asset('asset/js/route-map.js') }}"></script> -->
    <!-- <script src="https://maps.googleapis.com/maps/api/js?key={{ Setting::get('map_key') }}&libraries=places&callback=initMap" async defer></script> -->
     <script>
      // function initMap() {
      //    var current_latitude = {{--$tracking->s_latitude--}};
      //   var current_longitude ={{--$tracking->s_longitude--}};
        
      //   var map = new google.maps.Map(document.getElementById('map'), {
      //     zoom: 4,
      //     center: {lat: current_latitude, lng: current_longitude}  // Australia.
      //   });

      //   var directionsService = new google.maps.DirectionsService;
      //   var directionsDisplay = new google.maps.DirectionsRenderer({
      //     draggable: false,
      //     map: map
      //     // panel: document.getElementById('right-panel')
      //   });

      //   directionsDisplay.addListener('directions_changed', function() {
      //     computeTotalDistance(directionsDisplay.getDirections());
      //   });

      //   displayRoute('<?php //echo $tracking->s_latitude.','.$tracking->s_longitude ?>', '<?php //echo $tracking->d_latitude.','.$tracking->d_longitude ?>', directionsService,
      //       directionsDisplay);
      // }

      // function displayRoute(origin, destination, service, display) {
      //   service.route({
      //     origin: origin,
      //     destination: destination,
      //     waypoints: [ <?php //foreach($latlngs as $key => $value){ if($key==0) { ?> <?php //echo "{location: "."'$value->latitude, $value->longitude'"."}" ?> <?php //}else{ ?> <?php //echo ", {location: "."'$value->latitude, $value->longitude'"."}" ?> <?php// }} ?> ],
      //     travelMode: 'DRIVING',
      //     avoidTolls: true
      //   }, function(response, status) {
      //     if (status === 'OK') {
      //       display.setDirections(response);
      //     } else {
      //       alert('Could not display directions due to: ' + status);
      //     }
      //   });
      // }

      // function computeTotalDistance(result) {
      //   var total = 0;
      //   var myroute = result.routes[0];
      //   for (var i = 0; i < myroute.legs.length; i++) {
      //     total += myroute.legs[i].distance.value;
      //   }
      //   total = total / 1000;
      //   document.getElementById('total').innerHTML = total + ' km';
      // }
    </script> 

  <!-- Location zoom and view many point--> 
    <script>
      //   var current_latitude = {{--$tracking->s_latitude--}};
      //   var current_longitude ={{--$tracking->s_longitude--}};

      // function initMap() {

      //   var map = new google.maps.Map(document.getElementById('map'), {
      //     zoom: 6,
      //     center: {lat: current_latitude, lng: current_longitude}
      //   }); 

      //   // Create an array of alphabetical characters used to label the markers.
      //   var labels = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

      //   // Add some markers to the map.
      //   // Note: The code uses the JavaScript Array.prototype.map() method to
      //   // create an array of markers based on a given "locations" array.
      //   // The map() method here has nothing to do with the Google Maps API.
      //   var markers = locations.map(function(location, i) {
      //     return new google.maps.Marker({
      //       position: location,
      //       map: map,
      //       label: labels[i % labels.length]
      //     });
      //   });

      //   // Add a marker clusterer to manage the markers.
      //   var markerCluster = new MarkerClusterer(map, markers,
      //       {imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'});
      // }
      // var locations = [<?php //foreach($latlngs as $key => $value){ if($key==0) { ?> {lat: <?php //echo $value->latitude ?>, lng: <?php //echo $value->longitude ?>} <?php //}else{ ?>, {lat: <?php //echo $value->latitude ?>, lng: <?php //echo $value->longitude ?>} <?php //}} ?> 
      // ]
    </script>