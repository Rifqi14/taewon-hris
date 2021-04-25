@extends('admin.layouts.app')

@section('title', 'SPL | Surat Pengajuan Lembur')
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/daterangepicker/daterangepicker.css')}}" rel="stylesheet">
@endsection

@push('breadcrump')
<li class="breadcrumb-item active"><a href="{{route('spl.index')}}">Surat Pengajuan Lembur</a></li>
<li class="breadcrumb-item active">Edit</li>
@endpush


@section('content')
<form id="form" action="{{ route('spl.update',['id'=>$spl->id]) }}" autocomplete="off" method="post">
<div class="row">
    {{ csrf_field() }}
    {{ method_field('put') }}
	<div class="col-lg-8">
		<div class="card card-{{ config('configs.app_theme') }} card-outline">
			<div class="card-header" style="height: 55px;">
				<h3 class="card-title">Surat Pengajuan Lembur Data</h3>
			</div>
			<div class="card-body">
                <div class="row">
					<div class="col-sm-6">
                        <div class="form-group">
                            <label>Date <b class="text-danger">*</b></label>
                            <input type="text" name="spl_date" id="spl_date" class="form-control datepicker" placeholder="Date" value=" {{ \Carbon\Carbon::parse($spl->spl_date)->format('d/m/Y')}}" required>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Employee Name <b class="text-danger">*</b></label>
                            <input type="text" name="employee_name" id="employee_name" class="form-control" placeholder="Employee Name" required>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>NIK <b class="text-danger">*</b></label>
                            <input type="text" name="nik" class="form-control" placeholder="NIK" id="nik" required readonly value="{{ $spl->nik }}">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Start Date <b class="text-danger">*</b></label>
                            <input placeholder="Start Date" name="start_date" id="start_date" class="form-control" value="{{ date('d/m/Y',strtotime($spl->start_date)) }}"/>
                        </div>
                    </div>
					<div class="col-sm-6">
                        <div class="form-group">
                            <label>Start Time <b class="text-danger">*</b></label>
                            <input placeholder="Start Time" name="start_time" id="start_time" class="form-control timepicker" value="{{ date('H:i:s',strtotime($spl->start_time)) }}"/>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Finish Date <b class="text-danger">*</b></label>
                            <input placeholder="Finish Date" name="finish_date" id="finish_date" class="form-control" value="{{ date('d/m/Y',strtotime($spl->finish_date)) }}"/>
                        </div>
                    </div>
					<div class="col-sm-6">
                        <div class="form-group">
                            <label>Finish Time <b class="text-danger">*</b></label>
                            <input placeholder="Finish Time" name="finish_time" id="finish_time" class="form-control timepicker" value="{{ date('H:i:s',strtotime($spl->finish_time)) }}"/>
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
							<textarea style="height: 120px;" class="form-control" name="notes" placeholder="Notes">{{ $spl->notes }}</textarea>
						</div>
					</div>
                    <div class="col-sm-12">
                        <!-- text input -->
                        <div class="form-group">
                            <label>Status <b class="text-danger">*</b></label>
                            <select name="status" id="status" class="form-control select2"
                                data-placeholder="Select Status" required>
                                <option @if($spl->status == 1) selected @endif value="1">Active</option>
								<option @if($spl->status == 0) selected @endif value="0">Non Active</option>
                            </select>
                        </div>
                    </div>
					<div style="height: 165px;"></div>
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
            var nid = $('#employee_name').select2('data').nid;
            $('#nik').val(`${nid}`);
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