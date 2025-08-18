
    <div class="container-fluid">
        <div class="box box-block bg-white">
            @if(Setting::get('demo_mode') == 1)
        <div class="col-md-12" style="height:50px;color:red;">
                    ** Demo Mode : @lang('admin.demomode')
                </div>
                @endif

            <a href="{{ route('admin.tickets.create') }}" style="margin-left: 1em;" class="btn btn-primary pull-right b-a-radius-0-5"><i class="fa fa-plus"></i> Add New Ticket</a>
            <a href="{{ route('admin.import') }}" style="margin-left: 1em;" class="btn btn-success pull-right b-a-radius-0-5"><i class="fa fa-upload"></i> Upload CSV File</a>

            <h4 class="mb-2">Tickets History</h4>

            <ul class="nav nav-pills mb-1 b-b nav-cstm">
              <li class="nav-item mr-0-5">
                <a href="{{ route('admin.tickets1') }}" class="nav-link nav-link-cstm pb-1 {{ @Request::get('status') == ''? 'active' : ''}}">All</a>
              </li>
              @foreach ($ticket_status as $status)
                <li class=" nav-item mr-0-5">
                    <a href="{{ route('admin.tickets1',['status' => $status]) }}" class="nav-link nav-link-cstm pb-1 {{ $status == @Request::get('status') ? 'active' : '' }}">{{ $status }}</a>
                </li>
              @endforeach
            </ul>
            <form action="{{route('admin.tickets1', $query_params)}}" method="GET">
            <ul class="nav nav-pills mb-2 pb-1 b-b">
                <li class="nav-item mr-0-75">
                    <input type="text" class="form-control filter-box" id="searchInput" name="searchinfo" placeholder="Search..." onkeydown="return event.key != 'Enter';">
                </li>
                <li class="nav-item mr-0-75">
                    <select class="form-control selectpicker filter-box" data-show-subtext="true" data-live-search="true" name="district_id" id="searchblocklist">
                        <option value="">Please Select District</option>
                        @foreach($districts as $district)
                        <option value="{{$district->name}}" rel="{{$district->id}}" @if(Request::get('district_id')) @if(@Request::get('district_id') == $district->name) selected @endif @endif>{{$district->name}} </option> 
                        @endforeach 
                    </select>

                </li>
                  <li class="nav-item mr-0-75">
                    <select class="form-control selectpicker filter-box" data-show-subtext="true" data-live-search="true" name="zone_id" id="searchzonelist">
                        <option value="">Please Select Zonal</option>
                        @foreach($zonals as $zone)
                        <option value="{{$zone->id}}" rel="{{$zone->id}}" @if(Request::get('zone_id')) @if(@Request::get('zone_id') == $zone->id) selected @endif @endif>{{$zone->Name}} </option> 
                        @endforeach 
                    </select>

                </li>

                <li class="nav-item mr-0-75">
                    <select class="form-control selectpicker filter-box" data-show-subtext="true" data-live-search="true" name="block_id" id="getblock">
                        <option value="">Please Select Block</option>
                        @foreach($blocks as $district)
                        <option value="{{$district->name}}" @if(Request::get('block_id')) @if(@Request::get('block_id') == $district->name) selected @endif @endif>{{$district->name}} </option> 
                        @endforeach 
                    </select>
                </li>
                <li class="nav-item mr-0-75">
                    <input class="form-control filter-box filter" type="date" id="from_date" name="from_date" placeholder="From Date" value="{{ @Request::get('from_date') }}"  onclick="this.showPicker()">
                </li>
                <li class="nav-item mr-0-75 pull-right">
                    <button type="submit" class="form-control btn btn-primary btn-cstm" style="height:30px">Apply</button>
                </li>
            </ul>
            <input type="hidden" value="{{ @Request::get('status') }}" name="status">
            </form>

            @if(count($tickets) != 0)
            <table class="table row-bordered dataTable nowrap display" id="table-5" style="width:100%">
                <thead>
                    <tr>
                        <th>Ticket Id</th>
                        <th>Zonal Incharge</th>
                        <th>District Name</th>
                        <th>Block Name</th>
                        <th>GP Name</th>
                        <th>First Name</th>
                        <th>Last Name</th>     
                        <th>Mobile</th>
                        <th>LGD Code</th>
                        <th>Down Reason</th>
                        <th>Description</th>
                        <th>Ticket Down Date</th>
                        <th>Ticket Down Time</th>
                        <th>Source Address</th>
                        <th>Source Latitude</th>
                        <th>Source Longitude</th>
                        <th>Destination Address</th>
                        <th>Destination Latitude</th>
                        <th>Destination Longitude</th>
                        <th>Ticket Assigned Time</th>
                        <th>Ticket Started Time</th>
                        <th>Ticket Started Location</th>
                        <th>Ticket Reached Time</th>
                        <th>Ticket Reached Location</th>
                        <th>Ticket Closed Time</th>
                        <th>During Hours</th>
                       <?php
                        if (isset($_GET['status']) && $_GET['status'] == 'Completed') { ?>                        
                        <th>Ticket  Closed</th>
                        <?php } else { ?>
                        <th>Ticket Assigned</th>
                        <?php } 
                         ?>
                        <th>Status</th>
                        <th>Action</th>
                        
                    </tr>
                </thead>
                <tbody> 
                 @foreach($tickets as $index => $request)
             
                    <tr>
                        <td class="font-weight-bold">{{ $request->ticketid }}</td>
                        <td>{{ $request->zone_name}}</td>
                        <td>{{ $request->district }}</td>
                        <td>
                         {{ $request->mandal }}
                        </td>
                         <td>
                         {{ $request->gpname}}
                        </td>
                        <td>{{ $request->first_name}}</td>
                        <td>{{ $request->last_name}}</td>
                        <td>{{ $request->mobile}}</td>
                         <td>
                         {{ $request->lgd_code}}
                        </td>

                        <td>{{ $request->downreason }}</td>
                        
                        <td>
                         {{ $request->downreasonindetailed}}   
                            
                        </td>
                        <td>
                         {{ $request->downdate}}</td>
                        <td>{{ $request->downtime}}</td>
                        <td>{{ $request->s_address}}</td>
                        <td>{{ $request->s_latitude}}</td>
                        <td>{{ $request->s_longitude}}</td>
                        <td>{{ $request->d_address}}</td>
                        <td>{{ $request->d_latitude}}</td>
                        <td>{{ $request->d_longitude}}</td>
                        <td>{{ $request->assigned_at}}</td>
                        <td>{{ $request->started_at}}</td>
                        <td>{{ $request->started_location}}</td>
                        <td>{{ $request->reached_at}}</td>
                        <td>{{ $request->reached_location}}</td>
                        <td>{{ $request->finished_at}}</td>
                         <?php
 $downdate = $request->downdate;
 $downtime = $request->downtime;
 $downdatetime = date('Y-m-d H:i:s', strtotime("$downdate $downtime "));
 $todaydatetime = date('Y-m-d H:i:s');
 if(!empty($request->finished_at)){
 $seconds = strtotime($request->finished_at) - strtotime($downdatetime);
 } else {
 $seconds = strtotime($todaydatetime) - strtotime($downdatetime);
 }
 $hours = $seconds/3600;
?>

                        <td>{{ $hours}}</td>
                        <td>{{ $request->autoclose}}</td>

                        <?php if($request->status != ''){?>
                        <td>
                            @if($request->status == 'COMPLETED')
                                <span class="tag tag-success tag-brp"> {{ $request->status }} </span>
                            @elseif($request->status == 'CANCELLED')
                                <span class="tag tag-danger tag-brp"> {{ $request->status }} </span>
                            @elseif($request->status == 'SEARCHING')
                                <span class="tag tag-warning tag-brp"> {{ $request->status }} </span>
                            @elseif($request->status == 'SCHEDULED')
                                <span class="tag tag-primary tag-brp"> {{ $request->status }} </span>
                            @else 
                                <span class="tag tag-info tag-brp"> {{ $request->status }} </span>
                            @endif
                        </td>
                        <?php } else {?>
                          <td><span class="tag tag-info tag-brp">Not Assigned</span></td>
                         <?php } ?> 
                         <?php if($request->status != ''){?>
                           <td>
                                <div class="input-group-btn">
                                  <button type="button" class="btn btn-info b-a-radius-0-5 dropdown-toggle pull-left" data-toggle="dropdown">Action
                                    <span class="caret"></span>
                                  </button>
                                  <ul class="dropdown-menu">
                                    <li>
                                        <a href="{{ route('admin.requests.show', $request->request_id) }}" class="btn btn-default"><i class="fa fa-search"></i> More Details</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('admin.tickets.edit', $request->master_id) }}" class="btn btn-default"><i class="fa fa-pencil"></i> @lang('admin.edit')</a>
                                    </li>
                                     <?php if($request->status == 'SEARCHING'){ ?>
                                     <li>
                                        <a href="{{ route('admin.dispatcher.assignform', $request->request_id) }}" class="btn btn-default"><i class="fa fa-arrows"></i> Assign</a>
                                    </li>
                                   <?php } ?>
                                    <?php if($request->status != 'COMPLETED'){ ?>
                                    <li>
                                        <a href="{{ route('admin.dispatcher.completeform', $request->request_id) }}" class="btn btn-default"><i class="fa fa-arrows"></i> Request Close </a>
                                    </li>
                                     <?php } ?>

                                    <?php if($request->status == 'INCOMING'){ ?>
                                    <li>
                                        <a href="{{ route('admin.dispatcher.onholdform', $request->request_id) }}" class="btn btn-default"><i class="fa fa-arrows"></i> On Hold </a>
                                    </li>
                                     <?php } ?>


                                  </ul>
                                </div>
                            </td>
                           <?php } else{?>
                            <td>
                             <div class="input-group-btn">
                                  <button type="button" class="btn btn-info dropdown-toggle b-a-radius-0-5 pull-left" data-toggle="dropdown">Action
                                    <span class="caret"></span>
                                  </button>
                              </div>
                              </td>    
                           <?php } ?>   
                    </tr>
                @endforeach
                </tbody>
              </table>
            @else
            <h6 class="no-result">No results found</h6>
            @endif 
        </div>
         Showing {{$tickets->currentPage() != 1 ? $tickets->currentPage() * 10 - 9 : $tickets->currentPage()}} to {{$tickets->currentPage() * $tickets->perPage()}} of {{$tickets->total()}} entries
    </div>
      {{ $tickets->appends(['status' => @$status_get,'district_id'=>@$district_id_get,'zone_id'=>@$zone_id_get,'block_id'=>@$block_id_get,'from_date'=>@$from_date_get,'autoclose'=>@$autoclose_get,'default_autoclose'=>@$default_autoclose_get])->links()  }}
   