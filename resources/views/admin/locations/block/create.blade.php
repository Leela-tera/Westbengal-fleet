@extends('admin.layout.base')

@section('title', 'Add Block ')

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
            <a href="{{ route('admin.location.block') }}" class="btn btn-default pull-right"><i class="fa fa-angle-left"></i> @lang('admin.back')</a>

			<h5 style="margin-bottom: 2em;">@lang('admin.location.new_block')</h5>

      <form class="form-horizontal" action="{{route('admin.location.block.store')}}" method="POST" enctype="multipart/form-data" role="form">
            	{{csrf_field()}}
        <div class="top-cs box-block shadow-gray">
        	<h5 class="mb-2">Block Details</h5>
        	<div class="box-block">
					<div class="form-group row">
						<div class="col-sm-12 col-md-10">
							<label for="block_name" class="col-form-label ">@lang('admin.location.block_name')
							<span class="look-a-like">*</span></label>
							<input class="form-control select-box" type="text" value="{{ old('block_name') }}" name="block_name" required id="block_name" placeholder="Block Name">
						</div>
					</div>
					@if(count($districts) > 0)
						<div class="form-group row">
							<label for="district" class="col-xs-12 col-form-label">Districts
							<span class="look-a-like">*</span></label></label>
							<div class="col-xs-10">
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
						<div class="col-sm-12 col-md-10">
							<label for="m_s_code" class="col-form-label ">Ms Code							<span class="look-a-like">*</span></label>
							<input class="form-control select-box" type="text" value="{{ old('m_s_code') }}" name="m_s_code" required id="m_s_code" placeholder="MS Code">
						</div>
					</div>


					<div class="form-group row">
						<label for="zipcode" class="col-xs-12 col-form-label"></label>
						<div class="col-xs-12 mt-2">
							<button type="submit" class="btn btn-primary btn-cstm pull-right ">@lang('admin.location.add_block')</button>
							<a href="{{route('admin.location.block')}}" class="btn btn-default pull-right ">@lang('admin.cancel')</a>
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
@endsection
