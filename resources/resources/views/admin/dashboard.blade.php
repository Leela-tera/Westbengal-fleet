@extends('admin.layout.base')

@section('title', 'Dashboard ')

@section('styles')
	<!--<link rel="stylesheet" href="{{asset('main/vendor/jvectormap/jquery-jvectormap-2.0.3.css')}}">-->
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
		<a href="{{ url('/admin/tickets') }}"><div class="col-lg-3 col-md-6 col-xs-12">
			<div class="box box-block bg-white tile tile-1 mb-2">
				<div class="t-icon right"><span class="bg-danger"></span><i class="ti-rocket"></i></div>
				<div class="t-content">
					<h6 class="text-uppercase mb-1">@lang('admin.dashboard.Tickets')</h6>
					<h1 class="mb-1">{{$master_tickets}}</h1>
				</div>
			</div>
		</div></a>
		<div class="col-lg-3 col-md-6 col-xs-12">
			<a href="{{ url('/admin/tickets-completed-history') }}"><div class="box box-block bg-white tile tile-1 mb-2">
				<div class="t-icon right"><span class="bg-success"></span><i class="ti-bar-chart"></i></div>
				<div class="t-content">
					<h6 class="text-uppercase mb-1">@lang('admin.dashboard.resolved')</h6>
					<h1 class="mb-1">{{$completed_tickets}}</h1>
                                        <span class="text-muted font-180">Today &nbsp;&nbsp;<a href="/admin/tickets?from_date={{$today}}&to_date={{$today}}&status=Completed">{{$todayclosed_tickets}}</a></span><br/>
                                        <span class="text-muted font-180">Yesterday &nbsp;&nbsp;<a href="/admin/tickets?from_date={{$yesterday}}&to_date={{$yesterday}}&status=Completed">{{$yesterdayclosed_tickets}}</a></span><br/>
                                       </div>
			</div></a>
		</div>
		<div class="col-lg-3 col-md-6 col-xs-12">
			<a href="{{ url('/admin/tickets-ongoing-intervals') }}"><div class="box box-block bg-white tile tile-1 mb-2">
				<div class="t-icon right"><span class="bg-primary"></span><i class="ti-view-grid"></i></div>
				<div class="t-content">
					<h6 class="text-uppercase mb-1">@lang('admin.dashboard.assigned')</h6>
					<h1 class="mb-1">{{$ongoing_tickets}}</h1>
				</div>
			</div></a>
		</div>

                <div class="col-lg-3 col-md-6 col-xs-12">
			<a href="{{ url('/admin/tickets-ongoing-history') }}"><div class="box box-block bg-white tile tile-1 mb-2">
				<div class="t-icon right"><span class="bg-primary"></span><i class="ti-view-grid"></i></div>
				<div class="t-content">
					<h6 class="text-uppercase mb-1">@lang('admin.dashboard.ongoing')</h6>
                                        <h1 class="mb-1">{{$totalongoing_tickets}}</h1>
                                        <span class="text-muted font-180">Today &nbsp;&nbsp;<a href="/admin/tickets?from_date={{$today}}&to_date={{$today}}&status=OnGoing">{{$todayongoing_tickets}}</a></span><br/>
                                        <span class="text-muted font-180">Yesterday &nbsp;&nbsp;<a href="/admin/tickets?from_date={{$yesterday}}&to_date={{$yesterday}}&status=OnGoing">{{$yesterdayongoing_tickets}}</a></span><br/>

				</div>
			</div></a>
		</div>

		 <div class="col-lg-3 col-md-6 col-xs-12">
			<a href="{{ url('/admin/tickets?status=Onhold') }}"><div class="box box-block bg-white tile tile-1 mb-2">
				<div class="t-icon right"><span class="bg-warning"></span><i class="ti-archive"></i></div>
				<div class="t-content">
					<h6 class="text-uppercase mb-1">On hold Tickets</h6>
					<h1 class="mb-1">{{$onhold_tickets}}</h1>
                                        <span class="text-muted font-180">Today &nbsp;&nbsp;<a href="/admin/tickets?from_date={{$today}}&to_date={{$today}}&status=Onhold">{{$todayonhold_tickets}}</a></span><br/>
                                        <span class="text-muted font-180">Yesterday &nbsp;&nbsp;<a href="/admin/tickets?from_date={{$yesterday}}&to_date={{$yesterday}}&status=Onhold">{{$yesterdayonhold_tickets}}</a></span><br/>
                                        <span class="text-muted font-180">&nbsp;&nbsp;</span><br/>
                                        <span class="text-muted font-180">&nbsp;&nbsp;</span><br/>

				</div>
			</div></a>
		</div>
		<div class="col-lg-3 col-md-6 col-xs-12">
			<a href="{{ url('/admin/tickets?category=Power') }}"><div class="box box-block bg-white tile tile-1 mb-2">
				<div class="t-icon right"><span class="bg-success"></span><i class="ti-bar-chart"></i></div>
				<div class="t-content">
					<h6 class="text-uppercase mb-1">Power</h6>
					<h1 class="mb-1">{{$ups}}</h1>
					<span class="text-muted font-90">Not Started <a href="{{ url('/admin/tickets?category=Power&status=NotStarted') }}">{{$notstartedups}}</a></span><br/>
					<span class="text-muted font-90">Ongoing <a href="{{ url('/admin/tickets?category=Power&status=OnGoing') }}">{{$ongoingups}}</a></span><br/>
					<span class="text-muted font-90">Hold <a href="{{ url('/admin/tickets?category=Power&status=Onhold') }}">{{$holdups}}</a></span><br/>
                                        <span class="text-muted font-90">Completed <a href="{{ url('/admin/tickets?category=Power&status=Completed') }}">{{$completedups}}</a></span>
				</div>
			</div></a>
		</div>
		<div class="col-lg-3 col-md-6 col-xs-12">
			<a href="{{ url('/admin/tickets?category=Fiber') }}"><div class="box box-block bg-white tile tile-1 mb-2">
				<div class="t-icon right"><span class="bg-success"></span><i class="ti-user"></i></div>
				<div class="t-content">
					<h6 class="text-uppercase mb-1">@lang('admin.dashboard.fiber')</h6>
					<h1 class="mb-1">{{$fiber}}</h1>
					<span class="text-muted font-90">Not Started <a href="{{ url('/admin/tickets?category=Fiber&status=NotStarted') }}">{{$notstartedfiber}}</a></span><br/>
					<span class="text-muted font-90">Ongoing <a href="{{ url('/admin/tickets?category=Fiber&status=OnGoing') }}">{{$ongoingfiber}}</a></span><br/>
					<span class="text-muted font-90">Hold <a href="{{ url('/admin/tickets?category=Fiber&status=Onhold') }}">{{$holdfiber}}</a></span><br/>
                                        <span class="text-muted font-90">Completed <a href="{{ url('/admin/tickets?category=Fiber&status=Completed') }}">{{$completedfiber}}</a></span>
				</div>
			</div></a>
		</div>
		<div class="col-lg-3 col-md-6 col-xs-12">
			<a href="{{ url('/admin/tickets?category=Electronics') }}"><div class="box box-block bg-white tile tile-1 mb-2">
				<div class="t-icon right"><span class="bg-warning"></span><i class="ti-rocket"></i></div>
				<div class="t-content">
					<h6 class="text-uppercase mb-1">@lang('admin.dashboard.electronics')</h6>
					<h1 class="mb-1">{{$electronics}}</h1>
					<span class="text-muted font-90">Not Started <a href="{{ url('/admin/tickets?category=Electronics&status=NotStarted') }}">{{$notstartedelectronics}}</a></span><br/>
					<span class="text-muted font-90">Ongoing <a href="{{ url('/admin/tickets?category=Electronics&status=OnGoing') }}">{{$ongoingelectronics}}</a></span><br/>
					<span class="text-muted font-90">Hold <a href="{{ url('/admin/tickets?category=Electronics&status=Onhold') }}">{{$holdelectronics}}</a></span><br/>
                                        <span class="text-muted font-90">Completed <a href="{{ url('/admin/tickets?category=Electronics&status=Completed') }}">{{$completedelectronics}}</a></span>

				</div>
			</div></a>
		</div>
		
		<div class="col-lg-3 col-md-6 col-xs-12">
			<a href="{{ url('/admin/tickets?category=Pole Change') }}"><div class="box box-block bg-white tile tile-1 mb-2">
				<div class="t-icon right"><span class="bg-primary"></span><i class="ti-view-grid"></i></div>
				<div class="t-content">
					<h6 class="text-uppercase mb-1">Pole Change</h6>
					<h1 class="mb-1">{{$poles}}</h1>
					<span class="text-muted font-90">Not Started <a href="{{ url('/admin/tickets?category=Pole Change&status=NotStarted') }}">{{$notstartedpoles}}</a></span><br/>
					<span class="text-muted font-90">Ongoing <a href="{{ url('/admin/tickets?category=Pole Change&status=OnGoing') }}">{{$ongoingpoles}}</a></span><br/>
					<span class="text-muted font-90">Hold <a href="{{ url('/admin/tickets?category=Pole Change&status=Onhold') }}">{{$holdpoles}}</a></span><br/>
                                        <span class="text-muted font-90">Completed <a href="{{ url('/admin/tickets?category=Pole Change&status=Completed') }}">{{$completedpoles}}</a></span>

				</div>
			</div></a>
		</div>

               <div class="col-lg-3 col-md-6 col-xs-12">
			<a href="{{ url('/admin/tickets?category=Others') }}"><div class="box box-block bg-white tile tile-1 mb-2">
				<div class="t-icon right"><span class="bg-primary"></span><i class="ti-view-grid"></i></div>
				<div class="t-content">
					<h6 class="text-uppercase mb-1">Others</h6>
					<h1 class="mb-1">{{$others}}</h1>
					<span class="text-muted font-90">Not Started <a href="{{ url('/admin/tickets?category=Others&status=NotStarted') }}">{{$notstartedothers}}</a></span><br/>
					<span class="text-muted font-90">Ongoing <a href="{{ url('/admin/tickets?category=Others&status=OnGoing') }}">{{$ongoingothers}}</a></span><br/>
					<span class="text-muted font-90">Hold <a href="{{ url('/admin/tickets?category=Others&status=Onhold') }}">{{$holdothers}}</a></span><br/>
                                        <span class="text-muted font-90">Completed <a href="{{ url('/admin/tickets?category=Others&status=Completed') }}">{{$completedothers}}</a></span>

				</div>
			</div></a>
		</div>
	
		
	</div>
	<div class="row row-md">
		
	</div>

	<div class="row row-md mb-2">
		<div class="col-md-12">
			<div class="box bg-white">
					<div class="box-block clearfix">
						<h5 class="float-xs-left">Unique Teams Work</h5>
						<div class="float-xs-right">
 					     </div>
					<table class="table mb-md-0">
					   <tr>
                                             <th>No Of Teams</th>
                                             <th>Completed Teams</th>
                                             <th>Hold Teams Running</th>
                                             <th>Unique Teams Running</th>
                                             <th>Not Started Teams</th>
                                             <th>Today Not Started</th>
                                          </tr>
                                          <tr>
                                           <td><a href="/admin/totalteams">{{$teamcount}}</a></td>
                                           <td><a href="/admin/completedteams">{{$completedteams}}</a></td>
                                           <td><a href="/admin/holdteams">{{$holdteams}}</a></td>
                                           <td><a href="/admin/uniqueteams">{{$runningteams}}</a></td>
                                           <td><a href="/admin/notstartedteams">{{$notrunningteams}}</a></td>
                                           <td><a href="/admin/todaynotstartedteams">{{$notworkedteamscount }}</a></td>
                                           </tr>
					</table>
				</div>
			</div>

		
		
		</div>

	</div>
</div>
@endsection
