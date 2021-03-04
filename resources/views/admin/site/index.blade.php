@extends('admin.layouts.app')

@section('title', 'Unit')
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
@endsection
@push('breadcrump')
    <li class="breadcrumb-item active">Unit</li>
@endpush
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card card-{{config('configs.app_theme')}}  card-outline">
            <div class="card-header"> 
            <h3 class="card-title">List Unit</h3>
            <!-- tools box -->
            <div class="pull-right card-tools">
                <a href="{{route('site.create')}}" class="btn btn-{{config('configs.app_theme')}} btn-sm" data-toggle="tooltip" title="Tambah">
                <i class="fa fa-plus"></i>
                </a>
                <a href="#" onclick="filter()" class="btn btn-default btn-sm" data-toggle="tooltip" title="Search">
                    <i class="fa fa-search"></i>
                </a>
            </div>
            <!-- /. tools -->
            </div>
            <div class="card-body">
                <table class="table table-striped table-bordered datatable">
                    <thead>
                        <tr>
                            <th width="10">#</th>
                            <th width="100">Code</th>
                            <th width="120">Name</th>
                            <th width="100">Create</th>
                            <th width="10">#</th>
                        </tr>
                    </thead>
                </table>
            </div>
            <div class="overlay d-none">
                <i class="fas fa-2x fa-sync-alt fa-spin"></i>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="add-filter" tabindex="-1" role="dialog"  aria-hidden="true" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Search</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
			</div>
            <div class="modal-body">
                <form id="form-search" autocomplete="off">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label" for="code">Code</label>
                                <input type="text" name="code" class="form-control" placeholder="Kode">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label" for="name">Name</label>
                                <input type="text" name="name" class="form-control" placeholder="Nama">
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
        ajax: {
            url: "{{route('site.read')}}",
            type: "GET",
            data:function(data){
                var code = $('#form-search').find('input[name=code]').val();
                var name = $('#form-search').find('input[name=name]').val();
                data.code = code;
                data.name = name;
            }
        },
        columnDefs:[
            {
                orderable: false,targets:[0]
            },
            { className: "text-right", targets: [0] },
            { className: "text-center", targets: [4] },
            { render: function ( data, type, row ) {
                return `<div class="dropdown">
                    <button class="btn btn-default btn-xs  dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-bars"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="{{url('admin/site')}}/${row.id}/edit"><i class="fa fa-edit mr-2"></i> Edit</a>
                        <a class="dropdown-item delete" href="#" data-id="${row.id}"><i class="fa fa-trash mr-2"></i> Delete</a>
                    </div></div>`
            },targets: [4]
            }
        ],
        columns: [
            { data: "no" },
            { data: "code" },
            { data: "name" },
            { data: "created_at" },
            { data: "id" },
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
					className: 'btn-{{config('configs.app_theme')}}'
				},
				cancel: {
					label: '<i class="fa fa-undo"></i>',
					className: 'btn-default'
				},
			},
			title:'Menghapus toko?',
			message:'Data yang telah dihapus tidak dapat dikembalikan',
			callback: function(result) {
					if(result) {
						var data = {
                            _token: "{{ csrf_token() }}"
                        };
						$.ajax({
							url: `{{url('admin/site')}}/${id}`,
							dataType: 'json',
							data:data,
							type:'DELETE',
                            beforeSend:function(){
                                $('.overlay').removeClass('d-none');
                            }
                        }).done(function(response){
                            if(response.status){
                                $('.overlay').addClass('d-none');
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
                            $('.overlay').addClass('d-none');
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
