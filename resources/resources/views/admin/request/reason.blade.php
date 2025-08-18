@extends('admin.layout.base')

@section('title', 'Ticket History ')

@section('content')

    <div class="content-area py-1">
        <div class="container-fluid">
            
            <div class="box box-block bg-white">
                <h5 class="mb-1">Ticket History</h5>
                @if(count($requests) != 0)
                <table class="table table-striped table-bordered dataTable" id="table-4">
                    <thead>
                        <tr>
                            <th>@lang('admin.id')</th>
                            <th>@lang('admin.request.Request_Id')</th>
                            <th>Down Reason</th>
                            <th>Down Reason Detailed</th>
                            <th>@lang('admin.status')</th>
                            <th>@lang('admin.action')</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach(array_chunk($requests, 2) as $chunk)    
                    @foreach($chunk as $index => $request)
                        <tr>
                            <td>{{$index + 1}}</td>

                            <td>{{$request->ticketid}}</td>
                            
                            <td>
                                @if($request->downreason)
                                    {{$request->downreason?$request->downreason:'N/A'}} 
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>
                                @if($request->downreasonindetailed)
                                    {{$request->downreasonindetailed?$request->downreasonindetailed:'N/A'}} 
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>
                                {{$request->status ? $request->status : 'Not Assigned'}}
                            </td>

                            
                            <td>
                                <div class="input-group-btn">
                                  <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">Action
                                    <span class="caret"></span>
                                  </button>
                                  <ul class="dropdown-menu">
                                    <li>
                                        <a href="{{ route('admin.requests.show', $request->id) }}" class="btn btn-default"><i class="fa fa-search"></i> More Details</a>
                                    </li>
                                     <?php if($request->status == 'SEARCHING'){ ?>
                                     <li>
                                        <a href="{{ route('admin.dispatcher.assignform', $request->id) }}" class="btn btn-default"><i class="fa fa-arrows"></i> Assign</a>
                                    </li>
                                   <?php } ?>
                                  </ul>
                                </div>
                            </td>
                        </tr>
                      @endforeach
                     @endforeach 
                    </tbody>
                    <!--<tfoot>
                        <tr>
                            <th>@lang('admin.id')</th>
                            <th>@lang('admin.request.Request_Id')</th>
                            <th>@lang('admin.request.User_Name')</th>
                            <th>@lang('admin.request.Provider_Name')</th>
                            <th>@lang('admin.request.Scheduled_Date_Time')</th>
                            <th>@lang('admin.status')</th>
                            <th>@lang('admin.request.Payment_Mode')</th>
                            <th>@lang('admin.request.Payment_Status')</th>
                            <th>@lang('admin.action')</th>
                        </tr>
                    </tfoot>-->
                </table>
                @else
                    <h6 class="no-result">No results found</h6>
                @endif 
            </div>
            
        </div>
    </div>
@endsection