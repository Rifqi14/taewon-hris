@extends('admin.layouts.app')

@section('title', __('grade.grade'))
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
@endsection

@push('breadcrump')
<li class="breadcrumb-item active">{{ __('grade.grade') }}</li>
@endpush

@section('content')
<div class="wrapper wrapper-content">
    <div class="row">
        <div class="col-lg-12">
            <div class="card ">
                <div class="card-header">
                    <h3 class="card-title">{{ __('grade.grade') }} List</h3>
                    <!-- tools box -->
                    <div class="pull-right card-tools">
                        <a href="{{route('grade.create')}}" class="btn btn-{{ config('configs.app_theme') }} btn-sm text-white" data-toggle="tooltip" title="{{ __('general.crt') }}">
                            <i class="fa fa-plus"></i>
                        </a>
                        <a href="#" onclick="filter()" class="btn btn-default btn-sm" data-toggle="tooltip" title="{{ __('general.srch') }}">
                            <i class="fa fa-search"></i>
                        </a>
                    </div>
                    <!-- /. tools -->
                </div>
                <div class="card-body">
                    <table class="table table-striped table-bordered datatable" style="width:100%">
                        <thead>
                            <tr>
                                <th width="10">#</th>
                                {{-- <th width="200">Code</th> --}}
                                <th width="200">{{ __('general.name') }}</th>
                                <th width="50">{{ __('grade.order') }}</th>
                                <th width="80">{{ __('general.status') }}</th>
                                <th width="50">#</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div class="overlay d-none">
                    <i class="fa fa-refresh fa-spin"></i>
                </div>
            </div>
        </div>

    </div>
</div>
<div class="modal fade" id="add-filter" tabindex="-1" role="dialog" aria-hidden="true" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ __('general.srch') }}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <form id="form-search" autocomplete="off">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label" for="name">{{ __('general.name') }}</label>
                                <input type="text" name="name" class="form-control" placeholder="{{ __('general.name') }}">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button form="form-search" type="submit" class="btn btn-primary" title="{{ __('general.srch') }}"><i class="fa fa-search"></i></button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{asset('adminlte/component/dataTables/js/datatables.min.js')}}"></script>
<script type="text/javascript">
    function filter(){
		$('#add-filter').modal('show');
	}
    $(function(){
        dataTable = $('.datatable').DataTable( {
            stateSave:true,
            processing: true,
            serverSide: true,
            filter:false,
            info:false,
            lengthChange:true,
            responsive: true,
            order: [[ 4, "asc" ]],
            language: {
                lengthMenu: `{{ __('general.showent') }}`,
                processing: `{{ __('general.process') }}`,
                paginate: {
                    previous: `{{ __('general.prev') }}`,
                    next: `{{ __('general.next') }}`,
                }
            },
            ajax: {
                url: "{{route('grade.read')}}",
                type: "GET",
                data:function(data){
                    var name = $('#form-search').find('input[name=name]').val();
                    data.name = name;
                }
            },
            columnDefs:[
            {
                orderable: false,targets:[0]
            },
            { className: "text-right", targets: [2] },
            { className: "text-center", targets: [3, 4] },

            {
                render: function (data, type, row) {
                   if (row.status == 1) {
                      return `<span class="badge badge-success">{{ __('general.actv') }}</span>`
                  } else {
                      return `<span class="badge badge-danger">{{ __('general.noactv') }}</span>`
                  }
              },
              targets: [3]
          },
          { render: function ( data, type, row ) {
            return `<div class="dropdown">
            <button class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
            <i class="fa fa-bars"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-right">
            <li><a class="dropdown-item" href="{{url('admin/grade')}}/${row.id}/edit"><i class="fas fa-pencil-alt mr-2"></i> {{ __('general.edt') }}</a></li>
            <li><a class="dropdown-item delete" href="#" data-id="${row.id}"><i class="fas fa-trash mr-2"></i> {{ __('general.dlt') }}</a></li>
            </ul></div>`
        },targets: [4]
    }
    ],
    columns: [
    { 
       data: "no" 
   },
   {{-- { 
       data: "code" 
   }, --}}
   { 
       data: "name" 
   },
   { 
       data: "order" 
   },

   { 
       data: "status" 
   },
   { 
       data: "id" 
   },
   ]
});
        $(".select2").select2();
        $('#form-search').submit(function(e){
            e.preventDefault();
            dataTable.draw();
            $('#add-filter').modal('hide');
        })
        $(document).on('click','.delete',function(){
            var id = $(this).data('id');
            bootbox.confirm({
             buttons: {
                confirm: {
                   label: '<i class="fa fa-check"></i>',
                   className: 'btn-{{ config('configs.app_theme') }}'
               },
               cancel: {
                   label: '<i class="fa fa-undo"></i>',
                   className: 'btn-default'
               },
           },
           title:'Menghapus Grade?',
           message:'Data yang telah dihapus tidak dapat dikembalikan',
           callback: function(result) {
               if(result) {
                  var data = {
                    _token: "{{ csrf_token() }}"
                };
                $.ajax({
                 url: `{{url('admin/grade')}}/${id}`,
                 dataType: 'json',
                 data:data,
                 type:'DELETE',
                 beforeSend:function(){
                    $('.overlay').removeClass('hidden');
                }
            }).done(function(response){
                if(response.status){
                    $('.overlay').addClass('hidden');
                    $.gritter.add({
                        title: 'Success!',
                        text: response.message,
                        class_name: 'gritter-success',
                        time: 1000,
                    });
                    dataTable.ajax.reload( null, false );
                }
                else{
                    $.gritter.add({
                        title: 'Warning!',
                        text: response.message,
                        class_name: 'gritter-warning',
                        time: 1000,
                    });
                }
            }).fail(function(response){
                var response = response.responseJSON;
                $('.overlay').addClass('hidden');
                $.gritter.add({
                    title: 'Error!',
                    text: response.message,
                    class_name: 'gritter-error',
                    time: 1000,
                });
            })
        }
    }
});
        })
    })
</script>
@endpush