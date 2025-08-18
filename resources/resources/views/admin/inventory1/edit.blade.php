@extends('admin.layout.base')

@section('title', 'Update Inventory ')

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

			<h5 style="margin-bottom: 2em;">@lang('admin.inventory.update_inventory')</h5>

      <form class="form-horizontal" action="{{route('admin.inventory.update', $inventory->id )}}" method="POST" enctype="multipart/form-data" role="form">
            	{{csrf_field()}}
        <input type="hidden" name="_method" value="PATCH">
		<div class="top-cs box-block shadow-gray mb-3">
        	<h5 class="mb-2">Inventory Details</h5>
        	<div class="box-block pb-0">
					@if(count($materials) > 0)
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="material_id" class="col-form-label  ">Material List
							<span class="look-a-like">*</span></label>
							<select class="form-control select-box" name="material_id" id="material_id" required>
								<option value="">Please Select Item</option>
								@foreach($materials as $material)
								<option value="{{$material->id}}" {{ $material->id == $inventory->material_id ? 'selected' : '' }}>{{$material->name}}</option>
								@endforeach									
							</select>
						</div>
					</div>
					@endif
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="supplier_name" class="col-form-label  ">Supplier Name
							</label>
							<input class="form-control select-box" type="text" value="{{ $inventory->supplier_name }}" name="supplier_name"  id="supplier_name" placeholder="Supplier Name">
						</div>
					</div>
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="way_bill_no" class="col-form-label  ">Way Bill No
							</label>
							<input class="form-control select-box" type="text" value="{{ $inventory->way_bill_no }}" name="way_bill_no"  id="way_bill_no" placeholder="Way Bill No">
						</div>
					</div>
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="way_bill_date" class="col-form-label">Way Bill Date</label>
							<input class="form-control select-box" type="date" name="way_bill_date" value="{{ $inventory->way_bill_date }}" id="way_bill_date" placeholder="Way Bill Date" onclick="this.showPicker()">
						</div>	
					</div>
					@if(count($blocks) > 0)
			    <div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="block" class="col-form-label">Blocks
							<span class="look-a-like">*</span></label>
							<select class="form-control select-box" name="block" id="block" required>
								<option value="">Please Select Block</option>
								@foreach($blocks as $block)
								<option value="{{$block->id}}" {{ $block->id == $inventory->block_id ? 'selected' : '' }}>{{$block->name}}</option>
								@endforeach									
							</select>
						</div>
					</div>
			    @endif
					@if(count($providers) > 0)
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="contact" class="col-form-label  ">Contact Person
							</label>
							<select class="form-control select-box" name="contact" id="contact" >
								<option value="">Please Select Contact Person</option>
								@foreach($providers as $provider)
								<option value="{{$provider->id}}" {{ $provider->id == $inventory->provider_id ? 'selected' : '' }}>{{ $provider->first_name }} {{ $provider->last_name }}</option>
								@endforeach									
							</select>
						</div>
					</div>
					@endif
			    <div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="ct_name" class="col-form-label  ">Courier & Transporter Name
							</label>
							<input class="form-control select-box" type="text" value="{{ $inventory->transport_name }}" name="ct_name"  id="ct_name" placeholder="Courier & Transporter Name">
						</div>
					</div>
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="dc_lr_no" class="col-form-label  ">DC/LR NO
							</label>
							<input class="form-control select-box" type="number" value="{{ $inventory->dc_lr_no }}" name="dc_lr_no"  id="dc_lr_no" placeholder="DC/LR NO">
						</div>
					</div>
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="uom" class="col-form-label  ">UOM
							</label>
							<input class="form-control select-box" type="text" value="{{ $inventory->uom }}" name="uom"  id="uom" placeholder="UOM">
						</div>
					</div>
			    <div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="status" class="col-form-label">Status
							<span class="look-a-like">*</span></label>
							<select class="form-control select-box" name="status" id="status">
								<option value="">Please Select Status</option>
								<option value="in-stock" {{ $inventory->status == 'in-stock' ? 'selected' : '' }}>In Stock</option>
								<option value="out-of-stock" {{ $inventory->status == 'out-of-stock' ? 'selected' : '' }}>Out Of Stock</option>
								<option value="missing" {{ $inventory->status == 'missing' ? 'selected' : '' }}>Missing</option>
							</select>
						</div>
					</div>
			</div>
		</div>
		<div class="top-cs box-block shadow-gray">
        	<h5 class="mb-2">Stock Information</h5>
        	<div class="box-block pb-0">
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="received_qty" class="col-form-label  ">Received Qty cable
							</label>
							<input class="form-control select-box" type="number" value="{{ $inventory->received_qty }}" name="received_qty" required id="received_qty" placeholder="Received Quantity cable">
						</div>
					</div>
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="rejected_qty" class="col-form-label  ">Rejected/Shortage Qty
							</label>
							<input class="form-control select-box" type="text" value="{{ $inventory->rejected_qty }}" name="rejected_qty"  id="rejected_qty" placeholder="Rejected/Shortage Quantity">
						</div>
					</div>
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="good_condition" class="col-form-label  ">Good Condition
							</label>
							<input class="form-control select-box" type="text" value="{{ $inventory->good_condition }}" name="good_condition"  id="good_condition" placeholder="Quantity received in Good Condition">
						</div>
					</div>					
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="received_date" class="col-form-label">Received Date</label>
							<input class="form-control select-box" type="date" name="received_date" value="{{ $inventory->received_date }}" id="received_date" placeholder="Received Date" onclick="this.showPicker()">
						</div>	
					</div>
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="grn" class="col-form-label  ">Received not no GRN
							</label>
							<input class="form-control select-box" type="text" value="{{ old('grn') }}" name="grn"  id="grn" placeholder="GRN">
						</div>
					</div>
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="comments" class="col-form-label  ">Comments
							</label>
							<input class="form-control select-box" type="text" value="{{ $inventory->comments }}" name="comments"  id="comments" placeholder="Comments">
						</div>
					</div>
					<div class="form-group row">
						<label for="zipcode" class="col-xs-12 col-form-label"></label>
						<div class="col-xs-12 mt-2">
							<button type="submit" class="btn btn-primary btn-cstm pull-right ">@lang('admin.inventory.update_inventory')</button>
							<a href="{{route('admin.inventory.index')}}" class="btn btn-default pull-right ">@lang('admin.cancel')</a>
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
