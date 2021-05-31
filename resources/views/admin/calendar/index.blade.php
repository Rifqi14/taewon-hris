@extends('admin.layouts.app')

@section('title', __('config.calendar'))

@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
@endsection

@push('breadcrump')
<li class="breadcrumb-item active">{{ __('config.calendar') }}</li>
@endpush

@section('content')
<div class="wrapper wrapper-content">
	<div class="row">
		<div class="col-lg-12">
			<div class="card card-{{ config('configs.app_theme') }} card-outline">
				<div class="card-header">
					<h3 class="card-title">{{ __('config.calendar') }}</h3>
					<div class="pull-right card-tools">
						<a href="{{ route('calendar.create') }}" class="btn btn-{{ config('configs.app_theme') }} btn-sm text-white"
							data-toggle="tooltip" title="{{ __('config.crt') }}"><i class="fa fa-plus"></i></a>
						<a href="#" onclick="filter()" class="btn btn-default btn-sm" data-toggle="tooltip" title="Search"><i
								class="fa fa-search"></i></a>
					</div>
				</div>
				<div class="card-body">
					<table class="table table-striped table-bordered datatable" style="width:100%">
						<thead>
							<tr>
								<th width="10">#</th>
								{{-- <th width="40">Code</th> --}}
								<th width="150">{{ __('config.calname') }}</th>
								<th width="250">{{ __('config.desc') }}</th>
								<th width="5">Active</th>
								<th width="5">#</th>
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
	<div class="modal fade" id="add-filter" tabindex="-1" role="dialog" aria-hidden="true" tabindex="-1" role="dialog"
		aria-hidden="true" data-backdrop="static">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">Filter</h4>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				</div>
				<div class="modal-body">
					<form id="form-search" autocomplete="off">
						<div class="row">
							{{-- <div class="col-md-12">
								<div class="form-group">
									<label class="control-label" for="code">Calendar Code</label>
									<input type="text" name="code" class="form-control" placeholder="Calendar Code">
								</div>
							</div> --}}
							<div class="col-md-12">
								<div class="form-group">
									<label class="control-label" for="name">{{ __('config.calname') }}</label>
									<input type="text" name="name" class="form-control" placeholder="{{ __('config.calname') }}">
								</div>
							</div>
							<div class="col-md-12">
								<div class="form-group">
									<label class="control-label" for="name">{{ __('config.desc') }}</label>
									<input type="text" name="description" class="form-control" placeholder="{{ __('config.desc') }}">
								</div>
							</div>
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<button form="form-search" type="submit" class="btn btn-{{ config('configs.app_theme') }}" title="Apply"><i
							class="fa fa-search"></i></button>
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
	function filter() {
	$('#add-filter').modal('show');
}
$(function () {
	dataTable = $('.datatable').DataTable({
		stateSave:true,
    processing:true,
    serverSide:true,
    filter:false,
    info:false,
    lengthChange:true,
    responsive:true,
    order: [[ 4, "asc" ]],
		ajax: {
			url: "{{route('calendar.read')}}",
			type: "GET",
			data:function(data){
				{{-- var code = $('#form-search').find('input[name=code]').val(); --}}
				var name = $('#form-search').find('input[name=name]').val();
				var description = $('#form-search').find('input[name=description]').val();
				{{-- data.code = code; --}}
				data.name = name;
				data.description = description;
			}
		},
		columnDefs:[
			{ orderable: false,targets:[0] },
			{ className: "text-right", targets: [0] },
			{ className: "text-center", targets: [3, 4] },
			{ render: function(data, type, row) {
							if (data == 1) {
								return '<i class="fa fa-check text-green"></i>';
							} else {
								return '<i class="fa fa-times text-red"></i>';
							}
						}, targets:[3]},
			{ render: function ( data, type, row ) {
				return `<div class="dropdown">
									<button class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
										<i class="fa fa-bars"></i>
									</button>
									<ul class="dropdown-menu dropdown-menu-right">
										<li><a class="dropdown-item" href="{{url('admin/calendar')}}/${row.id}/edit"><i class="fas fa-pencil-alt mr-2"></i> Edit</a></li>
										<li><a class="dropdown-item delete" href="#" data-id="${row.id}"><i class="fas fa-trash mr-2"></i> Delete</a></li>
									</ul>
								</div>`
				},targets: [4]
			}
		],
		columns: [
			{ data: "no" },
			{{-- { data: "code" }, --}}
			{ data: "name" },
			{ data: "description" },
			{ data: "is_default" },
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
			title:'Delete calendar?',
			message:'Data that has been deleted cannot be recovered',
			callback: function(result) {
				if(result) {
					var data = {
						_token: "{{ csrf_token() }}"
					};
					$.ajax({
						url: `{{url('admin/calendar')}}/${id}`,
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
						} else {
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
					});
				}
			}
		});
	});
});
</script>
@endpush