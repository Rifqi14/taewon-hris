@extends('admin.layouts.app')

@section('title', 'Edit Account')

@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/css/fileinput.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.css')}}" rel="stylesheet">
@endsection

@push('breadcrump')
<li class="breadcrumb-item active"><a href="{{route('account.index')}}">Account</a></li>
<li class="breadcrumb-item active">Edit</li>
@endpush

@section('content')
<div class="row">
	<div class="col-lg-12">
		<div class="card card-{{ config('configs.app_theme') }} card-outline">
			<div class="card-header">
				<h3 class="card-title">General Information</h3>
				<div class="pull-right card-tools">
					<button form="form" type="submit" class="btn btn-sm btn-{{ config('configs.app_theme') }}" title="Simpan"><i
							class="fa fa-save"></i></button>
					<a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i
							class="fa fa-reply"></i></a>
				</div>
			</div>
			<div class="card-body">
				<form id="form" action="{{ route('account.update', ['id'=>$account->id]) }}" class="form-horizontal"
					method="post" autocomplete="off">
					{{ csrf_field() }}
					{{ method_field('put') }}
					<div class="box-body">
						<div class="row">
							<div class="col-sm-6">
								<div class="form-group">
									<label>Account Category</label>
									<select name="acc_category" id="acc_category" class="form-control select2" style="width: 100%"
										aria-hidden="true">
										@foreach (config('enums.account_category') as $key=>$value)
										<option @if ($key==$account->acc_category) selected @endif value="{{ $key }}">{{ $value }}</option>
										@endforeach
									</select>
								</div>
							</div>
							<div class="col-sm-6">
								<!-- text input -->
								<div class="form-group">
									<label>Account Parent</label>
									<input type="text" class="form-control" placeholder="Account Parent" id="parent_id" name="parent_id"
										value="{{ $account->parent_id }}">
								</div>
							</div>
						</div>
						<div class="row account-section">
							<div class="col-sm-6">
								<!-- text input -->
								<div class="form-group">
									<label>Account Code</label>
									<input type="text" class="form-control" placeholder="Account Code" id="acc_code" name="acc_code"
										value="{{ $account->acc_code }}" required>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label>Account Name</label>
									<input type="text" class="form-control" placeholder="Account Name" id="acc_name" name="acc_name"
										value="{{ $account->acc_name }}" required>
								</div>
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
<script>
	$(document).ready(function() {
    $('#acc_category').select2();
		$('#parent_id').select2({
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
		@if ($account->parent_id)
		$("#parent_id").select2('data',{id:{{$account->parent_id}},text:'{{$account->parent->acc_name}}'}).trigger('change');
		@endif
		$(document).on("change", "#parent_id", function () {
			if (!$.isEmptyObject($('#form').validate().submitted)) {
				$('#form').validate().form();
			}
		});
		$('#form').validate({
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
						$('.overlay').removeClass('hidden');
					}
				}).done(function(response){
					$('.overlay').addClass('hidden');
					if(response.status){
						document.location = response.results;
					} else {
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
		})
	});
</script>
@endpush