@extends('admin.layouts.app')

@section('title', 'Sallary & Allowance')
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/css/fileinput.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.css')}}" rel="stylesheet">
@endsection

@push('breadcrump')
<li class="breadcrumb-item active"><a href="{{route('employees.index')}}">Sallary & Allowance</a></li>
<li class="breadcrumb-item active">Create</li>
@endpush


@section('content')
<div class="row">
	<div class="col-lg-12">
		<div class="card">
			<div class="card-header">
				<h3 class="card-title">Employee Data</h3>
			</div>
			<div class="row">
				<div class="col-8">
					<div class="card-body">
						<form role="form">
							<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
										<label>Name</label>
										<input type="text" class="form-control" placeholder="Name" value="Bagus Mertha P">
									</div>
								</div>
								<div class="col-sm-6">
									<!-- text input -->
									<div class="form-group">
										<label>ID</label>
										<input type="text" class="form-control" placeholder="ID" value="B2310002">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-6">
									<!-- text input -->
									<div class="form-group">
										<label>Position</label>
										<input type="text" class="form-control" placeholder="Position" value="Manajer IT">
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label>Department</label>
										<input type="text" class="form-control" placeholder="Department" value="Dept IT">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-6">
									<!-- text input -->
									<div class="form-group">
										<label>Work Group Combination</label>
										<input type="text" class="form-control" placeholder="Work Group Combination" value="Pegawai Tetap - All In">
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label>Grade</label>
										<input type="text" class="form-control" placeholder="Grade" value="4A">
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
				<div class="col-4">
					<div class="card-body">
						<form role="form">
							<div class="row">
								<div class="col-sm-12">
									<!-- text input -->
									<div class="form-group">
										<label for="photo">Photo </label>
										<input type="file" class="form-control" name="photo" id="photo" accept="image/*" required/>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-lg-12">
		<div class="card card-danger card-outline card-outline-tabs">
			<div class="card-header p-0 border-bottom-0">
				<ul class="nav nav-tabs">
					<li class="nav-item"><a class="nav-link active" href="#sallary" data-toggle="tab">Sallary</a></li>
					<li class="nav-item"><a class="nav-link" href="#allowance" data-toggle="tab">Allowance</a></li>
				</ul>
			</div>
			<div class="tab-content">
				<div class="tab-pane active" id="sallary">
					<div class="card-body">
						<table  class="table table-bordered table-striped">
							<thead>
								<tr>
									<th style="text-align:center" width="10">No</th>
									<th width="150">Amount</th>
									<th width="300" >Description</th>
									<th width="150" >Update By</th>
									<th width="150">Update Time</th>
									<th width="100" >Action</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>1</td>
									<td>Rp 4.028.000</td>
									<td>Default System</td>
									<td>System</td>
									<td>01/01/2019 08:00:23</td>
									<td>
										<button class="btn"><i class="fas fa-search"></i></button>
										<button class="btn"><i class="fas fa-trash"></i></button>
									</td>
								</tr>
								<tr>
									<td>2</td>
									<td>Rp 5.500.000</td>
									<td>Adjustment by HR</td>
									<td>Bagus Mertha</td>
									<td>21/03/2019 09:20:00</td>
									<td>
										<button class="btn"><i class="fas fa-search"></i></button>
										<button class="btn"><i class="fas fa-trash"></i></button>
									</td>
								</tr>
								<tr>
									<td>3</td>
									<td>Rp 6.550.000</td>
									<td>Yearly Sallary Increase (10%/Year)</td>
									<td>System</td>
									<td>01/01/2020 09:20:00</td>
									<td>
										<button class="btn"><i class="fas fa-search"></i></button>
										<button class="btn"><i class="fas fa-trash"></i></button>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
				<div class="tab-pane" id="allowance">
					<div class="card-body">
						<table class="table table-striped table-bordered datatable" style="width:100%">
							<thead>
								<tr>
									<th width="10">No</th>
									<th width="200">Allowance</th>
									<th width="300">Category</th>
									<th width="100">Value</th>
									<th width="100">Default</th>
									<th width="100">Action</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>1</td>
									<td>Iuran Pensiunan/THT/JHT</td>
									<td>Iuran Pensiunan/THT/JHT dibayar pekerja</td>
									<td>Rp 2.000.000</td>
									<td>Default</td>
									<td>
										<button class="btn"><i class="fas fa-search"></i></button>
										<button class="btn"><i class="fas fa-trash"></i></button>
									</td>
								</tr>
								<tr>
									<td>2</td>
									<td>Tunjangan Pensiunan/THT/JHT</td>
									<td>Tunjangan Iuran Pensiunan/THT/JHT dibayar pemberi kerja</td>
									<td>Rp 500.000</td>
									<td>Updated</td>
									<td>
										<button class="btn"><i class="fas fa-search"></i></button>
										<button class="btn"><i class="fas fa-trash"></i></button>
									</td>
								</tr>
								<tr>
									<td>3</td>
									<td>Potongan Premi Kesehatan</td>
									<td>Premi asuransi kesehatan dibayar pekerja</td>
									<td>Rp 0</td>
									<td>Default</td>
									<td>
										<button class="btn"><i class="fas fa-search"></i></button>
										<button class="btn"><i class="fas fa-trash"></i></button>
									</td>
								</tr>
								<tr>
									<td>4</td>
									<td>Tunjangan Premi Kesehatan</td>
									<td>Premi asuransi kesehatan dibayar pemberi kerja</td>
									<td>Rp 0</td>
									<td>Default</td>
									<td>
										<button class="btn"><i class="fas fa-search"></i></button>
										<button class="btn"><i class="fas fa-trash"></i></button>
									</td>
								</tr>
								<tr>
									<td>5</td>
									<td>Tunjangan Hari Raya</td>
									<td>Tatiem,Bonus,Rapel dan THR</td>
									<td>Automatic</td>
									<td>Default</td>
									<td>
										<button class="btn"><i class="fas fa-search"></i></button>
										<button class="btn"><i class="fas fa-trash"></i></button>
									</td>
								</tr>
								<tr>
									<td>6</td>
									<td>Tunjangan Program JKK</td>
									<td>Tunjangan JKK,JKM</td>
									<td>0.24%</td>
									<td>Default</td>
									<td>
										<button class="btn"><i class="fas fa-search"></i></button>
										<button class="btn"><i class="fas fa-trash"></i></button>
									</td>
								</tr>
								<tr>
									<td>7</td>
									<td>Tunjangan Program JKM</td>
									<td>Tunjangan JKK,JKM</td>
									<td>0.3%</td>
									<td>Default</td>
									<td>
										<button class="btn"><i class="fas fa-search"></i></button>
										<button class="btn"><i class="fas fa-trash"></i></button>
									</td>
								</tr>
								<tr>
									<td>8</td>
									<td>Tunjangan Jabatan</td>
									<td>Tunjangan lainnya, uang lembur, dsb</td>
									<td>Rp 2.500.000</td>
									<td>Updated</td>
									<td>
										<button class="btn"><i class="fas fa-search"></i></button>
										<button class="btn"><i class="fas fa-trash"></i></button>
									</td>
								</tr>
								<tr>
									<td>9</td>
									<td>Tunjangan Makan</td>
									<td>Tunjangan lainnya, uang lembur, dsb</td>
									<td>Rp 500.000</td>
									<td>Updated</td>
									<td>
										<button class="btn"><i class="fas fa-search"></i></button>
										<button class="btn"><i class="fas fa-trash"></i></button>
									</td>
								</tr>
								<tr>
									<td>10</td>
									<td>Tunjangan PPh</td>
									<td>Tunjangan PPh</td>
									<td>Rp 0</td>
									<td>Default</td>
									<td>
										<button class="btn"><i class="fas fa-search"></i></button>
										<button class="btn"><i class="fas fa-trash"></i></button>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<button class="btn btn-success">Save</button>
		<button class="btn btn-secondary">Cancel</button>
		<br><br>
	</div>

</div>
@endsection
@push('scripts')
<script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-fileinput/js/fileinput.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.js')}}"></script>
<script src="{{asset('adminlte/component/dataTables/js/datatables.min.js')}}"></script>
<script src="{{asset('assets/js/plugins/bootbox/bootbox.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
<script>
	$(document).ready(function(){
		$('.datatable').dataTable({
			"searching" : false
		});
		$( "#parent" ).select2({
			ajax: {
				url: "{{route('productcategory.select')}}",
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
		$(document).on("change", "#parent", function () {
			if (!$.isEmptyObject($('#form').validate().submitted)) {
				$('#form').validate().form();
			}
		});

		$("#photo").fileinput({
			browseClass: "btn btn-danger",
			showRemove: false,
			showUpload: false,
			allowedFileExtensions: ["png"],
			dropZoneEnabled: false,
			theme:'explorer-fas'
		});
		$(document).on("change", "#photo", function () {
			if (!$.isEmptyObject($('#form').validate().submitted)) {
				$('#form').validate().form();
			}
		});

		$("#picture").fileinput({
			browseClass: "btn btn-danger",
			showRemove: false,
			showUpload: false,
			allowedFileExtensions: ["png", "jpg", "jpeg"],
			dropZoneEnabled: false,

			theme:'explorer-fas'
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