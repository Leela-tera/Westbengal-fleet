@extends('admin.layout.base')

@section('title', 'Update Return Note ')

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
            <a href="{{ route('admin.return_note.index') }}" class="btn btn-default pull-right"><i class="fa fa-angle-left"></i> @lang('admin.back')</a>

			<h5 style="margin-bottom: 2em;">@lang('admin.return_note.update_return_note')</h5>

      <form class="form-horizontal" action="{{route('admin.return_note.update', $return_note->id )}}" method="POST" enctype="multipart/form-data" role="form">
            	{{csrf_field()}}
        <input type="hidden" name="_method" value="PATCH">
		<div class="top-cs box-block shadow-gray mb-3">
        	<h5 class="mb-2">Return Note Details</h5>
        	<div class="box-block pb-0">
					@if(count($materials) > 0)
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="material_name" class="col-form-label  ">Materials
							<span class="look-a-like">*</span></label>
							<select class="form-control select-box" name="material_name" id="material_name" required>
								<option value="">Please Select Material Name</option>
								@foreach($materials as $material)
								<option value="{{$material->id}}" {{ $material->id == $return_note->material_id ? 'selected' : '' }}>{{$material->name}}</option>
								@endforeach									
							</select>
						</div>
					</div>
					@endif
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="supplier_name" class="col-form-label  ">Supplier Name
							</label>
							<input class="form-control select-box" type="text" value="{{ $return_note->supplier_name }}" name="supplier_name"  id="supplier_name" placeholder="Supplier Name">
						</div>
					</div>
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="issue_note_no" class="col-form-label  ">Issue Note No
							</label>
							<input class="form-control select-box" type="text" value="{{ $return_note->isn }}" name="issue_note_no"  id="issue_note_no" placeholder="Issue Note No">
						</div>
					</div>
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="issued_date" class="col-form-label  ">Issue Note Date
							</label>
							<input class="form-control select-box" type="date" name="issued_date" id="issued_date" placeholder="Date" value="{{ $return_note->issue_note_date }}" onclick="this.showPicker()">
						</div>
					</div>
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="received_loc" class="col-form-label  ">Received Location
							</label>
							<input class="form-control select-box" type="text" value="{{ $return_note->received_loc }}" name="received_loc"  id="received_loc" placeholder="Received Location">
						</div>
					</div>					
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="uom" class="col-form-label  ">UOM
							</label>
							<input class="form-control select-box" type="text" value="{{ $return_note->uom }}" name="uom" id="uom" placeholder="UOM">
						</div>
					</div>
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="received_qty" class="col-form-label  ">Received Quantity
							</label>
							<input class="form-control select-box" type="number" value="{{ $return_note->received_qty }}" name="received_qty" required id="received_qty" placeholder="Received Quantity">
						</div>
					</div>
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="rejected_qty" class="col-form-label  ">Rejected/Shortage Quantity
							</label>
							<input class="form-control select-box" type="text" value="{{ $return_note->rejected_qty }}" name="rejected_qty"  id="rejected_qty" placeholder="Rejected/Shortage Quantity">
						</div>
					</div>
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="issued_qty_good_condition" class="col-form-label  ">Quantity in Good Condition
							</label>
							<input class="form-control select-box" type="text" value="{{ $return_note->good_condition }}" name="issued_qty_good_condition"  id="issued_qty_good_condition" placeholder="Quantity received in Good Condition">
						</div>
					</div>					
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="received_date" class="col-form-label">Received Date</label>
							<input class="form-control select-box" type="date" name="received_date" id="received_date" placeholder="Received Date" value="{{ $return_note->received_date }}" onclick="this.showPicker()">
						</div>	
					</div>
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="mrn" class="col-form-label  ">Material Return Note No
							</label>
							<input class="form-control select-box" type="text" value="{{ $return_note->mrn }}" name="mrn"  id="mrn" placeholder="Material Return Note No">
						</div>
					</div>
					<div class="form-group row">
						<label for="zipcode" class="col-xs-12 col-form-label"></label>
						<div class="col-xs-12 mt-2">
							<button type="submit" class="btn btn-primary btn-cstm pull-right ">@lang('admin.return_note.update_return_note')</button>
							<a href="{{route('admin.return_note.index')}}" class="btn btn-default pull-right ">@lang('admin.cancel')</a>
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
