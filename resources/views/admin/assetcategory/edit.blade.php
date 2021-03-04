@extends('admin.layouts.app')

@section('title', 'Edit Asset Category')
@push('breadcrump')
    <li class="breadcrumb-item"><a href="{{route('assetcategory.index')}}">Asset Category</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endpush
@section('stylesheets')
<link rel="stylesheet" href="{{asset('adminlte/component/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}">
<link href="{{asset('adminlte/component/bootstrap-fileinput/css/fileinput.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.css')}}" rel="stylesheet">
@endsection
@section('content')
<div class="row">
    <div class="col-lg-12">
    <div class="card card-{{config('configs.app_theme')}} card-outline">
        <div class="card-header">
          <h3 class="card-title">Edit Asset Category</h3>
          <!-- tools box -->
          <div class="pull-right card-tools">
            <button form="form" type="submit" class="btn btn-sm btn-{{config('configs.app_theme')}}" title="Update"><i class="fa fa-save"></i></button>
            <a href="javascript:void(0)" onclick="backurl()" class="btn btn-sm btn-default" title="Back"><i class="fa fa-reply"></i></a>
          </div>
          <!-- /. tools -->
        </div>
        <div class="card-body">
            <form id="form" action="{{route('assetcategory.update',['id'=>$assetcategory->id])}}" class="form-horizontal" method="post" autocomplete="off">
               {{ csrf_field() }}
               {{ method_field('put') }}
               <div class="well well-sm">
                 <div class="form-group row">
                    <label for="name" class="col-sm-2 control-label">Category Parent</label>
                    <div class="col-sm-6">
                    <input type="text" class="form-control" id="parent_id" name="parent_id" data-placeholder="Choose Category Parent" readonly>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="name" class="col-sm-2 control-label">Category Name <b class="text-danger">*</b></label>
                    <div class="col-sm-6">
                    <input type="text" class="form-control" id="name" name="name" placeholder="Category Name" value="{{$assetcategory->name}}" required>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="name" class="col-sm-2 control-label">Category Name <b class="text-danger">*</b></label>
                    <div class="col-sm-6">
                    <select name="type" id="type" class="form-control select2" placeholder="Category Type">
                      <option value=""></option>
                      <option value="asset" @if($assetcategory->type == 'asset') selected @endif>Asset</option>
                      <option value="vehicle" @if($assetcategory->type == 'vehicle') selected @endif>Vehicle</option>
                    </select>
                    </div>
                  </div>
                </div>
                <div class="well well-sm">
                  <div class="form-group row">
                    <label for="name" class="col-sm-2 control-label">Description <b class="text-danger">*</b></label>
                    <div class="col-sm-6">
                    <textarea name="description" id="description" class="form-control" placeholder="Description" required>{{$assetcategory->description}}</textarea>
                    </div>
                  </div>
                  <div class="form-group row">
                      <label class="col-sm-2 control-label">Display</label>
                      <div class="col-sm-6">
                        <label><input class="form-control" type="checkbox" name="display" @if($assetcategory->display) checked @endif> <i></i></label>
                    </div>
                  </div>
                  <div class="form-group row">
                      <label for="app_logo" class="col-sm-2 control-label">Image</label>
                      <div class="col-sm-6">
                      <input type="file" class="form-control" name="picture" id="picture" value="{{$assetcategory->picture}}" accept="image/*"/>
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
<script>
  $(document).ready(function(){
      $('.select2').select2();
      $('input[name=display]').iCheck({
          checkboxClass: 'icheckbox_square-green',
          radioClass: 'iradio_square-green',
      });
      $( "#parent_id" ).select2({
        ajax: {
          url: "{{route('assetcategory.select')}}",
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
      @if($assetcategory->parent_id)
      $("#parent_id").select2('data',{id:{{$assetcategory->parent_id}},text:'{{$assetcategory->parent->name}}'}).trigger('change');
      @endif
      $(document).on("change", "#parent_id", function () {
        if (!$.isEmptyObject($('#form').validate().submitted)) {
          $('#form').validate().form();
        }
      });
      $("#picture").fileinput({
          browseClass: "btn btn-{{config('configs.app_theme')}}",
          showRemove: false,
          showUpload: false,
          allowedFileExtensions: ["png", "jpg", "jpeg"],
          dropZoneEnabled: false,
          initialPreview: '<img src="{{asset($assetcategory->picture)}}" class="kv-preview-data file-preview-image">',
          initialPreviewAsData: false,
          initialPreviewFileType: 'image',
          initialPreviewConfig: [
          {caption: "{{$assetcategory->picture}}", downloadUrl: "{{asset($assetcategory->picture)}}", size:"{{ @File::size(public_path($assetcategory->picture))}}",url: false}
          ],
          theme:'explorer-fas'
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
