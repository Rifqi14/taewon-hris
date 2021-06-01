@extends('admin.layouts.app')

@section('title', __('department.dep'))
@section('stylesheets')
@endsection

@push('breadcrump')
<li class="breadcrumb-item"><a href="{{route('department.index')}}">{{ __('department.dep') }}</a></li>
<li class="breadcrumb-item active">{{ __('general.crt') }}</li>
@endpush

@section('content')
<div class="wrapper wrapper-content">
  <div class="row">
    <div class="col-lg-8">
      <div class="card card-{{ config('configs.app_theme') }} card-outline">
        <div class="card-header" style="height: 57px;">
          <h3 class="card-title">{{ __('department.depdata') }}</h3>
        </div>
        <div class="card-body">
          <form id="form" action="{{ route('department.store') }}" method="post" autocomplete="off">
            {{ csrf_field() }}
            <div class="row">
              <div class="col-sm-6">
                <!-- text input -->
                <div class="form-group">
                  <label>{{ __('general.code') }} <b class="text-danger">*</b></label>
                  <input type="text" class="form-control" name="code" id="code" placeholder="{{ __('general.code') }}">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label>{{ __('general.name') }} <b class="text-danger">*</b></label>
                  <input type="text" class="form-control" name="name" placeholder="{{ __('general.name') }}">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-6">
                <!-- text input -->
                <div class="form-group">
                  <label>{{ __('department.parent') }} <b class="text-danger">*</b></label>
                  <input class="form-control" id="parent_id" data-placeholder="{{ __('department.chsparent') }}" name="parent_id">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label>Order Number <b class="text-danger"></b></label>
                  <input type="number" class="form-control" name="order_number" placeholder="Order Number">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-6">
                <!-- text input -->
                <div class="form-group">
                  <label>Dashboard <b class="text-danger">*</b></label>
                  <select name="dashboard" id="dashboard" class="form-control select2">
                    <option value="no">No</option>
                    <option value="yes">Yes</option>
                  </select>
                </div>
              </div>
              <div class="col-sm-6">
                <!-- text input -->
                <div class="form-group">
                  <label>Driver <b class="text-danger">*</b></label>
                  <select name="driver" id="driver" class="form-control select2">
                    <option value="no">No</option>
                    <option value="yes">Yes</option>
                  </select>
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
          <h3 class="card-title">{{ __('department.other') }}</h3>
          <div class="pull-right card-tools">
            <button form="form" type="submit" class="btn btn-sm btn-{{ config('configs.app_theme') }}" title="Simpan"><i class="fa fa-save"></i></button>
            <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="{{ __('general.prvious') }}"><i class="fa fa-reply"></i></a>
          </div>
        </div>
        <div class="card-body">
          <form role="form">
            <div class="row">
              <div class="col-sm-12">
                <!-- text input -->
                <div class="form-group">
                  <label>{{ __('department.notes') }} <b class="text-danger">*</b></label>
                  <textarea class="form-control" name="notes" placeholder="{{ __('department.notes') }}"></textarea>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-12">
                <div class="form-group">
                  <label>{{ __('department.status') }} <b class="text-danger">*</b></label>
                  <select name="status" id="status" class="form-control" data-placeholder="Select Status">
                    <option value="1">{{ __('general.actv') }}</option>
                    <option value="0">{{ __('general.noactv') }}</option>
                  </select>
                </div>
              </div>
            </div>
            <div style="height: 60px;"></div>
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
  <script>
    $(document).ready(function(){
          $('.select2').select2();
          $("#status").select2();
          $( "#parent_id" ).select2({
            ajax: {
              url: "{{route('department.select')}}",
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
          $(document).on("change", "#parent_id", function () {
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