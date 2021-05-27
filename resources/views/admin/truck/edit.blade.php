@extends('admin.layouts.app')

@section('title', 'Edit Truck')
@section('stylesheets')
@endsection

@push('breadcrump')
<li class="breadcrumb-item"><a href="{{route('truck.index')}}">Truck</a></li>
<li class="breadcrumb-item active">Edit</li>
@endpush

@section('content')
<div class="wrapper wrapper-content">
  <div class="row">
    <div class="col-lg-8">
      <div class="card card-{{ config('configs.app_theme') }} card-outline">
        <div class="card-header" style="height: 57px;">
          <h3 class="card-title">Edit Truck</h3>
        </div>
        <div class="card-body">
          <form id="form" action="{{ route('truck.update',['id'=>$truck->id]) }}" method="post"
            autocomplete="off">
            {{ csrf_field() }}
            {{ method_field('put') }}
            <div class="row">
              <div class="col-sm-6">
                <!-- text input -->
                <div class="form-group">
                  <label>Code <b class="text-danger">*</b></label>
                  <input type="text" class="form-control" name="code" value="{{ $truck->code }}"
                    placeholder="Code" readonly>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label>Name <b class="text-danger">*</b></label>
                  <input type="text" class="form-control" name="name" value="{{ $truck->name }}"
                    placeholder="Name">
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
          <!-- tools box -->
          <div class="pull-right card-tools">
            <button form="form" type="submit" class="btn btn-sm btn-{{ config('configs.app_theme') }}" title="Simpan"><i
                class="fa fa-save"></i></button>
            <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i
                class="fa fa-reply"></i></a>
          </div>
          <!-- /. tools -->
        </div>
        <div class="card-body">
          <form role="form">
            <div class="row">
              <div class="col-sm-12">
                <!-- text input -->
                <div class="form-group">
                  <label>Notes <b class="text-danger">*</b></label>
                  <textarea class="form-control" name="notes" placeholder="Notes">{{ $truck->notes}}</textarea>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-12">
                <div class="form-group">
                  <label>Status <b class="text-danger">*</b></label>
                  <select class="form-control" id="status" required name="status">
                    <option value="1" @if($truck->status == '1') selected @endif>Active</option>
                    <option value="0" @if($truck->status == '0') selected @endif>Tidak Active</option>
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
</div>
@endsection

@push('scripts')
<script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
<script>
  $(document).ready(function(){
      $('.select2').select2();
      $('#status').select2();
     
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
            bootbox.confirm({
              buttons: {
                confirm: {
                  label: '<i class="fa fa-check"></i>',
                  className: `btn-{{ config('configs.app_theme') }}`
                },
                cancel: {
                  label: '<i class="fa fa-undo"></i>',
                  className: 'btn-default'
                },
              },
              title:'Save the update?',
              message:'Are you sure to save the changes?',
              callback: function(result) { 
                if (result) {
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
                  });
                }
              }
            });
        }
      });
    });
</script>
@endpush