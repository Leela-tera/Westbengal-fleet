@extends('admin.layout.base')

@section('title', 'Inventory Dashboard ')

@section('styles')
	<link rel="stylesheet" href="{{asset('main/vendor/jvectormap/jquery-jvectormap-2.0.3.css')}}">
@endsection

@section('content')
<?php
use \Carbon\Carbon;
$todaydate = Carbon::today();
$today = $todaydate->toDateString();

$yesterdaydate = Carbon::yesterday();
$yesterday= $yesterdaydate->toDateString();

?>
<div class="content-area py-1">
<div class="container-fluid">
    <div class="row row-md">
                <div class="col-lg-3 col-md-6 col-xs-12">
			<a href="{{ url('/admin/material_inward') }}"><div class="box box-block bg-white tile tile-1 mb-2">
				<div class="t-icon right"><span class="bg-warning"></span><i class="ti-archive"></i></div>
				<div class="t-content">
					<h6 class="text-uppercase mb-1">@lang('admin.include.inward')</h6>
					<h1 class="mb-1">{{$inwards}}</h1>
				</div>
			</div></a>
		</div>

                 <div class="col-lg-3 col-md-6 col-xs-12">
			<a href="{{ url('/admin/material_incident') }}"><div class="box box-block bg-white tile tile-1 mb-2">
				<div class="t-icon right"><span class="bg-success"></span><i class="ti-rocket"></i></div>
				<div class="t-content">
					<h6 class="text-uppercase mb-1">@lang('admin.include.incident')</h6>
					<h1 class="mb-1">{{$incidents}}</h1>
				</div>
			</div></a>
		</div>
		
		
		 <div class="col-lg-3 col-md-6 col-xs-12">
			<a href="{{ url('/admin/inventory') }}"><div class="box box-block bg-white tile tile-1 mb-2">
				<div class="t-icon right"><span class="bg-danger"></span><i class="ti-rocket"></i></div>
				<div class="t-content">
					<h6 class="text-uppercase mb-1">@lang('admin.include.stock_statement')</h6>
					<h1 class="mb-1">{{$inventory}}</h1>
				</div>
			</div></a>
		</div>

				<div class="col-lg-3 col-md-6 col-xs-12">
							<a href="{{ url('/admin/parts') }}"><div class="box box-block bg-white tile tile-1 mb-2">
								<div class="t-icon right"><span class="bg-success"></span><i class="ti-bar-chart"></i></div>
								<div class="t-content">
									<h6 class="text-uppercase mb-1">@lang('admin.include.balance_material')</h6>
									<h1 class="mb-1">{{$parts}}</h1>
													   </div>
							</div></a>
				</div>
				
				<div class="col-lg-3 col-md-6 col-xs-12">
					<a href="{{ url('/admin/return_note') }}"><div class="box box-block bg-white tile tile-1 mb-2">
						<div class="t-icon right"><span class="bg-warning"></span><i class="ti-view-grid"></i></div>
						<div class="t-content">
							<h6 class="text-uppercase mb-1">@lang('admin.include.return_note')</h6>
							<h1 class="mb-1">{{$return_note}}</h1>
						</div>
					</div></a>
				</div>

				<div class="col-lg-3 col-md-6 col-xs-12">
					<a href="{{ url('/admin/material_issue') }}"><div class="box box-block bg-white tile tile-1 mb-2">
						<div class="t-icon right"><span class="bg-success"></span><i class="ti-view-grid"></i></div>
						<div class="t-content">
							<h6 class="text-uppercase mb-1">@lang('admin.include.issue')</h6>
							<h1 class="mb-1">{{$issues}}</h1>
						</div>
					</div></a>
				</div>

				<div class="col-lg-3 col-md-6 col-xs-12">
					<a href="{{ url('/admin/material_consumption') }}"><div class="box box-block bg-white tile tile-1 mb-2">
						<div class="t-icon right"><span class="bg-primary"></span><i class="ti-view-grid"></i></div>
						<div class="t-content">
							<h6 class="text-uppercase mb-1">@lang('admin.include.consumption')</h6>
							<h1 class="mb-1">{{$consumptions}}</h1>
						</div>
					</div></a>
				</div>
	
	</div>
</div>

@endsection
