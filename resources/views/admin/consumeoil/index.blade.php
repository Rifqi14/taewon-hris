@extends('admin.layouts.app')

@section('title',__('consumeoil.consume'))
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/daterangepicker/daterangepicker.css')}}" rel="stylesheet">
@endsection

@push('breadcrump')
<li class="breadcrumb-item active">{{__('consumeoil.consume')}}</li>
@endpush

@section('content')
<div class="wrapper wrapper-content">
    <div class="row">
        <div class="col-lg-12">
            <div class="card ">
                <div class="card-header">
                    <h3 class="card-title">{{__('consumeoil.conslist')}}</h3>
                    <!-- tools box -->
                    <div class="pull-right card-tools">
                        <a href="{{route('consumeoil.create')}}"
                            class="btn btn-{{ config('configs.app_theme') }} btn-sm text-white" data-toggle="tooltip"
                            title="{{__('general.crt')}}">
                            <i class="fa fa-plus"></i>
                        </a>
                    </div>
                    <!-- /. tools -->
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                            <label class="control-label" for="vehicle">{{__('consumeoil.vehicle')}}</label>
                            <input type="text" name="vehicle" id="vehicle" class="form-control filter" placeholder="{{__('consumeoil.vehicle')}}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                            <label class="control-label" for="license_no">{{__('consumeoil.vehicno')}}</label>
                            <input type="text" name="license_no" id="" class="form-control filter" placeholder="{{__('consumeoil.vehicno')}}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                            <label class="control-label" for="driver">{{__('consumeoil.driver')}}</label>
                            <input type="text" name="driver" id="driver" class="form-control filter" placeholder="{{__('consumeoil.driver')}}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                            <label class="control-label" for="oil">Oil</label>
                            <input type="text" name="oil" id="oil" class="form-control filter" placeholder="Oil">
                            </div>
                        </div>
                        
                        <div class="form-row col-md-4">
                            <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label" for="date_from">{{__('general.from')}}</label>
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
                            <label class="control-label" for="date_to">{{__('asset.to')}}</label>
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
                                <th width="100">{{__('consumeoil.vehicle')}}</th>
                                <th width="100">{{__('consumeoil.driver')}}</th>
                                <th width="100">Oil</th>
                                <th width="50">{{__('consumeoil.consume')}}</th>
                                <th width="50">{{__('consumeoil.usedtype')}}</th>
                                <th width="50">Km</th>
                                <th width="50">{{__('general.date')}}</th>
                                <th width="50">Status</th>
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

@endsection

@push('scripts')
<script src="{{asset('adminlte/component/dataTables/js/datatables.min.js')}}"></script>
<script src="{{asset('adminlte/component/daterangepicker/moment.min.js')}}"></script>
<script src="{{asset('adminlte/component/daterangepicker/daterangepicker.js')}}"></script>
<script type="text/javascript">
    
$(function(){
    
    dataTable = $('.datatable').DataTable( {
        stateSave:true,
        processing: true,
        serverSide: true,
        filter:false,
        info:false,
        lengthChange:true,
        responsive: true,
        order: [[ 8, "asc" ]],
        ajax: {
            url: "{{route('consumeoil.read')}}",
            type: "GET",
            data:function(data){
                data.oil = $('input[name=oil]').val();
                data.date_from = $('input[name=date_from]').val();
                data.date_to = $('input[name=date_to]').val();
                data.vehicle = $('input[name=vehicle]').val();
                data.license_no = $('input[name=license_no]').val();
                data.driver = $('input[name=driver]').val();
            }
        },
        columnDefs:[
            {
                orderable: false,targets:[0]
            },
            { className: "text-right", targets: [0,4,6] },
            { className: "text-center", targets: [7,8,9] },
            {
                render: function (data, type, row) {
                    return `${row.vehicle}<br>${row.license_no}<br>${row.vehicle_type}`
                },
                targets: [1]
            },
            {
                render: function (data, type, row) {
                    if (row.status == 1) {
                        return `<span class="badge badge-info">Publish</span>`
                    } else {
                        return `<span class="badge badge-warning">Draft</span>`
                    }
                },
                targets: [8]
            },
            { 
                render: function ( data, type, row ) {
                    if(row.status == 1){
                        return `<div class="dropdown">
                                <button class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                                    <i class="fa fa-bars"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right">
                                    <li><a class="dropdown-item" href="{{url('admin/consumeoil')}}/${row.id}"><i class="fas fa-info mr-2"></i> Detail</a></li>
                                </ul></div>`
                       
                    }else{
                         return `<div class="dropdown">
                                <button class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                                    <i class="fa fa-bars"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right">
                                    <li><a class="dropdown-item" href="{{url('admin/consumeoil')}}/${row.id}"><i class="fas fa-info mr-2"></i> Detail</a></li>
                                    <li><a class="dropdown-item" href="{{url('admin/consumeoil')}}/${row.id}/edit"><i class="fas fa-pencil-alt mr-2"></i> Edit</a></li>
                                    <li><a class="dropdown-item delete" href="#" data-id="${row.id}"><i class="fas fa-trash mr-2"></i> Delete</a></li>
                                </ul></div>`
                    }
                
            },targets: [9]
            }
        ],
        columns: [
            { 
                data: "no" 
            },
            { 
                data: "vehicle" 
            },
            { 
                data: "driver" 
            },
            { 
                data: "oil" 
            },
            { data:"engine_oil"},
            { data:"type"},
            { data: "km"},
            { data: "date"},
            { data:"status"},
            { 
                data: "id" 
            },
        ]
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
    $(".select2").select2();
    
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
			title:'Delete Consume Oil?',
			message:'Data that has been deleted cannot be recovered',
			callback: function(result) {
					if(result) {
						var data = {
                            _token: "{{ csrf_token() }}"
                        };
						$.ajax({
							url: `{{url('admin/consumeoil')}}/${id}`,
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