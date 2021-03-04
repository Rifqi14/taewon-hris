@extends('admin.layouts.app')

@section('title', 'Asset Category')
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
@endsection
@push('breadcrump')
    <li class="breadcrumb-item active">Asset Category</li>
@endpush

@section('content')

<div class="row">
    <div class="col-lg-12">
        <div class="card card-{{config('configs.app_theme')}} card-outline">
            <div class="card-header p-0 border-bottom-0">
                <ul class="nav nav-tabs">
                    <input type="hidden" name="status" value="1"/>
                    <li class="nav-item"><a class="nav-link active" href="#" data-toggle="pill" onClick="changeTab(1)">Active</a></li>
                    <li class="nav-item"><a class="nav-link" href="#" data-toggle="pill" onClick="changeTab(0)">Archive</a></li>
                </ul>
            </div>
            <div class="card-header">
                <h3 class="card-title">Data Asset Category</h3>
                <!-- tools box -->
                <div class="pull-right card-tools">
                    <a href="{{route('assetcategory.create')}}" class="btn btn-{{config('configs.app_theme')}} btn-sm text-white" data-toggle="tooltip" title="Create">
                    <i class="fa fa-plus"></i>
                    </a>
                    <a href="#" onclick="filter()" class="btn btn-default btn-sm" data-toggle="tooltip" title="Search">
                        <i class="fa fa-search"></i>
                    </a>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-striped table-bordered datatable" style="width:100%">
                    <thead>
                        <tr>
                            <th width="10">#</th>
                            <th width="200">Name</th>
                            <th width="50">Stock</th>
                            <th width="40">Created At</th>
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

<div class="modal fade" id="add-filter" tabindex="-1" role="dialog"  aria-hidden="true" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Search</h4>
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			</div>
            <div class="modal-body">
                <form id="form-search">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label" for="path">Name</label>
                                <input type="text" name="path" class="form-control" placeholder="Name">
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
        order: [[ 4, "asc" ]],
        ajax: {
            url: "{{route('assetcategory.read')}}",
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
            { className: "text-right", targets: [0,2] },
            { className: "text-center", targets: [3,4] },
            { render: function ( data, type, row ) {
                if(row.status == 1){
                    return `<div class="dropdown">
                    <button type="button" class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                    <i class="fa fa-bars"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li><a class="dropdown-item" href="{{url('admin/assetcategory')}}/${row.id}/edit"><i class="fa fa-edit mr-1"></i> Edit</a></li>
                        <li><a class="dropdown-item archive" href="#" data-id="${row.id}"><i class="fa fa-file mr-2"></i> Archive</a></li>
                    </ul>
                    </div>`
                }
                else{
                    return `<div class="dropdown">
                    <button type="button" class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                    <i class="fa fa-bars"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li><a class="dropdown-item" href="{{url('admin/assetcategory')}}/${row.id}/edit"><i class="fa fa-edit mr-1"></i> Edit</a></li>
                        <li><a class="dropdown-item nonarchive" href="#" data-id="${row.id}"><i class="fa fa-file mr-2"></i> Non Arsip</a></li>
                        <li><a class="dropdown-item delete" href="#" data-id="${row.id}"><i class="fa fa-trash mr-2"></i> Delete</a></li>
                    </ul>
                    </div>`
                }
            },targets: [4]
            }
        ],
        columns: [
            { data: "no" },
            { data: "path" },
            { data: "asset_stock"},
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
					className: 'btn-{{config('configs.app_theme')}}'
				},
				cancel: {
					label: '<i class="fa fa-undo"></i>',
					className: 'btn-default'
				},
			},
			title:'Menghapus Data Kategori Aset?',
			message:'Data yang telah dihapus tidak dapat dikembalikan',
			callback: function(result) {
					if(result) {
						var data = {
                            _token: "{{ csrf_token() }}",
                            id: id
                        };
						$.ajax({
							url: `{{url('admin/assetcategory')}}/${id}`,
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

    $(document).on('click','.nonarchive',function(){
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
			title:'Non Arsipkan Data Kategori Aset?',
			message:'Data akan titampilkan kembali',
			callback: function(result) {
					if(result) {
						var data = {
                            _token: "{{ csrf_token() }}",
                            id: id
                        };
						$.ajax({
							url: `{{url('admin/assetcategory/nonarchive')}}/${id}`,
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
					className: 'btn-{{config('configs.app_theme')}}'
				},
				cancel: {
					label: '<i class="fa fa-undo"></i>',
					className: 'btn-default'
				},
			},
			title:'Arsipkan Data Kategori Aset?',
			message:'Data yang telah diarsipkan tidak dapat dikembalikan',
			callback: function(result) {
					if(result) {
						var data = {
                            _token: "{{ csrf_token() }}",
                            id: id
                        };
						$.ajax({
							url: `{{url('admin/assetcategory/archive')}}/${id}`,
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
