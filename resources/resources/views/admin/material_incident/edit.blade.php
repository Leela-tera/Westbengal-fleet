@extends('admin.layout.base')

@section('title', 'Update Material Incident ')

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
            <a href="{{ route('admin.material_incident.index') }}" class="btn btn-default pull-right"><i class="fa fa-angle-left"></i> @lang('admin.back')</a>

			<h5 style="margin-bottom: 2em;">@lang('admin.material_incident.update_material_incident')</h5>

      <form class="form-horizontal" action="{{route('admin.material_incident.update', $material_incident->id )}}" method="POST" enctype="multipart/form-data" role="form">
            	{{csrf_field()}}
        <input type="hidden" name="_method" value="PATCH">
		<div class="top-cs box-block shadow-gray mb-3">
        	<h5 class="mb-2">Material Incident Details</h5>
        	<div class="box-block pb-0">
        			<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="date" class="col-form-label  ">Date
							</label>
							<input class="form-control select-box" type="date" name="date" id="date" placeholder="Date" value="{{ $material_incident->date }}" onclick="this.showPicker()">
						</div>
					</div>
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="uom" class="col-form-label  ">UOM
							</label>
							<input class="form-control select-box" type="text" value="{{ $material_incident->uom }}" name="uom" id="uom" placeholder="UOM">
						</div>
					</div>
					@if(count($materials) > 0)
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="material_name" class="col-form-label  ">Materials
							<span class="look-a-like">*</span></label>
							<select class="form-control select-box" name="material_id" id="material_id" required>
								<option value="">Please Select Material Name</option>
								@foreach($materials as $material)
								<option value="{{$material->id}}" {{ $material->id == $material_incident->material_id ? 'selected' : '' }}>{{$material->name}}</option>
								@endforeach									
							</select>
						</div>
					</div>
					@endif
                                        <div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="material_indent_note" class="col-form-label  ">Material Indent Note
							</label>
							<input class="form-control select-box" type="text" value="{{ $material_incident->material_indent_note }}" name="material_indent_note"  id="material_indent_note" placeholder="Material Indent Note">
						</div>
					</div>
 
					@if(count($districts) > 0)
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="districts_name" class="col-form-label">Districts
							<span class="look-a-like">*</span></label>
							<select class="form-control select-box" name="district_id" id="district_id" required>
								<option value="">Please Select District</option>
								@foreach($districts as $dist)
								<option value="{{$dist->id}}" {{ $dist->id == $material_incident->district_id ? 'selected' : '' }}>{{$dist->name}}</option>
								@endforeach									
							</select>
						</div>
					</div>
			            @endif

                                     
                                      @if(count($blocks) > 0)
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="districts_name" class="col-form-label">Blocks
							<span class="look-a-like">*</span></label>
							<select class="form-control select-box" name="block_id" id="block_id" required>
								<option value="">Please Select Block</option>
								@foreach($blocks as $dist)
								<option value="{{$dist->id}}" {{ $dist->id == $material_incident->block_id ? 'selected' : '' }}>{{$dist->name}}</option>
								@endforeach									
							</select>
						</div>
					</div>
			               @endif

                                      @if(count($fromgp_list) > 0)
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="districts_name" class="col-form-label">From Gp
							<span class="look-a-like">*</span></label>
							<select class="form-control select-box" name="from_gp" id="from_gp" required>
								<option value="">Please Select From GP</option>
								@foreach($fromgp_list as $dist)
								<option value="{{$dist->id}}" {{ $dist->id == $material_incident->from_gp ? 'selected' : '' }}>{{$dist->gp_name}}</option>
								@endforeach									
							</select>
						</div>
					</div>
			               @endif


                                     @if(count($togp_list) > 0)
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="districts_name" class="col-form-label">From Gp
							<span class="look-a-like">*</span></label>
							<select class="form-control select-box" name="to_gp" id="to_gp" required>
								<option value="">Please Select To GP</option>
								@foreach($togp_list as $dist)
								<option value="{{$dist->id}}" {{ $dist->id == $material_incident->to_gp ? 'selected' : '' }}>{{$dist->gp_name}}</option>
								@endforeach									
							</select>
						</div>
					</div>
			               @endif

					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="required_qty" class="col-form-label  ">Required Quantity
							</label>
							<input class="form-control select-box" type="text" value="{{ $material_incident->required_qty }}" name="required_qty"  id="required_qty" placeholder="Returned Quantity">
						</div>
					</div>	
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="remarks" class="col-form-label  ">Remarks
							</label>
							<input class="form-control select-box" type="text" value="{{ $material_incident->remarks }}" name="remarks"  id="remarks" placeholder="Remarks">
						</div>
					</div>						
					<div class="form-group row">
						<label for="zipcode" class="col-xs-12 col-form-label"></label>
						<div class="col-xs-12 mt-2">
							<button type="submit" class="btn btn-primary btn-cstm pull-right ">@lang('admin.material_incident.update_material_incident')</button>
							<a href="{{route('admin.material_incident.index')}}" class="btn btn-default pull-right ">@lang('admin.cancel')</a>
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
</script>
@endsection
