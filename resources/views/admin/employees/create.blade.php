@extends('admin.layouts.app')

@section('title',__('employee.employ'))
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/css/fileinput.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/daterangepicker/daterangepicker.css')}}" rel="stylesheet">
@endsection

@push('breadcrump')
<li class="breadcrumb-item active"><a href="{{route('employees.index')}}">{{ __('employee.employ') }}</a></li>
<li class="breadcrumb-item active">{{ __('general.crt') }}</li>
@endpush


@section('content')
    <form id="form" action="{{route('employees.store')}}" class="form-horizontal"
        method="post" autocomplete="off">
        {{ csrf_field() }}
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-{{ config('configs.app_theme') }} card-outline">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('general.crt') }} {{ __('employee.employ') }}</h3>
                        <div class="pull-right card-tools">
                            <button form="form" type="submit" class="btn btn-sm btn-{{ config('configs.app_theme') }}"
                                title="{{ __('general.save') }}"><i class="fa fa-save"></i></button>
                            <a href="#"  onClick="backurl()"  class="btn btn-sm btn-default" title="{{ __('general.prvious') }}"><i
                                    class="fa fa-reply"></i></a>
                        </div>
                    </div>
                    <div class="card-body">

                        <div class="row">
                            <div class="col-lg-8">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>{{ __('general.name') }} <b class="text-danger">*</b></label>
                                            <input type="text" class="form-control" placeholder="{{ __('general.name') }}"
                                                name="name" required>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <!-- text input -->
                                        <div class="form-group">
                                            <label>NIK Taewon <b class="text-danger">*</b></label>
                                            <input type="text" class="form-control" placeholder="Otomatic"
                                                name="nid">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>{{ __('department.dep') }} <b class="text-danger">*</b></label>
                                            <input type="text" class="form-control" name="department_id"
                                                id="department_id" data-placeholder="{{ __('general.chs') }} {{ __('department.dep') }}"
                                                required>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <!-- text input -->
                                        <div class="form-group">
                                            <label>{{ __('position.pos') }} <b class="text-danger">*</b></label>
                                            <input type="text" class="form-control" name="title_id"
                                                id="title_id" data-placeholder="{{ __('general.chs') }} {{ __('position.pos') }}" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <!-- text input -->
                                        <div class="form-group">
                                            <label>{{ __('employee.workcomb') }} <b class="text-danger">*</b></label>
                                            <input type="text" class="form-control" name="workgroup_id"
                                                id="workgroup_id" data-placeholder="{{ __('general.chs') }} {{ __('employee.workcomb') }}"
                                                required>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>{{ __('employee.grade') }} <b class="text-danger">*</b></label>
                                            <input type="text" class="form-control" name="grade_id"
                                                id="grade_id" data-placeholder="{{ __('general.chs') }} {{ __('employee.grade') }}" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <!-- text input -->
                                        <div class="form-group">
                                            <label for="app_logo"
                                                class="col-sm-5 control-label">{{ __('employee.img') }} </label>
                                            <div class="col-sm-12" style="border:1px solid #bdc3c7; border-radius:5px; height:203px; padding-top:10px;">
                                                <input type="file" class="form-control" name="photo"
                                                    id="picture" accept="photo/*" />
                                            </div>
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
                        <h3 class="card-title">{{ __('employee.person_data') }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <!-- text input -->
                                <div class="form-group">
                                    <label>No.KTP <b class="text-danger">*</b></label>
                                    <input type="text" class="form-control" placeholder="No.KTP" name="nik" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>NPWP</label>
                                    <input type="text" class="form-control" placeholder="NPWP" name="npwp">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <!-- text input -->
                                <div class="form-group">
                                    <label>{{ __('employee.pob') }} <b class="text-danger">*</b></label>
                                    <input type="text" class="form-control" name="place_of_birth" id="place_of_birth"
                                        data-placeholder="{{ __('general.chs') }} {{ __('employee.pob') }}" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>{{ __('employee.birthday') }} <b class="text-danger">*</b></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="far fa-calendar-alt"></i>
                                            </span>
                                        </div>
                                        <input type="text" name="birth_date" class="form-control datepicker"
                                            id="birth_date" placeholder="{{ __('employee.birthday') }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>{{ __('employee.gender') }} <b class="text-danger">*</b></label>
                                    <select name="gender" id="gender" class="form-control select2"
                                        data-placeholder="{{ __('general.chs') }} {{ __('employee.gender') }}" required>
                                        <option value=""></option>
                                        <option value="male">{{ __('general.m') }}</option>
                                        <option value="female">{{ __('general.f') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <!-- text input -->
                                <div class="form-group">
                                    <label> {{ __('employee.biomoth') }} <b class="text-danger">*</b></label>
                                    <input type="text" class="form-control" required placeholder="{{ __('employee.biomoth') }}" name="mother_name">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <!-- text input -->
                                <div class="form-group">
                                    <label>{{ __('employee.bpjs') }}</label>
                                    <input type="text" class="form-control" placeholder="{{ __('employee.bpjs') }}"
                                        name="bpjs_tenaga_kerja">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <!-- text input -->
                                <div class="form-group">
                                    <label>PTKP <b class="text-danger">*</b></label>
                                    <select name="ptkp" id="ptkp" class="form-control select2"
                                        data-placeholder="{{ __('general.chs') }} PTKP" required>
                                        <option value=""></option>
                                        @foreach(config('enums.ptkp') as $key => $value)
                                        <option value="{{ $key }}" @if($key=='None') selected @endif>{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <!-- text input -->
                                <div class="form-group">
                                    <label>{{ __('employee.nophone') }} <b class="text-danger">*</b></label>
                                    <input type="number" class="form-control" placeholder="{{ __('employee.nophone') }}" name="phone" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Email <b class="text-danger">*</b></label>
									<div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="fas fa-envelope"></i>
                                            </span>
                                        </div>
                                        <input type="email" name="email" class="form-control"
                                            id="email" placeholder="Email" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <!-- text input -->
                                <div class="form-group">
                                    <label>{{ __('employee.address') }} <b class="text-danger">*</b></label>
                                    <input type="text" class="form-control" placeholder="{{ __('employee.address') }}" name="address" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>{{ __('general.province') }} <b class="text-danger">*</b></label>
                                    <input type="text" class="form-control" name="province_id" id="province_id"
                                        data-placeholder="{{ __('general.chs') }} {{ __('general.province') }}" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <!-- text input -->
                                <div class="form-group">
                                    <label>{{ __('employee.city') }} <b class="text-danger">*</b></label>
                                    <input type="text" class="form-control" name="region_id" id="region_id"
                                        data-placeholder="{{ __('general.chs') }} {{ __('employee.city') }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <!-- text input -->
                                <div class="form-group">
                                    <label>{{ __('employee.acc_no') }}</label>
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control" name="account_bank"
                                                placeholder="Bank">
                                        </div>
                                        <div class="col-sm-8">
                                            <input type="number" class="form-control" name="account_no"
                                                placeholder="{{ __('employee.acc_no') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>{{ __('employee.acc_name') }}</label>
                                    <input type="text" class="form-control" placeholder="{{ __('employee.acc_name') }}"
                                        name="account_name">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <!-- text input -->
                                <div class="form-group">
                                    <label>{{ __('employee.emr_no') }} <b class="text-danger">*</b></label>
                                    <input type="number" class="form-control" placeholder="{{ __('employee.emr_no') }}"
                                        name="emergency_contact_no" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>{{ __('employee.emr_name') }} <b class="text-danger">*</b></label>
                                    <input type="text" class="form-control" placeholder="{{ __('employee.emr_name') }}"
                                        name="emergency_contact_name" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <!-- text input -->
                                <div class="form-group">
                                    <label>{{ __('employee.worktype') }} <b class="text-danger">*</b></label>
                                    <select name="working_time_type" id="working_time_type" class="form-control select2"
                                        data-placeholder="{{ __('general.chs') }} {{ __('employee.worktype') }}"  required >
                                        @foreach(config('enums.workingtime_type') as $value)
                                        <option value="{{ $value }}" @if($value=='Shift') selected @endif>{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>{{ __('employee.workingtime') }}</label>
                                    <input type="text" class="form-control" name="working_time" id="working_time"
                                        data-placeholder="{{ __('general.chs') }} {{ __('employee.workingtime') }}" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>{{ __('calendar.calendar') }} <b class="text-danger">*</b></label>
                                    <input type="text" class="form-control select2" name="calendar_id" id="calendar_id"
                                        data-placeholder="{{ __('general.chs') }} {{ __('calendar.calendar') }}" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <!-- text input -->
                                <div class="form-group">
                                    <label>{{ __('employee.tax_cal') }} <b class="text-danger">*</b></label>
                                    <select name="tax_calculation" id="calculation" class="form-control select2"
                                        data-placeholder="{{ __('general.chs') }} {{ __('employee.tax_cal') }}" required>
                                        <option value=""></option>
                                        @foreach(config('enums.calculation') as $key => $value)
                                        <option value="{{ $key }}" @if($key == 'Gross') selected @endif>{{ $value }}</option>
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
                        <h3 class="card-title">{{ __('general.other') }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <!-- text input -->
                                <div class="form-group">
                                    <label>Status <b class="text-danger">*</b></label>
                                    <select name="status" id="status" class="form-control select2"
                                        data-placeholder="{{ __('general.chs') }} Status" required>
                                        <option value="1">{{ __('general.actv') }}</option>
                                        <option value="0">{{ __('general.noactv') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>{{ __('general.notes') }}</label>
                                    <textarea type="text" class="form-control" name="notes"
                                        placeholder="{{ __('general.notes') }}"> </textarea>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>{{ __('employee.join_labor') }} <b class="text-danger">*</b></label>
                                    <select id="join" class="form-control select2" name="join"
                                        data-placeholder="{{ __('general.chs') }} {{ __('employee.join_labor') }}" required>
                                        <option value="yes">{{ __('general.yes') }}</option>
                                        <option value="no">{{ __('generl.no') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>{{ __('employee.outsrc') }}</label>
                                    <input type="text" class="form-control" name="outsourcing_id" id="outsourcing_id"
                                        placeholder="{{ __('employee.outsrc') }}" readonly>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>{{ __('employee.ot') }} <b class="text-danger">*</b></label>
                                    <select id="overtime" class="form-control select2" name="overtime"
                                        data-placeholder="{{ __('employee.ot') }}" required>
                                        <option value=""></option>
                                        <option value="yes" selected>{{ __('general.yes') }}</option>
                                        <option value="no">{{ __('generl.no') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>{{ __('employee.to') }} <b class="text-danger">*</b></label>
                                    <select id="timeout" class="form-control select2" name="timeout"
                                        data-placeholder="{{ __('employee.to') }}" required>
                                        <option value=""></option>
                                        <option value="yes" selected>{{ __('general.yes') }}</option>
                                        <option value="no">{{ __('generl.no') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>SPL <b class="text-danger">*</b></label>
                                    <select id="spl" class="form-control select2" name="spl"
                                        data-placeholder="{{ __('general.chs') }} SPL" required>
                                        <option value=""></option>
                                        <option value="yes" selected>{{ __('general.yes') }}</option>
                                        <option value="no">{{ __('generl.no') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>{{ __('employee.jd') }} <b class="text-danger">*</b></label>
                                    <!-- <input type="date" class="form-control select2" name="join_date" id="join_date"> -->
									<div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="far fa-calendar-alt"></i>
                                            </span>
                                        </div>
                                        <input type="text" name="join_date" class="form-control datepicker"
                                    id="join_date" placeholder="{{ __('employee.jd') }}" required value="{{date('d/m/Y')}}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>{{ __('employee.rd') }}</label>
                                    <!-- <input type="date" class="form-control select2" name="resign_date" id="resign_date"> -->
									<div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="far fa-calendar-alt"></i>
                                            </span>
                                        </div>
                                        <input type="text" name="resign_date" class="form-control datepicker"
                                            id="resign_date" placeholder="{{ __('employee.rd') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <small>Note : <b class="text-danger">*</b> {{ __('employee.note') }}</small>
                            </div>
                        </div>
                        <div style="height: 120px;"></div>
                    </div>
                    <div class="overlay d-none">
                        <i class="fa fa-2x fa-sync-alt fa-spin"></i>
                    </div>
                </div>
            </div>
        </div>
    </form>
    @endsection
    @push('scripts')
    <script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
    <script src="{{asset('adminlte/component/bootstrap-fileinput/js/fileinput.min.js')}}"></script>
    <script src="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.js')}}"></script>
    <script src="{{asset('adminlte/component/daterangepicker/moment.min.js')}}"></script>
    <script src="{{asset('adminlte/component/daterangepicker/daterangepicker.js')}}"></script>
    <script>
        $(document).ready(function () {
            $("#status").select2();
            $("#join").select2();
            $("#gender").select2();
            $("#working_time_type").select2();
            $("#calculation").select2();
            $("#overtime").select2();
            $("#ptkp").select2();
            $("#timeout").select2();
            $("#spl").select2();
            $("#picture").fileinput({
            browseClass: "btn btn-{{config('configs.app_theme')}}",
                showRemove: false,
                showUpload: false,
                allowedFileExtensions: ["png", "jpg", "jpeg"],
                dropZoneEnabled: false,
                initialPreviewAsData: false,
                initialPreviewFileType: 'image',
                initialPreviewConfig: [
                ],
                theme:'explorer-fas'
            });
			$('input[name=birth_date]').daterangepicker({
                    singleDatePicker: true,
                    autoUpdateInput: false,
                    timePicker: false,
                    locale: {
                        format: 'DD/MM/YYYY'
                    }
            },
            function(chosen_date) {
                $('input[name=birth_date]').val(chosen_date.format('DD/MM/YYYY'));
            });
			$('input[name=birth_date]').on('change', function(){
				if (!$.isEmptyObject($(this).closest("form").validate())) {
					$(this).closest("form").validate().form();
				}
			});
			$('input[name=join_date]').daterangepicker({
                    singleDatePicker: true,
                    autoUpdateInput: false,
                    timePicker: false,
                    locale: {
                        format: 'DD/MM/YYYY'
                    }
                    
            },
            function(chosen_date) {
                $('input[name=join_date]').val(chosen_date.format('DD/MM/YYYY'));
            });
			$('input[name=join_date]').on('change', function(){
				if (!$.isEmptyObject($(this).closest("form").validate())) {
					$(this).closest("form").validate().form();
				}
			});
			$('input[name=resign_date]').daterangepicker({
                    singleDatePicker: true,
                    autoUpdateInput: false,
                    timePicker: false,
                    locale: {
                        format: 'DD/MM/YYYY'
                    }
            },
                    function(chosen_date) {
                        $('input[name=resign_date]').val(chosen_date.format('DD/MM/YYYY'));
                    });
			$('input[name=resign_date]').on('change', function(){
				if (!$.isEmptyObject($(this).closest("form").validate().submitted)) {
					$(this).closest("form").validate().form();
				}
			});
            $(document).on("change", "#ptkp", function () {
                if (!$.isEmptyObject($('#form').validate().submitted)) {
                    $('#form').validate().form();
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
                                id: item.id,
                                code: item.code,
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
            $(document).on("change", "#workgroup_id", function () {
                if (!$.isEmptyObject($('#form').validate().submitted)) {
                    $('#form').validate().form();
                }
                var code = $("#workgroup_id").select2('data').code;
                $('#outsourcing_id').select2('val','');
				if (code == "outsource") {
					$('#outsourcing_id').select2('readonly',false);
                    $('#outsourcing_id').prop('required',true);
				}else{
					$('#outsourcing_id').select2('readonly',true);
                    $('#outsourcing_id').prop('required',false);
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

            $("#working_time").select2({
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
            $(document).on("change", "#working_time_type", function () {
                // alert(this.value);
                if (!$.isEmptyObject($('#form').validate().submitted)) {
                    $('#form').validate().form();
                }
                $('#working_time').select2('val','');
                if (this.value =='Non-Shift') {
                    $('#working_time').select2('readonly',false);
                    $('#working_time').prop('required',true);
                } else {
                    $('#working_time').select2('readonly',true);
                    $('#working_time').prop('required',false);
                }
            });
            $(document).on("change", "#working_time", function () {
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
            $("#place_of_birth").select2({
                ajax: {
                    url: "{{route('region.select')}}",
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
                                text: `${item.type} ${item.name}`
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
            $(document).on("change", "#province_id", function () {
                if (!$.isEmptyObject($('#form').validate().submitted)) {
                    $('#form').validate().form();
                }
            });
            $("#region_id").select2({
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
                                text: `${item.type}  ${item.name}`
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
            $(document).on("change", "#region_id", function () {
                if (!$.isEmptyObject($('#form').validate().submitted)) {
                    $('#form').validate().form();
                }
            });

            $("#outsourcing_id").select2({
                ajax: {
                    url: "{{route('outsourcing.select')}}",
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
            $(document).on("change", "#outsourcing_id", function () {
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
