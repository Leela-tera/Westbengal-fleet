@extends('admin.layout.base')

@section('title', 'View Equipment ')

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
            @if(Setting::get('demo_mode') == 1)
        <div class="col-md-12" style="height:50px;color:red;">
                    ** Demo Mode : @lang('admin.demomode')
                </div>
                @endif
            <h4 class="mb-1">
                <!-- @lang('admin.provides.providers') -->
                @lang('admin.equipment.equipment')
                @if(Setting::get('demo_mode', 0) == 1)
                <span class="pull-right">(*personal information hidden in demo)</span>
                @endif
            </h4>
            <a href="{{ route('admin.equipment.create') }}" style="margin-left: 1em;" class="btn btn-primary pull-right btn-cstm"><i class="fa fa-plus"></i>@lang('admin.equipment.add_equipment')</a>
            <table class="table row-bordered dataTable nowrap display" id="table-5" style="width:100%">
                <thead>
                    <tr>
                        <th>@lang('admin.id')</th>
                        <th width="35">@lang('Date')</th>
                        <th>@lang('Material Name')</th>
                        <th>@lang('District Name')</th>
                        <th>@lang('Opening Stock')</th>
                        <th>@lang('Inward')</th>
                        <th>@lang('Issued Quantity')</th>
                        <th>@lang('MRN Quantity')</th>
                        <th>@lang('Closing Stock')</th>
                        <th>@lang('admin.action')</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>{{ $inventory->date }}</td>
                        <td class="font-weight-bold">{{ $inventory->service_type }}</td>
                        <td>{{ $inventory->districts_name }}</td>
                        <td>{{ $inventory->opening_stock }}</td>
                        <td>{{ $inventory->inward }}</td>
                        <td>{{ $inventory->issued_qty }}</td>
                        <td>{{ $inventory->mrn_qty }}</td>
                        <td>{{ $inventory->closing_stock }}</td>
                        <td>
                            <div class="btn-group" style="width:200px">
                                @if( Setting::get('demo_mode') == 0)
                                <a href="{{ route('admin.equipment.edit', $equipment->id) }}" class="btn btn-default"><i class="fa fa-pencil"></i> @lang('admin.edit')</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">

    $('#table-5').DataTable( {
        scrollX: true,
        searching: false,
        paging:false,
        info:false,
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
@endsection