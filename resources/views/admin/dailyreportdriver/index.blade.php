@extends('admin.layouts.app')
@section('title', 'Daily Report Driver')
@section('stylesheets')
<link href="{{ asset('adminlte/component/dataTables/css/datatables.min.css') }}" rel="stylesheet">
<link href="{{asset('adminlte/component/daterangepicker/daterangepicker.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/jquery-ui/jquery-ui.min.css')}}" rel="stylesheet">
<style type="text/css">
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
<li class="breadcrumb-item active">Daily Report Driver</li>
@endpush

@section('content')
<div class="wrapper wrapper-content">
  <div class="row">
    <div class="col-lg-12">
      <div class="card card-{{ config('configs.app_theme') }} card-outline">
        <div class="card-header">
          <h3 class="card-title">Daily Report Driver List</h3>
          <div class="pull-right card-tools">
            <a href="{{route('dailyreportdriver.create')}}"
              class="btn btn-{{ config('configs.app_theme') }} btn-sm text-white" data-toggle="tooltip"
              title="Add Data">
              <i class="fa fa-plus"></i>
            </a>
          </div>
        </div>
        <div class="card-body">
          <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label" for="driver_id">Driver Name</label>
                  <input type="text" name="driver_id" id="driver_id" class="form-control filter" placeholder="Searching for">
                  {{-- <select name="driver_id" id="driver_id" class="form-control select2 filter" style="width: 100%" aria-hidden="true" data-placeholder="Driver Name">
                      <option value=""></option>
                      @foreach ($employees as $employee)
                      <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                      @endforeach
                  </select> --}}
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label" for="police_no">Police No</label>
                  {{-- <input type="text" name="police_no" id="police_no" class="form-control filter" placeholder="Police No"> --}}
                  <select name="police_no" id="police_no" class="form-control select2 filter" style="width: 100%" aria-hidden="true" multiple  data-placeholder="Police No">
                    <option value=""></option>
                    @foreach ($daily_report as $police)
                    <option value="{{ $police->police_no }}">{{ $police->police_no }}</option>
                    @endforeach
                  </select>
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
              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label" for="exp_passenger">Expedition/Passanger</label>
                  {{-- <input type="text" name="exp_passenger" id="exp_passenger" class="form-control filter" placeholder="Expedition/Passanger"> --}}
                  <select name="exp_passenger" id="exp_passenger" class="form-control select2 filter" style="width: 100%" aria-hidden="true" multiple  data-placeholder="Expedition/Passanger">
                    <option value=""></option>
                    @foreach ($passengers as $passenger)
                    <option value="{{ $passenger->exp_passenger }}">{{ $passenger->exp_passenger }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
            </div>
          <table class="table table-striped table-bordered datatable" style="width: 100%">
            <thead>
              <tr>
                <th width="10">No</th>
                <th width="100">Date</th>
                <th width="100">Driver</th>
                <th width="100">Police No</th>
                <th width="100" class="text-right">Grand Total</th>
                <th width="100" class="text-right">Total KM</th>
                <th width="100">Last Arrival Time</th>
                <th width="100">Expedition/ Passenger</th>
                <th width="10">Action</th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="{{asset('js/accounting/accounting.min.js')}}"></script>
<script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
<script src="{{asset('adminlte/component/dataTables/js/datatables.min.js')}}"></script>
<script src="{{asset('assets/js/plugins/bootbox/bootbox.min.js')}}"></script>
<script src="{{asset('adminlte/component/daterangepicker/moment.min.js')}}"></script>
<script src="{{asset('adminlte/component/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('adminlte/component/jquery-mask/jquery.mask.min.js')}}"></script>
<script src="{{asset('adminlte/component/jquery-ui/jquery-ui.min.js')}}"></script>
<script type="text/javascript">
  function filter() {
    $('#add-filter').modal('show');
  }
  $(function() {
    $('.select2').select2({
      allowClear: true
    });
    $('.datepicker').daterangepicker({
      singleDatePicker: true,
      timePicker: false,
      timePickerIncrement: 1,
      locale: {
      format: 'DD/MM/YYYY'
      }
    });
    $('#form-search').submit(function(e){
			e.preventDefault();
			dataTable.draw();
			$('#add-filter').modal('hide');
		});
    dataTable = $('.datatable').DataTable({
      stateSave:true,
      processing: true,
      serverSide: true,
      filter:false,
      info:false,
      lengthChange:true,
      responsive: true,
      order: [[ 1, "asc" ]],
      lengthMenu: [ 100, 250, 500, 1000, 2000 ],
      pageLength: 1000,
      ajax: {
          url: "{{route('dailyreportdriver.read')}}",
          type: "GET",
          data:function(data){
            data.driver_id = $('input[name=driver_id]').val();
            data.police_no = $('select[name=police_no]').val();
            data.exp_passenger   = $('select[name=exp_passenger]').val();
            data.date_from = $('input[name=date_from]').val();
            data.date_to   = $('input[name=date_to]').val();
          }
      },
      columnDefs: [
        {orderable: false, targets: [0]},
        {className: "text-center", targets: [0,6]},
        {className: "text-right", targets: [4,5]},
        { render: function ( data, type, row ) {
                        var grandtotal = accounting.formatMoney(data,'',',','.');
                        return `${grandtotal}`
				},targets: [4]
				},
        { render: function(data, type, row) {
            return `<a class="edit" href="#" data-id="${row.id}">${row.driver_name}</a><br>${row.nid}`;
        }, targets:[2]},
        { render: function(data, type, row) {
            return `${row.total_km}`;
        }, targets:[2]},
        { render: function ( data, type, row ) {
          return `<div class="dropdown">
                      <button class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                          <i class="fa fa-bars"></i>
                      </button>
                      <ul class="dropdown-menu dropdown-menu-right">
                          <li><a class="dropdown-item edit" href="#" data-id="${row.id}"><i class="fas fa-pencil-alt mr-2"></i> Edit</a></li>
                          <li><a class="dropdown-item delete" href="#" data-id="${row.id}"><i class="fas fa-trash mr-2"></i> Delete</a></li>
                      </ul>
                  </div>`
          },targets: [8]
        }
      ],
      columns: [
        { data: "no" },
        { data: "date" },
        { data: "driver_name" },
        { data: "police_no" },
        { data: "grandtotal" },
        { data: "total_km" },
        { data: "last_arrival" },
        { data: "exp_passenger" },
        { data: "id" },
      ]
    });
    $(document).ready(function(){
			var employees = [
				@foreach($employees as $employee)
                	"{!!$employee->name!!}",
            	@endforeach
			];
			$( "input[name=driver_id]" ).autocomplete({
			source: employees,
			minLength:0,
			appendTo: '#driver-container',
			select: function(event, response) {
				if(event.preventDefault(), 0 !== response.item.id){
					$(this).val(response.item.value);
					dataTable.draw();
				}
			}
			}).focus(function () {
				$(this).autocomplete("search");
			});
			$("input[name=driver_id]").keydown(function(event){
				if(event.keyCode == 13) {
					event.preventDefault();
					$('input[name=driver_id]').autocomplete('close');
					return false;
				}
			});
      $(document).on('change keyup keydown keypress focus', '.filter', function() {
      dataTable.draw();
    });
		});
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
        title:'Delete driver allowance?',
        message:'Data that has been deleted cannot be recovered',
        callback: function(result) {
            if(result) {
              var data = { _token: "{{ csrf_token() }}" };
              $.ajax({
                url: `{{url('admin/dailyreportdriver')}}/${id}`,
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
                } else {
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
              });
            }
        }
      });
    });
    $(document).on('click','.edit',function(){
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
        title:'Edit Daily Report Driver?',
        message:'You will be redirect to daily report driver edit page, are you sure?',
        callback: function(result) {
            if(result) {
              document.location = "{{url('admin/dailyreportdriver')}}/"+id+"/edit";
            }
        }
      });
    });
  });
</script>
@endpush