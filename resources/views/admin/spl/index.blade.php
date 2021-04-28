@extends('admin.layouts.app')

@section('title', 'SPL | Surat Pengajuan Lembur')
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
<style>
	.duration:hover {
		cursor: pointer;
	}
</style>
@endsection

@push('breadcrump')
<li class="breadcrumb-item active">SPL (Surat Pengajuan Lembur)</li>
@endpush

@section('content')
<div class="wrapper wrapper-content">
	<div class="row">
		<div class="col-lg-12">
			<div class="card ">
				<div class="card-header">
					<h3 class="card-title">SPL (Surat Pengajuan Lembur)</h3>
					<!-- tools box -->
					<div class="pull-right card-tools">
                        <a href="{{route('spl.import')}}" class="btn btn-{{ config('configs.app_theme') }} btn-sm" 
                            data-toggle="tooltip" title="Import" style="cursor: pointer;">
                            <i class="fa fa-file-import"></i>
                        </a>
						<a href="{{route('spl.create')}}" class="btn btn-{{ config('configs.app_theme')}} btn-sm text-white"
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
								<th width="100">Start Date</th>
								<th width="100">Start Time</th>
								<th width="100">Finish Date</th>
								<th width="100">Finish Time</th>
								<th width="10">Duration</th>
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
<div class="modal fade" id="edit-duration" tabindex="-1" role="dialog" aria-hidden="true" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Duration</h4>
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        </div>
        <div class="modal-body">
          <form id="form-duration" action="{{ route('spl.durationupdate') }}" class="form-horizontal no-gutters" method="post" autocomplete="off">
            {{ csrf_field() }}
            <input type="hidden" name="spl_id" id="spl_id">
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label class="control-label" for="duration">Duration</label>
                  <input type="text" class="form-control" name="duration" id="duration" placeholder="Duration" value="0">
                </div>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button form="form-duration" type="submit" class="btn btn-{{ config('configs.app_theme') }}" title="Apply"><i class="fa fa-save"></i></button>
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
				url: "{{route('spl.read')}}",
				type: "GET",
				data:function(data){
					var working_time_type = $('#form-search').find('select[name=working_time_type]').val();
					data.working_time_type = working_time_type;
				}
			},
			columnDefs:[
				{
					orderable: false,targets:[0]
				},
				{ className: "text-right", targets: [0] },
				{ className: "text-center", targets: [3,4,5,6,7,8,9] },
                { render: function(data, type, row) {
                    if (data == 1) {
                        return '<span class="badge badge-success">Active</span>';
                    } else {
                        return '<span class="badge badge-danger">Non Active</span>';
                    }
                }, targets:[8]},
				{ render: function ( data, type, row ) {
					return `<div class="dropdown">
					<button class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
					<i class="fa fa-bars"></i>
					</button>
					<ul class="dropdown-menu dropdown-menu-right">
					<li><a class="dropdown-item" href="{{url('admin/spl')}}/${row.id}/edit"><i class="fas fa-pencil-alt mr-2"></i> Edit</a></li>
					<li><a class="dropdown-item delete" href="#" data-id="${row.id}"><i class="fas fa-trash mr-2"></i> Delete</a></li>
					</ul></div>`
				},targets: [9]
				}
			],
			columns: [
			{ data: "no" },
			{ data: "nik" },
			{ data: "employee_name" },
			{ data: "start_date" },
			{ data: "start_time" },
			{ data: "finish_date" },
			{ data: "finish_time" },
			{ data: "duration", className: "duration align-middle text-center" },
			{ data: "status" },
			{ data: "id" },
			]
		});
		$('#form-search').submit(function(e){
			e.preventDefault();
			dataTable.draw();
			$('#add-filter').modal('hide');
		})
		$('.datatable').on('click', '.duration', function() {
		var data = dataTable.row(this).data();
		// console.table(data);
		if (data) {
			$('#edit-duration').modal('show');
			$('#form-duration input[name=spl_id]').attr('value', data.id);
			$('#form-duration input[name=duration]').attr('value', data.duration);
		}
		});
		// $("#form-duration").validate({
		// 	errorElement: 'div',
		// 	errorClass: 'invalid-feedback',
		// 	focusInvalid: false,
		// 	highlight: function (e) {
		// 		$(e).closest('.form-group').removeClass('has-success').addClass('was-validated has-error');
		// 	},

		// 	success: function (e) {
		// 		$(e).closest('.form-group').removeClass('has-error').addClass('has-success');
		// 		$(e).remove();
		// 	},
		// 	errorPlacement: function (error, element) {
		// 		if(element.is(':file')) {
		// 		error.insertAfter(element.parent().parent().parent());
		// 		}else
		// 		if(element.parent('.input-group').length) {
		// 		error.insertAfter(element.parent());
		// 		}
		// 		else
		// 		if (element.attr('type') == 'checkbox') {
		// 		error.insertAfter(element.parent());
		// 		}
		// 		else{
		// 		error.insertAfter(element);
		// 		}
		// 	},
		// 	submitHandler: function() {
		// 		$.ajax({
		// 		url:$('#form-duration').attr('action'),
		// 		method:'post',
		// 		data: new FormData($('#form-duration')[0]),
		// 		processData: false,
		// 		contentType: false,
		// 		dataType: 'json',
		// 		beforeSend:function(){
		// 			$('.overlay').removeClass('d-none');
		// 		}
		// 		}).done(function(response){
		// 			$('.overlay').addClass('d-none');
		// 			if(response.status){
		// 				dataTable.draw();
		// 				$('#edit-duration').modal('hide');
		// 				$('#form-duration input[name=spl_id]').attr('value', '');
		// 				$('#form-duration input[name=duration]').attr('value', '0');;
		// 				$.gritter.add({
		// 					title: 'Success!',
		// 					text: response.message,
		// 					class_name: 'gritter-success',
		// 					time: 1000,
		// 				});
		// 			}
		// 			else{
		// 				$.gritter.add({
		// 					title: 'Warning!',
		// 					text: response.message,
		// 					class_name: 'gritter-warning',
		// 					time: 1000,
		// 				});
		// 			}
		// 			return;
		// 		}).fail(function(response){
		// 			$('.overlay').addClass('d-none');
		// 			var response = response.responseJSON;
		// 			$.gritter.add({
		// 				title: 'Error!',
		// 				text: response.message,
		// 				class_name: 'gritter-error',
		// 				time: 1000,
		// 			});
		// 		});
		// 	}
		// });
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
				title:'Delete Surat Pengajuan Lembur?',
				message:'Data that has been deleted cannot be recovered',
				callback: function(result) {
					if(result) {
						var data = {
							_token: "{{ csrf_token() }}"
						};
						$.ajax({
							url: `{{url('admin/spl')}}/${id}`,
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