@extends('admin.layouts.app')

@section('title', 'Create Calendar')
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/css/fileinput.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css')}}" rel="stylesheet">
@endsection

@push('breadcrump')
<li class="breadcrumb-item active"><a href="{{route('calendar.index')}}">Calendar</a></li>
<li class="breadcrumb-item active">Create</li>
@endpush

@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="card card-{{ config('configs.app_theme') }} card-outline">
      <div class="card-header">
        <h3 class="card-title">Create Calendar</h3>
        <div class="pull-right card-tools">
          <button form="form" type="submit" class="btn btn-sm btn-{{ config('configs.app_theme') }}" title="Simpan"><i
              class="fa fa-save"></i></button>
          <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i
              class="fa fa-reply"></i></a>
        </div>
      </div>
      <div class="card-body">
        <form id="form" action="{{ route('calendar.store') }}" class="form-horizontal" method="post" autocomplete="off">
          {{ csrf_field() }}
          <div class="form-group row">
            <label class="col-md-2 col-xs-12 control-label" for="code">Calendar Code <span
                class="text-red">*</span></label>
            <div class="col-sm-6 controls">
              <input type="text" class="form-control" id="code" name="code" placeholder="Calendar Code" required>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-md-2 col-xs-12 control-label" for="calendar_name">Calendar Name <span
                class="text-red">*</span></label>
            <div class="col-sm-6 controls">
              <input type="text" class="form-control" id="calendar_name" name="calendar_name"
                placeholder="Calendar Name" required>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-sm-2 col-xs-12 control-label" for="label_color">Label Color</label>
            <div class="col-sm-6 controls">
              <div class="input-group my-colorpicker2">
                <input type="text" class="form-control" value="#000" name="label_color" id="label_color">
                <div class="input-group-append">
                  <span class="input-group-text input-group-addon">
                    <i></i>
                  </span>
                </div>
              </div>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-md-2 col-xs-12 control-label" for="calendar_desc">Calendar Description</label>
            <div class="col-sm-6 controls">
              <textarea class="form-control" id="calendar_desc" name="calendar_desc"
                placeholder="Calendar Description"></textarea>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-md-2 col-xs-12 control-label" for="is_default">Active</label>
            <div class="col-sm-6 controls">
              <input type="checkbox" class="custom-control-input i-checks" name="is_default" id="is_default">
            </div>
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
<script src="{{asset('adminlte/component/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js')}}"></script>
<script>
  $(document).ready(function() {
    $('.my-colorpicker2').each(function () {
      $(this).colorpicker();
    });
    $('.i-checks').iCheck({
      checkboxClass: 'icheckbox_square-green',
      radioClass: 'iradio_square-green',
    });
    $('#form').validate({
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
        });
      }
    });
  });
</script>
@endpush