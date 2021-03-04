@extends('admin.layouts.app')

@section('title', 'Tambah Grup Pelanggan')
@push('breadcrump')
    <li class="breadcrumb-item"><a href="{{route('customergroup.index')}}">UOMCategori</a></li>
    <li class="breadcrumb-item active">Tambah</li>
@endpush
@section('content')
<div class="card card-default">
  <div class="card-header">
    <h3 class="card-title">Tambah Grup Pelanggan</h3>
    <!-- tools card -->
    <div class="pull-right card-tools">
      <button form="form" type="submit" class="btn btn-sm btn-danger" title="Simpan"><i class="fa fa-save"></i></button>
      <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i class="fa fa-reply"></i></a>
    </div>
    <!-- /. tools -->
  </div>
  <div class="card-body">
    <form id="form"  action="{{route('customergroup.store')}}" method="post" autocomplete="off">
        {{ csrf_field() }}
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Code <b class="text-danger">*</b></label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="customergroup_code" id="customergroup_code" required placeholder="Code">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Name <b class="text-danger">*</b></label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="customergroup_name" id="customergroup_name" required placeholder="Name">
            </div>
        </div>
    </form>
</div>
  <div class="overlay d-none">
    <i class="fa fa-refresh fa-spin"></i>
  </div>
</div>
@endsection

@push('scripts')
<script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
<script>
    $(document).ready(function(){
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
					beforeSend: function () {
                        $('.overlay').removeClass('hidden');
                    }
				}).done(function(response){
                    $('.overlay').addClass('hidden');
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
                    var response = response.responseJSON;
                    $('.overlay').addClass('hidden');
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
