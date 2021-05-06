@extends('admin.layouts.app')

@section('title', 'Work Group Combination')
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
@endsection

@push('breadcrump')
<li class="breadcrumb-item active"><a href="{{route('workgroup.index')}}">Work Group Combination</a></li>
<li class="breadcrumb-item active">Create</li>
@endpush


@section('content')
<div class="row">
	<div class="col-lg-8 pb-3">
		<div class="card card-{{ config('configs.app_theme') }} card-outline h-100">
			<div class="card-header">
				<h3 class="card-title" style="padding-bottom: 12px">Work Group Combination Data</h3>
			</div>
			<div class="card-body">
				<form id="form" action="{{ route('workgroup.update', ['id'=>$workgroup->id]) }}" class="form-horizontal"
					method="post" autocomplete="off">
					{{ csrf_field() }}
					{{ method_field('put') }}
					<div class="row">
						<div class="col-sm-6">
							<!-- text input -->
							<div class="form-group">
								<label>Work Group</label>
								<input type="text" class="form-control" name="workgroup_id" id="workgroup_id" placeholder="Work Group">
							</div>
						</div>
						<div class="col-sm-6">
							<!-- text input -->
							<div class="form-group">
								<label>Combination Code</label>
								<input type="text" class="form-control" name="code" id="code" placeholder="Combination Code"
									value="{{ $workgroup->code }}">
							</div>
						</div>
						{{-- <div class="col-sm-6">
							<!-- text input -->
							<div class="form-group">
								<label>Penalty Type</label>
								<select name="penalty" id="penalty" class="form-control select2" style="width: 100%" aria-hidden="true">
									@foreach (config('enums.penalty_type') as $key => $value)
									<option value="{{ $key }}" @if ($workgroup->penalty == $key) selected @endif>{{ $value }}</option>
									@endforeach
								</select>
							</div>
						</div> --}}
					</div>
					<div class="row">
						
						<div class="col-sm-6">
							<div class="form-group">
								<label>Combination Name</label>
								<input type="text" class="form-control" name="name" id="name" placeholder="Combination Name" value="{{ $workgroup->name }}">
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
				<h3 class="card-title">Other</h3>
				<div class="pull-right card-tools">
					<button form="form" type="submit" class="btn btn-sm btn-{{ config('configs.app_theme')}}" title="Simpan"><i
							class="fa fa-save"></i></button>
					<a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i
							class="fa fa-reply"></i></a>
				</div>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-sm-12">
						<!-- text input -->
						<div class="form-group">
							<label>Description</label>
							<textarea class="form-control" id="description" name="description"
								placeholder="Notes">{{ $workgroup->description }}</textarea>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-12">
						<div class="form-group">
							<label>Status</label>
							<select name="status" id="status" class="form-control select2" style="width: 100%" aria-hidden="true">
								<option @if($workgroup->status == 1) selected @endif value="1">Active</option>
								<option @if($workgroup->status == 0) selected @endif value="0">Non-Active</option>
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
<div class="row">
	<div class="col-lg-12">
		<div class="card card-{{ config('configs.app_theme') }} card-outline h-100" id="workgroup_allowance">
			<div class="card-header">
				<h3 class="card-title">Allowance</h3>
			</div>
			<div class="card-body">
				<table class="table table-striped table-bordered datatable" style="width:100%" id="table-allowances">
					<thead>
						<tr>
							<th width="10">No</th>
							<th width="200">Allowance</th>
							<th width="500">Category</th>
							<th width="150">Value</th>
							<th width="50">Default</th>
							<th width="100">Action</th>
						</tr>
					</thead>
				</table>
			</div>
			<div class="overlay d-none">
				<i class="fa fa-refresh fa-spin"></i>
			</div>
		</div>
	</div>
</div>

{{-- Modal Allowance --}}
<div class="modal fade" id="add_allowance" tabindex="-1" role="dialog" aria-hidden="true" role="dialog"
	aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="overlay-wrapper">
				<div class="modal-header">
					<h4 class="modal-title">Add Allowance</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form id="form_allowance" class="form-horizontal" method="post" autocomplete="off">
						<div class="row">
							<input type="hidden" name="allowance_id">
							<div class="col-md-6">
								<div class="form-group">
									<label for="type" class="control-label">Type</label>
									<select class="form-control" data-placeholder="Type" name="type" id="type">
										<option value="percentage">Percentage</option>
										<option value="nominal">Nominal</option>
										<option value="automatic">Automatic</option>
									</select>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label for="value" class="control-label">Value</label>
									<input type="text" class="form-control" id="value" name="value" placeholder="Value" required>
								</div>
							</div>
							{{ csrf_field() }}
							<input type="hidden" name="_method" />
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<button form="form_allowance" type="submit" class="btn btn-sm btn-{{ config('configs.app_theme')}} text-white"
						title="Simpan"><i class="fa fa-save"></i></button>
				</div>
				<div class="overlay d-none">
					<i class="fa fa-2x fa-sync-alt fa-spin"></i>
				</div>
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
			@if ($workgroup->workgroupmaster_id)
			$("#workgroup_id").select2('data',{id:{{$workgroup->workgroupmaster_id}},text:'{{$workgroup->workgroupmaster->name}}'}).trigger('change');
			@endif
			$(document).on("change", "#workgroup_id", function () {
				if (!$.isEmptyObject($('#form').validate().submitted)) {
					$('#form').validate().form();
				}
			});
			$('.select2').select2();

			$('#form_allowance').validate({
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
					url:$('#form_allowance').attr('action'),
					method:'post',
					data: new FormData($('#form_allowance')[0]),
					processData: false,
					contentType: false,
					dataType: 'json',
					beforeSend:function(){
						$('.overlay').removeClass('hidden');
					}
					}).done(function(response){
						$('.overlay').addClass('hidden');
						if(response.status){
							$('#add_allowance').modal('hide');
							dataTableAllowance.draw();
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
						$('.overlay').addClass('hidden');
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

			dataTableAllowance = $('.datatable').DataTable({
				stateSave:true,
				processing: true,
				serverSide: true,
				filter:false,
				info:false,
				lengthChange:false,
				responsive: true,
				paginate:false,
				order: [[ 5, "asc" ]],
				ajax: {
						url: "{{route('workgroupallowance.read')}}",
						type: "GET",
						data:function(data){
							data.workgroup_id = {{ $workgroup->id }}
						}
				},
				columnDefs:[
					{ orderable: false,targets:[0] },
					{ className: "text-right", targets: [0] },
					{ className: "text-center", targets: [3,4,5] },
					{ render: function ( data, type, row ) {
						if(row.type == 'nominal'){
							return 'Rp. ' + row.value
						}else if(row.type == 'percentage'){
							return row.value + '%'
						}else{
							return row.value
						}
					},
					targets: [3] },
					{ render: function ( data, type, row ) {
						var html = '<input type="checkbox"';
						if (data == 1) {
						html += ` checked class="updateallowance" name="default" value="${row.id}">`;
						} else {
						html += ` class="updateallowance" name="default" value="${row.id}">`;
						}
						return html
					},
					targets: [4] },
					{ render: function ( data, type, row ) {
						return `<div class="dropdown">
						<button class="btn  btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
						<i class="fa fa-bars"></i>
						</button>
						<ul class="dropdown-menu dropdown-menu-right">
						<li><a class="dropdown-item editallowance" href="#" data-id="${row.id}"><i class="fas fa-pencil-alt mr-2"></i> Edit</a></li>
						</ul></div>`
						},
					targets: [5] }
				],
				columns: [
					{ data: "no" },
					{ data: "allowance" },
					{ data: "category" },
					{ data: "value" },
					{ data: "is_default" },
					{ data: 'id' }
				]
			});

			$(document).on('click','.editallowance',function(){
				var id = $(this).data('id');
				$.ajax({
					url:`{{url('admin/workgroupallowance')}}/${id}/edit`,
					method:'GET',
					dataType:'json',
					beforeSend:function(){
						$('#box-menu .overlay').removeClass('d-none');
					},
				}).done(function(response){
					$('#box-menu .overlay').addClass('d-none');
					if(response.status){
						$('#add_allowance .modal-title').html('Ubah Allowance');
						$('#add_allowance').modal('show');
						$('#form_allowance')[0].reset();
						$('#form_allowance .invalid-feedback').each(function () { $(this).remove(); });
						$('#form_allowance .form-group').removeClass('has-error').removeClass('has-success');
						$('#form_allowance input[name=_method]').attr('value','PUT');
						$('#form_allowance input[name=allowance_id]').attr('value',{{$workgroup->id}});
						$('#form_allowance select[name=type]').select2('val',response.data.type);
						$('#form_allowance input[name=value]').attr('value',response.data.value);
						$('#form_allowance').attr('action',`{{url('admin/workgroupallowance/')}}/${response.data.id}`);
					}          
				}).fail(function(response){
					var response = response.responseJSON;
					$('#box-menu .overlay').addClass('d-none');
					$.gritter.add({
						title: 'Error!',
						text: response.message,
						class_name: 'gritter-error',
						time: 1000,
					});
				})	
			});

			$(document).on('change','.updateallowance', function () {
				$.ajax({
					url: "{{url('admin/workgroup/update_allowances')}}",
					data: {
						_token: "{{ csrf_token() }}",
						id: this.value,
						is_default: this.checked ? 1 : 0,
											_method: 'PUT'
					},
					type: 'POST',
					dataType: 'json',
					beforeSend: function () {
						$('#workgroup_allowance .overlay').removeClass('d-none');
					}
				}).done(function (response) {
					$('#workgroup_allowance .overlay').addClass('d-none');
					if (response.status) {
						$.gritter.add({
							title: 'Success!',
							text: response.message,
							class_name: 'gritter-success',
							time: 1000,
						});
					} else {
						$.gritter.add({
							title: 'Warning!',
							text: response.message,
							class_name: 'gritter-warning',
							time: 1000,
						});
					}
					return;

				}).fail(function (response) {
					var response = response.responseJSON;
					$('#workgroup_allowance  .overlay').addClass('d-none');
					$.gritter.add({
						title: 'Error!',
						text: response.message,
						class_name: 'gritter-error',
						time: 1000,
					});
				})
			});
			$("#type").select2();

			$(document).on('change', '#type', function(){
			var value = $(this).val();
			$('#value').val(0);
			switch(value){
				case 'automatic':
				$('#value').val("Automatic");
				break;
				default:
				$('#value').attr('readonly', false);
				$('#value').removeAttr('max');
				$('.invalid-feedback').addClass('d-none');
				break;
				case 'percentage':
				$('#value').attr('readonly', false);
				$('#value').attr('max', 100);
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