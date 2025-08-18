@extends('admin.layout.base')

@section('title', 'Tickets')

@section('content')



<div class="content-area py-1">
    <div class="container-fluid">
        <div class="box box-block bg-white"> 
        <form action="{{route('admin.tickets')}}" method="GET">
            <div class="row">
                <div class="col-xs-3">
                   <select class="form-control selectpicker" data-show-subtext="true" data-live-search="true" name="district_id" id="searchblocklist">
                   	<option value="">Please Select District</option>
                    @foreach($districts as $district)
                    <option value="{{$district->name}}" rel="{{$district->id}}" @if(Request::get('district_id')) @if(@Request::get('district_id') == $district->name) selected @endif @endif>{{$district->name}} </option> 
                   @endforeach 
                  </select>
                </div>

                <div class="col-xs-3">
                   <select class="form-control selectpicker" data-show-subtext="true" data-live-search="true" name="block_id" id="getblock">
                   	<option value="">Please Select Block</option>
                    @foreach($blocks as $district)
                    <option value="{{$district->name}}" @if(Request::get('block_id')) @if(@Request::get('block_id') == $district->name) selected @endif @endif>{{$district->name}} </option> 
                   @endforeach 
                  </select>
                </div>


                <div class="form-group row col-md-3">
                            <label for="name" class="col-xs-4 col-form-label">Ticket Id</label>
                            <div class="col-xs-8">
                                <input class="form-control" type="text" name="ticket_id" placeholder="Enter Ticket Id">
                            </div>
                </div>
                                            
                <div class="col-xs-2">
                    <button type="submit" class="form-control btn btn-primary">Fetch</button>
                </div>  
            </div>
        </form>
        </div> 

        <div class="box box-block bg-white">
            @if(Setting::get('demo_mode') == 1)
        <div class="col-md-12" style="height:50px;color:red;">
                    ** Demo Mode : @lang('admin.demomode')
                </div>
                @endif
            <h5 class="mb-1">Tickets History</h5>
            <a href="{{ route('admin.import') }}" style="margin-left: 1em;" class="btn btn-success pull-right"><i class="fa fa-upload"></i> Upload CSV File</a>
            <a href="{{ route('admin.tickets.create') }}" style="margin-left: 1em;" class="btn btn-primary pull-right"><i class="fa fa-plus"></i>Add New Ticket</a>
                        @if(count($tickets) != 0)
            <table class="table table-striped table-bordered dataTable" id="table-4">
                <thead>
                    <tr>
                        <th>Ticket Id</th>
                        <th>District Name</th>
                        <th>Mandal Name</th>
                        <th>GP Name</th>
                        <th>Category</th>
                        <th>Down Reason</th>
                        <th>Description</th>
                        <th>Down Time</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody> 
                @php($page=($pagination->currentPage-1)*$pagination->perPage) 
                @foreach($tickets as $index => $request)
                @php($page++)
                    <tr>
                        <td>{{ $request->ticketid }}</td>
                        <td>{{ $request->district }}</td>
                        <td>
                         {{ $request->mandal }}
                        </td>
                         <td>
                         {{ $request->gpname}}
                        </td>

                        <td>
                           {{ $request->subsategory }}  
                        </td>
                        <td>{{ $request->downreason }}</td>
                        
                        <td>
                         {{ $request->downreasonindetailed}}   
                            
                        </td>
                        <td>
                         {{ $request->downdate}}   {{ $request->downtime}}
                        </td>

                        <?php if($request->status != ''){?>
                        <td>
                         {{ $request->status}}   
                            
                        </td>
                        <?php } else {?>
                          <td>Not Assigned</td>
                         <?php } ?> 
                         <?php if($request->status != ''){?>
                           <td>
                                <div class="input-group-btn">
                                  <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">Action
                                    <span class="caret"></span>
                                  </button>
                                  <ul class="dropdown-menu">
                                    <li>
                                        <a href="{{ route('admin.requests.show', $request->request_id) }}" class="btn btn-default"><i class="fa fa-search"></i> More Details</a>
                                    </li>
                                     <?php if($request->status != 'COMPLETED'){ ?>
                                     <li>
                                        <a href="{{ route('admin.dispatcher.assignform', $request->request_id) }}" class="btn btn-default"><i class="fa fa-arrows"></i> Assign</a>
                                    </li>
                                   <?php } ?>
                                    <?php if($request->status != 'COMPLETED'){ ?>
                                    <li>
                                        <a href="{{ route('admin.dispatcher.completeform', $request->request_id) }}" class="btn btn-default"><i class="fa fa-arrows"></i> Request Close </a>
                                    </li>
                                     <?php } ?>
                                    <li>
                                        <a href="{{ route('admin.tickets.edit', $request->request_id) }}" class="btn btn-default"><i class="fa fa-pencil"></i> @lang('admin.edit')</a>
                                    </li>

                                    <li>
                                        <a href="{{ route('admin.deleteticket', $request->ticketid ) }}" class="btn btn-default"><i class="fa fa-trash"></i> Delete </a>
                                    </li>


                                  </ul>
                                </div>
                            </td>
                           <?php } else{?>
                            <td>
                             <div class="input-group-btn">
                                  <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">Action
                                    <span class="caret"></span>
                                  </button>
                              </div>
                              </td>    
                           <?php } ?>   
                    </tr>
                @endforeach
                </tbody>
              </table>
             @include('common.pagination')
            @else
            <h6 class="no-result">No results found</h6>
            @endif 
        </div>
    </div>
   </div>
@endsection
@section('scripts')
 <script type="text/javascript">
$('.confirmation').on('click', function () {
        return confirm('Are you sure?');
    });
$('#searchblocklist').change(function(){
        var nid = $(this).find('option:selected').attr('rel');
        if(nid){
        $.ajax({
           type:"get",
            url: '/admin/getSearchblocklist/'+ nid,
            success:function(res)
           {       
                if(res)
                {
                    $("#getblock").empty();
                    $("#getblock").append('<option value="">Select block</option>');
                    $.each(res,function(key,value){
                        $("#getblock").append('<option value="'+value+'">'+value+'</option>');
                    });
                }
           }

        });
        }
});
jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
   if ( this.context.length ) {

     var string = window.location.search;
            if(string == ''){
                string = '?page=all';                         
            }

     var jsonResult = $.ajax({
       url: "{{url('admin/tickets')}}"+string,
       data: {},
       success: function (result) {                       
         p = new Array();
		 console.log(p);
         var current = 1;
         $.each(result.data, function (i, d)
         {
           var item = [
           current,
		   d.ticketid,
           d.zone_name,
           d.district,
           d.mandal,
           d.gpname,
           d.first_name,
           d.last_name,
           d.mobile,
           d.subsategory,
           d.downreason,
           d.downreasonindetailed,
           d.downdate,
		   d.downtime,
		   d.s_address,
		   d.s_latitude,
		   d.s_longitude,
		   d.d_address,
		   d.d_latitude,
		   d.d_longitude,
		   d.assigned_at,
		   d.started_at,
		   d.started_location,
		   d.reached_at,
		   d.reached_location,
		   d.finished_at,
                   d.autoclose,
           d.status
           ];
           p.push(item);
           current++;
         });
       },
       async: false
     });
     var head=new Array();
     head.push(
       "ID",
       "Ticket ID",
       "Zonal Incharge",
       "District Name",
       "Block Name",
       "GP Name",
       "First Name",
       "Last Name",
       "Mobile",
       "Category",
       "Down Reason",
       "Description",
       "Ticket Down Date",
       "Ticket Down Time",
	   "Source Address",
	   "Source Latitude",
	   "Source Longitude",
	   "Destination Address",
	   "Destination Latitude",
	   "Destination Longitude",
	   "Ticket Assigned Time",
	   "Ticket Started Time",
	   "Ticket Started Location",
	   "Ticket Reached Time",
	   "Ticket Reached Location",
	   "Ticket Closed Time",
           "Ticket Assigned",
       "Status"
       );            
     return {body: p, header: head};
   }
 } );
</script>
@endsection
