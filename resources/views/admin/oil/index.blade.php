@extends('admin.layouts.app')

@section('title', 'Oil')
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
<style type="text/css">
    .asset-wrapper{
        display: flex;
    }
</style>
@endsection
@push('breadcrump')
<li class="breadcrumb-item active">Oil</li>
@endpush
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card card-{{config('configs.app_theme')}} card-outline">
            <div class="card-header">
                <h3 class="card-title">Data Oil</h3>
                <!-- tools box -->
                <div class="pull-right card-tools">
                    <a href="{{route('oil.create')}}" class="btn btn-{{config('configs.app_theme')}} btn-sm text-white" data-toggle="tooltip"
                        title="{{__('general.crt')}}">
                        <i class="fa fa-plus"></i>
                    </a>
                    <a href="#" onclick="filter()" class="btn btn-default btn-sm" data-toggle="tooltip" title="{{__('general.srch')}}">
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
                            <th width="250">{{__('general.name')}}</th>
                            <th width="100">PIC</th>
                            <th width="100">{{__('oil.location')}}</th>
                            <th width="100">{{__('oil.buyprice')}}</th>
                            <th width="100">Vendor</th>
                            <th width="100">{{__('oil.buydate')}}</th>
                            <th width="50">{{__('oil.stock')}}</th>
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
<div class="modal fade" id="add-filter" tabindex="-1" role="dialog" aria-hidden="true" tabindex="-1" role="dialog"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{__('general.srch')}}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form-search">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label" for="name">{{__('general.name')}}</label>
                                <input type="text" name="name" class="form-control" placeholder="{{__('general.name')}}">
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
{{-- Edit Sock --}}
<div class="modal fade" id="edit-stock" tabindex="-1" role="dialog" aria-hidden="true" tabindex="-1" role="dialog"
    aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">{{__('general.edt')}} {{__('oil.stock')}}</h4>
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        </div>
        <div class="modal-body">
          <form id="form-stock" action="{{ route('oil.stockupdate') }}"
            class="form-horizontal no-gutters" method="post" autocomplete="off">
            {{ csrf_field() }}
            <input type="hidden" name="asset_id" id="asset_id">
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label class="control-label" for="stock">{{__('oil.stock')}}</label>
                  <input type="number" class="form-control" name="stock" id="stock"
                    placeholder="{{__('oil.stock')}}">
                </div>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button form="form-stock" type="submit" class="btn btn-{{ config('configs.app_theme') }}" title="Apply"><i
              class="fa fa-save"></i></button>
        </div>
      </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
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
                [4, "asc"]
            ],
            ajax: {
                url: "{{route('oil.read')}}",
                type: "GET",
                data: function (data) {
                    var name = $('#form-search').find('input[name=name]').val();
                    data.name = name;
                    // data.id = `${id}`
                }
            },
            columnDefs: [{
                    orderable: false,
                    targets: [0]
                },
                {
                    className: "text-center",
                    targets: [0,6,7,8]
                },
                {
                    className: "text-right",
                    targets: [4]
                },
                 { render: function ( data, type, row ) {
                        return `<span class="text-blue">${row.stock ? row.stock : '-'}</span>`;
                    },targets: [7]
                },

                {
					render: function ( data, type, row ) {
						return `
                        <div class="asset-wrapper">
                            <div class="mt-1">
                                <img src="${row.image}" width="50" class="img-fluid img-rounded elevation-1"/>
                            </div>
                            <div class="ml-2">
                                <a href="{{url('admin/oil')}}/${row.id}" title="Detail Data"><strong>${data}</strong><br/>${row.code}<br/>${row.category}</a>
                            </div>
                        </div>`;
					},
					targets: [1]
                },
                {
                    render: function (data, type, row) {
                        return `<div class="dropdown">
                    <button class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-bars"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li><a class="dropdown-item edit" href="#" data-id="${row.id}"><i class="fas fa-pencil-alt mr-2"></i> Edit</a></li>
                        <li><a class="dropdown-item" href="{{url('admin/oil')}}/${row.id}"><i class="fas fa-info mr-3"></i> Detail</a></li>
                        <li><a class="dropdown-item delete" href="#" data-id="${row.id}"><i class="fas fa-trash mr-2"></i> Delete</a></li>
                    </ul></div>`
                    },
                    targets: [8]
                },
            ],
            columns: [
                {data: "no"},
                { data: "name"},
                {data: "pic"},
                {data: "location"},
                {data: "buy_price"},
                {data: "vendor"},
                {data: "buy_date"},
                {data: "stock", className:"stock text-center"},
                {data: "id"},
            ]
        });
         $('.datatable').on('click', '.stock', function() {
            var data = dataTable.row(this).data();
            if (data) {
                $('#edit-stock').modal('show');
                $('#form-stock input[name=asset_id]').attr('value', data.id);
                $("#stock").attr('value', data.stock).trigger('change');
                $(document).on("change", "#stock", function () {
                    if (!$.isEmptyObject($('#form-stock').validate().submitted)) {
                        $('#form-stock').validate().form();
                    }
                });
            }
        });
        $("#form-stock").validate({
            errorElement: 'div',
            errorClass: 'invalid-feedback',
            focusInvalid: false,
            highlight: function (e) {
                $(e).closest('.form-group').removeClass('has-success').addClass('was-validated has-error');
            },

            success: function (e) {
                $(e).closest('.form-group').removeClass('has-error').addClass('has-success');
                $(e).remove();
            },
            errorPlacement: function (error, element) {
                if(element.is(':file')) {
                error.insertAfter(element.parent().parent().parent());
                }else
                if(element.parent('.input-group').length) {
                error.insertAfter(element.parent());
                }
                else
                if (element.attr('type') == 'checkbox') {
                error.insertAfter(element.parent());
                }
                else{
                error.insertAfter(element);
                }
            },
            submitHandler: function() {
                $.ajax({
                url:$('#form-stock').attr('action'),
                method:'post',
                data: new FormData($('#form-stock')[0]),
                processData: false,
                contentType: false,
                dataType: 'json',
                beforeSend:function(){
                    $('.overlay').removeClass('d-none');
                }
                }).done(function(response){
                    $('.overlay').addClass('d-none');
                    if(response.status){
                        dataTable.draw();
                        $('#edit-stock').modal('hide');
                        $.gritter.add({
                            title: 'Success!',
                            text: response.message,
                            class_name: 'gritter-success',
                            time: 1000,
                        });
                    }
                    else{
                        $.gritter.add({
                            title: 'Warning!',
                            text: response.message,
                            class_name: 'gritter-warning',
                            time: 1000,
                        });
                    }
                    return;
                }).fail(function(response){
                    $('.overlay').addClass('d-none');
                    var response = response.responseJSON;
                    $.gritter.add({
                        title: 'Error!',
                        text: response.message,
                        class_name: 'gritter-error',
                        time: 1000,
                    });
                });
            }
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
                            url: `{{url('admin/oil')}}/${id}`,
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
        });
        $(document).on('click', '.edit', function () {
            var id = $(this).data('id');
            bootbox.confirm({
                buttons: {
                    confirm: {
                        label: '<i class="fa fa-check"></i>',
                        className: 'btn-{{ config("configs.app_theme") }}'
                    },
                    cancel: {
                        label: '<i class="fa fa-undo"></i>',
                        className: 'btn-default'
                    },
                },
                title: 'Edit Oil?',
                message: 'You will be redirect to oil edit page, are you sure?',
                callback: function (result) {
                    if (result) {
                        document.location =  "{{url('admin/oil')}}/"+id+"/edit";
                    }
                }
            });
        });
    });
</script>
@endpush
