@extends('admin.layouts.app')
@section('title', 'Edit Overtime Scheme')
@section('stylesheets')
<link rel="stylesheet" href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}">
<link rel="stylesheet" href="{{asset('adminlte/component/bootstrap-fileinput/css/fileinput.min.css')}}">
<link rel="stylesheet" href="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.css')}}">
<style type="text/css">
	.customcheckbox {
		width: 22px;
		height: 22px;
		background: url("/img/green.png") no-repeat;
		background-position-x: 0%;
		background-position-y: 0%;
		cursor: pointer;
		margin: 0 auto;
	}

	.customcheckbox.checked {
		background-position: -48px 0;
	}

	.customcheckbox:hover {
		background-position: -24px 0;
	}

	.customcheckbox.checked:hover {
		background-position: -48px 0;
	}

	.customcheckbox input {
		cursor: pointer;
		opacity: 0;
		scale: 1.6;
		width: 22px;
		height: 22px;
		margin: 0;
	}
</style>
@endsection
@push('breadcrump')
<li class="breadcrumb-item"><a href="{{ route('overtimescheme.index') }}">Overtime Scheme</a></li>
<li class="breadcrumb-item active">Edit</li>
@endpush

@section('content')
<form id="form" action="{{ route('overtimescheme.update', ['id'=>$overtime->id]) }}" class="form-horizontal" method="post" autocomplete="off">
<div class="row">
  <div class="col-lg-12">
    <div class="card card-{{ config('configs.app_theme') }} card-outline">
      <div class="card-header">
        <h3 class="card-title">Edit Overtime Scheme</h3>
        <div class="pull-right card-tools">
          <button form="form" type="submit" class="btn btn-sm btn-{{ config('configs.app_theme') }}" title="Simpan"><i
              class="fa fa-save"></i></button>
          <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i
              class="fa fa-reply"></i></a>
        </div>
      </div>
      <div class="card-body">
        {{ csrf_field() }}
        {{ method_field('put') }}
        <div class="form-group row">
          <label for="scheme_name" class="col-sm-2 col-form-label">Scheme Name <span class="text-red">*</span></label>
          <div class="col-sm-6">
            <input type="text" class="form-control" name="scheme_name" id="scheme_name" value="{{ $overtime->scheme_name }}" required>
          </div>
        </div>
        <div class="form-group row">
          <label for="category" class="col-sm-2 col-form-label">Category <span class="text-red">*</span></label>
          <div class="col-sm-6">
            <select name="category" id="category" class="form-control select2" style="width: 100%" aria-hidden="true" required>
              @foreach (config('enums.allowance_category') as $key=>$value)
              <option value="{{ $key }}" @if ($overtime->category == $key) selected @endif>{{ $value }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="form-group row">
          <label for="working_time" class="col-sm-2 col-form-label">Working Time <span class="text-red">*</span></label>
          <div class="col-sm-1">
            <input type="number" class="form-control" name="working_time" id="working_time" value="{{ $overtime->working_time }}" required>
          </div>
          <p for="working_time" class="col-sm-2 col-form-label">Hours</p>
        </div>
        <div class="form-group row">
              <label for="type" class="col-sm-2 col-form-label">Type <b class="text-danger">*</b></label>
              <div class="col-sm-6">
                <select name="type" id="type" class="form-control select2" style="width: 100%" data-placeholder="Select Type" required>
                  @foreach (config('enums.penalty_config_type') as $key => $item)
                  <option value="{{ $key }}" @if ($overtime->type == $key) selected @endif>{{ $item }}</option>
                  @endforeach
                </select>
              </div>
            </div>
      </div>
      <div class="overlay d-none">
        <i class="fa fa-2x fa-sync-alt fa-spin"></i>
      </div>
    </div>
  </div>
  <div class="col-lg-12">
    <div class="card card-{{ config('configs.app_theme') }} card-outline">
      <div class="nav nav-tabs" id="nav-tab" role="tablist">
        <a class="nav-item nav-link active" id="nav-rule-tab" data-toggle="tab" href="#nav-rule" role="tab" aria-controls="nav-rule" aria-selected="true">Rule</a>
        <a class="nav-item nav-link" id="nav-department-tab" data-toggle="tab" href="#nav-department" role="tab" aria-controls="nav-department" aria-selected="false">Department</a>
        <a class="nav-item nav-link" id="nav-allowance-tab" data-toggle="tab" href="#nav-allowance" role="tab" aria-controls="nav-allowance" aria-selected="false">Allowance</a>
      </div>
      <div class="tab-content" id="nav-tabContent">
        <div class="tab-pane fade show active" id="nav-rule" role="tabpanel" aria-labelledby="nav-rule-tab">
          <div class="card-header">
            <h3 class="card-title">Edit Overtime Rules</h3>
          </div>
          <div class="card-body">
            <div class="form-group row">
              <label for="recurrence" class="col-sm-2 col-form-label">Workdays</label>
              <div class="col-sm-6">
                <select name="workday[]" id="workday" class="form-control select2" style="width: 100%" multiple="multiple" required>
                  <option value="Mon">Monday</option>
                  <option value="Tue">Tuesday</option>
                  <option value="Wed">Wednesday</option>
                  <option value="Thu">Thursday</option>
                  <option value="Fri">Friday</option>
                  <option value="Sat">Saturday</option>
                  <option value="Off">Day Off</option>
                </select>
              </div>
            </div>
            <table class="table table-striped table-bordered datatable" id="workday_table" style="width: 100%; height: 100%">
              <thead>
                <tr>
                  <th width="10">No</th>
                  <th width="100">Hour</th>
                  <th width="200">Amount</th>
                  <th width="10">Action</th>
                </tr>
              </thead>
              <tbody>
                @if (count($list) > 0 && $overtime->overtimelist->first()->recurrence_day != null)
                    @foreach ($list as $key => $item)
                    <tr>
                      <td class="text-center align-middle number">{{ $key + 1 }}</td>
                      <td class="text-center align-middle">
                        <div class="form-group mb-0"><input type="hidden" name="overtime_rules[{{ $key }}]" /><input type="number" placeholder="Hour"
                            name="hour[{{ $key }}]" value="{{ $item->hour }}" class="form-control" required /></div>
                      </td>
                      <td class="text-center align-middle">
                        <div class="form-group mb-0"><input type="number" placeholder="Amount" name="amount[{{ $key }}]" step="0.01" class="form-control" value="{{ $item->amount }}" required /></div>
                      </td>
                      <td class="text-center align-middle"><a href="javascript:void(0)"
                          onclick="addList()" class="fa fa-plus fa-lg d-inline"></a> / <a href="javascript:void(0)" class="fa fa-trash fa-lg d-inline remove"></td>
                    </tr>
                    @endforeach
                @else
                <tr>
                  <td class="text-center align-middle number">1</td>
                  <td class="text-center align-middle">
                    <div class="form-group mb-0"><input type="hidden" name="overtime_rules[0]" /><input type="number" placeholder="Hour"
                        name="hour[0]" class="form-control" required /></div>
                  </td>
                  <td class="text-center align-middle">
                    <div class="form-group mb-0"><input type="number" placeholder="Amount" name="amount[0]" step="0.1" class="form-control" required /></div>
                  </td>
                  <td class="text-center align-middle"><a href="javascript:void(0)"
                      onclick="addList()" class="fa fa-plus fa-lg d-inline"></a></td>
                </tr>
                @endif
              </tbody>
            </table>
          </div>
        </div>
        <div class="tab-pane fade show" id="nav-department" role="tabpanel" aria-labelledby="nav-department-tab">
          <div class="card-header">
            <h3 class="card-title">Department</h3>
          </div>
          <div class="card-body">
            <table class="table table-striped table-bordered datatable" id="department-table" style="width: 100%">
              <thead>
                <tr>
                  <th class="text-center align-middle">No</th>
                  <th width="400">Department Name</th>
                  <th class="text-center align-middle">
                    <div class="customcheckbox" id="customcheckbox_department">
                      <input type="checkbox" name="checkall" onclick="checkAll(this)" class="checkall" id="checkall">
                    </div>
                  </th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
        <div class="tab-pane fade show" id="nav-allowance" role="tabpanel" aria-labelledby="nav-allowance-tab">
              <div class="card-header">
                <h3 class="card-title">Allowance</h3>
              </div>
              <div class="card-body">
                <table class="table table-striped table-bordered datatable" id="allowance-table" style="width: 100%">
                  <thead>
                    <tr>
                      <th width="10">No</th>
                      <th width="200">Allowance</th>
                      <th width="200">Category</th>
                      <th width="200">Group</th>
                      <th width="10">
                        <div class="customcheckbox" id="customcheckbox_allowance">
                          <input type="checkbox" name="checkallallowance" class="checkall" onclick="checkAllAllowance(this)" id="checkallallowance">
                        </div>
                      </th>
                    </tr>
                  </thead>
                </table>
              </div>
          </div>
      </div>
      <div class="overlay d-none">
        <i class="fa fa-2x fa-sync-alt fa-spin"></i>
      </div>
    </div>
  </div>
</div>
</form>
@endsection

@push('scripts')
<script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
<script src="{{asset('adminlte/component/dataTables/js/datatables.min.js')}}"></script>
<script src="{{asset('adminlte/component/daterangepicker/moment.min.js')}}"></script>
<script src="{{asset('adminlte/component/daterangepicker/daterangepicker.js')}}"></script>
<script>
    const BASIC = 'BASIC';
  $(document).ready(function(){
    $('.select2').select2();
    @if ($day)
      $("#workday").select2('data',{!! json_encode($day) !!}).trigger('change');
    @endif
    $(document).on('click', '#customcheckbox_department input', function() {
			if ($(this).is(':checked')) {
				$(this).parent().addClass('checked');
			} else {
				$(this).parent().removeClass('checked');
			}
		});
		$(document).on('change', '#checkall', function() {
			if (this.checked) {
				$('input[name^=department_id]').prop('checked', true);
				$('input[name^=department_id]').parent().addClass('checked');
			} else {
				$('input[name^=department_id]').prop('checked', false);
				$('input[name^=department_id]').parent().removeClass('checked');
			}
		});
    $(document).on('click', '#customcheckbox_allowance input', function() {
			if ($(this).is(':checked')) {
				$(this).parent().addClass('checked');
			} else {
				$(this).parent().removeClass('checked');
			}
		});
		$(document).on('change', '#checkallallowance', function() {
			if (this.checked) {
				$('input[name^=allowanceID]').prop('checked', true);
				$('input[name^=allowanceID]').parent().addClass('checked');
			} else {
				$('input[name^=allowanceID]').prop('checked', false);
				$('input[name^=allowanceID]').parent().removeClass('checked');
			}
		});
    dataTableDepartment = $("#department-table").DataTable({
			stateSave: true,
			processing: true,
			serverSide: true,
			filter: false,
			info: false,
			lengtChange: true,
			responsive: true,
			order: [[1, "asc"]],
			lengthMenu: [ 100, 250, 500, 1000 ],
			ajax: {
				url: "{{ route('overtimeschemedepartment.read') }}",
				type: "GET",
				data: function(data) {
					data.overtime_scheme_id = `{{ $overtime->id }}`;
				}
			},
			columnDefs: [
				{ orderable: false, targets: [0,1,2] },
				{ className: "text-center", targets: [0,2] },
				{ render: function ( data, type, row ) {
              return row.departmentovertimescheme.length > 0 ? `<label class="customcheckbox checked" id="customcheckbox_department"><input value="${row.id}" type="checkbox" onclick="updateDepartment(this)" name="department_id[]" checked><span class="checkmark"></span></label>` : `<label class="customcheckbox" id="customcheckbox_department"><input value="${row.id}" type="checkbox" onclick="updateDepartment(this)" name="department_id[]"><span class="checkmark"></span></label>`
            },targets: [2] }
			],
			columns: [
				{ data: "no" },
				{ data: "name" },
				{ data: "id" },
			]
		});
    dataTableAllowance = $('#allowance-table').DataTable({
      stateSave: true,
      processing: true,
      serverSide: true,
      filter: false,
      info: false,
      lengthChange: false,
      responsive: true,
      paginate: false,
      order: [[ 1, "asc"]],
      ajax: {
        url: "{{ route('overtimeschemeallowance.read') }}",
        type: "GET",
        data: function(data) {
          data.overtime_scheme_id = `{{ $overtime->id }}`;
        }
      },
      columnDefs: [
        { orderable: false, targets: [0, 4] },
        { className: 'text-right', targets: [0] },
        { className: 'text-center', targets: [4] },
        { render: function( data, type, row ) {
          return row.groupallowance? `${row.groupallowance.name}` : ``
        }, targets: [3] },
        { render: function( data, type, row ) {
          return row.allowanceovertimescheme.length > 0 ? `<label class="customcheckbox checked" id="customcheckbox_allowance"><input value="${row.id}" type="checkbox" name="allowanceID[]" onclick="updateAllowance(this)" checked><span class="checkmark"></span></label>` : `<label class="customcheckbox" id="customcheckbox_allowance"><input value="${row.id}" type="checkbox" name="allowanceID[]" onclick="updateAllowance(this)"><span class="checkmark"></span></label>`
        }, targets: [4] }
      ],
      columns: [
        { data: 'no' },
        { data: 'allowance' },
        { data: 'category' },
        { data: 'groupallowance'},
        { data: 'id' },
      ]
    });
    var type = $("#type").val();
    console.log(type);
    if(type == 'BASIC'){
      $('#nav-allowance-tab').addClass('d-none');
      $('#nav-allowance').addClass('d-none');
    }
     $(document).on('change', '#type', function() {
      if (this.value == BASIC) {
        $('#nav-allowance-tab').addClass('d-none');
        $('#nav-allowance').addClass('d-none');
      } else {
        $('#nav-allowance-tab').removeClass('d-none');
        $('#nav-allowance').removeClass('d-none');
      }
    }).trigger('change');
  });
  function checkAll(data) {
		$.ajax({
			url: `{{ route('overtimeschemedepartment.updateall') }}`,
			method: 'post',
			data: {
				_token: "{{ csrf_token() }}",
				overtime_scheme_id: `{{ $overtime->id }}`,
				status: data.checked ? 1 : 0,
			},
			dataType: 'json',
			beforeSend: function() {
				$('.overlay').removeClass('d-none');
			}
		}).done(function(response) {
			$('.overlay').addClass('d-none');
			if (response.status) {
				$.gritter.add({
					title: 'Success!',
					text: response.message,
					class_name: 'gritter-success',
					time: 1000,
				});
			} else {
				$.gritter.add({
					title: 'Warning!',
					text: response.message,
					class_name: 'gritter-warning',
					time: 1000,
				});
			}
			return;
		}).fail(function(response) {
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
	function updateDepartment(data) {
		var overtime_scheme_id, department_id, status;
		if (data.checked) {
			overtime_scheme_id	= `{{ $overtime->id }}`;
			department_id		=	data.value;
			status					= 1;
		} else {
			overtime_scheme_id	= `{{ $overtime->id }}`;
			department_id		=	data.value;
			status					= 0;
		}
		$.ajax({
			url: `{{ route('overtimeschemedepartment.store') }}`,
			method: 'post',
			data: {
				_token: "{{ csrf_token() }}",
				overtime_scheme_id: overtime_scheme_id,
				department_id: department_id,
				status: status,
			},
			dataType: 'json',
			beforeSend: function() {
				$('.overlay').removeClass('d-none');
			}
		}).done(function(response) {
			$('.overlay').addClass('d-none');
			if (response.status) {
				$.gritter.add({
					title: 'Success!',
					text: response.message,
					class_name: 'gritter-success',
					time: 1000,
				});
			} else {
				$.gritter.add({
					title: 'Warning!',
					text: response.message,
					class_name: 'gritter-warning',
					time: 1000,
				});
			}
			return;
		}).fail(function(response) {
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
  function checkAllAllowance(data) {
		$.ajax({
			url: `{{ route('overtimeschemeallowance.updateall') }}`,
			method: 'post',
			data: {
				_token: "{{ csrf_token() }}",
				overtime_scheme_id: `{{ $overtime->id }}`,
				status: data.checked ? 1 : 0,
			},
			dataType: 'json',
			beforeSend: function() {
				$('.overlay').removeClass('d-none');
			}
		}).done(function(response) {
			$('.overlay').addClass('d-none');
			if (response.status) {
				$.gritter.add({
					title: 'Success!',
					text: response.message,
					class_name: 'gritter-success',
					time: 1000,
				});
			} else {
				$.gritter.add({
					title: 'Warning!',
					text: response.message,
					class_name: 'gritter-warning',
					time: 1000,
				});
			}
			return;
		}).fail(function(response) {
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
	function updateAllowance(data) {
		var overtime_scheme_id, allowanceID, status;
		if (data.checked) {
			overtime_scheme_id	= `{{ $overtime->id }}`;
			allowanceID		=	data.value;
			status					= 1;
		} else {
			overtime_scheme_id	= `{{ $overtime->id }}`;
			allowanceID		=	data.value;
			status					= 0;
		}
		$.ajax({
			url: `{{ route('overtimeschemeallowance.store') }}`,
			method: 'post',
			data: {
				_token: "{{ csrf_token() }}",
				overtime_scheme_id: overtime_scheme_id,
				allowanceID: allowanceID,
				status: status,
			},
			dataType: 'json',
			beforeSend: function() {
				$('.overlay').removeClass('d-none');
			}
		}).done(function(response) {
			$('.overlay').addClass('d-none');
			if (response.status) {
				$.gritter.add({
					title: 'Success!',
					text: response.message,
					class_name: 'gritter-success',
					time: 1000,
				});
			} else {
				$.gritter.add({
					title: 'Warning!',
					text: response.message,
					class_name: 'gritter-warning',
					time: 1000,
				});
			}
			return;
		}).fail(function(response) {
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
  function addList() {
    var length = $('#workday_table tr').length - 1;
    var number = $('#workday_table tr').length;
    var html = '<tr>';
        html += '<td class="text-center align-middle number">'+ number +'</td>';
        html += '<td class="text-center align-middle"><div class="form-group mb-0"><input type="hidden" name="overtime_rules['+length+']"/><input type="number" placeholder="Hour" name="hour['+length+']" class="form-control" required/></div></td>';
        html += '<td class="text-center align-middle"><div class="form-group mb-0"><input type="number" placeholder="Amount" name="amount['+length+']" class="form-control" step="0.01" required /></div></td>';
        html += '<td class="text-center align-middle"><a href="javascript:void(0)" class="fa fa-plus fa-lg d-inline" onclick="addList()"></a> / <a href="javascript:void(0)" class="fa fa-trash fa-lg d-inline remove"></a></td>';
        html += '</tr>'
    $('#workday_table').append(html);
  }
  $('#workday_table').on('click','.remove',function(){
    $(this).parents('tr').remove();
  });
  $('#form').validate({
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
              if (response.status == 'refresh') {
                $.gritter.add({
                    title: 'Error!',
                    text: response.message,
                    class_name: 'gritter-error',
                    time: 1000,
                });
                setTimeout(function() {
                    location.reload();
                }, 2000);
              }
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
            if (response.status == 'refresh') {
              $.gritter.add({
                  title: 'Error!',
                  text: response.message,
                  class_name: 'gritter-error',
                  time: 1000,
              });
              setTimeout(function() {
                  location.reload();
              }, 2000);
            } else {
              $.gritter.add({
                  title: 'Error!',
                  text: response.message,
                  class_name: 'gritter-error',
                  time: 1000,
              });
            }
        })
      }
  });
</script>
@endpush