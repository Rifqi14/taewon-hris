@extends('admin.layouts.app')

@section('title', 'Grade')
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
<link rel="stylesheet" href="{{asset('adminlte/component/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}">
@endsection

@push('breadcrump')
<li class="breadcrumb-item active"><a href="{{route('grade.index')}}">Grade</a></li>
<li class="breadcrumb-item active">Create</li>
@endpush


@section('content')
<div class="row">
	<div class="col-lg-8">
		<div class="row">
		    <div class="col-lg-12">
		        <div class="card card-{{ config('configs.app_theme') }} card-outline">
		            <div class="card-header" style="height:55px;">
		                <h3 class="card-title">Grade</h3>
		            </div>
		            <div class="card-body">
		                <form id="form" action="{{ route('grade.store')}}" method="post">
		                    {{ csrf_field() }}
		                    <div class="row">
		                        <div class="col-sm-6">
		                            <!-- text input -->
		                            <div class="form-group">
		                                <label>Code</label>
		                                <input type="text" class="form-control" placeholder="Code" name="code">
		                            </div>
		                        </div>
		                        <div class="col-sm-6">
		                            <div class="form-group">
		                                <label>Name</label>
		                                <input type="text" name="name" class="form-control" placeholder="Name">
		                            </div>
		                        </div>
		                    </div>
		                    <div class="row">
		                        <div class="col-sm-6">
		                            <!-- text input -->
		                            <div class="form-group">
		                                <label>Order</label>
		                                <input type="text" class="form-control" placeholder="Order" name="order" value="1"
		                                    readonly>
		                            </div>
		                        </div>
		                        <div class="col-sm-6">
		                            <div class="form-group">
		                                <label>Minimum Duration (Months)</label>
		                                <div class="row">
		                                    <div class="col-sm-4">
		                                        <select class="form-control" name="min_duration" id="min_duration">
		                                            <option value="yes">Yes</option>
		                                            <option value="no">No</option>
		                                        </select>
		                                    </div>
		                                    <div class="col-sm-8">
		                                        <input type="number" id="month" name="month" placeholder="Month"
		                                            class="form-control">
		                                    </div>
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
			<div class="col-lg-12">
		        <div class="card">
		            <div class="card-body">
		                    <div class="row">
		                        <div class="col-sm-6">
		                            <!-- text input -->
		                            <div class="form-group">
		                                <label>UMK Base on</label>
		                                <input type="text" class="form-control" id="bestsallary_id" name="bestsallary_id"
		                                    data-placeholder="Select Basic UMK Base">
		                            </div>
		                        </div>
		                        <div class="col-sm-6">
		                            <div class="form-group">
		                                <label>Basic UMK Value</label>
		                                <input type="text" name="basic_umk_value" class="form-control"
		                                    placeholder="Basic UMK Value" readonly="">
		                            </div>
		                        </div>
		                    </div>
		                    <div class="row">
		                        <div class="col-sm-6">
		                            <!-- text input -->
		                            <div class="form-group">
		                                <label>Additional Salary Type</label>
		                                <select class="form-control select2" data-placeholder="Select Additional Type"
		                                    name="additional_type" id="additional_type">
		                                    <option value=""></option>
		                                    <option value="percentage">Percentage</option>
		                                    <option value="nominal">Nominal</option>
		                                    <option value="none">None</option>
		                                </select>
		                            </div>
		                        </div>
		                        <div class="col-sm-6">
		                            <div class="form-group">
		                                <label>Additional Salary Value</label>
		                                <input type="text" id="additional_value" class="form-control"
		                                    placeholder="Additional Salary Value" name="additional_value" value="0">
		                            </div>
		                        </div>
		                    </div>
		                    <div class="row">
		                        <div class="col-sm-12">
		                            <!-- text input -->
		                            <div class="form-group">
		                                <label>Total Basic Salary</label>
		                                <input type="text" class="form-control" placeholder="Total Basic Salary"
		                                    name="basic_sallary" readonly="">
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
	</div>
	<div class="col-lg-4">
		<div class="card card-{{ config('configs.app_theme') }} card-outline">
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
							<textarea class="form-control" style="height: 100px;" name="notes" placeholder="Notes"></textarea>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-12">
						<div class="form-group">
							<label>Status</label>
							<select class="form-control select2" id="status" name="status" data-placeholder="Select Status">
								<option value=""></option>
								<option value="1">Active</option>
								<option value="0">Not Active</option>
							</select>
						</div>
					</div>
				</form>
			</div>
			<div style="height: 240px;"></div>
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
<script src="{{asset('adminlte/component/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
<script>
	$('#min_duration').select2();
	$('#additional_type').select2();
	$('#status').select2();



	function calculateBasicSalary(){
		var basicUmk = $('input[name=basic_umk_value]').val();
		var additionalType = $('#additional_type').val();
		var additionalValue = $('input[name=additional_value]').val();
		if(additionalType == 'percentage'){
			additionalValue = basicUmk * additionalValue / 100;
		}

		var basicSallary = parseFloat(basicUmk) + parseFloat(additionalValue);
		$('input[name=basic_sallary]').attr('value',basicSallary);
	}
	$(document).ready(function(){
		$('#additional_value').inputmask('decimal',{rightAlign:false});
		$(document).on('change', "#min_duration", function(){
			var value = $(this).val();
			switch(value) {
				case'no':
				$('#month').attr('readonly', true);
				break;
				default:
				$('#month').attr('readonly', false);
				break;
			}
		});
		$(document).on('change', '#additional_type', function(){
			var value = $(this).val();
			$('#additional_value').val(0);
			switch(value){
				case 'none':
				$('#additional_value').attr('readonly', true);
				break;
				default:
				$('#additional_value').attr('readonly', false);
				$('#additional_value').removeAttr('max');
				$('.invalid-feedback').addClass('d-none');
				break;
				case 'percentage':
				$('#additional_value').attr('readonly', false);
				$('#additional_value').attr('max', 100);
				break;
			}
			calculateBasicSalary();
		});
		$(document).on('change', '#increases_type', function(){
			var value = $(this).val();
			$('#increases_value').val(0);
			switch(value){
				case 'none':
				$('#increases_value').attr('readonly', true);
				break;
				default:
				$('#increases_value').attr('readonly', false);
				$('#increases_value').removeAttr('max');
				$('.invalid-feedback').addClass('d-none');
				break;
				case 'percentage':
				$('#increases_value').attr('max', 100);
				$('#increases_value').attr('readonly', false);
				break;
			}
		});
		$(document).on('keyup','#additional_value',function(){
			calculateBasicSalary();
		});
		$( "#bestsallary_id" ).select2({
			ajax: {
				url: "{{ route('basesallary.select') }}",
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
							sallary: item.sallary
						});
					});
					return {
						results: option, more: more,
					};
				},
			},
			allowClear: true,
		});
		$(document).on("change", "#bestsallary_id", function () {
			var data = $('#bestsallary_id').select2('data');
			// console.log(data)
			$('input[name=basic_umk_value]').attr('value', this.value==''?0:data.sallary);
			calculateBasicSalary();
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