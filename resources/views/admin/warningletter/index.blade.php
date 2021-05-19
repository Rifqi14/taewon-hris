@extends('admin.layouts.app')

@section('title', 'Warning Letter')
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/jquery-ui/jquery-ui.min.css')}}" rel="stylesheet">
<style>
	.ui-state-active{
        background: #28a745 !important;
        border-color: #28a745 !important;
    }
    .ui-menu {
        overflow: auto;
        height:200px;
    }
</style>
@endsection

@push('breadcrump')
<li class="breadcrumb-item active">Warning Letter</li>
@endpush

@section('content')
<div class="wrapper wrapper-content">
	<div class="row">
		<div class="col-lg-12">
			<div class="card ">
				<div class="card-header">
					<h3 class="card-title">Warning Letter</h3>
					<!-- tools box -->
					<div class="pull-right card-tools">
						<a href="{{route('warningletter.create')}}" class="btn btn-{{ config('configs.app_theme')}} btn-sm text-white"
							data-toggle="tooltip" title="Tambah">
							<i class="fa fa-plus"></i>
						</a>
					</div>
					<!-- /. tools -->
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<label class="control-label" for="employee_id">Employee Name</label>
								<input type="text" name="employee_name" id="employee_name" class="form-control" placeholder="Employee Name">
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
								<label class="control-label" for="department">Department</label>
								<select name="department" id="department" class="form-control select2" style="width: 100%" aria-hidden="true" multiple data-placeholder="Department">
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
								<label class="control-label" for="type">Position</label>
								<select name="position" id="position" class="form-control select2" style="width: 100%" aria-hidden="true" multiple data-placeholder="Position">
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
									<option value="0" selected>Active</option>
									<option value="1">Non Active</option>
								  </select>
							  </div>
							</div>
						  </div>
					</div>
					<table class="table table-striped table-bordered datatable" style="width:100%">
						<thead>
							<tr>
								<th width="10">#</th>
								<th width="100">NIK</th>
								<th width="100">Employee Name</th>
								<th width="100">Position</th>
								<th width="100">Department</th>
								<th width="100">Join date</th>
								<th width="100">Status</th>
								<th width="10">Action</th>
							</tr>
						</thead>
					</table>
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
<script src="{{asset('adminlte/component/jquery-ui/jquery-ui.min.js')}}"></script>
<script src="{{asset('adminlte/component/dataTables/js/datatables.min.js')}}"></script>
<script type="text/javascript">
	function filter(){
		$('#add-filter').modal('show');
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
			ajax: {
				url: "{{route('warningletter.read')}}",
				type: "GET",
				data:function(data){
					var employee_id = $('input[name=employee_name]').val();
					var nid = $('input[name=nik]').val();
					var department = $('select[name=department]').val();
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
				{ className: "text-right", targets: [0] },
				{ className: "text-center", targets: [3,4,5,6,7] },
                { render: function(data, type, row) {
                    if (row.status == 0) {
                        return '<span class="badge badge-success">Active</span>';
                    } if (row.status == 1) {
                        return '<span class="badge badge-danger">Non Active</span>';
                    }
                }, targets:[6]},
				{ render: function ( data, type, row ) {
					return `<div class="dropdown">
					<button class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
					<i class="fa fa-bars"></i>
					</button>
					<ul class="dropdown-menu dropdown-menu-right">
					<li><a class="dropdown-item" href="{{url('admin/warningletter')}}/${row.id}/edit"><i class="fas fa-pencil-alt mr-2"></i> Edit</a></li>
					<li><a class="dropdown-item delete" href="#" data-id="${row.id}"><i class="fas fa-trash mr-2"></i> Delete</a></li>
					</ul></div>`
				},targets: [7]
				}
			],
			columns: [
			{ data: "no" },
			{ data: "employee_id" },
			{ data: "employee_name" },
			{ data: "title_name" },
			{ data: "department_name" },
			{ data: "join_date" },
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