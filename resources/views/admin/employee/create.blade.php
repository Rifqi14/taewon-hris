@extends('admin.layouts.app')

@section('title', 'Employee')
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/css/fileinput.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.css')}}" rel="stylesheet">
<link rel="stylesheet" href="{{asset('adminlte/component/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}">
@endsection

@push('breadcrump')
<li class="breadcrumb-item active"><a href="{{route('employee.index')}}">Employee</a></li>
<li class="breadcrumb-item active">Create</li>
@endpush


@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card card-{{ config('configs.app_theme') }} card-outline card-outline-tabs" id="primary-card">
            <div class="card-header p-0 border-bottom-0">
                <ul class="nav nav-tabs">
                    <li class="nav-item"><a class="nav-link active" href="#personal" id="personal-data"
                        data-toggle="tab">Personal Data</a>
                    </li>
                </ul>
            </div>
            <div class="tab-content">
                <div class="tab-pane active" id="personal">
                    <div class="card-header">
                        <h3 class="card-title">Employee Data</h3>
                        <div class="pull-right card-tools">
                            <button form="form" type="submit" class="btn btn-sm btn-{{ config('configs.app_theme') }}"
                                title="Simpan"><i class="fa fa-save"></i></button>
                            <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i
                                    class="fa fa-reply"></i></a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-8">
                            <div class="card-body">
                                <form id="form" action="{{route('employee.store')}}" class="form-horizontal"
                                    method="post" autocomplete="off">
                                    {{ csrf_field() }}
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Name</label>
                                                <input type="text" class="form-control" placeholder="Name" name="name">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <!-- text input -->
                                            <div class="form-group">
                                                <label>NIK Bosung</label>
                                                <input type="text" class="form-control" placeholder="NIK" name="nid">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <!-- text input -->
                                            <div class="form-group">
                                                <label>Position</label>
                                                <input type="text" class="form-control" name="title_id"
                                                    id="title_id" data-placeholder="Pilih Position" required>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Department</label>
                                                <input type="text" class="form-control" name="department_id"
                                                    id="department_id" data-placeholder="Pilih Department" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <!-- text input -->
                                            <div class="form-group">
                                                <label>Work Group Combination</label>
                                                <input type="text" class="form-control" name="workgroup_combination"
                                                    id="workgroup_combination" data-placeholder="Pilih Workgroup"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Grade</label>
                                                <input type="text" class="form-control" name="grade_id" id="grade_id"
                                                    data-placeholder="Pilih Grade" required>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <!-- text input -->
                                        <div class="form-group">
                                            <label for="photo">Photo </label>
                                            <input type="file" class="form-control" name="photo" id="photo"
                                                accept="image/*" required />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-8">
                <div class="card card-{{ config('configs.app_theme') }} card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Personal Data</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <!-- text input -->
                                <div class="form-group">
                                    <label>No.KTP</label>
                                    <input type="text" class="form-control" placeholder="No.KTP" name="nik" max="16">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>NPWP</label>
                                    <input type="text" class="form-control" placeholder="NPWP" name="npwp" max="15">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <!-- text input -->
                                <div class="form-group">
                                    <label>Birthday</label>
                                    <!-- <input type="date" class="form-control" placeholder="Birthaday" name="birth_date"> -->
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="far fa-calendar-alt"></i>
                                            </span>
                                        </div>
                                        <input type="text" name="birth_date" class="form-control datepicker"
                                            id="birth_date" placeholder="Birthday">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Gender</label>
                                    <select name="gender" id="gender" class="form-control select2"
                                        data-placeholder="Select Gender">
                                        <option value="laki-laki">Laki-Laki</option>
                                        <option value="perempuan">Perempuan</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <!-- text input -->
                                <div class="form-group">
                                    <label>BPJS Tenaga Kerja</label>
                                    <input type="text" class="form-control" placeholder="BPJS Tenaga Kerja"
                                        name="bpjs_tenaga_kerja" >
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <!-- text input -->
                                <div class="form-group">
                                    <label>PTKP</label>
                                    <select name="ptkp" id="ptkp" class="form-control select2"
                                        data-placeholder="Select PTKP">
                                        <option value=""></option>
                                        @foreach(config('enums.ptkp') as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <!-- text input -->
                                <div class="form-group">
                                    <label>Phone No</label>
                                    <input type="number" class="form-control" placeholder="Phone No" name="phone">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Email</label>
                                    <!-- <input type="Email" class="form-control" placeholder="Email" name="email"> -->
									<div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="fas fa-envelope"></i>
                                            </span>
                                        </div>
                                        <input type="email" name="email" class="form-control"
                                            id="email" placeholder="Email">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <!-- text input -->
                                <div class="form-group">
                                    <label>Address</label>
                                    <input type="text" class="form-control" placeholder="Address" name="address">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Province</label>
                                    <input type="text" class="form-control" name="province_id" id="province_id"
                                        data-placeholder="Pilih Province" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <!-- text input -->
                                <div class="form-group">
                                    <label>City</label>
                                    <input type="text" class="form-control" name="place_of_birth" id="place_of_birth"
                                        data-placeholder="Pilih Region" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <!-- text input -->
                                <div class="form-group">
                                    <label>Account No</label>
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control" name="account_bank"
                                                placeholder="Bank">
                                        </div>
                                        <div class="col-sm-8">
                                            <input type="number" class="form-control" name="account_no"
                                                placeholder="Account No">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Account Name</label>
                                    <input type="text" class="form-control" placeholder="Account Name"
                                        name="account_name">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <!-- text input -->
                                <div class="form-group">
                                    <label>Emergency Contact No</label>
                                    <input type="number" class="form-control" placeholder="Emergency Contact No"
                                        name="emergency_contact_no">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Emergency Contact Name</label>
                                    <input type="text" class="form-control" placeholder="Emergency Contact Name"
                                        name="emergency_contact_name">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <!-- text input -->
                                <div class="form-group">
                                    <label>Working Time Type</label>
                                    <select name="working_time_type" id="working_time_type" class="form-control select2"
                                        data-placeholder="Select Working Time">
                                        <option value=""></option>
                                        @foreach(config('enums.workingtime_type') as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Working Time</label>
                                    <input type="text" class="form-control" name="working_time" id="working_time_id"
                                        data-placeholder="Pilih Working Time" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Calendar</label>
                                    <input type="text" class="form-control select2" name="calendar_id" id="calendar_id"
                                        data-placeholder="Select Calendar">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <!-- text input -->
                                <div class="form-group">
                                    <label>Tax Calculation Method</label>
                                    <select name="tax_calculation" id="calculation" class="form-control select2"
                                        data-placeholder="Select Calculation">
                                        <option value=""></option>
                                        @foreach(config('enums.calculation') as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="overlay d-none">
                        <i class="fa fa-refresh fa-spin"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card card-{{ config('configs.app_theme') }} card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Other</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <!-- text input -->
                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" id="status" class="form-control select2"
                                        data-placeholder="Select Status">
                                        <option value="1">Active</option>
                                        <option value="0">Not Active</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>Notes</label>
                                    <textarea type="text" class="form-control" name="notes"
                                        placeholder="Notes"> </textarea>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>Join The Union Labor</label>
                                    <select id="join" class="form-control select2" name="join"
                                        data-placeholder="Select Join Teh Union Labor">
                                        <option value="yes">Yes</option>
                                        <option value="no">No</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>Outsourcing</label>
                                    <input type="text" class="form-control" name="outsourcing" id="outsourcing"
                                        placeholder="Outsourcing">
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>Join Date</label>
                                    <!-- <input type="date" class="form-control select2" name="join_date" id="join_date"> -->
									<div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="far fa-calendar-alt"></i>
                                            </span>
                                        </div>
                                        <input type="text" name="join_date" class="form-control datepicker"
                                            id="join_date" placeholder="Join Date">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>Resign Date</label>
                                    <!-- <input type="date" class="form-control select2" name="resign_date" id="resign_date"> -->
									<div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="far fa-calendar-alt"></i>
                                            </span>
                                        </div>
                                        <input type="text" name="resign_date" class="form-control datepicker"
                                            id="resign_date" placeholder="Resign Date">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div style="height: 307px;"></div>
                    </div>
                    <div class="overlay d-none">
                        <i class="fa fa-2x fa-sync-alt fa-spin"></i>
                    </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
    @endsection
    @push('scripts')
    <script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
    <script src="{{asset('adminlte/component/bootstrap-fileinput/js/fileinput.min.js')}}"></script>
    <script src="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.js')}}"></script>
    <script src="{{asset('adminlte/component/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
    <script>
        $(document).ready(function () {
            $("#status").select2();
            $("#join").select2();
            $("#gender").select2();
            $("#working_time_type").select2();
            $("#working_time").select2();
            $("#calculation").select2();
            $("#ptkp").select2();
			$('input[name=birth_date]').datepicker({
				autoclose: true,
				format: 'yyyy-mm-dd'
			})
			$('input[name=birth_date]').on('change', function(){
				if (!$.isEmptyObject($(this).closest("form").validate().submitted)) {
					$(this).closest("form").validate().form();
				}
			});
			$('input[name=join_date]').datepicker({
				autoclose: true,
				format: 'yyyy-mm-dd'
			})
			$('input[name=join_date]').on('change', function(){
				if (!$.isEmptyObject($(this).closest("form").validate().submitted)) {
					$(this).closest("form").validate().form();
				}
			});
			$('input[name=resign_date]').datepicker({
				autoclose: true,
				format: 'yyyy-mm-dd'
			})
			$('input[name=resign_date]').on('change', function(){
				if (!$.isEmptyObject($(this).closest("form").validate().submitted)) {
					$(this).closest("form").validate().form();
				}
			});
            $("#calendar_id").select2({
                ajax: {
                    url: "{{route('calendar.select')}}",
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
            $(document).on("change", "#calendar_id", function () {
                if (!$.isEmptyObject($('#form').validate().submitted)) {
                    $('#form').validate().form();
                }
            });

            $("#workgroup_combination").select2({
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
            $(document).on("change", "#workgroup_combination", function () {
                if (!$.isEmptyObject($('#form').validate().submitted)) {
                    $('#form').validate().form();
                }
            });
            $("#workgroup_combination").on("change", function () {
                if ($(this).val() == 'outsource') {
                    $('#outsourcing').attr('readonly', true);
                } else {
                    $('#outsourcing').attr('readonly', false);
                }
            });
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
            $(document).on("change", "#title_id", function () {
                if (!$.isEmptyObject($('#form').validate().submitted)) {
                    $('#form').validate().form();
                }
            });

            $("#working_time_id").select2({
                ajax: {
                    url: "{{route('workingtime.select')}}",
                    type: 'GET',
                    dataType: 'json',
                    data: function (term, page) {
                        return {
                            working_time_type: $('#working_time_type').val(),
                            description: term,
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
                                text: `${item.description}`
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
            $(document).on("change", "#working_time_id", function () {
                if (!$.isEmptyObject($('#form').validate().submitted)) {
                    $('#form').validate().form();
                }
            });

            $(document).on("change", "#working_time_type", function () {
                if (!$.isEmptyObject($('#form').validate().submitted)) {
                    $('#form').validate().form();
                }
                $('#working_time_id').select2('val', '');
            });

            $("#department_id").select2({
                ajax: {
                    url: "{{route('department.select')}}",
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
            $(document).on("change", "#grade_id", function () {
                if (!$.isEmptyObject($('#form').validate().submitted)) {
                    $('#form').validate().form();
                }
            });

            $("#province_id").select2({
                ajax: {
                    url: "{{route('province.select')}}",
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
            $("#place_of_birth").select2({
                ajax: {
                    url: "{{route('region.select')}}",
                    type: 'GET',
                    dataType: 'json',
                    data: function (term, page) {
                        return {
                            province_id: $('#province_id').val(),
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
            $(document).on("change", "#place_of_birth", function () {
                if (!$.isEmptyObject($('#form').validate().submitted)) {
                    $('#form').validate().form();
                }
            });

            $("#workingtime_type_id").select2({
                ajax: {
                    url: "{{route('province.select')}}",
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
            $(document).on("change", "#workingtime_type_id", function () {
                if (!$.isEmptyObject($('#form').validate().submitted)) {
                    $('#form').validate().form();
                }
            });

            $("#photo").fileinput({
                browseClass: "btn btn-{{ config('configs.app_theme') }}",
                showRemove: false,
                showUpload: false,
                allowedFileExtensions: ["png", "jpg", "jpeg"],
                dropZoneEnabled: false,

                theme: 'explorer-fas'
            });

            $(document).on("change", "#photo", function () {
                if (!$.isEmptyObject($('#form').validate().submitted)) {
                    $('#form').validate().form();
                }
            });

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
        });

    </script>
    @endpush
