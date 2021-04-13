@extends('admin.layouts.app')

@section('title', 'Attendance Cut Off ')
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/daterangepicker/daterangepicker.css')}}" rel="stylesheet">
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
<li class="breadcrumb-item"><a href="{{route('breaktime.index')}}">Attendance Cut Off</a></li>
<li class="breadcrumb-item active">Edit</li>
@endpush

@section('content')
<form id="form" action="{{ route('attendancecutoff.update',['id'=>$attendancecutoff->id]) }}" method="post" autocomplete="off">
  <div class="wrapper wrapper-content">
    <div class="row">
      <div class="col-lg-8">
        <div class="card card-{{ config('configs.app_theme') }} card-outline">
          <div class="card-header" style="height: 57px;">
            <h3 class="card-title">Attendance Cut Off</h3>
          </div>
          <div class="card-body">
            @method('put')
            {{ csrf_field() }}
            <div class="row">
              <div class="col-sm-6">
                <div class="form-group">
                  <label>Name <b class="text-danger">*</b></label>
                  <input type="text" class="form-control" name="name" id="name" value="{{$attendancecutoff->name}}" placeholder="Name">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label>Option <b class="text-danger">*</b></label>
                  <select name="option" id="option" class="form-control select2" data-placeholder="Select Option">
                      <option value=""></option>
                      <option value="Flexible" @if ($attendancecutoff->option == 'Flexible') selected @endif>Flexible</option>
                      <option value="Static" @if ($attendancecutoff->option == 'Static') selected @endif>Static</option>
                    </select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-6" id="duration-section">
                <div class="form-group">
                  <label>Duration(Hour)<b class="text-danger">*</b></label>
                  <input type="number" class="form-control" value="{{$attendancecutoff->duration}}" id="duration" name="duration">
                </div>
              </div>
              <div class="col-sm-6" id="hour-section">
                <div class="form-group">
                  <label>Hour<b class="text-danger">*</b></label>
                  <input class="form-control timepicker" id="hour" name="hour" value="{{$attendancecutoff->hour}}">
                </div>
              </div>
            </div>
          </div>
          <div class="overlay d-none">
            <i class="fa fa-refresh fa-spin"></i>
          </div>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="card card-{{ config('configs.app_theme') }} card-outline">
          <div class="card-header">
            <h3 class="card-title">Other</h3>
            <div class="pull-right card-tools">
              <button form="form" type="submit" class="btn btn-sm btn-{{ config('configs.app_theme') }}" title="Save"><i class="fa fa-save"></i></button>
              <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Back"><i class="fa fa-reply"></i></a>
            </div>
          </div>
          <div class="card-body">
            <form role="form">
              <div class="row">
                <div class="col-sm-12">
                  <!-- text input -->
                  <div class="form-group">
                    <label>Notes</label>
                    <textarea class="form-control" name="description" placeholder="Notes" value="{{$attendancecutoff->description}}">{{$attendancecutoff->description}}</textarea>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-12">
                  <div class="form-group">
                    <label>Status <b class="text-danger">*</b></label>
                    <select name="status" id="status" class="form-control select2" data-placeholder="Select Status">
                      <option value=""></option>
                      <option @if ($attendancecutoff->status == 1) selected @endif value="1">Aktif</option>
                      <option @if ($attendancecutoff->status == 0) selected @endif value="0">Non Aktif</option>
                    </select>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
      <div class="col-lg-12">
        <div class="card card-{{ config('configs.app_theme') }} card-outline">
          <div class="card-header">
            <h3 class="card-title">Department</h3>
          </div>
          <div class="card-body">
            <table class="table table-striped table-bordered datatable" id="department-table" style="width: 100%">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Department Name</th>
                  <th>
                    <div class="customcheckbox">
                      <input type="checkbox" name="checkall" class="checkall" id="checkall" onclick="checkAll(this)">
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
</form>
@endsection

@push('scripts')
<script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
<script src="{{asset('adminlte/component/dataTables/js/datatables.min.js')}}"></script>
<script src="{{asset('adminlte/component/daterangepicker/moment.min.js')}}"></script>
<script src="{{asset('adminlte/component/daterangepicker/daterangepicker.js')}}"></script>
<script>
    function checkAll(data) {
      $.ajax({
        url: `{{ route('attendancecutoffdepartment.updateall') }}`,
        method: 'post',
        data: {
          _token: "{{ csrf_token() }}",
          attendance_cut_off_id: `{{ $attendancecutoff->id }}`,
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
		var attendance_cut_off_id, department_id, status;
		if (data.checked) {
			attendance_cut_off_id	= `{{ $attendancecutoff->id }}`;
			department_id	= data.value;
			status				= 1;
		} else {
			attendance_cut_off_id	= `{{ $attendancecutoff->id }}`;
			department_id	= data.value;
			status				= 0;
		}
		$.ajax({
			url: `{{ route('attendancecutoffdepartment.store') }}`,
			method: 'post',
			data: {
				_token: "{{ csrf_token() }}",
				attendance_cut_off_id: attendance_cut_off_id,
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
  $(document).ready(function(){
    $('.i-checks').iCheck({
      checkboxClass: 'icheckbox_square-green',
      radioClass: 'iradio_square-green',
    });
    $('#cross_date').iCheck({
      checkboxClass: 'icheckbox_square-green',
      radioClass: 'iradio_square-green',
    });
    $(".select2").select2();
    // $("#status").select2();
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
            url: "{{ route('attendancecutoffdepartment.read') }}",
            type: "GET",
            data: function(data) {
					data.attendance_cut_off_id = `{{ $attendancecutoff->id }}`;
				}
        },
        columnDefs: [
            { orderable: false, targets: [0,1,2] },
            { className: "text-center", targets: [0,2] },
            { render: function ( data, type, row ) {
              return row.attendancecutoffdepartment.length > 0 ? `<label class="customcheckbox checked"><input value="${row.id}" onclick="updateDepartment(this)" type="checkbox" name="department_id[]" checked><span class="checkmark"></span></label>` : `<label class="customcheckbox"><input value="${row.id}" onclick="updateDepartment(this)" type="checkbox" name="department_id[]"><span class="checkmark"></span></label>`
        },targets: [2] }
        ],
        columns: [
            { data: "no" },
            { data: "name" },
            { data: "id" },
        ]
    });
  
    $(document).on("change", "#department_id", function () {
      if (!$.isEmptyObject($('#form').validate().submitted)) {
        $('#form').validate().form();
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
        })
      }
    });
    @if ($attendancecutoff->duration)
      $('#duration-section').show();
      $('#hour-section').hide();
    @endif
    @if ($attendancecutoff->hour)
      $('#duration-section').hide();
      $('#hour-section').show();
    @endif
    $(document).on('change', '#option', function() {
        // alert(this.value);
      if (this.value == 'Flexible') {
        $('#duration-section').show();
        $('#hour').attr('value','');
        $('#hour-section').hide();
      } else {
        $('#duration').attr('value','');
        $('#duration-section').hide();
        $('#hour-section').show();
      }
    }).trigger('change');
    $('input[name=checkall]').prop('checked', true);
    $('input[name=checkall]').parent().addClass('checked');
    
    $(document).on('click', '.customcheckbox input', function() {
        if ($(this).is(':checked')) {
            $(this).parent().addClass('checked');
        } else {
            $(this).parent().removeClass('checked');
        }
    });
    $(document).on('change', '.checkall', function() {
        if (this.checked) {
            $('input[name^=department_id]').prop('checked', true);
            $('input[name^=department_id]').parent().addClass('checked');
        } else {
            $('input[name^=department_id]').prop('checked', false);
            $('input[name^=department_id]').parent().removeClass('checked');
        }
    });
  });
</script>
@endpush