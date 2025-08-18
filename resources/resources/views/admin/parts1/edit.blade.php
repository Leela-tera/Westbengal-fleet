@extends('admin.layout.base')

@section('title', 'Update Part ')

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
            <a href="{{ route('admin.parts.index') }}" class="btn btn-default pull-right"><i class="fa fa-angle-left"></i> @lang('admin.back')</a>

			<h5 style="margin-bottom: 2em;">@lang('admin.part.update_part')</h5>

      <form class="form-horizontal" action="{{route('admin.parts.update', $part->id )}}" method="POST" enctype="multipart/form-data" role="form">
            	{{csrf_field()}}
        <input type="hidden" name="_method" value="PATCH">
		<div class="top-cs box-block shadow-gray mb-3">
        	<h5 class="mb-2">Part Details</h5>
        	<div class="box-block pb-0">
					@if(count($materials) > 0)
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="material_name" class="col-form-label  ">Materials
							<span class="look-a-like">*</span></label>
							<select class="form-control select-box" name="material_name" id="material_name" required>
								<option value="">Please Select Material Name</option>
								@foreach($materials as $material)
								<option value="{{$material->id}}" {{ $material->id == $part->material_id ? 'selected' : '' }}>{{$material->name}}</option>
								@endforeach									
							</select>
						</div>
					</div>
					@endif
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="indent_person" class="col-form-label  ">Indent Person Name
							</label>
							<input class="form-control select-box" type="text" value="{{ $part->indent_person }}" name="indent_person"  id="indent_person" placeholder="Indent Person Name">
						</div>
					</div>
					@if(count($providers) > 0)
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="received_person" class="col-form-label  ">Received Person Name
							</label>
							<select class="form-control select-box" name="received_person" id="received_person" >
								<option value="">Please Select Received Person Name</option>
								@foreach($providers as $provider)
								<option value="{{$provider->id}}" {{ $provider->id == $part->provider_id ? 'selected' : '' }}>{{ $provider->first_name }} {{ $provider->last_name }}</option>
								@endforeach									
							</select>
						</div>
					</div>
					@endif
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="indent_date" class="col-form-label  ">Indent Date
							</label>
							<input class="form-control select-box" type="date" name="indent_date" id="indent_date" placeholder="Indent Date" value="{{ $part->indent_date }}" onclick="this.showPicker()">
						</div>
					</div>
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="indent_approve_name" class="col-form-label  ">Indent Approve Name
							</label>
							<input class="form-control select-box" type="text" value="{{ $part->indent_approve_name }}" name="indent_approve_name"  id="indent_approve_name" placeholder="Indent Approve Name">
						</div>
					</div>
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="received_date" class="col-form-label  ">Received Date
							</label>
							<input class="form-control select-box" type="date" name="received_date" id="received_date" placeholder="Received Date" value="{{ $part->received_date }}" onclick="this.showPicker()">
						</div>
					</div>
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="uom" class="col-form-label  ">UOM
							</label>
							<input class="form-control select-box" type="text" name="uom" id="uom" value="{{ $part->uom }}" placeholder="UOM">
						</div>
					</div>
			    <div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="status" class="col-form-label">Status
							<span class="look-a-like">*</span></label>
							<select class="form-control select-box" name="status" id="status" required>
								<option value="">Please Select Status</option>
								<option value="in-service" {{ $part->status == 'in-service' ? 'selected' : '' }}>In Service</option>
								<option value="out-of-service" {{ $part->status == 'out-of-service' ? 'selected' : '' }}>Out Of Service</option>
								<option value="missing" {{ $part->status == 'missing' ? 'selected' : '' }}>Missing</option>
							</select>
						</div>
					</div>
			</div>
		</div>
		<div class="top-cs box-block shadow-gray">
        	<h5 class="mb-2">Issued Information</h5>
        	<div class="box-block pb-0">
        	<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="issued_person" class="col-form-label  ">Person Name
							</label>
							<input class="form-control select-box" type="text" value="{{ $part->issued_person }}" name="issued_person"  id="issued_person" placeholder="Person Name">
						</div>
					</div>
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="issued_date" class="col-form-label  ">Date
							</label>
							<input class="form-control select-box" type="date" name="issued_date" id="issued_date" placeholder="Date" value="{{ $part->issued_date }}" onclick="this.showPicker()">
						</div>
					</div>
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="issued_person_mobile" class="col-form-label  ">Person Mobile
							</label>
							<input class="form-control select-box" type="text" value="{{ $part->issued_person_mobile }}" name="issued_person_mobile"  id="issued_person_mobile" placeholder="Person Mobile Number">
						</div>
					</div>
					@if(count($districts) > 0)
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="issued_district" class="col-form-label">Districts
							<span class="look-a-like">*</span></label>
							<select class="form-control select-box" name="issued_district" id="issued_district" required>
								<option value="">Please Select District</option>
								@foreach($districts as $dist)
								<option value="{{$dist->id}}" {{ $dist->id == $part->district_id ? 'selected' : '' }}>{{$dist->name}}</option>
								@endforeach									
							</select>
						</div>
					</div>
	        @endif
	        @if(count($blocks) > 0)
	        <div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="issued_block" class="col-form-label">Blocks
							<span class="look-a-like">*</span></label>
							<select class="form-control select-box" name="issued_block" id="issued_block" required>
								<option value="">Please Select Block</option>
								@foreach($blocks as $block)
								<option value="{{$block->id}}" {{ $block->id == $part->block_id ? 'selected' : '' }}>{{$block->name}}</option>
								@endforeach									
							</select>
						</div>
					</div>
	        @endif
	        <div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="gp_to_gp" class="col-form-label  ">GP To GP
							</label>
							<input class="form-control select-box" type="text" value="{{ $part->gp_to_gp }}" name="gp_to_gp"  id="gp_to_gp" placeholder="GP To GP">
						</div>
					</div>
			    <div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="issued_qty" class="col-form-label  ">Quantity
							</label>
							<input class="form-control select-box" type="text" value="{{ $part->issued_qty }}" name="issued_qty"  id="issued_qty" placeholder="Issued Quantity">
						</div>
					</div>
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="issued_qty_good_condition" class="col-form-label  ">Quantity in Good Condition
							</label>
							<input class="form-control select-box" type="text" value="{{ $part->is_good_condition }}" name="issued_qty_good_condition"  id="issued_qty_good_condition" placeholder="Quantity In Good Condition">
						</div>
					</div>
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="store_note_no" class="col-form-label  ">Store Issue Note No
							</label>
							<input class="form-control select-box" type="text" value="{{ $part->store_note_no }}" name="store_note_no"  id="store_note_no" placeholder="Store Issue Note No">
						</div>
					</div>
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="issued_comments" class="col-form-label  ">Comments
							</label>
							<input class="form-control select-box" type="text" value="{{ $part->issued_comments }}" name="issued_comments"  id="issued_comments" placeholder="Issued Comments">
						</div>
					</div>
					<div class="form-group row">
						<label for="zipcode" class="col-xs-12 col-form-label"></label>
						<div class="col-xs-12 mt-2">
							<button type="submit" class="btn btn-primary btn-cstm pull-right ">@lang('admin.part.update_part')</button>
							<a href="{{route('admin.parts.index')}}" class="btn btn-default pull-right ">@lang('admin.cancel')</a>
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
