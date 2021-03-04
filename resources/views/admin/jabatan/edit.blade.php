@extends('admin.layouts.app')

@section('title', 'Position')
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/css/fileinput.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.css')}}" rel="stylesheet">
@endsection

@push('breadcrump')
<li class="breadcrumb-item active"><a href="{{route('jabatan.index')}}">Position</a></li>
<li class="breadcrumb-item active">Edit</li>
@endpush


@section('content')

<div class="row">
	<div class="col-lg-8">
		<div class="card">
			<div class="card-header" style="height: 55px;">
				<h3 class="card-title">Position Data</h3>
			</div>
			<div class="card-body">
				<form id="form" method="post" action="{{ route('jabatan.update', ['id'=> $jabatan->id] )}}" autocomplete="off">
					{{ csrf_field() }}
					@method('put')
					<div class="row">
						<div class="col-sm-6">
							<!-- text input -->
							<input type="hidden" value="{{$jabatan->id}}" name="id"/>
							<div class="form-group">
								<label>Code <b class="text-danger">*</b></label>
								<input type="text" class="form-control" name="code" placeholder="Code" value="{{ $jabatan->code}}">
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label>Name <b class="text-danger">*</b></label>
								<input type="text" class="form-control" name="name" placeholder="Name" value="{{ $jabatan->name }}">
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-6">
							<!-- text input -->
							<div class="form-group">
								<label>Parent <b class="text-danger">*</b></label>
								<input type="text" class="form-control" id="parent_id" name="parent_id" data-placeholder="Select Parent" value="{{ $jabatan->department->name }}">
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label>Department <b class="text-danger">*</b></label>
								<input type="text" class="form-control" id="department_id" data-placeholder="Select Department" name="department_id" value="{{ $jabatan->department->name }}">
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-6">
							<!-- text input -->
							<div class="form-group">
								<label>Maximum Person <b class="text-danger">*</b></label>
								<input type="number" class="form-control" name="max_person" placeholder="Maximum Person" value="{{ $jabatan->max_person }}">
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-lg-4">
			<div class="card">
				<div class="card-header">
					<h3 class="card-title">Other</h3>
					<div class="pull-right card-tools">
						<button form="form" type="submit" class="btn btn-sm btn-danger text-white" title="Simpan"><i class="fa fa-save"></i></button>
						<a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i class="fa fa-reply"></i></a>
					</div>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-sm-12">
							<!-- text input -->
							<div class="form-group">
								<label>Notes <b class="text-danger">*</b></label>
								<textarea style="height: 120px;" class="form-control" name="notes" placeholder="Notes">{{ $jabatan->notes }}</textarea>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-12">
							<div class="form-group">
								<label>Status <b class="text-danger">*</b></label>
								<select class="form-control select2" required name="status" id="status">
									<option value="1" @if($jabatan->status == '1') selected @endif>Aktif</option>
									<option value="0" @if($jabatan->status == '0') selected @endif>Tidak Aktif</option>
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
	</form>
</div>
</div>

@endsection
@push('scripts')
<script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-fileinput/js/fileinput.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
<script>
	$(document).ready(function(){
		$("#status").select2();
		
		$( "#parent_id" ).select2({
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
		@if($jabatan->parent_id)
		$("#parent_id").select2('data',{id:{{$jabatan->parent_id}},text:'{{$jabatan->department->name}}'}).trigger('change');
		@endif
		$(document).on("change", "#parent_id", function () {
			if (!$.isEmptyObject($('#form').validate().submitted)) {
				$('#form').validate().form();
			}
		});

		$( "#department_id" ).select2({
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
		@if($jabatan->department_id)
		$("#department_id").select2('data',{id:{{$jabatan->department_id}},text:'{{$jabatan->department->name}}'}).trigger('change');
		@endif
		$(document).on("change", "#department_id", function () {
			if (!$.isEmptyObject($('#form').validate().submitted)) {
				$('#form').validate().form();
			}
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