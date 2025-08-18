@extends('admin.layout.base')

@section('title', 'Add Return Note ')

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

			<h5 style="margin-bottom: 2em;">@lang('admin.return_note.new_return_note')</h5>
      <form class="form-horizontal" action="{{route('admin.return_note.store')}}" method="POST" enctype="multipart/form-data" role="form">
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
							<label for="mrn" class="col-form-label  ">Material Return Note No
							</label>
							<input class="form-control select-box" type="text" value="{{ old('mrn') }}" name="mrn"  id="mrn" placeholder="Material Return Note No">
						</div>
					</div>
					@if(count($materials) > 0)
					<div class="form-group row field_wrapper_material">
						<div class="col-sm-1 col-md-1">
							<label for="material_name" class="col-form-label  ">Materials
							<span class="look-a-like">*</span></label>
							<select class="form-control select-box" name="materials[]" id="material_name" required>
								<option value="">Material</option>
								@foreach($materials as $material)
								<option value="{{$material->id}}">{{$material->name}}</option>
								@endforeach									
							</select>
						</div>
                                                <div class="col-sm-1 col-md-1">
							<label for="uom" class="col-form-label  ">UOM</label>
                                                        <select class="form-control select-box" name="uom[]" id="uom" required>
                                                                <option value="">Uom</option>
								<option value="Mtrs">Mtrs</option>
                                                                <option value="Nos">Nos</option>
                                                                <option value="Set">Set</option>									
							</select>
						</div>

                                               <div class="col-sm-2 col-md-2">
							<label for="issued_qty" class="col-form-label  ">Issued Quantity
							</label>
							<input class="form-control select-box" type="number" value="{{ old('issued_qty') }}" name="issued_qty[]" required id="issued_qty" placeholder="Issued Quantity">
						</div>

                      
                                              <div class="col-sm-2 col-md-2">
							<label for="balance_at_location" class="col-form-label  ">Balance At Location
							</label>
							<input class="form-control select-box" type="text" value="{{ old('balance_at_location') }}" name="balance_at_location[]"  id="balance_at_location" placeholder="Balance At Location">
						</div>

                                               <div class="col-sm-2 col-md-2">
							<label for="returned_qty" class="col-form-label  ">Returned Quantity
							</label>
							<input class="form-control select-box" type="number" value="{{ old('returned_qty') }}" name="returned_qty[]" required id="returned_qty" placeholder="Returned Quantity">
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
							<select class="form-control select-box" name="districts_name" id="districts_name" required>
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
							<label for="mr_person" class="col-form-label  ">Material Returning Person Name
							</label>
							<input class="form-control select-box" type="text" value="{{ old('mr_person') }}" name="mr_person"  id="mr_person" placeholder="Material Returning Person Name">
						</div>
					</div>
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="material_indent_note" class="col-form-label  ">Material Indent Note
							</label>
							<input class="form-control select-box" type="text" value="{{ old('material_indent_note') }}" name="material_indent_note"  id="material_indent_note" placeholder="Material Indent Note">
						</div>
					</div>
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="issue_note_no" class="col-form-label  ">Issue Note No
							</label>
							<input class="form-control select-box" type="text" value="{{ old('issue_note_no') }}" name="issue_note_no"  id="issue_note_no" placeholder="Issue Note No">
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
    var addButton = $('.add_multi_material'); //Add button selector
    var wrapper = $('.field_wrapper_material'); //Input field wrapper
    var fieldHTML = '<div class="clearfix"></div><div class="form-group row" style="margin:11px;"><div class="col-sm-1 col-md-1"><select class="form-control select-box" name="materials[]"><option value="">Material</option>@foreach($materials as $material)<option value="{{$material->id}}">{{$material->name}}</option>@endforeach</select></div><div class="col-sm-1 col-md-1"><select class="form-control select-box" name="uom[]" id="uom" required><option value="">UOM</option><option value="Mtrs">Mtrs</option><option value="Nos">Nos</option><option value="Set">Set</option></select></div><div class="col-sm-2 col-md-2"><input class="form-control select-box" type="text" name="issued_qty[]" value="" placeholder="Issued Qunatity"/></div><div class="col-sm-2 col-md-2"><input class="form-control select-box" type="text" name="balance_at_location[]" value="" placeholder="Balance at location"/></div><div class="col-sm-2 col-md-2"><input class="form-control select-box" type="text" name="returned_qty[]" value="" placeholder="Returned Qunatity"/></div><a href="javascript:void(0);" class="remove_multi_button"><i class="fa fa-trash"></i></a></div></div><div class="clearfix"></div>'; //New input field html 
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
