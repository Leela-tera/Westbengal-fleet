@extends('admin.layout.base')

@section('title', 'Completed Tickets History')

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
            
            <div class="box box-block bg-white">
                <h5 class="mb-1">Completed Tickets History</h5>
                   
                <table class="table row-bordered dataTable nowrap display" style="width:100%">
				  <thead>
						<tr>
						  <th scope="col">&nbsp</th>
                                                  <th scope="col">No of Tickets</th>
						  <th scope="col" colspan="2">Manually Created Tickets</th>
						  <th scope="col" colspan="2">Auto Tickets</th>
						</tr>
                                               <tr>
						  <th scope="col">&nbsp</th>
                                                  <th scope="col">&nbsp</th>
                                                  <th scope="col">Manually Closed</th>
						  <th scope="col">Auto Closed </th>
                                                  <th scope="col">Manually Closed</th>
						  <th scope="col">Auto Closed</th>
						</tr>

				  </thead>
				  <tbody>
						<tr>
						  <th scope="row">Tickets Completed</th>
						  <td><a href="{{ url('/admin/tickets?status=Completed') }}">{{$completed_tickets}}</a></td>
						  <td><a href="{{ url('/admin/tickets?status=Completed&default_autoclose=Manual&autoclose=Manual') }}">{{$manual_manual_tickets}}</a></td>
                                                  <td><a href="{{ url('/admin/tickets?status=Completed&default_autoclose=Manual&autoclose=Auto') }}">{{$manual_auto_tickets}}</a></td>
						  <td><a href="{{ url('/admin/tickets?status=Completed&default_autoclose=Auto&autoclose=Manual') }}">{{$auto_manual_tickets}}</a></td>
                                                  <td><a href="{{ url('/admin/tickets?status=Completed&default_autoclose=Auto&autoclose=Auto') }}">{{$auto_auto_tickets}}</a></td>
						
                                               </tr>
						<tr>
						  <th scope="row">Today</th>
						  <td><a href="/admin/tickets?from_date={{$today}}&to_date={{$today}}&status=Completed">{{$todayclosed_tickets}}</a></td>
						  <td><a href="/admin/tickets?from_date={{$today}}&to_date={{$today}}&status=Completed&default_autoclose=Manual&autoclose=Manual">{{$today_manual_manual_tickets}}</a></td>
                                                  <td><a href="/admin/tickets?from_date={{$today}}&to_date={{$today}}&status=Completed&default_autoclose=Manual&autoclose=Auto">{{$today_manual_auto_tickets}}</a></td>
						  <td><a href="/admin/tickets?from_date={{$today}}&to_date={{$today}}&status=Completed&default_autoclose=Auto&autoclose=Manual">{{$today_auto_manual_tickets}}</a></td>
                                                  <td><a href="/admin/tickets?from_date={{$today}}&to_date={{$today}}&status=Completed&default_autoclose=Auto&autoclose=Auto">{{$today_auto_auto_tickets}}</a></td>
						
 
                                                 </tr>
						<tr>
						  <th scope="row">Yesterday</th>
						  <td><a href="/admin/tickets?from_date={{$yesterday}}&to_date={{$yesterday}}&status=Completed">{{$yesterdayclosed_tickets}}</a></td>
						  <td><a href="/admin/tickets?from_date={{$yesterday}}&to_date={{$yesterday}}&status=Completed&default_autoclose=Manual&autoclose=Manual">{{$yesterday_manual_manual_tickets}}</a></td>
                                                  <td><a href="/admin/tickets?from_date={{$yesterday}}&to_date={{$yesterday}}&status=Completed&default_autoclose=Manual&autoclose=Auto">{{$yesterday_manual_auto_tickets}}</a></td>
						  <td><a href="/admin/tickets?from_date={{$yesterday}}&to_date={{$yesterday}}&status=Completed&default_autoclose=Auto&autoclose=Manual">{{$yesterday_auto_manual_tickets}}</a></td>
                                                  <td><a href="/admin/tickets?from_date={{$yesterday}}&to_date={{$yesterday}}&status=Completed&default_autoclose=Auto&autoclose=Auto">{{$yesterday_auto_auto_tickets}}</a></td>
                                                 </tr>
				  </tbody>
              </table>                
			  </div>
            
        </div>
    </div>
@endsection