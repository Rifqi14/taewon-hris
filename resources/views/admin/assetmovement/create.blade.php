@extends('admin.layouts.app')

@section('title', 'Create Asset Movement')
@section('stylesheets')
<link rel="stylesheet" href="{{asset('adminlte/component/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}">
<style>
    .card-header {
        height: 50px;
    }

    .card {
        height: 95%;
        display: flex;
    }

</style>
@endsection

@push('breadcrump')
<li class="breadcrumb-item"><a href="{{route('title.index')}}">Asset Movement</a></li>
<li class="breadcrumb-item active">Create</li>
@endpush

@section('content')
<div class="wrapper wrapper-content">
    <form id="form" action="{{ route('assetmovement.store') }}" method="post" autocomplete="off">
        {{ csrf_field() }}
        <div class="row">
            <div class="col-lg-7">
                <div class="card card-{{config('configs.app_theme')}} card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Create Asset Movement</h3>
                        <!-- tools box -->

                        <!-- /. tools -->
                    </div>
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="form-group col-sm-6">
                                <label for="asset_serial_id" class="col-sm-12 col-form-label">Asset Serial <b
                                        class="text-danger">*</b></label>
                                <div class="col-sm-12">
                                    <input type="text" class="form-control" id="asset_serial_id" name="asset_serial_id"
                                        data-placeholder="Choose Asset Serial" required>
                                </div>
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="transaction_date" class="col-sm-12 col-form-label">Transaction Date <b
                                        class="text-danger">*</b></label>
                                <div class="col-sm-12 input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text" style="border-right: none;"><i class="fas fa-calendar-alt"></i></div>
                                    </div>
                                    <input type="text" class="form-control" id="transaction_date" name="transaction_date"
                                        placeholder="Transaction Date" required>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex">
                            <div class="form-group col-sm-6">
                                <label class="col-sm-12 col-form-label">Type</label>
                                <div class="col-sm-12">
                                    <input class="form-control" type="radio" name="type" value="0" checked> <i></i>
                                    <label class="p-2">Out</label>
                                    <input class="form-control" type="radio" name="type" value="1"> <i></i>
                                    <label class="p-2">In</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="overlay d-none">
                        <i class="fas fa-sync fa-3x fa-spin"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card card-{{config('configs.app_theme')}} card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Others</h3>
                        <!-- tools box -->
                        <div class="pull-right card-tools">
                            <button form="form" type="submit" class="btn btn-sm btn-{{config('configs.app_theme')}}"
                                title="Save"><i class="fa fa-save"></i></button>
                            <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Back"><i
                                    class="fa fa-reply"></i></a>
                        </div>
                        <!-- /. tools -->
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="note" class="col-sm-3 col-form-label">Catatan </label>
                            <div class="col-sm-12">
                                <textarea name="note" id="note" class="form-control" placeholder="Tambah Catatan"
                                    style="height: 100px;"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
<script src="{{asset('adminlte/component/moment/moment.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
<script>
    $(document).ready(function(){

    $("input[name=qty]")
        .inputmask('decimal', {
            rightAlign: false
        });

    $('input[name=type], input[name=status]').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
    });

    //date
    $('input[name=transaction_date], input[name=expired_date]').datepicker({
				autoclose: true,
				format: 'yyyy-mm-dd'
			})

        $('input[name=transaction_date], input[name=expired_date]').on('change', function(){
            if (!$.isEmptyObject($(this).closest("form").validate().submitted)) {
                $(this).closest("form").validate().form();
            }
        });

    //select asset serial
    $( "#asset_serial_id" ).select2({
        ajax: {
        url: "{{route('assetserial.select')}}",
        type:'GET',
        dataType: 'json',
        data: function (term,page) {
            return {
            serial_no:term,
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
                text: `${item.serial_no}`
            });
            });
            return {
            results: option, more: more,
            };
        },
        },
        allowClear: true,
    });
    $(document).on("change", "#asset_serial_id", function () {
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
