@extends('admin.layout.base')

@section('title', 'Add Material Inward ')

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

			<h5 style="margin-bottom: 2em;">@lang('admin.material_inward.new_material_inward')</h5>
      <form class="form-horizontal" action="{{route('admin.material_inward.store')}}" method="POST" enctype="multipart/form-data" role="form">
            	{{csrf_field()}}
        <div class="top-cs box-block shadow-gray mb-3">
        	<h5 class="mb-2">Return Note Details</h5>
        	<div class="box-block pb-0">
        			<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="date" class="col-form-label  ">Date
							</label>
							<input class="form-control select-box" type="date" name="date" id="date" placeholder="Date" onclick="this.showPicker()">
						</div>
					</div>
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="mrn" class="col-form-label  ">GRIN No/ MRN No
							</label>
							<input class="form-control select-box" type="text" value="{{ old('mrn') }}" name="mrn"  id="mrn" placeholder="GRIN No/ MRN No">
						</div>
					</div>
					@if(count($materials) > 0)
					<div class="form-group row field_wrapper_material">
						<div class="col-sm-3 col-md-3">
							<label for="material_name" class="col-form-label  ">Materials Description
							<span class="look-a-like">*</span></label>
							<select class="form-control select-box" name="materials[]" id="material_id" required>
                                                                <option value="">Select Material</option>
								@foreach($materials as $material)
								<option value="{{$material->id}}">{{$material->name}}</option>
								@endforeach									
							</select>
						</div>

                                               <div class="col-sm-2 col-md-2">
							<label for="uom" class="col-form-label  ">UOM</label>
                                                        <select class="form-control select-box" name="uom[]" id="uom" required>
                                                                <option value="">Please Select</option>
								<option value="Mtrs">Mtrs</option>
                                                                <option value="Nos">Nos</option>
                                                                <option value="Set">Set</option>									
							</select>
						</div>
  

                                              <div class="col-sm-2 col-md-2">
							<label for="dc_qty" class="col-form-label  ">DC Quantity
							</label>
							<input class="form-control select-box" type="text" value="{{ old('dc_qty') }}" name="dc_qty[]"  id="dc_qty" placeholder="Enter Input">
						</div>
 
                                              <div class="col-sm-2 col-md-2">
							<label for="accepted_qty" class="col-form-label  ">Accepted Quantity
							</label>
							<input class="form-control select-box" type="text" value="{{ old('accepted_qty') }}" name="accepted_qty[]" required id="accepted_qty" placeholder="Enter Input">
						</div>  
                                               <div class="col-sm-2 col-md-2" style="margin-top:3em;">
                                                <label>&nbsp;</label>
                                               <a href="javascript:void(0);" class="add_multi_material" title="Add field"><i class="fa fa-plus"></i> Add More</a> 
                                               </div>  
					</div>
					@endif
                                         @if(count($districts) > 0)
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="districts_name" class="col-form-label">Districts
							<span class="look-a-like">*</span></label>
							<select class="form-control select-box" name="district_id" id="districts_name" required>
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
							<label for="uom" class="col-form-label  ">Location Name
							</label>
							<input class="form-control select-box" type="text" name="location_name" id="location_name" placeholder="Location Name">
						</div>
					</div>

                                         <div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="supplier" class="col-form-label  ">Supplier Name
							</label>
							<input class="form-control select-box" type="text" name="supplier_name" id="supplier_name" placeholder="Supplier Name">
						</div>
					</div>


                                         <div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="mr_person" class="col-form-label  ">Invoice No</label>
							<input class="form-control select-box" type="text" value="{{ old('invoice_no') }}" name="invoice_no"  id="invoice_no" placeholder="Enter Input">
						</div>
					</div>

                                        <div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="dcn0" class="col-form-label  ">Dc No</label>
							<input class="form-control select-box" type="text" value="{{ old('dc_no') }}" name="dc_no"  id="dc_no" placeholder="Enter Input">
						</div>
					</div>

                                        <div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="waybil_no" class="col-form-label  ">Waybill No/Date</label>
							<input class="form-control select-box" type="text" value="{{ old('waybil_no') }}" name="waybil_no"  id="waybil_no" placeholder="Enter Input">
						</div>
					</div>

                                         <div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="lr_no" class="col-form-label  ">Lr No</label>
							<input class="form-control select-box" type="text" value="{{ old('lr_no') }}" name="lr_no"  id="lr_no" placeholder="Enter Input">
						</div>
					</div>
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="transport_name" class="col-form-label  ">Transporter Name</label>
							<input class="form-control select-box" type="text" value="{{ old('transport_name') }}" name="transport_name"  id="transport_name" placeholder="Enter Input">
						</div>
					</div>
					</div>

					<div class="form-group row">
						<div class="col-sm-8 col-md-8 field_wrapper">
							<label for="drum_no" class="col-form-label  ">Drum Nos
							</label>
							<input class="form-control select-box" type="text" value="{{ old('drum_no') }}" name="drum_no[]"  id="drum_no" placeholder="Enter Input">
                                                        </div>

                                                <div class="col-sm-2 col-md-2" style="margin-top:3em;">
                                                <label>&nbsp;</label>
                                               <a href="javascript:void(0);" class="add_button" title="Add field"><i class="fa fa-plus"></i> Add More</a> 
                                               </div>  

					</div>
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="no_of_equipment" class="col-form-label  ">Sl Nos Of Equipment</label>
							<input class="form-control select-box" type="text" value="{{ old('no_of_equipment') }}" name="no_of_equipment"  id="no_of_equipment" placeholder="Enter Input">
						</div>
					</div>	
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="remarks" class="col-form-label  ">Remarks
							</label>
							<input class="form-control select-box" type="text" value="{{ old('remarks') }}" name="remarks"  id="remarks" placeholder="Remarks">
						</div>
					</div>	
					<div class="form-group row">
						<label for="zipcode" class="col-xs-12 col-form-label"></label>
						<div class="col-xs-12 mt-2">
							<button type="submit" class="btn btn-primary btn-cstm pull-right ">@lang('admin.return_note.add_return_note')</button>
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
$(document).ready(function(){
    var maxField = 10; //Input fields increment limitation
    var addButton = $('.add_button'); //Add button selector
    var wrapper = $('.field_wrapper'); //Input field wrapper
    var fieldHTML = '<div class="clearfix"></div><div class="form-group row" style="margin:11px;"><div class="col-sm-8 col-md-8"><input class="form-control select-box" type="text" name="drum_no[]" value="" placeholder="Drum No"/></div><a href="javascript:void(0);" class="remove_button"><i class="fa fa-trash"></i></a></div><div class="clearfix"></div>'; //New input field html 
    var x = 1; //Initial field counter is 1
    
    //Once add button is clicked
    $(addButton).click(function(){
        //Check maximum number of input fields
        if(x < maxField){ 
            x++; //Increment field counter
            $(wrapper).append(fieldHTML); //Add field html
        }
    });
    
    //Once remove button is clicked
    $(wrapper).on('click', '.remove_button', function(e){
        e.preventDefault();
        $(this).parent('div').remove(); //Remove field html
        x--; //Decrement field counter
    });
});

$(document).ready(function(){
    var maxField = 10; //Input fields increment limitation
    var addButton = $('.add_multi_material'); //Add button selector
    var wrapper = $('.field_wrapper_material'); //Input field wrapper
    var fieldHTML = '<div class="clearfix"></div><div class="form-group row" style="margin:11px;"><div class="col-sm-3 col-md-3"><select class="form-control select-box" name="materials[]"><option value="">Select Material</option>@foreach($materials as $material)<option value="{{$material->id}}">{{$material->name}}</option>@endforeach</select></div><div class="col-sm-2 col-md-2"><select class="form-control select-box" name="uom[]" id="uom" required><option value="">Select UOM</option><option value="Mtrs">Mtrs</option><option value="Nos">Nos</option><option value="Set">Set</option></select></div><div class="col-sm-2 col-md-2"><input class="form-control select-box" type="text" name="dc_qty[]" value="" placeholder="DC Qunatity"/></div><div class="col-sm-2 col-md-2"><input class="form-control select-box" type="text" name="accepted_qty[]" value="" placeholder="Accepted Qunatity"/></div><a href="javascript:void(0);" class="remove_multi_button"><i class="fa fa-trash"></i></a></div></div><div class="clearfix"></div>'; //New input field html 
    var x = 1; //Initial field counter is 1
    
    //Once add button is clicked
    $(addButton).click(function(){
        //Check maximum number of input fields
        if(x < maxField){ 
            x++; //Increment field counter
            $(wrapper).append(fieldHTML); //Add field html
        }
    });
    
    //Once remove button is clicked
    $(wrapper).on('click', '.remove_multi_button', function(e){
        e.preventDefault();
        $(this).parent('div').remove(); //Remove field html
        x--; //Decrement field counter
    });
});

</script>
@endsection
