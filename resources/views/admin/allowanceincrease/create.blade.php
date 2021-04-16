@extends('admin.layouts.app')

@section('title', 'Allowance Increase')
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/css/fileinput.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/daterangepicker/daterangepicker.css')}}" rel="stylesheet">
@endsection

@push('breadcrump')
<li class="breadcrumb-item active"><a href="{{route('allowanceincrease.index')}}">Allowance Increase</a></li>
<li class="breadcrumb-item active">Create</li>
@endpush


@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="row">
            <div class="col-lg-8">
                <div class="card card-{{ config('configs.app_theme') }} card-outline">
                    <div class="card-header" style="height:55px;">
                        <h3 class="card-title">Allowance Increase Data</h3>
                    </div>
                    <div class="card-body">
                        <form id="form" action="{{ route('allowanceincrease.store') }}" method="post" autocomplete="off">
                            {{ csrf_field() }}
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
                                        <label class="control-label pull-right col-md-2" for="period">Period</label>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <select class="form-control select2" name="month" id="month">
                                                    <option value="01" @if (date('m', time())=="01" ) selected @endif>January</option>
                                                    <option value="02" @if (date('m', time())=="02" ) selected @endif>February</option>
                                                    <option value="03" @if (date('m', time())=="03" ) selected @endif>March</option>
                                                    <option value="04" @if (date('m', time())=="04" ) selected @endif>April</option>
                                                    <option value="05" @if (date('m', time())=="05" ) selected @endif>May</option>
                                                    <option value="06" @if (date('m', time())=="06" ) selected @endif>June</option>
                                                    <option value="07" @if (date('m', time())=="07" ) selected @endif>July</option>
                                                    <option value="08" @if (date('m', time())=="08" ) selected @endif>August</option>
                                                    <option value="09" @if (date('m', time())=="09" ) selected @endif>September</option>
                                                    <option value="10" @if (date('m', time())=="10" ) selected @endif>October</option>
                                                    <option value="11" @if (date('m', time())=="11" ) selected @endif>November</option>
                                                    <option value="12" @if (date('m', time())=="12" ) selected @endif>December</option>
                                                </select>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="input-group">
                                                    <select name="year" class="form-control select2" id="year">
                                                        @php
                                                        $thn_skr = date('Y');
                                                        @endphp
                                                        @for ($i = $thn_skr; $i >= 1991; $i--)
                                                        <option value="{{ $i }}">{{ $i }}</option>
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
                                        <label>Allowance</label>
                                        <input type="text" name="allowance_id" id="allowance_id" class="form-control select2" placeholder="Choose Allowance">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Value</label>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <select class="form-control select2" data-placeholder="Type" name="type_value"
                                                    id="type">
                                                    <option value=""></option>
                                                    <option value="Percentage">Percentage</option>
                                                    <option value="Nominal">Nominal</option>
                                                </select>
                                            </div>
                                            <div class="col-sm-6">
                                                <input type="text" name="value" class="form-control"
                                                    placeholder="Value">
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
                        <h3 class="card-title">Other</h3>
                        <div class="pull-right card-tools">
                            <button form="form" type="submit" class="btn btn-sm btn-{{ config('configs.app_theme') }}"
                                title="Simpan"><i class="fa fa-save"></i></button>
                            <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i
                                    class="fa fa-reply"></i></a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>Notes</label>
                                    <textarea style="height:110px;" type="text" class="form-control" name="note"
                                        placeholder="Notes"></textarea>
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
