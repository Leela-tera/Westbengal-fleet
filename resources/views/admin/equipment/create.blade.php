@extends('admin.layout.base')

@section('title', 'Add Equipment ')

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
            <a href="{{ route('admin.equipment.index') }}" class="btn btn-default pull-right"><i class="fa fa-angle-left"></i> @lang('admin.back')</a>

			<h5 style="margin-bottom: 2em;">@lang('admin.equipment.new_equipment')</h5>
      <form class="form-horizontal" action="{{route('admin.equipment.store')}}" method="POST" enctype="multipart/form-data" role="form">
            	{{csrf_field()}}
        <div class="top-cs box-block shadow-gray mb-3">
        	<h5 class="mb-2">Equipment Details</h5>
        	<div class="box-block pb-0">
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="equipment_name" class="col-form-label  ">@lang('admin.equipment.equipment_name')
							<span class="look-a-like">*</span></label>
							<input class="form-control select-box" type="text" value="{{ old('equipment_name') }}" name="equipment_name" required id="equipment_name" placeholder="Equipment Name">
						</div>
					</div>
					@if(count($materials) > 0)
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="service_type" class="col-form-label  ">Service Type
							<span class="look-a-like">*</span></label>
							<select class="form-control select-box" name="service_type" id="service_type" required>
								<option value="">Please Select Service Type</option>
								@foreach($materials as $material)
								<option value="{{$material->id}}">{{$material->name}}</option>
								@endforeach									
							</select>
						</div>
					</div>
					@endif
					@if(count($providers) > 0)
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="current_assignee" class="col-form-label  ">Current Assignee
							</label>
							<select class="form-control select-box" name="current_assignee" id="current_assignee" >
								<option value="">Please Select Current Assignee</option>
								@foreach($providers as $provider)
								<option value="{{$provider->id}}">{{ $provider->first_name }} {{ $provider->last_name }}</option>
								@endforeach									
							</select>
						</div>
					</div>
					@endif
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="service_number" class="col-form-label  ">Service Number
							</label>
							<input class="form-control select-box" type="text" value="{{ old('service_number') }}" name="service_number"  id="service_number" placeholder="Service Number">
						</div>
					</div>
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="service_model" class="col-form-label  ">Service Model
							</label>
							<input class="form-control select-box" type="text" value="{{ old('service_model') }}" name="service_model"  id="service_model" placeholder="Service Model">
						</div>
					</div>
					@if(count($districts) > 0)
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="district" class="col-form-label">Districts
							<span class="look-a-like">*</span></label>
							<select class="form-control select-box" name="district" id="district" required>
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
							<label for="block" class="col-form-label">Blocks
							<span class="look-a-like">*</span></label>
							<select class="form-control select-box" name="block" id="block" required>
								<option value="">Please Select Block</option>
								@foreach($blocks as $block)
								<option value="{{$block->id}}">{{$block->name}}</option>
								@endforeach									
							</select>
						</div>
					</div>
			        @endif
			        <div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="status" class="col-form-label">Status
							<span class="look-a-like">*</span></label>
							<select class="form-control select-box" name="status" id="status" required>
								<option value="">Please Select Status</option>
								<option value="in-service">In Service</option>
								<option value="out-of-service">Out Of Service</option>
								<option value="missing">Missing</option>
							</select>
						</div>
					</div>
			</div>
		</div>
		<div class="top-cs box-block shadow-gray">
        	<h5 class="mb-2">Purchase Information</h5>
        	<div class="box-block pb-0">
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="purchase_price" class="col-form-label  ">Purchase Price
							</label>
							<input class="form-control select-box" type="text" value="{{ old('purchase_price') }}" name="purchase_price"  id="purchase_price" placeholder="Purchase Price">
						</div>
					</div>
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="purchase_date" class="col-form-label  ">Purchase Date
							</label>
							<input class="form-control select-box" type="date" name="purchase_date" id="purchase_date" placeholder="Purchase Date" onclick="this.showPicker()">
						</div>
					</div>
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="warranty_exp_date" class="col-form-label  ">Warranty Expiration Date
							</label>
							<input class="form-control select-box" type="date" name="warranty_exp_date" id="warranty_exp_date" placeholder="Warranty Expiration Date" onclick="this.showPicker()">
						</div>
					</div>
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="purchase_comments" class="col-form-label  ">Purchase Comments
							</label>
							<input class="form-control select-box" type="text" value="{{ old('purchase_comments') }}" name="purchase_comments"  id="purchase_comments" placeholder="Purchase Comments">
						</div>
					</div>
					<div class="form-group row">
						<label for="zipcode" class="col-xs-12 col-form-label"></label>
						<div class="col-xs-12 mt-2">
							<button type="submit" class="btn btn-primary btn-cstm pull-right ">@lang('admin.equipment.add_equipment')</button>
							<a href="{{route('admin.equipment.index')}}" class="btn btn-default pull-right ">@lang('admin.cancel')</a>
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
