@extends('admin.layout.base')

@section('title', 'Providers ')

@section('content')
<style type="text/css">
    table.dataTable thead th {
        background-color: #d9d9d9f5 !important;
        border-bottom: none !important;
    }
    .buttons-html5{
        border-radius: 10px;
/*        margin-right: 6px;*/
    }
    table.display tbody tr:hover td{
        background-color: #f1eeeef5 !important;
    }
    .dataTables_scrollBody thead {
        visibility: hidden;
    }
    select.select-box:not([size]):not([multiple]), input.select-box{
        height: 35px;
    }
</style>
<div class="content-area py-1">
    <div class="container-fluid">
        <div class="box box-block bg-white"> 
        <form action="{{route('admin.searchproviders')}}" method="GET">
            {{csrf_field()}}
            <div class="row">
                   
                <div class="form-group row col-md-8">
                            <label for="email" class="col-xs-6 col-form-label">Search By Name</label>
                            <label for="type" class="col-xs-6 col-form-label">Designation Type</label>
                            <div class="col-xs-6">
                                <input class="form-control select-box" type="text" id="search_name" name="search_name" placeholder="Search with first Name or Lastname" value="{{ request('search_name') }}">
                            </div>
                            
                            <div class="col-xs-6">
                                <select class="form-control select-box" name="type" id="type">
                                    <option value="">Designation</option>
                                     <option value="1" {{ request('type') == '1' ? 'selected' : '' }}>OFC</option>
    <option value="2" {{ request('type') == '2' ? 'selected' : '' }}>FRT</option>
    <option value="5" {{ request('type') == '5' ? 'selected' : '' }}>Patroller</option>
    <option value="3" {{ request('type') == '3' ? 'selected' : '' }}>Zonal incharge</option>
    <option value="4" {{ request('type') == '4' ? 'selected' : '' }}>District incharge</option>
                        </select>
                    </div>
                  </div>
            
                <div class="col-xs-2 mt-2 pt-1">
                    <button type="submit" class="form-control btn btn-primary btn-cstm" onclick="return validate_reqst()">Fetch</button>
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
            <h4 class="mb-1">
                <!-- @lang('admin.provides.providers') -->
                @lang('admin.contacts.contact')
                @if(Setting::get('demo_mode', 0) == 1)
                <span class="pull-right">(*personal information hidden in demo)</span>
                @endif
            </h4>
           @if(auth()->user()->role != 'super_admin')
            <a href="{{ route('admin.provider.create') }}" style="margin-left: 1em;" class="btn btn-primary pull-right btn-cstm"><i class="fa fa-plus"></i>@lang('admin.contacts.add_contact')</a>
           @endif 
           <table class="table row-bordered dataTable nowrap display" id="table-5" style="width:100%">
                <thead>
                    <tr>
                        <th>@lang('admin.id')</th>
                        <th>@lang('admin.provides.full_name')</th>
                        <th>Role</th>
                        <th>@lang('admin.email')</th>
                        <th>@lang('admin.mobile')</th>
                        <th>Version</th>
                        <th>@lang('admin.provides.total_requests')</th>
                        <th>@lang('admin.provides.accepted_requests')</th>
                        <th>@lang('admin.provides.cancelled_requests')</th> 
                        <th>@lang('admin.provides.service_type')</th>
                        <th>@lang('admin.provides.online')</th>
                        @if(auth()->user()->role != 'super_admin')
                        <th>@lang('admin.action')</th>
                       @endif
                    </tr>
                </thead>
                <tbody>
                @php($page =0)
                @foreach($providers as $index => $provider)
                @php($page++)
                    <tr>
                        <td>{{ $page }}</td>
                        <td class="font-weight-bold">{{ $provider->first_name }} {{ $provider->last_name }}</td>
                         @if($provider->type == 2)
                        <td>FRT</td>
                        @elseif($provider->type == 5)
                        <td>Patroller</td>
                        @else
                        <td></td>
                        @endif

                        @if(Setting::get('demo_mode', 0) == 1)
                        <td>{{ substr($provider->email, 0, 3).'****'.substr($provider->email, strpos($provider->email, "@")) }}</td>
                        @else
                        <td>{{ $provider->email }}</td>
                        @endif
                        @if(Setting::get('demo_mode', 0) == 1)
                        <td>+919876543210</td>
                        @else
                        <td>{{ $provider->mobile }}</td>
                        @endif
                        <td>{{ $provider->version}}</td>
                        <td>{{ $provider->total_requests() }}</td>
                        <td>{{ $provider->accepted_requests() }}</td>
                        <td>{{ $provider->total_requests() - $provider->accepted_requests() }}</td> 
                        <td>
                            {{-- @if($provider->active_documents() == $total_documents && $provider->service != null) --}}
                            @if($provider->service != null)
                                 <a class="btn btn-success btn-block btn-rounded" href="{{route('admin.provider.document.index', $provider->id )}}">All Set!</a>
                            @else                               
                                <a class="btn btn-danger btn-block btn-rounded" href="{{route('admin.provider.document.index', $provider->id )}}">Attention! Service{{--<span class="btn-label">{{ $provider->pending_documents() }}</span>--}}</a>
                            @endif
                        </td>
                        <td>
                            @if($provider->service)
                                @if($provider->service->status == 'active')
                                    <label class="btn btn-block btn-primary btn-rounded">Yes</label>
                                @else
                                    <label class="btn btn-block btn-warning btn-rounded">No</label>
                                @endif
                            @else
                                <label class="btn btn-block btn-danger btn-rounded">N/A</label>
                            @endif
                        </td>
                            <td>
                            <div class="btn-group" style="width:200px">
                                 <?php if(Auth::guard('admin')->user()->id == 1) {?>
                                @if($provider->status == 'approved')
                                <a class="btn btn-danger  b-a-radius-0-5 pull-left mr-1" href="{{ route('admin.provider.disapprove', $provider->id ) }}">@lang('Disable')</a>
                                @else
                                <a class="btn btn-success  b-a-radius-0-5 pull-left mr-1" href="{{ route('admin.provider.approve', $provider->id ) }}">@lang('Enable')</a>
                                @endif
                                <?php } ?>
                                <button type="button" 
                                    class="btn btn-info  dropdown-toggle b-a-radius-0-5 pull-left"
                                    data-toggle="dropdown">@lang('admin.action')
                                    <span class="caret"></span>
                                </button>
                                  <ul class="dropdown-menu">
                                      <?php if(Auth::guard('admin')->user()->id == 1) {?>
                                    <li>
                                        <a href="{{ route('admin.provider.request', $provider->id) }}" class="btn btn-default"><i class="fa fa-search"></i> @lang('admin.History')</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('admin.provider.statement', $provider->id) }}" class="btn btn-default"><i class="fa fa-account"></i> @lang('admin.Statements')</a>
                                    </li>
                                    <?php } ?>
                                    @if( Setting::get('demo_mode') == 0)
                                    <li>
                                        <a href="{{ route('admin.provider.edit', $provider->id) }}" class="btn btn-default"><i class="fa fa-pencil"></i> @lang('admin.edit')</a>
                                    </li>
                                    @endif
                                    @if(auth()->user()->role != 'super_admin')
                                     <?php if(Auth::guard('admin')->user()->id == 1) {?>
                                    <li>
                                        <form action="{{ route('admin.provider.destroy', $provider->id) }}" method="POST">
                                            {{ csrf_field() }}
                                            <input type="hidden" name="_method" value="DELETE">
                                            @if( Setting::get('demo_mode') == 0)
                                            <button class="btn btn-default look-a-like" onclick="return confirm('Are you sure?')"><i class="fa fa-trash"></i>@lang('admin.delete')</button>
                                            @endif
                                        </form>
                                    </li>
                                  <?php } ?>
                                  @endif
                                </ul>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
                <!--<tfoot>
                    <tr>
                        <th>@lang('admin.id')</th>
                        <th>@lang('admin.provides.full_name')</th>
                        <th>@lang('admin.email')</th>
                        <th>@lang('admin.mobile')</th>
                        <th>@lang('admin.provides.total_requests')</th>
                        <th>@lang('admin.provides.accepted_requests')</th>
                        <th>@lang('admin.provides.cancelled_requests')</th> 
                        <th>@lang('admin.provides.service_type')</th>
                        <th>@lang('admin.provides.online')</th>
                        <th>@lang('admin.action')</th>
                    </tr>
                </tfoot>-->
            </table>
              <div class="row">
    <div class="col-md-6 page_info">
        Showing {{($pagination->currentPage-1)*$pagination->perPage+1}} to {{$pagination->currentPage*$pagination->perPage}}
        of  {{$pagination->total}} entries                    
    </div>
    <div class="col-md-6 pagination_cover">
        {{ $providers->appends(request()->query())->links() }}
    </div>
</div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
    /*jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
        if ( this.context.length ) {
            var jsonResult = $.ajax({
                url: "{{url('admin/provider')}}?page=all",
                data: {},
                success: function (result) {                       
                    p = new Array();
                    $.each(result.data, function (i, d)
                    {
                        var item = [d.id,d.first_name, d.last_name, d.email,d.mobile,d.rating, d.wallet_balance];
                        p.push(item);
                    });
                },
                async: false
            });
            var head=new Array();
            head.push("ID", "First Name", "Last Name", "Email", "Mobile", "Rating", "Wallet");
            return {body: p, header: head};
        }
    } );*/

    $('#table-5').DataTable( {
        scrollX: true,
        searching: true,
        paging:false,
            info:false,
            dom: 'Bfrtip',
            buttons: [
                'copyHtml5',
                'excelHtml5',
                'csvHtml5',
                'pdfHtml5'
            ]
    } );
    
    function validate_reqst(){
        var contact_type = document.getElementById('type');
        var type = contact_type.options[contact_type.selectedIndex].value;
        var search_name = document.getElementById('search_name').value;

        if(!type && !search_name){
            document.getElementById('type').style.border = "1px solid red";
            document.getElementById('search_name').style.border = "1px solid red";
            return false;    // in failure case
        }  
        return true;
    }
</script>
@endsection