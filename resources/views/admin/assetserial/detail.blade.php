@extends('admin.layouts.app')

@section('title', 'Detail Aset Serial')
@push('breadcrump')
<li class="breadcrumb-item"><a href="{{route('asset.index')}}">Asset Serial</a></li>
<li class="breadcrumb-item active">Detail</li>
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

        <form id="form" class="form-horizontal"
            autocomplete="off">
            <div class="card card-{{config('configs.app_theme')}} card-outline">
                <div class="card-header">
                    <h3 class="card-title">Information Asset Serial</h3>
                    <!-- tools box -->
                    <div class="pull-right card-tools">
                        <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Back"><i
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
                                required value="{{ $asset->type }}" style="background-color: transparent; border:none;">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="name" class="col-sm-2 col-form-label">Name Asset </label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="name" name="name" placeholder="Name Asset"
                                required value="{{ $asset->name }}" disabled style="background-color: transparent; border:none;">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="description" class="col-sm-2 col-form-label">Description Asset </label>
                        <div class="col-sm-6">
                            <div class="form-control" style="border: none;">{!! $asset->description !!}</div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div for="assetcategory_id" class="col-sm-2 col-form-label"><b>Category Asset</b></div>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="name" name="name" placeholder="Nama"
                                required value="{{ $asset->assetcategory->name }}" disabled style="background-color: transparent; border:none;">
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
                            <div class="col-sm-10" id="add_conv" style="margin-top: -5px">
                                <table class="table table-borderless" id="table-serial">
                                    <thead>
                                        <tr>
                                            <th>Serial Number</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($asset->assetserials as $serial)
                                        <tr data-id="{{ $serial->id }}">
                                            <td>
                                                <input type="hidden" value="{{ $serial->id }}" name="serial_id[]">
                                                <input type="hidden" value="" name="serial_item[]">
                                                <input type="text" class="form-control" id="serial_no" name="serial_no[]"
                                                    placeholder="Serial Number" value="{{ $serial->serial_no }}" readonly style="background-color: transparent; border:none;">
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

@endpush
