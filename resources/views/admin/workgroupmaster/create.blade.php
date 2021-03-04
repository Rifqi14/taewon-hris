@extends('admin.layouts.app')

@section('title', 'Create Work Group')
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
@endsection

@push('breadcrump')
<li class="breadcrumb-item active"><a href="{{route('workgroupmaster.index')}}">Work Group</a></li>
<li class="breadcrumb-item active">Create</li>
@endpush

@section('content')
<div class="row">
	<div class="col-lg-12">
		<div class="card card-{{ config('configs.app_theme') }} card-outline h-100">
			<div class="card-header">
				<h3 class="card-title">Create Work Group</h3>
				<div class="pull-right card-tools">
					<button form="form" type="submit" class="btn btn-sm btn-{{ config('configs.app_theme')}}" title="Simpan"><i
							class="fa fa-save"></i></button>
					<a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i
							class="fa fa-reply"></i></a>
				</div>
			</div>
			<div class="card-body">
				<form id="form" action="{{ route('workgroupmaster.store') }}" class="form-horizontal" method="post"
					autocomplete="off">
					{{ csrf_field() }}
					<div class="row">
						<div class="col-sm-6">
							<!-- text input -->
							<div class="form-group">
								<label>Workgroup Code</label>
								<input type="text" class="form-control" name="code" id="code" placeholder="Workgroup Code" required>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label>Workgroup Name</label>
								<input type="text" class="form-control" name="name" id="name" placeholder="Workgroup Name" required>
							</div>
						</div>
					</div>
			</div>
			<div class="overlay d-none">
				<i class="fa fa-2x fa-sync-alt fa-spin"></i>
			</div>
		</div>
	</div>
</div>
@endsection

@push('scripts')
<script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
<script>
	$(document).ready(function(){
			$("#form").validate({
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
					if(element.is(':file')) {
						error.insertAfter(element.parent().parent().parent());
					}else
					if(element.parent('.input-group').length) {
						error.insertAfter(element.parent());
					}
					else
						if (element.attr('type') == 'checkbox') {
							error.insertAfter(element.parent());
						}
						else{
							error.insertAfter(element);
						}
					},
					submitHandler: function() {
						$.ajax({
							url:$('#form').attr('action'),
							method:'post',
							data: new FormData($('#form')[0]),
							processData: false,
							contentType: false,
							dataType: 'json',
							beforeSend:function(){
								$('.overlay').removeClass('d-none');
							}
						}).done(function(response){
							$('.overlay').addClass('d-none');
							if(response.status){
								document.location = response.results;
							}
							else{
								$.gritter.add({
									title: 'Warning!',
									text: response.message,
									class_name: 'gritter-warning',
									time: 1000,
								});
							}
							return;
						}).fail(function(response){
							$('.overlay').addClass('d-none');
							var response = response.responseJSON;
							$.gritter.add({
								title: 'Error!',
								text: response.message,
								class_name: 'gritter-error',
								time: 1000,
							});
						})
					}
			});
		});

</script>
@endpush