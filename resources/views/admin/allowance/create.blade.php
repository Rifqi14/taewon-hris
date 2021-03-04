@extends('admin.layouts.app')

@section('title', 'Create Allowance')
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/css/fileinput.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.css')}}" rel="stylesheet">
@endsection

@push('breadcrump')
<li class="breadcrumb-item"><a href="{{route('allowance.index')}}">Allowance</a></li>
<li class="breadcrumb-item active">Create</li>
@endpush


@section('content')
<div class="row px-2">
	<div class="col-lg-8 py-2">
		<div class="card h-100 card-{{ config('configs.app_theme') }} card-outline">
			<div class="card-header">
				<h3 class="card-title">Allowance List</h3>
			</div>
			<div class="card-body">
				<form id="form" action="{{ route('allowance.store') }}" class="form-horizontal" method="post"
					autocomplete="off">
					{{ csrf_field() }}
					<div class="box-body">
						<div class="row">
							<div class="col-sm-6">
								<!-- text input -->
								<div class="form-group">
									<label>Allowance Name</label>
									<input type="text" class="form-control" placeholder="Allowance" id="allowance" name="allowance"
										required>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label>Category</label>
									<select name="category" id="category" class="form-control select2" style="width: 100%"
										aria-hidden="true" data-placeholder="Select Category">
										<option value=""></option>
										@foreach (config('enums.allowance_category') as $key=>$value)
										<option value="{{ $key }}">{{ $value }}</option>
										@endforeach
									</select>
								</div>
							</div>
						</div>
						<div class="row account-section">
							<div class="col-sm-6">
								<!-- text input -->
								<div class="form-group">
									<label>Account</label>
									<input type="text" class="form-control" placeholder="Account" id="account" name="account">
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label>Recurrence</label>
									<select name="recurrence" id="recurrence" class="form-control select2" style="width: 100%"
										aria-hidden="true" data-placeholder="Select Recurrence">
										<option value=""></option>
										<option value="hourly">Hourly</option>
										<option value="daily">Daily</option>
										<option value="monthly">Monthly</option>
										<option value="yearly">Yearly</option>
									</select>
								</div>
							</div>
						</div>
						<div class="row ">
							<div class="col-sm-6">
								<div class="form-group">
									<label>Group Allowance</label>
									<input type="text" class="form-control" placeholder="Select Group Allowance" id="groupallowance" name="groupallowance">
								</div>
							</div>
							<div class="col-sm-6 working-time-section d-none">
								<div class="form-group">
									<label>Working Time</label>
									<input type="text" class="form-control" placeholder="Working Time" id="working_time" name="working_time">
								</div>
							</div>
						</div>
						<div class="row days-devisor-section d-none">
							<div class="col-sm-6">
								<!-- text input -->
								<div class="form-group">
									<label>Days Devisor</label>
									<input type="text" class="form-control" id="days_devisor" name="days_devisor"
										placeholder="Days Devisor">
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
	<div class="col-lg-4 py-2">
		<div class="card h-100 card-{{ config('configs.app_theme') }} card-outline">
			<div class="card-header">
				<h3 class="card-title">Other</h3>
				<div class="pull-right card-tools">
					<button form="form" type="submit" class="btn btn-sm btn-{{ config('configs.app_theme') }}" title="Simpan"><i
							class="fa fa-save"></i></button>
					<a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i class="fa fa-reply"></i></a>
				</div>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-sm-12">
						<!-- text input -->
						<div class="form-group">
							<label>Notes</label>
							<textarea class="form-control" id="notes" name="notes" placeholder="Notes"></textarea>
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
<script src="{{asset('adminlte/component/bootstrap-fileinput/js/fileinput.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
<script>
	$(document).ready(function(){
		$('.select2').select2();
		$("#account").select2({
			ajax: {
				url: "{{route('account.select')}}",
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
							text: `${item.acc_name}`
						});
					});
					return {
						results: option, more: more,
					};
				},
			},
			allowClear: true,
		});
		$("#groupallowance").select2({
			ajax: {
				url: "{{route('groupallowance.select')}}",
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
		$("#working_time").select2({
			ajax: {
				url: "{{route('workingtime.select')}}",
				type:'GET',
				dataType: 'json',
				data: function (term,page) {
					return {
						working_time_type: $('#working_type').val(),
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
							text: `${item.description}`
						});
					});
					return {
						results: option, more: more,
					};
				},
			},
			allowClear: true,
			multiple: true
		});
		$(document).on("change", "#parent", function () {
			if (!$.isEmptyObject($('#form').validate().submitted)) {
				$('#form').validate().form();
			}
		});
		$(document).on("change", "#category", function () {
			var value = $(this).val();
			var val = $('#working_type').val();
			switch (value) {
				case 'tunjanganLain':
				if (val == 'All') {
					$('#working_time').select2('disable');
					$('#working_time').select2('val', "");
				} else {
					$('#working_time').select2('enable');
				}
				$('.working-time-section').removeClass('d-none');
				$('.days-devisor-section').addClass('d-none');
				$('.basic-salary-section').addClass('d-none');
				break;
				case 'tunjanganJkkJkm':
				if (val == 'All') {
					$('#working_time').select2('disable');
					$('#working_time').select2('val', "");
				} else {
					$('#working_time').select2('enable');
				}
				$('.working-time-section').removeClass('d-none');
				$('.days-devisor-section').addClass('d-none');
				$('.basic-salary-section').removeClass('d-none');
				break;
				case 'tunjanganKehadiran':
				if (val == 'All') {
					$('#working_time').select2('disable');
					$('#working_time').select2('val', "");
				} else {
					$('#working_time').select2('enable');
				}
				$('.working-time-section').removeClass('d-none');
				$('.days-devisor-section').removeClass('d-none');
				$('.basic-salary-section').addClass('d-none');
				break;
				default:
				$('.working-time-section').addClass('d-none');
				$('.days-devisor-section').addClass('d-none');
				$('.basic-salary-section').addClass('d-none');
				break;
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