@extends('admin.layouts.app')

@section('title',__('vehicle.vehicle'))
@push('breadcrump')
<li class="breadcrumb-item"><a href="{{route('vehicle.index')}}">{{__('vehicle.vehicle')}}</a></li>
<li class="breadcrumb-item active">{{__('general.crt')}}</li>
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
        <form id="form" action="{{route('vehicle.create')}}" class="form-horizontal" method="get" autocomplete="off">
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
                        <label for="license_no" class="col-sm-2 col-form-label">{{__('vehicle.platno')}}<b
                                class="text-danger">*</b></label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="license_no" name="license_no" placeholder="{{__('vehicle.platno')}}" required>
                        </div>
                    </div>

                    {{-- <div class="row">
                        <div class="col-md-4 pick-category border-right">
                            <ul id="level-one" class="list-group">
                                @foreach ($categories as $category)
                                    <li data-path="{{ $category->path }}" data-id="{{ $category->id }}" data-children="true"
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
                    </div> --}}
                    <div class="row">
                        <div class="col-md-4 pick-category border-right">
                            <ul id="level-one" class="list-group">
                                @foreach ($categories as $category)
                            <li data-path="{{ $category->path }}" data-id="{{ $category->id }}" data-children="{{$category->children?true:false}}"
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


                <div class="row mt-2">
                    <div for="category_select" class="col-sm-2"><b>{{__('vehicle.vehcat')}}</b> <b
                            class="text-danger">*</b></div>
                    <div class="col-sm-6">
                        <div class="text-{{config('configs.app_theme')}}"><b id="category_select">{{__('asset.no')}} {{__('vehicle.vehcat')}}</b></div>
                        <input type="hidden" name="asset_category_id">
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
                $('#form').find('button[type=submit]').attr('disabled',true);
                $('#form').find('input[name=asset_category_id]').attr('value', $(this).data('id'));
                $('#form').find('input[name=category_name]').attr('value', $(this).data('path'));
                $('#category_select').html($(this).data('path'));
            } else {
                $('#form').find('button[type=submit]').attr('disabled',false);
                $('#form').find('input[name=asset_category_id]').attr('value', $(this).data('id'));
                $('#form').find('input[name=category_name]').attr('value', $(this).data('path'));
                $('#category_select').html($(this).data('path'));
            }
        });

        $("#level-two").on("click", ' li', function () {
            $("#level-two li").removeClass('selected');
            $(this).addClass('selected');
            if ($(this).data('children')) {
                $('#form').find('input[name=asset_category_id]').attr('value', $(this).data('id'));
                $('#form').find('input[name=category_name]').attr('value', $(this).data('path'));
                $('#category_select').html($(this).data('path'));
            } else {
                $('#form').find('input[name=asset_category_id]').attr('value', $(this).data('id'));
                $('#form').find('input[name=category_name]').attr('value', $(this).data('path'));
                $('#category_select').html($(this).data('path'));
            }
        });
        $("#level-three").on("click", ' li', function () {
            $("#level-three li").removeClass('selected');
            $(this).addClass('selected');
            $('#form').find('input[name=asset_category_id]').attr('value', $(this).data('id'));
            $('#form').find('input[name=category_name]').attr('value', $(this).data('path'));
            $('#category_select').html($(this).data('path'));
        });

        $("#form").validate({
            errorElement: 'div',
            errorClass: 'invalid-feedback',
            focusInvalid: false,
            rules: {
                asset_name: {
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
                url: "{{ route('vehicle.subcat') }}",
                type: "POST",
                data: {
                    cat_id: cat_id
                },
                success: function (data) {
                    // console.log(data);
                    $('#level-two').empty();
                    $('#level-three').empty();
                    $.each(data.subcategories[0].subcategories, function (index, subcategory) {
                        if(subcategory.children > 0){
                            $('#level-two').append('<li class="style list-group-item d-flex justify-content-between align-items-center" data-path="'+subcategory.path+'" data-id="'+ subcategory.id +'" data-children="true">'+subcategory.name+
                            '<i class="fa fa-chevron-right"></i>'+
                        '</li>');
                        }
                        else{
                            $('#level-two').append('<li class="style list-group-item d-flex justify-content-between align-items-center" data-path="'+subcategory.path+'" data-id="'+ subcategory.id +'" data-children="false">'+subcategory.name+
                        '</li>');
                        }
                    })
                    // $('#level-two').empty();
                    // $.each(data.subcategories[0].subcategories, function (index, subcategory) {
                    // $('#level-two').append('<li class="style list-group-item d-flex justify-content-between align-items-center" data-path="'+subcategory.path+'" data-id="'+ subcategory.id +'" data-children="true">'+subcategory.name+
                    //     '<i class="fa fa-chevron-right"></i>'+
                    // '</li>');
                    // })
                }
            })
        });

        $('#level-two').on('click', function (e) {
            var cat_id = $(e.target).attr("data-id");
            $.ajax({
                url: "{{ route('vehicle.subcat') }}",
                type: "POST",
                data: {
                    cat_id: cat_id
                },
                success: function (data) {
                    $('#level-three').empty();
                    $.each(data.subcategories[0].subcategories, function (index, subcategory) {
                        $('#level-three').append('<li class="style list-group-item d-flex justify-content-between align-items-center" data-path="'+subcategory.path+'" data-id="'+ subcategory.id +'" data-children="true">'+subcategory.name+' </li>');
                    })
                }
            })
        });
    });

</script>


@endpush
