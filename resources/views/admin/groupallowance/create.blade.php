@extends('admin.layouts.app')

@section('title', 'Create Group Allowance')
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/css/fileinput.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.css')}}" rel="stylesheet">
@endsection

@push('breadcrump')
<li class="breadcrumb-item"><a href="{{route('groupallowance.index')}}">Group Allowance</a></li>
<li class="breadcrumb-item active">Create</li>
@endpush


@section('content')
<div class="row px-2">
	<div class="col-lg-8 py-2">
		<div class="card h-100 card-{{ config('configs.app_theme') }} card-outline">
			<div class="card-header">
				<h3 class="card-title">Group Allowance List</h3>
			</div>
			<div class="card-body">
				<form id="form" action="{{ route('groupallowance.store') }}" class="form-horizontal" method="post" autocomplete="off">
					{{ csrf_field() }}
					<div class="box-body">
						<div class="row">
							<div class="col-sm-6">
								<!-- text input -->
								<div class="form-group">
									<label>Code</label>
									<input type="text" class="form-control" placeholder="Code" id="code" name="code">
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label>Group Allowance Name</label>
									<input type="text" class="form-control" placeholder="Group Allowance Name" id="group_allowance" name="group_allowance">
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-6">
								<div class="form-group">
									<label>Type</label>
									<select name="type" id="type" class="form-control select2" style="width: 100%" aria-hidden="true">
										<option value="ADDITIONAL">Additional</option>
										<option value="DEDUCTION">Dedcution</option>
									</select>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label>Status</label>
									<select name="status" id="status" class="form-control select2" style="width: 100%" aria-hidden="true">
										<option value="1">Active</option>
										<option value="0">Non-Active</option>
									</select>
								</div>
							</div>
						</div>
					</div>
			</div>
			<div class="overlay d-none">
				<i class="fa fa-2x fa-sync-alt fa-spin"></i>
			</div>
		</div>
	</div>
	<div class="col-lg-4 py-2">
		<div class="card h-100 card-{{ config('configs.app_theme') }} card-outline">
			<div class="card-header">
				<h3 class="card-title">Other</h3>
				<div class="pull-right card-tools">
					<button form="form" type="submit" class="btn btn-sm btn-{{ config('configs.app_theme') }}" title="Simpan"><i class="fa fa-save"></i></button>
					<a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i class="fa fa-reply"></i></a>
				</div>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-sm-12">
						<!-- text input -->
						<div class="form-group">
							<label>Notes</label>
							<textarea class="form-control" id="notes" name="notes" placeholder="Notes" rows="5"></textarea>
						</div>
					</div>
				</div>
				</form>
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
<script src="{{asset('adminlte/component/bootstrap-fileinput/js/fileinput.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.js')}}"></script>
<script>
	$(document).ready(function(){
		$('.select2').select2();
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