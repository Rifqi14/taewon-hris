@extends('admin.layouts.app')

@section('title',__('activity.aclog'))
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
<li class="breadcrumb-item active">{{__('activity.aclog')}}</li>
@endpush

@section('content')
<div class="wrapper wrapper-content">
  <div class="row">
    <div class="col-lg-12">
      <div class="card card-{{ config('configs.app_theme') }} card-outline">
        {{-- Title, Button Approve & Search --}}
        <div class="card-header">
          <h3 class="card-title">{{__('activity.listaclog')}}</h3>
        </div>
        {{-- .Title, Button Approve & Search --}}
        <div class="card-body">
          <div class="form-row">
            <div class="form-group col-md-4">
              <label for="user_id">{{__('activity.user')}}</label>
              <input type="text" class="form-control" id="user_id" placeholder="{{__('activity.user')}}" name="user_id">
              <div id="user-container"></div>
            </div>
            <div class="form-group col-md-4">
              <label for="page_id">{{__('activity.page')}}</label>
              <input type="text" class="form-control" id="page_id" placeholder="{{__('activity.page')}}" name="page_id">
              <div id="page-container"></div>
            </div>
            <div class="form-group col-md-4">
              <label for="employee_id">{{__('employee.empname')}}</label>
              <input type="text" class="form-control" id="employee_id" placeholder="{{__('employee.empname')}}" name="employee_id">
              <div id="employee-container"></div>
            </div>
            <div class="form-group col-md-4">
              <label for="department_id">{{__('department.dep')}}</label>
              <input type="text" class="form-control" id="department_id" placeholder="{{__('department.dep')}}" name="department_id">
              <div id="department-container"></div>
            </div>
            <div class="form-group col-md-4">
              <label for="activity_id">{{__('activity.activity')}}</label>
              <input type="text" class="form-control" id="activity_id" placeholder="{{__('activity.activity')}}" name="activity_id">
              <div id="activity-container"></div>
            </div>
            <div class="form-row col-md-4">
              <div class="form-group col-md-6">
                <label for="from">{{__('activity.from')}}</label>
                <input type="text" class="form-control datepicker" id="from" placeholder="{{__('activity.from')}}" name="from">
              </div>
              <div class="form-group col-md-6">
                <label for="to">{{__('activity.to')}}</label>
                <input type="text" class="form-control datepicker" id="to" placeholder="{{__('activity.to')}}" name="to">
              </div>
            </div>
            <div class="form-group col-md-4">
<<<<<<< HEAD
              <label for="detail_id">{{__('general.dtl')}}</label>
              <input type="text" class="form-control" id="detail_id" placeholder="{{__('general.dtl')}}" name="detail_id">
              <div id="detail-container"></div>
=======
              <label for="detail_id">Detail</label>
              <select name="detail_id" id="detail_id" class="form-control select2" style="width: 100%" aria-hidden="true" multiple>
                @foreach ($details as $detail)
                <option value="{{ $detail->detail }}">{{ $detail->detail }}</option>
                @endforeach
              </select>
>>>>>>> dba140484448f1daf08bb97a37a747f7373590ad
            </div>
          </div>
          <table class="table table-striped table-bordered datatable" style="width: 100%">
            <thead>
              <tr>
                <th width="5">No</th>
                <th width="100">{{__('general.date')}}</th>
                <th width="100">{{__('activity.user')}}</th>
                <th width="100">{{__('activity.page')}}</th>
                <th width="100">{{__('employee.empname')}}</th>
                <th width="100">{{__('department.dep')}}</th>
                <th width="100">{{__('activity.activity')}}</th>
                <th width="100">{{__('general.dtl')}}</th>
                <th width="2">{{__('activity.rslt')}}</th>
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
        url: "{{route('loghistory.read')}}",
        type: "GET",
        data:function(data){
          var employee_id = $('select[name=employee_id]').val();
          var user_id = $('select[name=user_id]').val();
          var page_id = $('select[name=page_id]').val();
          var activity_id = $('select[name=activity_id]').val();
          var detail_id = $('select[name=detail_id]').val();
          var department_id = $('select[name=department_id]').val();
          var from = $('input[name=from]').val();
          var to = $('input[name=to]').val();
          data.employee_id = employee_id;
          data.user_id = user_id;
          data.page_id = page_id;
          data.department_id = department_id;
          data.detail_id = detail_id;
          data.activity_id = activity_id;
          data.from = from;
          data.to = to;
        }
      },
      columnDefs:[
        { orderable: false,targets:[0] },
        { className: "text-center", targets: [0,1,3,4,5,6] },
        {
          targets: [7],
        },
      ],
      columns: [
        { data: "no" },
        { data: "date" },
        { data: "user_name" },
        { data: "page" },
        { data: "name" },
        { data: "department_name" },
        { data: "activity" },
        { data: "detail"},
        { data: "result"},
      ]
    });
    $(document).on('change', '#user_id', function() {
      dataTable.draw();
    });
    $(document).on('change', '#page_id', function() {
      dataTable.draw();
    });
    $(document).on('change', '#employee_id', function() {
      dataTable.draw();
    });
    $(document).on('change', '#department_id', function() {
      dataTable.draw();
    });
    $(document).on('change', '#activity_id', function() {
      dataTable.draw();
    });
    $(document).on('change', '#detail_id', function() {
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