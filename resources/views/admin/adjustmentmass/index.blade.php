@extends('admin.layouts.app')

@section('title', __('adjustmentmass.adjmass'))
@section('stylesheets')
<link href="{{ asset('adminlte/component/dataTables/css/datatables.min.css') }}" rel="stylesheet">
<link href="{{asset('adminlte/component/daterangepicker/daterangepicker.css')}}" rel="stylesheet">
@endsection
@if (!strpos(url()->previous(), 'attendanceapproval'))
{{ Session::forget('name') }}
{{ Session::forget('date') }}
@endif
@push('breadcrump')
<li class="breadcrumb-item active">{{ __('adjustmentmass.adjmass') }}</li>
@endpush

@section('content')
<div class="wrapper wrapper-content">
  <div class="row">
    <div class="col-lg-12">
      <div class="card card-{{ config('configs.app_theme') }} card-outline">
        <form id="form" action="{{ route('attendanceapproval.approve') }}" class="form-horizontal" method="post" autocomplete="off">
          {{ csrf_field() }}
          {{-- Title, Button Approve & Search --}}
          <div class="card-header">
            <h3 class="card-title">{{ __('adjustmentmass.adjmass') }}</h3>
            <div class="pull-right card-tools">
              <a href="#" onclick="updatemass()" class="btn btn-{{ config('configs.app_theme') }} btn-sm"><i class="fas fa-pencil-alt"></i></a>
            </div>
          </div>
          {{-- .Title, Button Approve & Search --}}
          <div class="card-body">

            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label" for="name">{{ __('employee.empname') }}</label>
                  <select name="name" id="name" class="form-control select2" style="width: 100%" aria-hidden="true">
                    <option value="">All</option>
                    @foreach ($employees as $employee)
                    <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                    @endforeach
                  </select>
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
                  <select name="department" id="department" class="form-control select2" style="width: 100%" aria-hidden="true" multiple>
                    @foreach ($departments as $department)
                    <option value="{{ $department->name }}">{{ $department->path }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label" for="workgroup">{{ __('workgroupcombination.workcomb') }}</label>
                  <select name="workgroup" id="workgroup" class="form-control select2" style="width: 100%" aria-hidden="true" multiple>
                    @foreach ($workgroups as $workgroup)
                    <option value="{{ $workgroup->id }}">{{ $workgroup->name }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label" for="overtime">{{ __('attendanceapproval.overtime') }}</label>
                  <input type="text" name="overtime" id="overtime" class="form-control" placeholder="{{ __('attendanceapproval.overtime') }}">
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label" for="shift_workingtime">Shift</label>
                  <select name="shift_workingtime" id="shift_workingtime" class="form-control select2" style="width: 100%" aria-hidden="true" multiple data-placeholder="Select Shift">
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
              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label" for="status">Status</label>
                  <select name="status" id="status" class="form-control select2" style="width: 100%" data-placeholder="Select Status" aria-hidden="true">
                    {{-- <option value=""></option> --}}
                    <option value="1" selected>Already Approval</option>
                    <option value="0">Waiting Approval</option>
                  </select>
                </div>
              </div>
            </div>
            <table class="table table-striped table-bordered datatable" style="width: 100%">
              <thead>
                <tr>
                  <th width="10">No</th>
                  <th width="50">{{ __('general.date') }}</th>
                  <th width="100">{{ __('attendanceapproval.workshif') }}</th>
                  <th width="100">{{ __('employee.employ') }}</th>
                  <th width="50">First In</th>
                  <th width="50">Last Out</th>
                  <th width="10">Summary</th>
                  <th width="50">Status</th>
                  <th width="10" class="text-center"><input type="checkbox" name="check_all" id="check_all"></th>
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
  <div class="modal fade" id="update-mass" tabindex="-1" role="dialog" aria-hidden="true" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">{{ __('adjustmentmass.adjwtot') }}</h4>
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        </div>
        <div class="modal-body">
          <form id="form-updatemass" action="{{ route('adjustmentmass.updatemass') }}" class="form-horizontal no-gutters" method="post" autocomplete="off">
            {{ csrf_field() }}
            <input type="hidden" name="workingtime_id" id="workingtime_id">
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label class="control-label" for="working_time">{{ __('attendanceapproved.worktime') }}</label>
                  <input type="number" class="form-control" name="working_time" id="working_time" placeholder="{{ __('attendanceapproved.worktime') }}">
                </div>
              </div>
              <div class="col-md-12">
                <div class="form-group">
                  <label class="control-label" for="over_time">{{ __('attendanceapproval.overtime') }}</label>
                  <input type="number" class="form-control" name="over_time" id="over_time" placeholder="{{ __('attendanceapproval.overtime') }}">
                </div>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" onclick="submitUpdatemass()" class="btn btn-{{ config('configs.app_theme') }}" title="{{ __('general.save') }}"><i class="fa fa-save"></i></button>
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

  function updatemass(){
    var approve = [];
    $("#form input[name='approve[]']:checked").each(function(key, value) {
      var item	= {
        id:               $(this).val(),
        get_working_time: $(this).parents('tr').find("input[name='get_working_time[]']").val(),
        get_over_time:    $(this).parents('tr').find("input[name='get_over_time[]']").val(),
      };
      approve.push(item);
    });
    if (approve.length > 0) {
      $('#update-mass').modal('show');
    } else {
      $.gritter.add({
        title: 'Warning!',
        text: 'Please check at least one data to adjustment mass.',
        class_name: 'gritter-warning',
        time: 1000,
      });
    }
  }
  function submitUpdatemass(){
    var approve = [];
    $("#form input[name='approve[]']:checked").each(function(key, value) {
      var item	= {
        id:               $(this).val(),
        get_working_time: $(this).parents('tr').find("input[name='get_working_time[]']").val(),
        get_over_time:    $(this).parents('tr').find("input[name='get_over_time[]']").val(),
      };
      approve.push(item);
    });
      $.ajax({
          url: "{{route('adjustmentmass.updatemass')}}",
          type: "POST",
          dataType:'json',
          data: {
            _token: "{{ csrf_token() }}",
              approve: approve,
              working_time: $('#form-updatemass').find("input[name='working_time']").val(),
              over_time: $('#form-updatemass').find("input[name='over_time']").val(),
          },
          success: function(response) {
            $('.overlay').removeClass('d-none');
            if(response.status){
            $('.overlay').addClass('d-none');
            $.gritter.add({
              title: 'Success!',
              text: response.message,
              class_name: 'gritter-success',
              time: 1000,
            });
            $('#update-mass').modal('hide');
            dataTable.ajax.reload( null, false );
          } else {
            $.gritter.add({
              title: 'Warning!',
              text: response.message,
              class_name: 'gritter-warning',
              time: 1000,
            });
          }
          }
      });
    if (approve.length > 0) {
    } else {
      $.gritter.add({
        title: 'Warning!',
        text: 'Please check at least one data to adjustment mass.',
        class_name: 'gritter-warning',
        time: 1000,
      });
    }
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
    $('.select2').select2();
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
    //stateSave:true,
    processing: true,
    serverSide: true,
    filter:false,
    info:false,
    lengthChange:false,
    responsive: true,
    paginate:false,
    language: {
      url: language_choosen == 'id' ? urlLocaleId : ''
    },
    deferLoading: 0,
      order: [[ 1, "asc" ]],
      ajax: {
          url: "{{route('adjustmentmass.read')}}",
          type: "GET",
          data:function(data){
              var employee_id = $('select[name=name]').val();
              var nid = $('input[name=nid]').val();
              var department = $('select[name=department]').val();
              var workgroup = $('select[name=workgroup]').val();
              var overtime = $('input[name=overtime]').val();
              var workingtime = $('select[name=shift_workingtime]').val();
              var checkincheckout = $('select[name=checkincheckout]').val();
              var status = $('select[name=status]').val();
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
              data.status = status;
          }
      },
      columnDefs:[
          {
              orderable: false,targets:[0,7,8]
          },
          { className: "text-center", targets: [0,1,7,8] },
          { render: function ( data, type, row ) {
            return `${row.attendance_date} / <span class="text-bold">${row.day ? row.day : ''}</span>`;
          },targets: [1]
          },
          { render: function ( data, type, row ) {
            return `<span class="text-blue">${row.description ? row.description : '-'}</span>`;
          },targets: [2]
          },
          { render: function ( data, type, row ) {
            return `${row.name}<br>${row.nid}`;
          },targets: [3]
          },
          { render: function ( data, type, row ) {
            if (row.attendance_in) {
              if (row.attendance_in < row.start_time) {
                return `<span class="text-blue">${row.attendance_in}</span><br><span class="text-bold">${formatTime(row.start_time)}</span><br><span class="text-success text-bold">- ${row.diff_in}</span>`
              } else {
                return `<span class="text-blue">${row.attendance_in}</span><br><span class="text-bold">${formatTime(row.start_time)}</span><br><span class="text-danger text-bold">+ ${row.diff_in}</span>`
              }
            } else {
              return '-';
            }
          },targets: [4]
          },
          { render: function ( data, type, row ) {
            if (row.attendance_out) {
              if (row.attendance_out > row.finish_time) {
                return `<span class="text-blue">${row.attendance_out}</span><br><span class="text-bold">${formatTime(row.finish_time)}</span><br><span class="text-success text-bold">- ${row.diff_out}</span>`
              } else {
                return `<span class="text-blue">${row.attendance_out}</span><br><span class="text-bold">${formatTime(row.finish_time)}</span><br><span class="text-danger text-bold">+ ${row.diff_out}</span>`
              }
            } else {
              return '-';
            }
          },targets: [5]
          },
          { render: function ( data, type, row ) {
              return `WT: ${row.adj_working_time} Hours<br>OT: ${row.adj_over_time} Hours
                      <input type="hidden" name="get_working_time[]" value="${row.adj_working_time} ">
                      <input type="hidden" name="get_over_time[]" value="${row.adj_over_time} ">`
          },targets: [6]
          },
          { render: function ( data, type, row ) {
            if (row.status == 0) {
              return '<span class="badge badge-warning">Waiting Approval</span>'
            } else {
              return '<span class="badge badge-success">Already Approval</span>'
            }
          },targets: [7]
          },
          { render: function ( data, type, row ) {
              return `<input type="checkbox" class="checkbox" name="approve[]" id="check_${row.id}" data-id="${row.id}" value="${row.id}">`
            },targets: [8]
          }
      ],
      columns: [
          { data: "no", className: "align-middle text-center" },
          { data: "attendance_date", className: "align-middle text-center" },
          { data: "description", className: "shift align-middle text-center" },
          { data: "name", className: "align-middle text-left"},
          { data: "attendance_in", className: "time_in text-center" },
          { data: "attendance_out", className: "time_out text-center" },
          { data: "adj_working_time", className: "worktime align-middle text-center" },
          { data: "status", className: "align-middle text-center" },
          { data: "status", className: "align-middle text-center" }
      ]
  });
  
  $(document).on('change', '#name', function() {
    dataTable.draw();
  });
  $(document).on('keyup', '#nid', function() {
    dataTable.draw();
  });
  $(document).on('change', '#department', function() {
    dataTable.draw();
  });
  $(document).on('change', '#checkincheckout', function() {
      dataTable.draw();
  });
  $(document).on('change', '#shift_workingtime', function() {
    // alert('aaaa');
    dataTable.draw();
  });
  $(document).on('change', '#workgroup', function() {
    dataTable.draw();
  });
  $(document).on('keyup', '#overtime', function() {
    dataTable.draw();
  });
  $(document).on('change', '#status', function() {
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
    $('#form-search').submit(function(e){
      e.preventDefault();
      dataTable.draw();
      $('#add-filter').modal('hide');
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
  });
</script>
@endpush