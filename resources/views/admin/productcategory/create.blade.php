@extends('admin.layouts.app')

@section('title', 'Tambah Kategori Produk')
@push('breadcrump')
    <li class="breadcrumb-item"><a href="{{route('productcategory.index')}}">Kategori Produk</a></li>
    <li class="breadcrumb-item active">Tambah</li>
@endpush
@section('stylesheets')
<link rel="stylesheet" href="{{asset('adminlte/component/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}">
<link href="{{asset('adminlte/component/bootstrap-fileinput/css/fileinput.min.css')}}" rel="stylesheet">
<link href="{{asset('adminlte/component/bootstrap-fileinput/themes/explorer-fas/theme.min.css')}}" rel="stylesheet">
<style type="text/css">
  #map {
       height: 300px;
       border: 1px solid #CCCCCC;
     }
</style>
@endsection
@section('content')
<div class="row">
    <div class="col-lg-12">
    <div class="card">
        <div class="card-header">
          <h3 class="card-title">Tambah Kategori Produk</h3>
          <!-- tools box -->
          <div class="pull-right card-tools">
            <button form="form" type="submit" class="btn btn-sm btn-danger text-white" title="Simpan"><i class="fa fa-save"></i></button>
            <a href="{{ url()->previous() }}" class="btn btn-sm btn-default" title="Kembali"><i class="fa fa-reply"></i></a>
          </div>
          <!-- /. tools -->
        </div>
        <div class="card-body">
            <form id="form" action="{{route('productcategory.store')}}" class="form-horizontal" method="post" autocomplete="off">
               {{ csrf_field() }}
               <div class="well well-sm">
                 <div class="form-group row">
                    <label for="name" class="col-sm-2 control-label">Parent Kategori</label>
                    <div class="col-sm-6">
                      <input type="text" class="form-control" id="parent" name="parent" data-placeholder="Pilih Parent Kategori">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="name" class="col-sm-2 control-label">Nama Kategori <b class="text-danger">*</b></label>
                    <div class="col-sm-6">
                      <input type="text" class="form-control" id="name" name="name" placeholder="Nama" required>
                    </div>
                  </div>
                </div>
                <div class="well well-sm">
                  <div class="form-group row">
                    <label for="name" class="col-sm-2 control-label">Keterangan <b class="text-danger">*</b></label>
                    <div class="col-sm-6">
                      <textarea name="description" id="description" class="form-control" placeholder="Keterangan" required></textarea>
                    </div>
                  </div>
                  <div class="form-group row">
                      <label class="col-sm-2 control-label">Tampilkan Di Web</label>
                      <div class="col-sm-6">
                        <label><input class="form-control" type="checkbox" name="display"> <i></i></label>
                    </div>
                  </div>
                  <div class="form-group row">
                      <label for="app_logo" class="col-sm-2 control-label">Foto</label>
                      <div class="col-sm-6">
                        <input type="file" class="form-control" name="picture" id="picture" accept="image/*"/>
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
    
      $('input[name=display]').iCheck({
          checkboxClass: 'icheckbox_square-green',
          radioClass: 'iradio_square-green',
      });
      $( "#parent" ).select2({
        ajax: {
          url: "{{route('productcategory.select')}}",
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
      $(document).on("change", "#parent", function () {
        if (!$.isEmptyObject($('#form').validate().submitted)) {
          $('#form').validate().form();
        }
      });
      
      $("#picture").fileinput({
          browseClass: "btn btn-danger",
          showRemove: false,
          showUpload: false,
          allowedFileExtensions: ["png", "jpg", "jpeg"],
          dropZoneEnabled: false,
  
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
