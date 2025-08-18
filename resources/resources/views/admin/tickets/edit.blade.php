@extends('admin.layout.base')

@section('title', 'Update Ticket Details ')

@section('content')

<div class="content-area py-1">
    <div class="container-fluid">
        <div class="box box-block bg-white">
            <a href="{{ route('admin.tickets') }}" class="btn btn-default pull-right"><i class="fa fa-angle-left"></i> @lang('admin.back')</a>

            <h5 style="margin-bottom: 2em;">Update Ticket Details</h5>

            <form class="form-horizontal" action="{{route('admin.tickets.update', $ticket->id )}}" method="POST" enctype="multipart/form-data" role="form">
                {{csrf_field()}}
                <input type="hidden" name="_method" value="PATCH">
                <div class="form-group row">
                    <label for="ticketid" class="col-xs-2 col-form-label">@lang('admin.request.Ticket_ID')</label>
                    <div class="col-xs-10">
                        <input class="form-control" type="text" value="{{ $ticket->ticketid }}" name="ticketid" required id="ticketid" placeholder="Ticket ID" disabled>
                    </div>
                </div>
               
                <input class="form-control" type="hidden" value="{{ $ticket->ticketid }}" name="ticket_id">
                <input class="form-control" type="hidden" value="{{ $ticket->downreason}}" name="downreason">



                @if(count($districts) > 0)
                <div class="form-group row">
                    <label for="district" class="col-xs-2 col-form-label">Districts</label>
                    <div class="col-xs-10">
                        <select class="form-control" name="district" id="district">
                            <option value="">Please Select District</option>
                            @foreach($districts as $dist)
                            <option value="{{$dist->name}}" {{ $dist->name== ucfirst(strtolower($ticket->district)) ? 'selected' : '' }}>{{$dist->name}}</option>
                            @endforeach                                
                        </select>
                    </div>
                </div>
                @endif

                @if(count($blocks) > 0)
                <div class="form-group row">
                    <label for="mandal" class="col-xs-2 col-form-label">Mandal</label>
                    <div class="col-xs-10">
                        <select class="form-control" name="mandal" id="mandal">
                            <option value="">Please Select Mandal</option>
                            @foreach($blocks as $block)
                            <option value="{{$block->name}}" {{ $block->name== ucfirst(strtolower($ticket->mandal )) ? 'selected' : '' }}>{{$block->name}}</option>
                            @endforeach
                            
                        </select>
                    </div>
                </div>
                @endif

                <div class="form-group row">
                    <label for="gpname" class="col-xs-2 col-form-label">GP Name</label>
                    <div class="col-xs-10">
                        <input class="form-control" type="text" value="{{ $ticket->gpname }}" name="gpname" required id="gpname" placeholder="GP Name">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="lat" class="col-xs-2 col-form-label">Latitude</label>
                    <div class="col-xs-10">
                        <input class="form-control" type="text" required name="lat" value="{{ $ticket->lat }}" id="lat" placeholder="Latitude">
                    </div>
                </div>
                
                <div class="form-group row">
                    <label for="log" class="col-xs-2 col-form-label">Longitude</label>
                    <div class="col-xs-10">
                        <input class="form-control" type="text" required name="log" value="{{ $ticket->log }}" id="log" placeholder="Longitude">
                    </div>
                </div>

                <div class="form-group row">                    
                    <div class="col-xs-5">
                        <label for="downdate" class="col-form-label">Down Date</label>
                        <input class="form-control" type="date" name="downdate" value="{{ $ticket->downdate }}" id="downdate" placeholder="Down Date">
                    </div>                  
                    <div class="col-xs-5">
                        <label for="downtime" class="col-form-label">Down Time</label>
                        <input class="form-control" type="time" value="{{ substr($ticket->downtime, 0, 8) }}" name="downtime" required id="downtime" placeholder="Down Time" step="2">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="district" class="col-xs-2 col-form-label">Down Reason</label>
                    <div class="col-xs-10">
                        <select class="form-control" name="downreason2" id="downreason2">
                            <option value="">Please Select reason</option>
                            @foreach($services as $service)
                            <option value="{{$service->name}}">{{$service->name}}</option>
                            @endforeach                                
                        </select>
                    </div>
                </div>


                <div class="form-group row">
                    <label for="downreasonindetailed" class="col-xs-2 col-form-label">Down Reason In Detailed</label>
                    <div class="col-xs-10">
                        <textarea class="form-control" type="textarea" name="downreasonindetailed" id="downreasonindetailed" placeholder="Down Reason In Detailed">{{ $ticket->downreasonindetailed }}</textarea>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="zipcode" class="col-xs-2 col-form-label"></label>
                    <div class="col-xs-10">
                        <button type="submit" class="btn btn-primary">Update Details</button>
                        <a href="{{route('admin.tickets')}}" class="btn btn-default">@lang('admin.cancel')</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
@section('scripts')
<script>
$("select[id='district_id']").change(function(){
  var district_id = $(this).val();
  $.get('{{url("admin/ajax-blocks")}}/'+district_id,function(data) {
    $("#block_id").empty().append(data);      
  });
});
</script>
@endsection

