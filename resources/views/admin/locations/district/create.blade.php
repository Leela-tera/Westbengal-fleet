@extends('admin.layout.base')

@section('title', 'Add District ')

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
            <a href="{{ route('admin.location.index') }}" class="btn btn-default pull-right"><i class="fa fa-angle-left"></i> @lang('admin.back')</a>

			<h5 style="margin-bottom: 2em;">@lang('admin.location.new_district')</h5>

      <form class="form-horizontal" action="{{route('admin.location.store')}}" method="POST" enctype="multipart/form-data" role="form">
            	{{csrf_field()}}
        <div class="top-cs box-block shadow-gray">
        	<h5 class="mb-2">District Details</h5>
        	<div class="box-block">
					<div class="form-group row">
						<div class="col-sm-12 col-md-12">
							<label for="district_name" class="col-form-label  ">@lang('admin.location.district_name')
							<span class="look-a-like">*</span></label>
							<input class="form-control select-box" type="text" value="{{ old('district_name') }}" name="district_name" required id="district_name" placeholder="District Name">
						</div>
					</div>

					<div class="form-group row">
						<label for="zipcode" class="col-xs-12 col-form-label"></label>
						<div class="col-xs-12 mt-2">
							<button type="submit" class="btn btn-primary btn-cstm pull-right ">@lang('admin.location.add_district')</button>
							<a href="{{route('admin.location.index')}}" class="btn btn-default pull-right ">@lang('admin.cancel')</a>
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
