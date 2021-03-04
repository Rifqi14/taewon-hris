@extends('admin.layouts.app')

@section('title', 'Detail Produk')
@push('breadcrump')
<li class="breadcrumb-item"><a href="{{route('product.index')}}">Produk</a></li>
<li class="breadcrumb-item active">Detail</li>
@endpush
@section('stylesheets')

@endsection
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title center"></h3>
                <!-- tools box -->
                <div class="pull-right card-tools">
                    <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i
                            class="fa fa-reply"></i></a>
                </div>
                <!-- /. tools -->
            </div>
        </div>


        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Informasi Produk</h3>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-2"><b>Tipe</b></div>
                    <div class="col-md-10">{{ $product->type }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-2"><b>Nama Produk</b></div>
                    <div class="col-md-10">{{ $product->name }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-2"><b>Deskripsi Produk</b></div>
                    <div class="col-md-10">{!! $product->description !!}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-2"><b>Kategori Produk</b></div>
                    <div class="col-md-10">{{ $product->productcategory->name }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-2"><b>Produk Unggulan</b></div>
                    <div class="col-sm-4">
                        <input class="form-control" type="checkbox" name="best_product" @if($product->best_product)
                        checked @endif> <i></i>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-2"><b>Merk</b></div>
                    <div class="col-md-10">{{ $product->merk }}</div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Informasi Penjualan</h3>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-2"><b>Harga</b></div>
                    <div class="col-md-10">Rp. {{ $product->price }}</div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Pengaturan Media</h3>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-2"><b>Foto Produk</b></div>
                    @foreach ($img as $item)
                    <div class="col-md-2">
                        <img src="{{ asset($item) }}" alt="" style="height:100px; width:100px;">
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Informasi Pengiriman</h3>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-2"><b>Berat</b></div>
                    <div class="col-md-10">{{ $product->weight }} gr</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-2"><b>Ukuran Paket</b></div>
                    <div class="col-md-2">{{ $product->volume_l }} cm</div>
                    <div class="col-md-2">{{ $product->volume_p }} cm</div>
                    <div class="col-md-2">{{ $product->volume_t }} cm</div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Lainya</h3>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-2"><b>Kondisi</b></div>
                    <div class="col-md-10">{{ $product->condition }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-2"><b>UOM</b></div>
                    <div class="col-md-10">{{ $product->uom->name }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-2"><b>SKU</b></div>
                    <div class="col-md-10">{{ $product->sku }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-2"><b>Barcode</b></div>
                    <div class="col-md-10">{{ $product->barcode }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-2"><b>Stok</b></div>
                    <div class="col-md-10">{{ $product->minimum_qty }}</div>
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
        $('input[name=best_product]').iCheck({
            checkboxClass: 'icheckbox_square-green',
            radioClass: 'iradio_square-green',
        });
    });
</script>
@endpush
