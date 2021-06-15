@extends('admin.layouts.app')

@section('title',__('consumeoil.consume'))
@section('stylesheets')
<link href="{{asset('adminlte/component/daterangepicker/daterangepicker.css')}}" rel="stylesheet">
@endsection

@push('breadcrump')
<li class="breadcrumb-item"><a href="{{route('department.index')}}">{{__('consumeoil.consume')}}</a></li>
<li class="breadcrumb-item active">{{__('general.crt')}}</li>
@endpush

@section('content')
<div class="wrapper wrapper-content">
<form id="form" action="{{ route('consumeoil.store') }}" method="post" autocomplete="off">

  <div class="row">
    <div class="col-lg-8">
      <div class="card card-{{ config('configs.app_theme') }} card-outline">
        <div class="card-header" style="height: 57px;">
          <h3 class="card-title">{{__('consumeoil.consume')}}</h3>
        </div>
        <div class="card-body">
            {{ csrf_field() }}
            <div class="d-flex">
              <div class="col-sm-6">
                <!-- text input -->
                <div class="form-group">
                    <label class="control-label" for="date">{{__('general.date')}}</label>
                    <div class="controls">
                      <div class="input-group">
                        <div class="input-group-prepend">
                          <span class="input-group-text">
                            <i class="far fa-calendar-alt"></i>
                          </span>
                        </div>
                        <input type="text" name="date" id="date" class="form-control datepicker filter" placeholder="{{__('general.date')}}">
                      </div>
                    </div>
                  </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label>{{__('consumeoil.vehicle')}} <b class="text-danger">*</b></label>
                  <input type="text" class="form-control" id="vehicle_id"name="vehicle_id" placeholder="{{__('consumeoil.vehicle')}}">
                </div>
              </div>
            </div>
            <div class="d-flex">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Oil <b class="text-danger">*</b></label>
                        <input class="form-control" id="oil_id" data-placeholder="Oil" name="oil_id">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>{{__('consumeoil.initial')}}<b class="text-danger"></b></label>
                        <input class="form-control" id="stock" data-placeholder="{{__('consumeoil.initial')}}" name="stock" readonly>
                    </div>
                </div>
            </div>
            {{-- </div> --}}
            <div class="d-flex">
                <div class="col-sm-6">
                    <div class="form-group">
                    <label>{{__('consumeoil.engineoil')}} <b class="text-danger">*</b></label>
                    <input class="form-control engineoil" id="engine_oil" data-placeholder="{{__('consumeoil.engineoil')}}" name="engine_oil">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                    <label>Km<b class="text-danger">*</b></label>
                    <input class="form-control" id="km" data-placeholder="Km" name="km">
                    </div>
                </div>
            </div>
            <div class="d-flex">
              <div class="col-sm-6">
                <!-- text input -->
                <div class="form-group">
                  <label>{{__('consumeoil.driver')}} <b class="text-danger">*</b></label>
                   <input class="form-control" id="driver" data-placeholder="{{__('consumeoil.driver')}}" name="driver">
                </div>
              </div>
              <div class="col-sm-6">
                <!-- text input -->
                <div class="form-group">
                  <label>{{__('consumeoil.type')}} <b class="text-danger">*</b></label>
                   <select name="type" id="type" class="form-control select2" data-placeholder="{{__('general.chs')}} {{__('consumeoil.type')}}">
                        <option value="Add Oil">{{__('consumeoil.addoil')}}</option>
                        <option value="Change Oil">{{__('consumeoil.change')}}</option>
                    </select>
                </div>
              </div>
            </div>
           
            <div style="height: 23px;"></div>
        </div>
        <div class="overlay d-none">
          <i class="fa fa-2x fa-sync-alt fa-spin"></i>
        </div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="card card-{{ config('configs.app_theme') }} card-outline" style="height: 97%">
        <div class="card-header">
          <h3 class="card-title">{{__('general.other')}}</h3>
          <div class="pull-right card-tools">
            <button form="form" type="submit" class="btn btn-sm btn-{{ config('configs.app_theme') }}" title="{{__('general.save')}}"><i
                class="fa fa-save"></i></button>
            <a href="javascript:void(0)" onclick="backurl()" class="btn btn-sm btn-default" title="{{__('general.prvious')}}"><i
                class="fa fa-reply"></i></a>
          </div>
        </div>
        <div class="card-body">
            <div class="row">
              <div class="col-sm-12">
                <!-- text input -->
                <div class="form-group">
                  <label>{{__('general.notes')}}</label>
                  <textarea style="width:310px; height:150px;" class="form-control" name="note" placeholder="{{__('general.notes')}}"></textarea>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-12">
                <!-- text input -->
                <div class="form-group">
                  <label>Status</label>
                  <div class="col-sm-12">
                    <input class="form-control" type="radio" name="status" value="0" checked> <i></i>
                    <label class="p-2">{{__('consumeoil.draft')}}</label>
                    <input class="form-control" type="radio" name="status" value="1"> <i></i>
                    <label class="p-2">{{__('consumeoil.publish')}}</label>
                 </div>
                </div>
              </div>
            </div>
          
        </div>
        
        
      </div>
    </div>
    </form>
    {{-- <div class="overlay d-none">
          <i class="fa fa-2x fa-sync-alt fa-spin"></i>
        </div> --}}
  </div>
  @endsection

  @push('scripts')
  <script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
  <script src="{{asset('adminlte/component/daterangepicker/moment.min.js')}}"></script>
<script src="{{asset('adminlte/component/daterangepicker/daterangepicker.js')}}"></script>
  <script>
    $(document).ready(function(){
        $(".select2").select2();
        $('input[name=status]').iCheck({
            checkboxClass: 'icheckbox_square-green',
            radioClass: 'iradio_square-green',
        });
          $('#date').daterangepicker({
            singleDatePicker: true,
            timePicker: false,
            timePickerIncrement: 1,
            locale: {
            format: 'YYYY/MM/DD'
            }
          });
            $('#date').on('change', function(){
                if (!$.isEmptyObject($(this).closest("form").validate())) {
                    $(this).closest("form").validate().form();
                }
            });
          $( "#oil_id" ).select2({
            ajax: {
              url: "{{route('consumeoil.readoil')}}",
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
                    stock: item.stock
                  });
                });
                return {
                  results: option, more: more,
                };
              },
            },
            allowClear: true,
          });
          
          $(document).on("change", "#oil_id", function () {
              
            var stock = $(this).select2('data').stock;
            // alert(stock);
            $('#stock').attr("value",stock);
        
            if (!$.isEmptyObject($('#form').validate().submitted)) {
              $('#form').validate().form();
            }
          });
          $(document).on('keyup',".engineoil",function(){
           var input = $(this);
            var stock = parseInt($('input[name=stock]').val());
            var engineoil = parseInt($('input[name=engine_oil]').val());
            // console.log(stock);
            if (engineoil > stock) {
                $.gritter.add({
				    title: 'Warning',
				    text:  'Can not Input Engine Oil',
			    });
                $(input).val(0);
                return;
            }

           });
          $( "#vehicle_id" ).select2({
            ajax: {
              url: "{{route('consumeoil.readvehicle')}}",
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
          $(document).on("change", "#vehicle_id", function () {
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