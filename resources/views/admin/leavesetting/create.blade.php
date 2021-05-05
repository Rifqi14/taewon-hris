@extends('admin.layouts.app')

@section('title', 'Add Leave Setting')
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/css/fileinput.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/daterangepicker/daterangepicker.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css')}}" rel="stylesheet">
@endsection

@push('breadcrump')
<li class="breadcrumb-item"><a href="{{route('leavesetting.index')}}">Leave Setting</a></li>
<li class="breadcrumb-item active">Create</li>
@endpush

@section('content')
<form id="form" action="{{ route('leavesetting.store') }}" class="form-horizontal no-gutters" method="post"
  autocomplete="off">
  <div class="row">
    <div class="col-lg-8">
      <div class="card card-{{ config('configs.app_theme') }} card-outline">
        <div class="card-header">
          <div class="card-title" style="padding-bottom: 7px">Data Leave Setting</div>
        </div>
        <div class="card-body">
          {{ csrf_field() }}
          <div class="form-group row">
            <label class="col-sm-2 col-xs-12 col-form-label" for="leave_name">Leave Name <span
                class="text-red">*</span></label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="leave_name" name="leave_name" placeholder="Leave Name"
                required>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-sm-2 col-xs-12 col-form-label" for="parent_id">Category</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="parent_id" name="parent_id" placeholder="Category">
            </div>
          </div>
          <div class="form-group row">
            <label class="col-sm-2 col-xs-12 col-form-label" for="balance">Balance</label>
            <div class="col-sm-10">
              <div class="input-group">
                <div class="input-group-prepend">
                  <div class="input-group-text">
                    <input type="checkbox" aria-label="Checkbox for following text input" name="unlimited"
                      id="unlimited">
                    <label class="form-check-label">&nbsp;Unlimited</label>
                  </div>
                </div>
                <input type="text" class="form-control" aria-label="Text input with checkbox" id="balance"
                  name="balance" placeholder="Balance per year" required>
              </div>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-sm-2 col-xs-12 col-form-label" for="reset_time">Reset Time</label>
            <div class="col-sm-10">
              <select class="form-control select2" id="reset_time" name="reset_time">
                <option value="beginningyear">Beginning Year</option>
                <option value="joindate">Join Date</option>
                <option value="specificdate">Specific Date</option>
              </select>
            </div>
          </div>
          <div class="form-group row date-specific d-none">
            <label class="col-sm-2 col-xs-12 col-form-label" for="date">Date</label>
            <div class="col-sm-10">
              <input type="text" name="date" id="date" class="form-control datepicker" placeholder="Date">
            </div>
          </div>
          <div class="form-group row">
            <label class="col-sm-2 col-xs-12 col-form-label" for="use_time">Use Time</label>
            <div class="col-sm-10">
              <select class="form-control select2" id="use_time" name="use_time">
                <option value="joindate">Join Date</option>
                <option value="firstmonth">First Month</option>
                <option value="thirdmonth">Third Month</option>
                <option value="sixthmonth">Sixth Month</option>
                <option value="oneyear">One Year</option>
              </select>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-sm-2 col-xs-12 col-form-label" for="department">Department</label>
            <div class="col-sm-10">
              <div class="input-group">
                <div class="input-group-prepend">
                  <div class="input-group-text">
                    <input type="checkbox" aria-label="Checkbox for following text input" id="all" name="all">
                  </div>
                </div>
                <input type="text" name="department" id="department" class="form-control" placeholder="Department">
              </div>
              <small id="departmentHelp" class="form-text text-muted">Check at checkbox to select all
                department.</small>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-sm-2 col-xs-12 col-form-label" for="label_color">Label Color</label>
            <div class="col-sm-10">
              <div class="input-group my-colorpicker2">
                <input type="text" class="form-control" value="#000" name="label_color" id="label_color">
                <div class="input-group-append">
                  <span class="input-group-text input-group-addon">
                    <i></i>
                  </span>
                </div>
              </div>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-sm-2 col-xs-12" for="balance">Description <span class="text-red">*</span></label>
            <div class="col-sm-10">
              <div class="form-check form-check-inline">
                <input class="form-check-input i-checks" type="radio" name="description" id="description1" value="1"
                  checked>
                <label class="form-check-label" for="description1">&emsp;Paid Leave</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input i-checks" type="radio" name="description" id="description2" value="0">
                <label class="form-check-label" for="description2">&emsp;Unpaid Leave</label>
              </div>
            </div>
          </div>
        </div>
        <div class="overlay d-none">
          <i class="fa fa-2x fa-sync-alt fa-spin"></i>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card card-{{ config('configs.app_theme') }} card-outline">
        <div class="card-header">
          <div class="card-title">Others</div>
          <div class="pull-right card-tools">
            <button form="form" type="submit" class="btn btn-sm btn-{{ config('configs.app_theme') }}" title="Save"><i
                class="fa fa-save"></i></button>
            <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i
                class="fa fa-reply"></i></a>
          </div>
        </div>
        <div class="card-body">
          {{ csrf_field() }}
          <div class="row">
            <div class="col-sm-12">
              <!-- text input -->
              <div class="form-group">
                <label>Note</label>
                <textarea class="form-control" id="note" name="note" placeholder="Notes" rows="4"></textarea>
              </div>
            </div>
          </div>
          <div class="row">
						<div class="col-sm-12">
							<div class="form-group">
								<label>Coordinate Slip</label>
								<select name="coordinate[]" id="coordinate" multiple="multiple" class="form-control select2" Placeholder="Coordinate" style="width: 100%" aria-hidden="true">
									<option value=""></option>
									<option value="3.3">3.3</option>
									<option value="3.4">3.4</option>
									<option value="3.5">3.5</option>
									<option value="3.6">3.6</option>
									<option value="5.1">5.1</option>
									<option value="5.2">5.2</option>
									<option value="5.3">5.3</option>
								</select>
							</div>
						</div>
					</div>
          <div class="row">
            <div class="col-sm-12">
              <div class="form-group">
                <label>Status</label>
                <select name="status" id="status" class="form-control select2" style="width: 100%" aria-hidden="true">
                  <option value="1">Active</option>
                  <option value="0">Non-Active</option>
                </select>
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
</form>
@endsection

@push('scripts')
<script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-fileinput/js/fileinput.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.js')}}"></script>
<script src="{{asset('adminlte/component/daterangepicker/moment.min.js')}}"></script>
<script src="{{asset('adminlte/component/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js')}}"></script>
<script type="text/javascript">
  $(document).ready(function() {
    $('.datepicker').daterangepicker({
			singleDatePicker: true,
			timePicker: false,
			locale: {
				format: 'DD/MM/YYYY'
			}
    });
    $('.my-colorpicker2').each(function () {
      $(this).colorpicker();
    });
    $('#reset_time').change(function(){
      if ($(this).val() == 'specificdate') {
        $('.date-specific').removeClass('d-none');
      } else {
        $('.date-specific').addClass('d-none');
      }
    });
    $('.i-checks').iCheck({
      checkboxClass: 'icheckbox_square-green',
      radioClass: 'iradio_square-green',
    });
    $('.select2').select2();
    $('#department').select2({
      multiple: true,
      ajax: {
				url: "{{route('department.select')}}",
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
    });
    $('#parent_id').select2({
      ajax: {
        url: "{{route('leavesetting.select')}}",
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
              text: `${item.leave_name}`
            });
          });
          return {
            results: option, more: more,
          };
        },
      },
      allowClear: true,
    });
    $('#unlimited').click(function() {
      if ($(this).prop("checked") == true) {
        $('#balance').prop("disabled", true);
        $('#balance').prop("required", false);
      } else {
        $('#balance').prop("disabled", false);
        $('#balance').prop("required", true);
      }
    });
    $('#all').click(function() {
      if ($(this).prop("checked") == true) {
        $("#department").select2('data', null);
        $('#department').prop("disabled", true);
      } else {
        $('#department').prop("disabled", false);
      }
    });
    $(function() {
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
    })
  });
</script>
@endpush