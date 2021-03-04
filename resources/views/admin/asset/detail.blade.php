@extends('admin.layouts.app')

@section('title', 'Detail Asset')
@push('breadcrump')
<li class="breadcrumb-item"><a href="{{route('asset.index')}}">Asset</a></li>
<li class="breadcrumb-item active">Detail</li>
@endpush
@section('stylesheets')

@endsection
@section('content')

<div class="row">
    <div class="col-lg-12">
        <div class="card card-{{config('configs.app_theme')}} card-outline">
            <div class="card-header p-0 border-bottom-0">
                <ul class="nav nav-tabs">
                    <li class="nav-item"><a class="nav-link" href="#information" data-toggle="tab">Information</a></li>
                    <li class="nav-item"><a class="nav-link" href="#history" data-toggle="tab">History</a></li>
                </ul>
            </div>
            <div class="tab-content">
                <div class="tab-pane active" id="information">
                        <div class="card-header">
                            <h3 class="card-title">Information Aset</h3>
                                <h3 class="card-title center"></h3>
                                <!-- tools box -->
                                <div class="pull-right card-tools">
                                    <a href="javascript:void(0)" onclick="backurl()" class="btn btn-sm btn-default" title="Kembali"><i
                                            class="fa fa-reply"></i></a>
                                </div>
                                <!-- /. tools -->
                        </div>
                        <div class="card-body">
                            <div class="row">
                            <div class="col-md-4">
                                <div class="col-md-12">
                                    <img class="img-fluid" src="{{ asset($asset->image) }}">
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="row mb-3">
                                    <div class="col-md-2"><b>Category</b></div>
                                    <div class="col-md-10">{{ $asset->assetcategory->name }}</div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-2"><b>Code</b></div>
                                    <div class="col-md-10">{{ $asset->name }}</div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-2"><b>Name</b></div>
                                    <div class="col-md-10">{{ $asset->name }}</div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-2"><b>PIC</b></div>
                                    <div class="col-md-10">{{ $asset->pic }}</div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-2"><b>Location</b></div>
                                    <div class="col-md-10">{{ $asset->location }}</div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-2"><b>Buy Price</b></div>
                                    <div class="col-md-10">{{ number_format($asset->buy_price,0,',','.') }}</div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-2"><b>Buy Date</b></div>
                                    <div class="col-md-10">{{ $asset->buy_date }}</div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-2"><b>Note</b></div>
                                    <div class="col-md-10">{{ $asset->note }}</div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-2"><b>Stock</b></div>
                                    <div class="col-md-10">{{ number_format($asset->stock,0,',','.') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            
                <div class="tab-pane" id="history">
                    <div class="card-header">
                        <h3 class="card-title">History</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <table class="table table-bordered table-striped" style="width:100%"> 
                                <thead>
                                        <tr>
                                            <th width="100">PIC</th>
                                            <th width="100">Location</th>
                                            <th width="50" class="text-center">Stock</th>
                                            <th width="50">Date</th>
                                        </tr>
                                </thead>
                                <tbody>
                                        @foreach($asset->assethistories as $assethistory)
                                        <tr>
                                            <td width="100">{{$assethistory->pic}}</td>
                                            <td width="100">{{$assethistory->location}}</td>
                                            <td width="50" class="text-center">{{number_format($assethistory->stock,0,',','.')}}</td>
                                            <td width="50">{{$assethistory->created_at}}</td>
                                        </tr>
                                        @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
        $('input[name=best_asset]').iCheck({
            checkboxClass: 'icheckbox_square-green',
            radioClass: 'iradio_square-green',
        });
    });
</script>
@endpush
