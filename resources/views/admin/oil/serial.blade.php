@extends('admin.layouts.app')

@section('title', 'Create Asset Serial')
@push('breadcrump')
<li class="breadcrumb-item"><a href="{{route('asset.index')}}">Asset Serial</a></li>
<li class="breadcrumb-item active">Create</li>
@endpush
@section('stylesheets')
<link href="{{asset('adminlte/component/summernote/css/summernote.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/css/fileinput.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.css')}}" rel="stylesheet">
<style type="text/css">
    li.style {
        border: 0 none;
        padding: 2%;
        color: grey;
        margin-right: 8%;
        cursor: pointer;
        font-weight: 600;
    }

    li.style:hover,
    li.selected {
        color: #df4759;
        background-color: #f5f5f5;
    }
</style>
@endsection
@section('content')
<div class="row">
    <div class="col-lg-12">

        <form id="form" action="{{ route('asset.serialupdate',['id'=>$asset->id]) }}" class="form-horizontal" method="post"
            autocomplete="off">
            {{ csrf_field() }}
            {{ method_field('put') }}
            <div class="card card-{{config('configs.app_theme')}} card-outline">
                <div class="card-header">
                    <h3 class="card-title">Information Asset Serial</h3>
                    <!-- tools box -->
                    <div class="pull-right card-tools">
                        <button form="form" type="submit" class="btn btn-sm btn-{{config('configs.app_theme')}} text-white" title="Save"><i
                                class="fa fa-save"></i></button>
                        <a href="javascript:void(0)" onclick="backurl()" class="btn btn-sm btn-default" title="Back"><i
                                class="fa fa-reply"></i></a>
                    </div>
                    <!-- /. tools -->
                </div>
                <div class="card-body">
                    <input type="text" hidden value="{{ $asset->id }}">
                    <div class="form-group row">
                        <label for="type" class="col-sm-2 col-form-label">Type </label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="type" name="type" placeholder="Type" disabled
                                required value="{{ $asset->type }}" style="border:none; background-color:transparent;">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="name" class="col-sm-2 col-form-label">Name Asset </label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="name" name="name" placeholder="Name Asset"
                                required value="{{ $asset->name }}" disabled style="border:none; background-color:transparent;">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="description" class="col-sm-2 col-form-label">Description Asset </label>
                        <div class="col-sm-6">
                            <div class="form-control" style="border:none;">{!! $asset->description !!}</div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="description" class="col-sm-2 col-form-label">Category Asset </label>
                        <div class="col-sm-6">
                            <div class="form-control" style="border:none;">{{ $asset->assetcategory->name }}</div>
                            <input type="hidden" name="assetcategory_id" value="{{ $asset->assetcategory->id }}">
                        </div>
                    </div>
                </div>
                <div class="overlay d-none">
                    <i class="fas fa-sync-alt fa-3x fa-spin"></i>
                </div>
            </div>


            <div class="card card-{{config('configs.app_theme')}} card-outline">
                <div class="card-header">
                    <h3 class="card-title">Serial</h3>
                </div>
                <div class="card-body">
                    <div id="disserial" style="display: block;">
                        <div class="form-group row">
                            <label for="serial" class="col-sm-2 col-form-label">Asset Serial </label>
                            <div class="col-sm-10" id="add_conv">
                                <a class="nohover btn btn-outline-{{config('configs.app_theme')}} col-sm-12" onclick="addSerial()" style="border-style: dashed; cursor: pointer;">Add </a>
                                <table class="table table-borderless" id="table-serial">
                                    <thead>
                                        <tr>
                                            <th>Serial Number</th>
                                            <th class="text-center">#</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($asset->assetserials as $serial)
                                        <tr data-id="{{ $serial->id }}">
                                            <td>
                                                <input type="hidden" value="{{ $serial->id }}" name="serial_id[]">
                                                <input type="hidden" value="{{ $loop->iteration }}" name="serial_item[]">
                                                <input type="text" class="form-control" id="serial_no{{ $loop->iteration }}" name="serial_no[]"
                                                    placeholder="Serial Number" value="{{ $serial->serial_no }}">
                                            </td>
                                            <td class="text-center">
                                                <a class="btn btn-{{config('configs.app_theme')}} btn-sm delete text-white" data-id="{{ $serial->id }}" style="cursor: pointer;"><i class="fas fa-trash"></i></a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="overlay d-none">
                    <i class="fas fa-sync-alt fa-3x fa-spin"></i>
                </div>
            </div>

        </form>
        <div class="overlay d-none">
            <i class="fa fa-refresh fa-spin"></i>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
<script src="{{asset('adminlte/component/summernote/js/summernote.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-fileinput/js/fileinput.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.js')}}"></script>

<script>

    $(document).ready(function (){
        $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

        $('input[name=best_asset]').iCheck({
            checkboxClass: 'icheckbox_square-green',
            radioClass: 'iradio_square-green',
        });

        $('.select2').select2({
            allowClear:true
        });

        $('#table-serial').on('click','.remove',function(){
			$(this).parents('tr').remove();
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

        $(document).on('click','.delete',function(){
            var id = $(this).data('id');
            console.log(id);
            bootbox.confirm({
                buttons: {
                    confirm: {
                        label: '<i class="fa fa-check"></i>',
                        className: 'btn-{{config('configs.app_theme')}}'
                    },
                    cancel: {
                        label: '<i class="fa fa-undo"></i>',
                        className: 'btn-default'
                    },
                },
                title:'Menghapus serial?',
                message:'Data yang telah dihapus tidak dapat dikembalikan',
                callback: function(result) {
                        if(result) {
                            var data = {
                                _token: "{{ csrf_token() }}"
                            };
                            $.ajax({
                                url: `{{url('admin/assetserial/deleteserial')}}/${id}`,
                                dataType: 'json',
                                data:data,
                                type:'DELETE',
                                beforeSend:function(){
                                    $('.overlay').removeClass('d-none');
                                }
                            }).done(function(response){
                                if(response.status){
                                    $('.overlay').addClass('d-none');
                                    $('#table-serial > tbody > tr[data-id="'+id+'"]').remove();

                                    // $('#table-product > tbody > tr[data-id="'+id+'"]').remove();
                                    $.gritter.add({
                                        title: 'Success!',
                                        text: response.message,
                                        class_name: 'gritter-success',
                                        time: 1000,
                                    });
                                }
                                else{
                                    $.gritter.add({
                                        title: 'Warning!',
                                        text: response.message,
                                        class_name: 'gritter-warning',
                                        time: 1000,
                                    });
                                }
                            }).fail(function(response){
                                var response = response.responseJSON;
                                $('.overlay').addClass('d-none');
                                $.gritter.add({
                                    title: 'Error!',
                                    text: response.message,
                                    class_name: 'gritter-error',
                                    time: 1000,
                                });
                            })
                        }
                }
            });
        })

    });

    function addSerial() {
        // console.log('aaa');
		var length = $('#table-serial tbody tr').length;
        // console.log(length);
        $('#table-serial tbody').append(
            `<tr>
                <td>
                    <input type="hidden" value="0" name="serial_id[]">
                    <input type="hidden" name="serial_item[]" value="${length}">
                    <input type="text" class="form-control" id="serial_no${length}" name="serial_no[]" placeholder="Serial Number" required>
                </td>
                <td class="text-center">
                    <a class="btn btn-{{config('configs.app_theme')}} btn-sm remove text-white" style="cursor: pointer;"><i class="fas fa-trash"></i></a>
                </td>
            </tr>`);


    }
</script>
@endpush
