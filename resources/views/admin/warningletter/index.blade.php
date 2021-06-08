@extends('admin.layouts.app')

@section('title',__('warningletter.wl'))
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
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
<li class="breadcrumb-item active">{{__('warningletter.wl')}}</li>
@endpush

@section('content')
<div class="wrapper wrapper-content">
	<div class="row">
		<div class="col-lg-12">
			<div class="card ">
				<div class="card-header">
					<h3 class="card-title">{{__('warningletter.wl')}}</h3>
					<!-- tools box -->
					<div class="pull-right card-tools">
						<a href="{{route('warningletter.create')}}" class="btn btn-{{ config('configs.app_theme')}} btn-sm text-white"
							data-toggle="tooltip" title="{{__('general.crt')}}">
							<i class="fa fa-plus"></i>
						</a>
						<a href="javascript:void(0)" onclick="exportwl()" class="btn btn-primary btn-sm text-white" data-toggle="tooltip" title="{{ __('general.exp') }}" style="cursor: pointer;"><i class="fa fa-download"></i></a>
					</div>
					<!-- /. tools -->
				</div>
				<div class="card-body">
					<form id="form" action="{{ route('warningletter.store') }}" class="form-horizontal" method="post">
			          	{{ csrf_field() }}
						<div class="row">
							<div class="col-md-4">
								<div class="form-group">
									<label class="control-label" for="employee_id">{{__('employee.empname')}}</label>
									<input type="text" name="employee_name" id="employee_name" class="form-control" placeholder="{{__('employee.empname')}}">
								</div>
								<div id="employee-container"></div>
							</div>
							<div class="col-md-4">
							<div class="form-group">
								<label class="control-label" for="nik">NIK</label>
								<input type="text" name="nik" id="nik" class="form-control" placeholder="NIK" multiple>
							</div>
							</div>
							<div class="col-md-4">
								<div class="col-md-12">
								<div class="form-group">
									<label class="control-label" for="department">{{__('department.dep')}}</label>
									<select name="department[]" id="department" class="form-control select2" style="width: 100%" aria-hidden="true" multiple data-placeholder="{{__('department.dep')}}">
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
									<label class="control-label" for="type">{{__('employee.position')}}</label>
									<select name="position" id="position" class="form-control select2" style="width: 100%" aria-hidden="true" multiple data-placeholder="{{__('employee.position')}}">
										@foreach ($titles as $title)
										<option value="{{ $title->id }}">{{ $title->name }}</option>
										@endforeach
									</select>
								</div>
								</div>
							</div>
							<div class="col-md-4">
								<div class="col-md-12">
								<div class="form-group">
									<label class="control-label" for="workgroup">Status</label>
									<select name="status" id="status" class="form-control select2" style="width: 100%" aria-hidden="true" data-placeholder="Status">
										<option value=""></option>
										<option value="0">{{__('general.actv')}}</option>
										<option value="1">{{__('general.noactv')}}</option>
									</select>
								</div>
								</div>
							</div>
						</div>
						<table class="table table-striped table-bordered datatable" style="width:100%">
							<thead>
								<tr>
									<th width="10">#</th>
									<th width="100">{{__('employee.empname')}}</th>
									<th width="100">{{__('employee.position')}}</th>
									<th width="100">{{__('department.dep')}}</th>
									<th width="100">{{__('employee.jd')}}</th>
									<th width="100">From</th>
									<th width="100">To</th>
									<th width="100">Total SP Aktif</th>
									<th width="120">Total SP Non Aktif</th>
									<th width="100">Status</th>
									<th width="10">{{__('general.act')}}</th>
								</tr>
							</thead>
						</table>
					</form>
				</div>
				<div class="overlay d-none">
					<i class="fa fa-refresh fa-spin"></i>
				</div>
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
	function filter(){
		$('#add-filter').modal('show');
	}
	function exportwl() {
		$.ajax({
			url: "{{ route('warningletter.export') }}",
			type: 'POST',
			dataType: 'JSON',
			data: $("#form").serialize(),
			beforeSend:function(){
				// $('.overlay').removeClass('d-none');
				waitingDialog.show('Loading...');
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
	$(function(){
		dataTable = $('.datatable').DataTable( {
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
				url: "{{route('warningletter.read')}}",
				type: "GET",
				data:function(data){
					var employee_id = $('input[name=employee_name]').val();
					var nid = $('input[name=nik]').val();
					var department = $("select[name='department[]']").val();
					var position = $('select[name=position]').val();
					var status = $('select[name=status]').val();
					data.employee_id = employee_id;
					data.nid = nid;
					data.department = department;
					data.position = position;
					data.status = status;
				}
			},
			columnDefs:[
				{
					orderable: false,targets:[0]
				},
				{ className: "text-right", targets: [0,7,8] },
				{ className: "text-center", targets: [3,4,5,6,7] },
				{
                    render: function (data, type, row) {
                        return `<span>${row.employee_name}</span><br>${row.nid}`;
                    },
                    targets: [1]
                },
                { render: function(data, type, row) {
                    if (row.status == 0) {
                        return '<span class="badge badge-success">{{__('general.actv')}}</span>';
                    } else {
                        return '<span class="badge badge-danger">{{__('general.noactv')}}</span>';
                    }
                }, targets:[9]},
				{ render: function ( data, type, row ) {
					return `<div class="dropdown">
					<button class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
					<i class="fa fa-bars"></i>
					</button>
					<ul class="dropdown-menu dropdown-menu-right">
					<li><a class="dropdown-item" href="{{url('admin/warningletter')}}/${row.id}/edit"><i class="fas fa-pencil-alt mr-2"></i> {{__('general.edt')}}</a></li>
					<li><a class="dropdown-item delete" href="#" data-id="${row.id}"><i class="fas fa-trash mr-2"></i> {{__('general.dlt')}}</a></li>
					</ul></div>`
				},targets: [10]
				}
			],
			columns: [
			{ data: "no" },
			{ data: "employee_name" },
			{ data: "title_name" },
			{ data: "department_name" },
			{ data: "join_date" },
			{ data: "from" },
			{ data: "to" },
			{ data: "sp_active" },
			{ data: "sp_non_active" },
			{ data: "status" },
			{ data: "id" },
			]
		});
		$(".select2").select2({
			allowClear: true
		});
		$(document).ready(function(){
			var employees = [
				@foreach($employees as $employee)
                	"{!!$employee->name!!}",
            	@endforeach
			];
			$( "input[name=employee_name]" ).autocomplete({
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
			$("input[name=employee_name]").keydown(function(event){
				if(event.keyCode == 13) {
					event.preventDefault();
					$('input[name=employee_name]').autocomplete('close');
					return false;
				}
			});
			$(document).on('keyup', '#employee_name', function() {
				dataTable.draw();
			});
			$(document).on('keyup', '#nik', function() {
				dataTable.draw();
			});
			$(document).on('change', '#department', function() {
				dataTable.draw();
			});
			$(document).on('change', '#position', function() {
				dataTable.draw();
			});
			$(document).on('change', '#status', function() {
				dataTable.draw();
			});
		});
		$(document).on('click','.delete',function(){
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
				title:'Delete Warning Letter?',
				message:'Data that has been deleted cannot be recovered',
				callback: function(result) {
					if(result) {
						var data = {
							_token: "{{ csrf_token() }}"
						};
						$.ajax({
							url: `{{url('admin/warningletter')}}/${id}`,
							dataType: 'json',
							data:data,
							type:'DELETE',
							beforeSend:function(){
								$('.overlay').removeClass('d-none');
							}
						}).done(function(response){
							if(response.status){
								$('.overlay').addClass('d-none');
								$.gritter.add({
									title: 'Success!',
									text: response.message,
									class_name: 'gritter-success',
									time: 1000,
								});
								dataTable.ajax.reload( null, false );
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
							var response = response.responseJSON;
							$('.overlay').addClass('d-none');
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