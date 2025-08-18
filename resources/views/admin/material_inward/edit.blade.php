@extends('admin.layout.base')

@section('title', 'Update Material Inward ')

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
            <a href="{{ route('admin.material_inward.index') }}" class="btn btn-default pull-right"><i class="fa fa-angle-left"></i> @lang('admin.back')</a>

			<h5 style="margin-bottom: 2em;">@lang('admin.material_inward.update_material_inward')</h5>

      <form class="form-horizontal" action="{{route('admin.material_inward.update', $material_inward->id )}}" method="POST" enctype="multipart/form-data" role="form">
            	{{csrf_field()}}
        <input type="hidden" name="_method" value="PATCH">
		<div class="top-cs box-block shadow-gray mb-3">
        	<h5 class="mb-2">Material Inward Details</h5>
        	<div class="box-block pb-0">
        			<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="date" class="col-form-label  ">Date
							</label>
							<input class="form-control select-box" type="date" name="date" id="date" placeholder="Date" value="{{ $material_inward->date }}" onclick="this.showPicker()">
						</div>
					</div>
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="mrn" class="col-form-label  ">GRIN No/ MRN No							</label>
							<input class="form-control select-box" type="text" value="{{ $material_inward->mrn }}" name="mrn"  id="mrn" placeholder="Material Return Note No">
						</div>
					</div>
      					@if(count($materials) > 0)
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="material_name" class="col-form-label  ">Materials Description
							<span class="look-a-like">*</span></label>
							<select class="form-control select-box" name="material_id" id="material_id" required>
								<option value="">Please Select Material Name</option>
								@foreach($materials as $material)
								<option value="{{$material->id}}" {{ $material->id == $material_inward->material_id ? 'selected' : '' }}>{{$material->name}}</option>
								@endforeach									
							</select>
						</div>
					</div>
					@endif
                                        <div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="uom" class="col-form-label  ">UOM
							</label>
							<input class="form-control select-box" type="text" value="{{ $material_inward->uom }}" name="uom" id="uom" placeholder="UOM">
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
								<option value="{{$dist->id}}" {{ $dist->id == $material_inward->district_id ? 'selected' : '' }}>{{$dist->name}}</option>
								@endforeach									
							</select>
						</div>
					</div>
			        @endif 
  
			                  <div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="uom" class="col-form-label  ">Location Name
							</label>
							<input class="form-control select-box" type="text" name="location_name" value="{{ $material_inward->location_name }}" id="location_name" placeholder="Location Name">
						</div>
					</div>

                                         <div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="supplier" class="col-form-label  ">Supplier Name
							</label>
							<input class="form-control select-box" type="text" name="supplier_name" value="{{ $material_inward->supplier_name }}" id="supplier_name" placeholder="Supplier Name">
						</div>
					</div>


                                         <div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="mr_person" class="col-form-label  ">Invoice No</label>
							<input class="form-control select-box" type="text" value="{{ $material_inward->invoice_no }}" name="invoice_no"  id="invoice_no" placeholder="Enter Input">
						</div>
					</div>

                                        <div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="dcn0" class="col-form-label  ">Dc No</label>
							<input class="form-control select-box" type="text" value="{{ $material_inward->dc_no }}" name="dc_no"  id="dc_no" placeholder="Enter Input">
						</div>
					</div>

                                        <div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="waybil_no" class="col-form-label  ">Waybill No/Date</label>
							<input class="form-control select-box" type="text" value="{{ $material_inward->waybil_no }}" name="waybil_no"  id="waybil_no" placeholder="Enter Input">
						</div>
					</div>

                                         <div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="lr_no" class="col-form-label  ">Lr No</label>
							<input class="form-control select-box" type="text" value="{{ $material_inward->lr_no }}" name="lr_no"  id="lr_no" placeholder="Enter Input">
						</div>
					</div>
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="transport_name" class="col-form-label  ">Transporter Name</label>
							<input class="form-control select-box" type="text" value="{{ $material_inward->transport_name}}" name="transport_name"  id="transport_name" placeholder="Enter Input">
						</div>
					</div>
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="dc_qty" class="col-form-label  ">DC Quantity
							</label>
							<input class="form-control select-box" type="text" value="{{$material_inward->dc_qty }}" name="dc_qty"  id="dc_qty" placeholder="Enter Input">
						</div>
					</div>
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="received_qty" class="col-form-label  ">Received Quantity
							</label>
							<input class="form-control select-box" type="text" value="{{$material_inward->received_qty }}" name="received_qty" required id="received_qty" placeholder="Enter Input">
						</div>
					</div>
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="rejacted_qty" class="col-form-label  ">Rejacted Quantity
							</label>
							<input class="form-control select-box" type="text" value="{{$material_inward->rejacted_qty}}" name="rejacted_qty" required id="rejacted_qty" placeholder="Enter Input">
						</div>
					</div>
                                        <div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="accepted_qty" class="col-form-label  ">Accepted Quantity
							</label>
							<input class="form-control select-box" type="text" value="{{$material_inward->accepted_qty}}" name="accepted_qty" required id="accepted_qty" placeholder="Enter Input">
						</div>
					</div>

					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="drum_no" class="col-form-label  ">Drum Nos
							</label>
							<input class="form-control select-box" type="text" value="{{ $material_inward->drum_no}}" name="drum_no"  id="drum_no" placeholder="Enter Input">
						</div>
					</div>
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="no_of_equipment" class="col-form-label  ">Sl Nos Of Equipment</label>
							<input class="form-control select-box" type="text" value="{{ $material_inward->no_of_equipment}}" name="no_of_equipment"  id="no_of_equipment" placeholder="Enter Input">
						</div>
					</div>	

				        <div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="remarks" class="col-form-label  ">Remarks
							</label>
							<input class="form-control select-box" type="text" value="{{ $material_inward->remarks }}" name="remarks"  id="remarks" placeholder="Remarks">
						</div>
					</div>						
					<div class="form-group row">
						<label for="zipcode" class="col-xs-12 col-form-label"></label>
						<div class="col-xs-12 mt-2">
							<button type="submit" class="btn btn-primary btn-cstm pull-right ">@lang('admin.material_inward.update_material_inward')</button>
							<a href="{{route('admin.material_inward.index')}}" class="btn btn-default pull-right ">@lang('admin.cancel')</a>
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
