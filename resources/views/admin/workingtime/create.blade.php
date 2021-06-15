@extends('admin.layouts.app')

@section('title', __('employee.workshift'))
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/daterangepicker/daterangepicker.css')}}" rel="stylesheet">
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
<li class="breadcrumb-item active"><a href="{{route('workingtime.index')}}">{{ __('employee.workshift') }}</a></li>
<li class="breadcrumb-item active">{{ __('general.crt') }}</li>
@endpush


@section('content')
<form id="form" action="{{ route('workingtime.store')}}" autocomplete="off" method="post">
	<div class="row">
		<div class="col-lg-8">
			<div class="card card-{{ config('configs.app_theme') }} card-outline">
				<div class="card-header" style="height: 55px;">
					<h3 class="card-title">{{ __('employee.workshift') }} Data</h3>
				</div>
				<div class="card-body">
					{{ csrf_field() }}
					<div class="row">
						<div class="col-sm-6">
							<!-- text input -->
							<div class="form-group">
								<label>{{ __('shift.wttype') }} <b class="text-danger">*</b></label>
								<select name="working_time_type" id="working_time_type" class="form-control select2" data-placeholder="{{ __('general.chs') }} {{ __('shift.wttype') }}">
									@foreach(config('enums.workingtime_type') as $key => $value)
									<option value="{{ $key }}">{{ $value }}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label>{{ __('shift.shftdet') }} <b class="text-danger">*</b></label>
								<input type="text" name="description" class="form-control" placeholder="{{ __('shift.shftdet') }}" required>
							</div>
						</div>
					</div>
				</div>
				<div class="overlay d-none">
					<i class="fa fa-2x fa-sync-alt fa-spin"></i>
				</div>
			</div>
		</div>
		<div class="col-lg-4">
			<div class="card card-{{ config('configs.app_theme') }} card-outline">
				<div class="card-header">
					<h3 class="card-title">{{ __('general.other') }}</h3>
					<div class="pull-right card-tools">
						<button form="form" type="submit" class="btn btn-sm btn-{{config('configs.app_theme')}} text-white" title="{{ __('general.save') }}"><i class="fa fa-save"></i></button>
						<a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="{{ __('general.prvious') }}"><i class="fa fa-reply"></i></a>
					</div>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-sm-12">
							<!-- text input -->
							<div class="form-group">
								<label>{{ __('general.notes') }}</label>
								<textarea style="height: 120px;" class="form-control" name="notes" placeholder="{{ __('general.notes') }}"></textarea>
							</div>
						</div>
					</div>
				</div>
				<div class="overlay d-none">
					<i class="fa fa-2x fa-sync-alt fa-spin"></i>
				</div>
			</div>
		</div>
		<div class="col-lg-12">
			<div class="card card-{{ config('configs.app_theme') }} card-outline">
				<div class="nav nav-tabs" id="nav-tab" role="tablist">
					<a class="nav-item nav-link active" id="nav-shift-tab" data-toggle="tab" href="#nav-shift" role="tab" aria-controls="nav-shift" aria-selected="true">{{ __('employee.workshift') }}</a>
					<a class="nav-item nav-link" id="nav-department-tab" data-toggle="tab" href="#nav-department" role="tab" aria-controls="nav-department" aria-selected="false">{{ __('department.dep') }}</a>
				</div>
				<div class="tab-content" id="nav-tabContent">
					<div class="tab-pane fade show active" id="nav-shift" role="tabpanel" aria-labelledby="nav-shift-tab">
						<div class="card-header">
							<h3 class="card-title">{{ __('shift.shiftrule') }}</h3>
						</div>
						<div class="card-body">
							<table class="table table-striped table-bordered datatable" id="shift-table" style="width: 100%">
								<thead>
									<tr>
										<th width="10" class="text-center align-middle">No</th>
										<th width="50">{{ __('general.day') }}</th>
										<th width="25" class="text-center align-middle">{{ __('shift.startime') }}</th>
										<th width="25" class="text-center align-middle">{{ __('shift.fintime') }}</th>
										<th width="25" class="text-center align-middle">{{ __('shift.mintime') }}</th>
										<th width="25" class="text-center align-middle">{{ __('shift.maxtime') }}</th>
										<th width="25" class="text-center align-middle">{{ __('shift.minwork') }}</th>
										<th width="50" class="text-center align-middle">Status</th>
									</tr>
								</thead>
								<tbody>
									@php
									($days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat','Sun', 'Off']);
									@endphp
									@foreach ($days as $key => $day)
									<tr>
										<td class="text-center align-middle">{{ ++$key }}</td>
										<td class="align-middle">
											<input type="hidden" name="day[]" value="{{ $day }}" />
											{{ $day }}
										</td>
										<td class="text-center align-middle">
											<div class="form-group mb-0"><input placeholder="Start Time" name="start[]" class="form-control timepicker" /></div>
										</td>
										<td class="text-center align-middle">
											<div class="form-group mb-0"><input placeholder="Finish Time" name="finish[]" class="form-control timepicker" /></div>
										</td>
										<td class="text-center align-middle">
											<div class="form-group mb-0"><input placeholder="Minimum time In" name="min_in[]" class="form-control timepicker" /></div>
										</td>
										<td class="text-center align-middle">
											<div class="form-group mb-0"><input placeholder="Maximum time Out" name="max_out[]" class="form-control timepicker" /></div>
										</td>
										<td class="text-center align-middle">
											<div class="form-group mb-0"><input type="number" placeholder="Minimum Workingtime" name="min_wt[]" class="form-control" /></div>
										</td>
										<td class="text-center align-middle"><input type="checkbox" name="save[]" class="i-checks" /></td>
									</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					</div>
					<div class="tab-pane fade show" id="nav-department" role="tabpanel" aria-labelledby="nav-department-tab">
						<div class="card-header">
							<h3 class="card-title">{{ __('department.dep') }}</h3>
						</div>
						<div class="card-body">
							<table class="table table-striped table-bordered datatable" id="department-table" style="width: 100%">
								<thead>
									<tr>
										<th class="text-center align-middle">No</th>
										<th width="400">{{ __('department.depname') }}</th>
										<th class="text-center align-middle">
											<div class="customcheckbox">
												<input type="checkbox" name="checkall" class="checkall">
											</div>
										</th>
									</tr>
								</thead>
							</table>
						</div>
					</div>
				</div>
				<div class="overlay d-none">
					<i class="fa fa-2x fa-sync-alt fa-spin"></i>
				</div>
			</div>
		</div>
	</div>
</form>
@endsection
@push('scripts')
<script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
<script src="{{asset('adminlte/component/dataTables/js/datatables.min.js')}}"></script>
<script src="{{asset('adminlte/component/daterangepicker/moment.min.js')}}"></script>
<script src="{{asset('adminlte/component/daterangepicker/daterangepicker.js')}}"></script>
<script>
	$(document).ready(function(){
		$('.i-checks').iCheck({
			checkboxClass: 'icheckbox_square-green',
			radioClass: 'iradio_square-green',
		});
		$('.timepicker').daterangepicker({
			singleDatePicker: true,
			timePicker: true,
			timePicker24Hour: true,
			timePickerIncrement: 1,
			timePickerSeconds: false,
			locale: {
				format: 'HH:mm'
			}
		}).on('show.daterangepicker', function(ev, picker) {
			picker.container.find('.calendar-table').hide();
    });
		dataTableDepartment = $("#department-table").DataTable({
			stateSave: true,
			processing: true,
			serverSide: true,
			filter: false,
			info: false,
			lengtChange: true,
			responsive: true,
			order: [[1, "asc"]],
			language: {
				url: language_choosen == 'id' ? urlLocaleId : '',
			},
			lengthMenu: [ 100, 250, 500, 1000 ],
			ajax: {
				url: "{{ route('departmentshift.read') }}",
				type: "GET",
				data: function(data) {
					
				}
			},
			columnDefs: [
				{ orderable: false, targets: [0,1,2] },
				{ className: "text-center", targets: [0,2] },
				{ render: function ( data, type, row ) {
              return `<label class="customcheckbox checked"><input value="${row.id}" type="checkbox" name="department_id[]" checked><span class="checkmark"></span></label>`
            },targets: [2] }
			],
			columns: [
				{ data: "no" },
				{ data: "name" },
				{ data: "id" },
			]
		});
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
				});
			}
		});
		$('input[name=checkall]').prop('checked', true);
		$('input[name=checkall]').parent().addClass('checked');
		$('input[name^=department_id]').prop('checked', true);
		$('input[name^=department_id]').parent().addClass('checked');
		$(document).on('click', '.customcheckbox input', function() {
			if ($(this).is(':checked')) {
				$(this).parent().addClass('checked');
			} else {
				$(this).parent().removeClass('checked');
			}
		});
		$(document).on('change', '.checkall', function() {
			if (this.checked) {
				$('input[name^=department_id]').prop('checked', true);
				$('input[name^=department_id]').parent().addClass('checked');
			} else {
				$('input[name^=department_id]').prop('checked', false);
				$('input[name^=department_id]').parent().removeClass('checked');
			}
		});
	});

</script>
@endpush