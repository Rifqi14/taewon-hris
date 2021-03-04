@extends('admin.layouts.app')

@section('title', 'Attendance Log')
@section('stylesheets')
<link href="{{ asset('adminlte/component/dataTables/css/datatables.min.css') }}" rel="stylesheet">
<link href="{{asset('adminlte/component/daterangepicker/daterangepicker.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/jquery-ui/jquery-ui.min.css')}}" rel="stylesheet">
<style type="text/css">
  .ui-state-active {
    background: #28a745 !important;
    border-color: #28a745 !important;
  }

  .ui-menu {
    overflow: auto;
    height: 200px;
  }
</style>
@endsection

@push('breadcrump')
<li class="breadcrumb-item active">Attendance Log</li>
@endpush

@section('content')
<div class="wrapper wrapper-content">
  <div class="row">
    <div class="col-lg-12">
      <div class="card card-{{ config('configs.app_theme') }} card-outline">
        {{-- Title, Button Approve & Search --}}
        <div class="card-header">
          <h3 class="card-title">List Attendance</h3>
          <div class="pull-right card-tools">
            <a href="{{route('attendance.import')}}" class="btn btn-{{ config('configs.app_theme') }} btn-sm" data-toggle="tooltip" title="Import">
              <i class="fa fa-file-import"></i>
            </a>
          </div>
        </div>
        {{-- .Title, Button Approve & Search --}}
        <div class="card-body">
          <div class="form-row">
            <div class="form-group col-md-4">
              <label for="nik">NIK</label>
              <input type="text" class="form-control" id="nik" placeholder="NIK" name="nik">
            </div>
            <div class="employee-container"></div>
            <div class="form-group col-md-4">
              <label for="employee_id">Employee Name</label>
              <input type="text" class="form-control" id="employee_id" placeholder="Employee Name" name="employee_id">
              <div id="employee-container"></div>
            </div>
            <div class="form-group col-md-4">
              <label for="working_group">Working Group Type</label>
              <select name="working_group" id="working_group" class="form-control select2" style="width: 100%" aria-hidden="true" multiple data-placeholder="Select Working Group">
                <option value="Shift">Shift</option>
                <option value="Non-Shift">Non Shift</option>
              </select>
            </div>
            <div class="form-group col-md-4">
              <label for="status">Status</label>
              <select name="status" id="status" class="form-control select2" multiple style="width: 100%" aria-hidden="true" data-placeholder="Select Status">
                <option value=""></option>
                <option value="1">Scan In</option>
                <option value="0">Scan Out</option>
              </select>
            </div>
            <div class="form-row col-md-4">
              <div class="form-group col-md-6">
                <label for="from">From</label>
                <input type="text" class="form-control datepicker" id="from" placeholder="From" name="from">
              </div>
              <div class="form-group col-md-6">
                <label for="to">To</label>
                <input type="text" class="form-control datepicker" id="to" placeholder="To" name="to">
              </div>
            </div>
          </div>
          <table class="table table-striped table-bordered datatable" style="width: 100%">
            <thead>
              <tr>
                <th width="5">No</th>
                <th width="5">Date Time</th>
                <th width="150">Employee Name</th>
                <th width="5">Machine ID</th>
                <th width="80">Working Group Type</th>
                <th width="10">Working Time</th>
                <th width="2">Status</th>
                <th width="100">Batch Upload</th>
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
<script src="{{asset('adminlte/component/dataTables/js/datatables.min.js')}}"></script>
<script src="{{asset('adminlte/component/daterangepicker/moment.min.js')}}"></script>
<script src="{{asset('adminlte/component/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('adminlte/component/jquery-ui/jquery-ui.min.js')}}"></script>
<script type="text/javascript">
  $(function() {
    $('.select2').select2();
    $('#from').daterangepicker({
      startDate: moment().startOf('month'),
      singleDatePicker: true,
      timePicker: false,
      timePickerIncrement: 30,
      locale: {
        format: 'MM/DD/YYYY'
      }
    }, function(chosen_date) {
      $('#from').val(chosen_date.format('MM/DD/YYYY'));
      dataTable.draw();
    });
    $('#to').daterangepicker({
      startDate: moment().endOf('month'),
      singleDatePicker: true,
      timePicker: false,
      timePickerIncrement: 30,
      locale: {
      format: 'MM/DD/YYYY'
      }
    }, function(chosen_date) {
      $('#to').val(chosen_date.format('MM/DD/YYYY'));
      dataTable.draw();
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
      ajax: {
        url: "{{route('attendance.read')}}",
        type: "GET",
        data:function(data){
          var employee = $('input[name=employee_id]').val();
          var nik = $('input[name=nik]').val();
          var working_group = $('select[name=working_group]').val();
          var status = $('select[name=status]').val();
          var from = $('input[name=from]').val();
          var to = $('input[name=to]').val();
          data.employee = employee;
          data.nik = nik;
          data.working_group = working_group;
          data.status = status;
          data.from = from;
          data.to = to;
        }
      },
      columnDefs:[
        { orderable: false,targets:[0] },
        { className: "text-center", targets: [0,1,3,4,5,6] },
        {
          targets: [7],
          visible: false,
        },
        { render: function(data, type, row) {
            return `${row.name}<br>${row.nid}`;
        }, targets:[2]},
        { render: function(data, type, row) {
          if (data == 1) {
            return '<span class="badge badge-success">Scan In</span>';
          } else {
            return '<span class="badge badge-danger">Scan Out</span>';
          }
        }, targets:[6]}
      ],
      columns: [
        { data: "no" },
        { data: "attendance_date" },
        { data: "name" },
        { data: "device_name" },
        { data: "working_group" },
        { data: "description" },
        { data: "type" },
        { data: "batch_upload"},
      ]
    });
    $(document).ready(function(){
			var employees = [
				@foreach($employees as $employee)
                	"{!!$employee->name!!}",
            	@endforeach
			];
			$( "input[name=employee_id]" ).autocomplete({
			source: employees,
			minLength:0,
			appendTo: '#employee-container',
			select: function(event, response) {
				if(event.preventDefault(), 0 !== response.item.id){
					$(this).val(response.item.value);
					dataTable.draw();
				}
			}
			}).focus(function () {
				$(this).autocomplete("search");
			});
			$("input[name=employee_id]").keydown(function(event){
				if(event.keyCode == 13) {
					event.preventDefault();
					$('input[name=employee_id]').autocomplete('close');
					return false;
				}
			});
      var employees = [
				@foreach($employees as $nik)
                	"{!!$nik->nid!!}",
            	@endforeach
			];
			$( "input[name=nik]" ).autocomplete({
			source: employees,
			minLength:0,
			appendTo: '#employee-container',
			select: function(event, response) {
				if(event.preventDefault(), 0 !== response.item.id){
					$(this).val(response.item.value);
					dataTable.draw();
				}
			}
			}).focus(function () {
				$(this).autocomplete("search");
			});
			$("input[name=nik]").keydown(function(event){
				if(event.keyCode == 13) {
					event.preventDefault();
					$('input[name=nik]').autocomplete('close');
					return false;
				}
			});
    $(document).on('keyup', '#nik', function() {
      dataTable.draw();
    });
		});
    $(document).on('change', '#working_group', function() {
      dataTable.draw();
    });
    $(document).on('change', '#status', function() {
      dataTable.draw();
    });
    $(document).on('keyup', '#employee_id', function() {
      dataTable.draw();
    });
    $(document).on('apply.daterangepicker', function() {
      dataTable.draw();
    }).trigger('apply.daterangepicker');
  });
  $(window).keyup(function(event) {
    console.log(event.keyCode == 119);
    if (event.keyCode == 119) {
 
      // Get the column API object
      var column = dataTable.column(7);

      // Toggle the visibility
      column.visible( ! column.visible() );
    }
  });
</script>
@endpush