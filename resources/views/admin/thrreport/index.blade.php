@extends('admin.layouts.app')

@section('title',__('thrreport.thrrpt'))
@section('stylesheets')
<link href="{{ asset('adminlte/component/dataTables/css/datatables.min.css') }}" rel="stylesheet">
<link href="{{asset('adminlte/component/jquery-ui/jquery-ui.min.css')}}" rel="stylesheet">
<style type="text/css">
    .customcheckbox {
        width: 22px;
        height: 22px;
        background: url("/img/green.png") no-repeat;
        background-position-x: 0%;
        background-position-y: 0%;
        cursor: pointer;
        margin: 0 auto;
    }

    .customcheckbox.checked {
        background-position: -48px 0;
    }

    .ui-state-active {
        background: #28a745 !important;
        border-color: #28a745 !important;
    }

    .ui-menu {
        overflow: auto;
        height: 200px;
    }

    .customcheckbox input {
        cursor: pointer;
        opacity: 0;
        scale: 1.6;
        width: 22px;
        height: 22px;
        margin: 0;
    }

    .dataTables_length {
        display: block !important;
    }
</style>
@endsection

@push('breadcrump')
<li class="breadcrumb-item active">{{__('thrreport.thrrpt')}}</li>
@endpush

@section('content')
<div class="wrapper wrapper-content">
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-{{ config('configs.app_theme') }} card-outline">
                <div class="card-header">
                    <h3 class="card-title">{{__('thrreport.thrrpt')}}</h3>
                    <div class="pull-right card-tools">
                        <button form="form" type="submit" class="btn btn-sm btn-{{ config('configs.app_theme') }}"
                            title="Generate"><i class="fa fa-sync"></i></button>
                        <a href="javascript:void(0)" onclick="printmass()" class="btn btn-sm btn-info text-white"
                            title="{{__('general.print')}} Mass"><i class="fa fa-print"></i></a>
                        <a href="#" onclick="exportThr()" class="btn btn-primary btn-sm text-white" title="{{__('general.download')}} Mass"><i
                                class="fa fa-download"></i></a>
                    </div>
                </div>
                <div class="card-body">
                    <form id="form" action="{{ route('thrreport.store') }}" class="form-horizontal" method="post">
                        {{ csrf_field() }}
                        <div class="row">
                            <input type="hidden" name="user" value="{{ Auth::guard('admin')->user()->id }}">
                            <div class="col-md-4">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label" for="employee_name">{{__('employee.empname')}}</label>
                                        {{-- <input type="text" class="form-control" placeholder="Employee Name"
                                            name="employee_name" id="employee_name"> --}}
                                        <select name="employee_name[]" id="employee_name" class="form-control select2"
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
                                        <input type="text" class="form-control" placeholder="Search" name="nid"
                                            id="nid">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label" for="position">{{__('employee.position')}}</label>
                                        <select name="position[]" id="position" class="form-control select2"
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
                                        <select name="department[]" id="department" class="form-control select2"
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
                                        <select name="workgroup_id[]" id="workgroup_id" class="form-control select2"
                                            style="width: 100%" aria-hidden="true" multiple
                                            data-placeholder="{{__('employee.workcomb')}}">
                                            @foreach ($workgroups as $workgroup)
                                            <option value="{{ $workgroup->id }}">{{ $workgroup->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label" for="period">{{__('general.month')}}</label>
                                        <select name="period" class="form-control select2" multiple id="period"
                                            data-placeholder="Period">
                                            @php
                                            $month = 1;
                                            @endphp
                                            @for ($i = $month; $i <=12; $i++)
                                            <option value="{{ $i }}">
                                                {{ $i }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label" for="period">{{__('thrreport.period_thr')}}</label>
                                        <div class="form-row">
                                            <div class="col-sm-4">
                                            <select class="form-control select2" placeholder="Day" multiple name="day" id="day">
                                                    <option value=""></option>
                                                    @for ($i = 1; $i <= 31; $i++)
                                                    <option value="{{ $i }}">{{ $i }}</option>
                                                    @endfor
                                            </select>
                                        </div>
                                            <div class="col-sm-4">
                                                <select class="form-control select2" name="montly[]" multiple id="montly" data-placeholder="Month">
                                                    <option value="01" @if (date('m', time()) == "01") selected @endif>{{__('general.jan')}}</option>
                                                    <option value="02" @if (date('m', time()) == "02") selected @endif>{{__('general.feb')}}</option>
                                                    <option value="03" @if (date('m', time()) == "03") selected @endif>{{__('general.march')}}</option>
                                                    <option value="04" @if (date('m', time()) == "04") selected @endif>{{__('general.apr')}}</option>
                                                    <option value="05" @if (date('m', time()) == "05") selected @endif>{{__('general.may')}}</option>
                                                    <option value="06" @if (date('m', time()) == "06") selected @endif>{{__('general.jun')}}</option>
                                                    <option value="07" @if (date('m', time()) == "07") selected @endif>{{__('general.jul')}}</option>
                                                    <option value="08" @if (date('m', time()) == "08") selected @endif>{{__('general.aug')}}</option>
                                                    <option value="09" @if (date('m', time()) == "09") selected @endif>{{__('general.sep')}}</option>
                                                    <option value="10" @if (date('m', time()) == "10") selected @endif>{{__('general.oct')}}</option>
                                                    <option value="11" @if (date('m', time()) == "11") selected @endif>{{__('general.nov')}}</option>
                                                    <option value="12" @if (date('m', time()) == "12") selected @endif>{{__('general.dec')}}</option>
                                                </select>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="input-group">
                                                    <select name="year[]" class="form-control select2" multiple id="year"
                                                        data-placeholder="{{__('general.year')}}">
                                                        @php
                                                        $thn_skr = date('Y');
                                                        @endphp
                                                        @for ($i = $thn_skr; $i >= 1991; $i--)
                                                        <option value="{{ $i }}" @if ($i==date('Y')) selected @endif>
                                                            {{ $i }}</option>
                                                        @endfor
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label" for="status">Status</label>
                                        <select name="status" id="status" class="form-control select2"
                                            style="width: 100%" aria-hidden="true" data-placeholder="Status" multiple>
                                            <option value=""></option>
                                            <option value="-1">Draft</option>
                                            <option value="0">{{__('general.wait_approve')}}</option>
                                            <option value="1">{{__('general.approved')}}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <table class="table table-striped table-bordered datatable" id="thr-table" width="100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>{{__('employee.empname')}}</th>
                                    <th>{{__('employee.position')}}</th>
                                    <th>{{__('department.dep')}}</th>
                                    <th>{{__('workgroup.workgrp')}}</th>
                                    <th>{{__('thrreport.period_thr')}}</th>
                                    <th>{{__('general.month')}}</th>
                                    <th>THR</th>
                                    <th>Status</th>
                                    <th>{{__('general.print')}}</th>
                                    <th>
                                        <div class="customcheckbox">
                                            <input type="checkbox" class="checkall">
                                        </div>
                                    </th>
                                    <th>{{__('general.act')}}</th>
                                </tr>
                            </thead>
                        </table>
                    </form>
                </div>
                <div class="overlay d-none">
                    <i class="fa fa-2x fa-sync-alt fa-spin"></i>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="print-mass" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header no-print">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                <h4 class="modal-title">{{__('general.print')}}</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <iframe id="bodyReplace" scrolling="no" allowtransparency="true"
                        style="width: 69%; border-width: 0px; position: relative; margin: 0 auto; display: block;"
                        onload="this.style.height=(this.contentDocument.body.scrollHeight+45) + 'px';"></iframe>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- Edit Period --}}
<div class="modal fade" id="edit-periode" tabindex="-1" role="dialog" aria-hidden="true" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Change Period</h4>
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        </div>
        <div class="modal-body">
          <form id="form-periode" action="{{ route('thrreport.quickupdate') }}" class="form-horizontal no-gutters" method="post" autocomplete="off">
            {{ csrf_field() }}
            <input type="hidden" name="periode_id" id="periode_id">
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label class="control-label" for="scheme">Month Period</label>
                  <select name="month_periode" class="form-control select2" id="month_periode"
                        data-placeholder="Period">
                        @php
                        $month = 1;
                        @endphp
                        @for ($i = $month; $i <=12; $i++)
                        <option value="{{ $i }}">
                            {{ $i }}</option>
                        @endfor
                    </select>
                </div>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button form="form-periode" type="submit" class="btn btn-{{ config('configs.app_theme') }}" title="Apply"><i class="fa fa-save"></i></button>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
<script src="{{asset('adminlte/component/dataTables/js/datatables.min.js')}}"></script>
<script src="{{asset('adminlte/component/jquery-ui/jquery-ui.min.js')}}"></script>
<script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
<script type="text/javascript">
    function filter() {
        $('#filter-page').modal('show');
    }

    function printmass() {
        var ids = [];
        $('input[name^=checkthr]').each(function () {
            if (this.checked) {
                ids.push($(this).data('id'));
            }
        });
        if (ids.length <= 0) {
            $.gritter.add({
                title: 'Warning!',
                text: 'No data has been selected yet',
                class_name: 'gritter-error',
                time: 1000,
            });
            return false;
        }
        printpreview(ids);
    }

    function printpreview(ids) {
        $('.overlay').removeClass('d-none');
        $.ajax({
            url: "{{ route('thrreport.printmass') }}",
            method: 'GET',
            data: {
                id: JSON.stringify(ids)
            },
            success: function (response) {
                $('.overlay').addClass('d-none');
                dataTable.draw();
                $('.customcheckbox').removeClass('checked');
                $('.customcheckbox input').prop('checked', false);
                var iframe = document.getElementById('bodyReplace');
                iframe = iframe.contentWindow || (iframe.contentDocument.document || iframe
                    .contentDocument);
                iframe.document.open();
                iframe.document.write(response);
                iframe.document.close();
            }
        });
    }

    function exportThr() {
        $.ajax({
            url: "{{ route('thrreport.exportthr') }}",
            type: 'POST',
            dataType: 'JSON',
            data: $("#form").serialize(),
            beforeSend: function () {
                // $('.overlay').removeClass('d-none');
                waitingDialog.show('Loading...');
            }
        }).done(function (response) {
            waitingDialog.hide();
            if (response.status) {
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
            } else {
                $.gritter.add({
                    title: 'Warning!',
                    text: response.message,
                    class_name: 'gritter-warning',
                    time: 1000,
                });
            }
        }).fail(function (response) {
            waitingDialog.hide();
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

    function approvemass() {
        var isChecked = $('input[name="checkthr[]"]').is(":checked");
        if (isChecked) {
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
                title: 'Approve Salary?',
                message: 'Data that has been approved cannot be delete or change',
                callback: function (result) {
                    if (result) {
                        var data = {
                            _token: "{{ csrf_token() }}"
                        };
                        $.ajax({
                            url: `{{ route('salaryreport.approve') }}`,
                            dataType: 'json',
                            data: new FormData($('#form')[0]),
                            processData: false,
                            contentType: false,
                            method: 'post',
                            beforeSend: function () {
                                $('.overlay').removeClass('d-none');
                            }
                        }).done(function (response) {
                            if (response.status) {
                                $('.overlay').addClass('d-none');
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
                            $('.overlay').addClass('d-none');
                            $.gritter.add({
                                title: 'Error!',
                                text: response.message,
                                class_name: 'gritter-error',
                                time: 1000,
                            });
                        });
                    }
                }
            });
        } else {
            $.gritter.add({
                title: 'Warning!',
                text: 'Please check at least one data to approve',
                class_name: 'gritter-warning',
                time: 1000,
            });
        }
    }

    $(document).ready(function () {
        $('#filter-page').on('shown.bs.modal', function (e) {
            $('#search').on('click', function () {
                dataTable.draw();
                $('#filter-page').modal('hide');
            });
            $('#generate').on('click', function () {
                $('#form').submit();
            });
        });
    });

    $(function () {
        $('.select2').select2({
            allowClear: true
        });
        dataTable = $('.datatable').DataTable({
            stateSave: true,
            processing: true,
            serverSide: true,
            filter: false,
            info: false,
            lengthChange: true,
            lengthMenu: [50, 200, 500, 1000, 2000],
            pageLength: 200,
            // deferRender: true,
            // scrollX: true,
            // scrollCollapse: true,
            // scroller: true,
            responsive: true,
            // fixedColumns: {
            //   rightColumns: 2
            // },
            order: [
                [1, "asc"]
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
                url: "{{route('thrreport.read')}}",
                type: "GET",
                data: function (data) {
                    var employee_id = $("select[name='employee_name[]']").val();
                    // var department_id = $('select[name=department]').val();
                    // var position = $('select[name=position]').val();
                    // var workgroup_id = $('select[name=workgroup_id]').val();
                    var department_id = $("select[name='department[]']").val();
                    var position = $("select[name='position[]']").val();
                    var workgroup_id = $("select[name='workgroup_id[]']").val();
                    var month = $('select[name=montly]').val();
                    var year = $("select[name='year[]']").val();
                    var period = $('select[name=period]').val();
                    var status = $('select[name=status]').val();
                    var type = $('select[name=type]').val();
                    var nid = $('input[name=nid]').val();
                    data.employee_id = employee_id;
                    data.department_id = department_id;
                    data.position = position;
                    data.workgroup_id = workgroup_id;
                    data.month = month;
                    data.year = year;
                    data.status = status;
                    data.type = type;
                    data.nid = nid;
                    data.period = period;
                }
            },
            columnDefs: [{
                    orderable: false,
                    targets: [0, 9, 10, 11]
                },
                {
                    className: "text-right",
                    targets: [7]
                },
                {
                    className: "text-center",
                    targets: [0, 6,9, 10, 11]
                },
                {
                    render: function (data, type, row) {
                        return `<a class="text-blue" href="{{url('admin/thrreport/${row.id}')}}">${row.employee_name}</a><br>${row.nik}`;
                    },
                    targets: [1]
                },
                { render: function ( data, type, row ) {
                    if((row.year && row.month) != null){
                    const d = new Date(row.year, parseInt(row.month) -1, 1);
                    const ye = new Intl.DateTimeFormat('en', { year: 'numeric' }).format(d);
                    const mo = new Intl.DateTimeFormat('en', { month: 'long' }).format(d);
                    return `${mo}-${ye}`
                    }else{
                        return "-";
                    }
                },targets: [5] },
                { render: function ( data, type, row ) {
                    return `<span class="text-blue">${row.period}</span>`
                },targets: [6]
                },
                {
                    render: function (data, type, row) {
                        if (row.amount) {
                            return `Rp. ${row.amount}`;
                        }
                        return `Rp. 0`;
                    },
                    targets: [7]
                },
                {
                    render: function (data, type, row) {
                        if (row.status == -1) {
                            return `<span class="badge badge-secondary">Draft</span>`;
                        } else if (row.status == 0) {
                            return `<span class="badge badge-warning">{{__('general.wait_approve')}}</span>`
                        } else if (row.status == 1) {
                            return `<span class="badge badge-success">Approved</span>`
                        } else {
                            `<span class="badge badge-danger">Rejected</span>`
                        }
                    },
                    targets: [8]
                },
                {
                    render: function (data, type, row) {
                        if (row.print_status == 1) {
                            return `<span class="badge badge-success"><i class="fa fa-check"></i></span>`
                        } else {
                            return `<span class="badge badge-danger"><i class="fa fa-times"></i></span>`
                        }
                    },
                    targets: [9]
                },
                {
                    render: function (data, type, row) {
                        return `<label class="customcheckbox">
                    <input data-id="${data}" type="checkbox" name="checkthr[]" value="${row.id}"><span class="checkmark"></span>
                    </label>`
                    },
                    targets: [10]
                },
                {
                    render: function (data, type, row) {
                        if (row.status == 1) {
                            return `<div class="dropdown">
                      <button class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-bars"></i>
                      </button>
                      <ul class="dropdown-menu dropdown-menu-right">
                        <li><a class="dropdown-item" href="{{url('admin/thrreport/${row.id}')}}"><i class="fas fa-info mr-3"></i> Detail</a></li>
                      </ul>
                    </div>`
                        } else {
                            return `<div class="dropdown">
                      <button class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-bars"></i>
                      </button>
                      <ul class="dropdown-menu dropdown-menu-right">
                        <li><a class="dropdown-item" href="{{url('admin/thrreport/${row.id}')}}"><i class="fas fa-info mr-3"></i> Detail</a></li>
                        <li><a class="dropdown-item delete" href="#" data-id="${row.id}"><i class="fas fa-trash mr-2"></i> Delete</a></li>
                      </ul>
                    </div>`
                        }
                    },
                    targets: [11]
                }
            ],
            columns: [{
                    data: "no"
                },
                {
                    data: "employee_name"
                },
                {
                    data: "title_name"
                },
                {
                    data: "department_name"
                },
                {
                    data: "workgroup_name"
                },
                {
                    data: "year"
                },
                {
                    data: "period", className:"periode align-middle text-center"
                },
                {
                    data: "amount"
                },
                {
                    data: "status"
                },
                {
                    data: "print_status"
                },
                {
                    data: "id"
                },
                {
                    data: "id"
                },
            ]
        });
    });
    $(document).ready(function(){
        var employees = [
				@foreach($employees as $nik)
                	"{!!$nik->nid!!}",
            	@endforeach
			];
			$( "input[name=nid]" ).autocomplete({
			source: employees,
			minLength:0,
			appendTo: '#employee-container',
			select: function(event, response) {
				if(event.preventDefault(), 0 !== response.item.id){
					$(this).val(response.item.value);
					dataTable.draw();
				}
			}
			}).focus(function () {
				$(this).autocomplete("search");
			});
			$("input[name=nid]").keydown(function(event){
				if(event.keyCode == 13) {
					event.preventDefault();
					$('input[name=nid]').autocomplete('close');
					return false;
				}
			});
    $(document).on('change', '#employee_name', function () {
        dataTable.draw();
    });
    $(document).on('keyup', '#nid', function () {
        dataTable.draw();
    });
    });
    $(document).on('click', '.customcheckbox input', function () {
        if ($(this).is(':checked')) {
            $(this).parent().addClass('checked');
        } else {
            $(this).parent().removeClass('checked');
        }
    });
    $(document).on('change', '.checkall', function () {
        if (this.checked) {
            $('input[name^=checkthr]').prop('checked', true);
            $('input[name^=checkthr]').parent().addClass('checked');
        } else {
            $('input[name^=checkthr]').prop('checked', false);
            $('input[name^=checkthr]').parent().removeClass('checked');
        }
    });
    $(document).on('change', '#department', function () {
        dataTable.draw();
    });
    $(document).on('change', '#position', function () {
        dataTable.draw();
    });
    $(document).on('change', '#workgroup_id', function () {
        dataTable.draw();
    });
    $(document).on('change', '#montly', function () {
        dataTable.draw();
    });
    $(document).on('change', '#year', function () {
        dataTable.draw();
    });
    $(document).on('change', '#type', function () {
        dataTable.draw();
    });
    $(document).on('change', '#status', function () {
        dataTable.draw();
    });
    $('.datatable').on('click', '.periode', function() {
      var data = dataTable.row(this).data();
      if (data) {
        $('#edit-periode').modal('show');
        $('#form-periode input[name=periode_id]').attr('value', data.id);
        $(document).on("change", "#month_periode", function () {
          if (!$.isEmptyObject($('#form-periode').validate().submitted)) {
            $('#form-periode').validate().form();
          }
        });
      }
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
            title: 'Delete THR report?',
            message: 'Data that has been deleted cannot be recovered',
            callback: function (result) {
                if (result) {
                    var data = {
                        _token: "{{ csrf_token() }}"
                    };
                    $.ajax({
                        url: `{{url('admin/thrreport')}}/${id}`,
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
                    });
                }
            }
        });
    });
    $('#form').validate({
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
                    dataTable.draw();
                    $.gritter.add({
                        title: 'Success!',
                        text: response.message,
                        class_name: 'gritter-success',
                        time: 1000,
                    });
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
            });
        }
    });

</script>
@endpush