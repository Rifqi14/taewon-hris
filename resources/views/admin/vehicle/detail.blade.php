@extends('admin.layouts.app')

@section('title', 'Detail Vehicle')
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
@endsection
@push('breadcrump')
<li class="breadcrumb-item"><a href="{{route('vehicle.index')}}">Vehicle</a></li>
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
                    <li class="nav-item"><a class="nav-link active" href="#information" data-toggle="tab">Information</a></li>
                    <li class="nav-item"><a class="nav-link" href="#history" data-toggle="tab">History</a></li>
                    <li class="nav-item"><a class="nav-link" href="#maintenance" data-toggle="tab">Maintenance</a></li>
                </ul>
            </div>
            <div class="tab-content">
                <div class="tab-pane active" id="information">
                        <div class="card-header">
                            <h3 class="card-title">Information Vehicle</h3>
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
                                    <img src="{{ asset($asset->image) }}" class="img-fluid">
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="row mb-3">
                                            <div class="col-md-4"><b>Category</b></div>
                                            <div class="col-md-8">{{ $asset->assetcategory->name }}</div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-4"><b>Engine No</b></div>
                                            <div class="col-md-8">{{ $asset->engine_no }}</div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-4"><b>Plat Number</b></div>
                                            <div class="col-md-8">{{ $asset->license_no }}</div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-4"><b>Merk</b></div>
                                            <div class="col-md-8">{{ $asset->merk }}</div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-4"><b>Type</b></div>
                                            <div class="col-md-8">{{ $asset->type }}</div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-4"><b>Model</b></div>
                                            <div class="col-md-8">{{ $asset->model }}</div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-4"><b>Manufacture</b></div>
                                            <div class="col-md-8">{{ $asset->manufacture }}</div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-4"><b>Engine Capacity</b></div>
                                            <div class="col-md-8">{{ $asset->engine_capacity }}</div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-4"><b>Production Year</b></div>
                                            <div class="col-md-8">{{ $asset->production_year }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row mb-3">
                                            <div class="col-md-4"><b>PIC</b></div>
                                            <div class="col-md-8">{{ $asset->pic }}</div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-4"><b>Location</b></div>
                                            <div class="col-md-8">{{ $asset->location }}</div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-4"><b>Buy Price</b></div>
                                            <div class="col-md-8">{{ number_format($asset->buy_price,0,',','.') }}</div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-4"><b>Buy Date</b></div>
                                            <div class="col-md-8">{{ $asset->buy_date }}</div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-4"><b>Note</b></div>
                                            <div class="col-md-8">{{ $asset->note }}</div>
                                        </div>
                                    </div>
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
                                                <select class="form-control select2" name="month" id="month">
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
                                                    <select name="year" class="form-control select2" id="year">
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
                            <table class="table table-bordered table-striped w-100" id="table-history">
                                <thead>
                                    <tr>
                                        <th width="100">PIC</th>
                                        <th width="100">Driver</th>
                                        <th width="100">Location</th>
                                        <th width="50" class="text-center">Stock</th>
                                        <th width="50">Date</th>
                                    </tr>
                                </thead>
                            </table>
                        </form>
                    </div>
                </div>

                <div class="tab-pane" id="maintenance">
                    <div class="card-header">
                        <h3 class="card-title">History Maintenance</h3>
                        <div class="pull-right card-tools">
                            <a href="javascript:void(0)" onclick="exportmaintenance()" class="btn btn-primary btn-sm text-white" data-toggle="tooltip" title="Export" style="cursor: pointer;"><i class="fa fa-download"></i></a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form id="form-maintenance" class="form-horizontal" method="post">
                            {{ csrf_field() }}
                            <input type="hidden" value="{{ $asset->id }}" name="vehicle_id">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label" for="period">Period</label>
                                            <div class="form-row">
                                                <div class="col-sm-8">
                                                    <select class="form-control select2" name="month_histories"
                                                        id="month_histories">
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
                                                        <select name="year_histories" class="form-control select2"
                                                            id="year_histories">
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
                                <table class="table table-bordered table-striped w-100" id="table-maintenance">
                                    <thead>
                                        <tr>
                                            <th width="10">No</th>
                                            <th width="100">Vehicle</th>
                                            <th width="100">Date</th>
                                            <th width="100">Km</th>
                                            <th width="100">Driver</th>
                                            <th width="50">Total</th>
                                            <th width="100">File</th>
                                            <th width="50">Status</th>
                                        </tr>
                                    </thead>
                                </table>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="overlay d-none">
            <i class="fa fa-refresh fa-spin"></i>
        </div>
    </div>
</div>

{{-- Modal Maintenance --}}
<div class="modal fade" id="show-document" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <embed id="url-document" src="" style="height:500px;width:500px">
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
<script src="{{asset('adminlte/component/dataTables/js/datatables.min.js')}}"></script>
<script src="{{asset('adminlte/component/summernote/js/summernote.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-fileinput/js/fileinput.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.js')}}"></script>

<script>
    function showDocument(e){
		$('#url-document').attr("src",$(e).data('url'));
		$('#show-document').modal('show');
    }
    function exportmaintenance() {
        $.ajax({
            url: "{{ route('vehicle.exportmaintenance') }}",
            type: 'POST',
            dataType: 'JSON',
            data: $("#form-maintenance").serialize(),
            beforeSend:function(){
                // $('.overlay').removeClass('d-none');
                waitingDialog.show('Loading...');
            }
        }).done(function(response){
            waitingDialog.hide();
            if(response.status){
            // $('.overlay').addClass('d-none');
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
            waitingDialog.hide();
            var response = response.responseJSON;
            // $('.overlay').addClass('d-none');
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
            url: "{{ route('vehicle.exporthistory') }}",
            type: 'POST',
            dataType: 'JSON',
            data: $("#form-history").serialize(),
            beforeSend:function(){
                // $('.overlay').removeClass('d-none');
                waitingDialog.show('Loading...');
            }
        }).done(function(response){
            waitingDialog.hide();
            if(response.status){
            // $('.overlay').addClass('d-none');
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
            waitingDialog.hide();
            var response = response.responseJSON;
            // $('.overlay').addClass('d-none');
            $.gritter.add({
                title: 'Error!',
                text: response.message,
                class_name: 'gritter-error',
                time: 1000,
            });
        });
    }
    $(document).ready(function (){
        $('input[name=best_asset]').iCheck({
            checkboxClass: 'icheckbox_square-green',
            radioClass: 'iradio_square-green',
        });

        $('.select2').select2();
        dataTable = $('#table-maintenance').DataTable( {
            stateSave:true,
            processing: true,
            serverSide: true,
            filter:false,
            info:false,
            lengthChange:true,
            responsive: true,
            order: [[ 7, "asc" ]],
            ajax: {
                url: "{{route('vehicle.readmaintenance')}}",
                type: "GET",
                data:function(data){
                    data.month = $('select[name=month_histories]').val(),
                    data.year = $('select[name=year_histories]').val(),
                    data.vehicle_id = {{$asset->id}};
                }
            },
            columnDefs:[
                {
                    orderable: false,targets:[0]
                },
                { className: "text-right", targets: [0,5] },
                { className: "text-center", targets: [3,6] },
                {
                    render: function (data, type, row) {
                        // return `<a href="${row.file}" target="_blank"><img class="img-fluid" src="${row.file}" height=\"100\" width=\"150\"/><a/>`
                            return `<a onclick="showDocument(this)" data-url="${row.link}" href="#"><span class="badge badge-info">Prview</span><a/>`
                    },
                    targets: [6]
                },
                {
                    render: function ( data, type, row ) {
                        return `
                        <div class="asset-wrapper">
                            <div class="ml-2">
                                <a href="{{url('admin/maintenance')}}/${row.id}" title="Detail Data">${row.vehicle}</a>
                            </div>
                        </div>`;
                    },
                    targets: [1]
                },
                {
                    render: function (data, type, row) {
                        if (row.status == 1) {
                            return `<span class="badge badge-info">Publish</span>`
                        } else {
                            return `<span class="badge badge-warning">Draft</span>`
                        }
                    },
                    targets: [7]
                }
            ],
            columns: [
                { data: "no" },
                { data: "vehicle"},
                { data: "date"},
                { data: "km"},
                { data: "driver"},
                { data: "total"},
                { data: "image"},
                { data: "status"}
            ]
        });
        dataTableHistory = $('#table-history').DataTable({
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
                url: "{{route('vehicle.readhistories')}}",
                type: "GET",
                data: function (data) {
                    data.month = $('select[name=month]').val(),
                    data.year = $('select[name=year]').val(),
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
                {data: "driver"},
                {data: "location"},
                {data: "stock"},
                {data: "created_at"}
            ]
        });
        $(document).on('change', '#month_histories', function () {
            dataTable.draw();
        });
        $(document).on('change', '#year_histories', function () {
            dataTable.draw();
        });
        $(document).on('change', '#month', function () {
            dataTableHistory.draw();
        });
        $(document).on('change', '#year', function () {
            dataTableHistory.draw();
        });
    });
</script>
@endpush
