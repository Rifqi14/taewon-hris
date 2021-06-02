@extends('admin.layouts.app')

@section('title', __('spl.spl'))
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/daterangepicker/daterangepicker.css')}}" rel="stylesheet">
@endsection

@push('breadcrump')
<li class="breadcrumb-item active"><a href="{{route('spl.index')}}">{{ __('spl.spl') }}</a></li>
<li class="breadcrumb-item active">{{ __('general.edt') }}</li>
@endpush


@section('content')
<form id="form" action="{{ route('spl.update',['id'=>$spl->id]) }}" autocomplete="off" method="post">
	<div class="row">
		{{ csrf_field() }}
		{{ method_field('put') }}
		<div class="col-lg-8">
			<div class="card card-{{ config('configs.app_theme') }} card-outline">
				<div class="card-header" style="height: 55px;">
					<h3 class="card-title">{{ __('spl.spl') }} {{ __('general.data') }}</h3>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-sm-6">
							<div class="form-group">
								<label>{{ __('employee.empname') }} <b class="text-danger">*</b></label>
								<input type="text" name="employee_name" id="employee_name" class="form-control" placeholder="{{ __('employee.empname') }}" required>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label>{{ __('general.date') }} <b class="text-danger">*</b></label>
								<input type="text" name="spl_date" id="spl_date" class="form-control datepicker" placeholder="{{ __('general.date') }}" value=" {{ \Carbon\Carbon::parse($spl->spl_date)->format('d/m/Y')}}" required>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label>{{ __('spl.startdt') }} <b class="text-danger">*</b></label>
								<input placeholder="{{ __('spl.startdt') }}" name="start_date" id="start_date" class="form-control" value="{{ date('d/m/Y',strtotime($spl->start_date)) }}" />
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label>{{ __('spl.starttm') }} <b class="text-danger">*</b></label>
								<input placeholder="{{ __('spl.starttm') }}" name="start_time" id="start_time" class="form-control timepicker" value="{{ date('H:i:s',strtotime($spl->start_time)) }}" />
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label>{{ __('spl.fnshdate') }} <b class="text-danger">*</b></label>
								<input placeholder="{{ __('spl.fnshdate') }}" name="finish_date" id="finish_date" class="form-control" value="{{ date('d/m/Y',strtotime($spl->finish_date)) }}" />
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label>{{ __('spl.fnshtime') }} <b class="text-danger">*</b></label>
								<input placeholder="{{ __('spl.fnshtime') }}" name="finish_time" id="finish_time" class="form-control timepicker" value="{{ date('H:i:s',strtotime($spl->finish_time)) }}" />
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
								<textarea style="height: 120px;" class="form-control" name="notes" placeholder="{{ __('general.notes') }}">{{ $spl->notes }}</textarea>
							</div>
						</div>
						<div class="col-sm-12">
							<!-- text input -->
							<div class="form-group">
								<label>{{ __('general.status') }} <b class="text-danger">*</b></label>
								<select name="status" id="status" class="form-control select2" data-placeholder="{{ __('general.chs') }} {{ __('general.status') }}" required>
									<option @if($spl->status == 1) selected @endif value="1">{{ __('general.actv') }}</option>
									<option @if($spl->status == 0) selected @endif value="0">{{ __('general.noactv') }}</option>
								</select>
							</div>
						</div>
						{{-- <div style="height: 165px;"></div> --}}
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
<script src="{{asset('adminlte/component/daterangepicker/moment.min.js')}}"></script>
<script src="{{asset('adminlte/component/daterangepicker/daterangepicker.js')}}"></script>
<script>
	$(document).ready(function(){
        $('.datepicker').daterangepicker({
            singleDatePicker: true,
            // autoUpdateInput: false,
            timePicker: false,
            locale: {
                format: 'DD/MM/YYYY'
            }
            
        },
        function(chosen_date) {
            $('.datepicker').val(chosen_date.format('DD/MM/YYYY'));
        });
        $('.datepicker').on('change', function(){
            if (!$.isEmptyObject($(this).closest("form").validate())) {
                $(this).closest("form").validate().form();
            }
        })
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
		$('#start_date').daterangepicker({
			singleDatePicker: true,
			timePicker: true,
			timePicker24Hour: true,
			timePickerIncrement: 1,
			timePickerSeconds: false,
			locale: {
				format: 'DD/MM/YYYY'
			}
		},
		function(chosen_date) {
            $('#start_date').val(chosen_date.format('DD/MM/YYYY'));
        });
		$('#finish_date').daterangepicker({
			singleDatePicker: true,
			timePicker: true,
			timePicker24Hour: true,
			timePickerIncrement: 1,
			timePickerSeconds: false,
			locale: {
				format: 'DD/MM/YYYY'
			}
		},
		function(chosen_date) {
            $('#finish_date').val(chosen_date.format('DD/MM/YYYY'));
        });
        $('#employee_name').select2({
            ajax: {
                url: "{{route('spl.selectemployee')}}",
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
                    text: `${item.name}`,
                    employee_id: `${item.id}`,
                    nid: `${item.nid}`
                    });
                });
                return {
                    results: option, more: more,
                };
                },
            },
        });

        @if($spl->employee_id)
            $("#employee_name").select2('data',{id:{{$spl->employee_id}},text:'{{$spl->employee->name}}'}).trigger('change');
        @endif
        
        $(document).on('change', '#employee_name', function () {
            var employee_id = $('#employee_name').select2('data').id;
           
            $('#employee_name').val(`${employee_id}`);
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
	});

</script>
@endpush