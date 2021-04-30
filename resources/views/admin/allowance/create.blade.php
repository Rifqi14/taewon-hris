@extends('admin.layouts.app')

@section('title', 'Create Allowance')
@section('stylesheets')
<link rel="stylesheet" href="{{ asset('adminlte/component/dataTables/css/datatables.min.css') }}">
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/css/fileinput.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.css')}}" rel="stylesheet">
<style type="text/css">
  .customcheckbox {
    width: 22px;
    height: 22px;
    background: url("/img/green.png") no-repeat;
    background-position-x: 0%;
    background-position-y: 0%;
    cursor: pointer;
    margin: 0 auto;
  }

  .customcheckbox.checked {
    background-position: -48px 0;
  }

  .customcheckbox:hover {
    background-position: -24px 0;
  }

  .customcheckbox.checked:hover {
    background-position: -48px 0;
  }

  .customcheckbox input {
    cursor: pointer;
    opacity: 0;
    scale: 1.6;
    width: 22px;
    height: 22px;
    margin: 0;
  }
</style>
@endsection

@push('breadcrump')
<li class="breadcrumb-item"><a href="{{route('allowance.index')}}">Allowance</a></li>
<li class="breadcrumb-item active">Create</li>
@endpush


@section('content')
<form id="form" action="{{ route('allowance.store') }}" class="form-horizontal" method="post"
	autocomplete="off">
	{{ csrf_field() }}
	<div class="row px-2">
		<div class="col-lg-8 py-2">
			<div class="card h-100 card-{{ config('configs.app_theme') }} card-outline">
				<div class="card-header">
					<h3 class="card-title">Allowance List</h3>
				</div>
				<div class="card-body">
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
											<option value="breaktime">BreakTime</option>
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
								<div class="col-sm-3">
									<div class="form-group">
										<label>Prorate</label>
										<select name="prorate" id="prorate" class="form-control select2" style="width: 100%" aria-hidden="true">
											<option value="No"  selected>No</option>
											<option value="Yes">Yes</option>
										</select>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>THR</label>
										<select name="thr" id="thr" class="form-control select2" style="width: 100%" aria-hidden="true">
											<option value="No" selected>No</option>
											<option value="Yes">Yes</option>
										</select>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-6 formula-bpjs-section d-none">
									<div class="form-group">
										<label for="formula-bpjs" class="control-label">Formula BPJS <b class="text-danger">*</b></label>
										<select name="formula_bpjs" id="formula_bpjs" class="form-control select2" data-placeholder="Formula BPJS" required>
										@foreach (config('enums.penalty_config_type') as $key => $item)
										<option value="{{ $key }}">{{ $item }}</option>
										@endforeach
										</select>
									</div>
								</div>
								<div class="col-sm-6 working-time-section d-none">
									<div class="form-group">
										<label>Working Time</label>
										<input type="text" class="form-control" placeholder="Working Time" id="working_time" name="working_time">
									</div>
								</div>
								<div class="col-sm-6 days-devisor-section d-none">
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
					
				</div>
				<div class="overlay d-none">
					<i class="fa fa-2x fa-sync-alt fa-spin"></i>
				</div>
			</div>
		</div>
		<div class="col-lg-12 allowance-section d-none">
			<div class="card card-{{ config('configs.app_theme') }} card-outline">
				<div class="card-header">
				<h3 class="card-titl">Allowance</h3>
				</div>
				<div class="card-body">
				<table class="table table-striped table-bordered datatable" id="allowance-table" style="width: 100%">
					<thead>
					<tr>
						<th width="10">No</th>
						<th width="200">Allowance</th>
						<th width="200">Category</th>
						<th width="200">Group</th>
						<th width="10">
						<div class="customcheckbox">
							<input type="checkbox" name="checkall" class="checkall" id="checkall">
						</div>
						</th>
					</tr>
					</thead>
				</table>
				</div>
			</div>
		</div>
	</div>
</form>
@endsection
@push('scripts')
<script src="{{ asset('adminlte/component/dataTables/js/datatables.min.js') }}"></script>
<script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-fileinput/js/fileinput.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
<script>
	const BASIC = 'BASIC';
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
				$('.formula-bpjs-section').addClass('d-none');
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
				$('.formula-bpjs-section').addClass('d-none');
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
				$('.formula-bpjs-section').addClass('d-none');
				break;
				case 'pensiunPekerja':
				if (val == 'All') {
					$('#formula_bpjs').select2('disable');
					$('#formula_bpjs').select2('val', "");
				} else {
					$('#formula_bpjs').select2('enable');
				}
				$('.formula-bpjs-section').removeClass('d-none');
				$('.days-devisor-section').addClass('d-none');
				$('.basic-salary-section').addClass('d-none');
				$('.working-time-section').addClass('d-none');
				break;
				case 'pensiunPemberi':
				if (val == 'All') {
					$('#formula_bpjs').select2('disable');
					$('#formula_bpjs').select2('val', "");
				} else {
					$('#formula_bpjs').select2('enable');
				}
				$('.formula-bpjs-section').removeClass('d-none');
				$('.days-devisor-section').addClass('d-none');
				$('.basic-salary-section').addClass('d-none');
				$('.working-time-section').addClass('d-none');
				break;
				case 'premiPekerja':
				if (val == 'All') {
					$('#formula_bpjs').select2('disable');
					$('#formula_bpjs').select2('val', "");
				} else {
					$('#formula_bpjs').select2('enable');
				}
				$('.formula-bpjs-section').removeClass('d-none');
				$('.days-devisor-section').addClass('d-none');
				$('.basic-salary-section').addClass('d-none');
				$('.working-time-section').addClass('d-none');
				break;
				case 'premiPemberi':
				if (val == 'All') {
					$('#formula_bpjs').select2('disable');
					$('#formula_bpjs').select2('val', "");
				} else {
					$('#formula_bpjs').select2('enable');
				}
				$('.formula-bpjs-section').removeClass('d-none');
				$('.days-devisor-section').addClass('d-none');
				$('.basic-salary-section').addClass('d-none');
				$('.working-time-section').addClass('d-none');
				break;
				default:
				$('.formula-bpjs-section').addClass('d-none');
				$('.working-time-section').addClass('d-none');
				$('.days-devisor-section').addClass('d-none');
				$('.basic-salary-section').addClass('d-none');
				break;
			}
		});

		$(document).on('change', '#formula_bpjs', function() {
		if (this.value == BASIC) {
			$('.allowance-section').addClass('d-none');
		} else {
			$('.allowance-section').removeClass('d-none');
		}
		}).trigger('change');
			$('input[name=checkall]').prop('checked', true);
			$('input[name=checkall]').parent().addClass('checked');
			$(document).on('click', '.customcheckbox input', function() {
				if ($(this).is(':checked')) {
					$(this).parent().addClass('checked');
				} else {
					$(this).parent().removeClass('checked');
				}
			});
		$(document).on('change', '.checkall', function() {
			if (this.checked) {
				$('input[name^=allowanceID]').prop('checked', true);
				$('input[name^=allowanceID]').parent().addClass('checked');
			} else {
				$('input[name^=allowanceID]').prop('checked', false);
				$('input[name^=allowanceID]').parent().removeClass('checked');
			}
		});
		dataTableAllowance = $('.datatable').DataTable({
			stateSave: true,
			processing: true,
			serverSide: true,
			filter: false,
			info: false,
			lengthChange: false,
			responsive: true,
			paginate: false,
			order: [[ 1, "asc"]],
			ajax: {
				url: "{{ route('allowance.read') }}",
				type: "GET",
				data: function(data) {
				
				}
			},
			columnDefs: [
				{ orderable: false, targets: [0, 4] },
				{ className: 'text-right', targets: [0] },
				{ className: 'text-center', targets: [4] },
				{ render: function( data, type, row ) {
				return `<label class="customcheckbox checked"><input value="${row.id}" type="checkbox" name="allowanceID[]" checked><span class="checkmark"></span></label>`
				}, targets: [4] }
			],
			columns: [
				{ data: 'no' },
				{ data: 'allowance' },
				{ data: 'category' },
				{ data: 'groupallowance' },
				{ data: 'id' },
			]
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