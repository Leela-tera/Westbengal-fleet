@extends('admin.layout.base')

@section('title', 'View Material Consumption ')

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
                @lang('admin.mt_consumption.mt_consumption')
                @if(Setting::get('demo_mode', 0) == 1)
                <span class="pull-right">(*personal information hidden in demo)</span>
                @endif
            </h4>
            <a href="{{ route('admin.material_consumption.create') }}" style="margin-left: 1em;" class="btn btn-primary pull-right btn-cstm"><i class="fa fa-plus"></i>@lang('admin.mt_consumption.add_mt_consumption')</a>
            <table class="table row-bordered dataTable nowrap display" id="table-5" style="width:100%">
                <thead>
                    <tr>
                        <th>@lang('admin.id')</th>
                        <th>@lang('Date')</th>
                        <th>@lang('Material Name')</th>
                        <th>@lang('UOM')</th>
                        <th>@lang('Materials Indent Note')</th>
                        <th>@lang('MIN NO')</th>
                        <th>@lang('District Name')</th>
                        <th>@lang('Block')</th>
                        <th>@lang('From GP')</th>
                        <th>@lang('To GP')</th>
                        <th>@lang('Link Name')</th>
                        <th>@lang('Drum Number')</th>
                        <th>@lang('Start Meter')</th>
                        <th>@lang('End Meter')</th>
                        <th>@lang('Consumed Quantity')</th>
                        <th>@lang('Status Link Up/Down')</th>
                        <th>@lang('admin.action')</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>{{ $material_consumption->date }}</td>
                        <td class="font-weight-bold">{{ $material_consumption->material_name }}</td>
                        <td>{{ $material_consumption->uom }}</td>
                        <td>{{ $material_consumption->material_indent_note }}</td>
                        <td>{{ $material_consumption->min_no }}</td>
                        <td>{{ $material_consumption->district_name }}</td>
                        <td>{{ $material_consumption->block_name }}</td>
                        <td>{{ $material_consumption->from_gp }}</td>
                        <td>{{ $material_consumption->to_gp }}</td>
                        <td>{{ $material_consumption->link_name }}</td>
                        <td>{{ $material_consumption->drum_number }}</td>
                        <td>{{ $material_consumption->start_meter }}</td>
                        <td>{{ $material_consumption->end_meter }}</td>
                        <td>{{ $material_consumption->consumed_qty }}</td>
                        <td>{{ $material_consumption->status_link_up_down }}</td>
                        <td>
                            <div class="btn-group" style="width:200px">
                                @if( Setting::get('demo_mode') == 0)
                                <a href="{{ route('admin.material_consumption.edit', $material_consumption->id) }}" class="btn btn-default"><i class="fa fa-pencil"></i> @lang('admin.edit')</a>
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