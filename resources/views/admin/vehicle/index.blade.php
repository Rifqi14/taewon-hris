@extends('admin.layouts.app')

@section('title', 'Vehicle')
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
<li class="breadcrumb-item active">Vehicle</li>
@endpush
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card card-{{config('configs.app_theme')}} card-outline">
            <div class="card-header">
                <h3 class="card-title">Data Vehicle</h3>
                <!-- tools box -->
                <div class="pull-right card-tools">
                    <a href="{{route('vehicle.draft')}}" class="btn btn-{{config('configs.app_theme')}} btn-sm text-white" data-toggle="tooltip"
                        title="Create">
                        <i class="fa fa-plus"></i>
                    </a>
                </div>
                <!-- /. tools -->
            </div>
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="code">Merk</label>
                        <input type="text" class="form-control select2" id="merk" placeholder="Merk" name="merk">
                    </div>
                    <div class="merk-container"></div>
                    <div class="form-group col-md-4">
                        <label for="plat_no">Plat Number</label>
                        <input type="text" class="form-control" id="plat_no" placeholder="Plat No" name="plat_no">
                    </div>
                    <div class="plat-container"></div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label" for="category">Vehicle Category</label>
                            {{-- <input type="text" name="category" id="category" class="form-control filter" placeholder="Category"> --}}
                            <select name="category" id="category" class="form-control select" style="width: 100%" aria-hidden="true" multiple data-placeholder="Select Category">
                                @foreach ($categories as $key => $category)
                                {{-- <option value="{{ $category->name }}">{{ $category->path }}</option> --}}
                                <option value="{{ $category->name }}" data-key="{{ $key }}" data-stock="{{ $category->stok }}">{{ $category->path }}</option>
                                @endforeach
                              </select>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="pic">PIC</label>
                        <select name="pic" id="pic" class="form-control option1" style="width: 100%" multiple aria-hidden="true" data-placeholder="PIC">
                            <option value=""></option>
                            @foreach ($assets as $asset)
                            <option value="{{ $asset->employee_id }}">{{ $asset->pic }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="driver">Driver</label>
                        {{-- <input type="text" class="form-control select2" id="driver" placeholder="Driver" name="driver"> --}}
                        <select name="driver" id="driver" class="form-control option1" style="width: 100%" multiple aria-hidden="true" data-placeholder="Driver">
                            <option value=""></option>
                            @foreach ($drivers as $driver)
                            <option value="{{ $driver->driver_id }}">{{ $driver->driver }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-row col-md-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label" for="date_from">From</label>
                                <div class="controls">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="far fa-calendar-alt"></i>
                                            </span>
                                        </div>
                                        <input type="text" name="date_from" id="date_from" class="form-control datepicker filter" placeholder="Date">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label" for="date_to">To</label>
                                <div class="controls">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                            <i class="far fa-calendar-alt"></i>
                                            </span>
                                        </div>
                                        <input type="text" name="date_to" id="date_to" class="form-control datepicker filter" placeholder="Date">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <table class="table table-striped table-bordered datatable" style="width:100%">
                    <thead>
                        <tr>
                            <th width="10">#</th>
                            <th width="250">Name</th>
                            <th width="100">Merk</th>
                            <th width="100">Type</th>
                            <th width="100">Driver</th>
                            <th width="100">PIC</th>
                            <th width="100">Location</th>
                            <th width="100">Last Maintenance</th>
                            <th width="10">#</th>
                        </tr>
                    </thead>
                </table>
            <div class="overlay d-none">
                <i class="fa fa-refresh fa-spin"></i>
            </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{asset('adminlte/component/dataTables/js/datatables.min.js')}}"></script>
<script src="{{asset('adminlte/component/daterangepicker/moment.min.js')}}"></script>
<script src="{{asset('adminlte/component/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('adminlte/component/jquery-ui/jquery-ui.min.js')}}"></script>
<script type="text/javascript">
   
    $(function () {
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
    $(function () {
        const category = '{!! $categories !!}';
        let totaldata = 0;

        $(".option1").select2({
            allowClear: true
        });
        $(".select").select2({
		    allowClear: true,
            formatResult: (item) => {
                const element = $(item.element[0]);
                // let html = `<option value="${item.id}"><div>${item.text}</div> <br/> <div>${element.data('stock')}</div></option>`;
                let html = `<span>${item.text}</span> 
                            <span class="font-italic" style="float: right">${(element.data('stock') > 0) ? element.data('stock') : totaldata}</span>`;
                return html;
            },
	    });
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
                url: "{{route('vehicle.read')}}",
                type: "GET",
                data: function (data) {
                    var name    = $('input[name=plat_no]').val();
                    var category    = $('#category').val();
                    var pic     = $('select[name=pic]').val();
                    var vendor  = $('input[name=vendor]').val();
                    var merk    = $('input[name=merk]').val();
                    var driver  = $('select[name=driver]').val();
                    var date_from = $('input[name=date_from]').val();
                    var date_to = $('input[name=date_to]').val();
                    data.name   = name;
                    data.category = category;
                    data.pic    = pic;
                    data.vendor = vendor;
                    data.merk   = merk;
                    data.driver = driver;
                    data.date_from = date_from;
                    data.date_to = date_to;
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
                    targets: [0]
                },

                {
					render: function ( data, type, row ) {
						return `
                        <div class="asset-wrapper">
                            <div class="mt-1">
                                <img src="${row.image}" width="50" class="img-fluid img-rounded elevation-1"/>
                            </div>
                            <div class="ml-2">
                                <a href="{{url('admin/vehicle')}}/${row.id}" title="Detail Data"><strong>${data}</strong><br/>${row.code}<br/>${row.category}</a>
                            </div>
                        </div>`;
					},
					targets: [1]
                },
                {
					render: function ( data, type, row ) {
						return `
                        <div class="asset-wrapper">
                            <div class="ml-2">
                                <a href="{{url('admin/vehicle')}}/${row.id}" title="Detail Data"><strong>${row.type} ${row.model}</strong><br/>${row.production_year}</a>
                            </div>
                        </div>`;
					},
					targets: [3]
                },
                {
                    render: function (data, type, row) {
                        return `<div class="dropdown">
                    <button class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-bars"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li><a class="dropdown-item edit" href="#" data-id="${row.id}"><i class="fas fa-pencil-alt mr-2"></i> Edit</a></li>
                        <li><a class="dropdown-item" href="{{url('admin/vehicle')}}/${row.id}"><i class="fas fa-info mr-3"></i> Detail</a></li>
                        <li><a class="dropdown-item delete" href="#" data-id="${row.id}"><i class="fas fa-trash mr-2"></i> Delete</a></li>
                    </ul></div>`
                    },
                    targets: [8]
                },
            ],
            columns: [
                {data: "no"},
                { data: "name"},
                {data: "merk"},
                {data: "type"},
                {data: "driver"},
                {data: "pic"},
                {data: "location"},
                {data: "buy_date"},
                {data: "id"},
            ]
        });
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
                title: 'Menghapus Vehicle?',
                message: 'Data yang telah dihapus tidak dapat dikembalikan',
                callback: function (result) {
                    if (result) {
                        var data = {
                            _token: "{{ csrf_token() }}"
                        };
                        $.ajax({
                            url: `{{url('admin/vehicle')}}/${id}`,
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
                        className: 'btn-{{ config("configs.app_theme") }}'
                    },
                    cancel: {
                        label: '<i class="fa fa-undo"></i>',
                        className: 'btn-default'
                    },
                },
                title: 'Edit vehicle?',
                message: 'You will be redirect to vehicle edit page, are you sure?',
                callback: function (result) {
                    if (result) {
                        document.location = "{{url('admin/vehicle')}}/"+id+"/edit";
                    }
                }
            });
        })
        // $("#category").select2({
        //     ajax: {
        //         url: "{{route('vehicle.selectcategory')}}",
        //         type: 'GET',
        //         dataType: 'json',
        //         data: function (term, page) {
        //             return {
        //                 name: term,
        //                 page: page,
        //                 limit: 30,
        //             };
        //         },
        //         results: function (data, page) {
        //             var more = (page * 30) < data.total;
        //             var option = [];
        //             $.each(data.rows, function (index, item) {
        //                 option.push({
        //                     id: item.name,
        //                     text: `${item.path}` ,
        //                     stock: `${item.stock}`
        //                 });
        //             });
        //             return {
        //                 results: option,
        //                 more: more,
        //             };
        //         },
        //     },
        //     allowClear: true,
        //     formatResult: function(item)
        //     {
        //         return item.stock;
        //     },
        // });
    });
    $(document).ready(function () {
        var merks = [
				@foreach($merks as $v_merk)
                	"{!!$v_merk->merk!!}",
            	@endforeach
			];
			$( "input[name=merk]" ).autocomplete({
			source: merks,
			minLength:0,
			appendTo: '#merk-container',
			select: function(event, response) {
				if(event.preventDefault(), 0 !== response.item.id){
					$(this).val(response.item.value);
					dataTable.draw();
				}
			}
			}).focus(function () {
				$(this).autocomplete("search");
			});
			$("input[name=merk]").keydown(function(event){
				if(event.keyCode == 13) {
					event.preventDefault();
					$('input[name=merk]').autocomplete('close');
					return false;
				}
		});
        var plats = [
				@foreach($plats as $plat)
                	"{!!$plat->name!!}",
            	@endforeach
			];
			$( "input[name=plat_no]" ).autocomplete({
			source: plats,
			minLength:0,
			appendTo: '#plat-container',
			select: function(event, response) {
				if(event.preventDefault(), 0 !== response.item.id){
					$(this).val(response.item.value);
					dataTable.draw();
				}
			}
			}).focus(function () {
				$(this).autocomplete("search");
			});
			$("input[name=plat_no]").keydown(function(event){
				if(event.keyCode == 13) {
					event.preventDefault();
					$('input[name=plat_no]').autocomplete('close');
					return false;
				}
		});
     $(document).on('keyup', '#merk', function() {
        dataTable.draw();
     });
     $(document).on('change', '#category', function() {
        dataTable.draw();
     });
     $(document).on('keyup', '#plat_no', function() {
        dataTable.draw();
     });
     $(document).on('change', '#pic', function() {
        dataTable.draw();
     });
     $(document).on('keyup', '#vendor', function() {
        dataTable.draw();
     });
     $(document).on('change', '#driver', function() {
        dataTable.draw();
     });
    });
</script>
@endpush
