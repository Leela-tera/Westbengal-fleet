@extends('admin.layout.pbase')

@section('title', 'Complete Form')

@section('content')

<div class="content-area py-1">
    <div class="container-fluid">
    	<div class="box box-block bg-white">
            <a href="{{ route('provider.tickets') }}" class="btn btn-default pull-right"><i class="fa fa-angle-left"></i> Back</a>

			<h5>Complete Form</h5>

            <form class="form-horizontal" action="{{route('provider.dispatcher.closerequest')}}" method="POST" enctype="multipart/form-data" role="form">
            	{!! csrf_field() !!}
                @csrf
				<div class="form-group row">
					<label for="first_name" class="col-xs-12 col-form-label">Ticket Id</label>
					<div class="col-xs-10">
						<p>{{$userrequest->booking_id}}</p>
					</div>
				</div>
				<div class="form-group row">
					<label for="last_name" class="col-xs-12 col-form-label">Issue Type</label>
					<div class="col-xs-10">
						<p>{{$userrequest->downreason}}</p>
					</div>
				</div>
				<div class="form-group row">
					<label for="last_name" class="col-xs-12 col-form-label">Description</label>
					<div class="col-xs-10">
						<p>{{$userrequest->downreasonindetailed}}</p>
					</div>
				</div>
				<div class="form-group row">
					<label for="last_name" class="col-xs-12 col-form-label">Select Category</label>
					<div class="col-xs-10">
						<select class="form-control" name="downreason" required>
							<option value="">Please Select</option>
							<?php foreach($service_types as $types) { ?>
							<option value="{{$types->name}}">{{$types->name}}</option>
						    <?php } ?>
						</select>
					</div>
				</div>
				<div class="form-group row">
					<label for="last_name" class="col-xs-12 col-form-label">Close Reason</label>
					<div class="col-xs-10">
						 <input class="form-control" type="textarea" name="downreasonindetailed" required placeholder="Description">
					</div>
				</div>
                                <div class="form-group row">
                                   <label class="col-xs-12 col-form-label">Before Image</label>
                                     <div class="col-xs-10">
                                   <input type="file" name="before_image[]" accept="image/*" class="form-control" multiple required>
                                  </div> 
                               </div>

                               <div class="form-group row">
                               <label class="col-xs-12 col-form-label">After Image</label>
                              <div class="col-xs-10">
                              <input type="file" name="after_image[]" accept="image/*" class="form-control" multiple required>
                             </div>
                            </div>

				<input type ="hidden" value="{{$userrequest->id}}" name="request_id">
                                <input type ="hidden" value="{{$userrequest->provider_id}}" name="provider_id">	
                                <input type ="hidden" value="{{$userrequest->booking_id}}" name="booking_id">
                                <input type="hidden" name="complete_latitude" id="complete_latitude">
                                <input type="hidden" name="complete_longitude" id="complete_longitude">


				
				<div class="form-group row">
					<label for="zipcode" class="col-xs-12 col-form-label"></label>
					<div class="col-xs-10">
						<button type="submit" class="btn btn-primary">Submit</button>
					</div>
				</div>
			</form>
		</div>
    </div>
</div>
<!---
<script>
document.addEventListener("DOMContentLoaded", function() {
    const form = document.querySelector('form[action="{{ route('provider.dispatcher.closerequest') }}"]');

    form.addEventListener('submit', function(event) {
        event.preventDefault(); // Stop default submit

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                // Set lat/lng
                document.getElementById('complete_latitude').value = position.coords.latitude;
                document.getElementById('complete_longitude').value = position.coords.longitude;

                // Submit after location is set
                form.submit();
            }, function() {
                alert("Please allow location access to complete the ticket.");
            });
        } else {
            alert("Geolocation not supported.");
        }
    });
});
</script>
--->

<script>
document.addEventListener("DOMContentLoaded", function() {
    const form = document.querySelector('form[action="{{ route('provider.dispatcher.closerequest') }}"]');

    if (!form) return;

    form.addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent default for now

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                // Got location — set hidden fields
                document.getElementById('complete_latitude').value = position.coords.latitude;
                document.getElementById('complete_longitude').value = position.coords.longitude;

                form.submit();
            }, function(error) {
                // If location blocked or failed — silently continue
                form.submit();
            });
        } else {
            // Geolocation not supported — still submit
            form.submit();
        }
    });
});
</script>


@endsection
