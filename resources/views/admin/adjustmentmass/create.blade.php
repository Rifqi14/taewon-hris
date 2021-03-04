@extends('admin.layouts.app')

@section('title', 'Adjustment Mass ')
@section('stylesheets')
<link href="{{asset('adminlte/component/daterangepicker/daterangepicker.css')}}" rel="stylesheet">
@endsection

@push('breadcrump')
<li class="breadcrumb-item"><a href="{{route('adjustmentmass.index')}}">Adjustment Mass</a></li>
<li class="breadcrumb-item active">Create</li>
@endpush

@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="card card-{{ config('configs.app_theme') }} card-outline">
      <div class="card-header">
        <h3 class="card-title">Create Adjustment Mass</h3>
        <div class="pull-right card-tools">
          <button form="form" type="submit" class="btn btn-sm btn-{{ config('configs.app_theme') }}" title="Simpan"><i
              class="fa fa-save"></i></button>
          <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i
              class="fa fa-reply"></i></a>
        </div>
      </div>
      <div class="card-body">
        <form id="form" action="{{ route('adjustmentmass.store') }}" class="form-horizontal" method="post" autocomplete="off">
          {{ csrf_field() }}
          <div class="row">
              <div class="col-lg-12">
                  <div class="row">
                  <div class="col-lg-6">
                      <div class="form-group row">
                          <label class="col-sm-4 col-form-label" for="employee_name">Employee</label>
                          <input type="text" class="form-control col-sm-8" name="employee_id" id="employee_id" data-placeholder="Employee">
                      </div>
                      <div class="form-group row">
                          <label class="col-sm-4 col-form-label">Adjustment Working Time</label>
                          <input type="text" class="form-control col-sm-8" placeholder="0" name="adjustment_workingtime" id="adjustment_workingtime" required>
                      </div>
                  </div>
                  <div class="col-lg-6">
                      <div class="form-group row">
                          <label class="col-sm-4 col-form-label" style="margin-right: -7px;">Date</label>
                          <div class="col-sm-8 controls">
                              <div class="input-group">
                              <div class="input-group-prepend">
                                  <span class="input-group-text">
                                  <i class="far fa-calendar-alt"></i>
                                  </span>
                              </div>
                              <input type="text" name="date" class="form-control datepicker" placeholder="Date" required />
                              </div>
                          </div>
                      </div>
                      <div class="form-group row">
                          <label class="col-sm-4 col-form-label">Adjustment Overtime</label>
                          <input type="text" class="form-control col-sm-8" placeholder="0" name="adjustment_overtime" id="adjustment_overtime" required>
                      </div>
                  </div>
                  </div>
              </div>
              <div style="height: 23px;"></div>
          </div>
        </form>
      </div>
      <div class="overlay d-none">
        <i class="fa fa-2x fa-sync-alt fa-spin"></i>
      </div>
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
          $('.datepicker').daterangepicker({
            singleDatePicker: true,
            timePicker: false,
            timePickerIncrement: 1,
            locale: {
            format: 'D/M/Y'
            }
          });
          $( "#employee_id" ).select2({
            multiple: true,
            ajax: {
              url: "{{route('employees.select')}}",
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
          $(document).on("change", "#employee_id", function () {
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
                    text: 'Warning!',
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
                  text: 'Error',
                  class_name: 'gritter-error',
                  time: 1000,
                });
              })
            }
          });
        });
  </script>
  @endpush