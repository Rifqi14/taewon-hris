@extends('admin.layouts.app')

@section('title', __('allowancemass.alwincr'))
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/css/fileinput.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/daterangepicker/daterangepicker.css')}}" rel="stylesheet">
@endsection

@push('breadcrump')
<li class="breadcrumb-item active"><a href="{{route('allowanceincrease.index')}}">{{__('allowancemass.alwincr')}}</a></li>
<li class="breadcrumb-item active">{{__('general.edt')}}</li>
@endpush


@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="row">
            <div class="col-lg-8">
                <div class="card card-{{ config('configs.app_theme') }} card-outline">
                    <div class="card-header" style="height:55px;">
                        <h3 class="card-title">{{__('allowancemass.alwincdata')}}</h3>
                    </div>
                    <div class="card-body">
                        <form id="form" action="{{ route('allowanceincrease.update', ['id'=>$allowanceincrease->id]) }}" method="post" autocomplete="off">
                            {{ csrf_field() }}
                            {{ method_field('put') }}
                            <div class="row">
                                {{-- <div class="col-sm-6">
                                    <!-- text input -->
                                    <div class="form-group">
                                        <label>Ref No</label>
                                        <input type="text" class="form-control" placeholder="Ref No" name="ref">
                                    </div>
                                </div> --}}
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label pull-right col-md-2" for="period">{{__('general.period')}}</label>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <select class="form-control select2" name="month" id="month">
                                                    <option value="01" @if ($allowanceincrease->month == "01" ) selected @endif>{{__('general.jan')}}</option>
                                                    <option value="02" @if ($allowanceincrease->month == "02" ) selected @endif>{{__('general.feb')}}</option>
                                                    <option value="03" @if ($allowanceincrease->month == "03" ) selected @endif>{{__('general.march')}}</option>
                                                    <option value="04" @if ($allowanceincrease->month == "04" ) selected @endif>{{__('general.apr')}}</option>
                                                    <option value="05" @if ($allowanceincrease->month == "05" ) selected @endif>{{__('general.may')}}</option>
                                                    <option value="06" @if ($allowanceincrease->month == "06" ) selected @endif>{{__('general.jun')}}</option>
                                                    <option value="07" @if ($allowanceincrease->month == "07" ) selected @endif>{{__('general.jul')}}</option>
                                                    <option value="08" @if ($allowanceincrease->month == "08" ) selected @endif>{{__('general.aug')}}</option>
                                                    <option value="09" @if ($allowanceincrease->month == "09" ) selected @endif>{{__('general.sep')}}</option>
                                                    <option value="10" @if ($allowanceincrease->month == "10" ) selected @endif>{{__('general.oct')}}</option>
                                                    <option value="11" @if ($allowanceincrease->month == "11" ) selected @endif>{{__('general.nov')}}</option>
                                                    <option value="12" @if ($allowanceincrease->month == "12" ) selected @endif>{{__('general.dec')}}</option>
                                                </select>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="input-group">
                                                    <select name="year" class="form-control select2" id="year">
                                                        @php
                                                        $thn_skr = date('Y');
                                                        @endphp
                                                        @for ($i = $thn_skr; $i >= 1991; $i--)
                                                        <option value="{{ $i }}" @if ($allowanceincrease->year == $i) selected @endif>{{ $i }}</option>
                                                        @endfor
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <!-- text input -->
                                    <div class="form-group">
                                        <label>{{__('allowance.alw')}}</label>
                                        <input type="text" name="allowance_id" id="allowance_id" class="form-control select2" placeholder="{{__('general.chs')}} {{__('allowance.alw')}}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>{{__('general.value')}}</label>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <select class="form-control select2" data-placeholder="Type" name="type_value"
                                                    id="type">
                                                    <option value=""></option>
                                                    <option @if($allowanceincrease->type_value == "Percentage") selected @endif value="Percentage">{{__('general.percen')}}</option>
                                                    <option @if($allowanceincrease->type_value == "Nominal" ) selected @endif value="Nominal">{{__('general.nom')}}</option>
                                                </select>
                                            </div>
                                            <div class="col-sm-6">
                                                <input type="text" name="value" class="form-control"
                                                    placeholder="{{__('general.value')}}" value="{{$allowanceincrease->value}}">
                                            </div>
                                        </div>
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
                            <button form="form" type="submit" class="btn btn-sm btn-{{ config('configs.app_theme') }}"
                                title="{{__('general.save')}}"><i class="fa fa-save"></i></button>
                            <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="{{__('general.prvious')}}"><i
                                    class="fa fa-reply"></i></a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>{{__('general.notes')}}</label>
                                    <textarea style="height:110px;" type="text" class="form-control" name="note"
                                        placeholder="{{__('general.notes')}}" value="{{$allowanceincrease->note}}">{{$allowanceincrease->note}}</textarea>
                                </div>
                            </div>
                        </div>
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
    <script src="{{asset('adminlte/component/dataTables/js/datatables.min.js')}}"></script>
    <script src="{{asset('adminlte/component/bootstrap-fileinput/js/fileinput.min.js')}}"></script>
    <script src="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.js')}}"></script>
    <script src="{{asset('adminlte/component/daterangepicker/moment.min.js')}}"></script>
    <script src="{{asset('adminlte/component/daterangepicker/daterangepicker.js')}}"></script>
    <script>
        $(document).ready(function () {
           
            $('#type').select2({
                allowClear: true,
            });
            $('#month').select2({
                allowClear: true,
            });
            $('#year').select2({
                allowClear: true,
            });

            //select Allowance
            $("#allowance_id").select2({
                ajax: {
                    url: "{{route('allowance.select')}}",
                    type: 'GET',
                    dataType: 'json',
                    data: function (term, page) {
                        return {
                            name: term,
                            page: page,
                            limit: 30,
                        };
                    },
                    results: function (data, page) {
                        var more = (page * 30) < data.total;
                        var option = [];
                        $.each(data.rows, function (index, item) {
                            option.push({
                                id: item.id,
                                text: `${item.allowance}`
                            });
                        });
                        return {
                            results: option,
                            more: more,
                        };
                    },
                },
                allowClear: true,
            });
            $("#allowance_id").select2('data',{id:{{$allowanceincrease->allowance_id}},text:'{{$allowanceincrease->allowance->allowance}}'}).trigger('change');
            $(document).on("change", "#allowance_id", function () {
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
                    $('.overlay').removeClass('hidden');
                    }
                }).done(function(response){
                        $('.overlay').addClass('hidden');
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
                    $('.overlay').addClass('hidden');
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
