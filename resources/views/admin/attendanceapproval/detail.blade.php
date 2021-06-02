@extends('admin.layouts.app')

@section('title', __('attendanceapproval.atapprov'))
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/css/fileinput.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/daterangepicker/daterangepicker.css')}}" rel="stylesheet">
@endsection

@push('breadcrump')
<li class="breadcrumb-item active"><a href="{{route('attendanceapproval.index')}}">{{ __('attendanceapproval.atapprov') }}</a></li>
<li class="breadcrumb-item active">Detail</li>
@endpush

@section('content')
<form id="form" action="{{ route('attendanceapproval.update',['id'=>$attendances->id]) }}" class="form-horizontal" method="post" autocomplete="off">
  <div class="row p-3">
    <div class="col-lg-8">
      <div class="row">
        {{ csrf_field() }}
        <input type="hidden" value="{{ $attendances->code_case }}">
        <input type="hidden" name="_method" value="put">
        <div class="col-lg-12">
          <div class="card card-{{ config('configs.app_theme') }} card-outline">
            <div class="card-header">
              <h3 class="card-title">{{ __('employee.empname') }} Data</h3>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-sm-6">
                  <div class="form-group">
                    <label>{{ __('employee.empname') }}</label>
                    <input type="text" class="form-control" placeholder="{{ __('employee.empname') }}" id="name" name="name" readonly value="{{ $attendances->name }}">
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="form-group">
                    <label>Absence ID</label>
                    <input type="text" class="form-control" placeholder="Absence ID" id="id" name="id" readonly value="{{ $attendances->id }}">
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-6">
                  <div class="form-group">
                    <label>{{ __('position.pos') }}</label>
                    <input type="text" class="form-control" placeholder="{{ __('position.pos') }}" id="position" name="position" readonly value="{{ $attendances->position }}">
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="form-group">
                    <label>{{ __('general.date') }}</label>
                    <input type="text" class="form-control" placeholder="{{ __('general.date') }}" id="date" name="date" readonly value="{{ changeDateFormat('d-m-Y', $attendances->attendance_date) }}">
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-6">
                  <div class="form-group">
                    <label>{{ __('attendancelog.worktype') }}</label>
                    <input type="text" class="form-control" placeholder="{{ __('attendancelog.worktype') }}" id="working_type" name="working_type" readonly value="{{ $attendances->working_group }}">
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="form-group">
                    <label>{{ __('attendancelog.worktime') }}</label>
                    <input type="text" class="form-control" placeholder="{{ __('attendancelog.worktime') }}" id="working_time" name="working_time" value="{{ $attendances->description }}">
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-12">
          <div class="card card-{{ config('configs.app_theme') }} card-outline">
            <div class="card-header">
              <h3 class="card-title">{{ __('attendanceapproval.history') }}</h3>
              <div class="pull-right card-tools">
                <a href="#" class="btn btn-{{ config('configs.app_theme')}} btn-sm text-white add_history">
                  <i class="fa fa-plus"></i>
                </a>
              </div>
            </div>
            <div class="card-body">
              <table class="table table-striped table-bordered datatable" id="table_attendance_history" style="width: 100%">
                <thead>
                  <tr>
                    <th width="10">No</th>
                    <th width="100">{{ __('attendancelog.time') }}</th>
                    <th width="100">{{ __('general.type') }}</th>
                    <th width="100">{{ __('machine.machine') }}</th>
                    <th width="5">{{ __('general.act') }}</th>
                  </tr>
                </thead>
              </table>
            </div>
            <div class="overlay d-none" id="overlay-history">
              <i class="fa fa-2x fa-sync-alt fa-spin"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="col-lg-12">
        <div class="card card-{{ config('configs.app_theme') }} card-outline">
          <div class="card-header">
            <h3 class="card-title">{{ __('general.other') }}</h3>
            <div class="pull-right card-tools">
              <button form="form" type="submit" class="btn btn-sm btn-{{ config('configs.app_theme')}} text-white" title="{{ __('general.add') }}"><i class="fa fa-save"></i></button>
              <a href="#" onClick="backurl()" class="btn btn-sm btn-default" title="{{ __('general.prvious') }}"><i class="fa fa-reply"></i></a>
            </div>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-sm-6">
                <div class="form-group">
                  <label>First In</label>
                  <input type="text" class="form-control" placeholder="Fisrt In" id="first_in" name="first_in" readonly value="{{ $attendances->attendance_in }}">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label>Last Out</label>
                  <input type="text" class="form-control" placeholder="Last Out" id="last_out" name="last_out" readonly value="{{ $attendances->attendance_out }}">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-6">
                <div class="form-group">
                  <label>{{ __('attendanceapproved.worktime') }}</label>
                  <input type="text" class="form-control" placeholder="{{ __('attendanceapproved.worktime') }}" id="work_time" name="work_time" readonly value="{{ $attendances->adj_working_time }}">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label>{{ __('attendanceapproval.adjwt') }}</label>
                  <input type="text" class="form-control" placeholder="{{ __('attendanceapproval.adjwt') }}" id="adj_working_time" name="adj_working_time" required value="{{ $attendances->adj_working_time }}">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-6">
                <div class="form-group">
                  <label>{{ __('attendanceapproval.overtime') }}</label>
                  <input type="text" class="form-control" placeholder="{{ __('attendanceapproval.overtime') }}" id="over_time" name="over_time" readonly value="{{ $attendances->adj_over_time }}">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label>{{ __('attendanceapproval.adjot') }}</label>
                  <input type="text" class="form-control" placeholder="{{ __('attendanceapproval.adjot') }}" id="adj_over_time" name="adj_over_time" required value="{{ $attendances->adj_over_time }}">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-12">
                <div class="form-group">
                  <label>{{ __('general.notes') }}</label>
                  <textarea class="form-control" id="note" name="note" placeholder="{{ __('general.notes') }}">{{ $attendances->note }}</textarea>
                  <input type="hidden" class="form-control" id="code_case" name="code_case" value="{{ $attendances->code_case }}">
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</form>
<div class="modal fade" id="add_history" tabindex="-1" role="dialog" aria-hidden="true" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="overlay-wrapper">
        <div class="modal-header">
          <h4 class="modal-title">{{ __('general.add') }}</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="form_history" action="{{ route('attendanceapproval.store')}}" method="post">
          <div class="modal-body">
            {{ csrf_field() }}
            <div class="row">
              <div class="col-md-12" style="visibility: hidden; position: absolute;">
                <div class="form-group">
                  <label for="attendance_id" class="control-label">Attendance ID</label>
                  <input type="text" class="form-control" id="attendance_id" name="attendance_id" placeholder="Attendance ID" value="{{ $attendances->id }}" readonly>
                </div>
              </div>
              <div class="col-md-12" style="visibility: hidden; position: absolute;">
                <div class="form-group">
                  <label for="attendance_id" class="control-label">{{ __('attendancelog.employid') }}</label>
                  <input type="text" class="form-control" id="employee_id" name="employee_id" placeholder="{{ __('attendancelog.employid') }}" value="{{ $attendances->employee_id }}" readonly>
                </div>
              </div>
              <div class="col-md-12">
                <div class="form-group">
                  <label for="time" class="control-label">{{ __('attendancelog.time') }}</label>
                  <input type="text" class="form-control datepicker" name="time" id="time" placeholder="{{ __('attendancelog.time') }}" required>
                </div>
              </div>
              <div class="col-md-12">
                <div class="form-group">
                  <label for="qty_allowance" class="control-label">{{ __('general.type') }}</label>
                  <select name="type" id="type" class="form-control select2" style="width: 100%" aria-hidden="true">
                    <option value="1">Scan In</option>
                    <option value="0">Scan Out</option>
                  </select>
                </div>
              </div>
              <div class="col-md-12">
                <div class="form-group">
                  <label for="qty_allowance" class="control-label">{{ __('machine.machine') }}</label>
                  <input type="text" class="form-control" id="machine" name="machine" placeholder="{{ __('machine.machine') }}">
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button form="form_history" type="submit" class="btn btn-sm btn-{{ config('configs.app_theme')}} text-white" title="{{ __('general.add') }}"><i class="fa fa-save"></i></button>
            <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
          </div>
        </form>
        <div class="overlay d-none">
          <i class="fa fa-2x fa-sync-alt fa-spin"></i>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="edit_history" tabindex="-1" role="dialog" aria-hidden="true" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="overlay-wrapper">
        <div class="modal-header">
          <h4 class="modal-title">Add</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="form_edit_history" class="form-horizontal" method="post" autocomplete="off">
          <div class="modal-body">
            {{ csrf_field() }}
            <div class="row">
              <div class="col-md-12" style="visibility: hidden; position: absolute;">
                <div class="form-group">
                  <label for="attendance_id_edit" class="control-label">Attendance ID</label>
                  <input type="text" class="form-control" id="attendance_id_edit" name="attendance_id_edit" placeholder="Attendance ID" value="{{ $attendances->id }}" readonly>
                </div>
              </div>
              <div class="col-md-12" style="visibility: hidden; position: absolute;">
                <div class="form-group">
                  <label for="attendance_id_edit" class="control-label">Employee ID</label>
                  <input type="text" class="form-control" id="employee_id_edit" name="employee_id_edit" placeholder="Employee ID" value="{{ $attendances->employee_id }}" readonly>
                </div>
              </div>
              <div class="col-md-12">
                <div class="form-group">
                  <label for="time_edit" class="control-label">Time</label>
                  <input type="text" class="form-control datepicker" name="time_edit" id="time_edit" placeholder="Time" required>
                </div>
              </div>
              <div class="col-md-12">
                <div class="form-group">
                  <label for="qty_allowance_edit" class="control-label">Type</label>
                  <select name="type_edit" id="type_edit" class="form-control select2" style="width: 100%" aria-hidden="true">
                    <option value="1">Scan In</option>
                    <option value="0">Scan Out</option>
                  </select>
                </div>
              </div>
              <div class="col-md-12">
                <div class="form-group">
                  <label for="qty_allowance_edit" class="control-label">Machine</label>
                  <input type="text" class="form-control" id="machine_edit" name="machine_edit" placeholder="Machine">
                </div>
              </div>
            </div>
          </div>
          <input type="hidden" name="history_id">
          <input type="hidden" name="_method" />
          <div class="modal-footer">
            <button form="form_edit_history" type="submit" class="btn btn-sm btn-{{ config('configs.app_theme')}} text-white" title="Add"><i class="fa fa-save"></i></button>
            <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
          </div>
        </form>
        <div class="overlay d-none">
          <i class="fa fa-2x fa-sync-alt fa-spin"></i>
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
    $("#working_time").select2({
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
    @if ($attendances->workingtime_id)
    $("#working_time").select2('data',{id:{{$attendances->workingtime_id}},text:'{{$attendances->description}}'}).trigger('change');
    @endif
  });
  $(function() {
    function formatDate(date) {
      var d = new Date(date),
              second = '' + d.getSeconds(),
              minute = '' + d.getMinutes(),
              hour = '' + d.getHours();
      
      if (second.length < 2)
        second = '0' + second;
      if (minute.length < 2)
        minute = '0' + minute;
      if (hour.length < 2)
        hour = '0' + hour;

      return [hour, minute, second].join(':');
    }
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
              orderable: false,targets:[0,4]
          },
          { className: "text-center", targets: [0,2,4] },
          { render: function(data, type, row) {
            if (row.type == 1) {
              return '<span class="badge badge-success">Scan In</span>';
            } else {
              return '<span class="badge badge-danger">Scan Out</span>';
            }
          }, targets:[2]},
          { render: function ( data, type, row ) {
              return `<div class="dropdown">
                      <button class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                          <i class="fa fa-bars"></i>
                      </button>
                      <ul class="dropdown-menu dropdown-menu-right">
                          <li><a class="dropdown-item edithistory" href="javascript:void(0)" data-id="${row.id}"><i class="fa fa-edit"></i> Edit</a></li>
                      </ul></div>`
          },targets: [4]
          }
      ],
      columns: [
          { data: "no" },
          { data: "attendance_date" },
          { data: "type" },
          { data: "device_name" },
          { data: "id" }
      ]
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
              if(response.status){
                document.location = response.results;
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
    $('.add_history').on('click', function() {
      $('#form_history')[0].reset();
      $("input.check-day").attr("disabled", true);
      $('#form_history').attr('action',"{{ route('attendanceapproval.store') }}");
      $('#form_history input[name=_method]').attr('value', 'POST');
      $('#add_history .modal-title').html(`{{ __('general.add') }}`);
      $('#add_history').modal('show');
      $('#form_history').find('.datepicker').daterangepicker({
        startDate: moment('{!! $attendances->attendance_date !!}'),
        singleDatePicker: true,
        timePicker: true,
        timePicker24Hour: true,
        timePickerIncrement: 1,
        locale: {
          format: 'MM/DD/YYYY HH:mm:ss'
        }
      });
    });
    $(document).on('click', '.edithistory', function() {
      var id = $(this).data('id');
      $.ajax({
        url:`{{url('admin/attendanceapproval')}}/${id}/edithistory`,
        method:'GET',
        dataType:'json',
        beforeSend:function(){
          $('#overlay-history .overlay').removeClass('d-none');
        },
      }).done(function(response){
        $('#overlay-history .overlay').addClass('d-none');
        if(response.status){
          $('#edit_history .modal-title').html('Edit History');
          $('#edit_history').modal('show');
          $('#form_edit_history')[0].reset();
          $('#form_edit_history .invalid-feedback').each(function () { $(this).remove(); });
          $('#form_edit_history .form-group').removeClass('has-error').removeClass('has-success');
          $('#form_edit_history input[name=_method]').attr('value','PUT');
          $('#form_edit_history input[name=history_id]').attr('value',id);
          $('#form_edit_history input[name=time_edit]').daterangepicker({
            startDate:moment(response.data.attendance_date),
            singleDatePicker: true,
            timePicker: true,
            timePicker24Hour: true,
            timePickerIncrement: 1,
            locale: {
              format: 'MM/DD/YYYY HH:mm:ss'
            }
          });
          $('#form_edit_history select[name=type_edit]').val(response.data.type).trigger('change');
          $('#form_edit_history input[name=machine_edit]').attr('value',response.data.device_name);
          $('#form_edit_history').attr('action',`{{url('admin/attendanceapproval/')}}/${response.data.id}/updatehistory`);
        }          
      }).fail(function(response){
        var response = response.responseJSON;
        $('#overlay-history .overlay').addClass('d-none');
        $.gritter.add({
          title: 'Error!',
          text: response.message,
          class_name: 'gritter-error',
          time: 1000,
        });
      });
    });
    $("#form_edit_history").validate({
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
          url:$('#form_edit_history').attr('action'),
          method:'post',
          data: new FormData($('#form_edit_history')[0]),
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
            $.gritter.add({
              title: 'Success!',
              text: response.message,
              class_name: 'gritter-success',
              time: 1000,
            });
            $('#edit_history').modal('hide');
            $('#first_in').val(formatDate(response.data.attendance_in));
            $('#last_out').val(formatDate(response.data.attendance_out));
            $('#work_time').val(response.data.adj_working_time);
            $('#over_time').val(response.data.adj_over_time);
            $('#adj_working_time').val(response.data.adj_working_time);
            $('#adj_over_time').val(response.data.adj_over_time);
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
        })
      }
    });
    $("#form_history").validate({
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
          url:$('#form_history').attr('action'),
          method:'post',
          data: new FormData($('#form_history')[0]),
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
            $.gritter.add({
              title: 'Success!',
              text: response.message,
              class_name: 'gritter-success',
              time: 1000,
            });
            $('#add_history').modal('hide');
            $('#first_in').val(formatDate(response.data.attendance_in));
            $('#last_out').val(formatDate(response.data.attendance_out));
            $('#work_time').val(response.data.adj_working_time);
            $('#over_time').val(response.data.adj_over_time);
            $('#adj_working_time').val(response.data.adj_working_time);
            $('#adj_over_time').val(response.data.adj_over_time);
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
        })
      }
    });
  });
</script>
@endpush