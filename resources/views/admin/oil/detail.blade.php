@extends('admin.layouts.app')

@section('title', 'Detail Oil')
@push('breadcrump')
<li class="breadcrumb-item"><a href="{{route('oil.index')}}">Oil</a></li>
<li class="breadcrumb-item active">Detail</li>
@endpush
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
@endsection
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card card-{{config('configs.app_theme')}} card-outline">
            <div class="card-header p-0 border-bottom-0">
                <ul class="nav nav-tabs">
                    <li class="nav-item"><a class="nav-link" href="#information" data-toggle="tab">Information</a></li>
                    <li class="nav-item"><a class="nav-link" href="#history" data-toggle="tab">History</a></li>
                    <li class="nav-item"><a class="nav-link" href="#consumeoil" data-toggle="tab">Movement</a></li>
                    <li class="nav-item"><a class="nav-link" href="#consumeoil1" data-toggle="tab">Consume Oil</a></li>
                </ul>
            </div>
            <div class="tab-content">
                <div class="tab-pane active" id="information">
                        <div class="card-header">
                            <h3 class="card-title">Information Oil</h3>
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
                                    <img src="{{ asset($asset->image) }}">
                                </div>
                            </div>
                            <div class="col-md-8">
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
                        <div class="pull-right card-tools">
                            <a href="javascript:void(0)" onclick="exporthistory()" class="btn btn-primary btn-sm text-white" data-toggle="tooltip" title="Export" style="cursor: pointer;"><i class="fa fa-download"></i></a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form id="form-history" class="form-horizontal" method="post">
                            {{ csrf_field() }}
                            <input type="hidden" value="{{ $asset->id }}" name="history_id">
                            <div class="row">    
                                <div class="col-md-4">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label" for="period">Period</label>
                                            <div class="form-row">
                                                <div class="col-sm-8">
                                                    <select class="form-control select2" name="month_histories" id="month_histories">
                                                        <option value="01" @if (date('m', time())=="01" ) selected @endif>
                                                            January</option>
                                                        <option value="02" @if (date('m', time())=="02" ) selected @endif>
                                                            February</option>
                                                        <option value="03" @if (date('m', time())=="03" ) selected @endif>
                                                            March</option>
                                                        <option value="04" @if (date('m', time())=="04" ) selected @endif>
                                                            April</option>
                                                        <option value="05" @if (date('m', time())=="05" ) selected @endif>
                                                            May</option>
                                                        <option value="06" @if (date('m', time())=="06" ) selected @endif>
                                                            June</option>
                                                        <option value="07" @if (date('m', time())=="07" ) selected @endif>
                                                            July</option>
                                                        <option value="08" @if (date('m', time())=="08" ) selected @endif>
                                                            August</option>
                                                        <option value="09" @if (date('m', time())=="09" ) selected @endif>
                                                            September</option>
                                                        <option value="10" @if (date('m', time())=="10" ) selected @endif>
                                                            October</option>
                                                        <option value="11" @if (date('m', time())=="11" ) selected @endif>
                                                            November</option>
                                                        <option value="12" @if (date('m', time())=="12" ) selected @endif>
                                                            December</option>
                                                    </select>
                                                </div>
                                                <div class="col-sm-4">
                                                    <div class="input-group">
                                                        <select name="year_histories" class="form-control select2" id="year_histories">
                                                            @php
                                                            $thn_skr = date('Y');
                                                            @endphp
                                                            @for ($i = $thn_skr; $i >= 1991; $i--)
                                                            <option value="{{ $i }}">{{ $i }}</option>
                                                            @endfor
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <table class="table table-striped table-bordered" style="width:100%" id="histories">
                            <thead>
                                    <tr>
                                        <th style="border-top:none">PIC</th>
                                        <th style="border-top:none">Location</th>
                                        <th style="border-top:none" class="text-center">Stock</th>
                                        <th style="border-top:none">Buy Price</th>
                                        <th style="border-top:none">Date</th>
                                    </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                <div class="tab-pane" id="consumeoil">
                    <div class="card-header">
                        <h3 class="card-title">Movement</h3>
                        <div class="pull-right card-tools">
                            <a href="javascript:void(0)" onclick="exportoil()" class="btn btn-primary btn-sm text-white" data-toggle="tooltip" title="Export" style="cursor: pointer;"><i class="fa fa-download"></i></a>
                        </div>
                    </div>
                    <div class="card-body">
                        {{-- <div class="row"> --}}
                            <form id="form" action="{{ route('salaryreport.store') }}" class="form-horizontal" method="post">
                                {{ csrf_field() }}
                                <input type="hidden" value="{{ $asset->id }}" name="asset_id">
                                <div class="row">    
                                    <div class="col-md-4">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="control-label" for="period">Period</label>
                                                <div class="form-row">
                                                    <div class="col-sm-8">
                                                        <select class="form-control select2" name="month_movement" id="month_movement">
                                                            <option value="01" @if (date('m', time())=="01" ) selected @endif>
                                                                January</option>
                                                            <option value="02" @if (date('m', time())=="02" ) selected @endif>
                                                                February</option>
                                                            <option value="03" @if (date('m', time())=="03" ) selected @endif>
                                                                March</option>
                                                            <option value="04" @if (date('m', time())=="04" ) selected @endif>
                                                                April</option>
                                                            <option value="05" @if (date('m', time())=="05" ) selected @endif>
                                                                May</option>
                                                            <option value="06" @if (date('m', time())=="06" ) selected @endif>
                                                                June</option>
                                                            <option value="07" @if (date('m', time())=="07" ) selected @endif>
                                                                July</option>
                                                            <option value="08" @if (date('m', time())=="08" ) selected @endif>
                                                                August</option>
                                                            <option value="09" @if (date('m', time())=="09" ) selected @endif>
                                                                September</option>
                                                            <option value="10" @if (date('m', time())=="10" ) selected @endif>
                                                                October</option>
                                                            <option value="11" @if (date('m', time())=="11" ) selected @endif>
                                                                November</option>
                                                            <option value="12" @if (date('m', time())=="12" ) selected @endif>
                                                                December</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <div class="input-group">
                                                            <select name="year_movement" class="form-control select2" id="year_movement">
                                                                @php
                                                                $thn_skr = date('Y');
                                                                @endphp
                                                                @for ($i = $thn_skr; $i >= 1991; $i--)
                                                                <option value="{{ $i }}">{{ $i }}</option>
                                                                @endfor
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <table class="table table-striped table-bordered datatable" style="width:100%"> 
                                <thead>
                                        <tr>
                                            <th width="10" class="text-center">#</th>
                                            <th width="100">Note</th>
                                            <th width="50">Type</th>
                                            <th width="20" class="text-center">Qty</th>
                                            <th width="50">Date</th>
                                        </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        {{-- </div> --}}
                    </div>
                </div>
                <div class="tab-pane" id="consumeoil1">
                    <div class="card-header">
                        <h3 class="card-title">Consume Oil</h3>
                        <div class="pull-right card-tools">
                            <a href="javascript:void(0)" onclick="exportconsume()" class="btn btn-primary btn-sm text-white" data-toggle="tooltip" title="Export" style="cursor: pointer;"><i class="fa fa-download"></i></a>
                        </div>
                    </div>
                    <div class="card-body">
                        {{-- <div class="row"> --}}
                            <form id="form-consume" class="form-horizontal" method="post">
                                {{ csrf_field() }}
                                <input type="hidden" value="{{ $asset->id }}" name="oil_id">
                                <div class="row">    
                                    <div class="col-md-4">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="control-label" for="period">Period</label>
                                                <div class="form-row">
                                                    <div class="col-sm-8">
                                                        <select class="form-control select2" name="month_consume" id="month_consume">
                                                            <option value="01" @if (date('m', time())=="01" ) selected @endif>
                                                                January</option>
                                                            <option value="02" @if (date('m', time())=="02" ) selected @endif>
                                                                February</option>
                                                            <option value="03" @if (date('m', time())=="03" ) selected @endif>
                                                                March</option>
                                                            <option value="04" @if (date('m', time())=="04" ) selected @endif>
                                                                April</option>
                                                            <option value="05" @if (date('m', time())=="05" ) selected @endif>
                                                                May</option>
                                                            <option value="06" @if (date('m', time())=="06" ) selected @endif>
                                                                June</option>
                                                            <option value="07" @if (date('m', time())=="07" ) selected @endif>
                                                                July</option>
                                                            <option value="08" @if (date('m', time())=="08" ) selected @endif>
                                                                August</option>
                                                            <option value="09" @if (date('m', time())=="09" ) selected @endif>
                                                                September</option>
                                                            <option value="10" @if (date('m', time())=="10" ) selected @endif>
                                                                October</option>
                                                            <option value="11" @if (date('m', time())=="11" ) selected @endif>
                                                                November</option>
                                                            <option value="12" @if (date('m', time())=="12" ) selected @endif>
                                                                December</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <div class="input-group">
                                                            <select name="year_consume" class="form-control select2" id="year_consume">
                                                                @php
                                                                $thn_skr = date('Y');
                                                                @endphp
                                                                @for ($i = $thn_skr; $i >= 1991; $i--)
                                                                <option value="{{ $i }}">{{ $i }}</option>
                                                                @endfor
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <table class="table table-striped table-bordered" style="width:100%" id="consumeoilDatatable"> 
                                <thead>
                                    <tr>
                                        <th width="10" class="text-center">#</th>
                                        <th width="100">Vehicle</th>
                                        <th width="50">Driver</th>
                                        <th width="20">Used Type</th>
                                        <th width="50">Used Oil</th>
                                        <th width="50">KM</th>
                                        <th width="50">Initial Stock</th>
                                        <th width="50">Stock Left</th>
                                        <th width="50">Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        {{-- </div> --}}
                    </div>
                </div>
            </div>
        </div>
        <div class="overlay d-none">
            <i class="fa fa-refresh fa-spin"></i>
        </div>
    </div>
</div>
<a href=""></a>
@endsection

@push('scripts')
<script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
<script src="{{asset('adminlte/component/summernote/js/summernote.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-fileinput/js/fileinput.min.js')}}"></script>
<script src="{{asset('adminlte/component/dataTables/js/datatables.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.js')}}"></script>

<script>
    function exportoil() {
        $.ajax({
            url: "{{ route('oil.export') }}",
            type: 'POST',
            dataType: 'JSON',
            data: $("#form").serialize(),
            beforeSend:function(){
                $('.overlay').removeClass('d-none');
            }
        }).done(function(response){
            if(response.status){
            $('.overlay').addClass('d-none');
            $.gritter.add({
                title: 'Success!',
                text: response.message,
                class_name: 'gritter-success',
                time: 1000,
            });
            let download = document.createElement("a");
            download.href = response.file;
            document.body.appendChild(download);
            download.download = response.name;
            download.click();
            download.remove();
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
        });
    }
    function exporthistory() {
        $.ajax({
            url: "{{ route('oil.exporthistory') }}",
            type: 'POST',
            dataType: 'JSON',
            data: $("#form-history").serialize(),
            beforeSend:function(){
                $('.overlay').removeClass('d-none');
            }
        }).done(function(response){
            if(response.status){
            $('.overlay').addClass('d-none');
            $.gritter.add({
                title: 'Success!',
                text: response.message,
                class_name: 'gritter-success',
                time: 1000,
            });
            let download = document.createElement("a");
            download.href = response.file;
            document.body.appendChild(download);
            download.download = response.name;
            download.click();
            download.remove();
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
        });
    }
    function exportconsume() {
        $.ajax({
            url: "{{ route('oil.exportconsume') }}",
            type: 'POST',
            dataType: 'JSON',
            data: $("#form-consume").serialize(),
            beforeSend:function(){
                $('.overlay').removeClass('d-none');
            }
        }).done(function(response){
            if(response.status){
            $('.overlay').addClass('d-none');
            $.gritter.add({
                title: 'Success!',
                text: response.message,
                class_name: 'gritter-success',
                time: 1000,
            });
            let download = document.createElement("a");
            download.href = response.file;
            document.body.appendChild(download);
            download.download = response.name;
            download.click();
            download.remove();
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
        });
    }
    $(document).ready(function (){
        $('.select2').select2();
        dataTable = $('.datatable').DataTable({
            stateSave: true,
            processing: true,
            serverSide: true,
            filter: false,
            info: false,
            lengthChange: true,
            responsive: true,
            order: [
                [4, "asc"]
            ],
            ajax: {
                url: "{{route('oil.readconsumeoil')}}",
                type: "GET",
                data: function (data) {
                    data.month = $('select[name=month_movement]').val(),
                    data.year = $('select[name=year_movement]').val(),
                    data.id = "{{ $asset->id }}"
                }
            },
            columnDefs: [{
                    orderable: false,
                    targets: [0]
                },
                {
                    className: "text-center",
                    targets: [0,3]
                },
                //  { render: function ( data, type, row ) {
                //         return `<a href="{{url('admin/consumeoil')}}/${row.id}">${row.note}</a>`;
                //     },targets: [1]
                // },
                
            ],
            columns: [
                {data: "no"},
                {data: "note"},
                {data: "type"},
                {data: "qty"},
                {data: "created_at"}
            ]
        });
        dataTableHistory = $('#histories').DataTable({
            stateSave: true,
            processing: true,
            serverSide: true,
            filter: false,
            info: false,
            lengthChange: true,
            responsive: true,
            order: [
                [1, "asc"]
            ],
            ajax: {
                url: "{{route('oil.readhistories')}}",
                type: "GET",
                data: function (data) {
                    data.month = $('select[name=month_histories]').val(),
                    data.year = $('select[name=year_histories]').val(),
                    data.id = "{{ $asset->id }}"
                }
            },
            columnDefs: [{
                    orderable: false,
                    targets: [0,1,2,3]
                },
                {
                    className: "text-center",
                    targets: [3]
                },
                
            ],
            columns: [
                {data: "pic"},
                {data: "location"},
                {data: "stock"},
                {data: "price"},
                {data: "date"}
            ]
        });
        dataTableConsumeOil = $('#consumeoilDatatable').DataTable({
            stateSave: true,
            processing: true,
            serverSide: true,
            filter: false,
            info: false,
            lengthChange: true,
            responsive: true,
            order: [
                [1, "asc"]
            ],
            ajax: {
                url: "{{route('oil.consumeoil')}}",
                type: "GET",
                data: function (data) {
                    data.month = $('select[name=month_consume]').val(),
                    data.year = $('select[name=year_consume]').val(),
                    data.id = "{{ $asset->id }}"
                }
            },
            columnDefs: [{
                    orderable: false,
                    targets: [0,1,2,3,4,5,6,7,8]
                },
                {
                    className: "text-right",
                    targets: [4,5,6,7]
                },
                { render: function (data, type, row) { 
                    return `${row.license_no}<br>${row.vehicle}`
                    }, targets: [1] },
                { render: function (data, type, row) { 
                    return row.stock - row.engine_oil
                    }, targets: [7] },
                
            ],
            columns: [
                {data: "no"},
                {data: "vehicle"},
                {data: "driver"},
                {data: "type"},
                {data: "engine_oil"},
                {data: "km"},
                {data: "stock"},
                {data: "stock"},
                {data: "date"}
            ]
        });
        $('input[name=best_asset]').iCheck({
            checkboxClass: 'icheckbox_square-green',
            radioClass: 'iradio_square-green',
        });
        $(document).on('change', '#month_consume', function () {
            dataTableConsumeOil.draw();
        });
        $(document).on('change', '#year_consume', function () {
            dataTableConsumeOil.draw();
        });
        $(document).on('change', '#month_histories', function () {
            dataTableHistory.draw();
        });
        $(document).on('change', '#year_histories', function () {
            dataTableHistory.draw();
        });
        $(document).on('change', '#month_movement', function () {
            dataTable.draw();
        });
        $(document).on('change', '#year_movement', function () {
            dataTable.draw();
        });
    });
</script>
@endpush
