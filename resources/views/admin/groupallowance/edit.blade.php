@extends('admin.layouts.app')

@section('title', __('config.grpalw'))
@section('stylesheets')
@endsection

@push('breadcrump')
<li class="breadcrumb-item"><a href="{{route('groupallowance.index')}}">{{ __('config.grpalw') }}</a></li>
<li class="breadcrumb-item active">{{ __('config.edt') }}</li>
@endpush

@section('content')
<div class="wrapper wrapper-content">
  <div class="row">
    <div class="col-lg-8">
      <div class="card card-{{ config('configs.app_theme') }} card-outline">
        <div class="card-header" style="height: 57px;">
          <h3 class="card-title">{{ __('config.edt') }} {{ __('config.grpalw') }}</h3>
        </div>
        <div class="card-body">
          <form id="form" action="{{ route('groupallowance.update',['id'=>$groupAllowance->id]) }}" method="post" autocomplete="off">
            {{ csrf_field() }}
            {{ method_field('put') }}
            <div class="row">
              <div class="col-sm-6">
                <!-- text input -->
                <div class="form-group">
                  <label>{{ __('config.code') }} <b class="text-danger">*</b></label>
                  <input type="text" class="form-control" name="code" value="{{ $groupAllowance->code }}" placeholder="{{ __('config.code') }}">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label>{{ __('config.alwgrp') }} <b class="text-danger">*</b></label>
                  <input type="text" class="form-control" name="group_allowance" value="{{ $groupAllowance->name }}" placeholder="{{ __('config.alwgrp') }}">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-6">
                <div class="form-group">
                  <label>{{ __('config.type') }} <b class="text-danger">*</b></label>
                  <select class="form-control select2" id="type" required name="type">
                    <option value="ADDITIONAL" @if($groupAllowance->group_type == 'ADDITIONAL') selected @endif>{{ __('config.add') }}</option>
                    <option value="DEDUCTION" @if($groupAllowance->group_type == 'DEDUCTION') selected @endif>{{ __('config.deduct') }}</option>
                  </select>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label>{{ __('config.status') }} <b class="text-danger">*</b></label>
                  <select class="form-control select2" id="status" required name="status">
                    <option value="1" @if($groupAllowance->status == '1') selected @endif>{{ __('config.actv') }}</option>
                    <option value="0" @if($groupAllowance->status == '0') selected @endif>{{ __('config.noactv') }}</option>
                  </select>
                </div>
              </div>
            </div>
        </div>
        <div class="overlay d-none">
          <i class="fa fa-refresh fa-spin"></i>
        </div>
        <div style="height: 10px;"></div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="card card-{{ config('configs.app_theme') }} card-outline">
        <div class="card-header">
          <h3 class="card-title">{{ __('config.other') }}</h3>
          <!-- tools box -->
          <div class="pull-right card-tools">
            <button form="form" type="submit" class="btn btn-sm btn-{{ config('configs.app_theme') }}" title="{{ __('config.save') }}"><i class="fa fa-save"></i></button>
            <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="{{ __('config.prvious') }}"><i class="fa fa-reply"></i></a>
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
                  <textarea class="form-control" name="notes" placeholder="{{ __('config.notes') }}" rows="5">{{ $groupAllowance->notes}}</textarea>
                </div>
              </div>
            </div>
             <div class="row">
              <div class="col-sm-12">
                <div class="form-group">
                  <label>Coordinate Slip</label>
                  <select name="coordinate" id="coordinate" class="form-control select2" Placeholder="Coordinate" style="width: 100%" aria-hidden="true">
                    <option value=""></option>
                    <option value="1.2" @if ($groupAllowance->coordinate == "1.2") selected @endif>1.2</option>
                    <option value="1.3" @if ($groupAllowance->coordinate == "1.3") selected @endif>1.3</option>
                    <option value="1.4" @if ($groupAllowance->coordinate == "1.4") selected @endif>1.4</option>
                    <option value="4.3" @if ($groupAllowance->coordinate == "4.3") selected @endif>4.3</option>
                    <option value="4.4" @if ($groupAllowance->coordinate == "4.4") selected @endif>4.4</option>
                    <option value="4.5" @if ($groupAllowance->coordinate == "4.5") selected @endif>4.5</option>
                    <option value="5.4" @if ($groupAllowance->coordinate == "5.4") selected @endif>5.4</option>
                    <option value="5.5" @if ($groupAllowance->coordinate == "5.5") selected @endif>5.5</option>
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
</div>
@endsection

@push('scripts')
<script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
<script>
  $(document).ready(function(){
      $('.select2').select2();
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