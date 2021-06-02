@extends('admin.layouts.app')

@section('title',__('customer.cust'))
@section('stylesheets')
@endsection

@push('breadcrump')
<li class="breadcrumb-item"><a href="{{route('partner.index')}}">{{ __('customer.cust') }}</a></li>
<li class="breadcrumb-item active">{{ __('general.crt') }}</li>
@endpush

@section('content')
<div class="wrapper wrapper-content">
  <div class="row">
    <div class="col-lg-8">
      <div class="card card-{{ config('configs.app_theme') }} card-outline">
        <div class="card-header" style="height: 57px;">
          <h3 class="card-title">{{ __('general.crt') }} {{ __('customer.cust') }}</h3>
        </div>
        <div class="card-body">
          <form id="form" action="{{ route('partner.store') }}" method="post" autocomplete="off">
            {{ csrf_field() }}
            <div class="row">
              <div class="col-sm-6">
                <!-- text input -->
                <div class="form-group">
                  <label>{{ __('general.code') }}</label>
                  <input type="text" class="form-control" name="code" id="code" placeholder="{{ __('general.code') }}">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label>{{ __('general.name') }} <b class="text-danger">*</b></label>
                  <input type="text" class="form-control" name="name" placeholder="{{ __('general.name') }}" required>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-6">
                <!-- text input -->
                <div class="form-group">
                  <label>Rit <b class="text-danger">*</b></label>
                  <input class="form-control" id="rit" placeholder="Rit" name="rit" required>
                </div>
              </div>
              <div class="col-sm-6">
                <!-- text input -->
                <div class="form-group">
                  <label>{{ __('customer.nohp') }}</label>
                  <input class="form-control" type="number" id="phone" placeholder="{{ __('customer.nohp') }}" name="phone">
                </div>
              </div>
              <div class="col-sm-6">
                <!-- text input -->
                <div class="form-group">
                  <label>Email</label>
                  <input class="form-control" type="email" id="email" placeholder="Email" name="email">
                </div>
                
              </div>
              <div class="col-sm-6">
									<div class="form-group">
										<label>{{ __('general.truck') }} <b class="text-danger">*</b></label>
										<select name="truck_id" id="truck_id" class="form-control select2" style="width: 100%"
											aria-hidden="true" data-placeholder="{{ __('general.chs') }} {{ __('general.truck') }}" required>
											<option value=""></option>
											@foreach ($trucks as $truck)
											<option value="{{ $truck->id }}">{{ $truck->name }}</option>
											@endforeach
										</select>
									</div>
              </div>
              <div class="col-sm-6">
									<div class="form-group">
										<label>{{ __('department.dep') }} <b class="text-danger">*</b></label>
										<select name="department_id" id="department_id" class="form-control select2" style="width: 100%"
											aria-hidden="true" data-placeholder="{{ __('general.chs') }} {{ __('department.dep') }}" required>
											<option value=""></option>
											@foreach ($departments as $department)
											<option value="{{ $department->id }}">{{ $department->name }}</option>
											@endforeach
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
          <h3 class="card-title">{{ __('general.other') }}</h3>
          <div class="pull-right card-tools">
            <button form="form" type="submit" class="btn btn-sm btn-{{ config('configs.app_theme') }}" title="{{ __('general.save') }}"><i
                class="fa fa-save"></i></button>
            <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="{{ __('general.prvious') }}"><i
                class="fa fa-reply"></i></a>
          </div>
        </div>
        <div class="card-body">
          <form role="form">
            <div class="row">
              <div class="col-sm-12">
                <!-- text input -->
                <div class="form-group">
                  <label>{{ __('general.address') }}</label>
                  <textarea class="form-control" name="address" placeholder="{{ __('general.address') }}" rows="4"></textarea>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-12">
                <div class="form-group">
                  <label>Status</label>
                  <select name="status" id="status" class="form-control">
                    <option value="1">{{ __('general.actv') }}</option>
                    <option value="0">{{ __('general.noactv') }}</option>
                  </select>
                </div>
              </div>
            </div>
          </form>
          <div style="height: 10px;"></div>
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
         $('#status').select2();
         $('#truck_id').select2();
         $('#department_id').select2();
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