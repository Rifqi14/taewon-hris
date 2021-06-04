@extends('admin.layouts.app')

@section('title', __('attendancelog.import'))
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/css/fileinput.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/daterangepicker/daterangepicker.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/jquery-ui/jquery-ui.min.css')}}" rel="stylesheet">
<style type="text/css">
  .overlay-wrapper {
    position: relative;
  }
</style>
@endsection
@push('breadcrump')
<li class="breadcrumb-item"><a href="{{route('attendanceapproval.index')}}">{{ __('attendancelog.attenlog') }}</a></li>
<li class="breadcrumb-item active">{{ __('attendancelog.import') }}</li>
@endpush
@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="card card-{{ config('configs.app_theme') }} card-outline" id="attendance-preview">
      <div class="card-header">
        <h3 class="card-title">{{ __('attendancelog.import') }}</h3>
        <!-- tools card -->
        <div class="pull-right card-tools">
          <a href="#" class="btn btn-warning text-white btn-sm sync" data-toggle="tooltip" title="{{ __('attendancelog.sync') }}">
            <i class="fa fa-sync-alt"></i>
          </a>
          <button form="form" type="submit" class="btn btn-sm btn-{{ config('configs.app_theme') }}" title="{{ __('general.save') }}"><i class="fa fa-save"></i></button>
          <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="{{ __('general.prvious') }}"><i class="fa fa-reply"></i></a>
        </div>
        <!-- /. tools -->
      </div>
      <div class="card-body">
        <form id="form" action="{{route('attendance.newstoremass')}}">
        </form>
        <div class="row pb-3">
          <div class="col-lg-4">
            <label for="date" class="control-label">{{ __('general.date') }}</label>
            <input type="text" name="date" id="date" class="form-control" required>
            <input type="hidden" name="no" id="no" class="form-control" required value="1">
          </div>
          <div class="col-lg-4">
            <label for="time" class="control-label">{{ __('attendancelog.time') }}</label>
            <select name="time" id="time" class="form-control select2" required>
              <option value="00:00 - 07:59">00:00 - 07:59</option>
              <option value="08:00 - 15:59">08:00 - 15:59</option>
              <option value="16:00 - 23:59">16:00 - 23:59</option>
            </select>
          </div>
          <div class="col-lg-4">
            <label for="attendanceMachine" class="control-label">{{ __('machine.machine') }}</label>
            <input type="text" name="attendanceMachine" id="attendanceMachine" class="form-control" required>
          </div>
        </div>
        <table class="table table-striped table-bordered" style="width:100%" id="table-item">
          <thead>
            <tr>
              <th width="100">{{ __('attendancelog.employid') }}</th>
              <th width="100">{{ __('attendancelog.firstnm') }}</th>
              <th width="100">{{ __('attendancelog.lastnm') }}</th>
              <th width="50">Area</th>
              <th width="100">{{ __('attendancelog.device') }}</th>
              <th width="100">{{ __('attendancelog.attentp') }}</th>
              <th width="100">{{ __('general.date') }}</th>
              <th width="50">Status</th>
            </tr>
          </thead>
        </table>
      </div>
      <div class="overlay d-none">
        <i class="fa fa-2x fa-sync-alt fa-spin"></i>
      </div>
    </div>
  </div>
</div>
<div class="modal" id="select-file" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog">
    <div class="overlay-wrapper">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">{{ __('general.chs') }} {{ __('general.file') }}</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form class="form-horizontal" id="form-import" action="#" method="post">
            {{ csrf_field() }}
            <div class="row">
              <div class="col-sm-12">
                <div class="form-group">
                  <label class="control-label" for="file">File Excel</label>
                  <input type="file" class="form-control" id="file" name="file" required accept=".xlsx" />
                </div>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button form="form-import" type="submit" class="btn btn-{{ config('configs.app_theme') }}" title="{{ __('general.imp') }}"><i class="fa fa-file-import"></i></button>
        </div>
      </div>
      <div class="overlay d-none">
        <i class="fa fa-refresh fa-spin"></i>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="{{asset('adminlte/component/dataTables/js/datatables.min.js')}}"></script>
<script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-fileinput/js/fileinput.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.js')}}"></script>
<script src="{{asset('adminlte/component/daterangepicker/moment.min.js')}}"></script>
<script src="{{asset('adminlte/component/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('adminlte/component/jquery-ui/jquery-ui.min.js')}}"></script>
<script type="text/javascript">
  var items = {},count=0;
  function addImport(){
      $('#form-import')[0].reset();
      $('#form-import').find('.help-block').remove();
      $('#form-import .form-group').removeClass('has-error').removeClass('has-success');
      $('#select-file').modal('show');
  }
  function loadItem(table_item){
      count=0;
      $.each(items, function() {
          table_item.row.add([
            this.personel_id,
            this.first_name,
            this.last_name,
            this.attendance_area,
            this.device_name,
            this.point_name,
            this.attendance_date,
            this.employee_id
          ]).draw(false);
          count++;
      });
  }
  function loadItem2(table_item){
      count=0;
      $.each(items, function() {
          table_item.row.add([
            this.personel_id,
            this.first_name,
            this.last_name,
            this.attendance_area,
            this.device_name,
            this.point_name,
            this.attendance_date,
            this.employee_id,
            this.workingtime_id,
            this.workingtime,
            this.overtime,
            this.in,
            this.out
          ]).draw(false);
          count++;
      });
  }
  $(function(){
    $('#date').daterangepicker({
      singleDatePicker: true,
      timePicker: false,
      timePickerIncrement: 30,
      locale: {
        format: 'DD/MM/YYYY'
      }
    }, function(chosen_date) {
      $('#date').val(chosen_date.format('DD/MM/YYYY'));
    });

    $('#attendanceMachine').select2({
      ajax: {
        url: "{{ route('attendancemachine.select') }}",
        type: "GET",
        dataType: "JSON",
        data: function(term, page) {
          return { name: term, page: page, limit: 30 };
        },
        results: function(data, page) {
          var more = (page * 30) < data.total;
          var option = [];
          $.each(data.rows, function(index, item) {
            option.push({
              id: `${item.device_sn}`,
              text: `${item.device_sn} - ${item.point_name}`
            });
          });
          return { results: option, more: more, };
        },
      },
      allowClear: true,
    });
    $('.select2').select2();
    $("#file").fileinput({
          browseClass: "btn btn-{{ config('configs.app_theme') }}",
          showRemove: false,
          showUpload: false,
          allowedFileExtensions: ["xlsx"],
          dropZoneEnabled: false,
          theme:'explorer-fas'
      });
      $(document).on("change", "#file", function () {
          if (!$.isEmptyObject($('#form-import').validate().submitted)) {
            $('#form-import').validate().form();
          }
      });
      table_item = $('#table-item').DataTable({
          responsive:true,
          filter:false,
          info:false,
          lengthChange:true,
          autoWidth:false,
          paging:true,
          order: [[ 7, "asc" ]],
          columnDefs: [
              {
                  orderable: false,targets:[3]
              },
              { className: "text-center", targets: [7] },
              { render: function ( data, type, row ) {
                  if (row[7] !== null) {
                    return '<span class="badge badge-success">Valid</span>';
                  } else {
                    return '<span class="badge badge-danger">Not Valid</span>';
                  }
                },targets: [7]
              }
          ],
      });

      $("#form-import").validate({
          errorElement: 'span',
          errorClass: 'help-block',
          focusInvalid: false,
          highlight: function (e) {
              $(e).closest('.form-group').removeClass('has-success').addClass('has-error');
          },

          success: function (e) {
              $(e).closest('.form-group').removeClass('has-error').addClass('has-success');
              $(e).remove();
          },
          errorPlacement: function (error, element) {
              if(element.is(':file')) {
                  error.insertAfter(element.closest('.file-input'));
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
                  url:"{{route('attendance.preview')}}",
                  method:'post',
                  data: new FormData($('#form-import')[0]),
                  processData: false,
                  contentType: false,
                  dataType: 'json',
                  beforeSend:function(){
                      $('#select-file .overlay').removeClass('d-none');
                  }
              }).done(function(response){
                  $('#select-file .overlay').addClass('d-none');
                  $("#select-file").modal('hide');
                  items = {};
                  $.each(response.data,function(){
                      items[this.index] = this;
                  });
                  table_item.clear().draw();
                  // loadItem2(table_item);
                  loadItem(table_item);
              })
          }
      });
      $("#form").validate({
          errorElement: 'span',
          errorClass: 'help-block',
          focusInvalid: false,
          highlight: function (e) {
              $(e).closest('.form-group').removeClass('has-success').addClass('has-error');
          },
          success: function (e) {
              $(e).closest('.form-group').removeClass('has-error').addClass('has-success');
              $(e).remove();
          },
          errorPlacement: function (error, element) {
              if(element.is(':file')) {
                  error.insertAfter(element.parent());
              }else if(element.parent('.input-group').length) {
                  error.insertAfter(element.parent());
              } 
              else{
                  error.insertAfter(element);
              }
          },
          submitHandler: function() { 
              if (count == 0) {
                  $.gritter.add({
                      title: 'Warning',
                      text:  'No items have been added yet',
                      class_name: 'gritter-error',
                  }); 
                  return false;
              }
              var attendance =[];
              $.each(items, function() {
                attendance.push(this);
              });
              $.ajax({
                  url:$('#form').attr('action'),
                  dataType: 'json',
                  type:'POST',
                  data: {
                      _token: "{{ csrf_token() }}",
                      attendance: JSON.stringify(attendance),
                      period: $("#date").val(),
                  },
                  beforeSend:function(){
                      $('#attendance-preview .overlay').removeClass('d-none');
                  }
              }).done(function(response){
                  $('.overlay').addClass('d-none');
                  if(response.status){
                    items = {};
                    table_item.clear().draw();
                    $('#no').attr('value',response.last);
                    loadItem(table_item);
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
      $(document).on('click', '.sync', function(){
        bootbox.confirm({
          buttons: {
            confirm: {
              label: '<i class="fa fa-check"></i>',
              className: 'btn-{{ config("configs.app_theme") }}'
            },
            cancel: {
              label: '<i class="fa fa-undo"></i>',
              className: 'btn-default'
            },
          },
          title: 'Synchronize Attendance From Machine?',
          message: 'Synchronizing data takes time, depending on the amount of data',
          callback: function(result) {
            if (result) {
              var data = {
                _token: "{{ csrf_token() }}",
                period: $("#date").val(),
                time: $("#time").val(),
                attendanceMachine: $("#attendanceMachine").val(),
                no: $("#no").val(),
              };
              $.ajax({
                url:"{{route('attendance.sync')}}",
                dataType: "JSON",
                data: data,
                type: "GET",
                beforeSend: function () {
                  $(".overlay").removeClass('d-none');
                }
              }).done(function (response) {
                if (response.status) {
                  $('.overlay').addClass('d-none');
                  //items = {};
                  $.each(response.data,function () {
                    items[this.index] = this;
                  });
                  table_item.clear().draw();
                  $('#no').attr('value',response.last);
                  loadItem(table_item);
                } else {
                  $.gritter.add({
                    title: 'Warning!',
                    text: response.message,
                    class_name: 'gritter-warning',
                    time: 1000,
                  });
                }
              }).fail(function (response) {
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
      })
  });
</script>
@endpush