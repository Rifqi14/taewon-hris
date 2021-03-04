@extends('admin.layouts.app')

@section('title', 'Add Leave Balance')
@section('stylesheets')
<link href="{{asset('adminlte/component/dataTables/css/datatables.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/css/fileinput.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/daterangepicker/daterangepicker.css')}}" rel="stylesheet">
@endsection

@push('breadcrump')
<li class="breadcrumb-item"><a href="{{route('leavebalance.index')}}">Leave Balance</a></li>
<li class="breadcrumb-item active">Create</li>
@endpush

@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="card card-{{ config('configs.app_theme') }} card-outline">
      <div class="card-header">
        <div class="card-title">Data Leave Balance</div>
        <div class="pull-right card-tools">
          <button form="form" type="submit" class="btn btn-sm btn-{{ config('configs.app_theme') }}" title="Save"><i
              class="fa fa-save"></i></button>
          <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i
              class="fa fa-reply"></i></a>
        </div>
      </div>
      <div class="card-body">
        <form id="form" action="{{ route('leavebalance.store') }}" class="form-horizontal" method="post"
          autocomplete="off">
          {{ csrf_field() }}
          <div class="form-group row">
            <label class="col-md-2 col-xs-12 col-form-label" for="leave_type">Leave Type <span
                class="text-red">*</span></label>
            <div class="col-sm-6">
              <input type="text" class="form-control" id="leave_type" name="leave_type" placeholder="Leave Type"
                required>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-md-2 col-xs-12 col-form-label" for="balance">Amount per year <span
                class="text-red">*</span></label>
            <div class="col-sm-6">
              <input type="text" class="form-control" id="balance" name="balance" placeholder="Amount per year"
                required>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-md-2 col-xs-12 col-form-label" for="leave_tag">Leave Name <span
                class="text-red">*</span></label>
            <div class="col-sm-6">
              <input type="text" class="form-control" id="leave_tag" name="leave_tag"
                placeholder="Leave tag, separated by comma (,)" required>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-md-2 col-xs-12" for="balance">Description <span class="text-red">*</span></label>
            <div class="col-sm-6">
              <div class="form-check form-check-inline">
                <input class="form-check-input i-checks" type="radio" name="description" id="description1"
                  value="Paid Leave" checked>
                <label class="form-check-label" for="description1">&emsp;Paid Leave</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input i-checks" type="radio" name="description" id="description2"
                  value="Unpaid Leave">
                <label class="form-check-label" for="description2">&emsp;Unpaid Leave</label>
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
<script src="{{asset('adminlte/component/bootstrap-fileinput/js/fileinput.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
<script type="text/javascript">
  $('#leave_tag').select2({
    tags:[],
    tokenSeparators: [","]
  });
  $(document).ready(function() {
    $('.i-checks').iCheck({
      checkboxClass: 'icheckbox_square-green',
      radioClass: 'iradio_square-green',
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