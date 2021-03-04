@extends('admin.layouts.app')

@section('title', 'Add Unit')
@push('breadcrump')
    <li class="breadcrumb-item"><a href="{{route('site.index')}}">Unit</a></li>
    <li class="breadcrumb-item active">Add</li>
@endpush
@section('stylesheets')
<link href="{{asset('adminlte/component/summernote/css/summernote.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/css/fileinput.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.css')}}" rel="stylesheet">
@endsection
@section('content')
<div class="card card-{{config('configs.app_theme')}}  card-outline">
  <div class="card-header">
    <h3 class="card-title">Add Unit</h3>
    <!-- tools box -->
    <div class="pull-right card-tools">
      <button form="form" type="submit" class="btn btn-sm btn-{{config('configs.app_theme')}}" title="Simpan"><i class="fa fa-save"></i></button>
      <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i class="fa fa-reply"></i></a>
    </div>
    <!-- /. tools -->
  </div>
  <div class="card-body">
      <form id="form" action="{{route('site.store')}}"  method="post" autocomplete="off">
          {{ csrf_field() }}
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="code">Code <b class="text-danger">*</b></label>
              <input type="text" class="form-control" id="code" name="code" placeholder="Code" minlength="3" maxlength="3"  required>
                    <p class="help-block">Ex. M12 (Only letters uppercase and digit input).</p>
            </div>
            <div class="form-group">
              <label for="name">Name <b class="text-danger">*</b></label>
              <input type="text" class="form-control" id="name" name="name" placeholder="Name" required>
            </div>
            <div class="form-group">
              <label for="phone">Phone <b class="text-danger">*</b></label>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text"><i class="fa fa-phone"></i></span>
                </div>
                <input type="text" class="form-control" id="phone" name="phone" placeholder="Phone" required>
              </div>
            </div>
            <div class="form-group">
              <label for="email">Email <b class="text-danger">*</b></label>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text"><i class="fa fa-envelope"></i></span>
                </div>
                <input type="email" class="form-control" id="email" name="email" placeholder="Email"  required>
              </div>
            </div>
            <div class="form-group">
              <label for="province_id">Province <b class="text-danger">*</b></label>
                <input type="text" class="form-control" id="province_id" name="province_id" data-placeholder="Select Province" required>
            </div>
            <div class="form-group">
              <label for="region_id">Region <b class="text-danger">*</b></label>
              <input type="text" class="form-control" id="region_id" name="region_id" data-placeholder="Select Region" required>
            </div>
            <div class="form-group">
              <label for="district_id">District <b class="text-danger">*</b></label>
                <input type="text" class="form-control" id="district_id" name="district_id" data-placeholder="Select District" required>
            </div>
            <div class="form-group">
              <label for="address">Address <b class="text-danger">*</b></label>
              <textarea class="form-control" id="address" name="address" placeholder="Address" required></textarea>
            </div>

          <div class="form-group">
            <label for="postal_code">Postal Code <b class="text-danger">*</b></label>
              <input type="text" class="form-control" id="postal_code" name="postal_code" placeholder="Postal Code" required>
          </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="logo">Icon </label>
                <input type="file" class="form-control" name="logo" id="logo" accept="image/*" required/>
            </div>
            <div class="form-group">
              <label for="receipt_header">Head Notes </label>
                <textarea class="form-control summernote" name="receipt_header" id="receipt_header"></textarea>
            </div>
            <div class="form-group">
              <label for="receipt_footer">Food Notes </label>
              <textarea class="form-control summernote" name="receipt_footer" id="receipt_footer"></textarea>
            </div>
          </div>
        </div>
      </form>
  </div>
  <div class="overlay d-none">
    <i class="fas fa-2x fa-sync-alt fa-spin"></i>
  </div>
</div>
@endsection

@push('scripts')
<script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}"></script>
<script src="{{asset('adminlte/component/summernote/js/summernote.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-fileinput/js/fileinput.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.js')}}"></script>
<script>
  $(document).ready(function(){
      //Input Mask Component
      $("input[name=code]").inputmask("Regex", { regex: "[A-Z0-9]*" });
      //Select2 Component
      $( "#province_id" ).select2({
        ajax: {
          url: "{{route('province.select')}}",
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
      $( "#region_id" ).select2({
        ajax: {
          url: "{{route('region.select')}}",
          type:'GET',
          dataType: 'json',
          data: function (term,page) {
            return {
              province_id:$('#province_id').val(),
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
                text: `${item.type} ${item.name}`
              });
            });
            return {
              results: option, more: more,
            };
          },
        },
        allowClear: true,
      });
      $( "#district_id" ).select2({
        ajax: {
          url: "{{route('district.select')}}",
          type:'GET',
          dataType: 'json',
          data: function (term,page) {
            return {
              region_id:$('#region_id').val(),
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
      $(document).on("change", "#province_id", function () {
        if (!$.isEmptyObject($('#form').validate().submitted)) {
          $('#form').validate().form();
        }
        $('#region_id').select2('val','');
        $('#district_id').select2('val','');
      });
      $(document).on("change", "#region_id", function () {
        if (!$.isEmptyObject($('#form').validate().submitted)) {
          $('#form').validate().form();
        }
        $('#district_id').select2('val','');
      });
      $(document).on("change", "#district_id", function () {
        if (!$.isEmptyObject($('#form').validate().submitted)) {
          $('#form').validate().form();
        }
      });
      //Text Editor Component
      $('.summernote').summernote({
            height:225,
            placeholder:'Tulis sesuatu disini...',
            toolbar: [
              ['style', ['bold', 'italic', 'underline', 'clear']],
              ['font', ['strikethrough', 'superscript', 'subscript']],
              ['fontsize', ['fontsize']],
              ['color', ['color']],
              ['para', ['ul', 'ol', 'paragraph']],
              ['height', ['height']]
            ]
      });
      //Bootstrap fileinput component
      $("#logo").fileinput({
          browseClass: "btn btn-{{config('configs.app_theme')}}",
          showRemove: false,
          showUpload: false,
          allowedFileExtensions: ["png"],
          dropZoneEnabled: false,
          theme:'explorer-fas'
      });
      $(document).on("change", "#logo", function () {
        if (!$.isEmptyObject($('#form').validate().submitted)) {
          $('#form').validate().form();
        }
      });
      $("#form").validate({
        errorElement: 'div',
        errorClass: 'invalid-feedback',
        focusInvalid: false,
        highlight: function (e) {
          $(e).closest('.form-group').removeClass('has-success').addClass('was-validated  has-error');
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
