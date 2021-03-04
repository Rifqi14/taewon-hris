@extends('admin.layouts.app')

@section('title', 'Maintenance')
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/daterangepicker/daterangepicker.css')}}" rel="stylesheet">
@endsection

@push('breadcrump')
<li class="breadcrumb-item active">Maintenance</li>
@endpush

@section('content')
<div class="wrapper wrapper-content">
    <div class="row">
        <div class="col-lg-12">
            <div class="card ">
                <div class="card-header">
                    <h3 class="card-title">Maintenance List</h3>
                    <!-- tools box -->
                    <div class="pull-right card-tools">
                        <a href="{{route('maintenance.create')}}"
                            class="btn btn-{{ config('configs.app_theme') }} btn-sm text-white" data-toggle="tooltip"
                            title="Tambah">
                            <i class="fa fa-plus"></i>
                        </a>
                        <a href="javascript:void(0)" onclick="exportmaintenance()" class="btn btn-primary btn-sm text-white" data-toggle="tooltip" title="Export" style="cursor: pointer;"><i class="fa fa-download"></i></a>
                    </div>
                    <!-- /. tools -->
                </div>
                <div class="card-body">
                    <form id="form-maintenance" class="form-horizontal" method="post">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="vehicle_name">Vehicle</label>
                                    <input type="text" name="vehicle_name" id="vehicle_name" class="form-control filter"
                                        placeholder="Vehicle">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="vehicle_no">Vehicle No</label>
                                    <input type="text" name="vehicle_no" id="vehicle_no" class="form-control filter"
                                        placeholder="Vehicle No">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="vehicle_category">Vehicle Category</label>
                                    <input type="text" name="vehicle_category" id="vehicle_category"
                                        class="form-control filter" placeholder="Vehicle Category">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="vendor">Vendor</label>
                                    <input type="text" name="vendor" id="vendor" class="form-control filter"
                                        placeholder="Vendor">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="driver">Driver</label>
                                    <input type="text" name="driver" id="driver" class="form-control filter"
                                        placeholder="Driver">
                                </div>
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
                                                <input type="text" name="date_from" id="date_from"
                                                    class="form-control datepicker filter" placeholder="Date">
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
                                                <input type="text" name="date_to" id="date_to"
                                                    class="form-control datepicker filter" placeholder="Date">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </form>
                    <table class="table table-striped table-bordered datatable" style="width:100%">
                        <thead>
                            <tr>
                                <th width="10">#</th>
                                <th width="100">Vehicle</th>
                                <th width="50">Date</th>
                                <th width="50">Km</th>
                                <th width="100">Driver</th>
                                <th class="text-right" width="50">Total</th>
                                <th width="50">Image</th>
                                <th width="50">Status</th>
                                <th width="50">#</th>
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
</div>

{{-- Modal Maintenance --}}
<div class="modal fade" id="show-document" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <embed id="url-document" src="" style="height:500px;width:500px;object-fit:contain;padding:20px;">
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{asset('adminlte/component/dataTables/js/datatables.min.js')}}"></script>
<script src="{{asset('adminlte/component/daterangepicker/moment.min.js')}}"></script>
<script src="{{asset('adminlte/component/daterangepicker/daterangepicker.js')}}"></script>
<script type="text/javascript">
    function showDocument(e){
		$('#url-document').attr("src",$(e).data('url'));
		$('#show-document').modal('show');
    }
    function exportmaintenance() {
        $.ajax({
            url: "{{ route('maintenance.export') }}",
            type: 'POST',
            dataType: 'JSON',
            data: $("#form-maintenance").serialize(),
            beforeSend:function(){
                // $('.overlay').removeClass('d-none');
                waitingDialog.show('Loading...');
            }
        }).done(function(response){
            waitingDialog.hide();
            if(response.status){
            // $('.overlay').addClass('d-none');
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
            // $('.overlay').addClass('d-none');
            $.gritter.add({
                title: 'Error!',
                text: response.message,
                class_name: 'gritter-error',
                time: 1000,
            });
        });
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
        order: [[ 7, "asc" ]],
        ajax: {
            url: "{{route('maintanance.read')}}",
            type: "GET",
            data:function(data){
                var vehicle_name      = $('input[name=vehicle_name]').val();
                var vehicle_no        = $('input[name=vehicle_no]').val();
                var vehicle_category  = $('input[name=vehicle_category]').val();
                var vendor            = $('input[name=vendor]').val();
                var driver            = $('input[name=driver]').val();
                var date_from         = $('input[name=date_from]').val();
                var date_to           = $('input[name=date_to]').val();
                data.vehicle_name     = vehicle_name;
                data.vehicle_no       = vehicle_no;
                data.vehicle_category = vehicle_category;
                data.vendor           = vendor;
                data.driver           = driver;
                data.date_from        = date_from;
                data.date_to          = date_to;
                
            }
        },
        columnDefs:[
            {
                orderable: false,targets:[0]
            },
            { className: "text-right", targets: [0,5] },
            { className: "text-center", targets: [6,3,6] },
            {
			render: function (data, type, row) {
					return `<a onclick="showDocument(this)" data-url="${row.link}" href="#"><span class="badge badge-success">Prview</span><a/>`
			},
			targets: [6]
			},
            {
                render: function (data, type, row) {
                    if (row.status == 1) {
                        return `<span class="badge badge-info">Publish</span>`
                    } else {
                        return `<span class="badge badge-warning">Draft</span>`
                    }
                },
                targets: [7]
            },
            { 
                render: function ( data, type, row ) {
                    if(row.status == 1){
                        return `<div class="dropdown">
                                <button class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                                    <i class="fa fa-bars"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right">
                                    <li><a class="dropdown-item" href="{{url('admin/maintenance')}}/${row.id}"><i class="fas fa-info mr-2"></i> Detail</a></li>
                                </ul></div>`
                       
                    }else{
                         return `<div class="dropdown">
                                <button class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                                    <i class="fa fa-bars"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right">
                                    <li><a class="dropdown-item" href="{{url('admin/maintenance')}}/${row.id}"><i class="fas fa-info mr-2"></i> Detail</a></li>
                                    <li><a class="dropdown-item" href="{{url('admin/maintenance')}}/${row.id}/edit"><i class="fas fa-pencil-alt mr-2"></i> Edit</a></li>
                                    <li><a class="dropdown-item delete" href="#" data-id="${row.id}"><i class="fas fa-trash mr-2"></i> Delete</a></li>
                                </ul></div>`
                    }
                
            },targets: [8]
            }
        ],
        columns: [
            { data: "no" },
            { data: "vehicle" },
            { data: "date"},
            { data: "km"},
            { data: "driver"},
            { data: "total"},
            { data: "image"},
            { data: "status"},
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
					className: `btn-{{ config('configs.app_theme') }}`
				},
				cancel: {
					label: '<i class="fa fa-undo"></i>',
					className: 'btn-default'
				},
			},
			title:'Delete Maintenance?',
			message:'Data that has been deleted cannot be recovered',
			callback: function(result) {
					if(result) {
						var data = {
                            _token: "{{ csrf_token() }}"
                        };
						$.ajax({
							url: `{{url('admin/maintenance')}}/${id}`,
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
})
</script>
@endpush