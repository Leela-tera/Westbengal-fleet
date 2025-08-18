@extends('admin.layout.base')

@section('title', 'Add Issued Material ')

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

			<h5 style="margin-bottom: 2em;">@lang('admin.mt_issue.new_mt_issue')</h5>
      <form class="form-horizontal" action="{{route('admin.material_issue.store')}}" method="POST" enctype="multipart/form-data" role="form">
            	{{csrf_field()}}
        <div class="top-cs box-block shadow-gray mb-3">
        	<h5 class="mb-2">Material Issue Details</h5>
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
						<div class="col-sm-3 col-md-3">
							<label for="material_name" class="col-form-label  ">Materials
							<span class="look-a-like">*</span></label>
							<select class="form-control select-box" name="materials[]" id="material_name" required>
								<option value="">Please Select Material</option>
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
							<label for="material_indent_qty" class="col-form-label  ">Materials Indent Quantity
							</label>
							<input class="form-control select-box" type="text" value="{{ old('material_indent_qty') }}" name="material_indent_qty[]"  id="material_indent_qty" placeholder="Material Indent Quantity">
						</div>

                                            <div class="col-sm-2 col-md-2">
							<label for="issued_qty" class="col-form-label  ">MaterialsIssued Quantity
							</label>
							<input class="form-control select-box" type="text" value="{{ old('issued_qty') }}" name="issued_qty[]" id="issued_qty" placeholder="Issued Quantity">
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
							<label for="drum_no" class="col-form-label  ">Drum Nos
							</label>
                                                        <select class="form-control select-box" name="drum_no" id="drum_no" required>
								<option value="">Please Select Drum</option>
								@foreach($drums as $drum)
								<option value="{{$drum->drum_no}}">{{$drum->drum_no}}</option>
								@endforeach									
							</select>
                                                </div>
					</div>
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="si_no_equipment" class="col-form-label  ">SI Nos Of Equipment
							</label>
							<input class="form-control select-box" type="text" value="{{ old('si_no_equipment') }}" name="si_no_equipment" id="si_no_equipment" placeholder="SI Nos Equipment">
						</div>
					</div>
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="receiving_person_name" class="col-form-label  ">Materials Receiving Person Name
							</label>
							<input class="form-control select-box" type="text" value="{{ old('receiving_person_name') }}" name="receiving_person_name"  id="receiving_person_name" placeholder="Receiving Person Name">
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
							<button type="submit" class="btn btn-primary btn-cstm pull-right ">@lang('admin.mt_issue.add_mt_issue')</button>
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
   $.get('{{url("admin/ajax-blocks-id")}}/'+district,function(data) {
    $("#block").empty().append(data);      
  });
});

$(document).ready(function(){
    var maxField = 10; //Input fields increment limitation
    var addButton = $('.add_multi_material'); //Add button selector
    var wrapper = $('.field_wrapper_material'); //Input field wrapper
    var fieldHTML = '<div class="clearfix"></div><div class="form-group row" style="margin:11px;"><div class="col-sm-3 col-md-3"><select class="form-control select-box" name="materials[]"><option value="">Select Material</option>@foreach($materials as $material)<option value="{{$material->id}}">{{$material->name}}</option>@endforeach</select></div><div class="col-sm-2 col-md-2"><select class="form-control select-box" name="uom[]" id="uom" required><option value="">Select UOM</option><option value="Mtrs">Mtrs</option><option value="Nos">Nos</option><option value="Set">Set</option></select></div><div class="col-sm-2 col-md-2"><input class="form-control select-box" type="text" name="material_indent_qty[]" value="" placeholder="Material Indent Qunatity"/></div><div class="col-sm-2 col-md-2"><input class="form-control select-box" type="text" name="issued_qty[]" value="" placeholder="Issued Qunatity"/></div><a href="javascript:void(0);" class="remove_multi_button"><i class="fa fa-trash"></i></a></div></div><div class="clearfix"></div>'; //New input field html 
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
