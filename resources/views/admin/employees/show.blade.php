@extends('admin.layouts.app')

@section('title',__('employee.employ'))
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">

@endsection
@push('breadcrump')
    <li class="breadcrumb-item"><a href="{{route('employees.index')}}">{{__('employee.employ')}}</a></li>
    <li class="breadcrumb-item active">{{__('general.dtl')}}</li>
@endpush
@section('content')
<div class="row">
    <div class="col-lg-4">
        <div class="card card-{{config('configs.app_theme')}} card-outline">
            <div class="card-header">
                <h3 class="card-title">{{__('general.dtl')}} {{__('employee.employ')}}</h3>
                <div class="pull-right card-tools">
                    <a class="btn btn-{{ config('configs.app_theme') }} btn-sm text-white" data-toggle="tooltip" href="{{url('admin/employees')}}/{{ $employee->id}}/edit"><i class="fas fa-pencil-alt"></i></a>
                </div>
            </div>
            <div class="card-body card-profile text-center">
                <img class="profile-user-img img-responsive img-circle" src="{{asset($employee->photo)}}" alt="User profile picture">
                <h3 class="profile-username">{{$employee->name}}</h3>
                <p class="text-muted text-center">{{$employee->nid}}</p>
                <ul class="list-group list-group-unbordered">
                    <li class="list-group-item d-flex">
                        <b class="mr-auto">{{__('employee.gender')}}</b> <span class="pull-right">{{$employee->gender}}</span>
                    </li>
                    <li class="list-group-item d-flex">
                        <b class="mr-auto">Email</b> <span class="pull-right">{{$employee->email}}</span>
                    </li>
                    <li class="list-group-item d-flex">
                        <b class="mr-auto">{{__('employee.nophone')}}</b> <span class="pull-right">{{$employee->phone}}</span>
                    </li>
                    <li class="list-group-item d-flex">
                        <b class="mr-auto">{{__('employee.birthday')}}</b> <span class="pull-right">{{$employee->birth_date}}</span>
                    </li>
                    <li class="list-group-item d-flex">
                        <b class="mr-auto">{{__('employee.end_contract')}}</b> <span class="pull-right">{{$employee->resign_date}}</span>
                    </li>
                </ul>
            </div>
            <div class="overlay d-none">
                <i class="fa fa-refresh fa-spin"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card card-{{config('configs.app_theme')}} card-outline card-outline-tabs">
            <div class="card-header p-0 border-bottom-0">
                <ul class="nav nav-tabs">
                    <li class="nav-item"><a class="nav-link active" href="#salary" data-toggle="tab">{{__('general.history')}} {{__('employee.salary')}}</a></li>
                    <li class="nav-item"><a class="nav-link" href="#leave" data-toggle="tab">{{__('general.history')}} {{__('employee.leave')}}</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contract" data-toggle="tab">{{__('general.history')}} {{__('employee.contract')}}</a></li>
                    <li class="nav-item"><a class="nav-link" href="#attendance" data-toggle="tab">{{__('general.history')}} {{__('attendancelog.attenlog')}}</a></li>
                </ul>
            </div>
            <div class="tab-content">
                <div class="tab-pane active" id="salary">
                    <div class="card-body">
                        <div class="overlay-wrapper">
                            <form id="form"  class="form-horizontal"  action="" method="post" autocomplete="off">
                                {{ csrf_field() }}
                                <input type="hidden" name="user_id" value=""/>
                            </form>
                            <table class="table table-bordered table-striped" id="table-salary">
                                <thead>
                                    <tr>
                                        <th style="text-align:center" width="10">#</th>
                                        <th width="200">{{__('employee.amount')}}</th>
                                        <th width="150">{{__('general.desc')}}</th>
                                        <th width="150">{{__('general.month')}}</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                        <div class="overlay d-none">
                            <i class="fa fa-2x fa-sync-alt fa-spin"></i>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="leave">
                    <div class="card-body">
                        <table class="table table-bordered table-striped" style="width:100%" id="table-leave">
                            <thead>
                                <tr>
                                    <th style="text-align:center" width="10">#</th>
                                    <th width="150">{{__('employee.range_date')}}</th>
                                    <th width="100">{{__('employee.duration')}}</th>
                                    <th width="150">{{__('leavesetting.leavename')}}</th>
                                    <th width="150">Status</th>
                                    <th width="100">{{__('general.act')}}</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <div class="tab-pane" id="contract">
                    <div class="card-body">
                        <table class="table table-bordered table-striped" style="width:100%" id="table-contract">
                            <thead>
                                <tr>
                                    <th style="text-align:center" width="10">No</th>
                                    <th width="100">{{__('employee.contract')}}</th>
                                    <th width="100">{{__('employee.period')}}</th>
                                    <th width="200">{{__('general.desc')}}</th>
                                    <th width="50">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane" id="attendance">
                    <div class="card-body">
                        <div class="form-group row">
                            <label for="period" class="col-sm-2 col-md-1 form-label">{{__('employee.period')}}</label>
                            <div class="col-sm-5 col-md-2">
                                <select class="form-control select2" name="month" id="month">
                                    <option value="01" @if (date('m', time())=="01" ) selected @endif>{{__('general.jan')}}</option>
                                    <option value="02" @if (date('m', time())=="02" ) selected @endif>{{__('general.feb')}}</option>
                                    <option value="03" @if (date('m', time())=="03" ) selected @endif>{{__('general.march')}}</option>
                                    <option value="04" @if (date('m', time())=="04" ) selected @endif>{{__('general.apr')}}</option>
                                    <option value="05" @if (date('m', time())=="05" ) selected @endif>{{__('general.may')}}</option>
                                    <option value="06" @if (date('m', time())=="06" ) selected @endif>{{__('general.jun')}}</option>
                                    <option value="07" @if (date('m', time())=="07" ) selected @endif>{{__('general.jul')}}</option>
                                    <option value="08" @if (date('m', time())=="08" ) selected @endif>{{__('general.aug')}}</option>
                                    <option value="09" @if (date('m', time())=="09" ) selected @endif>{{__('general.sep')}}</option>
                                    <option value="10" @if (date('m', time())=="10" ) selected @endif>{{__('general.oct')}}</option>
                                    <option value="11" @if (date('m', time())=="11" ) selected @endif>{{__('general.nov')}}</option>
                                    <option value="12" @if (date('m', time())=="12" ) selected @endif>{{__('general.dec')}}</option>
                                </select>
                            </div>
                            <div class="col-sm-5 col-md-2">
                                @php
                                $thn_skr = date('Y');
                                @endphp
                                <select class="form-control select2" name="year" id="year">
                                    @for ($i = $thn_skr; $i >= 1991; $i--)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <table class="table table-striped table-bordered datatable" style="width: 100%">
                            <thead>
                                <tr>
                                    <th width="10">No</th>
                                    <th width="50">{{__('general.date')}}</th>
                                    <th width="50">{{__('employee.workshift')}}</th>
                                    <th width="10">{{__('employee.check_in')}}</th>
                                    <th width="10">{{__('employee.check_out')}}</th>
                                    <th width="10">{{__('employee.summary')}}</th>
                                    <th width="50">Status</th>
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
<script type="text/javascript">
  function filter(){
        $('#add-filter').modal('show');
    }
    function formatTime(date) {
    var now = new Date(), year = now.getFullYear();
    var d = new Date(year + ' ' + date),
            minute = '' + d.getMinutes(),
            hour = '' + d.getHours();

    if (minute.length < 2)
        minute = '0' + minute;
    if (hour.length < 2)
        hour = '0' + hour;

    return [hour, minute].join(':');
    }
    function dayName(date) {
    var weekday = ["Sun","Mon","Tue","Wed","Thu","Fri","Sat"];
    var date = new Date(date);

    return weekday[date.getDay()];
  }
  $(function() {
    dataTable = $('.datatable').DataTable({
      stateSave:true,
      processing: true,
      serverSide: true,
      filter:false,
      info:false,
      lengthChange:false,
      responsive: true,
      order: [[ 1, "asc" ]],
      language: {
            lengthMenu: `{{ __('general.showent') }}`,
            processing: `{{ __('general.process') }}`,
            paginate: {
                previous: `{{ __('general.prev') }}`,
                next: `{{ __('general.next') }}`,
            }
        },
      lengthMenu: [ 100, 250, 500, 1000, 2000 ],
      pageLength: 1000,
      ajax: {
          url: "{{route('employees.readattendance')}}",
          type: "GET",
          data:function(data){
            var employee_id = $('input[name=name]').val();
            data.employee_id = {{$employee->id}};
			data.month = $('#month').val();
			data.year = $('#year').val(); 
          }
      },
      columnDefs:[
          {
              orderable: false,targets:[0]
          },
          { className: "text-center", targets: [0,1,2,6] },
          { render: function ( data, type, row ) {
            var date = new Date(row.attendance_date);
            return `${row.attendance_date} <br> <span class="text-bold ${row.day == 'Off' ? 'text-red' : ''}">${dayName(row.attendance_date)}</span>`;
          },targets: [1]
          },
          { render: function ( data, type, row ) {
            return `<span class="text-blue">${row.description ? row.description : '-'}</span>`;
          },targets: [2]
          },
          { render: function ( data, type, row ) {
            if (row.attendance_in) {
              if (row.attendance_in < row.start_time) {
                return `<span class="text-blue">${row.attendance_in}</span><br><span class="text-bold">${formatTime(row.start_time)}</span><br><span class="text-success text-bold">- ${row.diff_in}</span>`
              } else {
                return `<span class="text-blue">${row.attendance_in}</span><br><span class="text-bold">${formatTime(row.start_time)}</span><br><span class="text-danger text-bold">+ ${row.diff_in}</span>`
              }
            } else {
              return '<span class="text-red text-bold">?</span>';
            }
          },targets: [3]
          },
          { render: function ( data, type, row ) {
            if (row.attendance_out) {
              if (row.attendance_out > row.finish_time) {
                return `<span class="text-blue">${row.attendance_out}</span><br><span class="text-bold">${formatTime(row.finish_time)}</span><br><span class="text-danger text-bold">+ ${row.diff_out}</span>`
              } else {
                return `<span class="text-blue">${row.attendance_out}</span><br><span class="text-bold">${formatTime(row.finish_time)}</span><br><span class="text-success text-bold">- ${row.diff_out}</span>`
              }
            } else {
              return '<span class="text-red text-bold">?</span>';
            }
          },targets: [4]
          },
          { render: function ( data, type, row ) {
              return `WT: ${row.adj_working_time} Hours<br>OT: ${row.adj_over_time} Hours`
          },targets: [5]
          },
          { render: function ( data, type, row ) {
            if (row.status == 0) {
              return '<span class="badge badge-warning">{{__('general.wait_approve')}}</span>'
            } else {
              return '<span class="badge badge-success">{{__('general.approved')}}</span>'
            }
          },targets: [6]
          },
      ],
      columns: [
          { data: "no", className: "align-middle text-center" },
          { data: "attendance_date", className: "align-middle text-center" },
          { data: "description", className: "shift align-middle text-center" },
          { data: "attendance_in", className: "time_in align-middle text-center" },
          { data: "attendance_out", className: "time_out align-middle text-center"},
          { data: "adj_working_time", className: "worktime align-middle text-center" },
          { data: "status", className: "align-middle text-center" }
      ]
    });
    
    $(document).on('change', '#month', function() {
        dataTable.draw();
    });
    $(document).on('change', '#year', function() {
        dataTable.draw();
    });

    dataTableContract = $('#table-contract').DataTable( {
		stateSave:true,
		processing: true,
		serverSide: true,
		filter:false,
		info:false,
		lengthChange:true,
		responsive: true,
		order: [[ 4, "asc" ]],
        language: {
            lengthMenu: `{{ __('general.showent') }}`,
            processing: `{{ __('general.process') }}`,
            paginate: {
                previous: `{{ __('general.prev') }}`,
                next: `{{ __('general.next') }}`,
            }
        },
		ajax: {
			url: "{{route('employeecontract.read')}}",
			type: "GET",
			data:function(data){
				var name = $('#form-search').find('input[name=name]').val();
				data.name = name;
				data.employee_id = {{$employee->id}};
			}
		},
		columnDefs:[
			{
				orderable: false,targets:[0]
			},
			{ className: "text-right", targets: [0] },
			{ className: "text-center", targets: [4] },
			{
			render: function (data, type, row) {
				return `${row.start_date}-${row.end_date}`
			},
			targets: [2]
			},
			{
				render: function (data, type, row) {
					if (row.status == 'Active') {
						return `<span class="badge badge-success">{{__('general.actv')}}</span>`
					} else if(row.status == 'Non Active')
					{
						return `<span class="badge badge-info">{{__('general.noactv')}}</span>`
					}else{
						return `<span class="badge badge-danger">{{__('general.expired')}}</span>`
					}
				},
				targets: [4]
			},
		],
		columns: [
			{ data: "no" },
			{ data: "code" },
			{ data: "start_date" },
			{ data: "description"},
			{ data: "status" },
		]
	});

    dataTableSalary = $('#table-salary').DataTable( {
		stateSave:true,
		processing: true,
		serverSide: true,
		filter:false,
		info:false,
		lengthChange:false,
		responsive: true,
		order: [[ 1, "desc" ]],
        language: {
            lengthMenu: `{{ __('general.showent') }}`,
            processing: `{{ __('general.process') }}`,
            paginate: {
                previous: `{{ __('general.prev') }}`,
                next: `{{ __('general.next') }}`,
            }
        },
		ajax: {
			url: "{{ route('salaryemployee.read') }}",
			type: "GET",
			data:function(data){
				var name = $('#form-search').find('input[name=name]').val();
				data.name = name;
				data.employee_id = {{$employee->id}};
			}
		},
		columnDefs:[
			{
				orderable: false,targets:[0]
			},
			{ className: "text-right", targets: [0] },
			{ className: "text-center", targets: [0] }
		],
		columns: [
			{ data: "no" },
			{ data: "amount", render: $.fn.dataTable.render.number( '.', ',', 0, 'Rp. ' )},
			{ data: "description" },
			{ data: "updated_at" }
		]
	});

    dataTableDocument = $('#table-leave').DataTable( {
		stateSave:true,
		processing: true,
		serverSide: true,
		filter:false,
		info:false,
		lengthChange:true,
		responsive: true,
		order: [[ 1, "asc" ]],
        language: {
            lengthMenu: `{{ __('general.showent') }}`,
            processing: `{{ __('general.process') }}`,
            paginate: {
                previous: `{{ __('general.prev') }}`,
                next: `{{ __('general.next') }}`,
            }
        },
		ajax: {
			url: "{{ route('employees.showleave') }}",
			type: "GET",
			data:function(data){
				data.employee_id = {{$employee->id}};
			}
		},
		columnDefs:[
			{
				orderable: false,targets:[0]
			},
			{ className: "text-right", targets: [2] },
			{ className: "text-center", targets: [5] },
			{ render: function(data, type, row) {
				return `${row.start_date} - ${row.finish_date}`;
			}, targets:[1]},
			{ render: function(data, type, row) {
				return `${row.duration} days`;
			}, targets:[2]},
			{ render: function(data, type, row) {
                if (row.status == -1) {
                    return `<span class="badge badge-secondary">Draft</span>`;
                } else if (row.status == 0) {
                    return `<span class="badge badge-warning">{{__('general.wait_approve')}}</span>`;
                } else if (row.status == 1) {
                    return `<span class="badge badge-success">{{__('general.approved')}}</span>`;
                } else {
                    return `<span class="badge badge-danger">{{__('general.reject')}}</span>`;
                }
			}, targets:[4]},
			{ render: function ( data, type, row ) {
				return `<div class="dropdown">
					<button type="button" class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
					<i class="fa fa-bars"></i>
					</button>
					<ul class="dropdown-menu dropdown-menu-right">
						<li><a class="dropdown-item editsalary" href="#" data-id="${row.id}"><i class="fas fa-search mr-2"></i> {{__('general.dtl')}}</a></li>
					</ul>
					</div>`
			},targets: [5]
			}
		],
		columns: [
			{ data: "no" },
			{ data: "start_date" },
			{ data: "duration" },
			{ data: "leave_name" },
			{ data: "status" },
			{ data: "id" },
		]
	});
  });
</script>
@endpush
