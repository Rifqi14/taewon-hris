@extends('admin.layouts.app')

@section('title', 'Tambah Produk')
@push('breadcrump')
<li class="breadcrumb-item"><a href="{{route('product.index')}}">Principle</a></li>
<li class="breadcrumb-item active">Tambah</li>
@endpush
@section('stylesheets')
<link href="{{asset('adminlte/component/summernote/css/summernote.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/css/fileinput.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.css')}}" rel="stylesheet">
<style type="text/css">

</style>
@endsection
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title center"></h3>
                <!-- tools box -->
                <div class="pull-right card-tools">
                    <button form="form" type="submit" class="btn btn-sm btn-danger text-white" title="Simpan"><i
                            class="fa fa-save"></i></button>
                    <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i
                            class="fa fa-reply"></i></a>
                </div>
                <!-- /. tools -->
            </div>
        </div>

        <form id="form" action="{{ route('product.store') }}" class="form-horizontal" method="post" autocomplete="off">
            {{ csrf_field() }}

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Informasi Produk</h3>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <label for="type" class="col-sm-2 col-form-label">Tipe <b class="text-danger">*</b></label>
                        <div class="col-sm-6">
                            <select id="type" name="type" class="form-control select2" placeholder="Pilih Tipe"
                                required>
                                <option value=""></option>
                                <option value="standart">Standart</option>
                                <option value="combo">Combo</option>
                                <option value="service">Service</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="name" class="col-sm-2 col-form-label">Nama Produk <b
                                class="text-danger">*</b></label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="name" name="name" placeholder="Nama"
                                value="{{ $name }}" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="description" class="col-sm-2 col-form-label">Deskripsi Produk <b
                                class="text-danger">*</b></label>
                        <div class="col-sm-6">
                            <textarea name="description" id="description" class="form-control summernote"
                                placeholder="Deskripsi Produk"></textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="productcategory_id" class="col-sm-2 col-form-label">Kategori Produk <b
                                class="text-danger">*</b></label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="productcategory_id"
                                value="{{$category_name}}">
                            {{-- <input type="text" class="form-control" id="productcategory_id" name="productcategory_id"
                                data-placeholder="Pilih Kategori" required> --}}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Produk Unggulan</label>
                        <div class="col-sm-4">
                            <label> <input class="form-control" type="checkbox" name="best_product"> <i></i></label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="merk" class="col-sm-2 col-form-label">Merk <b class="text-danger">*</b></label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="merk" name="merk" placeholder="Merk" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Informasi Penjualan</h3>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <label for="price" class="col-sm-2 col-form-label">Harga <b class="text-danger">*</b></label>
                        <div class="col-sm-6 controls">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">Rp</div>
                                </div>
                                <input type="text" class="form-control" id="price" name="price" placeholder="Harga"
                                    required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Pengaturan Media</h3>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <label for="image" class="col-sm-2 col-form-label">Photo</label>
                        <div class="col-sm-6">
                            <input type="file" class="form-control" name="image[]" id="image" accept="image/*" required
                                multiple data-overwrite-initial="false" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Informasi Pengiriman</h3>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-md-2 col-form-label" for="weight">Berat</label>
                        <div class="col-sm-6 controls">
                            <div class="input-group">
                                <input type="text" class="form-control" id="weight" name="weight" placeholder="Berat"
                                    required />
                                <div class="input-group-append">
                                    <div class="input-group-text">gr</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-2 col-form-label" for="volume">Ukuran Paket</label>
                        <div class="col-sm-2 controls">
                            <div class="input-group">
                                <input type="text" class="form-control" id="volume_l" name="volume_l" placeholder="L"
                                    required />
                                <div class="input-group-append">
                                    <div class="input-group-text">cm</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-2 controls">
                            <div class="input-group">
                                <input type="text" class="form-control" id="volume_p" name="volume_p" placeholder="P"
                                    required />
                                <div class="input-group-append">
                                    <div class="input-group-text">cm</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-2 controls">
                            <div class="input-group">
                                <input type="text" class="form-control" id="volume_t" name="volume_t" placeholder="T"
                                    required />
                                <div class="input-group-append">
                                    <div class="input-group-text">cm</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Informasi Produk</h3>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <label for="condition" class="col-sm-2 col-form-label">Kondisi <b
                                class="text-danger">*</b></label>
                        <div class="col-sm-6">
                            <select id="condition" name="condition" class="form-control select2"
                                placeholder="Pilih Kondisi" required>
                                <option value=""></option>
                                <option value="baru">Baru</option>
                                <option value="bekas">Bekas</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="uom_id" class="col-sm-2 col-form-label">UOM <b class="text-danger">*</b></label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="uom_id" name="uom_id"
                                data-placeholder="Pilih UOM" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="sku" class="col-sm-2 col-form-label">SKU <b class="text-danger">*</b></label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="sku" name="sku" placeholder="SKU" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="barcode" class="col-sm-2 col-form-label">barcode <b
                                class="text-danger">*</b></label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="barcode" name="barcode" placeholder="Barcode"
                                required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="minimum_qty" class="col-sm-2 col-form-label">Minimum Stock <b
                                class="text-danger">*</b></label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="minimum_qty" name="minimum_qty"
                                placeholder="Minimum Stock" required>
                        </div>
                    </div>
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
    $(document).ready(function () {
        $('input[name=best_product]').iCheck({
            checkboxClass: 'icheckbox_square-green',
            radioClass: 'iradio_square-green',
        });

        $("input[name=price], input[name=weight], input[name=volume_l], input[name=volume_p], input[name=volume_t]")
            .inputmask('decimal', {
                rightAlign: false
            });

        $('.select2').select2({
            allowClear: true
        });

        //select kategori produk
        $("#productcategory_id").select2({
            ajax: {
                url: "{{route('productcategory.select')}}",
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

        //select uom
        $("#uom_id").select2({
            ajax: {
                url: "{{route('uom.select')}}",
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

        //Text Editor Component
        $('.summernote').summernote({
            height: 225,
            placeholder: 'Tulis sesuatu disini...',
            toolbar: [
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['font', ['strikethrough', 'superscript', 'subscript']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['height', ['height']]
            ]
        });

        //Bootstrap fileinput component
        $("#image").fileinput({
            browseClass: "btn btn-danger",
            showRemove: false,
            showUpload: false,
            maxFileCount: 6,
            allowedFileExtensions: ["png", "jpg", "jpeg"],
            dropZoneEnabled: false,
            theme: 'explorer-fas'
        });

        // validation
        $(document).on("change", "#image", function () {
            if (!$.isEmptyObject($('#form').validate().submitted)) {
                $('#form').validate().form();
            }
        });

        $(document).on("change", "#productcategory_id", function () {
            if (!$.isEmptyObject($('#form').validate().submitted)) {
                $('#form').validate().form();
            }
        });

        $(document).on("change", "#uom_id", function () {
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
