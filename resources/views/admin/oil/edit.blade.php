@extends('admin.layouts.app')

@section('title', 'Edit Oil')
@push('breadcrump')
<li class="breadcrumb-item"><a href="{{route('oil.index')}}">Oil</a></li>
<li class="breadcrumb-item active">Edit</li>
@endpush
@section('stylesheets')
<link href="{{asset('adminlte/component/summernote/css/summernote.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/css/fileinput.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/daterangepicker/daterangepicker.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/jquery-ui/jquery-ui.min.css')}}" rel="stylesheet">
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

        <form id="form" action="{{ route('oil.update',['id'=>$asset->id]) }}" class="form-horizontal" method="post"
            autocomplete="off">
            {{ csrf_field() }}
            {{ method_field('put') }}
            <div class="card card-{{config('configs.app_theme')}} card-outline">
                <div class="card-header">
                    <h3 class="card-title">Information Oil</h3>
                    <!-- tools box -->
                    <div class="pull-right card-tools">
                        <button form="form" type="submit" class="btn btn-sm btn-{{config('configs.app_theme')}} text-white" title="Update"><i
                                class="fa fa-save"></i></button>
                        <a href="javascript:void(0)" onclick="backurl()" class="btn btn-sm btn-default" title="Back"><i
                                class="fa fa-reply"></i></a>
                    </div>
                    <!-- /. tools -->
                </div>
                <div class="card-body">
                    <input type="text" hidden value="{{ $asset->id }}">

                    <div class="form-group row">
                        <label for="code" class="col-sm-2 col-form-label">Code <b
                                class="text-danger">*</b></label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="name" name="code" placeholder="Oil Code"  value="{{ $asset->code }}" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="name" class="col-sm-2 col-form-label">Name <b class="text-danger">*</b></label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="name" name="name" placeholder="Asset Name"
                                required value="{{ $asset->name }}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="pic" class="col-sm-2 col-form-label">PIC <b
                                class="text-danger">*</b></label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="pic" name="pic" value="{{ $asset->pic }}" placeholder="PIC"
                                required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="location" class="col-sm-2 col-form-label">Location <b
                                class="text-danger">*</b></label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="location" name="location" value="{{ $asset->location }}" placeholder="Lokasi"
                                required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="buy_price" class="col-sm-2 col-form-label">Buy Price <b
                                class="text-danger">*</b></label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="buy_price" value="{{ $asset->buy_price }}" name="buy_price" placeholder="Buy Price"
                                required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="vendor" class="col-sm-2 col-form-label">Vendor <b
                                class="text-danger">*</b></label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="vendor" name="vendor" value="{{ $asset->vendor }}"  placeholder="Vendor"
                                required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="buy_date" class="col-sm-2 col-form-label">Buy Date <b
                                class="text-danger">*</b></label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control datepicker" id="buy_date" value="{{ $asset->buy_date }}"  name="buy_date" placeholder="Buy Date"
                                required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="note" class="col-sm-2 col-form-label">Note / Guarantee</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="note" name="note" value="{{ $asset->note }}"  placeholder="Note / Guarantee">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="stock" class="col-sm-2 col-form-label">Stock <b
                                class="text-danger">*</b></label>
                        <div class="col-sm-2">
                            <div class="input-group mb-3">
                            <input type="text" class="form-control" id="stock" name="stock" placeholder="Stock" value="{{ $asset->stock*1 }}"
                                required>
                                <div class="input-group-append">
                                    <span class="input-group-text" >Liter</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="overlay d-none">
                    <i class="fas fa-sync-alt fa-3x fa-spin"></i>
                </div>
            </div>


            <div class="card card-{{config('configs.app_theme')}} card-outline">
                <div class="card-header">
                    <h3 class="card-title">Document</h3>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <label for="image" class="col-sm-2 col-form-label">Oil Foto</label>
                        <div class="col-sm-6">
                            <input type="file" class="form-control" name="image" id="image" accept="image/*" />
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
<script src="{{asset('adminlte/component/jquery-ui/jquery-ui.min.js')}}"></script>
<script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
<script src="{{asset('adminlte/component/summernote/js/summernote.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-fileinput/js/fileinput.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.js')}}"></script>

<script src="{{asset('adminlte/component/daterangepicker/moment.min.js')}}"></script>
<script src="{{asset('adminlte/component/daterangepicker/daterangepicker.js')}}"></script>
<script>
    function gantiCat() {
        $('#add-modal').modal('hide');
    }
    function changeCategory() {
        $('#add-modal').modal('show');
        $('#category_select').html('kosong');
        $('#category_name').html('kosong');

        $("#level-one li").on('click', function () {
            $("#level-one li").removeClass('selected');
            $(this).addClass('selected');
            $('#form-pick').find('button[type=submit]').attr('disabled', false);
            if ($(this).data('children')) {
                $('#form').find('input[name=assetcategory_id]').attr('value', $(this).data('id'));
                $('#form-pick').find('input[name=asset_category_id]').attr('value', $(this).data('id'));
                $('#category_name').html($(this).data('path'));
                $('#category_select').html($(this).data('path'));
            } else {
                $('#form').find('input[name=assetcategory_id]').attr('value', $(this).data('id'));
                $('#form-pick').find('input[name=asset_category_id]').attr('value', $(this).data('id'));
                $('#category_name').html($(this).data('path'));
                $('#category_select').html($(this).data('path'));
            }
        });

        $("#level-two").on("click", ' li', function () {
            $("#level-two li").removeClass('selected');
            $(this).addClass('selected');
            $('#form-pick').find('button[type=submit]').attr('disabled', false);
            if ($(this).data('children')) {
                $('#form').find('input[name=assetcategory_id]').attr('value', $(this).data('id'));
                $('#form-pick').find('input[name=asset_category_id]').attr('value', $(this).data('id'));
                $('#category_name').html($(this).data('path'));
                $('#category_select').html($(this).data('path'));
            } else {
                $('#form').find('input[name=assetcategory_id]').attr('value', $(this).data('id'));
                $('#form-pick').find('input[name=asset_category_id]').attr('value', $(this).data('id'));
                $('#category_name').html($(this).data('path'));
                $('#category_select').html($(this).data('path'));
            }
        });
        $("#level-three").on("click", ' li', function () {
            $("#level-three li").removeClass('selected');
            $(this).addClass('selected');
            $('#form-pick').find('button[type=submit]').attr('disabled', false);
            $('#form').find('input[name=assetcategory_id]').attr('value', $(this).data('id'));
            $('#form-pick').find('input[name=asset_category_id]').attr('value', $(this).data('id'));
            $('#category_name').html($(this).data('path'));
            $('#category_select').html($(this).data('path'));
        });
    }

    $(document).ready(function (){
        var vendors = [
        @foreach($vendors as $vendor)
        "{{$vendor->vendor}}",
        @endforeach
        ];
        var pics = [
        @foreach($pics as $pic)
        "{{$pic->pic}}",
        @endforeach
        ];
        var locations = [
        @foreach($locations as $location)
        "{{$location->location}}",
        @endforeach
        ];
        $( "input[name=vendor]" ).autocomplete({
            source: vendors
        });
        $( "input[name=pic]" ).autocomplete({
            source: pics
        });
        $( "input[name=location]" ).autocomplete({
            source: locations
        });
        $('input[name=buy_price]').inputmask('decimal',{
            rightAlign:false
        });
        $('input[name=stock]').inputmask('Regex', {regex: "[0-9]*"});
        $('#form').find('.datepicker').daterangepicker({
            startDate: moment(),
            singleDatePicker: true,
            timePicker: false,
            locale: {
             format: 'YYYY-MM-DD'
            }
        });
        //Bootstrap fileinput component

        $("#image").fileinput({
            browseClass: "btn btn-{{config('configs.app_theme')}}",
            showRemove: false,
            showUpload: false,
            allowedFileExtensions: ["png", "jpg", "jpeg"],
            dropZoneEnabled: false,
            initialPreview: '<img src="{{asset($asset->image)}}" class="kv-preview-data file-preview-image">',
            initialPreviewAsData: false,
            initialPreviewFileType: 'image',
            initialPreviewConfig: [
            {caption: "{{$asset->image}}", downloadUrl: "{{asset($asset->image)}}", size:"{{ @File::size(public_path($asset->image))}}",url: false}
            ],
            theme:'explorer-fas'
        });
        $("#document").fileinput({
            browseClass: "btn btn-{{config('configs.app_theme')}}",
            showRemove: false,
            showUpload: false,
            dropZoneEnabled: false,
            initialPreview: '{{asset($asset->document)}}',
            initialPreviewAsData: true, // defaults markup
            initialPreviewFileType: 'image', 
            initialPreviewConfig: [
            {caption: "{{$asset->document}}", downloadUrl: "{{asset($asset->document)}}", size:"{{ @File::size(public_path($asset->document))}}",url: false}
            ],
            theme:'explorer-fas',
            preferIconicPreview: true, // this will force thumbnails to display icons for following file extensions
            previewFileIconSettings: { // configure your icon file extensions
                'doc': '<i class="fas fa-file-word text-primary"></i>',
                'xls': '<i class="fas fa-file-excel text-success"></i>',
                'ppt': '<i class="fas fa-file-powerpoint text-danger"></i>',
                'pdf': '<i class="fas fa-file-pdf text-danger"></i>',
                'zip': '<i class="fas fa-file-archive text-muted"></i>',
                'htm': '<i class="fas fa-file-code text-info"></i>',
                'txt': '<i class="fas fa-file-alt text-info"></i>',
                'mov': '<i class="fas fa-file-video text-warning"></i>',
                'mp3': '<i class="fas fa-file-audio text-warning"></i>',
                // note for these file types below no extension determination logic 
                // has been configured (the keys itself will be used as extensions)
                'jpg': '<i class="fas fa-file-image text-danger"></i>', 
                'gif': '<i class="fas fa-file-image text-muted"></i>', 
                'png': '<i class="fas fa-file-image text-primary"></i>'    
            },
            previewFileExtSettings: { // configure the logic for determining icon file extensions
                'doc': function(ext) {
                    return ext.match(/(doc|docx)$/i);
                },
                'xls': function(ext) {
                    return ext.match(/(xls|xlsx)$/i);
                },
                'ppt': function(ext) {
                    return ext.match(/(ppt|pptx)$/i);
                },
                'zip': function(ext) {
                    return ext.match(/(zip|rar|tar|gzip|gz|7z)$/i);
                },
                'htm': function(ext) {
                    return ext.match(/(htm|html)$/i);
                },
                'txt': function(ext) {
                    return ext.match(/(txt|ini|csv|java|php|js|css)$/i);
                },
                'mov': function(ext) {
                    return ext.match(/(avi|mpg|mkv|mov|mp4|3gp|webm|wmv)$/i);
                },
                'mp3': function(ext) {
                    return ext.match(/(mp3|wav)$/i);
                }
            }
        });

        // validation
        // $(document).on("change", "#image", function () {
        //     if (!$.isEmptyObject($('#form').validate().submitted)) {
        //     $('#form').validate().form();
        //     }
        // });

       

        $(document).on("change", "#assetcategory_id", function () {
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
                        if(result) {
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
                            });
                        }
                    }
                });
            }
        });

        $('#level-one').on('click', function (e) {
            var cat_id = $(e.target).attr("data-id");
            $.ajax({
                url: "{{ route('subcat') }}",
                type: "POST",
                data: {
                    cat_id: cat_id,
                    _token: "{{ csrf_token() }}"
                },
                success: function (data) {
                    // console.log(data);
                    $('#level-two').empty();
                    $.each(data.subcategories[0].subcategories, function (index, subcategory) {
                    $('#level-two').append('<li class="style list-group-item d-flex justify-content-between" data-path="'+subcategory.path+'" data-id="'+ subcategory.id +'" data-children="true">'+subcategory.name+
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
                    cat_id: cat_id,
                    _token: "{{ csrf_token() }}"
                },
                success: function (data) {
                    $('#level-three').empty();
                    $.each(data.subcategories[0].subcategories, function (index, subcategory) {
                        $('#level-three').append('<li class="style list-group-item d-flex justify-content-between" data-path="'+subcategory.path+'" data-id="'+ subcategory.id +'" data-children="false">'+subcategory.name+' </li>');
                    })
                }
            })
        });

    });
</script>
@endpush
