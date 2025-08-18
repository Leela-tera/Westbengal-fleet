@extends('admin.layout.base')

@section('title', 'Map View')

@section('content')
<div class="content-area py-1">
    <div class="container-fluid">
        <div class="box box-block bg-white">
            <h5 class="mb-1">Tracking View</h5>
            <div class="filter-section">
                <label><input type="checkbox" value="ONT" class="type-filter" checked> ONT</label>
                <label><input type="checkbox" value="BHQ" class="type-filter" checked> BHQ</label>
                <label><input type="checkbox" value="OLT" class="type-filter" checked> OLT</label>
                <label><input type="checkbox" value="default" class="type-filter" checked> Default</label>
                <label></label>
                <br/>
                <label></label>
   <!-- District Filter -->

    <label for="state-filter">State:</label>
    <select id="state-filter" class="filter-dropdown">
        <option value="">All States</option>
        @foreach($distinctStates as $state)
            <option value="{{ $state}}">{{ $state}}</option>
        @endforeach
    </select>


    <label for="district-filter">District:</label>
    <select id="district-filter" class="filter-dropdown">
        <option value="">All Districts</option>
        @foreach($distinctDistricts as $district)
            <option value="{{ $district }}">{{ $district }}</option>
        @endforeach
    </select>


              <!-- Block Filter -->
    <label for="block-filter">Block:</label>
    <select id="block-filter" class="filter-dropdown">
        <option value="">All Blocks</option>
        @foreach($distinctBlocks as $block)
            <option value="{{ $block }}">{{ $block }}</option>
        @endforeach
    </select>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <div id="map"></div>
                    <div id="legend"><h3>Legend:</h3></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    #map {
        height: 100%;
        min-height: 500px;
    }
    #legend {
        font-family: Arial, sans-serif;
        background: rgba(255, 255, 255, 0.8);
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
    var googleMarkers = [];
    var directionsService;
    var directionsRenderer;
    var ontMarker = null;
    var bhqMarker = null;
    var oltMarker = null;


    const avgLatitude = {{ $avgLatitude }};
    const avgLongitude = {{ $avgLongitude }};



    var mapIcons = {
        ONT: '{{ asset("asset/img/marker-home.png") }}', // ONT icon
        BHQ: '{{ asset("asset/img/map-marker-red.png") }}',  // BHQ icon
        OLT: '{{ asset("asset/img/marker-user.png") }}',
        default: 'https://maps.google.com/mapfiles/kml/shapes/road_shield3.png'
    };

    let selectedTypes = ["ONT", "BHQ", "OLT", "default"]; // Default selection

    $(document).on("change", ".type-filter", function() {
        selectedTypes = $(".type-filter:checked").map(function() {
            return this.value;
        }).get();
        fetchGPSData();
    });

    function initMap() {
    map = new google.maps.Map(document.getElementById('map'), {
        center: { lat: avgLatitude , lng: avgLongitude  },
        zoom: 14,  // Increased zoom level for more detailed roads and buildings
        mapTypeId: google.maps.MapTypeId.ROADMAP
    });

    directionsService = new google.maps.DirectionsService();
    directionsRenderer = new google.maps.DirectionsRenderer({
        map: map,
        suppressMarkers: true
    });

    var legend = document.getElementById('legend');
    addLegendItem(legend, mapIcons['ONT'], 'ONT');
    addLegendItem(legend, mapIcons['BHQ'], 'BHQ');
    addLegendItem(legend, mapIcons['OLT'], 'OLT');

    map.controls[google.maps.ControlPosition.RIGHT_BOTTOM].push(legend);

    fetchGPSData();
    }
      

      const districtFilter = document.getElementById('district-filter');
    const blockFilter = document.getElementById('block-filter');

    document.getElementById('state-filter').addEventListener('change', function () {
        const state = this.value;
        districtFilter.disabled = true;
        districtFilter.innerHTML = '<option value="">Loading...</option>';
        blockFilter.disabled = true;
        blockFilter.innerHTML = '<option value="">Select a District first</option>';

        if (state) {
            fetch(`/admin/get-map-districts?state=${state}`)
                .then(response => response.json())
                .then(data => {
                    districtFilter.innerHTML = '<option value="">All Districts</option>';
                    data.forEach(district => {
                        districtFilter.innerHTML += `<option value="${district}">${district}</option>`;
                    });
                    districtFilter.disabled = false;
                })
                .catch(error => {
                    console.error('Error fetching districts:', error);
                    districtFilter.innerHTML = '<option value="">Error loading districts</option>';
                });
                  fetchGPSData();
        } else {
            districtFilter.innerHTML = '<option value="">Select a State first</option>';
        }
    });

    districtFilter.addEventListener('change', function () {
        const district = this.value;
        blockFilter.disabled = true;
        blockFilter.innerHTML = '<option value="">Loading...</option>';

        if (district) {
            fetch(`/admin/get-map-blocks?district=${district}`)
                .then(response => response.json())
                .then(data => {
                    blockFilter.innerHTML = '<option value="">All Blocks</option>';
                    data.forEach(block => {
                        blockFilter.innerHTML += `<option value="${block}">${block}</option>`;
                    });
                    blockFilter.disabled = false;
                     fetchGPSData();
                })
                .catch(error => {
                    console.error('Error fetching blocks:', error);
                    blockFilter.innerHTML = '<option value="">Error loading blocks</option>';
                });
        } else {
            blockFilter.innerHTML = '<option value="">Select a District first</option>';
        }
    });

    $(document).on("change", "#block-filter", function() {
    selectedBlock = this.value;
    fetchGPSData();
});

      function fetchGPSData() {
    const state = document.getElementById('state-filter').value;
    const district = document.getElementById('district-filter').value;
    const block = document.getElementById('block-filter').value;

    googleMarkers.forEach(marker => marker.setMap(null));
    googleMarkers = [];

    $.ajax({
        url: '/admin/map/fetch-gps-data',
        dataType: "JSON",
        type: "GET",
        data: {
            state: state,
            district: district,
            block: block
        },
        success: function(data) {
            data.forEach(addMarkerToMap);
        },
        error: function(error) {
            console.error('Error fetching GPS data:', error);
        }
    });
}

   
    function displayRouteOrDrawLink(origin, destination, useDirections = true) {
    if (useDirections) {
        const request = {
            origin: origin,
            destination: destination,
            travelMode: google.maps.TravelMode.DRIVING,
            provideRouteAlternatives: false  // False to get a single route
        };
        directionsService.route(request, function(result, status) {
            if (status === google.maps.DirectionsStatus.OK) {
                directionsRenderer.setDirections(result);

                // Create a polyline for the route
                const route = result.routes[0];
                const path = route.overview_path;

                // Draw the polyline with a red line
                const polyline = new google.maps.Polyline({
                    path: path,
                    geodesic: true,
                    strokeColor: "#fff", // Red color for the path
                    strokeOpacity: 1.0,
                    strokeWeight: 4 // Thickness of the line
                });
                polyline.setMap(map);
            } else {
                console.error('Directions failed:', status);
                drawLink(origin, destination); // Fall back to a simple line
            }
        });
    } else {
        drawLink(origin, destination);
    }
}

function drawLink(origin, destination) {
    const polyline = new google.maps.Polyline({
        path: [origin, destination],
        geodesic: true,
        strokeColor: "#FF6347", // Red color for the line
        strokeOpacity: 1.0,
        strokeWeight: 4 // Line thickness
    });
    polyline.setMap(map);
}

function addMarkerToMap(element) {
    const icon = mapIcons[element.type] || mapIcons.default;

    const marker = new google.maps.Marker({
        position: { lat: parseFloat(element.lattitude), lng: parseFloat(element.longitude) },
        map: map,
        title: element.name,
        icon: icon
    });

    googleMarkers.push(marker);

    const infoWindow = new google.maps.InfoWindow({
        content: `<strong>${element.name}</strong><br>Type: ${element.type}<br>Latitude: ${element.lattitude}<br>Longitude: ${element.longitude}`
    });

    marker.addListener('mouseover', () => infoWindow.open(map, marker));
    marker.addListener('mouseout', () => infoWindow.close());

    // Store specific markers for ONT and BHQ to draw the line later
    if (element.type === 'ONT') {
        ontMarker = marker;
    } else if (element.type === 'BHQ') {
        bhqMarker = marker;
    } else if (element.type === 'OLT') {
        oltMarker = marker;
    }

    

    // Draw the red line between the two markers if both are available
    //if (ontMarker && bhqMarker) {
        //displayRouteOrDrawLink(ontMarker.position, bhqMarker.position);
    //}
}
    function addLegendItem(legend, iconUrl, label) {
        var div = document.createElement('div');
        div.innerHTML = `<img src="${iconUrl}"> ${label}`;
        legend.appendChild(div);
    }
</script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ Setting::get('map_key') }}&callback=initMap" async defer></script>
@endsection
