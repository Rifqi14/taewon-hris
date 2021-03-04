@extends('admin.layouts.app')

@section('title', 'Salary Increases')
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/css/fileinput.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/daterangepicker/daterangepicker.css')}}" rel="stylesheet">
@endsection

@push('breadcrump')
<li class="breadcrumb-item active"><a href="{{route('salaryincreases.index')}}">Salary Increases</a></li>
<li class="breadcrumb-item active">Create</li>
@endpush


@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="row">
            <div class="col-lg-8">
                <div class="card card-{{ config('configs.app_theme') }} card-outline">
                    <div class="card-header" style="height:55px;">
                        <h3 class="card-title">Increases Data</h3>
                    </div>
                    <div class="card-body">
                        <form id="form" action="{{ route('salaryincreases.store') }}" method="post" autocomplete="off">
                            {{ csrf_field() }}
                            <div class="row">
                                {{-- <div class="col-sm-6">
                                    <!-- text input -->
                                    <div class="form-group">
                                        <label>Ref No</label>
                                        <input type="text" class="form-control" placeholder="Ref No" name="ref">
                                    </div>
                                </div> --}}
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Date</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="far fa-calendar-alt"></i>
                                                </span>
                                            </div>
                                            <input type="text" name="date" class="form-control datepicker" id="date"
                                                placeholder="Date" value="{{date('d/m/Y')}}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <!-- text input -->
                                    <div class="form-group">
                                        <label>Increases Type</label>
                                        <select name="increases_type" class="form-control select2"
                                            data-placeholder="Select Increases Type" id="increases_type">
                                            <option value=""></option>
                                            <option value="basic_salary">Basic Salary</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Value</label>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <select class="form-control select2" data-placeholder="Type" name="type"
                                                    id="type">
                                                    <option value=""></option>
                                                    <option value="Percentage">Percentage</option>
                                                    <option value="Nominal">Nominal</option>
                                                </select>
                                            </div>
                                            <div class="col-sm-6">
                                                <input type="text" name="value" class="form-control"
                                                    placeholder="Value">
                                            </div>
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
            <div class="col-lg-4">
                <div class="card card-{{ config('configs.app_theme') }} card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Other</h3>
                        <div class="pull-right card-tools">
                            <button form="form" type="submit" class="btn btn-sm btn-{{ config('configs.app_theme') }}"
                                title="Simpan"><i class="fa fa-save"></i></button>
                            <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i
                                    class="fa fa-reply"></i></a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>Notes</label>
                                    <textarea style="height:110px;" type="text" class="form-control" name="notes"
                                        placeholder="Notes"> </textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="overlay d-none">
                        <i class="fa fa-2x fa-sync-alt fa-spin"></i>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{-- Modal Career --}}
    <div class="modal fade" id="add_career" tabindex="-1" role="dialog" aria-hidden="true" role="dialog"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="overlay-wrapper">
                    <div class="modal-header">
                        <h4 class="modal-title">Add Employee</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="form_career" class="form-horizontal" method="post" autocomplete="off">
                            <input type="hidden" name="employee_id">
                            <div class="d-flex">
                                <div class="form-group col-sm-6">
                                    <label for="position" class="control-label">Employee Name</label>
                                    <input type="text" class="form-control" id="position" name="position"
                                        placeholder="Position">
                                </div>
                                <div class="form-group col-sm-6">
                                    <label for="grade" class="control-label">Employee ID</label>
                                    <input type="text" class="form-control" id="grade" name="grade" placeholder="Grade"
                                        value="1">
                                </div>
                            </div>
                            <div class="d-flex">
                                <div class="form-group col-sm-6">
                                    <label for="department" class="control-label">Department</label>
                                    <input type="text" class="form-control" id="department" name="department"
                                        placeholder="Department">
                                </div>
                                <div class="form-group col-sm-6">
                                    <label for="reference" class="control-label">Position</label>
                                    <input type="text" class="form-control" id="reference" name="reference"
                                        placeholder="Reference">
                                </div>
                            </div>
                            <div class="col-12">
                                <table class="table table-bordered table-striped" style="width:100%;"
                                    id="table-employee">
                                    <thead>
                                        <tr>
                                            <th style="text-align:center" width="10">#</th>
                                            <th width="250">Employee</th>
                                            <th width="250">Position</th>
                                            <th width="200">Current Sallary</th>
                                            <th><input type="checkbox" value="" class="i-checks" autocomplete="off" />
                                            </th>
                                            {{-- <th width="10"><input type="checkbox" name="check_all" id="check_all"></th> --}}
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                            {{ csrf_field() }}
                            <input type="hidden" name="_method" />
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button form="form_career" type="submit"
                            class="btn btn-sm btn-{{config('configs.app_theme')}} text-white" title="Simpan"><i
                                class="fa fa-save"></i></button>
                    </div>
                    <div class="overlay d-none">
                        <i class="fa fa-2x fa-sync-alt fa-spin"></i>
                    </div>
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
    <script src="{{asset('adminlte/component/daterangepicker/moment.min.js')}}"></script>
    <script src="{{asset('adminlte/component/daterangepicker/daterangepicker.js')}}"></script>
    <script>
        $(document).ready(function () {
            $('.i-checks').iCheck({
                checkboxClass: 'icheckbox_square-green',
                radioClass: 'iradio_square-green',
            });
            $('#date').daterangepicker({
                singleDatePicker: true,
                timePicker: false,
                timePickerIncrement: 1,
                locale: {
                format: 'DD/MM/YYYY'
                }
            },
            function(chosen_date) {
                $('input[name=date]').val(chosen_date.format('DD/MM/YYYY'));
            });
			$('input[name=date]').on('change', function(){
				if (!$.isEmptyObject($(this).closest("form").validate())) {
					$(this).closest("form").validate().form();
				}
			});
            $('#increases_type').select2({
                allowClear: true,
            });
            $('#type').select2({
                allowClear: true,
            });
            dataTableEmployee = $('#table-upcoming-salary').DataTable({
                stateSave: true,
                processing: true,
                serverSide: true,
                filter: false,
                info: false,
                lengthChange: false,
                responsive: true,
                order: [
                    [5, "asc"]
                ],
                ajax: {
                    url: "{{ route('salaryincreasedetail.read') }}",
                    type: "GET",
                    data: function (data) {
                        var name = $('#form-search').find('input[name=name]').val();
                        var allowance = $('#allowance-id-history').val();
                        data.name = name;
                        data.allowance = allowance;
                    }
                },
                columnDefs: [{
                        orderable: false,
                        targets: [0]
                    },
                    {
                        className: "text-right",
                        targets: [3]
                    },
                    {
                        className: "text-center",
                        targets: [5]
                    },
                    {
                        render: function (data, type, row) {
                            return `${row.employee_name} </br> <small>${row.nid}</small>`
                        },
                        targets: [1]
                    },
                    {
                        render: function (data, type, row) {
                            return `${row.department} </br> <small>${row.position}</small>`
                        },
                        targets: [2]
                    },
                    {
                        render: function (data, type, row) {
                            return `<div class="dropdown">
                        <button class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-bars"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <li><a class="dropdown-item" href="{{url('admin/department')}}/${row.id}/edit"><i
                                        class="fas fa-pencil-alt mr-2"></i> Edit</a></li>
                            <li><a class="dropdown-item delete" href="#" data-id="${row.id}"><i
                                        class="fas fa-trash mr-2"></i> Delete</a></li>
                        </ul>
                    </div>`
                        },
                        targets: [5]
                    }

                ],
                columns: [{
                        data: "no"
                    },
                    {
                        data: "employee_name"
                    },
                    {
                        data: "department"
                    },
                    {
                        data: "current_salary"
                    },
                    {
                        data: "upcoming_amount"
                    },
                    {
                        data: "id"
                    }
                ]
            });
            dataTableEmployee = $('#table-employee').DataTable({
                stateSave: true,
                processing: true,
                serverSide: true,
                filter: false,
                info: false,
                lengthChange: false,
                responsive: true,
                order: [
                    [1, "asc"]
                ],
                ajax: {
                    url: "{{ route('salaryincreases.reademployee') }}",
                    type: "GET",
                    data: function (data) {
                        var name = $('#form-search').find('input[name=name]').val();
                        var allowance = $('#allowance-id-history').val();
                        data.name = name;
                        data.allowance = allowance;
                    }
                },
                columnDefs: [{
                        orderable: false,
                        targets: [-1]
                    },
                    {
                        className: "text-right",
                        targets: [3]
                    },
                    {
                        className: "text-center",
                        targets: [4]
                    },
                    {
                        render: function (data, type, row) {
                            return `${row.name} </br> <small>${row.nid}</small>`
                        },
                        targets: [1]
                    },
                    {
                        render: function (data, type, row) {
                            return `${row.department_name} </br> <small>${row.title_name}</small>`
                        },
                        targets: [2]
                    },
                    {
                        render: function (data, type, row) {
                            // if (row.status == 0) {
                            return `<input type="checkbox" value="1" class="checkcok" autocomplete="off" />`
                            // } else {
                            // return `<span class="badge badge-success"><i class="fa fa-check"></i></span>`
                            // }
                        },
                        targets: [4]
                    },
                ],
                columns: [{
                        data: "no"
                    },
                    {
                        data: "name"
                    },
                    {
                        data: "department_name"
                    },
                    {
                        data: "current_salary"
                    },
                    {
                        data: "id"
                    }
                ]
            });
            $("#form").validate({
                errorElement: 'div',
                errorClass: 'invalid-feedback',
                focusInvalid: false,
                highlight: function (e) {
                $(e).closest('.form-group').removeClass('has-success').addClass('was-validated has-error');
                },

                success: function (e) {
                $(e).closest('.form-group').removeClass('has-error').addClass('has-success');
                $(e).remove();
                },
                errorPlacement: function (error, element) {
                if(element.is(':file')) {
                    error.insertAfter(element.parent().parent().parent());
                }else
                if(element.parent('.input-group').length) {
                    error.insertAfter(element.parent());
                }
                else
                if (element.attr('type') == 'checkbox') {
                    error.insertAfter(element.parent());
                }
                else{
                    error.insertAfter(element);
                }
                },
                submitHandler: function() {
                $.ajax({
                    url:$('#form').attr('action'),
                    method:'post',
                    data: new FormData($('#form')[0]),
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    beforeSend:function(){
                    $('.overlay').removeClass('hidden');
                    }
                }).done(function(response){
                        $('.overlay').addClass('hidden');
                        if(response.status){
                        document.location = response.results;
                        }
                        else{
                        $.gritter.add({
                            title: 'Warning!',
                            text: response.message,
                            class_name: 'gritter-warning',
                            time: 1000,
                        });
                        }
                        return;
                }).fail(function(response){
                    $('.overlay').addClass('hidden');
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
