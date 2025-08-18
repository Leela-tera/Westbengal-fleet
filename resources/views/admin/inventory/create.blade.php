@extends('admin.layout.base')

@section('title', 'Add Inventory ')

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
            <a href="{{ route('admin.inventory.index') }}" class="btn btn-default pull-right"><i class="fa fa-angle-left"></i> @lang('admin.back')</a>

			<h5 style="margin-bottom: 2em;">@lang('admin.inventory.new_inventory')</h5>
      <form class="form-horizontal" action="{{route('admin.inventory.store')}}" method="POST" enctype="multipart/form-data" role="form">
            	{{csrf_field()}}
        <div class="top-cs box-block shadow-gray mb-3">
        	<h5 class="mb-2">Inventory Details</h5>
        	<div class="box-block pb-0">
        			<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="date" class="col-form-label  ">Date
							</label>
							<input class="form-control select-box" type="date" name="date" id="date" placeholder="Date" onclick="this.showPicker()">
						</div>
					</div>
					@if(count($materials) > 0)
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="material_id" class="col-form-label  ">Materials Description
							<span class="look-a-like">*</span></label>
							<select class="form-control select-box" name="material_id" id="material_id" required>
								<option value="">Please Select Material Name</option>
								@foreach($materials as $material)
								<option value="{{$material->id}}">{{$material->name}}</option>
								@endforeach									
							</select>
						</div>
					</div>
					@endif
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="uom" class="col-form-label  ">UOM
							</label>
							<input class="form-control select-box" type="text" value="{{ old('uom') }}" name="uom"  id="uom" placeholder="UOM">
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
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="opening_stock" class="col-form-label  ">Opening Stock
							</label>
							<input class="form-control select-box" type="text" value="{{ old('opening_stock') }}" name="opening_stock"  id="opening_stock" placeholder="Opening Stock">
						</div>
					</div>
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="inward" class="col-form-label  ">Inward
							</label>
							<input class="form-control select-box" type="text" value="{{ old('inward') }}" name="inward"  id="inward" placeholder="Inward">
						</div>
					</div>
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="issued_qty" class="col-form-label  ">Issued Quantity
							</label>
							<input class="form-control select-box" type="text" value="{{ old('issued_qty') }}" name="issued_qty"  id="issued_qty" placeholder="Issued Quantity">
						</div>
					</div>
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="mrn_qty" class="col-form-label  ">MRN Quantity
							</label>
							<input class="form-control select-box" type="text" value="{{ old('mrn_qty') }}" name="mrn_qty"  id="mrn_qty" placeholder="MRN Quantity">
						</div>
					</div>
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="closing_stock" class="col-form-label  ">Closing Stock
							</label>
							<input class="form-control select-box" type="text" value="{{ old('closing_stock') }}" name="closing_stock"  id="closing_stock" placeholder="Closing Stock">
						</div>
					</div>
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="comments" class="col-form-label  ">Remarks
							</label>
							<input class="form-control select-box" type="text" value="{{ old('comments') }}" name="comments"  id="comments" placeholder="Remarks">
						</div>
					</div>
					<div class="form-group row">
						<label for="zipcode" class="col-xs-12 col-form-label"></label>
						<div class="col-xs-12 mt-2">
							<button type="submit" class="btn btn-primary btn-cstm pull-right ">@lang('admin.inventory.add_inventory')</button>
							<a href="{{route('admin.inventory.index')}}" class="btn btn-default pull-right ">@lang('admin.cancel')</a>
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
