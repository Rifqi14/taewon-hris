@extends('admin.layouts.app')

@section('title', 'Add User')
@push('breadcrump')
<li class="breadcrumb-item"><a href="{{route('user.index')}}">User</a></li>
<li class="breadcrumb-item active">Add</li>
@endpush
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card card-{{config('configs.app_theme')}} card-outline">
            <div class="card-header">
                <h3 class="card-title">Add User</h3>
                <!-- tools box -->
                <div class="pull-right card-tools">
                    <button form="form" type="submit" class="btn btn-sm btn-{{config('configs.app_theme')}}" title="Simpan"><i
                            class="fa fa-save"></i></button>
                    <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i
                            class="fa fa-reply"></i></a>
                </div>
                <!-- /. tools -->
            </div>
            <div class="card-body">
                <form id="form" action="{{route('user.store')}}" class="form-horizontal" method="post"
                    autocomplete="off">
                    {{ csrf_field() }}
                    <div class="card-body">
                        <div class="form-group row">
                            <label for="name" class="col-sm-2 col-form-label">Role <b class="text-danger">*</b></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="role_id" name="role_id"
                                    data-placeholder="Select Role" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="name" class="col-sm-2 col-form-label">Name <b class="text-danger">*</b></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="name" name="name" placeholder="Name"
                                    required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="username" class="col-sm-2 col-form-label">Username <b
                                    class="text-danger">*</b></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="username" name="username"
                                    placeholder="Username" required>
                                <span class="form-text text-muted">Ex. johndue (Only letters lowercase input).</span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="email" class="col-sm-2 col-form-label">Email <b
                                    class="text-danger">*</b></label>
                            <div class="col-sm-6">
                                <input type="email" class="form-control" id="email" name="email" placeholder="Email"
                                    required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="password" class="col-sm-2 col-form-label">Password <b
                                    class="text-danger">*</b></label>
                            <div class="col-sm-6">
                                <input type="password" class="form-control" id="password" name="password"
                                    placeholder="Password" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Status</label>
                            <div class="col-sm-4">
                                <label> <input class="form-control" type="checkbox" name="status"> <i></i></label>
                            </div>
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
<script>
    $(document).ready(function () {
        $("input[name=username]").inputmask("Regex", {
            regex: "[a-z]*"
        });
        $('input[name=status]').iCheck({
            checkboxClass: 'icheckbox_square-green',
            radioClass: 'iradio_square-green',
        });
        $("#role_id").select2({
            ajax: {
                url: "{{route('role.select')}}",
                type: 'GET',
                dataType: 'json',
                data: function (term, page) {
                    return {
                        display_name: term,
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
                            text: `${item.display_name}`
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
        $(document).on("change", "#role_id", function () {
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
                        $('.overlay').removeClass('d-none');
                    }
                }).done(function (response) {
                    $('.overlay').addClass('d-none');
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
