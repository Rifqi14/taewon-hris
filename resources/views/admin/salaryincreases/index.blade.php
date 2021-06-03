@extends('admin.layouts.app')

@section('title',__('salarymass.slinc'))
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/daterangepicker/daterangepicker.css')}}" rel="stylesheet">

@endsection
@push('breadcrump')
<li class="breadcrumb-item">{{__('salarymass.slinc')}}</li>
@endpush
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card card-{{config('configs.app_theme')}} card-outline card-outline-tabs">
            <div class="card-header p-0 border-bottom-0">
                <ul class="nav nav-tabs">
                    <li class="nav-item"><a class="nav-link active" href="#salaryincreases" data-toggle="tab">List
                            {{__('salarymass.slinc')}}</a></li>
                    <li class="nav-item"><a class="nav-link" href="#employee" data-toggle="tab">List Employee</a></li>
                </ul>
            </div>
            <div class="tab-content">
                <div class="tab-pane active" id="salaryincreases">
                    <div class="card-header">
                        <div class="pull-right card-tools">
                            <a href="{{route('salaryincreases.create')}}"
                                class="btn btn-{{ config('configs.app_theme') }} btn-sm text-white"
                                data-toggle="tooltip" title="{{__('general.crt')}}">
                                <i class="fa fa-plus"></i>
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="overlay-wrapper">
                            <form id="form" class="form-horizontal" action="" method="post" autocomplete="off">
                            {{ csrf_field() }}
                            <input type="hidden" name="user_id" value="" />
                            <div class="row">
                                {{-- <div class="col-md-4">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label" for="nid">Reff No</label>
                                            <input type="text" class="form-control" placeholder="Reff No" name="reff"
                                                id="reff">
                                        </div>
                                    </div>
                                </div> --}}
                                <div class="col-md-4">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label" for="nid">{{__('general.desc')}}</label>
                                            <input type="text" class="form-control" placeholder="{{__('general.desc')}}" name="description"
                                                id="description">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-row col-md-4">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label" for="date_from">{{__('general.from')}}</label>
                                            <div class="controls">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="far fa-calendar-alt"></i>
                                                </span>
                                                </div>
                                                <input type="text" name="date_from" id="date_from" class="form-control datepicker filter" placeholder="Date">
                                            </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                      <div class="form-group">
                                        <label class="control-label" for="date_to">{{__('salarymass.to')}}</label>
                                        <div class="controls">
                                          <div class="input-group">
                                            <div class="input-group-prepend">
                                              <span class="input-group-text">
                                                <i class="far fa-calendar-alt"></i>
                                              </span>
                                            </div>
                                            <input type="text" name="date_to" id="date_to" class="form-control datepicker filter" placeholder="Date">
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                </div>
                            </div>
                            </form>
                            <table class="table table-bordered table-striped" id="table-salary-increases">
                                <thead>
                                    <tr>
                                        <th style="text-align:center" width="10">#</th>
                                        {{-- <th width="150">Ref No</th> --}}
                                        <th width="100">{{__('general.date')}}</th>
                                        <th width="250">{{__('general.desc')}}</th>
                                        <th width="100">{{__('salarymass.totalemp')}}</th>
                                        <th width="100">{{__('general.act')}}</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                        <div class="overlay d-none">
                            <i class="fa fa-2x fa-sync-alt fa-spin"></i>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="employee">
                    <div class="card-body">
                        <div class="row">
                            <input type="hidden" name="user" value="{{ Auth::guard('admin')->user()->id }}">
                            <div class="col-md-4">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label" for="employee_name">{{__('employee.empname')}}</label>
                                        {{-- <input type="text" class="form-control" placeholder="Employee Name"
                                            name="employee_name" id="employee_name"> --}}
                                        <select name="employee_name[]" id="employee_name" class="form-control select2 sorting"
                                            style="width: 100%" aria-hidden="true" multiple
                                            data-placeholder="{{__('employee.empname')}}">
                                            <option value=""></option>
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
                                        <input type="text" class="form-control sorting" placeholder="Search" name="nid"
                                            id="nid">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label" for="position">{{__('employee.position')}}</label>
                                        <select name="position[]" id="position" class="form-control select2 sorting"
                                            style="width: 100%" aria-hidden="true" multiple data-placeholder="{{__('employee.position')}}">
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
                                        <label class="control-label" for="department">{{__('department.dep')}}</label>
                                        <select name="department[]" id="department" class="form-control select2 sorting"
                                            style="width: 100%" aria-hidden="true" multiple
                                            data-placeholder="{{__('department.dep')}}">
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
                                        <label class="control-label" for="workgroup">{{__('employee.workcomb')}}</label>
                                        <select name="workgroup_id[]" id="workgroup_id" class="form-control select2 sorting"
                                            style="width: 100%" aria-hidden="true" multiple
                                            data-placeholder="{{__('employee.workcomb')}}">
                                            @foreach ($workgroups as $workgroup)
                                            <option value="{{ $workgroup->id }}">{{ $workgroup->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <table class="table table-bordered table-striped" id="table-list-employee" style="width:100%;">
                            <thead>
                                <tr>
                                    <th style="text-align:center" width="10">#</th>
                                    <th width="200">{{__('salarymass.reff_no')}}</th>
                                    <th width="250">{{__('employee.employ')}}</th>
                                    <th width="250">{{__('employee.position')}}</th>
                                    <th width="100">{{__('salarymass.incramt')}}</th>
                                    {{-- <th width="100">Action</th> --}}
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection

@push('scripts')

<script src="{{asset('adminlte/component/dataTables/js/datatables.min.js')}}"></script>
<script src="{{asset('adminlte/component/daterangepicker/moment.min.js')}}"></script>
<script src="{{asset('adminlte/component/daterangepicker/daterangepicker.js')}}"></script>

<script>
    $('.select2').select2();
    $('#date_from').daterangepicker({
      autoUpdateInput: false,
      singleDatePicker: true,
      timePicker: false,
      timePickerIncrement: 30,
      locale: {
      format: 'DD/MM/YYYY'
      }
    }, function(chosen_date) {
      $('#date_from').val(chosen_date.format('DD/MM/YYYY'));
      dataTable.draw();
    });
    $('#date_to').daterangepicker({
      singleDatePicker: true,
      timePicker: false,
      timePickerIncrement: 30,
      locale: {
      format: 'DD/MM/YYYY'
      }
    }, function(chosen_date) {
      $('#date_to').val(chosen_date.format('DD/MM/YYYY'));
      dataTable.draw();
    });
    $(function () {
        dataTable = $('#table-salary-increases').DataTable({
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
            language: {
                lengthMenu: `{{ __('general.showent') }}`,
                processing: `{{ __('general.process') }}`,
                paginate: {
                    previous: `{{ __('general.prev') }}`,
                    next: `{{ __('general.next') }}`,
                }
            },
            ajax: {
                url: "{{route('salaryincreases.read')}}",
                type: "GET",
                data: function (data) {
                    // var reff_no = $('input[name=reff]').val();
                    var description = $('input[name=description]').val();
                    data.date_from = $('input[name=date_from]').val();
                    data.date_to   = $('input[name=date_to]').val();
                    // data.reff_no = reff_no;
                    data.description = description;
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
                    targets: [0, 1, 4]
                },
                {
                    render: function (data, type, row) {
                        return `<div class="dropdown">
                    <button class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-bars"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li><a class="dropdown-item" href="{{url('admin/salaryincreases')}}/${row.id}/edit"><i class="fas fa-pencil-alt mr-2"></i> {{__('general.edt')}}</a></li>
                        <li><a class="dropdown-item" href="{{url('admin/salaryincreases')}}/${row.id}/show"><i class="fas fa-user mr-2"></i> Add Mass</a></li>
                    </ul></div>`
                    },
                    targets: [4]
                }
            ],
            columns: [{
                    data: "no"
                },
                {
                    data: "date"
                },
                {
                    data: "notes"
                },
                {
                    data: "total_employee"
                },
                {
                    data: "id"
                },
            ]
        });
        dataTableListEmployee = $('#table-list-employee').DataTable({
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
                language: {
                    lengthMenu: `{{ __('general.showent') }}`,
                    processing: `{{ __('general.process') }}`,
                    paginate: {
                        previous: `{{ __('general.prev') }}`,
                        next: `{{ __('general.next') }}`,
                    }
                },
                ajax: {
                    url: "{{ route('salaryincreasedetail.read') }}",
                    type: "GET",
                    data: function (data) {
                        var employee_id = $("select[name='employee_name[]']").val();
                        var department = $("select[name='department[]']").val();
                        var position = $("select[name='position[]']").val();
                        var workgroup = $("select[name='workgroup_id[]']").val();
                        var nid = $('input[name=nid]').val();
                        data.employee_id = employee_id;
                        data.departments = department;
                        data.position = position;
                        data.nid = nid;
                        data.workgroup = workgroup;
                    }
                },
                columnDefs: [{
                        orderable: false,
                        targets: [0]
                    },
                    {
                        className: "text-right",
                        targets: [4]
                    },
                    {
                        className: "text-center",
                        targets: [0]
                    },
                    {
                        render: function (data, type, row) {
                            return `${row.reff_no} </br> <small>${row.date}</small>`
                        },
                        targets: [1]
                    },
                    {
                        render: function (data, type, row) {
                            return `${row.employee_name} </br> <small>${row.nid}</small>`
                        },
                        targets: [2]
                    },
                    {
                        render: function (data, type, row) {
                            return `${row.department} </br> <small>${row.position}</small>`
                        },
                        targets: [3]
                    },
                    { 
                        render: function ( data, type, row ) {
                            if(row.type == 'Nominal'){
                                return 'Rp. ' + row.increases_amount
                            }else if(row.type == 'Percentage'){
                                return row.increases_amount + '%'
                            }else{
                                return row.increases_amount
                            }
                        },
                    targets: [4] },

                ],
                columns: [{
                        data: "no"
                    },
                    {
                        data: "reff_no"
                    },
                    {
                        data: "employee_name"
                    },
                    {
                        data: "department"
                    },
                    {
                        data: "increases_amount"
                    }
                ]
            });
            $(document).on('keyup', '#reff', function () {
                dataTable.draw();
            });
            $(document).on('keyup', '#description', function () {
                dataTable.draw();
            });
            $(document).on('change keyup keydown keypress focus', '.filter', function() {
                dataTable.draw();
            });
            $(document).on('change keyup keydown keypress focus', '.sorting', function() {
                dataTableListEmployee.draw();
            });
        $(document).on('click', '.delete', function () {
            var id = $(this).data('id');
            bootbox.confirm({
                buttons: {
                    confirm: {
                        label: '<i class="fa fa-check"></i>',
                        className: `btn-{{ config('configs.app_theme') }}`
                    },
                    cancel: {
                        label: '<i class="fa fa-undo"></i>',
                        className: 'btn-default'
                    },
                },
                title: 'Menghapus Salary Increases?',
                message: 'Data yang telah dihapus tidak dapat dikembalikan',
                callback: function (result) {
                    if (result) {
                        var data = {
                            _token: "{{ csrf_token() }}"
                        };
                        $.ajax({
                            url: `{{url('admin/salaryincreases')}}/${id}`,
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
                                dataTable.ajax.reload(null, false);
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
        })
    })

</script>
@endpush
