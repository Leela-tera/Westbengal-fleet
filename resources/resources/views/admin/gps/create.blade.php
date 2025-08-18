@extends('admin.layout.base')

@section('title', 'Add GP ')

@section('content')
<style type="text/css">
	.shadow-gray {
    box-shadow: 0 0 5px 1px #3333332e !important;
	}
	.col-form-label{
		font-size: 13px !important;
		font-weight: 600;
	}
	.p-2-5{
		padding:2.5rem;
	}
</style>
<div class="content-area py-1">
    <div class="container-fluid">
    	<div class="box box-block bg-white">
            <a href="{{ route('admin.gps.index') }}" class="btn btn-default pull-right"><i class="fa fa-angle-left"></i> @lang('admin.back')</a>

			<h5 style="margin-bottom: 2em;">@lang('admin.gp.new_gp')</h5>
      <form class="form-horizontal" action="{{route('admin.gps.store')}}" method="POST" enctype="multipart/form-data" role="form">
            	{{csrf_field()}}
        <div class="top-cs box-block shadow-gray mb-3">
        	<h5 class="mb-2">GP Details</h5>
        	<div class="box-block pb-0">
        	<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="gp_name" class="col-form-label  ">GP Name
							</label>
							<input class="form-control select-box" type="text" value="{{ old('gp_name') }}" name="gp_name"  id="gp_name" placeholder="GP Name">
						</div>
					</div>
					@if(count($districts) > 0)
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
						<label for="district" class="col-form-label">Districts</label>
							<select class="form-control select-box" name="district" id="district">
								<option value="">Please Select District</option>
								@foreach($districts as $dist)
								<option value="{{$dist->id}}">{{$dist->name}}</option>
								@endforeach
								
							</select>
						</div>
					</div>
	        @endif
	        @if(count($blocks) > 0)
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
						<label for="block" class="col-form-label">Block</label>
							<select class="form-control select-box" name="block" id="block">
								<option value="">Please Select Block</option>
								@foreach($blocks as $block)
								<option value="{{$block->id}}">{{$block->name}}</option>
								@endforeach
								
							</select>
						</div>
					</div>
	        @endif

                  @if(count($zonals) > 0)
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
						<label for="district" class="col-form-label">Zonal Manager</label>
							<select class="form-control select-box" name="zone" id="zone">
								<option value="">Please Select zone</option>
								@foreach($zonals as $zone)
								<option value="{{$zone->id}}">{{$zone->Name}}</option>
								@endforeach
								
							</select>
						</div>
					</div>
	        @endif

					@if(count($providers) > 0)
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="provider" class="col-form-label  ">Provider Name
							</label>
							<select class="form-control select-box" name="provider" id="provider" >
								<option value="">Please Select Provider Name</option>
								@foreach($providers as $provider)
								<option value="{{ $provider->first_name }} {{ $provider->last_name }}">{{ $provider->first_name }} {{ $provider->last_name }}</option>
								@endforeach									
							</select>
						</div>
					</div>
					@endif
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="contact" class="col-form-label  ">Contact
							</label>
							<input class="form-control select-box" type="number" value="{{ old('contact') }}" name="contact"  id="contact" placeholder="Provider Contact">
						</div>
					</div>
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="lgd_code" class="col-form-label  ">LGD Code
							</label>
							<input class="form-control select-box" type="text" value="{{ old('lgd_code') }}" name="lgd_code"  id="lgd_code" placeholder="LGD Code">
						</div>
					</div>
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="phase" class="col-form-label  ">Phase
							</label>
							<input class="form-control select-box" type="text" value="{{ old('phase') }}" name="phase"  id="phase" placeholder="Phase">
						</div>
					</div>
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="latitude" class="col-form-label  ">Latitude
							</label>
							<input class="form-control select-box" type="text" name="latitude" id="latitude" placeholder="Latitude">
						</div>
					</div>
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="longitude" class="col-form-label  ">Longitude
							</label>
							<input class="form-control select-box" type="text" name="longitude" id="longitude" placeholder="Longitude">
						</div>
					</div>
					<div class="form-group row">
						<label for="zipcode" class="col-xs-12 col-form-label"></label>
						<div class="col-xs-12 mt-2">
							<button type="submit" class="btn btn-primary btn-cstm pull-right ">@lang('admin.gp.add_gp')</button>
							<a href="{{route('admin.gps.index')}}" class="btn btn-default pull-right ">@lang('admin.cancel')</a>
						</div>
					</div>
			</div>
		</div>
			</form>
		 </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
$("select[id='district']").change(function(){
  var district = $(this).val();
  $.get('{{url("admin/ajax-blocks")}}/'+district,function(data) {
    $("#block").empty().append(data);      
  });
});
</script>
@endsection
