@extends('admin.layouts.app')

@section('title',__('vehicle.vehicle'))
@push('breadcrump')
<li class="breadcrumb-item"><a href="{{route('vehicle.index')}}">Vehicle</a></li>
<li class="breadcrumb-item active">Create</li>
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
        <form id="form" action="{{ route('vehicle.store') }}" class="form-horizontal" method="post" autocomplete="off">
            {{ csrf_field() }}
            <div class="card card-{{config('configs.app_theme')}} card-outline">
                <div class="card-header">
                    <h3 class="card-title">{{__('general.crt')}} {{__('vehicle.vehicle')}}</h3>
                    <!-- tools box -->
                    <div class="pull-right card-tools">
                        <button form="form" type="submit" class="btn btn-sm btn-{{config('configs.app_theme')}} text-white" title="{{__('general.save')}}"><i
                                class="fa fa-save"></i></button>
                        <a href="javascript:void(0)" onclick="backurl()" class="btn btn-sm btn-default" title="{{__('general.prvious')}}"><i
                                class="fa fa-reply"></i></a>
                    </div>
                    <!-- /. tools -->
                </div>
                <div class="card-body">

                    <div class="form-group row">
                        <div for="assetcategory_id" class="col-sm-2"><b>{{__('vehicle.vehcat')}}</b> <b
                                class="text-danger">*</b></div>
                        <div class="col-sm-6">
                            <div class="text-danger-400"><b><a href="#" onclick="changeCategory()" name="category_name" id="category_name">{{ $category_name }}</a></b></div>
                            <input type="hidden" name="assetcategory_id" value="{{ $asset_category_id }}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="engine_no" class="col-sm-2 col-form-label">{{__('vehicle.engine')}} <b
                                class="text-danger">*</b></label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="engine_no" name="engine_no" placeholder="{{__('vehicle.engine')}}" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="license_no" class="col-sm-2 col-form-label">{{__('vehicle.platno')}} <b
                                class="text-danger">*</b></label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="license_no" name="license_no" placeholder="{{__('vehicle.platno')}}"
                                value="{{ $license_no }}" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="merk" class="col-sm-2 col-form-label">Merk <b
                                class="text-danger">*</b></label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="merk" name="merk" placeholder="Merk"
                                required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="type" class="col-sm-2 col-form-label">{{__('vehicle.type')}} <b
                                class="text-danger">*</b></label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="type" name="type" placeholder="{{__('vehicle.type')}}"
                                required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="model" class="col-sm-2 col-form-label">Model <b
                                class="text-danger">*</b></label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="model" name="model" placeholder="Model"
                                required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="manufacture" class="col-sm-2 col-form-label">{{__('vehicle.manufac')}} <b
                                class="text-danger">*</b></label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="manufacture" name="manufacture" placeholder="{{__('vehicle.manufac')}}"
                                required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="engine_capacity" class="col-sm-2 col-form-label">{{__('vehicle.capacity')}}<b
                                class="text-danger">*</b></label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="engine_capacity" name="engine_capacity" placeholder="{{__('vehicle.capacity')}}"
                                required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="production_year" class="col-sm-2 col-form-label">{{__('vehicle.prodyear')}} <b
                                class="text-danger">*</b></label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="production_year" name="production_year" placeholder="{{__('vehicle.prodyear')}}"
                                required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="pic" class="col-sm-2 col-form-label">PIC <b
                                class="text-danger">*</b></label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="pic" name="pic" placeholder="PIC"
                                required>
                            <input type="hidden" class="form-control" id="employee_id" name="employee_id">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="pic" class="col-sm-2 col-form-label">{{__('vehicle.driver')}} <b
                                class="text-danger">*</b></label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="driver" name="driver" placeholder="{{__('vehicle.driver')}}"
                                required>
                            <input type="hidden" class="form-control" id="driver_id" name="driver_id">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="location" class="col-sm-2 col-form-label">{{__('vehicle.loc')}} <b
                                class="text-danger">*</b></label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="location" name="location" placeholder="{{__('vehicle.loc')}}"
                                required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="buy_price" class="col-sm-2 col-form-label">{{__('vehicle.buyprice')}} <b
                                class="text-danger">*</b></label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="buy_price" name="buy_price" placeholder="{{__('vehicle.buyprice')}}"
                                required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="vendor" class="col-sm-2 col-form-label">Vendor <b
                                class="text-danger">*</b></label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="vendor" name="vendor" placeholder="Vendor"
                                required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="buy_date" class="col-sm-2 col-form-label">{{__('vehicle.buydate')}} <b
                                class="text-danger">*</b></label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control datepicker" id="buy_date" name="buy_date" placeholder="{{__('vehicle.buydate')}}"
                                required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="note" class="col-sm-2 col-form-label">{{__('vehicle.note')}}</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="note" name="note" placeholder="{{__('vehicle.note')}}">
                        </div>
                    </div>
                </div>
                <div class="overlay d-none">
                    <i class="fa fa-sync fa-2x fa-spin"></i>
                </div>
            </div>

            <div class="card card-{{config('configs.app_theme')}} card-outline">
                <div class="card-header">
                    <h3 class="card-title">{{__('vehicle.document')}}</h3>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <label for="image" class="col-sm-2 col-form-label">{{__('vehicle.photo')}}</label>
                        <div class="col-sm-6">
                            <input type="file" class="form-control" name="image" id="image" accept="image/*" />
                        </div>
                    </div>
                </div>
                <div class="overlay d-none">
                    <i class="fa fa-sync fa-2x fa-spin"></i>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="add-modal" tabindex="-1" role="dialog" aria-hidden="true" tabindex="-1" role="dialog"
    aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" id="form-pick">
            <div class="modal-header">
                <h4 class="modal-title">{{__('general.chs')}} {{__('general.category')}}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">

                    <div class="col-md-4 pick-category border-right">
                        <ul id="level-one" class="list-group">
                            @foreach ($categories as $category)
                            <li data-path="{{ $category->path }}" data-id="{{ $category->id }}" data-children="false"
                                class="style list-group-item d-flex justify-content-between align-items-center">{{ $category->name }}
                                @foreach ($drafts as $draft)
                                @if ($draft->id == $category->id)
                                <i class="fa fa-chevron-right"></i>
                                @endif
                                @endforeach
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="col-md-4 pick-category border-right">
                        <ul id="level-two" class="list-group">
                        </ul>
                    </div>
                    <div class="col-md-4 pick-category">
                        <ul id="level-three" class="list-group">
                        </ul>
                    </div>
                </div>
                <div class="form-group row mt-2">
					<div class="col-md-2 col-xs-12" for="catname">{{__('gneral.chs')}} {{__('general.chs')}} {{__('general.category')}}</div>
					<div class="col-sm-9 controls">
                        <div class="text-{{config('configs.app_theme')}}"><b name="category_select" id="category_select">{{ $category_name }}</b></div>
                        <input type="hidden" name="asset_category_id" value="{{ $asset_category_id }}">
					</div>
				</div>
            </div>
            <div class="modal-footer">
                {{-- <input type="hidden" name="asset_category_id"> --}}
                <button class="btn btn-{{config('configs.app_theme')}} pull-right btn-sm" data-style="slide-down"
                    type="submit" disabled onclick="gantiCat()">{{__('general.save')}}</button>
                <div class="clearfix"></div>
            </div>
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

    $(document).ready(function () {
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
        $('#form').find('.datepicker').daterangepicker({
            startDate: moment(),
            singleDatePicker: true,
            timePicker: false,
            locale: {
             format: 'YYYY-MM-DD'
            }
        });
        $('input[name=production_year]').inputmask('Regex', {regex: "[0-9]*"});
        //Bootstrap fileinput component
        $("#image").fileinput({
            browseClass: "btn btn-{{config('configs.app_theme')}}",
            showRemove: false,
            showUpload: false,
            allowedFileExtensions: ["png", "jpg", "jpeg"],
            dropZoneEnabled: false,
            initialPreview: '<img src="{{asset('img/no-image.png')}}" class="kv-preview-data file-preview-image">',
            initialPreviewFileType: 'image',
            initialPreviewConfig: [
            {caption: "{{('img/no-image.png')}}", downloadUrl: "{{asset('img/no-image.png')}}", size:"{{ @File::size(public_path('img/no-image.jpg'))}}",url: false}
            ],
            theme: 'explorer-fas'
        });

       
        // validation
        // $(document).on("change", "#image", function () {
        //     if (!$.isEmptyObject($('#form').validate().submitted)) {
        //         $('#form').validate().form();
        //     }
        // });

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
        $( "#pic" ).select2({
            ajax: {
              url: "{{route('employees.select')}}",
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
                    name: `${item.name}`
                  });
                });
                return {
                  results: option, more: more,
                };
              },
            },
            allowClear: true,
        });
          
        $(document).on("change", "#pic", function () {

            var employee_id = $('#pic').select2('data').id;
            var pic         = $('#pic').select2('data').name;

            $('#employee_id').val(`${employee_id}`);
            $('#pic').val(`${pic}`);
        
            if (!$.isEmptyObject($('#form').validate().submitted)) {
                $('#form').validate().form();
            }
        });

        $("#driver" ).select2({
        ajax: {
            url: "{{route('employees.select')}}",
            type:'GET',
            dataType: 'json',
            data: function (term,page) {
            return {
                path:'Driver',
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
                text: item.name,
                name: `${item.name}`
                });
            });
            return {
                results: option, more: more,
            };
            },
        },
        allowClear: true,
        });
          
        $(document).on("change", "#driver", function () {

            var driver_id = $('#driver').select2('data').id;
            var driver      = $('#driver').select2('data').name;

            $('#driver_id').val(`${driver_id}`);
            $('#driver').val(`${driver}`);
        
            if (!$.isEmptyObject($('#form').validate().submitted)) {
                $('#form').validate().form();
            }
        });

    });
</script>
@endpush
