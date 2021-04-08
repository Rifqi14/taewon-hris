@extends('admin.layouts.app')

@section('title', __('config.title'))
@push('breadcrump')
<li class="breadcrumb-item active">{{ __('config.title') }}</li>
@endpush
@section('stylesheets')
<link href="{{asset('bootstrap-taginput/tagsinput.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/css/fileinput.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.css')}}" rel="stylesheet">
@endsection
@section('content')
<div class="card card-{{config('configs.app_theme')}} card-outline">
  <div class="card-header">
    <h3 class="card-title">{{ __('config.subtitle') }}</h3>
    <!-- tools box -->
    <div class="pull-right card-tools">
      <button form="form" type="submit" class="btn btn-sm btn-{{config('configs.app_theme')}}" title="Simpan"><i class="fa fa-save"></i></button>
    </div>
    <!-- /. tools -->
  </div>
  <div class="card-body">
    <form id="form" action="{{route('config.update')}}" enctype="multipart/form-data" method="post" accept-charset="utf-8">
      {{ csrf_field() }}
      <input type="hidden" name="_method" value="put">
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label for="app_name">{{ __('config.app_name') }}</label>
            <input type="text" name="app_name" value="{{config('configs.app_name')}}" class="form-control" id="app_name" required />
          </div>
          <div class="form-group">
            <label for="app_name">{{ __('config.copyright') }}</label>
            <input type="text" name="app_copyright" value="{{config('configs.company_name')}}" class="form-control" id="app_copyright" required />
          </div>
          <div class="form-group">
            <label for="app_logo">{{ __('config.login_icon') }}</label>
            <input type="file" class="form-control" name="app_logo" id="app_logo" accept="image/*" />
          </div>
          <div class="form-group">
            <label for="app_icon">{{ __('config.icon') }}</label>
            <input type="file" class="form-control" name="app_icon" id="app_icon" accept="image/*" />
          </div>
          <div class="form-group">
            <label for="app_theme">{{ __('config.theme') }}</label>
            <select name="app_theme" class="form-control select2" placeholder="Pilih Tema" required>
              <option value="primary" @if(config('configs.app_theme')=='primary' ) selected @endif>Primary</option>
              <option value="danger" @if(config('configs.app_theme')=='danger' ) selected @endif>Danger</option>
              <option value="info" @if(config('configs.app_theme')=='info' ) selected @endif>Info</option>
              <option value="success" @if(config('configs.app_theme')=='success' ) selected @endif>Success</option>
              <option value="navy" @if(config('configs.app_theme')=='navy' ) selected @endif>Navy</option>
            </select>
          </div>
        </div>
        <div class="col-md-6">
          {{-- <div class="form-group">
            <label for="expired_contract">Expired Contract</label>
            <input type="text" name="expired_contract" value="{{config('configs.expired_contract')}} Days" class="form-control" id="expired_contract" required />
        </div> --}}
        {{-- <div class="form-group">
            <label for="expired_document">Expired Document</label>
            <input type="text" name="expired_document" value="{{config('configs.expired_document')}} Days" class="form-control" id="expired_document" required />
      </div> --}}
      <div class="form-group">
        <label for="company_name">{{ __('config.company_name') }}</label>
        <input type="text" name="company_name" value="{{config('configs.company_name')}}" class="form-control" id="company_name" required />
      </div>
      <div class="form-group">
        <label for="company_email">{{ __('config.company_email') }}</label>
        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text"><i class="fa fa-envelope"></i></span>
          </div>
          <input type="email" name="company_email" value="{{config('configs.company_email')}}" class="form-control" id="company_email" required />
        </div>
      </div>
      <div class="form-group">
        <label for="company_phone">{{ __('config.company_phone') }}</label>
        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text"><i class="fa fa-phone"></i></span>
          </div>
          <input type="text" name="company_phone" value="{{config('configs.company_phone')}}" class="form-control" id="company_phone" required />
        </div>
      </div>
      <div class="form-group">
        <label for="company_address">{{ __('config.address') }}</label>
        <textarea class="form-control" id="company_address" name="company_address" placeholder="Alamat" required>{{config('configs.company_address')}}</textarea>
      </div>
      <div class="form-group">
        <label for="email_push">{{ __('config.push_notif') }}</label>
        <input type="text" name="email_push" value="{{config('configs.email_push')}}" class="form-control" id="email_push" data-role="tagsinput" />
      </div>
      <div class="form-group">
        <label for="language">{{ __('config.language') }}</label>
        <select name="language" id="language" class="form-control select2" placeholder="Choose Language">
          @foreach (config('enums.languages') as $key => $item)
          <option value="{{ $key }}" @if (config('configs.language')==$key) selected @endif>{{ $item }}</option>
          @endforeach
        </select>
      </div>
      <div class="form-group">
        <label for="cut_off">Cut Off</label>
        <select name="cut_off" class="form-control select2" placeholder="Pilih Cut Off" required>
          @php  
              for ($x = 1; $x <= 31; $x++) {
                @endphp
                <option value="{{ $x }}" @if(config('configs.cut_off') == $x) selected @endif>{{ $x }}</option>
                @php 
              }
          @endphp
        </select>
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
<script src="{{asset('adminlte/component/validate/jquery.validate.min.js')}}
"></script>
<script src="{{asset('bootstrap-taginput/tagsinput.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-fileinput/js/fileinput.min.js')}}"></script>
<script src="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.js')}}"></script>
<script>
  $(document).ready(function(){
    $('.select2').select2();
    $("#app_logo").fileinput({
      browseClass: "btn btn-{{config('configs.app_theme')}}",
      showRemove: false,
      showUpload: false,
      showDrag:false,
      allowedFileExtensions: ["png"],
      dropZoneEnabled: false,
      initialPreview: '<img src="{{asset(config('configs.app_logo'))}}" class="kv-preview-data file-preview-image">',
      initialPreviewAsData: false,
      initialPreviewFileType: 'image',
      initialPreviewConfig: [
      {caption: "{{config('configs.app_logo')}}", downloadUrl: "{{asset(config('configs.app_logo'))}}", size:"{{ @File::size(public_path(config('configs.app_logo')))}}",url: false}
      ],
      theme:'explorer-fas'
    });
    $("#app_icon").fileinput({
      browseClass: "btn btn-{{config('configs.app_theme')}}",
      showRemove: false,
      showUpload: false,
      showDrag:false,
      allowedFileExtensions: ["png"],
      dropZoneEnabled: false,
      initialPreview: '<img src="{{asset(config('configs.app_icon'))}}" class="kv-preview-data file-preview-image">',
      initialPreviewAsData: false,
      initialPreviewFileType: 'image',
      initialPreviewConfig: [
      {caption: "{{config('configs.app_icon')}}", downloadUrl: "{{asset(config('configs.app_icon'))}}", size:"{{ @File::size(public_path(config('configs.app_icon')))}}",url: false}
      ],
      theme:'explorer-fas'
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