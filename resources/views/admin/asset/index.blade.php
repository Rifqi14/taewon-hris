@extends('admin.layouts.app')

@section('title', 'Asset')
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/daterangepicker/daterangepicker.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/jquery-ui/jquery-ui.min.css')}}" rel="stylesheet">
<style type="text/css">
    .asset-wrapper{
        display: flex;
    }
    .ui-state-active{
        background: #28a745 !important;
        border-color: #28a745 !important;
    }
    .ui-menu {
        overflow: auto;
        height:200px;
    }
</style>
@endsection
@push('breadcrump')
<li class="breadcrumb-item active">Asset</li>
@endpush
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card card-{{config('configs.app_theme')}} card-outline">
            <div class="card-header">
                <h3 class="card-title">Data Asset</h3>
                <!-- tools box -->
                <div class="pull-right card-tools">
                    <a href="{{route('asset.draft')}}" class="btn btn-{{config('configs.app_theme')}} btn-sm text-white" data-toggle="tooltip" title="Create"><i class="fa fa-plus"></i></a>
                    <a href="{{route('asset.import')}}" class="btn btn-{{ config('configs.app_theme') }} btn-sm" data-toggle="tooltip" title="Import" style="cursor: pointer;"><i class="fa fa-file-import"></i></a>
                    <a href="javascript:void(0)" onclick="exportasset()" class="btn btn-primary btn-sm text-white" data-toggle="tooltip" title="Export" style="cursor: pointer;"><i class="fa fa-download"></i></a>
                </div>
                <!-- /. tools -->
            </div>
            <div class="card-body">
                <form id="form" class="form-horizontal" method="post">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label" for="name">Searching For</label>
                                <input type="text" name="asset_name" id="asset_name" class="form-control filter" placeholder="Asset Name">
                                
                                {{-- <select name="asset_name" id="asset_name" class="form-control select2" style="width: 100%" aria-hidden="true" data-placeholder="Asset Name">
                                    <option value=""></option>
                                    @foreach ($asset_names as $a_name)
                                    <option value="{{ $a_name->name }}">{{ $a_name->name }}</option>
                                    @endforeach
                                  </select> --}}
                            </div>
                            <div id="asset-container"></div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label" for="pic">PIC</label>
                                {{-- <input type="text" name="pic" id="pic" class="form-control filter" placeholder="PIC"> --}}
                                <select name="pic" id="pic" class="form-control select2" style="width: 100%" aria-hidden="true" multiple data-placeholder="PIC">
                                    <option value=""></option>
                                    @foreach ($assets as $asset)
                                    <option value="{{ $asset->pic }}">{{ $asset->pic }}</option>
                                    @endforeach
                                  </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label" for="location">Location</label>
                                {{-- <input type="text" name="location" id="location" class="form-control filter" placeholder="Location"> --}}
                                <select name="location" id="location" class="form-control select2" style="width: 100%" aria-hidden="true" multiple data-placeholder="Location">
                                    <option value=""></option>
                                    @foreach ($locations as $lokal)
                                    <option value="{{ $lokal->location }}">{{ $lokal->location }}</option>
                                    @endforeach
                                  </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label" for="vendor">Vendor</label>
                                {{-- <input type="text" name="vendor" id="vendor" class="form-control filter" placeholder="Vendor"> --}}
                                <select name="vendor" id="vendor" class="form-control select2" style="width: 100%" aria-hidden="true" multiple data-placeholder="Vendor">
                                    <option value=""></option>
                                    @foreach ($vendors as $ven)
                                    <option value="{{ $ven->vendor }}">{{ $ven->vendor }}</option>
                                    @endforeach
                                  </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label" for="category">Asset Category</label>
                                {{-- <input type="text" name="category[]" id="category" class="form-control filter" placeholder="Category"> --}}
                                  <select name="category" id="category" class="form-control option1" style="width: 100%" aria-hidden="true" multiple data-placeholder="Select Category">
                                    @foreach ($categories as $key => $category)
                                    {{-- <option value="{{ $category->name }}">{{ $category->path }}</option> --}}
                                    <option value="{{ $category->name }}" data-key="{{ $key }}" data-stock="{{ $category->stok }}">{{ $category->path }}</option>
                                    @endforeach
                                  </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="code">Code</label>
                                <input type="text" class="form-control filter" id="code" name="code" placeholder="Code">
                            </div>
                            <div class="code-container"></div>
                        </div>
                        <div class="form-row col-md-4">
                            <div class="form-group col-md-6">
                                <label for="from">From</label>
                                <input type="text" class="form-control datepicker filter" id="date_from" placeholder="From" name="date_from">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="to">To</label>
                                <input type="text" class="form-control datepicker filter" id="date_to" placeholder="To" name="date_to">
                            </div>
                        </div>
                    </div>
                </form>
                <table class="table table-striped table-bordered datatable" style="width:100%">
                    <thead>
                        <tr>
                            <th width="10">#</th>
                            <th width="250">Name</th>
                            <th width="100">PIC</th>
                            <th width="100">Location</th>
                            <th width="100">Buy Price</th>
                            <th width="100">Vendor</th>
                            <th width="100">Buy Date</th>
                            <th width="50">Stock</th>
                            <th width="10">#</th>
                        </tr>
                    </thead>
                </table>
            </div>
            {{-- <div class="overlay d-none">
                <i class="fa fa-refresh fa-spin"></i>
            </div> --}}
            <div class="overlay d-none" style="border: 1px solid black;">
                <i>
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="margin: auto; background: none; display: block; shape-rendering: auto; margin-top;10px;" width="200px" height="200px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
                        <circle cx="84" cy="50" r="10" fill="#e15b64">
                            <animate attributeName="r" repeatCount="indefinite" dur="0.25s" calcMode="spline" keyTimes="0;1" values="10;0" keySplines="0 0.5 0.5 1" begin="0s"></animate>
                            <animate attributeName="fill" repeatCount="indefinite" dur="1s" calcMode="discrete" keyTimes="0;0.25;0.5;0.75;1" values="#e15b64;#abbd81;#f8b26a;#f47e60;#e15b64" begin="0s"></animate>
                        </circle><circle cx="16" cy="50" r="10" fill="#e15b64">
                          <animate attributeName="r" repeatCount="indefinite" dur="1s" calcMode="spline" keyTimes="0;0.25;0.5;0.75;1" values="0;0;10;10;10" keySplines="0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1" begin="0s"></animate>
                          <animate attributeName="cx" repeatCount="indefinite" dur="1s" calcMode="spline" keyTimes="0;0.25;0.5;0.75;1" values="16;16;16;50;84" keySplines="0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1" begin="0s"></animate>
                        </circle><circle cx="50" cy="50" r="10" fill="#f47e60">
                          <animate attributeName="r" repeatCount="indefinite" dur="1s" calcMode="spline" keyTimes="0;0.25;0.5;0.75;1" values="0;0;10;10;10" keySplines="0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1" begin="-0.25s"></animate>
                          <animate attributeName="cx" repeatCount="indefinite" dur="1s" calcMode="spline" keyTimes="0;0.25;0.5;0.75;1" values="16;16;16;50;84" keySplines="0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1" begin="-0.25s"></animate>
                        </circle><circle cx="84" cy="50" r="10" fill="#f8b26a">
                          <animate attributeName="r" repeatCount="indefinite" dur="1s" calcMode="spline" keyTimes="0;0.25;0.5;0.75;1" values="0;0;10;10;10" keySplines="0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1" begin="-0.5s"></animate>
                          <animate attributeName="cx" repeatCount="indefinite" dur="1s" calcMode="spline" keyTimes="0;0.25;0.5;0.75;1" values="16;16;16;50;84" keySplines="0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1" begin="-0.5s"></animate>
                        </circle><circle cx="16" cy="50" r="10" fill="#abbd81">
                          <animate attributeName="r" repeatCount="indefinite" dur="1s" calcMode="spline" keyTimes="0;0.25;0.5;0.75;1" values="0;0;10;10;10" keySplines="0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1" begin="-0.75s"></animate>
                          <animate attributeName="cx" repeatCount="indefinite" dur="1s" calcMode="spline" keyTimes="0;0.25;0.5;0.75;1" values="16;16;16;50;84" keySplines="0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1" begin="-0.75s"></animate>
                        </circle>
                    </svg>
                </i>
            </div>
            {{-- <div class="progress">
                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: 75%"></div>
            </div> --}}
        </div>
    </div>
</div>

{{-- Edit Sock --}}
<div class="modal fade" id="edit-stock" tabindex="-1" role="dialog" aria-hidden="true" tabindex="-1" role="dialog"
    aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Change Stock</h4>
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        </div>
        <div class="modal-body">
          <form id="form-stock" action="{{ route('asset.stockupdate') }}"
            class="form-horizontal no-gutters" method="post" autocomplete="off">
            {{ csrf_field() }}
            <input type="hidden" namea_nameid" id="asset_id">
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label class="control-label" for="stock">Stock</label>
                  <input type="number" class="form-control" name="stock" id="stock"
                    placeholder="Stock">
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
<script src="{{asset('adminlte/component/jquery-ui/jquery-ui.min.js')}}"></script>
<script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
<script src="{{asset('adminlte/component/dataTables/js/datatables.min.js')}}"></script>
<script src="{{asset('adminlte/component/daterangepicker/moment.min.js')}}"></script>
<script src="{{asset('adminlte/component/daterangepicker/daterangepicker.js')}}"></script>
<script type="text/javascript">
    function exportasset() {
        $.ajax({
            url: "{{ route('asset.export') }}",
            type: 'POST',
            dataType: 'JSON',
            data: $("#form").serialize(),
            beforeSend:function(){
                // $('.overlay').removeClass('d-none');
                waitingDialog.show('Loading...');
            }
        }).done(function(response){
            waitingDialog.hide();
            if(response.status){
                $('.overlay').addClass('d-none');
                $.gritter.add({
                    title: 'Success!',
                    text: response.message,
                    class_name: 'gritter-success',
                    time: 1000,
                });
                let download = document.createElement("a");
                download.href = response.file;
                document.body.appendChild(download);
                download.download = response.name;
                download.click();
                download.remove();
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
            waitingDialog.hide();
            var response = response.responseJSON;
            $('.overlay').addClass('d-none');
            $.gritter.add({
                title: 'Error!',
                text: response.message,
                class_name: 'gritter-error',
                time: 1000,
            });
        });
    }

    // $("select[name=asset_name]").keyup(function(e){ 
    //     var code = e.key; // recommended to use e.key, it's normalized across devices and languages
    //     if(code==="Enter") e.preventDefault();
    //     if(code===" " || code==="Enter" || code===","|| code===";"){
    //         // $("#displaysomething").html($(this).val());
    //         alert($(this).val());
    //     } // missing closing if brace
    // });

    $(function () {
        const category = '{!! $categories !!}';
        let totaldata = 0;

        $(".select2").select2({
			allowClear: true
		});

        $(".option1").select2({
            formatResult: (item) => {
                const element = $(item.element[0]);
                let html = `<span>${item.text}</span> 
                            <span class="font-italic" style="float: right">${(element.data('stock') > 0) ? element.data('stock') : totaldata}</span>`;
                return html;
            },
        })
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
            lengthMenu: [ 50, 100, 250, 500, 1000, 2000 ],
            pageLength: 50,
            ajax: {
                url: "{{route('asset.read')}}",
                type: "GET",
                data: function (data) {
                    var asset_name = $('input[name=asset_name]').val();
                    var pic = $('select[name=pic]').val();
                    var location = $('select[name=location]').val();
                    var vendor = $('select[name=vendor]').val();
                    var category = $('#category').val();
                    var date_from = $('input[name=date_from]').val();
                    var code = $('input[name=code]').val();
                    var date_to = $('input[name=date_to]').val();
                    data.asset_name = asset_name;
                    data.pic = pic;
                    data.code = code;
                    data.location = location;
                    data.vendor = vendor;
                    data.category = category;
                    data.date_from = date_from;
                    data.date_to = date_to;
                    
                    // console.log(asset_name);
                }
            },
            columnDefs: [{
                    orderable: false,
                    targets: [0]
                },
                {
                    className: "text-center",
                    targets: [0,6,8]
                },
                {
                    className: "text-right",
                    targets: [4,7]
                },

                {
					render: function ( data, type, row ) {
						return `
                        <div class="asset-wrapper">
                            <div class="mt-1">
                                <img src="${row.image}" width="50" class="img-fluid img-rounded elevation-1"/>
                            </div>
                            <div class="ml-2">
                                <a href="{{url('admin/asset')}}/${row.id}" title="Detail Data"><strong>${data}</strong><br/>${row.code}<br/>${row.category}</a>
                            </div>
                        </div>`;
					},
					targets: [1]
                },
                    { render: function ( data, type, row ) {
                        return `<span class="text-blue">${row.stock ? row.stock : '-'}</span>`;
                    },targets: [7]
                    },
                    {
                    render: function (data, type, row) {
                        return `<div class="dropdown">
                    <button class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-bars"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li><a class="dropdown-item edit" href="#" data-id="${row.id}"><i class="fas fa-pencil-alt mr-2"></i> Edit</a></li>
                        <li><a class="dropdown-item" href="{{url('admin/asset')}}/${row.id}"><i class="fas fa-info mr-3"></i> Detail</a></li>
                        <li><a class="dropdown-item delete" href="#" data-id="${row.id}"><i class="fas fa-trash mr-2"></i> Delete</a></li>
                    </ul></div>`
                    },
                    targets: [8]
                },
            ],
            columns: [
                {data: "no"},
                {data: "name"},
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
                            url: `{{url('admin/asset')}}/${id}`,
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
        $(document).on('click', '.edit', function () {
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
                title: 'Edit asset?',
                message: 'You will be redirect to asset edit page, are you sure?',
                callback: function (result) {
                    if (result) {
                        document.location = "{{url('admin/asset')}}/"+id+"/edit";
                    }
                }
            });
        })
    });
    $(document).ready(function () {
        var asset_names = [
            @foreach($asset_names as $asset_name)
                "{!!$asset_name->name!!}",
            @endforeach
        ];
            $( "input[name=asset_name]" ).autocomplete({
        source: asset_names,
        minLength:0,
        appendTo: '#asset-container',
        select: function(event, response) {
            if(event.preventDefault(), 0 !== response.item.id){
                $(this).val(response.item.value);
                dataTable.draw();
            }
        }
        }).focus(function () {
            $(this).autocomplete("search");
        });
        $("input[name=asset_name]").keydown(function(event){
            if(event.keyCode == 13) {
                event.preventDefault();
                $('input[name=asset_name]').autocomplete('close');
                return false;
            }
        });
        var codes = [
            @foreach($codes as $v_code)
                "{!!$v_code->code!!}",
            @endforeach
        ];
            $( "input[name=code]" ).autocomplete({
        source: codes,
        minLength:0,
        appendTo: '#code-container',
        select: function(event, response) {
            if(event.preventDefault(), 0 !== response.item.id){
                $(this).val(response.item.value);
                dataTable.draw();
            }
        }
        }).focus(function () {
            $(this).autocomplete("search");
        });
        $("input[name=code]").keydown(function(event){
            if(event.keyCode == 13) {
                event.preventDefault();
                $('input[name=code]').autocomplete('close');
                return false;
            }
        });
        $('#date_to').daterangepicker({
            autoUpdateInput: false,
            singleDatePicker: true,
            timePicker: false,
            timePickerIncrement: 1,
            locale: {
            format: 'DD/MM/YYYY'
            }
        },function(chosen_date) {
            $('#date_to').val(chosen_date.format('DD/MM/YYYY'));
            dataTable.draw();
        });
        $('#date_from').daterangepicker({
            autoUpdateInput: false,
            singleDatePicker: true,
            timePicker: false,
            timePickerIncrement: 1,
            locale: {
            format: 'DD/MM/YYYY'
            }
        },function(chosen_date) {
            $('#date_from').val(chosen_date.format('DD/MM/YYYY'));
            dataTable.draw();
        });
        $(document).on('change keyup keydown keypress focus', '.filter', function() {
            dataTable.draw();
        });
        $(document).on('change', '#asset_name', function() {
		    dataTable.draw();
	    });
		$(document).on('change', '#pic', function() {
		    dataTable.draw();
	    });
        $(document).on('change', '#location', function() {
		    dataTable.draw();
	    });
        $(document).on('change', '#vendor', function() {
		    dataTable.draw();
	    });
        $(document).on('change', '#category', function() {
		    dataTable.draw();
	    });
    });
</script>
@endpush
