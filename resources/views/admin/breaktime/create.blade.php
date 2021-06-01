@extends('admin.layouts.app')

@section('title',__('breaktime.breaktime'))
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
<li class="breadcrumb-item"><a href="{{route('breaktime.index')}}">{{ __('breaktime.breaktime') }}</a></li>
<li class="breadcrumb-item active">{{ __('general.crt') }}</li>
@endpush

@section('content')
<form id="form" action="{{ route('breaktime.store') }}" method="post" autocomplete="off">
  <div class="wrapper wrapper-content">
    <div class="row">
      <div class="col-lg-8">
        <div class="card card-{{ config('configs.app_theme') }} card-outline">
          <div class="card-header" style="height: 57px;">
            <h3 class="card-title">{{ __('general.crt') }} {{ __('breaktime.breaktime') }}</h3>
          </div>
          <div class="card-body">
            {{ csrf_field() }}
            <div class="row">
              <div class="col-sm-6">
                <div class="form-group">
                  <label>{{ __('breaktime.breaktime') }} <b class="text-danger">*</b></label>
                  <input type="text" class="form-control" name="break_time" id="break_time" placeholder="{{ __('breaktime.breaktime') }}">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label>{{ __('workgroupcombination.workcomb') }} <b class="text-danger">*</b></label>
                  <input type="text" class="form-control" name="workgroup" id="workgroup" data-placeholder="{{ __('workgroupcombination.workcomb') }}">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-6">
                <div class="form-group">
                  <label>{{ __('general.start_time') }}<b class="text-danger">*</b></label>
                  <input class="form-control timepicker" id="start_time" name="start_time">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label>{{ __('general.finish_time') }}<b class="text-danger">*</b></label>
                  <input class="form-control timepicker" id="finish_time" name="finish_time">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-6">
                <div class="form-group">
                  <label class="pr-5">{{ __('breaktime.crossdt') }}</label>
                  <input class="form-control" type="checkbox" id="cross_date" name="cross_date" checked>
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
            <h3 class="card-title">{{ __('general.other') }}</h3>
            <div class="pull-right card-tools">
              <button form="form" type="submit" class="btn btn-sm btn-{{ config('configs.app_theme') }}" title="Save"><i class="fa fa-save"></i></button>
              <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="{{ __('general.prvious') }}"><i class="fa fa-reply"></i></a>
            </div>
          </div>
          <div class="card-body">
            <form role="form">
              <div class="row">
                <div class="col-sm-12">
                  <!-- text input -->
                  <div class="form-group">
                    <label>{{ __('general.notes') }}</label>
                    <textarea class="form-control" name="notes" placeholder="{{ __('general.notes') }}"></textarea>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-12">
                  <div class="form-group">
                    <label>{{ __('general.status') }} <b class="text-danger">*</b></label>
                    <select name="status" id="status" class="form-control select2" data-placeholder="{{ __('general.chs') }} {{ __('general.status') }}">
                      <option value=""></option>
                      <option value="1">{{ __('general.actv') }}</option>
                      <option value="0">{{ __('general.noactv') }}</option>
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
            <h3 class="card-title">{{ __('department.dep') }}</h3>
          </div>
          <div class="card-body">
            <table class="table table-striped table-bordered datatable" id="department-table" style="width: 100%">
              <thead>
                <tr>
                  <th>No</th>
                  <th>{{ __('department.dep') }} {{ __('general.name') }}</th>
                  <th>
                    <div class="customcheckbox">
                      <input type="checkbox" name="checkall" class="checkall" id="checkall">
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
  $(document).ready(function(){
    $('.i-checks').iCheck({
      checkboxClass: 'icheckbox_square-green',
      radioClass: 'iradio_square-green',
    });
    $('#cross_date').iCheck({
      checkboxClass: 'icheckbox_square-green',
      radioClass: 'iradio_square-green',
    });
    $("#status").select2();
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
    $( "#workgroup" ).select2({
      multiple: true,
      ajax: {
        url: "{{route('workgroup.select')}}",
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
              text: `${item.name}`
            });
          });
          return {
            results: option, more: more,
          };
        },
      },
      allowClear: true,
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
				url: "{{ route('breaktimedepartment.read') }}",
				type: "GET",
				data: function(data) {
					
				}
			},
			columnDefs: [
				{ orderable: false, targets: [0,1,2] },
				{ className: "text-center", targets: [0,2] },
				{ render: function ( data, type, row ) {
              return `<label class="customcheckbox checked"><input value="${row.id}" type="checkbox" name="department_id[]" checked><span class="checkmark"></span></label>`
            },targets: [2] }
			],
			columns: [
				{ data: "no" },
				{ data: "name" },
				{ data: "id" },
			]
		});
    $(document).on("change", "#workgroup", function () {
      if (!$.isEmptyObject($('#form').validate().submitted)) {
        $('#form').validate().form();
      }
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
		$('input[name=checkall]').prop('checked', true);
		$('input[name=checkall]').parent().addClass('checked');
		$('input[name^=department_id]').prop('checked', true);
		$('input[name^=department_id]').parent().addClass('checked');
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