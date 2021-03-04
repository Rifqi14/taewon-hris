@extends('admin.layouts.app')

@section('title', 'Daily Report')
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/css/fileinput.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/daterangepicker/daterangepicker.css')}}" rel="stylesheet">
@endsection

@push('breadcrump')
<li class="breadcrumb-item active"><a href="{{route('dailyreport.index')}}">Daily Report</a></li>
<li class="breadcrumb-item active">Details</li>
@endpush

@section('content')
<div class="row p-3">
  <div class="col-lg-8">
    <div class="row">
      <div class="col-lg-12">
        <div class="card card-{{ config('configs.app_theme') }} card-outline">
          <div class="card-header">
            <h3 class="card-title">Employee Data</h3>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-sm-6">
                <div class="form-group">
                  <label>Employee Name</label>
                  <input type="text" class="form-control" placeholder="Name" id="name" name="name" readonly
                    value="{{ $attendances->employee->name }}">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label>Absence ID</label>
                  <input type="text" class="form-control" placeholder="Absence ID" id="id" name="id" readonly
                    value="{{ $attendances->id }}">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-6">
                <div class="form-group">
                  <label>Position</label>
                  <input type="text" class="form-control" placeholder="Position" id="position" name="position" readonly
                    value="{{ $attendances->employee->title->name }}">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label>Date</label>
                  <input type="text" class="form-control" placeholder="Date" id="date" name="date" readonly
                    value="{{ changeDateFormat('d-m-Y', $attendances->attendance_date) }}">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-6">
                <div class="form-group">
                  <label>Working Shift Type</label>
                  <input type="text" class="form-control" placeholder="Wokring Shift Type" id="working_type"
                    name="working_type" readonly value="{{ $attendances->workingtime->working_time_type }}">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label>Working Time</label>
                  <input type="text" class="form-control" placeholder="Working Time" id="working_time"
                    name="working_time" readonly value="{{ $attendances->workingtime->description }}">
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-12">
        <div class="card card-{{ config('configs.app_theme') }} card-outline">
          <div class="card-header">
            <h3 class="card-title">Attendance History</h3>
          </div>
          <div class="card-body">
            <table class="table table-striped table-bordered datatable" id="table_attendance_history"
              style="width: 100%">
              <thead>
                <tr>
                  <th width="10">No</th>
                  <th width="100">Time</th>
                  <th width="100">Type</th>
                  <th width="100">Machine ID</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="col-lg-12">
      <div class="card card-{{ config('configs.app_theme') }} card-outline">
        <div class="card-header">
          <h3 class="card-title">Other</h3>
          <div class="pull-right card-tools">
            <a href="javascript:void(0)" onclick="backurl()" class="btn btn-default btn-sm">
              <i class="fa fa-reply"></i>
            </a>
          </div>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-sm-6">
              <div class="form-group">
                <label>First In</label>
                <input type="text" class="form-control" placeholder="Fisrt In" id="first_in" name="first_in" readonly
                  value="{{ changeDateFormat('H:i:s', $attendances->attendance_in) }}">
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <label>Last Out</label>
                <input type="text" class="form-control" placeholder="Last Out" id="last_out" name="last_out" readonly
                  value="{{ changeDateFormat('H:i:s', $attendances->attendance_out) }}">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-6">
              <div class="form-group">
                <label>Working Time</label>
                <input type="text" class="form-control" placeholder="Working Time" id="work_time" name="work_time"
                  readonly value="{{ $attendances->adj_working_time }}">
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <label>Adj Working Time</label>
                <input type="text" class="form-control" placeholder="Adjustment Working Time" id="adj_working_time"
                  name="adj_working_time" readonly value="{{ $attendances->adj_working_time }}">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-6">
              <div class="form-group">
                <label>Over Time</label>
                <input type="text" class="form-control" placeholder="Over Time" id="over_time" name="over_time" readonly
                  value="{{ $attendances->adj_over_time }}">
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <label>Adj Over Time</label>
                <input type="text" class="form-control" placeholder="Adjustment Over Time" id="adj_over_time"
                  name="adj_over_time" readonly value="{{ $attendances->adj_over_time }}">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-12">
              <div class="form-group">
                <label>Notes</label>
                <textarea class="form-control" id="note" name="note" readonly placeholder="Notes">{{ $attendances->note }}</textarea>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
<script src="{{asset('adminlte/component/dataTables/js/datatables.min.js')}}"></script>
<script src="{{asset('adminlte/component/daterangepicker/moment.min.js')}}"></script>
<script src="{{asset('adminlte/component/daterangepicker/daterangepicker.js')}}"></script>
<script type="text/javascript">
  $(document).ready(function(){
		$('.select2').select2();
  });
  $(function() {
    dataTable = $('#table_attendance_history').DataTable({
      stateSave:true,
      processing: true,
      serverSide: true,
      filter:false,
      info:false,
      lengthChange:true,
      responsive: true,
      order: [[ 1, "asc" ]],
      ajax: {
          url: "{{route('attendanceapproval.attendance_log')}}",
          type: "GET",
          data:function(data){
            var id = {{ $attendances->id }};
            data.id = id;
          }
      },
      columnDefs:[
          {
              orderable: false,targets:[0]
          },
          { className: "text-center", targets: [0,2] },
          { render: function(data, type, row) {
            if (row.type == 1) {
              return '<span class="badge badge-success">Scan In</span>';
            } else {
              return '<span class="badge badge-danger">Scan Out</span>';
            }
          }, targets:[2]},
      ],
      columns: [
          { data: "no" },
          { data: "attendance_date" },
          { data: "type" },
          { data: "device_name" },
      ]
    });
  });
</script>
@endpush