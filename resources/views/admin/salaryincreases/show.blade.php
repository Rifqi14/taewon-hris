@extends('admin.layouts.app')

@section('title', 'Salary Increases')
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/css/fileinput.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.css')}}" rel="stylesheet">
<link rel="stylesheet" href="{{asset('adminlte/component/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}">
@endsection

@push('breadcrump')
<li class="breadcrumb-item active"><a href="{{route('salaryincreases.index')}}">Salary Increases</a></li>
<li class="breadcrumb-item active">Detail</li>
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
                        <form id="form" action="{{route('salaryincreases.update',['id'=>$salaryincreases->id])}}"
                            method="post" autocomplete="off">
                            {{ csrf_field() }}
                            <input type="hidden" name="_method" value="put">
                            <div class="row">
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
                                                placeholder="Date" value="{{$salaryincreases->date}}" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <!-- text input -->
                                    <div class="form-group">
                                        <label>Increases Type</label>
                                        <select name="increases_type" class="form-control"
                                            data-placeholder="Select Increases Type" disabled id="increases_type">
                                            <option value=""></option>
                                            <option @if($salaryincreases->basic_salary) selected @endif
                                                value="BasicSalary">Basic Salary
                                            </option>
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
                                                <input type="text" class="form-control" name="type" id="type" readonly value="{{$salaryincreases->type}}">
                                            </div>
                                            <div class="col-sm-6">
                                                <input type="text" name="value" class="form-control" placeholder="Value"
                                                    value="{{$salaryincreases->value}}" id="value" readonly>
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
                                        placeholder="Notes" readonly>{{$salaryincreases->notes}}</textarea>
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
        <div class="card card-{{ config('configs.app_theme') }} card-outline card-outline-tabs" id="primary-card">
            <div class="card-header">
                <h3 class="card-title">List Employee</h3>
                <div class="pull-right card-tools">
                    <a href="#" class="btn btn-{{config('configs.app_theme')}} btn-sm text-white add_career"
                        data-toggle="tooltip" title="Tambah">
                        <i class="fa fa-plus"></i>
                    </a>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card-body">
                        <input type="hidden" value="{{$salaryincreases->id}}">
                        <table class="table table-bordered table-striped" id="table-upcoming-salary">
                            <thead>
                                <tr>
                                    <th style="text-align:center" width="10">#</th>
                                    <th width="250">Employee</th>
                                    <th width="250">Position</th>
                                    <th width="100">Current Salary</th>
                                    <th width="100">Upcoming Salary</th>
                                    {{-- <th><input type="checkbox" value="" id="deletemassal" autocomplete="off" /></th> --}}
                                    <th width="100">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Modal List Employee --}}
    <div class="modal fade" id="add_career" tabindex="-1" role="dialog" aria-hidden="true" role="dialog"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
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
                            {{-- <input type="hidden" name="employee_id"> --}}
                        <input type="hidden" name="salaryincrease_id" value="{{$salaryincreases->id}}">
                        <input type="hidden" name="amount" id="amount">
                        <div class="row">
                            <input type="hidden" name="user" value="{{ Auth::guard('admin')->user()->id }}">
                            <div class="col-md-4">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label" for="employee_name">Employee Name</label>
                                        <select name="employee_name[]" id="employee_name" class="form-control select2"
                                            style="width: 100%" aria-hidden="true" multiple>
                                            @foreach ($employees as $employee)
                                            <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label" for="nid">NIK</label>
                                        <input type="text" class="form-control" name="nid"
                                            id="nid">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label" for="position">Position</label>
                                        <select name="position[]" id="position" class="form-control select2"
                                            style="width: 100%" aria-hidden="true" multiple >
                                            @foreach ($titles as $position)
                                            <option value="{{ $position->id }}">{{ $position->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label" for="department">Department</label>
                                        <select name="department[]" id="department" class="form-control select2"
                                            style="width: 100%" aria-hidden="true" multiple>
                                            @foreach ($departments as $department)
                                            <option value="{{ $department->name }}">{{ $department->path }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label" for="workgroup">Workgroup Combination</label>
                                        <select name="workgroup_id[]" id="workgroup_id" class="form-control select2"
                                            style="width: 100%" aria-hidden="true" multiple>
                                            @foreach ($workgroups as $workgroup)
                                            <option value="{{ $workgroup->id }}">{{ $workgroup->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
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
                                            <th><input type="checkbox" value="" id="checkAll" autocomplete="off" />
                                            </th>
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
    <script src="{{asset('adminlte/component/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
    <script>
        $(document).ready(function () {
            // $('.select2').select2();
            $('#employee_name').select2({
                allowClear:true,
            });
            $('#department').select2({
                allowClear:true,
            });
            $('#position').select2({
                allowClear:true,
            });
            $('#workgroup_id').select2({
                allowClear:true,
            });
            $("#checkAll").click(function(){
                $('input:checkbox').not(this).prop('checked', this.checked);
            });
            // $("#deletemassal").click(function(){
            //     $('input:checkbox').not(this).prop('checked', this.checked);
            // });
            $('input[name=date]').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd'
            });
            $('input[name=date]').on('change', function () {
                if (!$.isEmptyObject($(this).closest("form").validate())) {
                    $(this).closest("form").validate().form();
                }
            });
            $('#increases_type').select2({
                allowClear: true,
            });
            // $('#type').select2({
            //     allowClear: true,
            // });
            $("#form_career").validate({
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
                    } else {
                        error.insertAfter(element);
                    }
                },
                submitHandler: function () {
                    $.ajax({
                        url: $('#form_career').attr('action'),
                        method: 'post',
                        data: new FormData($('#form_career')[0]),
                        processData: false,
                        contentType: false,
                        dataType: 'json',
                        beforeSend: function () {
                            $('.overlay').removeClass('d-none');
                        }
                    }).done(function (response) {
                        $('.overlay').addClass('d-none');
                        $("#check_all").prop('checked', false);
                        if (response.status) {
                            $('#add_career').modal('hide');
                            dataTableEmployeeUpcoming.draw();
                            dataTableEmployee.draw();
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
            dataTableEmployeeUpcoming = $('#table-upcoming-salary').DataTable({
                stateSave: true,
                processing: true,
                serverSide: true,
                filter: false,
                info: false,
                lengthChange: true,
                responsive: true,
                order: [
                    [5, "asc"]
                ],
                ajax: {
                    url: "{{ route('salaryincreasedetail.read') }}",
                    type: "GET",
                    data: function (data) {
                        data.salaryincreases_id = {{$salaryincreases->id}};
                    }
                },
                columnDefs: [{
                        orderable: false,
                        targets: [0]
                    },
                    {
                        className: "text-right",
                        targets: [3,4]
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
                    // {
                    //     render: function (data, type, row) {
                    //         // if (row.status == 0) {
                    //         return `<input type="checkbox" name="employee_id[]" value="${row.id}" class="checkcok" autocomplete="off" />
                    //                 <input type="hidden" name="current_salary[]" value="${row.current_salary}">
                    //                 <input type="hidden" name="employess[]" value="${row.id}">
                    //         `
                    //         // } else {
                    //         // return `<span class="badge badge-success"><i class="fa fa-check"></i></span>`
                    //         // }
                    //     },
                    //     targets: [5]
                    // },
                    {
                        render: function (data, type, row) {
                            return `<div class="dropdown">
                        <button class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-bars"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <li><a class="dropdown-item delete" href="#" data-id="${row.id}"><i class="fas fa-trash mr-2"></i> Delete</a></li>
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
                        data: "current_Salary", render: $.fn.dataTable.render.number( '.', ',', 0, 'Rp. ' )
                    },
                    {
                        data: "upcoming_amount", render: $.fn.dataTable.render.number( '.', ',', 0, 'Rp. ' )
                    },
                    // {
                    //     data: "id"
                    // },
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
                lengthChange: true,
                responsive: true,
                order: [
                    [1, "asc"]
                ],
                ajax: {
                    url: "{{ route('salaryincreases.reademployee') }}",
                    type: "GET",
                    data: function (data) {
                        var employee_id = $("#form_career select[name='employee_name[]']").val();
                        var department = $("#form_career select[name='department[]']").val();
                        var position = $("#form_career select[name='position[]']").val();
                        var workgroup = $("#form_career select[name='workgroup_id[]']").val();
                        var nid = $('#form_career input[name=nid]').val();
                        var allowance = $('#allowance-id-history').val();
                        data.salaryincreases_id = {{$salaryincreases->id}};
                        data.employee_id = employee_id;
                        data.departments = department;
                        data.position = position;
                        data.nid = nid;
                        data.workgroup = workgroup;
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
                            return `<input type="checkbox" name="employee_id[]" value="${row.id}" class="checkcok" autocomplete="off" />
                                    <input type="hidden" name="current_salary[]" value="${row.current_salary}">
                                    <input type="hidden" name="employess[]" value="${row.id}">
                            `
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
                        data: "current_salary", render: $.fn.dataTable.render.number( '.', ',', 0, 'Rp. ' )
                    },
                    {
                        data: "id"
                    }
                ]
            });
            $(document).on('change', '#employee_name', function () {
                dataTableEmployee.draw();
            });
            $(document).on('change', '#department', function () {
                dataTableEmployee.draw();
            });
            $(document).on('change', '#position', function () {
                dataTableEmployee.draw();
            });
            $(document).on('change', '#workgroup_id', function () {
                dataTableEmployee.draw();
            });
            $(document).on('keyup', '#nid', function () {
                dataTableEmployee.draw();
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
            // Career


            $('.add_career').on('click', function () {
                $('#form_career')[0].reset();
                $('#form_career').attr('action', "{{route('salaryincreasedetail.store')}}");
                var cokcok = $('#value').val();

                $('#amount').val(cokcok);
                $('#add_career .modal-title').html('Add Employee');
                $('#add_career').modal('show');
            });

            $('#table-employee').on('change', "input[type='checkbox']", function (e) {
                var id = this.value;
                var status = this.checked ? 1 : 0;
                // console.log(status);
            })

            $(document).on('click', '.delete', function () {
                var id = $(this).data('id');
                bootbox.confirm({
                    buttons: {
                        confirm: {
                            label: '<i class="fa fa-check"></i>',
                            className: `btn-{{config('configs.app_theme')}}`
                        },
                        cancel: {
                            label: '<i class="fa fa-undo"></i>',
                            className: 'btn-default'
                        },
                    },
                    title: 'Menghapus Data Salary Increases Employee?',
                    message: 'Data yang telah dihapus tidak dapat dikembalikan',
                    callback: function (result) {
                    if (result) {
                        var data = {
                            _token: "{{ csrf_token() }}"
                        };
                        $.ajax({
                            url: `{{url('admin/salaryincreasedetail')}}/${id}`,
                            dataType: 'json',
                            data: data,
                            type: 'DELETE',
                            beforeSend: function () {
                                $('.overlay').removeClass('hidden');
                            }
                        }).done(function (response) {
                            if (response.status) {
                                $('.overlay').addClass('hidden');
                                $.gritter.add({
                                    title: 'Success!',
                                    text: response.message,
                                    class_name: 'gritter-success',
                                    time: 1000,
                                });
                                dataTableEmployee.ajax.reload(null, false);
                                dataTableEmployeeUpcoming.ajax.reload(null, false);
                            } else {
                                $.gritter.add({
                                    title: 'Warning!',
                                    text: response.message,
                                    class_name: 'gritter-warning',
                                    time: 1000,
                                });
                            }
                        }).fail(function (response) {
                            var response = response.responseJSON;
                            $('.overlay').addClass('hidden');
                            $.gritter.add({
                                title: 'Error!',
                                text: response.message,
                                class_name: 'gritter-error',
                                time: 1000,
                            });
                        })
                    }
                }
                });
            });
        });

    </script>
    @endpush
