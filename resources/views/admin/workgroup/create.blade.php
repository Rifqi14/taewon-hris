@extends('admin.layouts.app')

@section('title', 'Work Group Combination')
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
@endsection

@push('breadcrump')
<li class="breadcrumb-item active"><a href="{{route('workgroup.index')}}">Work Group Combination</a></li>
<li class="breadcrumb-item active">Create</li>
@endpush


@section('content')
<div class="row">
	<div class="col-lg-8 pb-3">
		<div class="card card-{{ config('configs.app_theme') }} card-outline h-100">
			<div class="card-header">
				<h3 class="card-title" style="padding-bottom: 12px">Work Group Combination Data</h3>
			</div>
			<div class="card-body">
				<form id="form" action="{{ route('workgroup.store') }}" class="form-horizontal" method="post"
					autocomplete="off">
					{{ csrf_field() }}
					<div class="row">
						<div class="col-sm-6">
							<!-- text input -->
							<div class="form-group">
								<label>Work Group</label>
								<input type="text" class="form-control" name="workgroup_id" id="workgroup_id" placeholder="Work Group">
							</div>
						</div>
						<div class="col-sm-6">
							<!-- text input -->
							<div class="form-group">
								<label>Penalty Type</label>
								<select name="penalty" id="penalty" class="form-control select2" style="width: 100%" aria-hidden="true">
									@foreach (config('enums.penalty_type') as $key => $value)
									<option value="{{ $key }}">{{ $value }}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-6">
							<!-- text input -->
							<div class="form-group">
								<label>Combination Code</label>
								<input type="text" class="form-control" name="code" id="code" placeholder="Combination Code">
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label>Combination Name</label>
								<input type="text" class="form-control" name="name" id="name" placeholder="Combination Name">
							</div>
						</div>
					</div>
			</div>
			<div class="overlay d-none">
				<i class="fa fa-2x fa-sync-alt fa-spin"></i>
			</div>
		</div>
	</div>
	<div class="col-lg-4 pb-3">
		<div class="card card-{{ config('configs.app_theme') }} card-outline h-100">
			<div class="card-header">
				<h3 class="card-title">Other</h3>
				<div class="pull-right card-tools">
					<button form="form" type="submit" class="btn btn-sm btn-{{ config('configs.app_theme')}}" title="Simpan"><i
							class="fa fa-save"></i></button>
					<a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i
							class="fa fa-reply"></i></a>
				</div>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-sm-12">
						<!-- text input -->
						<div class="form-group">
							<label>Description</label>
							<textarea class="form-control" id="description" name="description" placeholder="Notes"></textarea>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-12">
						<div class="form-group">
							<label>Status</label>
							<select name="status" id="status" class="form-control select2" style="width: 100%" aria-hidden="true">
								<option value="1">Active</option>
								<option value="0">Non-Active</option>
							</select>
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
<script src="{{asset('adminlte/component/dataTables/js/datatables.min.js')}}"></script>
<script>
	$(document).ready(function(){
			$('.select2').select2();
			function filter(){
				$('#add-filter').modal('show');
			}
			$("#workgroup_id").select2({
				ajax: {
					url: "{{route('workgroupmaster.select')}}",
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
			});
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