@extends('admin.layouts.app')

@section('title', 'Working Shift')
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/daterangepicker/daterangepicker.css')}}" rel="stylesheet">
@endsection

@push('breadcrump')
<li class="breadcrumb-item active"><a href="{{route('workingtime.index')}}">Working Shift</a></li>
<li class="breadcrumb-item active">Edit</li>
@endpush


@section('content')
<form id="form" action="{{ route('workingtime.update',['id'=>$workingtime->id])}}" method="post">
	{{ csrf_field() }}
<div class="row">
	<div class="col-lg-8">
		<div class="card card-{{ config('configs.app_theme')}} card-outline">
			<div class="card-header" style="height: 55px;">
				<h3 class="card-title">Update Working Shift</h3>
			</div>
			<div class="card-body">
					@method('put')
					<div class="row">
						<div class="col-sm-6">
							<!-- text input -->
							<div class="form-group">
								<label>Working Shift Type <b class="text-danger">*</b></label>
								<select name="working_time_type" id="working_time_type" class="form-control select2" data-placeholder="Select Working Time">
									@foreach(config('enums.workingtime_type') as $key => $value)
									<option value="{{ $key }}" @if ($workingtime->working_time_type == $key) selected @endif>{{ $value }}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label>Shift Detail <b class="text-danger">*</b></label>
								<input type="text" value="{{ $workingtime->description }}" name="description" class="form-control" placeholder="Description" required>
							</div>
						</div>
					</div>
			</div>
		</div>
	</div>
	<div class="col-lg-4">
		<div class="card card-{{ config('configs.app_theme')}} card-outline">
			<div class="card-header">
				<h3 class="card-title">Other</h3>
				<div class="pull-right card-tools">
					<button form="form" type="submit" class="btn btn-sm btn-{{config('configs.app_theme')}} text-white" title="Simpan"><i class="fa fa-save"></i></button>
					<a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i class="fa fa-reply"></i></a>
				</div>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-sm-12">
						<!-- text input -->
						<div class="form-group">
							<label>Notes</label>
							<textarea style="height: 120px;" class="form-control" name="notes" placeholder="Notes">{{ $workingtime->notes }}</textarea>
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
			<div class="card-header">
				<h3 class="card-title">Working Shift Rule</h3>
			</div>
			<div class="card-body">
				<table class="table table-striped table-bordered datatable" id="shift-table" style="width: 100%">
					<thead>
						<tr>
							<th width="10" class="text-center align-middle">No</th>
							<th width="50">Day</th>
							<th width="25" class="text-center align-middle">Start Time</th>
							<th width="25" class="text-center align-middle">Finish Time</th>
							<th width="25" class="text-center align-middle">Min Time In</th>
							<th width="25" class="text-center align-middle">Max Time Out</th>
							<th width="25" class="text-center align-middle">Min Workingtime</th>
							<th width="50" class="text-center align-middle">Status</th>
						</tr>
					</thead>
					<tbody>
						@foreach ($workingtime->detail as $key => $item)		
						<tr>
							<td class="text-center align-middle">{{ ++$key }}</td>
							<td class="align-middle">
								<input type="hidden" name="day[]" value="{{ $item->day }}"/>
								{{ $item->day }}
							</td>
							<td class="text-center align-middle">
								<div class="form-group mb-0"><input placeholder="Start Time" name="start[]" class="form-control timepicker" value="{{ $item->start }}"/></div>
							</td>
							<td class="text-center align-middle">
								<div class="form-group mb-0"><input placeholder="Finish Time" name="finish[]" class="form-control timepicker" value="{{ $item->finish }}"/></div>
							</td>
							<td class="text-center align-middle">
								<div class="form-group mb-0"><input placeholder="Minimum time In" name="min_in[]" class="form-control timepicker" value="{{ $item->min_in }}"/></div>
							</td>
							<td class="text-center align-middle">
								<div class="form-group mb-0"><input placeholder="Maximum time Out" name="max_out[]" class="form-control timepicker" value="{{ $item->max_out }}"/></div>
							</td>
							<td class="text-center align-middle">
								<div class="form-group mb-0"><input type="number" placeholder="Minimum Workingtime" name="min_wt[]" class="form-control" value="{{ $item->min_workhour }}"/></div>
							</td>
							<td class="text-center align-middle"><input type="checkbox" name="save[]" class="i-checks" @if ($item->status == 1) checked @endif/></td>
						</tr>
						@endforeach
					</tbody>
				</table>
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
<script src="{{asset('adminlte/component/daterangepicker/moment.min.js')}}"></script>
<script src="{{asset('adminlte/component/daterangepicker/daterangepicker.js')}}"></script>
<script>
	$(document).ready(function(){
		$('.i-checks').iCheck({
			checkboxClass: 'icheckbox_square-green',
			radioClass: 'iradio_square-green',
		});
		$("#working_time_type").select2();
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