@extends('admin.layouts.app')

@section('title', 'Attendance Machine Create')

@push('breadcrump')
<li class="breadcrumb-item"><a href="{{ route('attendancemachine.index') }}">Attendance Machine</a></li>
<li class="breadcrumb-item active">Create</li>
@endpush

@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="card card-{{ config('configs.app_theme') }} card-outline">
      <div class="card-header">
        <h3 class="card-title">Attendance Machine Data</h3>
        <div class="pull-right card-tools">
          <button type="submit" form="form" class="btn btn-sm btn-{{ config('configs.app_theme') }} text-white" title="Save"><i class="fa fa-save"></i></button>
          <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Back"><i class="fa fa-reply"></i></a>
        </div>
      </div>
      <div class="card-body">
        <form action="{{ route('attendancemachine.store') }}" method="post" autocomplete="off" id="form">
          @csrf
          <div class="row">
            <div class="col-sm-6">
              <div class="form-group">
                <label for="deviceSN" class="control-label">Device Serial Number <b class="text-danger">*</b></label>
                <input type="text" name="deviceSN" id="deviceSN" class="form-control" placeholder="Device Serial Number" required>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <label for="pointName" class="control-label">Point Name <b class="text-danger">*</b></label>
                <select name="pointName" id="pointName" class="form-control select2" aria-placeholder="Point Name" required>
                  <option value="MASUK">Masuk</option>
                  <option value="KELUAR">Keluar</option>
                </select>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
@push('scripts')
<script src="{{ asset('adminlte/component/validate/jquery.validate.min.js') }}"></script>
<script>
  $(function(){
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
				} else if(element.parent('.input-group').length) {
					error.insertAfter(element.parent());
				} else if (element.attr('type') == 'checkbox') {
					error.insertAfter(element.parent());
				} else{
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