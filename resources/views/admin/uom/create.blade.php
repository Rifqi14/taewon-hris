@extends('admin.layouts.app')

@section('title', 'Create UOM')
@section('stylesheets')
@endsection

@push('breadcrump')
<li class="breadcrumb-item"><a href="{{route('uom.index')}}">UOM</a></li>
<li class="breadcrumb-item active">Tambah</li>
@endpush

@section('content')
<div class="wrapper wrapper-content">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tambah UOM</h3>
                    <!-- tools box -->
                    <div class="pull-right card-tools">
                        <button form="form" type="submit" class="btn btn-sm btn-danger" title="Simpan"><i
                                class="fa fa-save"></i></button>
                        <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i
                                class="fa fa-reply"></i></a>
                    </div>
                    <!-- /. tools -->
                </div>
                <div class="card-body">
                    <form id="form" action="{{route('uom.store')}}" method="post" autocomplete="off">
                        {{ csrf_field() }}
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Name <b class="text-danger">*</b></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="name" id="uom_name" placeholder="Name" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Category <b class="text-danger">*</b></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="uomcategory_id" name="uomcategory_id" data-placeholder="Select Category" required />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Type <b class="text-danger">*</b></label>
                            <div class="col-sm-6">
                                <select class="form-control select2" id="uom_type" required name="type">
                                    <option value="reference">Reference</option>
                                    <option value="biggerthan">Bigger Than</option>
                                    <option value="smallerthan">Smaller Than</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Ratio <b class="text-danger">*</b></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="ratio" id="uom_ratio" placeholder="Ratio" value="1" readonly required>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="overlay d-none">
                    <i class="fa fa-refresh fa-spin"></i>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
<script>
    $(document).ready(function(){
        $("input[name=ratio]").inputmask('decimal', {
			rightAlign: false
		});
        $('.select2').select2();
        $( "#uomcategory_id" ).select2({
			ajax: {
				url: "{{route('uomcategory.select')}}",
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
        $(document).on("change", "#uomcategory_id", function () {
			if (!$.isEmptyObject($('#form').validate().submitted)) {
				$('#form').validate().form();
			}
		});
        $(document).on("change", "#uom_type", function () {
			if(this.value == 'reference'){
                $('input[name=ratio]').prop('readonly',true);
                $('input[name=ratio]').val(1);
            }
            else{
                $('input[name=ratio]').prop('readonly',false);
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
