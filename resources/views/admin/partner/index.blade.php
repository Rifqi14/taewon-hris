@extends('admin.layouts.app')

@section('title',__('customer.cust'))
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
@endsection
@push('breadcrump')
    <li class="breadcrumb-item active">{{ __('customer.cust') }}</li>
@endpush
@section('content')
<div class="row">
    <div class="col-lg-12">
    <div class="card card-{{ config('configs.app_theme') }} card-outline">
        <div class="card-header">
          <h3 class="card-title">{{ __('customer.custdata') }}</h3>
          <!-- tools card -->
          <div class="pull-right card-tools">
            <a href="{{route('partner.create')}}" class="btn btn-{{config('configs.app_theme')}} btn-sm text-white" data-toggle="tooltip" title="{{ __('general.crt') }}">
              <i class="fa fa-plus"></i>
            </a>
            <a href="{{route('partner.import')}}" class="btn btn-{{ config('configs.app_theme') }} btn-sm" data-toggle="tooltip" title="{{__('general.imp')}}" style="cursor: pointer;"><i class="fa fa-file-import"></i></a>
            <a href="#" onclick="filter()" class="btn btn-default btn-sm" data-toggle="tooltip" title="Search">
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
                        <th width="100">{{ __('general.name') }}</th>
                        <th width="100">{{__('department.dep')}}</th>
                        <th width="100">{{__('general.truck')}}</th>
                        <th width="100">RIT</th>
                        <th width="50">Status</th>
                        <th width="10">#</th>
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
<div class="modal fade" id="add-filter" tabindex="-1" role="dialog"  aria-hidden="true" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ __('general.filter') }}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>   
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
                <button form="form-search" type="submit" class="btn btn-default" title="Apply"><i class="fa fa-search"></i></button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{asset('adminlte/component/dataTables/js/datatables.min.js')}}"></script>
<script src="{{asset('assets/js/plugins/bootbox/bootbox.min.js')}}"></script>
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
        order: [[ 5, "asc" ]],
        language: {
            lengthMenu: `{{ __('general.showent') }}`,
            processing: `{{ __('general.process') }}`,
            paginate: {
                previous: `{{ __('general.prev') }}`,
                next: `{{ __('general.next') }}`,
            }
        },
        ajax: {
            url: "{{route('partner.read')}}",
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
            { className: "text-right", targets: [0,4] },
            { className: "text-center", targets: [5,6] },
            { render: function ( data, type, row ) {
                return `${row.name} <br><small>${row.code}</small>`
            },targets: [1]},
            { render: function(data, type, row) {
                if (data == 1) {
                    return '<span class="badge badge-success">{{ __('general.actv') }}</span>';
                } else {
                    return '<span class="badge badge-danger">{{ __('general.noactv') }}</span>';
                }
            }, targets:[5]},
            { render: function ( data, type, row ) {
                return `<div class="dropdown">
                    <button class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-bars"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li><a class="dropdown-item" href="{{url('admin/partner')}}/${row.id}/edit"><i class="fa fa-edit"></i> {{ __('general.edt') }}</a></li>
                        <li><a class="dropdown-item delete" href="#" data-id="${row.id}"><i class="fa fa-trash"></i> {{ __('general.dlt') }}</a></li>
                    </ul></div>`
            },targets: [6]
            }
        ],
        columns: [
            { data: "no" },
            { data: "name" },
            { data: "department_name" },
            { data: "truck_name" },
            { data: "rit" },
            { data: "status" },
            { data: "id" },
        ]
    });
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
			title:'Menghapus partner?',
			message:'Data yang telah dihapus tidak dapat dikembalikan',
			callback: function(result) {
					if(result) {
						var data = {
                            _token: "{{ csrf_token() }}"
                        };
						$.ajax({
							url: `{{url('admin/partner')}}/${id}`,
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