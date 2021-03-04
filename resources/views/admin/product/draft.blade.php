@extends('admin.layouts.app')

@section('title', 'Tambah Produk')
@push('breadcrump')
<li class="breadcrumb-item"><a href="{{route('product.index')}}">Product</a></li>
<li class="breadcrumb-item active">Tambah</li>
@endpush
@section('stylesheets')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link href="{{asset('adminlte/component/summernote/css/summernote.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/css/fileinput.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.css')}}" rel="stylesheet">
<style type="text/css">
    .ui-menu {
        width: 150px;
    }

    li:active {
        color: red;
    }

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

        <form id="form" action="{{route('product.create')}}" class="form-horizontal" method="get" autocomplete="off">
            {{ csrf_field() }}

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Produk</h3>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <label for="name" class="col-sm-2 col-form-label">Nama Produk <b
                                class="text-danger">*</b></label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="name" name="name" placeholder="Nama" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 pick-category">
                            <ul id="level-one">
                                @foreach ($categories as $category)
                                <li data-path="{{ $category->name }}" data-id="{{ $category->id }}" data-children="true"
                                    class="d-flex justify-content-between">{{ $category->name }}
                                    @foreach ($drafts as $draft)
                                    @if ($draft->id == $category->id)
                                    <i class="fa fa-chevron-right"></i>
                                    @endif
                                    @endforeach
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="col-md-4 pick-category">
                            <ul id="level-two">
                            </ul>
                        </div>
                        <div class="col-md-4 pick-category">
                            <ul id="level-three">
                            </ul>
                        </div>
                    </div>


                    {{-- <ul id="menu">
                        @foreach ($categories as $category)
                        <li>
                            <div id="pilih-{{ $category->id }}">{{ $category->name }}
                </div>
                @foreach ($categories2 as $category2)
                @if ($category->id == $category2->parent)
                <ul>
                    <li>
                        <div id="pilih-{{ $category2->id }}">{{ $category2->name }}</div>
                    </li>
                </ul>
                @endif
                @endforeach
                </li>
                @endforeach
                </ul> --}}

                <div class="form-group row">
                    <label for="category_select" class="col-sm-2 col-form-label">Dipilih <b
                            class="text-danger">*</b></label>
                    <div class="col-sm-6">
                        <div id="category_select">tidak ada kategori dipilih</div>
                        <input type="hidden" name="product_category_id">
                        <input type="hidden" name="category_name">
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
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
<script src="{{asset('adminlte/component/summernote/js/summernote.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-fileinput/js/fileinput.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.js')}}"></script>

{{-- <script>
    $(document).ready(function () {
        $(function () {
            $("#menu").menu();
        });

        $('#menu').on('click', 'div', function () {
            var idtest = $(this).attr("id");
            // console.log(idtest);

            $("#datahasil").hide();
            var pilihhasil = $("#" + idtest).html();
            console.log(pilihhasil);

            $("#datahasil").show();
            $("#datahasil").html(pilihhasil);

        });

    })

</script> --}}
<script type="text/javascript">
    $(document).ready(function () {
        $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });


        $("#level-one li").on('click', function () {
            $("#level-one li").removeClass('selected');
            $(this).addClass('selected');
            if ($(this).data('children')) {
                // $('#form').find('button[type=submit]').attr('disabled', true);
                $('#form').find('input[name=product_category_id]').attr('value', $(this).data('id'));
                $('#form').find('input[name=category_name]').attr('value', $(this).data('path'));
                $('#category_select').html($(this).data('path'));
                // loadCategory(1, $("#level-two"), $(this).data('id'));
                // $('#level-two').show();
            } else {
                // $('#level-two').hide();
                // $('#level-three').hide();
                // $('#form').find('button[type=submit]').attr('disabled', false);
                $('#form').find('input[name=product_category_id]').attr('value', $(this).data('id'));
                $('#form').find('input[name=category_name]').attr('value', $(this).data('path'));
                $('#category_select').html($(this).data('path'));
            }
        });

        $("#level-two").on("click", ' li', function () {
            $("#level-two li").removeClass('selected');
            $(this).addClass('selected');
            if ($(this).data('children')) {
                // $('#form').find('button[type=submit]').attr('disabled', true);
                $('#form').find('input[name=product_category_id]').attr('value', $(this).data('id'));
                $('#form').find('input[name=category_name]').attr('value', $(this).data('path'));
                $('#category_select').html($(this).data('path'));
                // loadCategory(2, $("#level-three"), $(this).data('id'));
                // $('#level-three').show();
            } else {
                // $('#level-three').hide();
                // $('#form').find('button[type=submit]').attr('disabled', false);
                $('#form').find('input[name=product_category_id]').attr('value', $(this).data('id'));
                $('#form').find('input[name=category_name]').attr('value', $(this).data('path'));
                $('#category_select').html($(this).data('path'));
            }
        });
        $("#level-three").on("click", ' li', function () {
            $("#level-three li").removeClass('selected');
            $(this).addClass('selected');
            // $('#form').find('button[type=submit]').attr('disabled', false);
            $('#form').find('input[name=product_category_id]').attr('value', $(this).data('id'));
            $('#form').find('input[name=category_name]').attr('value', $(this).data('path'));
            $('#category_select').html($(this).data('path'));
        });

        $("#form").validate({
            errorElement: 'div',
            errorClass: 'invalid-feedback',
            focusInvalid: false,
            rules: {
                product_name: {
                    required: true,
                },
            },
            highlight: function (e) {
                $(e).closest('.form-group').removeClass('has-success').addClass('was-validated has-error');
            },

            success: function (e) {
                $(e).closest('.form-group').removeClass('has-error').addClass('has-success');
                $(e).remove();
            },
        });

        $('#level-one').on('click', function (e) {
            var cat_id = $(e.target).attr("data-id");
            $.ajax({
                url: "{{ route('subcat') }}",
                type: "POST",
                data: {
                    cat_id: cat_id
                },
                success: function (data) {
                    // console.log(data);
                    $('#level-two').empty();
                    $.each(data.subcategories[0].subcategories, function (index, subcategory) {
                    $('#level-two').append('<li class="d-flex justify-content-between" data-path="'+subcategory.name+'" data-id="'+ subcategory.id +'" data-children="true">'+subcategory.name+
                        '<i class="fa fa-chevron-right"></i>'+
                    '</li>');
                    })
                }
            })
        });

        $('#level-two').on('click', function (e) {
            var cat_id = $(e.target).attr("data-id");
            $.ajax({
                url: "{{ route('subcat') }}",
                type: "POST",
                data: {
                    cat_id: cat_id
                },
                success: function (data) {
                    $('#level-three').empty();
                    $.each(data.subcategories[0].subcategories, function (index, subcategory) {
                        $('#level-three').append('<li class="d-flex justify-content-between" data-path="'+subcategory.name+'" data-id="'+ subcategory.id +'" data-children="true">'+subcategory.name+' </li>');
                    })
                }
            })
        });
    });

    // function loadCategory(level, ul, parent_id) {
    //     var data = {
    //         parent_id: parent_id,
    //         is_delete: 0
    //     };
    //     $.ajax({
    //         // url: 'https://envio.biiscorp.com/api/productcategory/list',
    //         dataType: 'json',
    //         data: data,
    //         type: 'POST',
    //         success: function (response) {
    //             if (level == 1) {
    //                 $('#level-two').empty();
    //                 $('#level-three').empty();
    //             } else {
    //                 $('#level-three').empty();
    //             }
    //             $.each(response.rows, function (i, item) {
    //                 if (item.children > 0) {
    //                     ul.append('<li data-path="' + item.path + '" data-id="' + item.id +
    //                         '" data-children="true" class="new_category">' + item
    //                         .category_name + ' <i class="fa fa-chevron-right"></i></li>');
    //                 } else {
    //                     ul.append('<li data-path="' + item.path + '" data-id="' + item.id +
    //                         '" data-children="false" class="new_category">' + item
    //                         .category_name + '</li>');
    //                 }

    //             });
    //         }
    //     });
    // }





</script>


@endpush
