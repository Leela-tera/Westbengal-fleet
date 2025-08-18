@extends('admin.layout.base')

@section('title', 'Material Issue ')

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
                @lang('admin.mt_issue.mt_issue')
                @if(Setting::get('demo_mode', 0) == 1)
                <span class="pull-right">(*personal information hidden in demo)</span>
                @endif
            </h4>
            <a href="{{ route('admin.material_issue.create') }}" style="margin-left: 1em;" class="btn btn-primary pull-right btn-cstm"><i class="fa fa-plus"></i>@lang('admin.mt_issue.add_mt_issue')</a>
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
                        <th>@lang('Material Indent Qty')</th>
                        <th>@lang('Issued Quantity')</th>
                        <th>@lang('Drum No')</th>
                        <th>@lang('SI No Equipment')</th>
                        <th>@lang('Receiving Person Name')</th>
                        <th>@lang('admin.action')</th>
                    </tr>
                </thead>
                <tbody>
                @php($page = ($pagination->currentPage-1)*$pagination->perPage)
                @foreach($materials_issue as $index => $material_issue)
                @php($page++)
                    <tr>
                        <td>{{ $page }}</td>
                        <td>{{ $material_issue->date }}</td>
                        <td class="font-weight-bold">{{ $material_issue->material_name }}</td>
                        <td>{{ $material_issue->uom }}</td>
                        <td>{{ $material_issue->material_indent_note }}</td>
                        <td>{{ $material_issue->min_no }}</td>
                        <td>{{ $material_issue->district_name }}</td>
                        <td>{{ $material_issue->block_name }}</td>
                        <td>{{ $material_issue->material_indent_qty }}</td>
                        <td>{{ $material_issue->issued_qty }}</td>
                        <td>{{ $material_issue->drum_no }}</td>
                        <td>{{ $material_issue->si_no_equipment }}</td>
                        <td>{{ $material_issue->receiving_person_name }}</td>
                        <td>
                            <div class="btn-group" style="width:200px">
                                @if( Setting::get('demo_mode') == 0)
                                <form action="{{ route('admin.material_issue.destroy', $material_issue->id) }}" method="POST">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button class="btn btn-danger b-a-radius-0-5 pull-left mr-1" onclick="return confirm('Are you sure you want to delete this issued material?')"><i class="fa fa-trash"></i> @lang('admin.delete')</button>
                                </form>
                                <a href="{{ route('admin.material_issue.edit', $material_issue->id) }}" class="btn btn-default"><i class="fa fa-pencil"></i> @lang('admin.edit')</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            @include('common.pagination')
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