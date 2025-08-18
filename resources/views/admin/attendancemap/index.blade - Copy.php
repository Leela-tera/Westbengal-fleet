@extends('admin.layout.base')

@section('title', 'Map View ')

   @section('content')
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
        <script src="http://maps.google.com/maps/api/js?key=AIzaSyDzQo9SnLErjicJ4EkXsgJT6HLBGBjsXZU&sensor=false" type="text/javascript"></script>
        <script type="text/javascript">
		
            var icon = new google.maps.MarkerImage('{{ asset("asset/img/marker-user.png") }}',
                       new google.maps.Size(32, 32), new google.maps.Point(0, 0),
                       new google.maps.Point(16, 32));
            var center = null;
            var map = null;
            var currentPopup;
            var bounds = new google.maps.LatLngBounds();
            function addMarker(lat, lng, info) {
                var pt = new google.maps.LatLng(lat, lng);
                bounds.extend(pt);
                var marker = new google.maps.Marker({
                    position: pt,
                    icon: icon,
                    map: map
                });
                var popup = new google.maps.InfoWindow({
                    content: info,
                    maxWidth: 300
                });
                google.maps.event.addListener(marker, "click", function() {
                    if (currentPopup != null) {
                        currentPopup.close();
                        currentPopup = null;
                    }
                    popup.open(map, marker);
                    currentPopup = popup;
                });
                google.maps.event.addListener(popup, "closeclick", function() {
                    map.panTo(center);
                    currentPopup = null;
                });
            }           
            function initMap() {
                map = new google.maps.Map(document.getElementById("map"), {
                    center: new google.maps.LatLng(0, 0),
                    zoom: 7,
                    mapTypeId: google.maps.MapTypeId.ROADMAP,
                    mapTypeControl: true,
                    mapTypeControlOptions: {
                        style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR
                    },
                    navigationControl: true,
                    navigationControlOptions: {
                        style: google.maps.NavigationControlStyle.ZOOM_PAN
                    }
                });
<?php
foreach($Providers as $row){
	
  $name = $row->first_name.''.$row->last_name;
  $lat = $row->latitude;
  $lon = $row->longitude;
  $desc = $row->address;



  echo("addMarker($lat, $lon, '<b>$name</b><br />$desc');\n");

}


?>
 center = bounds.getCenter();
     map.fitBounds(bounds);

     }
     </script>
     </head>
     <body onload="initMap()" style="margin:0px; border:0px; padding:0px;">
     <div class="content-area py-1">
    <div class="container-fluid">
        	<div class="box box-block bg-white"> 
        <form action="{{route('admin.trackattendance')}}" method="GET">
            <div class="row">
                <div class="col-xs-3">
                   <select class="form-control selectpicker" data-show-subtext="true" data-live-search="true" name="district_id" required>
                   	<option value="">Please Select District</option>
                    @foreach($districts as $district)
                    <option value="{{$district->id}}" @if(Request::get('district_id')) @if(@Request::get('district_id') == $district->id) selected @endif @endif>{{$district->name}} </option> 
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
            <h5 class="mb-1">Tracking View</h5>
            <div class="row">
            	 <input type="hidden" name="districtdata" value="{{$district_id}}" id="districtdata" />
				 <input type="hidden" name="fromdate" value="{{$from_date}}" id="fromdate" />
				 <input type="hidden" name="todate" value="{{$to_date}}" id="todate" />
				 
                <div class="col-xs-12">
                    <div id="map"></div>
                </div>
            </div>
        </div>
    </div>

</div>
   @endsection