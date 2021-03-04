@extends('admin.layouts.app')

@section('title', 'Kategori Produk')
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
@endsection
@push('breadcrump')
    <li class="breadcrumb-item active">Kategori Produk</li>
@endpush

@section('content')

<div class="row">
    <div class="col-lg-12">
        <ul class="nav nav-tabs">
            <input type="hidden" name="status" value="1"/>
            <li class="nav-item"><a class="nav-link active" href="#" data-toggle="pill" onClick="changeTab(1)">Aktif</a></li>
            <li class="nav-item"><a class="nav-link" href="#" data-toggle="pill" onClick="changeTab(0)">Diarsipkan</a></li>
        </ul>
        <div class="card">
            <div class="card-header">
            <h3 class="card-title">Data Kategori Produk</h3>
            <!-- tools box -->
            <div class="pull-right card-tools">
                <a href="{{route('productcategory.create')}}" class="btn btn-danger btn-sm text-white" data-toggle="tooltip" title="Tambah">
                <i class="fa fa-plus"></i>
                </a>
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
                            <th width="200">Nama</th>
                            <th width="40">Dibuat</th>
                            <th width="10">#</th>
                        </tr>
                    </thead>
                </table>
            </div>
            <div class="overlay d-none">
                <i class="fa fa-2x fa-sync-alt fa-spin"></i>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="add-filter" tabindex="-1" role="dialog"  aria-hidden="true" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Pencarian</h4>
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			</div>
            <div class="modal-body">
                <form id="form-search" autocomplete="off">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label" for="path">Nama</label>
                                <input type="text" name="path" class="form-control" placeholder="Nama">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button form="form-search" type="submit" class="btn btn-default text-danger" title="Apply"><i class="fa fa-search"></i></button>
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
function changeTab(filter){
    $('input[name=status]').attr('value',filter);
    dataTable.draw();
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
        order: [[ 1, "asc" ]],
        ajax: {
            url: "{{route('productcategory.read')}}",
            type: "GET",
            data:function(data){
                var path = $('#form-search').find('input[name=path]').val();
                var status = $('input[name=status]').val();
                data.status = status;
                data.path = path;
            }
        },
        columnDefs:[
            {
                orderable: false,targets:[0,1,2,3]
            },
            { className: "text-right", targets: [0] },
            { className: "text-center", targets: [3] },
            { render: function ( data, type, row ) {
                return `<div class="dropdown">
                    <button type="button" class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                    <i class="fa fa-bars"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li><a class="dropdown-item" href="{{url('admin/productcategory')}}/${row.id}/edit"><i class="fa fa-pencil-alt mr-2"></i> Edit</a></li>
                        <li><a class="dropdown-item archive" href="#" data-id="${row.id}"><i class="fa fa-file mr-2"></i> Arsipkan</a></li>
                    </ul>
                    </div>`
            },targets: [3]
            }
        ],
        columns: [
            { data: "no" },
            { data: "path" },
            { data: "created_at" },
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
					className: 'btn-danger'
				},
				cancel: {
					label: '<i class="fa fa-undo"></i>',
					className: 'btn-default'
				},
			},
			title:'Menghapus Data Kategori Produk?',
			message:'Data yang telah dihapus tidak dapat dikembalikan',
			callback: function(result) {
					if(result) {
						var data = {
                            _token: "{{ csrf_token() }}",
                            id: id
                        };
						$.ajax({
							url: `{{url('admin/productcategory')}}/${id}`,
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
     $(document).on('click','.archive',function(){
        var id = $(this).data('id');
        bootbox.confirm({
			buttons: {
				confirm: {
					label: '<i class="fa fa-check"></i>',
					className: 'btn-danger'
				},
				cancel: {
					label: '<i class="fa fa-undo"></i>',
					className: 'btn-default'
				},
			},
			title:'Arsipkan Data Kategori Produk?',
			message:'Data yang telah diarsipkan tidak dapat dikembalikan',
			callback: function(result) {
					if(result) {
						var data = {
                            _token: "{{ csrf_token() }}",
                            id: id
                        };
						$.ajax({
							url: `{{url('admin/productcategory/archive')}}/${id}`,
							dataType: 'json',
							data:data,
							type:'POST',
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
                                dataTableSite.ajax.reload( null, false );
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
