@extends('admin.layout.base')

@section('title', 'Parts ')

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
                @lang('admin.part.part')
                @if(Setting::get('demo_mode', 0) == 1)
                <span class="pull-right">(*personal information hidden in demo)</span>
                @endif
            </h4>
            <a href="{{ route('admin.parts.create') }}" style="margin-left: 1em;" class="btn btn-primary pull-right btn-cstm"><i class="fa fa-plus"></i>@lang('admin.part.add_part')</a>
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
                        <th>@lang('Issued Quantity')</th>
                        <th>@lang('Consumed Quantity')</th>
                        <th>@lang('Balance At Location')</th>
                        <th>@lang('admin.action')</th>
                    </tr>
                </thead>
                <tbody>
                @php($page = ($pagination->currentPage-1)*$pagination->perPage)
                @foreach($parts as $index => $part)
                @php($page++)
                    <tr>
                        <td>{{ $page }}</td>
                        <td>{{ $part->date }}</td>
                        <td class="font-weight-bold">{{ $part->material_name }}</td>
                        <td>{{ $part->uom }}</td>
                        <td>{{ $part->material_indent_note }}</td>
                        <td>{{ $part->min_no }}</td>
                        <td>{{ $part->district_name }}</td>
                        <td>{{ $part->block_name }}</td>
                        <td>{{ $part->from_gp }}</td>
                        <td>{{ $part->to_gp }}</td>
                        <td>{{ $part->issued_qty }}</td>
                        <td>{{ $part->consumed_qty }}</td>
                        <td>{{ $part->balance_at_location }}</td>
                        <td>
                            <div class="btn-group" style="width:200px">
                                @if( Setting::get('demo_mode') == 0)
                                <form action="{{ route('admin.parts.destroy', $part->id) }}" method="POST">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button class="btn btn-danger b-a-radius-0-5 pull-left mr-1" onclick="return confirm('Are you sure you want to delete this part?')"><i class="fa fa-trash"></i> @lang('admin.delete')</button>
                                </form>
                                <a href="{{ route('admin.parts.edit', $part->id) }}" class="btn btn-default"><i class="fa fa-pencil"></i> @lang('admin.edit')</a>
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