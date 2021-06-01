@extends('admin.layouts.app')

@section('title', 'Customer ')
@section('stylesheets')
@endsection

@push('breadcrump')
<li class="breadcrumb-item"><a href="{{route('partner.index')}}">Customer</a></li>
<li class="breadcrumb-item active">Create</li>
@endpush

@section('content')
<div class="wrapper wrapper-content">
  <div class="row">
    <div class="col-lg-8">
      <div class="card card-{{ config('configs.app_theme') }} card-outline">
        <div class="card-header" style="height: 57px;">
          <h3 class="card-title">Customer Data</h3>
        </div>
        <div class="card-body">
          <form id="form" action="{{ route('partner.store') }}" method="post" autocomplete="off">
            {{ csrf_field() }}
            <div class="row">
              <div class="col-sm-6">
                <!-- text input -->
                <div class="form-group">
                  <label>Code</label>
                  <input type="text" class="form-control" name="code" id="code" placeholder="Code">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label>Name <b class="text-danger">*</b></label>
                  <input type="text" class="form-control" name="name" placeholder="Name" required>
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
                  <label>Number Phone</label>
                  <input class="form-control" type="number" id="phone" placeholder="Number Phone" name="phone">
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
										<label>Truck <b class="text-danger">*</b></label>
										<select name="truck_id" id="truck_id" class="form-control select2" style="width: 100%"
											aria-hidden="true" data-placeholder="Select Truck" required>
											<option value=""></option>
											@foreach ($trucks as $truck)
											<option value="{{ $truck->id }}">{{ $truck->name }}</option>
											@endforeach
										</select>
									</div>
              </div>
              <div class="col-sm-6">
									<div class="form-group">
										<label>Department <b class="text-danger">*</b></label>
										<select name="department_id" id="department_id" class="form-control select2" style="width: 100%"
											aria-hidden="true" data-placeholder="Select Truck" required>
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
          <h3 class="card-title">Other</h3>
          <div class="pull-right card-tools">
            <button form="form" type="submit" class="btn btn-sm btn-{{ config('configs.app_theme') }}" title="Simpan"><i
                class="fa fa-save"></i></button>
            <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i
                class="fa fa-reply"></i></a>
          </div>
        </div>
        <div class="card-body">
          <form role="form">
            <div class="row">
              <div class="col-sm-12">
                <!-- text input -->
                <div class="form-group">
                  <label>Address</label>
                  <textarea class="form-control" name="address" placeholder="Address" rows="4"></textarea>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-12">
                <div class="form-group">
                  <label>Status</label>
                  <select name="status" id="status" class="form-control">
                    <option value="1">Active</option>
                    <option value="0">Non Active</option>
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