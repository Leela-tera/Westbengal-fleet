@extends('admin.layout.base')

@section('title', 'Update Issued Material ')

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
            <a href="{{ route('admin.material_issue.index') }}" class="btn btn-default pull-right"><i class="fa fa-angle-left"></i> @lang('admin.back')</a>

			<h5 style="margin-bottom: 2em;">@lang('admin.mt_issue.update_mt_issue')</h5>

      <form class="form-horizontal" action="{{route('admin.material_issue.update', $material_issue->id )}}" method="POST" enctype="multipart/form-data" role="form">
            	{{csrf_field()}}
        <input type="hidden" name="_method" value="PATCH">
		<div class="top-cs box-block shadow-gray mb-3">
        	<h5 class="mb-2">Material Issue Details</h5>
        	<div class="box-block pb-0">
        	<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="date" class="col-form-label  ">Date
							</label>
							<input class="form-control select-box" type="date" name="date" id="date" placeholder="Date" value="{{ $material_issue->date }}" onclick="this.showPicker()">
						</div>
					</div>
					@if(count($materials) > 0)
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="material_name" class="col-form-label  ">Materials
							<span class="look-a-like">*</span></label>
							<select class="form-control select-box" name="material_name" id="material_name" required>
								<option value="">Please Select Material Name</option>
								@foreach($materials as $material)
								<option value="{{$material->id}}" {{ $material->id == $material_issue->material_id ? 'selected' : '' }}>{{$material->name}}</option>
								@endforeach									
							</select>
						</div>
					</div>
					@endif
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="uom" class="col-form-label  ">UOM
							</label>
							<input class="form-control select-box" type="text" name="uom" id="uom" value="{{ $material_issue->uom }}" placeholder="UOM">
						</div>
					</div>
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="material_indent_note" class="col-form-label  ">Material Indent Note
							</label>
							<input class="form-control select-box" type="text" value="{{ $material_issue->material_indent_note }}" name="material_indent_note"  id="material_indent_note" placeholder="Material Indent Note">
						</div>
					</div>			
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="min_no" class="col-form-label  ">Min No
							</label>
							<input class="form-control select-box" type="text" value="{{ $material_issue->min_no }}" name="min_no"  id="min_no" placeholder="Min No">
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
								<option value="{{$dist->id}}" {{ $dist->id == $material_issue->district_id ? 'selected' : '' }}>{{$dist->name}}</option>
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
								<option value="{{$block->id}}" {{ $block->id == $material_issue->block_id ? 'selected' : '' }}>{{$block->name}}</option>
								@endforeach									
							</select>
						</div>
					</div>
	        @endif
	        <div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="material_indent_qty" class="col-form-label  ">Materials Indent Quantity
							</label>
							<input class="form-control select-box" type="text" value="{{ $material_issue->material_indent_qty }}" name="material_indent_qty"  id="material_indent_qty" placeholder="Material Indent Quantity">
						</div>
					</div>
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="issued_qty" class="col-form-label  ">Materials Issued Quantity
							</label>
							<input class="form-control select-box" type="text" value="{{ $material_issue->issued_qty }}" name="issued_qty" id="issued_qty" placeholder="Issued Quantity">
						</div>
					</div>
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="drum_no" class="col-form-label  ">Drum Nos
							</label>

                                                         <select class="form-control select-box" name="drum_no" id="drum_no" required>
								<option value="">Please Select Drum</option>
								@foreach($drums as $drum)
                                                             <option value="{{$drum->drum_id}}" {{ $drum->drum_id == $material_issue->drum_no ? 'selected' : '' }}>{{$drum->drum_no}}</option>
								@endforeach									
							</select>
                                                 </div>
					</div>
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="si_no_equipment" class="col-form-label  ">SI Nos Of Equipment
							</label>
							<input class="form-control select-box" type="text" value="{{ $material_issue->si_no_equipment }}" name="si_no_equipment" id="si_no_equipment" placeholder="SI Nos Of Equipment">
						</div>
					</div>
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="receiving_person_name" class="col-form-label  ">Materials Receiving Person Name
							</label>
							<input class="form-control select-box" type="text" value="{{ $material_issue->receiving_person_name }}" name="receiving_person_name"  id="receiving_person_name" placeholder="Receiving Person Name">
						</div>
					</div>
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="remarks" class="col-form-label  ">Remarks
							</label>
							<input class="form-control select-box" type="text" value="{{ $material_issue->remarks }}" name="remarks"  id="remarks" placeholder="Remarks">
						</div>
					</div>
					<div class="form-group row">
						<label for="zipcode" class="col-xs-12 col-form-label"></label>
						<div class="col-xs-12 mt-2">
							<button type="submit" class="btn btn-primary btn-cstm pull-right ">@lang('admin.mt_issue.update_mt_issue')</button>
							<a href="{{route('admin.material_issue.index')}}" class="btn btn-default pull-right ">@lang('admin.cancel')</a>
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
