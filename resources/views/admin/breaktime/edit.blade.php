@extends('admin.layouts.app')

@section('title', 'Break Time')
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/daterangepicker/daterangepicker.css')}}" rel="stylesheet">
@endsection

@push('breadcrump')
<li class="breadcrumb-item active"><a href="{{route('breaktime.index')}}">Break Time</a></li>
<li class="breadcrumb-item active">Edit</li>
@endpush


@section('content')
<form id="form" action="{{ route('breaktime.update',['id'=>$breaktime->id])}}" method="post">
	{{ csrf_field() }}
<div class="row">
	<div class="col-lg-8">
		<div class="card card-{{ config('configs.app_theme')}} card-outline">
			<div class="card-header" style="height: 55px;">
				<h3 class="card-title">Update Break Time</h3>
			</div>
			<div class="card-body">
				@method('put')
				<div class="row">
					<div class="col-sm-6">
						<div class="form-group">
							<label>Break Time <b class="text-danger">*</b></label>
							<input type="text" value="{{ $breaktime->break_time }}" name="break_time" class="form-control" placeholder="Break Time" required>
						</div>
					</div>
                    <div class="col-sm-6">
						<div class="form-group">
							<label>Workgroup Combination <b class="text-danger">*</b></label>
							<input type="text" value="" multiple="multiple" name="workgroup[]" class="form-control select2" placeholder="Workgroup Combination" id="workgroup">
						</div>
					</div>
				</div>
                <div class="row">
					<div class="col-sm-6">
						<div class="form-group">
							<label>Start Time <b class="text-danger">*</b></label>
							<input value="{{ $breaktime->start_time }}" name="start_time" class="form-control timepicker" placeholder="Start Time" required>
						</div>
					</div>
                    <div class="col-sm-6">
						<div class="form-group">
							<label>Finish Time <b class="text-danger">*</b></label>
							<input value="{{ $breaktime->finish_time }}" name="finish_time" class="form-control timepicker" placeholder="Finish Time" required>
						</div>
					</div>
				</div>
                <div style="height: 23px;"></div>
			</div>
		</div>
	</div>
	<div class="col-lg-4">
      <div class="card card-{{ config('configs.app_theme') }} card-outline">
        <div class="card-header">
          <h3 class="card-title">Other</h3>
          <div class="pull-right card-tools">
            <button form="form" type="submit" class="btn btn-sm btn-{{ config('configs.app_theme') }}" title="Save"><i
                class="fa fa-save"></i></button>
            <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Back"><i
                class="fa fa-reply"></i></a>
          </div>
        </div>
        <div class="card-body">
          <form role="form">
            <div class="row">
              <div class="col-sm-12">
                <!-- text input -->
                <div class="form-group">
                  <label>Notes</label>
                  <textarea class="form-control" name="notes" placeholder="Notes">{{$breaktime->notes}}</textarea>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-12">
                <div class="form-group">
                  <label>Status <b class="text-danger">*</b></label>
                  <select name="status" id="status" class="form-control select2" data-placeholder="Select Status">
                    <option value="1" @if($breaktime->status == '1') selected @endif>Aktif</option>
                    <option value="0" @if($breaktime->status == '0') selected @endif>Tidak Aktif</option>
                  </select>
                </div>
              </div>
            </div>
          </form>
        </div>
        <div class="overlay d-none">
          <i class="fa fa-2x fa-sync-alt fa-spin"></i>
        </div>
        </form>
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
		$( "#workgroup" ).select2({
            multiple: true,
            tags: true,
            ajax: {
              url: "{{route('workgroup.select')}}",
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
          $(document).on("change", "#workgroup", function () {
            if (!$.isEmptyObject($('#form').validate().submitted)) {
              $('#form').validate().form();
            }
          });

		  $.ajax({
          url:  "{{route('breaktime.multi')}}",
          type: 'GET',
          dataType: 'json',
          data: {{ $breaktime->id }},
          success: function (result) {                  
			//    console.log(result.results);
		var data = [];
           $.each(result.results, function(key, val){
			//    console.log(val.workgroup_id);
			data.push({ id:val.workgroup_id,text:val.workgroup_name });
				// $("#workgroup").append('<option value="'+val.workgroup_id+'" selected>'+val.workgroup_name+'</option>');
            });
				$("#workgroup").select2('data',data).trigger('change');    

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