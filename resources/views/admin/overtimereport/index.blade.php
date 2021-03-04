@extends('admin.layouts.app')

@section('title', 'Overtime Report')
@section('stylesheets')
<link rel="stylesheet" href="{{ asset('adminlte/component/dataTables/css/datatables.min.css') }}">
<link href="{{asset('adminlte/component/daterangepicker/daterangepicker.css')}}" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('adminlte/component/jquery-ui/jquery-ui.min.css') }}">
<style>
	.ui-state-active {
		background: #28a745 !important;
		border-color: #28a745 !important;
	}

	.ui-menu {
		overflow: auto;
		height: 200px;
	}
</style>
@endsection

@push('breadcrump')
<li class="breadcrumb-item active">Overtime Report</li>
@endpush

@section('content')
<div class="wrapper wrapper-content">
	<div class="row">
		<div class="col-lg-12">
			<div class="card card-{{ config('configs.app_theme') }} card-outline">
				<div class="card-header">
					<h3 class="card-title">Overtime Report</h3>
					<div class="pull-right card-tools">
						<a href="#" onclick="export1()" class="btn btn-{{ config('configs.app_theme') }} btn-sm text-white" data-toggle="tooltip" title="Export Overtime Report"><i class="fa fa-download"></i> Overtime Report</a>
						<a href="#" onclick="export2()" class="btn btn-primary btn-sm text-white" data-toggle="tooltip" title="Export Overtime Report+Nominal"><i class="fa fa-download"></i> Overtime Report + Nominal</a>
					</div>
				</div>
				<div class="card-body">
					<form id="form" class="form-horizontal" method="POST">
						@csrf
						<div class="row">
							<div class="col-md-4">
								<div class="form-group">
									<label for="employee_id" class="control-label">Employee Name</label>
									<input type="text" name="employee_name" id="employee_name" class="form-control filter" placeholder="Employee Name">
								</div>
								<div id="employee-container"></div>
							</div>
							<div class="col-md-4">
								<label for="nid" class="control-label">NIK</label>
								<input type="text" class="form-control filter" placeholder="NIK" name="nid" id="nid">
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label for="workgroup_id" class="control-label">Workgroup Combination</label>
									<input type="text" name="workgroup_id" id="workgroup_id" class="form-control filter" placeholder="Workgroup Combination">
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label for="department_id" class="control-label">Department</label>
									<input type="text" name="department_id" id="department_id" class="form-control filter" placeholder="Department">
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label for="position_id" class="control-label">Position</label>
									<input type="text" name="position_id" id="position_id" class="form-control filter" placeholder="Position">
								</div>
							</div>
							<div class="col-md-2">
								<label for="month" class="control-label">Bulan</label>
								<select class="form-control select2 filter" id="month" name="month" placeholder="Bulan">
									<option value=""></option>
									@php
									$months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
									@endphp
									@foreach ($months as $key => $month)
									<option value="{{ ++$key }}" @if ($key==date('m')) selected @endif>{{ $month }}</option>
									@endforeach
								</select>
							</div>
							<div class="col-md-2">
								<label for="year" class="control-label">Tahun</label>
								<select class="form-control select2 filter" id="year" name="year" placeholder="Tahun">
									<option value=""></option>
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
					</form>
					<table class="table table-striped table-bordered datatable" style="width: 100%">
						<thead>
							<tr>
								<th width="10">#</th>
								<th width="200">Employee Name</th>
								<th width="100">Workgroup</th>
								<th width="100">Department</th>
								<th width="100">Position</th>
								<th width="50">Date</th>
								<th width="50">Hour</th>
								<th width="100">Amount</th>
							</tr>
						</thead>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('adminlte/component/daterangepicker/moment.min.js') }}"></script>
<script src="{{ asset('adminlte/component/daterangepicker/daterangepicker.js') }}"></script>
<script src="{{ asset('adminlte/component/dataTables/js/datatables.min.js') }}"></script>
<script src="{{ asset('adminlte/component/validate/jquery.validate.min.js') }}"></script>
<script src="{{ asset('adminlte/component/jquery-ui/jquery-ui.min.js') }}"></script>
<script type="text/javascript">
	function export2() {
    $.ajax({
        url: "{{ route('overtimereport.export2') }}",
        type: 'POST',
        dataType: 'JSON',
        data: $("#form").serialize(),
        beforeSend:function(){
            // $('.overlay').removeClass('d-none');
            waitingDialog.show();
        }
    }).done(function(response){
        waitingDialog.hide();
        if(response.status){
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
        $('.overlay').addClass('d-none');
        $.gritter.add({
            title: 'Error!',
            text: response.message,
            class_name: 'gritter-error',
            time: 1000,
        });
    });
  }
	function export1() {
    $.ajax({
        url: "{{ route('overtimereport.export1') }}",
        type: 'POST',
        dataType: 'JSON',
        data: $("#form").serialize(),
        beforeSend:function(){
            // $('.overlay').removeClass('d-none');
            waitingDialog.show();
        }
    }).done(function(response){
        waitingDialog.hide();
        if(response.status){
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
        $('.overlay').addClass('d-none');
        $.gritter.add({
            title: 'Error!',
            text: response.message,
            class_name: 'gritter-error',
            time: 1000,
        });
    });
  }
	$(function() {
		$('.select2').select2({
		});
		$(".datepicker").daterangepicker({
			startDate: moment().subtract(1, 'week'),
			endDate: moment(),
			locale: {
				format: 'DD/MM/YYYY'
			}
		});

		$('#department_id').select2({
      ajax: {
        url: "{{route('department.select')}}",
        type:'GET',
        dataType: 'json',
        data: function (term,page) {
          return {
            name:term,
            page:page,
            limit:30,
          };
        },
        results: function (data,page) {
          var more = (page * 30) < data.total;
          var option = [];
          $.each(data.rows,function(index,item){
            option.push({
              id:item.name,
              text: `${item.path}`
            });
          });
          return {
            results: option, more: more,
          };
        },
      },
      allowClear: true,
      multiple: true
    });

		$('#workgroup_id').select2({
      ajax: {
        url: "{{route('workgroup.select')}}",
        type:'GET',
        dataType: 'json',
        data: function (term,page) {
          return {
            name:term,
            page:page,
            limit:30,
          };
        },
        results: function (data,page) {
          var more = (page * 30) < data.total;
          var option = [];
          $.each(data.rows,function(index,item){
            option.push({
              id:item.id,
              text: `${item.name}`
            });
          });
          return {
            results: option, more: more,
          };
        },
      },
      allowClear: true,
      multiple: true
    });

		$('#position_id').select2({
      ajax: {
        url: "{{route('title.select')}}",
        type:'GET',
        dataType: 'json',
        data: function (term,page) {
          return {
            name:term,
            page:page,
            limit:30,
          };
        },
        results: function (data,page) {
          var more = (page * 30) < data.total;
          var option = [];
          $.each(data.rows,function(index,item){
            option.push({
              id:item.id,
              text: `${item.name}`
            });
          });
          return {
            results: option, more: more,
          };
        },
      },
      allowClear: true,
      multiple: true
    });

		var employees = [
			@foreach($employees as $employee)
				"{!! $employee->name !!}",
			@endforeach
		];

		var employees_nid = [
			@foreach($employees as $employee)
				"{!! $employee->nid !!}",
			@endforeach
		];

		$("#employee_name").autocomplete({
			source: employees,
			minLength: 0,
			appendTo: '#employee-container',
			select: function(event, response) {
				if (event.preventDefault(), 0 !== response.item.id) {
					$(this).val(response.item.value);
					dataTable.draw();
				}
			}
		}).focus(function () {
			$(this).autocomplete("search");
		});
		$("#employee_name").keydown(function(event){
			if (event.keyCode == 13) {
				event.preventDefault();
				$("#employee_name").autocomplete('close');
				return false;
			}
		});

		$("#nid").autocomplete({
			source: employees_nid,
			minLength: 0,
			appendTo: '#employee-container',
			select: function(event, response) {
				if (event.preventDefault(), 0 !== response.item.id) {
					$(this).val(response.item.value);
					dataTable.draw();
				}
			}
		}).focus(function () {
			$(this).autocomplete("search");
		});
		$("#nid").keydown(function(event){
			if (event.keyCode == 13) {
				event.preventDefault();
				$("#nid").autocomplete('close');
				return false;
			}
		});

		dataTable = $('.datatable').DataTable({
			processing: true,
			serverSide: true,
			stateSave: true,
			filter: false,
			info: false,
			lengthChange: true,
			responsive: true,
			order: false,
			lengthMenu: [ 50, 100, 250, 500],
			ajax: {
				url: "{{ route('overtimereport.read') }}",
				type: "GET",
				data: function(data){
					var employee_id = $('input[name=employee_name]').val();
					var nid = $('input[name=nid]').val();
					var department_id = $('input[name=department_id]').val();
					var workgroup_id = $('input[name=workgroup_id]').val();
					var position_id = $('input[name=position_id]').val();
					var month = $('select[name=month]').val();
					var year = $('select[name=year]').val();
					data.employee_id = employee_id;
					data.nid = nid;
					data.department_id = department_id;
					data.workgroup_id = workgroup_id;
					data.position_id = position_id;
					data.month = month;
					data.year = year;
				}
			},
			columnDefs: [
				{ orderable: false, targets: [0,1,2,3,4,5,6,7] },
				{ className: "text-center", targets: [5] },
				{ className: "text-right", targets: [6,7]},
			],
			columns: [
				{ data: "no" },
				{ data: "employee_name"},
				{ data: "workgroup_name"},
				{ data: "department_name"},
				{ data: "title_name"},
				{ data: "date"},
				{ data: "total_hour"},
				{ data: "total_overtime", render: $.fn.dataTable.render.number('.', '', 0, 'Rp. ')},
			]
		})
		
		$(".filter").on("change", function(){
			dataTable.draw();
		});
	});
</script>
@endpush