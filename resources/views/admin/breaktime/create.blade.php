@extends('admin.layouts.app')

@section('title', 'Break Time ')
@section('stylesheets')
<link href="{{asset('adminlte/component/daterangepicker/daterangepicker.css')}}" rel="stylesheet">
@endsection

@push('breadcrump')
<li class="breadcrumb-item"><a href="{{route('breaktime.index')}}">Break Time</a></li>
<li class="breadcrumb-item active">Create</li>
@endpush

@section('content')
<div class="wrapper wrapper-content">
  <div class="row">
    <div class="col-lg-8">
      <div class="card card-{{ config('configs.app_theme') }} card-outline">
        <div class="card-header" style="height: 57px;">
          <h3 class="card-title">Break Time</h3>
        </div>
        <div class="card-body">
          <form id="form" action="{{ route('breaktime.store') }}" method="post" autocomplete="off">
            {{ csrf_field() }}
            <div class="row">
              <div class="col-sm-6">
                <div class="form-group">
                  <label>Break Time <b class="text-danger">*</b></label>
                  <input type="text" class="form-control" name="break_time" id="break_time" placeholder="Break Time">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label>Workgroup Combination <b class="text-danger">*</b></label>
                  <input type="text" class="form-control" name="workgroup" id="workgroup" data-placeholder="Workgroup Combination">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-6">
                <div class="form-group">
                  <label>Start Time<b class="text-danger">*</b></label>
                  <input class="form-control timepicker" id="start_time" name="start_time">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label>Finish Time<b class="text-danger">*</b></label>
                  <input class="form-control timepicker" id="finish_time" name="finish_time">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-6">
                <div class="form-group">
                  <label>Department <b class="text-danger">*</b></label>
                  <input type="text" class="form-control" id="department_id" name="department_id" placeholder="Select Department Parent" required>
                  <small class="form-text text-muted">This department will show only parent department. And create shift will apply to department child too.</small>
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
                  <textarea class="form-control" name="notes" placeholder="Notes"></textarea>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-12">
                <div class="form-group">
                  <label>Status <b class="text-danger">*</b></label>
                  <select name="status" id="status" class="form-control select2" data-placeholder="Select Status">
                    <option value=""></option>
                    <option value="1">Aktif</option>
                    <option value="0">Non Aktif</option>
                  </select>
                </div>
              </div>
            </div>
          </form>
        </div>
        <div class="overlay d-none">
          <i class="fa fa-2x fa-sync-alt fa-spin"></i>
        </div>
        </form>
      </div>
    </div>
  </div>
  @endsection

  @push('scripts')
  <script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
  <script src="{{asset('adminlte/component/daterangepicker/moment.min.js')}}"></script>
  <script src="{{asset('adminlte/component/daterangepicker/daterangepicker.js')}}"></script>
  <script>
    $(document).ready(function(){
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
          $('#department_id').select2({
            multiple: true,
            ajax: {
              url: "{{route('department.select')}}",
              type:'GET',
              dataType: 'json',
              data: function (term,page) {
                return {
                  name:term,
                  page:page,
                  limit:30,
                  level: 1,
                };
              },
              results: function (data,page) {
                var more = (page * 30) < data.total;
                var option = [];
                $.each(data.rows,function(index,item){
                  option.push({
                    id:`${item.path}`,
                    text: `${item.name}`
                  });
                });
                return {
                  results: option, more: more,
                };
              },
            },
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
        });
  </script>
  @endpush