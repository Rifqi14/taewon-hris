@extends('admin.layouts.app')

@section('title',  __('config.dep'))
@section('stylesheets')
@endsection

@push('breadcrump')
<li class="breadcrumb-item"><a href="{{route('department.index')}}">{{ __('config.dep') }}</a></li>
<li class="breadcrumb-item active">{{ __('config.edt') }}</li>
@endpush

@section('content')
<div class="wrapper wrapper-content">
  <div class="row">
    <div class="col-lg-8">
      <div class="card card-{{ config('configs.app_theme') }} card-outline">
        <div class="card-header" style="height: 57px;">
          <h3 class="card-title">{{ __('config.edt') }} {{ __('config.dep') }}</h3>
        </div>
        <div class="card-body">
          <form id="form" action="{{ route('department.update',['id'=>$department->id]) }}" method="post"
            autocomplete="off">
            {{ csrf_field() }}
            {{ method_field('put') }}
            <div class="row">
              <div class="col-sm-6">
                <!-- text input -->
                <div class="form-group">
                  <label>{{ __('config.code') }} <b class="text-danger">*</b></label>
                  <input type="text" class="form-control" name="code" value="{{ $department->code }}"
                    placeholder="{{ __('config.code') }}" readonly>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label>{{ __('config.name') }} <b class="text-danger">*</b></label>
                  <input type="text" class="form-control" name="name" value="{{ $department->name }}"
                    placeholder="{{ __('config.name') }}">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-6">
                <!-- text input -->
                <div class="form-group">
                  <label>{{ __('config.parent') }} <b class="text-danger">*</b></label>
                  <input class="form-control" id="parent_id" data-placeholder="{{ __('config.chsparent') }}" name="parent_id"
                    value="">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label>Order Number <b class="text-danger">*</b></label>
                  <input type="number" class="form-control" name="order_number" value="{{ $department->order_number }}"
                    placeholder="Order Number">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-6">
                <div class="form-group">
                  <label>Dashboard <b class="text-danger">*</b></label>
                  <select class="form-control select2" id="dashboard" required name="dashboard">
                    <option value="no" @if($department->dashboard == 'no') selected @endif>No</option>
                    <option value="yes" @if($department->dashboard == 'yes') selected @endif>Yes</option>
                  </select>
                </div>
              </div>
              <div class="col-sm-6">
                <!-- text input -->
                <div class="form-group">
                  <label>Driver <b class="text-danger">*</b></label>
                  <select name="driver" id="driver" class="form-control select2">
                    <option value="no" @if($department->driver == 'no') selected @endif>No</option>
                    <option value="yes" @if($department->driver == 'yes') selected @endif>Yes</option>
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
          <h3 class="card-title">{{ __('config.other') }}</h3>
          <!-- tools box -->
          <div class="pull-right card-tools">
            <button form="form" type="submit" class="btn btn-sm btn-{{ config('configs.app_theme') }}" title="Simpan"><i
                class="fa fa-save"></i></button>
            <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="{{ __('config.prvious') }}"><i
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
                  <label>{{ __('config.notes') }} <b class="text-danger">*</b></label>
                  <textarea class="form-control" name="notes" placeholder="{{ __('config.notes') }}">{{ $department->notes}}</textarea>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-12">
                <div class="form-group">
                  <label>{{ __('config.status') }} <b class="text-danger">*</b></label>
                  <select class="form-control" id="status" required name="status">
                    <option value="1" @if($department->status == '1') selected @endif>{{ __('config.actv') }}</option>
                    <option value="0" @if($department->status == '0') selected @endif>{{ __('config.noactv') }}</option>
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
      @if($department->parent_id)
      $("#parent_id").select2('data',{id:{{$department->parent_id}},text:'{{$department->parent->name}}'}).trigger('change');
      @endif
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