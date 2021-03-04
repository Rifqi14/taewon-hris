@extends('admin.layouts.app')

@section('title', 'Asset Movement')
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
<style type="text/css">
    .asset-wrapper{
        display: flex;
    }
</style>
@endsection
@push('breadcrump')
<li class="breadcrumb-item active">Asset Movement</li>
@endpush
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card card-{{config('configs.app_theme')}} card-outline">
            <div class="card-header">
                <h3 class="card-title">Data Asset Movement</h3>
                <!-- tools box -->
                <div class="pull-right card-tools">
                    <a href="{{route('assetmovement.create')}}" class="btn btn-{{config('configs.app_theme')}} btn-sm text-white" data-toggle="tooltip"
                        title="Back">
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
                            <th width="100">Asset Serial</th>
                            <th width="100">Type</th>
                            <th width="100">Transaction Date</th>
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
<div class="modal fade" id="add-filter" tabindex="-1" role="dialog" aria-hidden="true" tabindex="-1" role="dialog"
    aria-hidden="true" data-backdrop="static">
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
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label" for="serial">Serial Number</label>
                                <input type="text" name="serial" class="form-control" placeholder="Serial Number">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button form="form-search" type="submit" class="btn btn-default" title="Apply"><i
                        class="fa fa-search"></i></button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{asset('adminlte/component/dataTables/js/datatables.min.js')}}"></script>
<script src="{{asset('assets/js/plugins/bootbox/bootbox.min.js')}}"></script>
<script type="text/javascript">
    function filter() {
        $('#add-filter').modal('show');
    }
    $(function () {
        dataTable = $('.datatable').DataTable({
            stateSave: true,
            processing: true,
            serverSide: true,
            filter: false,
            info: false,
            lengthChange: true,
            responsive: true,
            order: [
                [3, "asc"]
            ],
            ajax: {
                url: "{{route('assetmovement.read')}}",
                type: "GET",
                data: function (data) {
                    var serial = $('#form-search').find('input[name=serial]').val();
                    data.serial = serial;
                }
            },
            columnDefs: [{
                    orderable: false,
                    targets: [0]
                },
                {
                    className: "text-center",
                    targets: [0, 2]
                },
                {
					render: function ( data, type, row ) {
                        if(row.type == 1){
						return `<div>
                                <label class="badge badge-success">In</label>
                            </div>`;
                        }else{
                        return `<div>
                                <label class="badge badge-warning">Out</label>
                            </div>`;
                        }
					},
					targets: [2]
                },
            ],
            columns: [
                { data: "no" },
                { data: "asset_serial" },
                { data: "type" },
                { data: "transaction_date" },
            ]
        });

        $('#form-search').submit(function (e) {
            e.preventDefault();
            dataTable.draw();
            $('#add-filter').modal('hide');
        })

        //asset delete
        $(document).on('click', '.delete', function () {
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
                title: 'Menghapus bidang?',
                message: 'Data yang telah dihapus tidak dapat dikembalikan',
                callback: function (result) {
                    if (result) {
                        var data = {
                            _token: "{{ csrf_token() }}"
                        };
                        $.ajax({
                            url: `{{url('admin/assetmovement')}}/${id}`,
                            dataType: 'json',
                            data: data,
                            type: 'DELETE',
                            beforeSend: function () {
                                $('.overlay').removeClass('hidden');
                            }
                        }).done(function (response) {
                            if (response.status) {
                                $('.overlay').addClass('hidden');
                                $.gritter.add({
                                    title: 'Success!',
                                    text: response.message,
                                    class_name: 'gritter-success',
                                    time: 1000,
                                });
                                dataTable.ajax.reload(null, false);
                            } else {
                                $.gritter.add({
                                    title: 'Warning!',
                                    text: response.message,
                                    class_name: 'gritter-warning',
                                    time: 1000,
                                });
                            }
                        }).fail(function (response) {
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
    });
</script>
@endpush
