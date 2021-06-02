@extends('admin.layouts.app')

@section('title', __('attendanceapproval.atapprov'))
@section('stylesheets')
<link href="{{ asset('adminlte/component/dataTables/css/datatables.min.css') }}" rel="stylesheet">
<link href="{{asset('adminlte/component/daterangepicker/daterangepicker.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/jquery-ui/jquery-ui.min.css')}}" rel="stylesheet">
<style>
  .shift:hover {
    cursor: pointer;
  }

  .scheme:hover {
    cursor: pointer;
  }

  .time_in:hover {
    cursor: pointer;
  }

  .time_out:hover {
    cursor: pointer;
  }

  .worktime:hover {
    cursor: pointer;
  }

  table.dataTable tbody td {
    height: 60px;
  }

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
@if (!strpos(url()->previous(), 'attendanceapproval'))
{{ Session::forget('name') }}
{{ Session::forget('date') }}
@endif
@push('breadcrump')
<li class="breadcrumb-item active">{{ __('attendanceapproval.atapprov') }}</li>
@endpush

@section('content')
<div class="wrapper wrapper-content">
  <div class="row">
    <div class="col-lg-12">
      <div class="card card-{{ config('configs.app_theme') }} card-outline">
        <form id="form" action="{{ route('attendanceapproval.approve') }}" class="form-horizontal" method="post">
          {{ csrf_field() }}
          {{-- Title, Button Approve & Search --}}
          <div class="card-header">
            <h3 class="card-title">{{ __('attendanceapproval.atapprov') }}</h3>
            <div class="pull-right card-tools">
              <button form="form" type="submit" class="btn btn-sm btn-{{ config('configs.app_theme')}}"><i class="fa fa-check"></i></button>
              <a href="#" onclick="deletemass()" class="btn btn-{{ config('configs.app_theme') }} btn-sm"><i class="fa fa-trash"></i></a>
              <a href="#" onclick="exportdata()" class="btn btn-primary btn-sm text-white"><i class="fa fa-download"></i></a>
            </div>
          </div>
          {{-- .Title, Button Approve & Search --}}
          <div class="card-body">
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label" for="name">{{ __('employee.empname') }}</label>
                  <input type="text" class="form-control" id="employee_id" placeholder="{{ __('employee.empname') }}" name="employee_id">
                  <div id="employee-container"></div>
                  {{-- <select name="name" id="name" class="form-control select2" style="width: 100%" aria-hidden="true" data-placeholder="Employee Name">
                    <option value=""></option>
                    @foreach ($employees as $employee)
                    <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                  @endforeach
                  </select> --}}
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label" for="nid">NIK</label>
                  <input type="text" name="nid" id="nid" class="form-control" placeholder="NIK">
                </div>
              </div>
              <div class="form-row col-md-4">
                <div class="form-group col-md-6">
                  <label for="from">{{ __('general.from') }}</label>
                  <input type="text" class="form-control datepicker" id="from" placeholder="{{ __('general.from') }}" name="from">
                </div>
                <div class="form-group col-md-6">
                  <label for="to">{{ __('general.To') }}</label>
                  <input type="text" class="form-control datepicker" id="to" placeholder="{{ __('general.To') }}" name="to">
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label" for="department">{{ __('department.dep') }}</label>
                  <select name="department" id="department" class="form-control select2" style="width: 100%" aria-hidden="true" multiple data-placeholder="{{ __('general.chs') }} {{ __('department.dep') }}">
                    @foreach ($departments as $department)
                    <option value="{{ $department->name }}">{{ $department->path }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label" for="workgroup">{{ __('workgroupcombination.workcomb') }}</label>
                  <select name="workgroup" id="workgroup" class="form-control select2" style="width: 100%" aria-hidden="true" multiple data-placeholder="{{ __('general.chs') }} {{ __('workgroupcombination.workcomb') }}">
                    @foreach ($workgroups as $workgroup)
                    <option value="{{ $workgroup->id }}">{{ $workgroup->name }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label" for="overtime">{{ __('attendanceapproval.overtime') }}</label>
                  <input type="text" class="form-control" id="overtime" placeholder="{{ __('attendanceapproval.overtime') }}" name="overtime">
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label" for="shift_workingtime">Shift</label>
                  <select name="shift_workingtime" id="shift_workingtime" class="form-control select2" style="width: 100%" aria-hidden="true" multiple data-placeholder="{{ __('general.chs') }} Shift">
                    @foreach ($workingtimes as $workingtime)
                    <option value="{{ $workingtime->id }}">{{ $workingtime->description }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label" for="checkincheckout">Check In Status</label>
                  <select name="checkincheckout" id="checkincheckout" class="form-control select2" style="width: 100%" aria-hidden="true" data-placeholder="Select Check In Status">
                    <option value=""></option>
                    <option value="checkin">Check In Only</option>
                    <option value="checkout">Check Out Only</option>
                    <option value="checkin_checkout">Check In and Check Out</option>
                    <option value="!checkin_checkout">No Check In and Check Out</option>
                  </select>
                </div>
              </div>
            </div>
            <table class="table table-striped table-bordered datatable" style="width: 100%">
              <thead>
                <tr>
                  <th width="10">No</th>
                  <th width="50">{{ __('general.date') }}</th>
                  <th width="50">{{ __('attendanceapproval.scheme') }}</th>
                  <th width="50">{{ __('department.dep') }}<br>{{ __('position.pos') }}</th>
                  <th width="50">{{ __('workgroup.workgrp') }}</th>
                  <th width="100">{{ __('employee.employ') }}</th>
                  <th width="50">{{ __('attendanceapproval.workshif') }}</th>
                  <th width="10">Check In</th>
                  <th width="10">Check Out</th>
                  <th width="10">Summary</th>
                  <th width="50">Status</th>
                  <th width="10" class="text-center"><input type="checkbox" name="check_all" id="check_all">
                  </th>
                  <th width="10">{{ __('general.act') }}</th>
                </tr>
              </thead>
            </table>
          </div>
          <div class="overlay">
            <i class="fa fa-refresh fa-spin"></i>
          </div>
        </form>
      </div>
    </div>
  </div>
  <div class="modal fade" id="edit-scheme" tabindex="-1" role="dialog" aria-hidden="true" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">{{ __('general.edt') }} {{ __('overtimescheme.otschem') }}</h4>
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        </div>
        <div class="modal-body">
          <form id="form-scheme" action="{{ route('attendanceapproval.quickupdate') }}" class="form-horizontal no-gutters" method="post" autocomplete="off">
            {{ csrf_field() }}
            <input type="hidden" name="scheme_id" id="scheme_id">
            <input type="hidden" name="type_action" value="approval">
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label class="control-label" for="scheme">{{ __('overtimescheme.otschem') }}</label>
                  <input type="text" class="form-control" name="scheme" id="scheme" placeholder="{{ __('overtimescheme.otschem') }}">
                </div>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button form="form-scheme" type="submit" class="btn btn-{{ config('configs.app_theme') }}" title="Apply"><i class="fa fa-save"></i></button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="edit-shift" tabindex="-1" role="dialog" aria-hidden="true" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">{{ __('general.edt') }} {{ __('attendanceapproval.workshif') }}</h4>
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        </div>
        <div class="modal-body">
          <form id="form-shift" action="{{ route('attendanceapproval.quickupdate') }}" class="form-horizontal no-gutters" method="post" autocomplete="off">
            {{ csrf_field() }}
            <input type="hidden" name="attendance_id" id="attendance_id">
            <input type="hidden" name="type_action" value="approval">
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label class="control-label" for="working_shift">{{ __('attendanceapproval.workshif') }}</label>
                  <input type="text" class="form-control" name="working_shift" id="working_shift" placeholder="{{ __('attendanceapproval.workshif') }}">
                </div>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button form="form-shift" type="submit" class="btn btn-{{ config('configs.app_theme') }}" title="{{ __('general.save') }}"><i class="fa fa-save"></i></button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="edit-in" tabindex="-1" role="dialog" aria-hidden="true" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Change First In</h4>
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        </div>
        <div class="modal-body">
          <form id="form-in" action="{{ route('attendanceapproval.quickupdate') }}" class="form-horizontal no-gutters" method="post" autocomplete="off">
            {{ csrf_field() }}
            <input type="hidden" name="first_in_id" id="first_in_id">
            <input type="hidden" name="type_action" value="approval">
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label class="control-label" for="first_in">First In</label>
                  <input type="text" class="form-control timepicker" name="first_in" id="first_in" placeholder="First In">
                </div>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button form="form-in" type="submit" class="btn btn-{{ config('configs.app_theme') }}" title="Apply"><i class="fa fa-save"></i></button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="edit-out" tabindex="-1" role="dialog" aria-hidden="true" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Change Last Out</h4>
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        </div>
        <div class="modal-body">
          <form id="form-out" action="{{ route('attendanceapproval.quickupdate') }}" class="form-horizontal no-gutters" method="post" autocomplete="off">
            {{ csrf_field() }}
            <input type="hidden" name="first_out_id" id="first_out_id">
            <input type="hidden" name="type_action" value="approval">
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label class="control-label" for="last_out">Last Out</label>
                  <input type="text" class="form-control timepicker" name="last_out" id="last_out" placeholder="Last Out">
                </div>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button form="form-out" type="submit" class="btn btn-{{ config('configs.app_theme') }}" title="Apply"><i class="fa fa-save"></i></button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="edit-worktime" tabindex="-1" role="dialog" aria-hidden="true" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">{{ __('general.edt') }} {{ __('attendanceapproved.worktime') }} & {{ __('attendanceapproval.overtime') }}</h4>
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        </div>
        <div class="modal-body">
          <form id="form-worktime" action="{{ route('attendanceapproval.quickupdate') }}" class="form-horizontal no-gutters" method="post" autocomplete="off">
            {{ csrf_field() }}
            <input type="hidden" name="workingtime_id" id="workingtime_id">
            <input type="hidden" name="type_action" value="approval">
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label class="control-label" for="working_time">{{ __('attendanceapproved.worktime') }}</label>
                  <input type="number" class="form-control" name="working_time" id="working_time" placeholder="{{ __('attendanceapproved.worktime') }}" value="0">
                </div>
              </div>
              <div class="col-md-12">
                <div class="form-group">
                  <label class="control-label" for="over_time">{{ __('attendanceapproval.overtime') }}</label>
                  <input type="number" class="form-control" name="over_time" id="over_time" placeholder="{{ __('attendanceapproval.overtime') }}" value="0">
                </div>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button form="form-worktime" type="submit" class="btn btn-{{ config('configs.app_theme') }}" title="{{ __('general.save') }}"><i class="fa fa-save"></i></button>
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
<script src="{{asset('adminlte/component/jquery-ui/jquery-ui.min.js')}}"></script>
<script type="text/javascript">
  exportdata = () => {
    $.ajax({
      url: "{{ route('attendanceapproval.export') }}",
      type: "POST",
      dataType: "JSON",
      data: $("#form").serialize(),
      beforeSend: function() {
        waitingDialog.show();
      }
    }).done(function(response) {
      waitingDialog.hide();
      if (response.status) {
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
      } else {
        $.gritter.add({
          title: 'Warning!',
          text: response.message,
          class_name: 'gritter-warning',
          time: 1000,
        });
      }
    }).fail(function(response) {
      waitingDialog.hide();
      var response = response.responseJSON;
      $.gritter.add({
          title: 'Error!',
          text: response.message,
          class_name: 'gritter-error',
          time: 1000,
      });
    });
  }
  function filter(){
		$('#add-filter').modal('show');
		$('.select2').select2();

  }

  function formatTime(date) {
    var now = new Date(), year = now.getFullYear();
    var d = new Date(year + ' ' + date),
            minute = '' + d.getMinutes(),
            hour = '' + d.getHours();
    
    if (minute.length < 2)
      minute = '0' + minute;
    if (hour.length < 2)
      hour = '0' + hour;

    return [hour, minute].join(':');
  }

  function dayName(date) {
    var weekday = ["Sun","Mon","Tue","Wed","Thu","Fri","Sat"];
    var date = new Date(date);

    return weekday[date.getDay()];
  }

  function deletemass() {
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
			title:'Delete attendance?',
			message:'Data that has been deleted cannot be recovered',
			callback: function(result) {
				if(result) {
					var data = {
						_token: "{{ csrf_token() }}"
					};
					$.ajax({
						url: `{{ route('attendanceapproval.deletemass') }}`,
						dataType: 'json',
						data: new FormData($('#form')[0]),
            processData: false,
            contentType: false,
            method:'post',
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
						$('.overlay').addClass('d-none');
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
  }
  $(document).ready(function() {
    $('.select2').select2({
      allowClear: true
    });
    $("#check_all").click(function(){
      var checked = $(this).is(':checked');
      if(checked){
        $(".checkbox").each(function(){
          $(this).prop("checked",true);
        });
      }else{
        $(".checkbox").each(function(){
          $(this).prop("checked",false);
        });
      }
    });

    $('.checkbox').change(function(){ 
      if(false == $(this).prop("checked")){
        $("#check_all").prop('checked', false);
      }
      if ($('.checkbox:checked').length == $('.checkbox').length ){
        $("#check_all").prop('checked', true);
      }
    });
  });
  $(function() {
    $('#from').daterangepicker({
      autoUpdateInput: false,
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
      processing: true,
      serverSide: true,
      stateSave: true,
      filter:false,
      info:false,
      lengthChange:true,
      responsive: true,
      order: [[ 1, "asc" ]],
      language: {
        url: language_choosen == 'id' ? urlLocaleId : '',
      },
      lengthMenu: [ 100, 250, 500, 1000, 2000 ],
      ajax: {
          url: "{{route('attendanceapproval.read')}}",
          type: "GET",
          data:function(data){
              var employee_id = $('input[name=employee_id]').val();
              var nid = $('input[name=nid]').val();
              var department = $('select[name=department]').val();
              var workgroup = $('select[name=workgroup]').val();
              var workingtime = $('select[name=shift_workingtime]').val();
              var checkincheckout = $('select[name=checkincheckout]').val();
              var overtime = $('input[name=overtime]').val();
              var from = $('input[name=from]').val();
              var to = $('input[name=to]').val();
              data.employee_id = employee_id;
              data.nid = nid;
              data.from = from;
              data.to = to;
              data.department = department;
              data.workgroup = workgroup;
              data.overtime = overtime;
              data.workingtime = workingtime;
              data.checkincheckout = checkincheckout;
          }
      },
      columnDefs:[
          {
              orderable: false,targets:[0,10,11]
          },
          { className: "text-center", targets: [0,1,7,8,9] },
          { render: function ( data, type, row ) {
            var date = new Date(row.attendance_date);
            return `${row.attendance_date} <br> <span class="text-bold ${row.day == 'Off' ? 'text-red' : ''}">${dayName(row.attendance_date)}</span>`;
          },targets: [1]
          },
          { render: function ( data, type, row ) {
            return `<span class="text-blue">${row.scheme_name ? row.scheme_name : '-'}</span>`;
          },targets: [2]
          },
          { render: function ( data, type, row ) {
            return `<span>${row.department_name} <br> ${row.title_name}</span>`;
          },targets: [3]
          },
          { render: function ( data, type, row ) {
            return `${row.name}<br>${row.nid}`;
          },targets: [5]
          },
          { render: function ( data, type, row ) {
            return `<span class="text-blue">${row.description ? row.description : '-'}</span>`;
          },targets: [6]
          },
          { render: function ( data, type, row ) {
            if (row.attendance_in) {
              if (row.attendance_in < row.start_time) {
                return `<span class="text-blue">${row.attendance_in}</span><br><span class="text-bold">${formatTime(row.start_time)}</span><br><span class="text-success text-bold">- ${row.diff_in}</span>`
              } else {
                return `<span class="text-blue">${row.attendance_in}</span><br><span class="text-bold">${formatTime(row.start_time)}</span><br><span class="text-danger text-bold">+ ${row.diff_in}</span>`
              }
            } else {
              return '<span class="text-red text-bold">?</span>';
            }
          },targets: [7]
          },
          { render: function ( data, type, row ) {
            if (row.attendance_out) {
              if (row.attendance_out > row.finish_time) {
                return `<span class="text-blue">${row.attendance_out}</span><br><span class="text-bold">${formatTime(row.finish_time)}</span><br><span class="text-danger text-bold">+ ${row.diff_out}</span>`
              } else {
                return `<span class="text-blue">${row.attendance_out}</span><br><span class="text-bold">${formatTime(row.finish_time)}</span><br><span class="text-success text-bold">- ${row.diff_out}</span>`
              }
            } else {
              return '<span class="text-red text-bold">?</span>';
            }
          },targets: [8]
          },
          { render: function ( data, type, row ) {
              return `WT: ${row.adj_working_time} Hours<br>OT: ${row.adj_over_time} Hours`
          },targets: [9]
          },
          { render: function ( data, type, row ) {
            if (row.status == 0) {
              return '<span class="badge badge-warning">Waiting Approval</span>'
            } else {
              return '<span class="badge badge-success">Already Approval</span>'
            }
          },targets: [10]
          },
          { render: function ( data, type, row ) {
            if (row.status == 0) {
              return `<input type="checkbox" class="checkbox" name="approve[]" id="check_${row.id}" data-id="${row.id}" value="${row.id}">`
            } else {
              return `<span class="badge badge-success"><i class="fa fa-check"></i></span>`
            }
            },targets: [11]
          },
          { render: function ( data, type, row ) {
              return `<div class="dropdown">
                      <button class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                          <i class="fa fa-bars"></i>
                      </button>
                      <ul class="dropdown-menu dropdown-menu-right">
                          <li><a class="dropdown-item" href="{{url('admin/attendanceapproval')}}/${row.id}/detail"><i class="fa fa-search"></i> Detail</a></li>
                      </ul></div>`
          },targets: [12]
          }
      ],
      columns: [
          { data: "no", className: "align-middle text-center" },
          { data: "attendance_date", className: "align-middle text-center" },
          { data: "scheme_name", className: "scheme align-middle text-center" },
          { data: "department_name", className: "align-middle text-center" },
          { data: "workgroup_name", className: "align-middle text-center" },
          { data: "name", className: "align-middle text-left"},
          { data: "description", className: "shift align-middle text-center" },
          { data: "attendance_in", className: "time_in align-middle text-center" },
          { data: "attendance_out", className: "time_out align-middle text-center"},
          { data: "adj_working_time", className: "worktime align-middle text-center" },
          { data: "status", className: "align-middle text-center" },
          { data: "status", className: "align-middle text-center" },
          { data: "id", className: "align-middle text-center" }
      ]
    });
    $('#shift_filter').select2({
      ajax: {
        url: "{{route('workingtime.select')}}",
        type:'GET',
        dataType: 'json',
        data: function (term,page) {
          return {
            name:term,
            page:page,
            limit:30,
          };
        },
        results: function (data,page) {
          var more = (page * 30) < data.total;
          var option = [];
          $.each(data.rows,function(index,item){
            option.push({
              id:item.id,
              text: `${item.description}`
            });
          });
          return {
            results: option, more: more,
          };
        },
      },
      allowClear: true,
    });
    $('#working_shift').select2({
      ajax: {
        url: "{{route('attendanceapproval.selectworkingtime')}}",
        type:'GET',
        dataType: 'json',
        data: function (term,page) {
          return {
            name:term,
            page:page,
            limit:30,
          };
        },
        results: function (data,page) {
          var more = (page * 30) < data.total;
          var option = [];
          $.each(data.rows,function(index,item){
            option.push({
              id:item.id,
              text: `${item.description}`
            });
          });
          return {
            results: option, more: more,
          };
        },
      },
      allowClear: true,
    });
    $('#scheme').select2({
      ajax: {
        url: "{{route('attendanceapproval.selectscheme')}}",
        type:'GET',
        dataType: 'json',
        data: function (term,page) {
          return {
            name:term,
            page:page,
            limit:30,
          };
        },
        results: function (data,page) {
          var more = (page * 30) < data.total;
          var option = [];
          $.each(data.rows,function(index,item){
            option.push({
              id:item.id,
              text: `${item.scheme_name}`
            });
          });
          return {
            results: option, more: more,
          };
        },
      },
      allowClear: true,
    });
    // $('#department').select2({
    //   ajax: {
    //     url: "{{route('department.select')}}",
    //     type:'GET',
    //     dataType: 'json',
    //     data: function (term,page) {
    //       return {
    //         name:term,
    //         page:page,
    //         limit:30,
    //       };
    //     },
    //     results: function (data,page) {
    //       var more = (page * 30) < data.total;
    //       var option = [];
    //       $.each(data.rows,function(index,item){
    //         option.push({
    //           id:item.name,
    //           text: `${item.path}`
    //         });
    //       });
    //       return {
    //         results: option, more: more,
    //       };
    //     },
    //   },
    //   allowClear: true,
    //   multiple: true
    // });
    // $('#workgroup').select2({
    //   ajax: {
    //     url: "{{route('workgroup.select')}}",
    //     type:'GET',
    //     dataType: 'json',
    //     data: function (term,page) {
    //       return {
    //         name:term,
    //         page:page,
    //         limit:30,
    //       };
    //     },
    //     results: function (data,page) {
    //       var more = (page * 30) < data.total;
    //       var option = [];
    //       $.each(data.rows,function(index,item){
    //         option.push({
    //           id:item.id,
    //           text: `${item.name}`
    //         });
    //       });
    //       return {
    //         results: option, more: more,
    //       };
    //     },
    //   },
    //   allowClear: true,
    //   multiple: true
    // });
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
			$( "input[name=nid]" ).autocomplete({
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
			$("input[name=nid]").keydown(function(event){
				if(event.keyCode == 13) {
					event.preventDefault();
					$('input[name=nid]').autocomplete('close');
					return false;
				}
		});
    $(document).on('keyup', '#employee_id', function() {
      dataTable.draw();
    });
		});
    $(document).on('keyup', '#nid', function() {
      dataTable.draw();
    });
    $(document).on('change', '#department', function() {
      dataTable.draw();
    });
    $(document).on('change', '#workgroup', function() {
      dataTable.draw();
    });
    $(document).on('change', '#shift_workingtime', function() {
      dataTable.draw();
    });
    $(document).on('change', '#checkincheckout', function() {
      dataTable.draw();
    });
    $(document).on('keyup', '#overtime', function() {
      dataTable.draw();
    });
    $(document).on('apply.daterangepicker', function() {
      dataTable.draw();
    });
    $('.timepicker').daterangepicker({
			singleDatePicker: true,
			timePicker: true,
			timePicker24Hour: true,
			timePickerIncrement: 1,
			timePickerSeconds: false,
			locale: {
				format: 'HH:mm'
			}
		}).on('show.daterangepicker', function(ev, picker) {
			picker.container.find('.calendar-table').hide();
    });
    $('.datatable').on('click', '.scheme', function() {
      var data = dataTable.row(this).data();
      // console.log(data);
      if (data) {
        $('#edit-scheme').modal('show');
        $('#form-scheme input[name=scheme_id]').attr('value', data.id);
        $(document).on("change", "#scheme", function () {
          if (!$.isEmptyObject($('#form-scheme').validate().submitted)) {
            $('#form-scheme').validate().form();
          }
        });
      }
    });
    $('.datatable').on('click', '.shift', function() {
      var data = dataTable.row(this).data();
      if (data) {
        $('#edit-shift').modal('show');
        $('#form-shift input[name=attendance_id]').attr('value', data.id);
        $("#working_shift").select2('data',{id:data.workingtime_id,text:data.description}).trigger('change');
        $(document).on("change", "#working_shift", function () {
          if (!$.isEmptyObject($('#form-shift').validate().submitted)) {
            $('#form-shift').validate().form();
          }
        });
      }
    });
    $('.datatable').on('click', '.time_in', function() {
      var data = dataTable.row(this).data();
      if (data) {  
        if (!data.attendance_in) {
          $('#edit-in').modal('show');
          $('#form-in input[name=first_in_id]').attr('value', data.id);
          $('#form-in input[name=first_in]').daterangepicker({
            startDate: moment(data.attendance_date),
            singleDatePicker: true,
            timePicker: true,
            timePicker24Hour: true,
            timePickerSeconds: true,
            timePickerIncrement: 1,
            locale: {
              format: 'MM/DD/YYYY HH:mm:ss'
            }
          });
        } else {
          $('#edit-in').modal('show');
          $('#form-in input[name=first_in_id]').attr('value', data.id);
          $('#form-in input[name=first_in]').daterangepicker({
            startDate: moment(data.time_in),
            singleDatePicker: true,
            timePicker: true,
            timePicker24Hour: true,
            timePickerSeconds: true,
            timePickerIncrement: 1,
            locale: {
              format: 'MM/DD/YYYY HH:mm:ss'
            }
          });
        }
      }
    });
    $('.datatable').on('click', '.time_out', function() {
      var data = dataTable.row(this).data();
      if (data) {  
        if (!data.attendance_out) {
          $('#edit-out').modal('show');
          $('#form-out input[name=first_out_id]').attr('value', data.id);
          $('#form-out input[name=last_out]').daterangepicker({
            startDate: moment(data.attendance_date),
            singleDatePicker: true,
            timePicker: true,
            timePicker24Hour: true,
            timePickerSeconds: true,
            timePickerIncrement: 1,
            locale: {
              format: 'MM/DD/YYYY HH:mm:ss'
            }
          });
        } else {
          $('#edit-out').modal('show');
          $('#form-out input[name=first_out_id]').attr('value', data.id);
          $('#form-out input[name=last_out]').daterangepicker({
            startDate: moment(data.time_out),
            singleDatePicker: true,
            timePicker: true,
            timePicker24Hour: true,
            timePickerSeconds: true,
            timePickerIncrement: 1,
            locale: {
              format: 'MM/DD/YYYY HH:mm:ss'
            }
          });
        }
      }
    });
    $('.datatable').on('click', '.worktime', function() {
      var data = dataTable.row(this).data();
      if (data) {
        $('#edit-worktime').modal('show');
        $('#form-worktime input[name=workingtime_id]').attr('value', data.id);
        $('#form-worktime input[name=working_time]').attr('value', data.adj_working_time);
        $('#form-worktime input[name=over_time]').attr('value', data.adj_over_time);
      }
    });
    $("#form-scheme").validate({
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
          url:$('#form-scheme').attr('action'),
          method:'post',
          data: new FormData($('#form-scheme')[0]),
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
                $('#edit-scheme').modal('hide');
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
    $("#form-shift").validate({
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
          url:$('#form-shift').attr('action'),
          method:'post',
          data: new FormData($('#form-shift')[0]),
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
                $('#edit-shift').modal('hide');
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
    $("#form-in").validate({
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
          url:$('#form-in').attr('action'),
          method:'post',
          data: new FormData($('#form-in')[0]),
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
                $('#edit-in').modal('hide');
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
    $("#form-out").validate({
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
          url:$('#form-out').attr('action'),
          method:'post',
          data: new FormData($('#form-out')[0]),
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
                $('#edit-out').modal('hide');
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
    $("#form-worktime").validate({
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
          url:$('#form-worktime').attr('action'),
          method:'post',
          data: new FormData($('#form-worktime')[0]),
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
                $('#edit-worktime').modal('hide');
                $('#form-worktime input[name=workingtime_id]').attr('value', '');
                $('#form-worktime input[name=working_time]').attr('value', '0');
                $('#form-worktime input[name=over_time]').attr('value', '0');
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
    $("#form").validate({
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
          url:$('#form').attr('action'),
          method:'post',
          data: new FormData($('#form')[0]),
          processData: false,
          contentType: false,
          dataType: 'json',
          beforeSend:function(){
              $('.overlay').removeClass('d-none');
          }
        }).done(function(response){
              $('.overlay').addClass('d-none');
              $("#check_all").prop('checked', false);
              if(response.status){
                dataTable.draw();
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
    dataTable.on('page.dt', function() {
      $('html, body').animate({
        scrollTop: $(".dataTables_wrapper").offset().top
      }, 'slow');
    });
  });
</script>
@endpush