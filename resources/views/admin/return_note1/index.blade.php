@extends('admin.layout.base')

@section('title', 'Return Notes ')

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
                @lang('admin.return_note.return_note')
                @if(Setting::get('demo_mode', 0) == 1)
                <span class="pull-right">(*personal information hidden in demo)</span>
                @endif
            </h4>
            <a href="{{ route('admin.return_note.create') }}" style="margin-left: 1em;" class="btn btn-primary pull-right btn-cstm"><i class="fa fa-plus"></i>@lang('admin.return_note.add_return_note')</a>
            <table class="table row-bordered dataTable nowrap display" id="table-5" style="width:100%">
                <thead>
                    <tr>
                        <th>@lang('admin.id')</th>
                        <th>@lang('Material Name')</th>
                        <th>@lang('Supplier Name')</th>
                        <th>@lang('Received Location')</th>
                        <th>@lang('Received Quantity')</th>
                        <th>@lang('Issued Date')</th>
                        <th>@lang('Received Date')</th>
                        <th>@lang('admin.action')</th>
                    </tr>
                </thead>
                <tbody>
                @php($page = ($pagination->currentPage-1)*$pagination->perPage)
                @foreach($return_notes as $index => $return_note)
                @php($page++)
                    <tr>
                        <td>{{ $page }}</td>
                        <td class="font-weight-bold">{{ $return_note->material_name }}</td>
                        <td>{{ $return_note->supplier_name }}</td>
                        <td>{{ $return_note->received_loc }}</td>
                        <td>{{ $return_note->received_qty }}</td>
                        <td>{{ $return_note->issue_note_date }}</td>
                        <td>{{ $return_note->received_date }}</td>
                        <td>
                            <div class="btn-group" style="width:200px">
                                @if( Setting::get('demo_mode') == 0)
                                <form action="{{ route('admin.return_note.destroy', $return_note->id) }}" method="POST">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button class="btn btn-danger b-a-radius-0-5 pull-left mr-1" onclick="return confirm('Are you sure you want to delete this Return Note?')"><i class="fa fa-trash"></i> @lang('admin.delete')</button>
                                </form>
                                <a href="{{ route('admin.return_note.edit', $return_note->id) }}" class="btn btn-default"><i class="fa fa-pencil"></i> @lang('admin.edit')</a>
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