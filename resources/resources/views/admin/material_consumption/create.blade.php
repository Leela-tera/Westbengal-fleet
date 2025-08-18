@extends('admin.layout.base')

@section('title', 'Add Material Consumption ')

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
            <a href="{{ route('admin.material_consumption.index') }}" class="btn btn-default pull-right"><i class="fa fa-angle-left"></i> @lang('admin.back')</a>

			<h5 style="margin-bottom: 2em;">@lang('admin.mt_consumption.new_mt_consumption')</h5>
      <form class="form-horizontal" action="{{route('admin.material_consumption.store')}}" method="POST" enctype="multipart/form-data" role="form">
            	{{csrf_field()}}
        <div class="top-cs box-block shadow-gray mb-3">
        	<h5 class="mb-2">Material Consumption Details</h5>
        	<div class="box-block pb-0">
        	<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="date" class="col-form-label  ">Date
							</label>
							<input class="form-control select-box" type="date" name="date" id="date" placeholder="Date" onclick="this.showPicker()">
						</div>
					</div>
					@if(count($materials) > 0)
					<div class="form-group row field_wrapper_material">
						<div class="col-sm-2 col-md-2">
							<label for="material_name" class="col-form-label  ">Materials
							<span class="look-a-like">*</span></label>
							<select class="form-control select-box" name="materials[]" id="material_name" required>
								<option value="">Please Select Material Name</option>
								@foreach($materials as $material)
								<option value="{{$material->id}}">{{$material->name}}</option>
								@endforeach									
							</select>
						</div>

                                               <div class="col-sm-1 col-md-1">
							<label for="uom" class="col-form-label  ">UOM</label>
                                                        <select class="form-control select-box" name="uom[]" id="uom" required>
                                                                <option value="">UOM</option>
								<option value="Mtrs">Mtrs</option>
                                                                <option value="Nos">Nos</option>
                                                                <option value="Set">Set</option>									
							</select>
						</div>

                                              <div class="col-sm-2 col-md-2">
							<label for="drum_no" class="col-form-label  ">Drum Nos
							</label>
                                                        <select class="form-control select-box" name="drum_no[]" id="drum_no" required>
								<option value="">Please Select Drum</option>
								@foreach($drums as $drum)
								<option value="{{$drum->drum_no}}">{{$drum->drum_no}}</option>
								@endforeach									
							</select>
                                                </div>

                                             <div class="col-sm-1 col-md-1">
							<label for="start_meter" class="col-form-label  ">Start Meter
							</label>
							<input class="form-control select-box" type="text" value="{{ old('start_meter') }}" name="start_meter[]"  id="start_meter" placeholder="Start Meter">
						</div>

                                           <div class="col-sm-1 col-md-1">
							<label for="end_meter" class="col-form-label  ">End Meter
							</label>
							<input class="form-control select-box" type="text" value="{{ old('end_meter') }}" name="end_meter[]"  id="end_meter" placeholder="End Meter">
						</div>


                                          <div class="col-sm-2 col-md-2">
							<label for="gprs_coordinates" class="col-form-label  ">GPRS Coordinates
							</label>
							<input class="form-control select-box" type="text" value="{{ old('gprs_coordinates') }}" name="gprs_coordinates[]"  id="gprs_coordinates" placeholder="GPRS Coordinates">
						</div>

                                            <div class="col-sm-2 col-md-2">
							<label for="consumed_qty" class="col-form-label  ">Consumed Quantity
							</label>
							<input class="form-control select-box" type="text" value="{{ old('consumed_qty') }}" name="consumed_qty[]"  id="consumed_qty" placeholder="Consumed Quantity">
						</div>

                                           <div class="col-sm-2 col-md-2" style="margin-top:3em;">
                                                <label>&nbsp;</label>
                                               <a href="javascript:void(0);" class="add_multi_material" title="Add field"><i class="fa fa-plus"></i> Add More</a> 
                                               </div> 



					</div>
					@endif

					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="material_indent_note" class="col-form-label  ">Material Indent Note
							</label>
							<input class="form-control select-box" type="text" value="{{ old('material_indent_note') }}" name="material_indent_note"  id="material_indent_note" placeholder="Material Indent Note">
						</div>
					</div>			
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="min_no" class="col-form-label  ">Min No
							</label>
							<input class="form-control select-box" type="text" value="{{ old('min_no') }}" name="min_no"  id="min_no" placeholder="Min No">
						</div>
					</div>
					@if(count($districts) > 0)
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="issued_district" class="col-form-label">Districts
							<span class="look-a-like">*</span></label>
							<select class="form-control select-box" name="issued_district" id="district" required>
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
							<label for="issued_block" class="col-form-label">Blocks
							<span class="look-a-like">*</span></label>
							<select class="form-control select-box" name="issued_block" id="block" required>
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
							<label for="from_gp" class="col-form-label  ">From GP
							</label>
							<input class="form-control select-box" type="text" value="{{ old('from_gp') }}" name="from_gp"  id="from_gp" placeholder="From GP">
						</div>
					</div>
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="to_gp" class="col-form-label  ">To GP
							</label>
							<input class="form-control select-box" type="text" value="{{ old('to_gp') }}" name="to_gp"  id="to_gp" placeholder="To GP">
						</div>
					</div>
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="link_name" class="col-form-label  ">Link Name
							</label>
							<input class="form-control select-box" type="text" value="{{ old('link_name') }}" name="link_name"  id="link_name" placeholder="Link Name">
						</div>
					</div>
					
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="status_link_up_down" class="col-form-label  ">Status Link Up/Down
							</label>
							<input class="form-control select-box" type="text" value="{{ old('status_link_up_down') }}" name="status_link_up_down"  id="status_link_up_down" placeholder="Status Link Up/Down">
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
							<button type="submit" class="btn btn-primary btn-cstm pull-right ">@lang('admin.mt_consumption.add_mt_consumption')</button>
							<a href="{{route('admin.material_consumption.index')}}" class="btn btn-default pull-right ">@lang('admin.cancel')</a>
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
  $.get('{{url("admin/ajax-blocks-id")}}/'+district,function(data) {
    $("#block").empty().append(data);      
  });
});
$(document).ready(function(){
    var maxField = 10; //Input fields increment limitation
    var addButton = $('.add_multi_material'); //Add button selector
    var wrapper = $('.field_wrapper_material'); //Input field wrapper
    var fieldHTML = '<div class="clearfix"></div><div class="form-group row" style="margin:11px;"><div class="col-sm-2 col-md-2"><select class="form-control select-box" name="materials[]"><option value="">Select Material</option>@foreach($materials as $material)<option value="{{$material->id}}">{{$material->name}}</option>@endforeach</select></div><div class="col-sm-1 col-md-1"><select class="form-control select-box" name="uom[]" id="uom" required><option value="">UOM</option><option value="Mtrs">Mtrs</option><option value="Nos">Nos</option><option value="Set">Set</option></select></div><div class="col-sm-2 col-md-2"><select class="form-control select-box" name="drum_no[]"><option value="">Select Drum No</option>@foreach($drums as $drum)<option value="{{$drum->drum_no}}">{{$drum->drum_no}}</option>@endforeach</select></div><div class="col-sm-1 col-md-1"><input class="form-control select-box" type="text" name="start_meter[]" value="" placeholder="S Meter"/></div><div class="col-sm-1 col-md-1"><input class="form-control select-box" type="text" name="end_meter[]" value="" placeholder="End Meter"/></div><div class="col-sm-2 col-md-2"><input class="form-control select-box" type="text" name="gprs_coordinates[]" value="" placeholder="GPS coordinates"/></div><div class="col-sm-2 col-md-2"><input class="form-control select-box" type="text" name="consumed_qty[]" value="" placeholder="Consumed Quantity"/></div><a href="javascript:void(0);" class="remove_multi_button"><i class="fa fa-trash"></i></a></div></div><div class="clearfix"></div>'; //New input field html 
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
