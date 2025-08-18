@extends('admin.layout.pbase')

@section('title', 'Edit Reason')

@section('content')

<div class="content-area py-1">
    <div class="container-fluid">
    	<div class="box box-block bg-white">
            <a href="#" class="btn btn-default pull-right"><i class="fa fa-angle-left"></i> Back</a>

			<h5>Edit Reason</h5>

            <form class="form-horizontal" action="{{route('provider.dispatcher.editreasonrequest')}}" method="POST" enctype="multipart/form-data" role="form">
            	{!! csrf_field() !!}
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
					<label for="last_name" class="col-xs-12 col-form-label">Reason</label>
					<div class="col-xs-10">
						 <input class="form-control" type="textarea" name="downreasonindetailed" required placeholder="Description" >
					</div>
				</div>
				<input type ="hidden" value="{{$userrequest->id}}" name="request_id">
                                <input type ="hidden" value="{{$userrequest->booking_id}}" name="booking_id">

				
				<div class="form-group row">
					<label for="zipcode" class="col-xs-12 col-form-label"></label>
					<div class="col-xs-10">
						<button type="submit" class="btn btn-primary">Update</button>
					</div>
				</div>
			</form>
		</div>
    </div>
</div>

@endsection
