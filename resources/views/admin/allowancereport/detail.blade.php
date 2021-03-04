@extends('admin.layouts.app')

@section('title', 'Detail Allowance')
@push('breadcrump')
    <li class="breadcrumb-item"><a href="{{route('allowancereport.index')}}">Allowance Report</a></li>
    <li class="breadcrumb-item active">Detail</li>
@endpush
@section('stylesheets')
<link rel="stylesheet" href="{{asset('adminlte/component/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}">
<link href="{{asset('adminlte/component/bootstrap-fileinput/css/fileinput.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">

@endsection
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card card-{{config('configs.app_theme')}} card-outline">
            <div class="card-header">
                <h3 class="card-title">Employee Data</h3>
            </div>
            <div class="card-body">
                <form id="form" action="{{route('employees.store')}}" class="form-horizontal" method="post"
                    autocomplete="off">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="row">
                                <input type="hidden" name="report_id" id="report_id" value="{{ $allowance_detail->id }}">
                                <input type="hidden" name="employee_id" value="{{ $allowance_detail->employee_id }}">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Name</label>
                                        <input type="text" class="form-control" placeholder="Name" name="name"
                                            value="{{$employee->name}}" readonly>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <!-- text input -->
                                    <div class="form-group">
                                        <label>NIK Bosung</label>
                                        <input type="text" class="form-control" placeholder="NIK" name="nid"
                                            value="{{$employee->nid}}" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Department</label>
                                        <input type="text" class="form-control" name="department_id" id="department_id"
                                            data-placeholder="Select Department" readonly>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <!-- text input -->
                                    <div class="form-group">
                                        <label>Position</label>
                                        <input type="text" class="form-control" name="title_id" id="title_id"
                                            data-placeholder="Select Position" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <!-- text input -->
                                    <div class="form-group">
                                        <label>Work Group Combination</label>
                                        <input type="text" class="form-control" name="workgroup_id" id="workgroup_id"
                                            data-placeholder="Select Workgroup" readonly>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Grade</label>
                                        <input type="text" class="form-control" name="grade_id" id="grade_id"
                                            data-placeholder="Select Grade" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="row">
                                <div class="col-sm-12">
                                    <!-- text input -->
                                    <div class="form-group">
                                        <label for="app_logo" class="col-sm-5 control-label">Image</label>
                                        <div class="col-sm-12"
                                            style="border:1px solid #bdc3c7; border-radius:5px; height:203px; padding-top:10px;">
                                            <input type="file" class="form-control" name="photo" id="picture"
                                                accept="photo/*" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </div>
    <div class="col-lg-12">
        <div class="card card-{{ config('configs.app_theme') }} card-outline">
            <div class="card-header">
                <h3 class="card-title">Allowance Details</h3>
            </div>
            <div class="card-body">
                <table class="table table-striped table-bordered datatable" id="table-allowance" style="width: 100%">
                    <thead>
                        <tr>
                            <th width="10">No</th>
                            <th width="600">Description</th>
                            <th width="100">Factor</th>
                            <th width="100">Value</th>
                            <th width="200">Total</th>
                        </tr>
                    </thead>
                    <tfoot>
                      <tr>
                        <th colspan="4" class="text-right">Total</th>
                        <th>Rp {{ number_format($allowance_detail->total, 2, ',', '.') }}</th>
                      </tr>
                    </tfoot>
                </table>
            </div>
            <div class="overlay d-none">
                <i class="fa fa-refresh fa-spin"></i>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
<script src="{{asset('adminlte/component/dataTables/js/datatables.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-fileinput/js/fileinput.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDk0A3uPdfOld8ZG1ibIZRaEktd-2Kv33E"></script>
<script>

    $("#title_id").select2({
    ajax: {
    url: "{{route('title.select')}}",
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
    var more = (page * 30) < data.total; var option=[]; $.each(data.rows, function (index, item) { option.push({ id:
        item.id, text: `${item.name}` }); }); return { results: option, more: more, }; }, }, allowClear: true, });
        @if($employee->title_id)
        $("#title_id").select2('data',{id:{{$employee->title_id}},text:'{{$employee->title->name}}'}).trigger('change');
        @endif
        $(document).on("change", "#title_id", function () {
        if (!$.isEmptyObject($('#form').validate().submitted)) {
        $('#form').validate().form();
        }
        });
    $("#department_id").select2({
                ajax: {
                    url: "{{route('department.select')}}",
                    type: 'GET',
                    dataType: 'json ',
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
			@if($employee->department_id)
      		$("#department_id").select2('data',{id:{{$employee->department_id}},text:'{{$employee->department->name}}'}).trigger('change');
      		@endif
            $(document).on("change", "#department_id", function () {
                if (!$.isEmptyObject($('#form').validate().submitted)) {
                    $('#form').validate().form();
                }
			});
            $("#grade_id").select2({
                ajax: {
                    url: "{{route('grade.select')}}",
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
			@if($employee->grade_id)
      		$("#grade_id").select2('data',{id:{{$employee->grade_id}},text:'{{$employee->grade->name}}'}).trigger('change');
      		@endif
            $(document).on("change", "#grade_id", function () {
                if (!$.isEmptyObject($('#form').validate().submitted)) {
                    $('#form').validate().form();
                }
			});
            $("#workgroup_id").select2({
                ajax: {
                    url: "{{route('workgroup.select')}}",
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
                                id: item.workgroupmaster_id,
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
            @if($employee->workgroup_id)
      		$("#workgroup_id").select2('data',{id:{{$employee->workgroup_id}},text:'{{$employee->workgroup->name}}'}).trigger('change');
      		@endif
            $(document).on("change", "#workgroup_id", function () {
                if (!$.isEmptyObject($('#form').validate().submitted)) {
                    $('#form').validate().form();
                }
            });
            $("#picture").fileinput({
                browseClass: "btn btn-{{config('configs.app_theme')}}",
                showRemove: false,
                showUpload: false,
                allowedFileExtensions: ["png", "jpg", "jpeg"],
                dropZoneEnabled: false,
                initialPreview: '<img src="{{asset($employee->photo)}}" class="kv-preview-data file-preview-image">',
                initialPreviewAsData: false,
                initialPreviewFileType: 'image',
                initialPreviewConfig: [
                    {caption: "{{$employee->photo}}", downloadUrl: "{{asset($employee->photo)}}", size:"{{ @File::size(public_path($employee->photo))}}",url: false}
                ],
                theme:'explorer-fas'
            });
            $(function () {
                dataTable = $('#table-allowance').DataTable({
                    stateSave:true,
                    processing:true,
                    serverSide:true,
                    filter:false,
                    info:false,
                    lengthChange:false,
                    paging: false,
                    responsive:true,
                    ordering: false,
                    ajax: {
                    url: "{{route('allowancereportdetail.read')}}",
                    type: "GET",
                    data:function(data){
                        var report_id = $('#report_id').val();
                        data.report_id = report_id;
                    }
                    },
                    columnDefs:[
                    { orderable: false,targets:[0] },
                    { className: "text-right", targets: [4] }
                    ],
                    columns: [
                    { data: "no" },
                    { data: "allowance" },
                    { data: "factor" },
                    { data: "total" },
                    { data: "value", render: $.fn.dataTable.render.number( '.', ',', 0, 'Rp. ' )}
                    ],
                    // footerCallback: function(row, data, start, end, display) {
                    // var api = this.api(), data;

                    // var intVal = function ( i ) {
                    //     return typeof i === 'string' ? i.replace(/[\$,]/g, '')*1 : typeof i === 'number' ? i : 0;
                    // };

                    // total = api.column( 2 ).data().reduce( function (a, b) { return intVal(a) + intVal(b);}, 0 );

                    // pageTotal = api.column( 2, { page: 'current'} ).data().reduce( function (a, b) {return intVal(a) + intVal(b);}, 0 );
                    // var numFormat = $.fn.dataTable.render.number('.', ',', 0, 'Rp. ' ).display;

                    // $( api.column( 2 ).footer() ).html(numFormat(total));
                    // $('#gross').attr('data-gross', total);
                    // }
                });
                });
</script>
@endpush
