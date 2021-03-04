@extends('admin.layouts.app')

@section('title', 'Basic UMK Salary')
@section('stylesheets')
@endsection

@push('breadcrump')
<li class="breadcrumb-item"><a href="{{route('basesallary.index')}}">Basic UMK Salary</a></li>
<li class="breadcrumb-item active">Create</li>
@endpush

@section('content')
<div class="wrapper wrapper-content">
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-{{ config('configs.app_theme')}} card-outline">
                <div class="card-header">
                    <h3 class="card-title">Basic UMK Data</h3>
                    <div class="pull-right card-tools">
                        <button form="form" type="submit"
                            class="btn btn-sm btn-{{config('configs.app_theme')}} text-white" title="Simpan"><i
                                class="fa fa-save"></i></button>
                        <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i
                                class="fa fa-reply"></i></a>
                    </div>
                </div>
                <div class="card-body">
                    <form id="form" action="{{ route('basesallary.store') }}" method="post" autocomplete="off">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-sm-6">
                                <!-- text input -->
                                <div class="form-group">
                                    <label>Region</label>
                                    <input type="text" class="form-control" id="region_id" name="region_id"
                                        data-placeholder="Pilih Region">
                                </div>
                            </div>
                            <!-- <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Salary</label>
                                    <input type="number" class="form-control" name="sallary" placeholder="Salary">
                                </div>
                            </div> -->
                            <div class="col-sm-6">
                            <label for="">Salary</label>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Rp.</span>
                                </div>
                                <input type="number" class="form-control" name="sallary" placeholder="Salary" aria-label="Amount (to the nearest dollar)">
                                <div class="input-group-append">
                                    <span class="input-group-text">,-</span>
                                </div>
                            </div>
                            </div>
                        </div>
                </div>
                <div class="overlay d-none">
                    <i class="fa fa-refresh fa-spin"></i>
                </div>
            </div>
            </a>
        </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
<script>
    $(document).ready(function () {
        $("#region_id").select2({
            ajax: {
                url: "{{ route('region.select') }}",
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
                            text: `${item.name}`
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
        $(document).on("change", "#region_id", function () {
            if (!$.isEmptyObject($('#form').validate().submitted)) {
                $('#form').validate().form();
            }
        });
        $("#form").validate({
            errorElement: 'div',
            errorClass: 'invalid-feedback',
            focusInvalid: false,
            highlight: function (e) {
                $(e).closest('.form-group').removeClass('has-success').addClass(
                    'was-validated has-error');
            },

            success: function (e) {
                $(e).closest('.form-group').removeClass('has-error').addClass('has-success');
                $(e).remove();
            },
            errorPlacement: function (error, element) {
                if (element.is(':file')) {
                    error.insertAfter(element.parent().parent().parent());
                } else
                if (element.parent('.input-group').length) {
                    error.insertAfter(element.parent());
                } else
                if (element.attr('type') == 'checkbox') {
                    error.insertAfter(element.parent());
                } else {
                    error.insertAfter(element);
                }
            },
            submitHandler: function () {
                $.ajax({
                    url: $('#form').attr('action'),
                    method: 'post',
                    data: new FormData($('#form')[0]),
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    beforeSend: function () {
                        $('.overlay').removeClass('hidden');
                    }
                }).done(function (response) {
                    $('.overlay').addClass('hidden');
                    if (response.status) {
                        document.location = response.results;
                    } else {
                        $.gritter.add({
                            title: 'Warning!',
                            text: response.message,
                            class_name: 'gritter-warning',
                            time: 1000,
                        });
                    }
                    return;
                }).fail(function (response) {
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
