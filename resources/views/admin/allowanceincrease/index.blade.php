@extends('admin.layouts.app')

@section('title', 'Allowance Increase')
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/daterangepicker/daterangepicker.css')}}" rel="stylesheet">

@endsection
@push('breadcrump')
<li class="breadcrumb-item">Allowance Increase</li>
@endpush
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card card-{{ config('configs.app_theme')}} card-outline">
            <div class="card-header">
                <h3 class="card-title">Allowance Increase List</h3>
                <!-- tools box -->
                <div class="pull-right card-tools">
                    <a href="{{route('allowanceincrease.create')}}" class="btn btn-{{ config('configs.app_theme')}} btn-sm text-white"
                        data-toggle="tooltip" title="Tambah">
                        <i class="fa fa-plus"></i>
                    </a>
                </div>
                <!-- /. tools -->
            </div>
            <div class="card-body">
                <div class="overlay-wrapper">
                    <form id="form" class="form-horizontal" action="" method="post" autocomplete="off">
                    {{ csrf_field() }}
                    {{-- <input type="hidden" name="user_id" value="" /> --}}
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
                                    <label class="control-label" for="nid">Description</label>
                                    <input type="text" class="form-control" placeholder="Description" name="note"
                                        id="note">
                                </div>
                            </div>
                            </div>
                            <div class="col-md-4">
                                <label class="control-label" for="period">Period</label>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <select class="form-control select2" name="month" id="month">
                                            <option value="01" @if (date('m', time())=="01" ) selected @endif>January</option>
                                            <option value="02" @if (date('m', time())=="02" ) selected @endif>February</option>
                                            <option value="03" @if (date('m', time())=="03" ) selected @endif>March</option>
                                            <option value="04" @if (date('m', time())=="04" ) selected @endif>April</option>
                                            <option value="05" @if (date('m', time())=="05" ) selected @endif>May</option>
                                            <option value="06" @if (date('m', time())=="06" ) selected @endif>June</option>
                                            <option value="07" @if (date('m', time())=="07" ) selected @endif>July</option>
                                            <option value="08" @if (date('m', time())=="08" ) selected @endif>August</option>
                                            <option value="09" @if (date('m', time())=="09" ) selected @endif>September</option>
                                            <option value="10" @if (date('m', time())=="10" ) selected @endif>October</option>
                                            <option value="11" @if (date('m', time())=="11" ) selected @endif>November</option>
                                            <option value="12" @if (date('m', time())=="12" ) selected @endif>December</option>
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
                    </form>
                    <table class="table table-bordered table-striped" id="table-salary-increases">
                        <thead>
                            <tr>
                                <th style="text-align:center" width="10">#</th>
                                {{-- <th width="150">Ref No</th> --}}
                                <th width="100">Period</th>
                                <th width="250">Description</th>
                                <th width="100">Total Employee</th>
                                <th width="100">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div class="overlay d-none">
                    <i class="fa fa-2x fa-sync-alt fa-spin"></i>
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
            ajax: {
                url: "{{route('allowanceincrease.read')}}",
                type: "GET",
                data: function (data) {
                    // var reff_no = $('input[name=reff]').val();
                    var note = $('input[name=note]').val();
                    data.month = $('#month').val();
				    data.year = $('select[name=year]').val();
                    // data.reff_no = reff_no;
                    data.note = note;
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
                { render: function ( data, type, row ) {
                    if((row.year && row.month) != null){
                    const d = new Date(row.year, parseInt(row.month) -1, 1);
                    const ye = new Intl.DateTimeFormat('en', { year: 'numeric' }).format(d);
                    const mo = new Intl.DateTimeFormat('en', { month: 'long' }).format(d);
                    return `${mo}-${ye}`
                    }else{
                        return "-";
                    }
                },
                targets: [1] },
                {
                    render: function (data, type, row) {
                        return `<div class="dropdown">
                    <button class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-bars"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li><a class="dropdown-item" href="{{url('admin/allowanceincrease')}}/${row.id}/edit"><i class="fas fa-pencil-alt mr-2"></i> Edit</a></li>
                        <li><a class="dropdown-item" href="{{url('admin/allowanceincrease')}}/${row.id}"><i class="fas fa-user mr-2"></i> Add Massal</a></li>
                    </ul></div>`
                    },
                    targets: [4]
                }
            ],
            columns: [{
                    data: "no"
                },
                {
                    data: "year"
                },
                {
                    data: "note"
                },
                {
                    data: "total_employee"
                },
                {
                    data: "id"
                },
            ]
        });
        
            $(document).on('keyup', '#reff', function () {
                dataTable.draw();
            });
            $(document).on('keyup', '#description', function () {
                dataTable.draw();
            });
            $(document).on('change', '#year', function() {
                dataTable.draw();
            });
             $(document).on('change', '#month', function() {
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
