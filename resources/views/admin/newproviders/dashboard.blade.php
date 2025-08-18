@extends('admin.layout.pbase')

@section('title', 'Dashboard ')

@section('styles')
	<!--<link rel="stylesheet" href="{{asset('main/vendor/jvectormap/jquery-jvectormap-2.0.3.css')}}">-->
@endsection

@section('content')
<style>
.box {border-radius: 20px;box-shadow: 0px 5px 10px 0px rgba(0, 0, 0, 0.5);}
.bg-chocolate{background-color:#d2691e;}
.bg-darkmagenta	{background-color:#8b008b;}
.bg-olivedrab	{background-color:#6b8e23;}
.bg-teal	{background-color:#008080;}
.bg-yellowgreen	{background-color:#9acd32;}
.bg-peru	{background-color:#cd853f;}
.filter-box{border-radius: 25px;height: 30px !important;}

</style>

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
		<a href="{{ url('/provider/tickets') }}"><div class="col-lg-3 col-md-6 col-xs-12">
			<div class="box box-block bg-white tile tile-1 mb-2">
				<div class="t-icon right"><span class="bg-danger"></span><i class="ti-archive"></i></div>
				<div class="t-content">
					<h6 class="text-uppercase mb-1">@lang('admin.dashboard.Tickets')</h6>
					<h1 class="mb-1">{{$master_tickets}}</h1>
				</div>
			</div>
		</div></a>
		<div class="col-lg-3 col-md-6 col-xs-12">
			<a href="{{ url('/provider/tickets?status=Completed') }}"><div class="box box-block bg-white tile tile-1 mb-2">
				<div class="t-icon right"><span class="bg-success"></span><i class="ti-thumb-up"></i></div>
				<div class="t-content">
					<h6 class="text-uppercase mb-1">@lang('admin.dashboard.resolved')</h6>
					<h1 class="mb-1">{{$completed_tickets}}</h1>
                                        <span class="text-muted font-180">Today &nbsp;&nbsp;<a href="/public/westbengal/public/provider/tickets?from_date={{$today}}&to_date={{$today}}&status=Completed">{{$todayclosed_tickets}}</a></span><br/>
                                        <span class="text-success font-180"><b>Yesterday</b> &nbsp;&nbsp;<a href="/public/westbengal/public/provider/tickets?from_date={{$yesterday}}&to_date={{$yesterday}}&status=Completed">{{$yesterdayclosed_tickets}}</a></span><br/>
                                       </div>
			</div></a>
		</div>
		<div class="col-lg-3 col-md-6 col-xs-12">
			<a href="{{ url('/provider/tickets?status=NotStarted') }}"><div class="box box-block bg-white tile tile-1 mb-2">
				<div class="t-icon right"><span class="bg-primary"></span><i class="ti-view-grid"></i></div>
				<div class="t-content">
					<h6 class="text-uppercase mb-1">@lang('admin.dashboard.assigned')</h6>
					<h1 class="mb-1">{{$ongoing_tickets}}</h1>
				</div>
			</div></a>
		</div>

                <div class="col-lg-3 col-md-6 col-xs-12">
			<a href="{{ url('/provider/tickets?status=OnGoing') }}"><div class="box box-block bg-white tile tile-1 mb-2">
				<div class="t-icon right"><span class="bg-primary"></span><i class="ti-view-grid"></i></div>
				<div class="t-content">
					<h6 class="text-uppercase mb-1">@lang('admin.dashboard.ongoing')</h6>
                                        <h1 class="mb-1">{{$totalongoing_tickets}}</h1>
                                        <span class="text-muted font-180">Today &nbsp;&nbsp;<a href="/public/westbengal/public/admin/tickets?from_date={{$today}}&to_date={{$today}}&status=OnGoing">{{$todayongoing_tickets}}</a></span><br/>
                                        <span class="text-success font-180"><b>Yesterday :</b> &nbsp;&nbsp;<a href="/public/westbengal/public/admin/tickets?from_date={{$yesterday}}&to_date={{$yesterday}}&status=OnGoing">{{$yesterdayongoing_tickets}}</a></span><br/>

				</div>
			</div></a>
		</div>

		 <div class="col-lg-3 col-md-6 col-xs-12">
			<a href="{{ url('/provider/tickets?status=Onhold') }}"><div class="box box-block bg-white tile tile-1 mb-2">
				<div class="t-icon right"><span class="bg-warning"></span><i class="ti-control-pause"></i></div>
				<div class="t-content">
					<h6 class="text-uppercase mb-1">On hold Tickets</h6>
					<h1 class="mb-1">{{$onhold_tickets}}</h1>
                                        <table>
                                       <tr>
                                        <td><span class="text-muted font-180">Today : &nbsp;&nbsp;<a href="/public/westbengal/public/provider/tickets?from_date={{$today}}&to_date={{$today}}&status=Onhold">{{$todayonhold_tickets}}</a></span></td>
                                        <td><span class="text-muted font-90">Power :&nbsp;&nbsp; <a href="{{ url('/provider/tickets?category=Power&status=Onhold') }}">{{$holdups}}</a></span></td>
                                          </tr>
                                       <tr> 
                                        <td><span class="text-success font-90"><b>Yesterday :</b> &nbsp;&nbsp;<a href="/public/westbengal/public/admin/tickets?from_date={{$yesterday}}&to_date={{$yesterday}}&status=Onhold">{{$yesterdayonhold_tickets}}</a></span></td>
                                        <td><span class="text-muted font-90">Fiber :&nbsp;&nbsp; <a href="{{ url('/provider/tickets?category=Fiber&status=Onhold') }}">{{$holdfiber}}</a></span></td>
                                        </tr>
                                        <tr>
                                        <td><span class="text-muted font-90">@lang('admin.dashboard.software_hardware'):&nbsp;&nbsp; <a href="{{ url('/provider/tickets?category=Software/Hardware&status=Onhold') }}">{{$holdelectronics}}</a>&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
                                        <td><span class="text-muted font-90">Solar :&nbsp;&nbsp; <a href="{{ url('/provider/tickets?category=Solar&status=Onhold') }}">{{$holdsolar}}</a></span></td>
                                       </tr>
                                         <tr>
                                          <td><span class="text-muted font-90">@lang('admin.dashboard.ccu_battery') :&nbsp;&nbsp; <a href="{{ url('/admin/provider?category=CCU/Battery&status=Onhold') }}">{{$holdccu}}</a></span></td>
                                          <td><span class="text-muted font-90">Olt :&nbsp;&nbsp; <a href="{{ url('/provider/tickets?category=OLT&status=Onhold') }}">{{$holdolt}}</a></span></td>
                                        </tr>
                                        <tr>
                                        <td><span class="text-muted font-90">Others :&nbsp;&nbsp; <a href="{{ url('/provider/tickets?category=Others&status=Onhold') }}">{{$holdothers}}</a></span></td>
                                        <td>
                                        </td>
                                        </tr>
                                        </table>
                                                                                

				</div>
			</div></a>
		</div>
				
	
	
	</div>
		</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            fetch('{{ url("/provider/update-location") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude
                })
            });
        });
    }
});
</script>

@endsection
