@extends('admin.layouts.app')

@section('title', __('salarydeduction.slrdeduc'))
@section('stylesheets')
<link href="{{asset('adminlte/component/daterangepicker/daterangepicker.css')}}" rel="stylesheet">
@endsection

@push('breadcrump')
<li class="breadcrumb-item"><a href="{{route('salarydeduction.index')}}">{{__('salarydeduction.slrdeduc')}}</a></li>
<li class="breadcrumb-item active">Create</li>
@endpush

@section('content')
<div class="wrapper wrapper-content">
  <div class="row">
    <div class="col-lg-8">
      <div class="card card-{{ config('configs.app_theme') }} card-outline">
        <div class="card-header" style="height: 57px;">
          <h3 class="card-title">{{__('salarydeduction.slrdeduc')}}</h3>
        </div>
        <div class="card-body" >
          <form id="form" action="{{ route('salarydeduction.store') }}" method="post" autocomplete="off">
            {{ csrf_field() }}
            <div class="row">
              <div class="col-sm-6">
                <!-- text input -->
                <div class="form-group">
                  <label>{{__('employee.empname')}} <b class="text-danger">*</b></label>
                  <input type="text" class="form-control select2" required name="employee_id" id="employee_id" placeholder="{{__('general.chs')}} {{__('employee.employ')}}">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label>NIK <b class="text-danger">*</b></label>
                  <input type="text" class="form-control" id="nik" name="nik" placeholder="NIK" readonly required>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-6">
                <!-- text input -->
                <div class="form-group">
                  <label>{{__('employee.position')}} <b class="text-danger">*</b></label>
                  <input type="text" class="form-control select2" required name="position" id="position" readonly placeholder="{{__('employee.position')}}">
                  <input type="hidden" id="title_id" name="title_id">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label>{{__('department.dep')}} <b class="text-danger">*</b></label>
                  <input type="text" class="form-control" name="department" id="department" placeholder="{{__('department.dep')}}" readonly required>
                  <input type="hidden" id="department_id" name="department_id">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-6">
                <div class="form-group">
                  <label>{{__('workgroup.workgrp')}} <b class="text-danger">*</b></label>
                  <input type="text" class="form-control" name="workgroup" id="workgroup" placeholder="{{__('workgroup.workgrp')}}" readonly required>
                  <input type="hidden" id="workgroup_id" name="workgroup_id">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label>Nominal <b class="text-danger">*</b></label>
                  <input type="number" class="form-control" name="nominal" placeholder="Nominal" value="0" required>
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
          <h3 class="card-title">{{__('general.other')}}</h3>
          <div class="pull-right card-tools">
            <button form="form" type="submit" class="btn btn-sm btn-{{ config('configs.app_theme') }}" title="{{__('general.save')}}"><i
                class="fa fa-save"></i></button>
            <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="{{__('general.prvious')}}"><i
                class="fa fa-reply"></i></a>
          </div>
        </div>
        <div class="card-body" style="height:289px">
          <form role="form">
            <div class="row">
              <div class="col-sm-12">
                <!-- text input -->
                <div class="form-group">
                  <label>{{__('salarydeduction.date')}}<b class="text-danger">*</b></label>
                  <div class="col-sm-12">
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text">
                          <i class="far fa-calendar-alt"></i>
                        </span>
                      </div>
                      <input type="text" name="date" id="date" class="form-control datepicker" placeholder="{{__('salarydeduction.date')}}" required />
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-12">
                <!-- text input -->
                <div class="form-group">
                  <label>{{__('general.desc')}} <b class="text-danger">*</b></label>
                  <textarea class="form-control" name="description" placeholder="{{__('general.desc')}}" required></textarea>
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
      
      $('#employee_id').select2({
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
                  text: `${item.name}`,
                  nik: item.nid,
                  department_id: item.department_id,
                  title_id: item.title_id,
                  workgroup_id: item.workgroup_id,
                  department: item.department_name,
                  title: item.title_name,
                  workgroup: item.workgroup_name
                  });
                });
                return {
                  results: option, more: more,
              };
              },
          },
      });
      $(document).on("change", "#employee_id", function () {
        var nik = $(this).select2('data').nik;
        var title_id = $(this).select2('data').title_id;
        var department_id = $(this).select2('data').department_id;
        var workgroup_id = $(this).select2('data').workgroup_id;
        var department = $(this).select2('data').department;
        var title = $(this).select2('data').title;
        var workgroup = $(this).select2('data').workgroup;
            // alert(stock);
        $('#nik').attr("value",nik);
        $('#title_id').attr("value",title_id);
        $('#department_id').attr("value",department_id);
        $('#workgroup_id').attr("value",workgroup_id);
        $('#department').attr("value",department);
        $('#position').attr("value",title);
        $('#workgroup').attr("value",workgroup);

        if (!$.isEmptyObject($('#form').validate().submitted)) {
          $('#form').validate().form();
        }
      });
      $('.datepicker').daterangepicker({
        singleDatePicker: true,
        timePicker: false,
        timePickerIncrement: 1,
        locale: {
        format: 'YYYY/MM/DD',
        }
      });
      $(document).on("change", "#date", function () {
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