@extends('admin.layouts.app')

@section('title', __('workgroupcombination.workcomb'))
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
@endsection

@push('breadcrump')
<li class="breadcrumb-item active"><a href="{{route('workgroup.index')}}">{{ __('workgroupcombination.workcomb') }}</a></li>
<li class="breadcrumb-item active">{{ __('general.crt') }}</li>
@endpush


@section('content')
<div class="row">
	<div class="col-lg-8 pb-3">
		<div class="card card-{{ config('configs.app_theme') }} card-outline h-100">
			<div class="card-header">
				<h3 class="card-title" style="padding-bottom: 12px">{{ __('workgroupcombination.workcombdata') }}</h3>
			</div>
			<div class="card-body">
				<form id="form" action="{{ route('workgroup.store') }}" class="form-horizontal" method="post" autocomplete="off">
					{{ csrf_field() }}
					<div class="row">
						<div class="col-sm-6">
							<!-- text input -->
							<div class="form-group">
								<label>{{ __('workgroup.workgrp') }}</label>
								<input type="text" class="form-control" name="workgroup_id" id="workgroup_id" placeholder="{{ __('general.chs') }} {{ __('workgroup.workgrp') }}">
							</div>
						</div>
						<div class="col-sm-6">
							<!-- text input -->
							<div class="form-group">
								<label>{{ __('workgroupcombination.combcode') }}</label>
								<input type="text" class="form-control" name="code" id="code" placeholder="{{ __('workgroupcombination.combcode') }}">
							</div>
						</div>
						{{-- <div class="col-sm-6">
							<!-- text input -->
							<div class="form-group">
								<label>Penalty Type</label>
								<select name="penalty" id="penalty" class="form-control select2" style="width: 100%" aria-hidden="true">
									@foreach (config('enums.penalty_type') as $key => $value)
									<option value="{{ $key }}">{{ $value }}</option>
						@endforeach
						</select>
					</div>
			</div> --}}
		</div>
		<div class="row">
			<div class="col-sm-6">
				<div class="form-group">
					<label>{{ __('workgroupcombination.combname') }}</label>
					<input type="text" class="form-control" name="name" id="name" placeholder="{{ __('workgroupcombination.combname') }}">
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
			<h3 class="card-title">{{ __('general.other') }}</h3>
			<div class="pull-right card-tools">
				<button form="form" type="submit" class="btn btn-sm btn-{{ config('configs.app_theme')}}" title="{{ __('general.save') }}"><i class="fa fa-save"></i></button>
				<a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="{{ __('general.prvious') }}"><i class="fa fa-reply"></i></a>
			</div>
		</div>
		<div class="card-body">
			<div class="row">
				<div class="col-sm-12">
					<!-- text input -->
					<div class="form-group">
						<label>{{ __('general.desc') }}</label>
						<textarea class="form-control" id="description" name="description" placeholder="{{ __('general.desc') }}"></textarea>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<div class="form-group">
						<label>{{ __('general.status') }}</label>
						<select name="status" id="status" class="form-control select2" style="width: 100%" aria-hidden="true">
							<option value="1">{{ __('general.actv') }}</option>
							<option value="0">{{ __('general.noactv') }}</option>
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