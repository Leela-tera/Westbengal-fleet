@extends('admin.layout.base')

@section('title', 'GPs Downreports ')

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

.nav-cstm .nav-link-cstm:not(.active):hover{
        color: #333333 !important;
        border-bottom:3px solid #edf1f2;
        transition: none !important;
    }

    .nav-cstm .nav-link-cstm{
        font-weight: 600;
        color: #636f73 !important;
    }

    .nav-link-cstm.active{
        background-color: transparent !important;
        color: #2b3eb1 !important;
        border-bottom: 3px solid #2b3eb1;        
    }
    .filter-box{
        border-radius: 25px;
        height: 30px !important;
    }
    #table-5_filter label{
        display: none !important;
    }
    .pt-5 {
     padding-top:5px;  
     }
    .br-10{
        border-radius: 10px;
    }
    .dropdown-menu{
        left: -50px !important;
    }
   .btn-cstm1{
   background:red;
  text:#fff; 
   }


</style>
<div class="content-area py-1">
    <div class="container-fluid">
        <div class="box box-block bg-white">


              <form action="{{route('admin.reports')}}" method="GET">
            <ul class="nav nav-pills mb-2 pb-1 b-b">

                 <li class="nav-item mr-0-75">
                    <select class="form-control selectpicker filter-box" data-show-subtext="true" data-live-search="true" name="zone_id" id="searchzonelist">
                        <option value="">Zonal</option>
                        @foreach($zonals as $zone)
                        <option value="{{$zone->id}}" rel="{{$zone->id}}" @if(Request::get('zone_id')) @if(@Request::get('zone_id') == $zone->id) selected @endif @endif>{{$zone->Name}} </option> 
                        @endforeach 
                    </select>

                </li>

                 <li class="nav-item mr-0-75">
                    <select class="form-control selectpicker filter-box" data-show-subtext="true" data-live-search="true" name="district_id" id="searchblocklist">
                        <option value="">District</option>
                        @foreach($districts as $district)
                        <option value="{{$district->name}}" rel="{{$district->id}}" @if(Request::get('district_id')) @if(@Request::get('district_id') == $district->name) selected @endif @endif>{{$district->name}} </option> 
                        @endforeach 
                    </select>

                </li>
                  

                <li class="nav-item mr-0-75">
                    <select class="form-control selectpicker filter-box" data-show-subtext="true" data-live-search="true" name="block_id" id="getblock">
                        <option value="">Block</option>
                        @foreach($blocks as $district)
                        <option value="{{$district->name}}" @if(Request::get('block_id')) @if(@Request::get('block_id') == $district->name) selected @endif @endif>{{$district->name}} </option> 
                        @endforeach 
                    </select>
                </li>


                 
                  <li class="nav-item mr-0-75">
                    <input class="form-control filter-box filter" type="date" id="from_date" name="from_date" placeholder="From Date" value="{{ @Request::get('from_date') }}"  onclick="this.showPicker()">
                
                <li class="nav-item mr-0-75">
                    <input class="form-control filter-box filter" type="date" id="to_date" name="to_date" placeholder="To Date" value="{{ @Request::get('to_date') }}"  onclick="this.showPicker()">
                </li>

                <li class="nav-item mr-0-75 pull-right mt">
                    <button type="submit" class="form-control btn btn-primary btn-cstm" style="height:30px">Apply</button>
                </li>

                 <!-- Reset Filters Button -->
                  <li class="nav-item mr-0-75 pull-right mt">
                  <button type="button" id="resetFilters" class="form-control btn btn-primary btn-cstm" style="height:30px;">Reset</button>
               </li>

            </ul>
            </form>



            @if(Setting::get('demo_mode') == 1)
        <div class="col-md-12" style="height:50px;color:red;">
                    ** Demo Mode : @lang('admin.demomode')
                </div>
                @endif
            <h4 class="mb-1">
                @lang('admin.gp.gp')
                @if(Setting::get('demo_mode', 0) == 1)
                <span class="pull-right">(*personal information hidden in demo)</span>
                @endif
            </h4>
            
            <table class="table row-bordered dataTable nowrap display" id="table-5" style="width:100%">
                <thead>
                    <tr>
                        <th>@lang('admin.id')</th>
                        <th>@lang('GP Name')</th>
                        <th>@lang('District')</th>
                        <th>@lang('Block')</th>
                        <th>Zonal Incharge</th>
                        <th>@lang('LGD Code')</th>
                        <th>Down Hours</th>
                     </tr>
                </thead>
                <tbody>
                @foreach($downreport as $index => $gp)
                    <tr>
                        <td>{{ $index }}</td>
                        <td class="font-weight-bold">{{ $gp->gpname }}</td>
                        <td>{{ $gp->district }}</td>
                        <td>{{ $gp->mandal }}</td>
                        <td>{{ $gp->zone_name }}</td>
                        <td>{{ $gp->lgd_code }}</td>
                        <td>{{ $gp->total_gps_down_hours}}</td>
                        
                   </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">

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


    $('#table-5').DataTable( {
        scrollX: true,
        searching: true,
        paging:true,
        info:true,
        dom: 'Bfrtip',
        // buttons: [
        //     'copyHtml5',
        //     'excelHtml5',
        //     'csvHtml5',
        //     'pdfHtml5'
        // ]
        buttons: [
            {
                extend: 'copyHtml5',
                exportOptions: {
                    modifier: {
                      page: 'all'
                    }
                  }
            },
            {
                extend: 'excelHtml5',
                exportOptions: {
                    modifier: {
                      page: 'all'
                    }
                  }
            },
            {
                extend: 'csvHtml5',
                exportOptions: {
                    modifier: {
                      page: 'all'
                    }
                  }
            },
            {
                extend: 'pdfHtml5',
                exportOptions: {
                    modifier: {
                      page: 'all'
                    }
                  }
            }
        ]
    } );
    
    
</script>

<script>
document.getElementById('resetFilters').addEventListener('click', function() {
    // Reset all filters
    document.getElementById('searchzonelist').value = "";
    document.getElementById('searchblocklist').value = "";
    document.getElementById('getblock').value = "";
    document.getElementById('from_date').value = "";
    document.getElementById('to_date').value = "";

    // Reload the page without filters
    //window.location.href = "{{ route('admin.reports') }}";
});
</script>

@endsection