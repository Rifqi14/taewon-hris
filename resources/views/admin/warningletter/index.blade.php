@extends('admin.layouts.app')

@section('title', 'Warning Letter')
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
<style>
	.duration:hover {
		cursor: pointer;
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
                        {{-- <a href="{{route('spl.import')}}" class="btn btn-{{ config('configs.app_theme') }} btn-sm" 
                            data-toggle="tooltip" title="Import" style="cursor: pointer;">
                            <i class="fa fa-file-import"></i>
                        </a> --}}
						<a href="{{route('warningletter.create')}}" class="btn btn-{{ config('configs.app_theme')}} btn-sm text-white"
							data-toggle="tooltip" title="Tambah">
							<i class="fa fa-plus"></i>
						</a>
						<a href="#" onclick="filter()" class="btn btn-default btn-sm" data-toggle="tooltip" title="Search">
							<i class="fa fa-search"></i>
						</a>
					</div>
					<!-- /. tools -->
				</div>
				<div class="card-body">
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
<script src="{{asset('adminlte/component/dataTables/js/datatables.min.js')}}"></script>
<script src="{{asset('assets/js/plugins/bootbox/bootbox.min.js')}}"></script>
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
					
				}
			},
			columnDefs:[
				{
					orderable: false,targets:[0]
				},
				{ className: "text-right", targets: [0] },
				{ className: "text-center", targets: [3,4,5,6,7] },
                { render: function(data, type, row) {
                    if (data == 0) {
                        return '<span class="badge badge-warning">Waiting Approval</span>';
                    } else {
                        return '<span class="badge badge-success">Approval</span>';
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
		$('#form-search').submit(function(e){
			e.preventDefault();
			dataTable.draw();
			$('#add-filter').modal('hide');
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